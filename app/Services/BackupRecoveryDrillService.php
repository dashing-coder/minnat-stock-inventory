<?php

namespace App\Services;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Recover into a dedicated, separately configured MySQL drill database and isolated
 * upload directory. Never uses the active ERP database or active upload paths.
 * Verifies each restored row and file, rather than checking only archive readability.
 */
class BackupRecoveryDrillService
{
    public function __construct(private BackupService $backups) {}

    private function tables(ConnectionInterface $db): array
    {
        $excluded = ['cache', 'cache_locks', 'jobs', 'job_batches', 'sessions'];
        $tables = array_map(fn ($row) => array_values((array) $row)[0], $db->select('SHOW TABLES'));
        return array_values(array_filter($tables, fn ($table) => !in_array($table, $excluded, true)));
    }

    private function fingerprint(array $rows): string
    {
        $digests = [];
        foreach ($rows as $row) {
            $normalized = [];
            foreach ((array) $row as $key => $value) {
                $normalized[$key] = $value === null ? null : (string) $value;
            }
            ksort($normalized, SORT_STRING);
            $digests[] = hash('sha256', json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        }
        sort($digests, SORT_STRING);
        return hash('sha256', implode('', $digests));
    }

    private function assertSafeStagingConnection(): ConnectionInterface
    {
        if (!config('erp_backup.drill_enabled', false)) {
            throw new RuntimeException('Staging recovery drill is disabled. Set ERP_BACKUP_DRILL_ENABLED=true only on a safe development/staging machine.');
        }
        if (app()->environment('production')) {
            throw new RuntimeException('Run recovery drills on a separate development/staging machine, not on production.');
        }
        $database = (string) config('erp_backup.drill_database', '');
        if (!preg_match('/\Aafrin_erp_restore_drill_[a-zA-Z0-9_]+\z/D', $database)) {
            throw new RuntimeException('Staging database name must start with afrin_erp_restore_drill_.');
        }
        $liveConfig = (array) config('database.connections.mysql', []);
        $override = array_filter((array) config('erp_backup.drill_connection', []), fn ($v) => $v !== null);
        $stagingConfig = array_merge($liveConfig, $override, ['driver' => 'mysql', 'database' => $database]);
        if (strtolower((string) ($liveConfig['database'] ?? '')) === strtolower($database)) {
            throw new RuntimeException('Refusing recovery drill into the configured main ERP database.');
        }
        config(['database.connections.erp_backup_drill' => $stagingConfig]);
        DB::purge('erp_backup_drill');
        $target = DB::connection('erp_backup_drill');
        $live = DB::connection();
        // DATABASE() is authoritative: database name configuration may contain aliases/DSNs.
        $sourceName = (string) ($live->selectOne('SELECT DATABASE() AS db')->db ?? '');
        $targetName = (string) ($target->selectOne('SELECT DATABASE() AS db')->db ?? '');
        if ($targetName === '' || strcasecmp($targetName, $sourceName) === 0) {
            throw new RuntimeException('Recovery drill target resolves to the active ERP database. Refusing to proceed.');
        }
        if (strcasecmp($targetName, $database) !== 0) {
            throw new RuntimeException('Unexpected staging database connection: '.$targetName);
        }
        return $target;
    }

    public function run(string $name): array
    {
        $target = $this->assertSafeStagingConnection();
        $payload = $this->backups->readPayloadForRecoveryDrill($name);
        if (!str_ends_with($name, '.json.gz.enc')) throw new RuntimeException('Recovery drills require encrypted backups.');
        $expected = array_keys($payload['tables']);
        $actual = $this->tables($target);
        $schemaCreated = false;
        if (!$actual && isset($payload['schema'])) {
            // This acts ONLY on the tightly guarded, separately configured staging DB.
            // CREATE TABLE causes DDL commits, so a failed drill may leave a partial
            // empty staging schema; never point this at any valuable database.
            $target->statement('SET FOREIGN_KEY_CHECKS=0');
            try {
                foreach ($payload['schema'] as $table => $ddl) {
                    $target->statement($ddl);
                }
                $schemaCreated = true;
            } finally {
                $target->statement('SET FOREIGN_KEY_CHECKS=1');
            }
            $actual = $this->tables($target);
        }
        sort($expected);
        sort($actual);
        if ($expected !== $actual) {
            throw new RuntimeException('Staging schema does not match backup tables. Prepare matching empty schema first; no data was changed.');
        }
        // Validate all table columns and fingerprints before *any* staging mutations.
        $expectedHashes = [];
        $totalRows = 0;
        foreach ($payload['tables'] as $table => $rows) {
            $columns = array_map(fn ($row) => $row->Field, $target->select('SHOW COLUMNS FROM `'.$table.'`'));
            foreach ($rows as $row) {
                if (array_diff(array_keys($row), $columns)) {
                    throw new RuntimeException('Staging table column mismatch: '.$table);
                }
            }
            $expectedHashes[$table] = $this->fingerprint($rows);
            $totalRows += count($rows);
        }

        // All user-upload bytes are restored only to an isolated recovery-drill area.
        $base = storage_path('app/erp_backup_recovery_drills');
        if (is_link($base)) throw new RuntimeException('Recovery drill storage root may not be a symbolic link.');
        File::ensureDirectoryExists($base, 0700, true);
        $targetFolder = $base.DIRECTORY_SEPARATOR.'drill_'.bin2hex(random_bytes(8));
        File::ensureDirectoryExists($targetFolder, 0700, true);
        $fileCount = 0;
        try {
            foreach ($payload['files'] as $relative => $encoded) {
                // BackupService has already validated relative paths and archive bounds.
                $decoded = base64_decode($encoded, true);
                if ($decoded === false || strlen($decoded) > 10 * 1024 * 1024) {
                    throw new RuntimeException('Corrupt upload in backup.');
                }
                $path = $targetFolder.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
                File::ensureDirectoryExists(dirname($path), 0700, true);
                if (file_put_contents($path, $decoded, LOCK_EX) !== strlen($decoded) ||
                    !hash_equals(hash('sha256', $decoded), (string) hash_file('sha256', $path))) {
                    throw new RuntimeException('Staging uploaded-file recovery validation failed.');
                }
                $fileCount++;
            }
            // MySQL InnoDB rows roll back if any table digest fails; staging tables only.
            $target->transaction(function () use ($target, $payload, $expectedHashes): void {
                $target->statement('SET FOREIGN_KEY_CHECKS=0');
                try {
                    foreach ($payload['tables'] as $table => $rows) {
                        $target->table($table)->delete();
                        foreach (array_chunk($rows, 200) as $batch) {
                            if ($batch) $target->table($table)->insert($batch);
                        }
                    }
                    foreach ($expectedHashes as $table => $hash) {
                        $restored = $target->table($table)->get()->map(fn ($row) => (array) $row)->all();
                        if (!hash_equals($hash, $this->fingerprint($restored))) {
                            throw new RuntimeException('Recovery drill data differs from backup: '.$table);
                        }
                    }
                } finally {
                    $target->statement('SET FOREIGN_KEY_CHECKS=1');
                }
            });
            return [
                'success' => true,
                'source_backup' => $name,
                'staging_database' => (string) config('erp_backup.drill_database'),
                'tables_recovered_and_hashed' => count($expectedHashes),
                'schema_created_from_encrypted_archive' => $schemaCreated,
                'rows_recovered_and_hashed' => $totalRows,
                'files_recovered_and_hashed' => $fileCount,
                'isolated_files_directory' => $targetFolder,
                'live_database_modified' => false,
                'live_uploads_modified' => false,
            ];
        } catch (\Throwable $e) {
            // On error, remove only the newly created isolated staging upload directory.
            File::deleteDirectory($targetFolder);
            throw $e;
        }
    }
}
