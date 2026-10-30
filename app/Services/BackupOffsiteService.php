<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Remote, encrypted-only backup replication. It never writes plaintext to the remote disk.
 * Verification reads the REMOTE object back; upload success alone is insufficient.
 * Requires a private S3-compatible Flysystem disk and the optional S3 adapter.
 */
class BackupOffsiteService
{
    public function __construct(private BackupService $backups) {}

    public function configured(): bool
    {
        $config = (array) config('filesystems.disks.erp_offsite', []);
        return (bool) config('erp_backup.offsite_enabled', false)
            && ($config['driver'] ?? null) === 's3'
            && !empty($config['bucket']);
    }

    private function diskName(): string
    {
        if (!$this->configured()) {
            throw new RuntimeException('Off-site backups are disabled. Configure ERP_BACKUP_OFFSITE_ENABLED and a private S3 disk.');
        }
        $diskName = (string) config('erp_backup.offsite_disk', 'erp_offsite');
        $config = (array) config("filesystems.disks.{$diskName}", []);
        if ($diskName === '' || $diskName === 'public' || $diskName === 'local'
            || ($config['driver'] ?? null) !== 's3'
            || empty($config['bucket']) || !empty($config['visibility']) && $config['visibility'] !== 'private') {
            throw new RuntimeException('Off-site storage must be a configured PRIVATE S3-compatible bucket (not local/public/FTP).');
        }
        return $diskName;
    }

    private function objectKey(string $name): string
    {
        // Local lookup performs strict backup filename and existence checks.
        $this->backups->path($name);
        if (!str_ends_with($name, '.json.gz.enc')) {
            throw new RuntimeException('Off-site replication requires an encrypted V2 backup.');
        }
        $prefix = trim((string) config('erp_backup.offsite_prefix', 'afrin-erp'), '/');
        if ($prefix === '' || strlen($prefix) > 120 || !preg_match('#\A[a-zA-Z0-9_-]+(?:/[a-zA-Z0-9_-]+)*\z#D', $prefix)) {
            throw new RuntimeException('Invalid off-site object prefix.');
        }
        return $prefix.'/'.$name;
    }

    private function readRemoteHash(string $diskName, string $key): string
    {
        $stream = Storage::disk($diskName)->readStream($key);
        if (!is_resource($stream)) {
            throw new RuntimeException('Cannot read remote encrypted backup for verification.');
        }
        try {
            $context = hash_init('sha256');
            if (hash_update_stream($context, $stream) === false) {
                throw new RuntimeException('Cannot hash remote encrypted backup.');
            }
            return hash_final($context);
        } finally {
            fclose($stream);
        }
    }

    public function replicate(string $name): array
    {
        $diskName = $this->diskName();
        $key = $this->objectKey($name);
        $this->backups->verify($name); // validate authenticated contents before copying
        $path = $this->backups->path($name);
        $hash = hash_file('sha256', $path);
        if ($hash === false) throw new RuntimeException('Cannot hash encrypted local backup.');
        $stream = fopen($path, 'rb');
        if (!$stream) throw new RuntimeException('Cannot read local backup.');
        try {
            if (Storage::disk($diskName)->put($key, $stream, ['visibility' => 'private']) === false) {
                throw new RuntimeException('Off-site upload failed. Local backup has been retained.');
            }
        } finally {
            fclose($stream);
        }
        $remoteHash = $this->readRemoteHash($diskName, $key);
        if (!hash_equals($hash, $remoteHash)) {
            throw new RuntimeException('Off-site SHA-256 mismatch. Local backup retained; remote copy NOT verified.');
        }
        $receipt = [
            'name' => $name,
            'disk' => $diskName,
            'key' => $key,
            'sha256' => $hash,
            'verified_at' => now()->toIso8601String(),
            'bytes' => filesize($path),
        ];
        // Operational metadata only; do not treat a receipt as proof of later availability.
        File::put($this->backups->directory().'/.last-offsite-success.json', json_encode($receipt, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        return $receipt;
    }

    public function verify(string $name): array
    {
        $diskName = $this->diskName();
        $key = $this->objectKey($name);
        $this->backups->verify($name);
        $localHash = hash_file('sha256', $this->backups->path($name));
        if (!$localHash || !hash_equals($localHash, $this->readRemoteHash($diskName, $key))) {
            throw new RuntimeException('Remote backup differs from the locally verified encrypted archive.');
        }
        return ['disk' => $diskName, 'key' => $key, 'sha256' => $localHash, 'verified' => true];
    }

    /**
     * Fetch a remote object into the encrypted local backup directory, verifying authenticated
     * payload before retaining it. No database or upload data is restored by this method.
     */
    public function recover(string $name): array
    {
        $diskName = $this->diskName();
        if (!preg_match('/\Aafrin-erp-\d{4}-\d{2}-\d{2}_\d{6}(?:_\d{6})?\.json\.gz\.enc\z/D', $name)) {
            throw new RuntimeException('Invalid encrypted backup filename.');
        }
        $prefix = trim((string) config('erp_backup.offsite_prefix', 'afrin-erp'), '/');
        if ($prefix === '' || strlen($prefix) > 120 || !preg_match('#\A[a-zA-Z0-9_-]+(?:/[a-zA-Z0-9_-]+)*\z#D', $prefix)) {
            throw new RuntimeException('Invalid off-site prefix.');
        }
        $key = $prefix.'/'.$name;
        $target = $this->backups->directory().DIRECTORY_SEPARATOR.$name;
        if (file_exists($target) || is_link($target)) {
            throw new RuntimeException('Local backup already exists. Refusing to overwrite it.');
        }
        $stream = Storage::disk($diskName)->readStream($key);
        if (!is_resource($stream)) throw new RuntimeException('Remote backup is unavailable.');
        $temp = tempnam($this->backups->directory(), '.remote-recovery-');
        if ($temp === false) { fclose($stream); throw new RuntimeException('Unable to stage remote backup.'); }
        @chmod($temp, 0600);
        try {
            $output = fopen($temp, 'wb');
            if (!$output) throw new RuntimeException('Cannot open temporary backup file.');
            try {
                $max = (int) config('erp_backup.max_archive_bytes', 150 * 1024 * 1024);
                $copied = 0;
                while (!feof($stream)) {
                    $part = fread($stream, 1024 * 1024);
                    if ($part === false) throw new RuntimeException('Remote backup download failed.');
                    $copied += strlen($part);
                    if ($copied > $max) throw new RuntimeException('Remote encrypted backup exceeds safety limit.');
                    if ($part !== '' && fwrite($output, $part) !== strlen($part)) {
                        throw new RuntimeException('Remote backup staging failed.');
                    }
                }
            } finally { fclose($output); }
        } catch (\Throwable $e) {
            @unlink($temp);
            throw $e;
        } finally { fclose($stream); }
        try {
            if (!rename($temp, $target)) throw new RuntimeException('Failed to finalize encrypted backup download.');
            @chmod($target, 0600);
            try {
                $info = $this->backups->verify($name); // authenticated decrypt + manifest check
            } catch (\Throwable $error) {
                @unlink($target);
                throw $error;
            }
            return ['name' => $name, 'sha256' => hash_file('sha256', $target), 'tables' => $info['tables'], 'files' => $info['files']];
        } finally {
            if (is_file($temp)) @unlink($temp);
        }
    }

    public function status(): array
    {
        $file = $this->backups->directory().'/.last-offsite-success.json';
        $receipt = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
        return ['configured' => $this->configured(), 'last_verified' => is_array($receipt) ? $receipt : null];
    }
}
