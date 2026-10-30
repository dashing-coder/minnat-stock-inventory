<?php

namespace App\Services\Quality;

use App\Services\Accounting\AccountingAuditService;
use App\Services\Accounting\BankReconciliationAuditService;
use App\Services\BackupOffsiteService;
use App\Services\Commercial\CommercialAuditService;
use App\Services\Collections\DueLoanAuditService;
use App\Services\Inventory\InventoryAuditService;
use App\Services\Pos\PosAuditService;
use App\Services\Reports\ReportAuditService;
use App\Services\Security\UserPermissionAuditService;
use Illuminate\Support\Facades\DB;

/**
 * Cross-module RELEASE PREFLIGHT. Read-only: never corrects history or posts GL/stock.
 * This is not a replacement for physical POS, backup recovery or statement matching.
 */
final class ReleaseAuditService
{
    public function run(string $asOf, bool $preflightOnly = false): array
    {
        $checks = [];
        $add = static function (string $name, string $status, string $detail = '') use (&$checks): void {
            $checks[$name] = ['status' => $status, 'detail' => $detail];
        };

        $required = ['openssl', 'fileinfo', 'mbstring', 'dom', 'xmlwriter', 'pdo_mysql'];
        $missing = array_values(array_filter($required, static fn ($extension) => !extension_loaded($extension)));
        $add('php_extensions', !$missing ? 'PASS' : 'FAIL', !$missing ? 'Required PHP extensions are loaded.' : 'Missing: '.implode(', ', $missing));

        $folders = [
            'storage/framework/sessions', 'storage/framework/views', 'storage/framework/cache/data',
            'storage/logs', 'storage/app/private', 'storage/app/public', 'bootstrap/cache',
        ];
        $unwritable = [];
        foreach ($folders as $folder) {
            $directory = base_path($folder);
            if (!is_dir($directory) || !is_writable($directory)) {
                $unwritable[] = $folder;
            }
        }
        $add('runtime_directories', !$unwritable ? 'PASS' : 'FAIL', !$unwritable ? 'All required directories are writable.' : 'Missing/unwritable: '.implode(', ', $unwritable));

        $appKey = (string) config('app.key', '');
        $add('app_key', $appKey === '' ? 'FAIL' : 'PASS', $appKey === '' ? 'APP_KEY is empty. Do not regenerate an existing production key.' : 'APP_KEY is configured (value not displayed).');
        $add('debug_mode', app()->environment('production') && (bool) config('app.debug') ? 'FAIL' : 'PASS',
            app()->environment('production') && (bool) config('app.debug') ? 'Debug mode must be disabled in production.' : 'Production-debug guard satisfied.');

        if ($preflightOnly) {
            $add('live_business_data', 'NOT_RUN', 'Run without --preflight-only on your XAMPP staging database.');
        } else {
            try {
                DB::connection()->select('SELECT 1 AS connection_ok');
                $add('database_connection', 'PASS', 'Database responded to read-only query.');
                $migrationFiles = glob(database_path('migrations/*.php')) ?: [];
                $expected = array_map(static fn ($path) => pathinfo($path, PATHINFO_FILENAME), $migrationFiles);
                $recorded = DB::table('migrations')->pluck('migration')->all();
                $missingMigrations = array_values(array_diff($expected, $recorded));
                sort($missingMigrations);
                $add('migration_history', $missingMigrations ? 'FAIL' : 'PASS',
                    $missingMigrations ? 'Pending: '.implode(', ', $missingMigrations) : 'All source migrations recorded.');

                $services = [
                    'accounting' => static fn () => app(AccountingAuditService::class)->run($asOf),
                    'inventory' => static fn () => app(InventoryAuditService::class)->run(),
                    'commercial' => static fn () => app(CommercialAuditService::class)->run(),
                    'due_collection_loans' => static fn () => app(DueLoanAuditService::class)->run($asOf),
                    'bank_reconciliation' => static fn () => app(BankReconciliationAuditService::class)->run($asOf),
                    'pos' => static fn () => app(PosAuditService::class)->run(),
                    'permissions' => static fn () => app(UserPermissionAuditService::class)->run(),
                    'reports' => static fn () => app(ReportAuditService::class)->run($asOf, $asOf),
                ];
                foreach ($services as $name => $callback) {
                    try {
                        $result = $callback();
                        $counts = $result['issue_counts'] ?? $result['counts'] ?? $result['values']['mismatch_counts'] ?? [];
                        $details = is_array($counts) ? json_encode($counts, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR) : '';
                        $add($name, !empty($result['pass']) ? 'PASS' : 'FAIL', (string) $details);
                    } catch (\Throwable $e) {
                        // Avoid rendering SQL/credentials from an exception into generated QA reports.
                        $add($name, 'ERROR', 'Audit could not run: '.$e::class.'; inspect local application logs.');
                    }
                }
            } catch (\Throwable $e) {
                $add('database_connection', 'ERROR', 'Database unavailable ('.$e::class.'). Verify MySQL service, credentials and driver.');
                $add('live_business_data', 'NOT_RUN', 'Database-backed checks require a working staging connection.');
            }
        }

        try {
            $offsite = app(BackupOffsiteService::class)->status();
            $add('offsite_backup_status', $offsite['configured'] && $offsite['last_verified'] ? 'PASS' : 'REVIEW',
                $offsite['configured'] && $offsite['last_verified'] ? 'Verified off-site receipt exists; repeat full recovery drill.' : 'Off-site backup is not configured or has no verified receipt.');
        } catch (\Throwable $e) {
            $add('offsite_backup_status', 'ERROR', 'Backup status unavailable; review storage and configuration.');
        }

        $manual = [
            'real MySQL backup restore into isolated database',
            'actual off-site object readback',
            'physical scanner and receipt printer',
            'real bank statement matching',
            'Admin / Seller / Sales Executive browser authorization tests',
            'historical return/transfer/inventory GL exceptions',
            'browser Dashboard and report totals',
            'customer/supplier due statements and real loan repayment receipts',
        ];
        $blocking = array_keys(array_filter($checks, static fn ($check) => in_array($check['status'], ['FAIL', 'ERROR'], true)));
        return [
            'mode' => $preflightOnly ? 'preflight_only' : 'read_only_staging',
            'as_of' => $asOf,
            'checks' => $checks,
            'blocking_checks' => $blocking,
            'code_and_data_preflight_pass' => !$preflightOnly && count($blocking) === 0 && !in_array('NOT_RUN', array_column($checks, 'status'), true),
            'production_signoff_complete' => false, // physical/documentary acceptance cannot be automated
            'manual_acceptance_required' => $manual,
        ];
    }
}
