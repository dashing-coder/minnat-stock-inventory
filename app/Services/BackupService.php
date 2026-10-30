<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Creates authenticated, encrypted ERP snapshots.
 * The APP_KEY is required to decrypt them: preserve it separately and securely.
 * This is a recovery aid, not a substitute for a tested off-host SQL backup.
 */
class BackupService
{
    private const FORMAT = 'afrin-erp-backup-v2';
    private const LEGACY_FORMAT = 'afrin-erp-backup-v1';
    private const SKIP_TABLES = ['cache', 'cache_locks', 'jobs', 'job_batches', 'sessions'];
    private const MAX_FILE_BYTES = 10 * 1024 * 1024;
    private const MAX_TOTAL_FILE_BYTES = 50 * 1024 * 1024;
    private const MAX_INFLATED_BYTES = 256 * 1024 * 1024;

    public function directory(): string
    {
        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory, 0700, true);
        @chmod($directory, 0700);
        return $directory;
    }

    public function create(): string
    {
        // Never produce plaintext backups. Laravel's encrypted payload is authenticated.
        if (!config('app.key')) {
            throw new RuntimeException('APP_KEY is required to encrypt ERP backups.');
        }

        // InnoDB reads use the same transaction/consistent snapshot (subject to DB isolation).
        $tables = $this->currentTables();
        $rows = DB::transaction(function () use ($tables): array {
            $result = [];
            foreach ($tables as $table) {
                $result[$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
            }
            return $result;
        });

        // Include source table DDL so a NEW encrypted archive can recreate an empty
        // disaster-recovery database, rather than requiring a surviving live schema.
        $schema = [];
        foreach ($tables as $table) {
            if (!preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/D', $table)) {
                throw new RuntimeException('Unsafe database table identifier during backup.');
            }
            $createRow = DB::selectOne('SHOW CREATE TABLE `'.$table.'`');
            $ddl = array_values((array) $createRow)[1] ?? null;
            if (!is_string($ddl) || !str_starts_with($ddl, 'CREATE TABLE `'.$table.'`')) {
                throw new RuntimeException('Could not capture validated table DDL for '.$table);
            }
            $schema[$table] = $ddl;
        }

        $files = [];
        $budget = self::MAX_TOTAL_FILE_BYTES;
        $root = storage_path('app');
        foreach (['public', 'private'] as $folder) {
            $directory = $root.DIRECTORY_SEPARATOR.$folder;
            if (!is_dir($directory)) continue;
            foreach (File::allFiles($directory) as $file) {
                if ($file->isLink()) continue;
                $size = $file->getSize();
                if ($size < 0 || $size > self::MAX_FILE_BYTES || $budget < $size) {
                    throw new RuntimeException('An uploaded file exceeds the backup size limit; incomplete backups are prohibited.');
                }
                $relative = $folder.'/'.str_replace('\\', '/', substr($file->getPathname(), strlen($directory) + 1));
                if (!$this->safeRelativeFile($relative)) continue;
                $content = file_get_contents($file->getPathname());
                if ($content === false) throw new RuntimeException('Could not read an application upload for backup.');
                $files[$relative] = base64_encode($content);
                $budget -= strlen($content);
            }
        }

        $payload = [
            'format' => self::FORMAT,
            'created_at' => now()->toIso8601String(),
            'tables' => $rows,
            'schema' => $schema,
            'files' => $files,
        ];
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $gzip = gzencode($json, 6);
        if ($gzip === false) throw new RuntimeException('ERP backup compression failed.');
        $encrypted = Crypt::encryptString($gzip);
        if (strlen($encrypted) > (int) config('erp_backup.max_archive_bytes', 150 * 1024 * 1024)) {
            throw new RuntimeException('Encrypted backup exceeds the allowed archive size; no incomplete backup was saved.');
        }

        $name = 'afrin-erp-'.now()->format('Y-m-d_His_u').'.json.gz.enc';
        $target = $this->directory().DIRECTORY_SEPARATOR.$name;
        $temporary = tempnam($this->directory(), '.backup-');
        if ($temporary === false) throw new RuntimeException('Could not create a temporary backup file.');

        try {
            @chmod($temporary, 0600);
            if (file_put_contents($temporary, $encrypted, LOCK_EX) !== strlen($encrypted)) {
                throw new RuntimeException('Could not write complete encrypted backup.');
            }
            if (!rename($temporary, $target)) throw new RuntimeException('Could not finalize encrypted backup.');
            @chmod($target, 0600);
        } finally {
            if (is_file($temporary)) @unlink($temporary);
        }
        // Retention must run only AFTER any required off-site replication succeeds.
        // The scheduled command handles it; manually created archives are retained.
        return $name;
    }

    public function files(): array
    {
        return collect(File::files($this->directory()))
            ->filter(fn ($file) => $this->validName($file->getFilename()) && !$file->isLink())
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'encrypted' => str_ends_with($file->getFilename(), '.enc'),
                'size' => $file->getSize(),
                'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
            ])->values()->all();
    }

    public function path(string $name): string
    {
        abort_unless($this->validName($name), 404);
        $target = $this->directory().DIRECTORY_SEPARATOR.$name;
        abort_unless(is_file($target) && !is_link($target), 404);
        return $target;
    }

    /** Decrypt and validate the archive before any restore mutation. */
    public function verify(string $name): array
    {
        $payload = $this->readAndValidate($name);
        return [
            'created_at' => $payload['created_at'] ?? null,
            'tables' => count($payload['tables']),
            'files' => count($payload['files']),
            'encrypted' => str_ends_with($name, '.enc'),
        ];
    }

    /** Read an authenticated snapshot for an isolated DRILL ONLY (never exposed over HTTP). */
    public function readPayloadForRecoveryDrill(string $name): array
    {
        if (!str_ends_with($name, '.json.gz.enc')) {
            throw new RuntimeException('Recovery drills require an encrypted V2 archive.');
        }
        return $this->readAndValidate($name);
    }

    public function restore(string $name): void
    {
        $payload = $this->readAndValidate($name);
        $expected = $this->currentTables();
        $actual = array_keys($payload['tables']);
        sort($expected);
        sort($actual);
        if ($expected !== $actual) {
            throw new RuntimeException('Backup schema differs from this database. Restore on a matching staging installation.');
        }

        // Validate/decode every file before changing database rows.
        $restoreFiles = [];
        foreach ($payload['files'] as $relative => $encoded) {
            $decoded = base64_decode($encoded, true);
            if ($decoded === false || strlen($decoded) > self::MAX_FILE_BYTES) {
                throw new RuntimeException('Invalid or oversized file inside the backup.');
            }
            $this->assertStorageTargetSafe($relative);
            $restoreFiles[$relative] = $decoded;
        }

        // DB rollback works for transactional tables. Uploaded files are not covered by the DB
        // transaction; restore should be executed in maintenance mode on a staging copy first.
        DB::transaction(function () use ($payload): void {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            try {
                foreach ($payload['tables'] as $table => $rows) {
                    DB::table($table)->delete();
                    foreach (array_chunk($rows, 250) as $chunk) {
                        if ($chunk) DB::table($table)->insert($chunk);
                    }
                }
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        });

        foreach ($restoreFiles as $relative => $data) {
            $target = storage_path('app'.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative));
            File::ensureDirectoryExists(dirname($target));
            $this->assertStorageTargetSafe($relative);
            $temp = tempnam(dirname($target), '.erp-restore-');
            if ($temp === false) throw new RuntimeException('Cannot stage restored upload: '.$relative);
            try {
                if (file_put_contents($temp, $data, LOCK_EX) !== strlen($data) || !rename($temp, $target)) {
                    throw new RuntimeException('Database restored but an upload could not be restored: '.$relative);
                }
            } finally {
                if (is_file($temp)) @unlink($temp);
            }
        }
    }

    public function delete(string $name): void
    {
        if (!File::delete($this->path($name))) {
            throw new RuntimeException('Could not delete backup.');
        }
    }

    /** Remove only encrypted ERP backups older than the configured retention window. */
    public function prune(): int
    {
        $days = (int) config('erp_backup.retention_days', 30);
        if ($days < 1) return 0; // <= 0 disables pruning
        $cutoff = time() - $days * 86400;
        $removed = 0;
        foreach (File::files($this->directory()) as $file) {
            if (!$file->isLink() && str_ends_with($file->getFilename(), '.json.gz.enc')
                && $this->validName($file->getFilename()) && $file->getMTime() < $cutoff) {
                if (File::delete($file->getPathname())) $removed++;
            }
        }
        return $removed;
    }

    private function readAndValidate(string $name): array
    {
        $path = $this->path($name);
        if (filesize($path) > (int) config('erp_backup.max_archive_bytes', 150 * 1024 * 1024)) {
            throw new RuntimeException('Backup archive exceeds the configured safety limit.');
        }
        $input = file_get_contents($path);
        if ($input === false) throw new RuntimeException('Backup file cannot be read.');
        if (str_ends_with($name, '.enc')) {
            $gzip = Crypt::decryptString($input); // authenticated; tampering fails here
        } else {
            if (!config('erp_backup.allow_legacy_restore', false)) {
                throw new RuntimeException('Legacy unencrypted backups are blocked. Store them offline or explicitly enable a staged migration.');
            }
            $gzip = $input;
        }

        $json = gzdecode($gzip, self::MAX_INFLATED_BYTES);
        if ($json === false) throw new RuntimeException('Backup compression is invalid or too large.');
        $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $expectedFormat = str_ends_with($name, '.enc') ? self::FORMAT : self::LEGACY_FORMAT;
        if (!is_array($payload) || ($payload['format'] ?? null) !== $expectedFormat ||
            !isset($payload['tables']) || !is_array($payload['tables']) || count($payload['tables']) < 1 ||
            !isset($payload['files']) || !is_array($payload['files'])) {
            throw new RuntimeException('ERP backup manifest is invalid.');
        }

        // Old encrypted V2 snapshots may not have schema metadata. They remain
        // readable but need a separately schema-provisioned recovery database.
        if (isset($payload['schema'])) {
            if (!is_array($payload['schema'])) {
                throw new RuntimeException('Invalid schema metadata in ERP backup.');
            }
            $ddlTables = array_keys($payload['schema']);
            $dataTables = array_keys($payload['tables']);
            sort($ddlTables);
            sort($dataTables);
            if ($ddlTables !== $dataTables) throw new RuntimeException('Backup DDL/table list mismatch.');
            foreach ($payload['schema'] as $table => $ddl) {
                if (!is_string($ddl) || !str_starts_with($ddl, 'CREATE TABLE `'.$table.'` (')) {
                    throw new RuntimeException('Backup contains invalid table schema: '.$table);
                }
            }
        }
        foreach ($payload['tables'] as $table => $rows) {
            if (!is_string($table) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/D', $table)
                || !is_array($rows)) throw new RuntimeException('ERP backup has invalid table data.');
            foreach ($rows as $row) {
                if (!is_array($row)) throw new RuntimeException('ERP backup row is malformed.');
            }
        }
        foreach ($payload['files'] as $relative => $encoded) {
            if (!$this->safeRelativeFile($relative) || !is_string($encoded)) {
                throw new RuntimeException('ERP backup contains an unsafe file path.');
            }
        }
        return $payload;
    }

    private function validName(string $name): bool
    {
        return preg_match('/\Aafrin-erp-\d{4}-\d{2}-\d{2}_\d{6}(?:_\d{6})?\.json\.gz(?:\.enc)?\z/D', $name) === 1;
    }

    private function safeRelativeFile(mixed $relative): bool
    {
        if (!is_string($relative) || strlen($relative) > 512 ||
            str_contains($relative, "\0") || str_contains($relative, '\\')) return false;
        if (!str_starts_with($relative, 'public/') && !str_starts_with($relative, 'private/')) return false;
        foreach (explode('/', $relative) as $piece) {
            if ($piece === '' || $piece === '.' || $piece === '..') return false;
        }
        return true;
    }

    private function assertStorageTargetSafe(string $relative): void
    {
        // A path that is lexically safe can still escape if an intermediate directory
        // or destination file is a symlink. Reject such paths before restoring data.
        $path = storage_path('app');
        foreach (explode('/', $relative) as $part) {
            $path .= DIRECTORY_SEPARATOR.$part;
            if (is_link($path)) {
                throw new RuntimeException('Backup file path resolves through a symbolic link.');
            }
        }
    }

    private function currentTables(): array
    {
        $tables = array_map(fn ($row) => array_values((array) $row)[0], DB::select('SHOW TABLES'));
        return array_values(array_filter($tables, fn ($table) => !in_array($table, self::SKIP_TABLES, true)));
    }
}
