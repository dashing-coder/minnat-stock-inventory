<?php

namespace Tests\Feature;

use App\Services\BackupService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class BackupSecurityTest extends TestCase
{
    public function test_backup_path_rejects_directory_traversal(): void
    {
        $this->expectException(NotFoundHttpException::class);
        app(BackupService::class)->path('../.env');
    }

    public function test_unencrypted_legacy_archive_cannot_be_restored_by_default(): void
    {
        config(['erp_backup.allow_legacy_restore' => false]);
        $service = app(BackupService::class);
        $name = 'afrin-erp-2026-10-27_000000.json.gz';
        $path = $service->directory().DIRECTORY_SEPARATOR.$name;
        File::put($path, gzencode('{"format":"afrin-erp-backup-v1","tables":{"accounts":[]},"files":{}}'));
        try {
            $this->expectException(\RuntimeException::class);
            $service->verify($name);
        } finally {
            File::delete($path);
        }
    }

    public function test_encrypted_backup_rejects_modified_payload(): void
    {
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        $service = app(BackupService::class);
        $name = 'afrin-erp-2026-10-27_000001.json.gz.enc';
        $path = $service->directory().DIRECTORY_SEPARATOR.$name;
        File::put($path, 'tampered ciphertext');
        try {
            $this->expectException(DecryptException::class);
            $service->verify($name);
        } finally {
            File::delete($path);
        }
    }
}
