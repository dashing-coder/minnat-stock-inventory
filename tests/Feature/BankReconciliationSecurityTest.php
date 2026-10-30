<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BankReconciliationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);
    }

    public function test_valid_cleared_journal_lines_reconcile(): void
    {
        $bank = $this->account('BANK-TEST-01');
        $line = $this->line($bank->id, 'posted', '2026-10-15', 120);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 120, [$line]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bank_reconciliations', [
            'account_id' => $bank->id, 'statement_date' => '2026-10-20', 'status' => 'reconciled',
        ]);
        $this->assertDatabaseHas('bank_reconciliation_items', ['journal_line_id' => $line]);
    }

    public function test_cross_account_or_unposted_or_future_lines_are_rejected(): void
    {
        $bank = $this->account('BANK-TEST-02');
        $other = $this->account('BANK-TEST-03');
        $invalid = [
            $this->line($other->id, 'posted', '2026-10-15', 120),
            $this->line($bank->id, 'draft', '2026-10-15', 120),
            $this->line($bank->id, 'posted', '2026-10-25', 120),
        ];
        foreach ($invalid as $line) {
            $this->post(route('production-controls.bank.store'), $this->request($bank->id, 120, [$line]))
                ->assertSessionHasErrors('journal_line_ids');
        }
        $this->assertSame(0, DB::table('bank_reconciliations')->count());
    }

    public function test_unmatched_cleared_amount_remains_draft(): void
    {
        $bank = $this->account('BANK-TEST-04');
        $line = $this->line($bank->id, 'posted', '2026-10-15', 90);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 120, [$line]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bank_reconciliations', ['account_id' => $bank->id, 'status' => 'draft']);
    }

    public function test_later_bank_statement_includes_prior_finalized_cleared_lines(): void
    {
        $bank = $this->account('BANK-PRIOR');
        $first = $this->line($bank->id, 'posted', '2026-10-10', 120);
        $second = $this->line($bank->id, 'posted', '2026-10-21', 70);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 120, [$first]))
            ->assertSessionHasNoErrors();
        $this->post(route('production-controls.bank.store'), [
            'account_id' => $bank->id, 'statement_date' => '2026-10-25',
            'statement_balance' => '190.00', 'journal_line_ids' => [$second],
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bank_reconciliations', [
            'account_id' => $bank->id, 'statement_date' => '2026-10-25',
            'status' => 'reconciled', 'cleared_balance' => '190.00', 'difference' => '0.00',
        ]);
        $this->assertDatabaseCount('bank_reconciliation_items', 2);
    }

    public function test_an_already_finalized_line_cannot_be_cleared_twice(): void
    {
        $bank = $this->account('BANK-DOUBLE');
        $line = $this->line($bank->id, 'posted', '2026-10-10', 120);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 120, [$line]))
            ->assertSessionHasNoErrors();
        $this->post(route('production-controls.bank.store'), [
            'account_id' => $bank->id, 'statement_date' => '2026-10-25',
            'statement_balance' => '120', 'journal_line_ids' => [$line],
        ])->assertSessionHasErrors('journal_line_ids');
        $this->assertDatabaseCount('bank_reconciliations', 1);
    }

    public function test_mismatch_draft_can_accumulate_entries_then_finalize(): void
    {
        $bank = $this->account('BANK-DRAFT');
        $one = $this->line($bank->id, 'posted', '2026-10-10', 60);
        $two = $this->line($bank->id, 'posted', '2026-10-11', 40);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 100, [$one]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bank_reconciliations', ['account_id' => $bank->id, 'status' => 'draft']);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 100, [$two]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bank_reconciliations', ['account_id' => $bank->id,
            'status' => 'reconciled', 'cleared_balance' => '100.00']);
        $this->assertDatabaseCount('bank_reconciliations', 1);
        $this->assertDatabaseCount('bank_reconciliation_items', 2);
    }

    public function test_cannot_backdate_after_a_later_statement_was_finalized(): void
    {
        $bank = $this->account('BANK-BACKDATE');
        $line = $this->line($bank->id, 'posted', '2026-10-10', 50);
        $this->post(route('production-controls.bank.store'), [
            'account_id' => $bank->id, 'statement_date' => '2026-10-25',
            'statement_balance' => '50', 'journal_line_ids' => [$line],
        ])->assertSessionHasNoErrors();
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 0, []))
            ->assertSessionHasErrors('statement_date');
    }

    public function test_draft_reset_does_not_modify_finalized_statements(): void
    {
        $bank = $this->account('BANK-RESET');
        $line = $this->line($bank->id, 'posted', '2026-10-10', 30);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 40, [$line]))
            ->assertSessionHasNoErrors();
        $this->post(route('production-controls.bank.draft.reset'), [
            'account_id' => $bank->id, 'statement_date' => '2026-10-20',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('bank_reconciliations', 0);

        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 30, [$line]))
            ->assertSessionHasNoErrors();
        $this->post(route('production-controls.bank.draft.reset'), [
            'account_id' => $bank->id, 'statement_date' => '2026-10-20',
        ])->assertSessionHasErrors('statement_date');
        $this->assertDatabaseHas('bank_reconciliations', [
            'account_id' => $bank->id, 'status' => 'reconciled',
        ]);
    }

    public function test_rejects_more_than_two_decimal_places_in_statement_balance(): void
    {
        $bank = $this->account('BANK-PRECISION');
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 1.001, []))
            ->assertSessionHasErrors('statement_balance');
    }

    public function test_historical_audit_detects_duplicate_finalized_items(): void
    {
        $bank = $this->account('BANK-AUDIT');
        $line = $this->line($bank->id, 'posted', '2026-10-10', 10);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 10, [$line]))
            ->assertSessionHasNoErrors();
        $second = DB::table('bank_reconciliations')->insertGetId([
            'account_id' => $bank->id, 'statement_date' => '2026-10-21',
            'statement_balance' => '10', 'system_balance' => '10', 'cleared_balance' => '10',
            'difference' => '0', 'status' => 'reconciled',
            'closed_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('bank_reconciliation_items')->insert([
            'bank_reconciliation_id' => $second, 'journal_line_id' => $line,
            'amount' => '10.00', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $result = app(\App\Services\Accounting\BankReconciliationAuditService::class)->run('2026-10-25');
        $this->assertFalse($result['pass']);
        $this->assertFalse($result['checks']['no_double_cleared_finalized_lines']);
    }

    public function test_net_zero_unselected_journals_do_not_auto_finalize(): void
    {
        $bank = $this->account('BANK-ZERO-UNSELECTED');
        $one = $this->line($bank->id, 'posted', '2026-10-10', 10);
        $other = $this->line($bank->id, 'posted', '2026-10-11', 10);
        DB::table('journal_lines')->where('id', $other)->update(['debit' => 0, 'credit' => 10]);
        $this->post(route('production-controls.bank.store'), $this->request($bank->id, 0, []))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bank_reconciliations', [
            'account_id' => $bank->id, 'status' => 'draft',
        ]);
    }

    private function account(string $code): Account
    {
        return Account::create(['code' => $code, 'name' => $code, 'type' => 'asset',
            'subtype' => 'bank', 'is_active' => true]);
    }

    private function line(int $accountId, string $status, string $date, int $debit): int
    {
        $journalId = DB::table('journal_entries')->insertGetId([
            'journal_no' => 'TEST-'.uniqid(), 'journal_date' => $date, 'status' => $status,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return DB::table('journal_lines')->insertGetId([
            'journal_entry_id' => $journalId, 'account_id' => $accountId,
            'debit' => $debit, 'credit' => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function request(int $accountId, int $balance, array $lines): array
    {
        return [
            'account_id' => $accountId, 'statement_date' => '2026-10-20',
            'statement_balance' => $balance, 'journal_line_ids' => $lines,
        ];
    }
}
