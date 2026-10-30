<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\BackupService;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('erp:backup', function (BackupService $backups, \App\Services\BackupOffsiteService $offsite) {
    if (config('erp_backup.require_offsite', false) && !$offsite->configured()) {
        $this->error('Off-site backup REQUIRED but disabled. No local-only success is reported.');
        return 1;
    }
    $name = $backups->create();
    $this->info("Encrypted local ERP backup created: {$name}");
    if ($offsite->configured()) {
        // On failure, command exits nonzero and leaves local encrypted archive intact.
        $receipt = $offsite->replicate($name);
        $this->info("Remote backup verified by SHA-256: {$receipt['sha256']}");
    } else {
        $this->warn('OFF-SITE NOT CONFIGURED: this is a LOCAL backup only.');
    }
    $backups->prune();
    return 0;
})->purpose('Create ERP encrypted snapshot and optionally verify off-site copy');

Schedule::command('erp:backup')->dailyAt('02:00')->withoutOverlapping();

// Verify authenticated archive contents without mutating business data.
Artisan::command('erp:backup:verify {name}', function (BackupService $backups) {
    $details = $backups->verify($this->argument('name'));
    $this->info("Verified: {$details['tables']} tables; {$details['files']} uploaded files.");
})->purpose('Verify ERP encrypted-backup integrity before attempting recovery');

// Offline maintenance-mode restore. Never run this command on an active live application.
Artisan::command('erp:backup:restore {name}', function (BackupService $backups) {
    if (!app()->isDownForMaintenance()) {
        $this->error('Restore refused: put this ERP into maintenance mode (php artisan down).');
        return 1;
    }
    $name = $this->argument('name');
    $details = $backups->verify($name);
    $this->warn("About to replace ERP data using {$name} ({$details['tables']} tables). Uploaded files are not transactional.");
    if (!$this->confirm('Do you have an OFF-HOST verified database + files snapshot and are you on the correct server?', false)) {
        $this->error('Restore cancelled.');
        return 1;
    }
    $backups->restore($name);
    $this->info('Restore completed. Verify financial totals and run tests before php artisan up.');
    return 0;
})->purpose('Interactively restore a verified ERP backup in maintenance mode');

// READ ONLY: check the actual database without changing a single transaction.
Artisan::command('erp:accounting:audit {--as-of= : YYYY-MM-DD accounting cutoff} {--json : Output JSON for archiving}',
    function (\App\Services\Accounting\AccountingAuditService $service) {
        $date = (string) ($this->option('as-of') ?: now()->toDateString());
        try {
            $parsed = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $date);
            if (!$parsed || $parsed->toDateString() !== $date) throw new \InvalidArgumentException('Invalid date');
            $date = $parsed->toDateString();
        } catch (\Throwable $e) {
            $this->error('Date must be YYYY-MM-DD.');
            return 2;
        }
        $result = $service->run($date);
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->info('Read-only accounting audit at ' . $date);
            foreach ($result['checks'] as $label => $passed) {
                $this->line(($passed ? 'PASS ' : 'FAIL ') . str_replace('_', ' ', $label));
            }
            $this->line(json_encode($result['values'], JSON_PRETTY_PRINT));
        }
        return $result['pass'] ? 0 : 1;
    }
)->purpose('Read-only verification of journal, P&L, balance sheet, AR/AP and source postings');

// READ ONLY: cashbook per account and customer/supplier subledger vs GL control accounts.
Artisan::command('erp:ledger-cashbook:audit {--from= : YYYY-MM-DD start date} {--to= : YYYY-MM-DD end date} {--json : JSON output}',
    function (\App\Services\Accounting\LedgerCashbookAuditService $service) {
        $from = (string) ($this->option('from') ?: now()->startOfMonth()->toDateString());
        $to = (string) ($this->option('to') ?: now()->toDateString());
        foreach ([$from, $to] as $date) {
            $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
            if (! $parsed || $parsed->format('Y-m-d') !== $date) {
                $this->error('Dates must be valid YYYY-MM-DD values.');
                return 2;
            }
        }
        if ($from > $to) {
            $this->error('--from must not be later than --to.');
            return 2;
        }
        $result = $service->run($from, $to);
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->info("Read-only cashbook and ledger audit: {$from} to {$to}");
            foreach ($result['checks'] as $label => $passed) {
                $this->line(($passed ? 'PASS ' : 'FAIL ') . str_replace('_', ' ', $label));
            }
            $this->line(json_encode(['cash_bank_totals'=>$result['cash_bank_totals'], 'party_ledgers'=>$result['party_ledgers']], JSON_PRETTY_PRINT));
        }
        return $result['pass'] ? 0 : 1;
    }
)->purpose('Read-only customer/supplier ledger and cashbook control-account audit');

