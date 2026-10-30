<?php

namespace Tests\Feature;

use App\Services\BackupOffsiteService;
use App\Services\BackupRecoveryDrillService;
use App\Services\BackupService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class BackupDisasterRecoveryTest extends TestCase
{
    private function testArchive(): string
    {
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        app()->forgetInstance('encrypter');
        Crypt::clearResolvedInstance('encrypter');
        $name = 'afrin-erp-2026-10-27_120000_123456.json.gz.enc';
        $data = ['format' => 'afrin-erp-backup-v2', 'created_at' => now()->toIso8601String(),
            'tables' => ['dummy' => [['id' => 1, 'name' => 'Test']]], 'files' => ['private/proof.txt' => base64_encode('sample')]];
        $bytes = Crypt::encryptString(gzencode(json_encode($data, JSON_THROW_ON_ERROR)));
        File::put(app(BackupService::class)->directory().DIRECTORY_SEPARATOR.$name, $bytes);
        return $name;
    }

    private function configureFakeRemote(): void
    {
        config([
            'erp_backup.offsite_enabled' => true,
            'erp_backup.offsite_prefix' => 'secured/afrin-erp',
            'filesystems.disks.erp_offsite.driver' => 's3',
            'filesystems.disks.erp_offsite.bucket' => 'test-private-bucket',
            'filesystems.disks.erp_offsite.visibility' => 'private',
        ]);
        // Fake adapter exercises round-trip hashes without network access; it does
        // NOT prove remote availability. The actual S3 recovery drill is still required.
        Storage::fake('erp_offsite');
    }

    public function test_offsite_requires_an_explicit_private_remote_provider(): void
    {
        config(['erp_backup.offsite_enabled' => true, 'filesystems.disks.erp_offsite.driver' => 'local']);
        $this->expectException(RuntimeException::class);
        app(BackupOffsiteService::class)->replicate('unavailable.json.gz.enc');
    }

    public function test_offsite_upload_is_read_back_and_integrity_checked(): void
    {
        $this->configureFakeRemote();
        $name = $this->testArchive();
        try {
            $offsite = app(BackupOffsiteService::class);
            $receipt = $offsite->replicate($name);
            $this->assertSame(hash_file('sha256', app(BackupService::class)->path($name)), $receipt['sha256']);
            $this->assertTrue($offsite->verify($name)['verified']);
            Storage::disk('erp_offsite')->put('secured/afrin-erp/'.$name, 'damaged');
            $this->expectException(RuntimeException::class);
            $offsite->verify($name);
        } finally {
            File::delete(app(BackupService::class)->directory().DIRECTORY_SEPARATOR.$name);
        }
    }

    public function test_offsite_recovery_download_validates_authenticated_snapshot(): void
    {
        $this->configureFakeRemote();
        $name = $this->testArchive();
        $path = app(BackupService::class)->path($name);
        try {
            $offsite = app(BackupOffsiteService::class);
            $offsite->replicate($name);
            File::delete($path); // simulate loss of on-server local backup
            $recovered = $offsite->recover($name);
            $this->assertSame($name, $recovered['name']);
            $this->assertSame(1, $recovered['tables']);
            $this->assertSame(1, $recovered['files']);
        } finally {
            File::delete($path);
        }
    }

    public function test_recovery_drill_is_disabled_by_default(): void
    {
        config(['erp_backup.drill_enabled' => false]);
        $this->expectException(RuntimeException::class);
        app(BackupRecoveryDrillService::class)->run('missing.json.gz.enc');
    }
}