// READ ONLY: posted-GL Profit & Loss classification, net profit and voucher integrity.
Artisan::command('erp:profit-loss:audit {--from= : YYYY-MM-DD start} {--to= : YYYY-MM-DD end} {--json : Structured result}',
    function (\App\Services\Reports\ProfitLossAuditService $audit) {
        $from = (string) ($this->option('from') ?: now()->startOfMonth()->toDateString());
        $to = (string) ($this->option('to') ?: now()->toDateString());
        foreach ([$from, $to] as $date) {
            $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
            if (! $parsed || $parsed->format('Y-m-d') !== $date) {
                $this->error('Use valid YYYY-MM-DD dates.');
                return 2;
            }
        }
        if ($from > $to) {
            $this->error('Start date cannot be later than end date.');
            return 2;
        }
        $result = $audit->run($from, $to);
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->info("Posted-GL Profit & Loss audit: {$from} to {$to}");
            foreach ($result['checks'] as $label => $passed) {
                $this->line(($passed ? 'PASS ' : 'REVIEW ') . str_replace('_', ' ', $label));
            }
            $this->line(json_encode($result['values'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->line($result['note']);
        }
        return $result['pass'] ? 0 : 1;
    }
)->purpose('Read-only Profit & Loss and posted journal validation');

// READ ONLY: stock levels, cost layers, FEFO batches, transfers and GL inventory.
Artisan::command('erp:inventory:audit {--json : Output JSON for record-keeping}', function (\App\Services\Inventory\InventoryAuditService $service) {
    $result = $service->run();
    if ($this->option('json')) {
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->info('Read-only inventory audit: ' . ($result['pass'] ? 'PASS' : 'REVIEW REQUIRED'));
        foreach ($result['checks'] as $label => $passed) {
            $this->line(($passed ? 'PASS ' : 'FAIL ') . str_replace('_', ' ', $label));
        }
        $this->line(json_encode($result['values'], JSON_PRETTY_PRINT));
        foreach ($result['examples'] as $label => $samples) {
            if ($samples) $this->warn($label . ': ' . json_encode($samples));
        }
    }
    return $result['pass'] ? 0 : 1;
})->purpose('Read-only inventory accuracy and GL valuation reconciliation');

// READ ONLY: focused multi-warehouse configuration and transfer history review.
Artisan::command('erp:warehouses:audit {--json : Structured output}', function (\App\Services\Inventory\InventoryAuditService $service) {
    $full = $service->run();
    $keys = ['posted_warehouse_transfers_balanced', 'warehouse_default_configuration_valid',
        'warehouse_matches_stock_movements', 'warehouse_matches_cost_layers',
        'tracked_batches_match_stock', 'active_reservations_match_reserved_quantity'];
    $checks = array_intersect_key($full['checks'], array_flip($keys));
    $result = [
        'pass' => ! in_array(false, $checks, true),
        'checks' => $checks,
        'mismatch_counts' => $full['values']['mismatch_counts'],
        'examples' => array_intersect_key($full['examples'], array_flip(['transfer_mismatch', 'warehouse_configuration',
            'movement_mismatch', 'cost_layer_mismatch', 'batch_mismatch', 'reservation_register_mismatch'])),
    ];
    if ($this->option('json')) {
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->info('Read-only warehouse audit: ' . ($result['pass'] ? 'PASS' : 'REVIEW REQUIRED'));
        foreach ($checks as $label => $pass) $this->line(($pass ? 'PASS ' : 'REVIEW ') . $label);
        foreach ($result['examples'] as $label => $rows) if ($rows) $this->warn($label . ': ' . json_encode($rows));
    }
    return $result['pass'] ? 0 : 1;
})->purpose('Read-only warehouse defaults, stock layers, and posted-transfer checks');

// READ ONLY: accounting bank statement coverage, cleared-line integrity and legacy snapshots.
Artisan::command('erp:bank-reconciliation:audit {--as-of= : YYYY-MM-DD cutoff} {--json : Structured output}',
    function (\App\Services\Accounting\BankReconciliationAuditService $service) {
        $date = (string) ($this->option('as-of') ?: now()->toDateString());
        try {
            $parsed = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $date);
            if (!$parsed || $parsed->toDateString() !== $date) throw new \InvalidArgumentException();
        } catch (\Throwable $e) {
            $this->error('Invalid --as-of. Use YYYY-MM-DD.');
            return 2;
        }
        $result = $service->run($date);
        if ($this->option('json')) $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        else {
            foreach ($result['checks'] as $check => $ok) $this->line(($ok ? 'PASS' : 'REVIEW') . ' ' . $check);
            $this->line(json_encode($result['counts'], JSON_PRETTY_PRINT));
            foreach ($result['examples'] as $type => $rows) if ($rows) $this->warn($type . ': ' . json_encode($rows));
            foreach ($result['warnings'] as $warning) $this->warn($warning);
        }
        return $result['pass'] ? 0 : 1;
    }
)->purpose('Read-only historical bank reconciliation and journal clearing audit');

// No database or upload changes: checks remote encrypted object bytes against local verified backup.
Artisan::command('erp:backup:offsite {name}', function (\App\Services\BackupOffsiteService $offsite) {
    $receipt = $offsite->replicate($this->argument('name'));
    $this->info(json_encode($receipt, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return 0;
})->purpose('Replicate an encrypted ERP backup to off-site object storage and read it back');

Artisan::command('erp:backup:offsite:verify {name}', function (\App\Services\BackupOffsiteService $offsite) {
    $result = $offsite->verify($this->argument('name'));
    $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return 0;
})->purpose('Re-read remote encrypted backup and compare its SHA-256 to local copy');

Artisan::command('erp:backup:offsite:recover {name}', function (\App\Services\BackupOffsiteService $offsite) {
    $result = $offsite->recover($this->argument('name'));
    $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return 0;
})->purpose('Download an off-site backup and authenticate archive, without modifying ERP data');

Artisan::command('erp:backup:status', function (\App\Services\BackupOffsiteService $offsite) {
    $status = $offsite->status();
    $this->line(json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return $status['configured'] && $status['last_verified'] ? 0 : 1;
})->purpose('Show whether off-site backups have been configured and last verified');

// DANGEROUS TO STAGING DATA ONLY: selected database MUST have the strict drill prefix.
Artisan::command('erp:backup:drill {name}', function (\App\Services\BackupRecoveryDrillService $drill) {
    $result = $drill->run($this->argument('name'));
    $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return 0;
})->purpose('Actually restore and hash-check all backup rows/uploads in an isolated staging DB');

// READ ONLY: source documents, discounts, posted stock quantities and return provenance.
Artisan::command('erp:commercial:audit {--json : Print the audit as JSON}', function (\App\Services\Commercial\CommercialAuditService $service) {
    $audit = $service->run();
    if ($this->option('json')) {
        $this->line(json_encode($audit, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->line('Sales / purchases / returns reviewed: '.json_encode($audit['checked']));
        foreach ($audit['issue_counts'] as $check => $number) {
            $this->line(($number ? 'REVIEW' : 'PASS') . ' ' . $check . ': ' . $number);
            foreach ($audit['examples'][$check] as $example) $this->warn(json_encode($example));
        }
        $this->line($audit['note']);
    }
    return $audit['pass'] ? 0 : 1;
})->purpose('Read-only sales/purchases/returns, discounts and posted stock audit');

// READ ONLY: check scanner code collisions and POS sale/stock/GL integrity.
Artisan::command('erp:pos:audit {--json : Output JSON audit results}', function (\App\Services\Pos\PosAuditService $service) {
    $result = $service->run();
    if ($this->option('json')) {
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->info('Posted/draft POS sales checked: '.$result['checked_pos_sales']);
        foreach ($result['counts'] as $name => $count) {
            $this->line(($count ? 'REVIEW' : 'PASS').' '.$name.': '.$count);
            foreach ($result['examples'][$name] as $row) $this->warn(json_encode($row));
        }
        $this->line($result['note']);
    }
    return $result['pass'] ? 0 : 1;
})->purpose('Read-only POS barcode ambiguity, sales, stock postings and journal audit');


// READ ONLY: effective role policy, privilege drift and unexpected direct grants.
Artisan::command('erp:permissions:audit {--json : Output structured results}', function () {
    $audit = app(\App\Services\Security\UserPermissionAuditService::class)->run();
    if ($this->option('json')) {
        $this->line(json_encode($audit, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->info('ERP role/permission audit: '.($audit['pass'] ? 'PASS' : 'REVIEW REQUIRED'));
        foreach ($audit['issue_counts'] as $check => $number) {
            $this->line(($number ? 'REVIEW' : 'PASS').' '.$check.': '.$number);
            foreach ($audit['examples'][$check] as $example) $this->warn(json_encode($example));
        }
        $this->line('No changes were made to users, roles or permissions.');
    }
    return $audit['pass'] ? 0 : 1;
})->purpose('Read-only audit of Admin, Seller and Sales Executive permission/role drift');

// EXPLICIT WRITE: apply only the fixed Seller/Sales Executive permission ceiling.
// Never changes journal/stock/business data or silently alters mixed-role assignments.
Artisan::command('erp:permissions:enforce {--apply : Confirm changes to restricted role grants and direct grants}', function () {
    if (!$this->option('apply')) {
        $this->warn('No data changed. Back up the database first, then re-run with --apply.');
        return 2;
    }
    $changes = \Illuminate\Support\Facades\DB::transaction(function () {
        $seller = \Spatie\Permission\Models\Role::findByName('Seller', 'web');
        $executive = \Spatie\Permission\Models\Role::findByName('Sales Executive', 'web');
        $allowed = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
            ->whereIn('name', \App\Support\RoleSecurity::SELLER_ALLOWED)->pluck('name')->all();
        if (count($allowed) !== count(\App\Support\RoleSecurity::SELLER_ALLOWED)) {
            throw new \RuntimeException('One or more required Seller permissions are missing. Run RolePermissionSeeder on staging first.');
        }
        $seller->syncPermissions($allowed);
        $execAllowed = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
            ->whereIn('name', \App\Support\RoleSecurity::SALES_EXEC_ALLOWED)->pluck('name')->all();
        if (count($execAllowed) !== count(\App\Support\RoleSecurity::SALES_EXEC_ALLOWED)) {
            throw new \RuntimeException('Required Sales Executive permissions are missing. Run RolePermissionSeeder on staging first.');
        }
        $executive->syncPermissions($execAllowed);

        $sellerUsers = \App\Models\User::role('Seller')->get();
        foreach ($sellerUsers as $user) $user->syncPermissions([]);
        $executiveUsers = \App\Models\User::role('Sales Executive')->get();
        foreach ($executiveUsers as $user) {
            $user->syncPermissions([]);
        }
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return ['seller_users_checked' => $sellerUsers->count(), 'sales_executives_checked' => $executiveUsers->count()];
    });
    $this->info('Restricted role permissions reconciled: '.json_encode($changes));
    $this->warn('Mixed-role assignments (if any) were NOT altered. Run erp:permissions:audit and review them manually.');
    return 0;
})->purpose('With --apply, reconcile only Seller/Sales Executive core role grants and direct grants');

// READ ONLY: report GL balancing, posted voucher integrity, document FX and return provenance.
Artisan::command('erp:reports:audit {--from= : YYYY-MM-DD (default month start)} {--to= : YYYY-MM-DD (default today)} {--json : Structured audit output}',
    function (\App\Services\Reports\ReportAuditService $service) {
        $from = (string) ($this->option('from') ?: now()->startOfMonth()->toDateString());
        $to = (string) ($this->option('to') ?: now()->toDateString());
        foreach ([$from, $to] as $date) {
            try {
                $parsed = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $date);
                if (!$parsed || $parsed->toDateString() !== $date) throw new \InvalidArgumentException();
            } catch (\Throwable $e) {
                $this->error('Dates must be real calendar dates in YYYY-MM-DD format.');
                return 2;
            }
        }
        if ($from > $to) {
            $this->error('--from must not exceed --to.');
            return 2;
        }
        $result = $service->run($from, $to);
        if ($this->option('json')) $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        else {
            $this->info('Read-only Financial Reports audit: '.$from.' through '.$to);
            foreach ($result['checks'] as $name => $pass) $this->line(($pass ? 'PASS' : 'REVIEW').' '.$name);
            $this->line(json_encode($result['metrics'], JSON_PRETTY_PRINT));
            $this->line(json_encode($result['issue_counts'], JSON_PRETTY_PRINT));
            $this->line($result['note']);
        }
        return $result['pass'] ? 0 : 1;
    }
)->purpose('Read-only validation of financial reports, vouchers, FX totals and linked returns');

// FINAL RELEASE GATE — read-only. Never migrates, corrects, restores or posts data.
Artisan::command('erp:release:check {--as-of= : YYYY-MM-DD date} {--preflight-only : Do not connect to the database} {--json : Print JSON}', function (\App\Services\Quality\ReleaseAuditService $audit) {
    $date = (string) ($this->option('as-of') ?: now()->toDateString());
    try {
        $parsed = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $date);
        if (!$parsed || $parsed->toDateString() !== $date) throw new \InvalidArgumentException('Invalid date');
    } catch (\Throwable $e) {
        $this->error('Invalid --as-of date; use YYYY-MM-DD.');
        return 2;
    }
    $result = $audit->run($date, (bool) $this->option('preflight-only'));
    if ($this->option('json')) {
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));
    } else {
        $this->line('M/S Afrin Traders ERP — Read-only release check ('.$result['mode'].')');
        foreach ($result['checks'] as $name => $check) {
            $this->line(str_pad($check['status'], 8).' '.str_replace('_', ' ', $name).': '.$check['detail']);
        }
        $this->warn('Manual acceptance remains required: '.implode('; ', $result['manual_acceptance_required']));
        $this->line('No data was changed. Production signoff is not automatic.');
    }
    return $result['blocking_checks'] ? 1 : 0;
})->purpose('Read-only combined ERP release readiness gate: accounting, stock, reports, roles, backups');

// READ ONLY: verify posted physical delivery notes against their source invoice lines.
Artisan::command('erp:invoice-delivery:audit {--json : Structured audit output}', function (\App\Services\Commercial\DeliveryChallanAuditService $service) {
    $result = $service->run();
    if ($this->option('json')) {
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->line('Invoice/Challan Audit: '.($result['pass'] ? 'PASS' : 'REVIEW REQUIRED'));
        $this->line(json_encode($result['counts'], JSON_PRETTY_PRINT));
        foreach ($result['issues_sample'] as $issue) $this->warn(json_encode($issue));
    }
    return $result['pass'] ? 0 : 1;
})->purpose('Read-only validation of delivery challans, posted quantities and source invoices');


// Read only: verify customer/supplier documents, opening ledgers and receipts.
Artisan::command('erp:parties:audit {--json : Output structured results}', function (\App\Services\Parties\PartyManagementAuditService $audit) {
    $result = $audit->run();
    if ($this->option('json')) {
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->info('Customer & supplier integrity audit: '.($result['pass'] ? 'PASS' : 'REVIEW REQUIRED'));
        foreach ($result['counts'] as $name => $count) {
            $this->line(($count === 0 ? 'PASS' : 'REVIEW').' '.$name.': '.$count);
        }
    }
    return $result['pass'] ? 0 : 1;
})->purpose('Read-only customer/supplier integrity, linked payments and opening ledgers');

// READ ONLY — verifies due collections, payment posting, loan balance and repayment schedule.
Artisan::command('erp:dues-loans:audit {--as-of= : YYYY-MM-DD (defaults today)} {--json : Structured output}', function (\App\Services\Collections\DueLoanAuditService $audit) {
    $date = (string) ($this->option('as-of') ?: now()->toDateString());
    try {
        $parsed = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d', $date);
        if (!$parsed || $parsed->toDateString() !== $date) throw new \InvalidArgumentException();
    } catch (\Throwable $e) {
        $this->error('Invalid --as-of date; use YYYY-MM-DD.');
        return 2;
    }
    $result = $audit->run($date);
    if ($this->option('json')) $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    else {
        $this->info('Due, Collection & Loan integrity: '.($result['pass'] ? 'PASS' : 'REVIEW REQUIRED'));
        foreach ($result['counts'] as $key => $count) $this->line(($count ? 'REVIEW' : 'PASS').' '.$key.': '.$count);
        $this->line(json_encode($result['metrics'], JSON_PRETTY_PRINT));
        $this->warn($result['note']);
    }
    return $result['pass'] ? 0 : 1;
})->purpose('Read-only audit of due balances, collections, loan principal, interest, schedule and GL journals');

// Barcode catalogue QA; does not depend on the POS checkout-requests migration.
Artisan::command('erp:barcode:audit {--json : Output machine-readable audit}', function (\App\Services\Barcode\BarcodeAuditService $service) {
    $result = $service->run();
    if ($this->option('json')) {
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->info('Barcode catalogue audit: '.($result['pass'] ? 'PASS' : 'ISSUES FOUND'));
        $this->line('Active POS stock products: '.$result['checked_active_stock_products']);
        foreach ($result['issues'] as $name => $count) {
            $this->line("{$name}: {$count}");
        }
        $this->line('Review details: php artisan erp:barcode:audit --json');
    }
    return $result['pass'] ? 0 : 1;
})->purpose('Read-only scan-code ambiguity and Code 39 label printability audit');

// READ ONLY: product variants, batch traceability, expiry status and stock linkage.
Artisan::command('erp:variants-batches:audit {--as-of= : YYYY-MM-DD cutoff} {--json : JSON output}',
    function (\App\Services\Inventory\BatchVariantAuditService $service) {
        $date = (string) ($this->option('as-of') ?: now()->toDateString());
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (! $parsed || $parsed->format('Y-m-d') !== $date) {
            $this->error('Date must be valid YYYY-MM-DD.');
            return 2;
        }
        $result = $service->run($date);
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->info('Read-only product variant and batch audit: '.($result['pass'] ? 'PASS' : 'REVIEW REQUIRED'));
            foreach ($result['counts'] as $label => $count) $this->line($label.': '.$count);
            foreach ($result['issues'] as $label => $samples) $this->warn('ISSUE '.$label.': '.json_encode($samples));
            foreach ($result['alerts'] as $label => $samples) $this->warn('ALERT '.$label.': '.json_encode($samples));
        }
        return $result['pass'] ? 0 : 1;
    }
)->purpose('Read-only batch/expiry/variant integrity QA');

// READ ONLY: FIFO receipt layers, moving weighted average and recorded valuation.
Artisan::command('erp:costing:audit {--json : Print valuation and layer coverage as JSON}',
    function (\App\Services\Inventory\InventoryValuationService $valuation,
              \App\Services\Inventory\InventoryAuditService $inventory) {
        $snapshot = $valuation->snapshot();
        $audit = $inventory->run();
        $result = [
            'pass' => $snapshot['complete'] && $audit['checks']['warehouse_matches_cost_layers']
                && $audit['checks']['fifo_layers_valid']
                && $audit['checks']['gl_inventory_matches_stock_valuation'],
            'stock_valuation_complete' => $snapshot['complete'],
            'cost_layer_review_rows' => $snapshot['review_count'],
            'reportable_valuation_bdt' => $snapshot['total_value'],
            'inventory_to_gl_difference_bdt' => $audit['values']['inventory_to_gl_difference'],
            'layer_quantity_matched' => $audit['checks']['warehouse_matches_cost_layers'],
            'invalid_cost_layers' => $audit['values']['invalid_cost_layers'],
            'review_examples' => $snapshot['rows']->where('status', 'review_required')->take(15)->values()->all(),
        ];
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->info('FIFO / Weighted Average costing: '.($result['pass'] ? 'PASS' : 'REVIEW REQUIRED'));
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        return $result['pass'] ? 0 : 1;
    }
)->purpose('Read-only FIFO cost-layer, weighted average, and inventory-to-GL audit');

// READ ONLY: validate expense drafts, posted journals, account types and linked cash/bank postings.
Artisan::command('erp:expenses:audit {--as-of= : Optional YYYY-MM-DD cutoff} {--json : Output JSON}',
    function (\App\Services\Expenses\ExpenseManagementAuditService $service) {
        $date = $this->option('as-of') ?: null;
        if ($date !== null) {
            $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
            if (!$parsed || $parsed->format('Y-m-d') !== $date) {
                $this->error('--as-of must be a valid YYYY-MM-DD date.');
                return 2;
            }
        }
        $report = $service->run($date);
        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->info('Read-only Expense Management Audit: ' . ($date ?? 'all dates'));
            foreach ($report['counts'] as $label => $value) {
                $this->line($label . ': ' . $value);
            }
            foreach ($report['samples'] as $sample) {
                $this->warn($sample['type'] . ': ' . $sample['reference']);
            }
        }
        return $report['pass'] ? 0 : 1;
    }
)->purpose('Read-only Expense Management audit (drafts, GL, cash/bank and duplicate postings)');
