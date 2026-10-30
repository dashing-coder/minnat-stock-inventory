<?php
namespace App\Http\Controllers;

use App\Models\Account;use App\Models\Customer;use App\Models\CustomerPayment;use App\Models\Expense;use App\Models\JournalEntry;use App\Models\JournalLine;use App\Models\Product;use App\Models\Purchase;use App\Models\PurchaseReturn;use App\Models\Sale;use App\Models\SaleReturn;use App\Models\Setting;use App\Models\Supplier;use App\Models\SupplierPayment;use App\Models\WarehouseStock;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;use Symfony\Component\HttpFoundation\StreamedResponse;use App\Services\Reports\CustomerLedgerReportService;use App\Services\Reports\ReportMoney;
class ReportController extends Controller
{
    public function index(){return view('reports.index');}


    public function clientLedger(Request $r, CustomerLedgerReportService $ledgerReport)
    {
        $r->validate([
            'customer_id' => 'nullable|integer|min:1',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d|after_or_equal:from',
            'type' => 'nullable|string|max:50',
        ]);
        $customers = Customer::query()->where('is_active', true)->orderBy('name')->get(['id','code','name','phone']);
        $customer = null;
        $ledger = [
            'entries' => collect(),
            'opening_balance' => 0.0,
            'closing_balance' => 0.0,
            'period_debit' => 0.0,
            'period_credit' => 0.0,
        ];
        $entryTypes = collect();

        $from = $r->input('from') ?: now()->startOfMonth()->toDateString();
        $to = $r->input('to') ?: now()->toDateString();
        abort_if($from > $to, 422, 'From date must not exceed To date.');
        $type = $r->filled('type') ? $r->string('type')->toString() : null;

        if ($r->filled('customer_id')) {
            $customer = Customer::query()->findOrFail($r->integer('customer_id'));
            if (! $customers->contains('id', $customer->id)) {
                $customers->push($customer);
                $customers = $customers->sortBy('name')->values();
            }

            $ledger = $ledgerReport->build($customer, $from, $to, $type);
            $entryTypes = $customer->ledgerEntries()->select('entry_type')->distinct()->orderBy('entry_type')->pluck('entry_type');
        } else {
            $entryTypes = \App\Models\CustomerLedgerEntry::query()->select('entry_type')->distinct()->orderBy('entry_type')->pluck('entry_type');
        }

        if ($r->boolean('csv')) {
            abort_unless($customer, 422, 'Select a customer before exporting the ledger.');
            return $this->clientLedgerCsv($customer, $ledger, $from, $to);
        }

        return view('reports.client-ledger', compact('customers','customer','ledger','entryTypes','from','to','type'));
    }

    /** Posted-GL income statement with COGS, contra revenue, margins and account drill-down. */
    public function profitLoss(Request $r, ?\App\Services\Reports\ProfitLossReportService $profitLoss = null)
    {
        [$from, $to] = $this->dates($r);
        $r->validate(['entity_id' => 'nullable|integer|min:1']);
        $accountId = $r->filled('entity_id') ? (int) $r->input('entity_id') : null;
        $accounts = Account::withTrashed()->whereIn('type', ['income', 'expense'])
            ->orderBy('code')->get(['id', 'code', 'name', 'type', 'deleted_at', 'is_active']);
        if ($accountId !== null) {
            abort_unless($accounts->contains('id', $accountId), 404, 'P&L account not found.');
        }
        $report = ($profitLoss ?? app(\App\Services\Reports\ProfitLossReportService::class))->build($from, $to, $accountId);
        $summary = $report['summary'];
        $groups = $report['groups'];
        $totals = [
            'Total Income' => number_format($summary['total_income'], 2),
            'Total Expense' => number_format($summary['total_expense'], 2),
            'Net Profit' => number_format($summary['net_profit'], 2),
        ];
        if ($r->boolean('csv')) {
            return response()->streamDownload(function () use ($report) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Profit & Loss (posted GL; BDT)']);
                fputcsv($out, ['Period', $report['from'], $report['to']]);
                if ($report['accountId'] !== null) fputcsv($out, ['Filtered Account ID', $report['accountId']]);
                fputcsv($out, []);
                fputcsv($out, ['Category', 'GL Account', 'Name', 'Signed Amount (BDT)']);
                foreach (\App\Services\Reports\ProfitLossReportService::GROUPS as $key => $title) {
                    foreach ($report['groups'][$key] as $account) {
                        fputcsv($out, [$title, $this->safeLedgerCsv($account['code']), $this->safeLedgerCsv($account['name']), number_format($account['amount'], 2, '.', '')]);
                    }
                }
                fputcsv($out, []);
                foreach ($report['summary'] as $name => $value) {
                    fputcsv($out, [$name, $value === null ? 'N/A' : number_format($value, 2, '.', '')]);
                }
                fclose($out);
            }, "profit-loss-{$from}-to-{$to}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
        }
        return view('reports.profit-loss', compact('from', 'to', 'accountId', 'accounts', 'report', 'summary', 'groups', 'totals'));
    }

    public function trialBalance(Request $r)
    {
        [$from, $to] = $this->dates($r);
        abort_if($from > $to, 422, 'From date must not exceed To date.');

        // Closing balances must include all posted entries through the report end-date,
        // not just this period's movements. Include inactive/soft-deleted accounts with GL history.
        $data = JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->where('journal_entries.status', 'posted')
            ->whereDate('journal_entries.journal_date', '<=', $to)
            ->when($r->filled('entity_id'), fn ($q) => $q->where('accounts.id', $r->entity_id))
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name')
            ->orderBy('accounts.code')
            ->selectRaw('accounts.code, accounts.name')
            ->selectRaw('SUM(CASE WHEN journal_entries.journal_date < ? THEN journal_lines.debit ELSE 0 END) AS opening_debit', [$from])
            ->selectRaw('SUM(CASE WHEN journal_entries.journal_date < ? THEN journal_lines.credit ELSE 0 END) AS opening_credit', [$from])
            ->selectRaw('SUM(CASE WHEN journal_entries.journal_date >= ? THEN journal_lines.debit ELSE 0 END) AS period_debit', [$from])
            ->selectRaw('SUM(CASE WHEN journal_entries.journal_date >= ? THEN journal_lines.credit ELSE 0 END) AS period_credit', [$from])
            ->get();

        $rows = [];
        $totals = ['opening_debit' => 0, 'opening_credit' => 0, 'period_debit' => 0, 'period_credit' => 0, 'closing_debit' => 0, 'closing_credit' => 0];
        foreach ($data as $entry) {
            $opening = round((float) $entry->opening_debit - (float) $entry->opening_credit, 2);
            $closing = round($opening + (float) $entry->period_debit - (float) $entry->period_credit, 2);
            $values = [
                'opening_debit' => max($opening, 0), 'opening_credit' => max(-$opening, 0),
                'period_debit' => (float) $entry->period_debit, 'period_credit' => (float) $entry->period_credit,
                'closing_debit' => max($closing, 0), 'closing_credit' => max(-$closing, 0),
            ];
            foreach ($values as $key => $value) $totals[$key] += $value;
            $rows[] = array_merge([$entry->code, $entry->name], array_map(fn ($value) => number_format($value, 2), array_values($values)));
        }
        return $this->table('Trial Balance',
            ['Code', 'Account', 'Opening Debit', 'Opening Credit', 'Period Debit', 'Period Credit', 'Closing Debit', 'Closing Credit'],
            $rows,
            array_combine(['Opening Debit', 'Opening Credit', 'Period Debit', 'Period Credit', 'Closing Debit', 'Closing Credit'],
                array_map(fn ($value) => number_format($value, 2), array_values($totals))), $r);
    }

    public function balanceSheet(Request $r){$r->validate(['to' => 'sometimes|date_format:Y-m-d']);$to=$r->input('to',now()->toDateString());$data=JournalLine::query()->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')->where('journal_entries.status','posted')->join('accounts','accounts.id','=','journal_lines.account_id')->whereDate('journal_entries.journal_date','<=',$to)->whereIn('accounts.type',['asset','liability','equity'])->when($r->filled('entity_id'),fn($q)=>$q->where('accounts.id',$r->entity_id))->groupBy('accounts.id','accounts.code','accounts.name','accounts.type')->orderBy('accounts.type')->orderBy('accounts.code')->selectRaw('accounts.code,accounts.name,accounts.type,SUM(journal_lines.debit) debit,SUM(journal_lines.credit) credit')->get();$rows=[];$tot=['asset'=>0,'liability'=>0,'equity'=>0];foreach($data as $x){$amt=$x->type==='asset'?(float)$x->debit-(float)$x->credit:(float)$x->credit-(float)$x->debit;$tot[$x->type]+=$amt;$rows[]=[$x->code,$x->name,ucfirst($x->type),number_format($amt,2)];}
        // Include unclosed cumulative retained earnings: P&L entries remain in the income/expense
        // accounts until an explicit year-end closing entry is posted. Without this, the
        // balance sheet can appear out of balance despite a perfectly balanced GL.
        $currentEarnings = (float) JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->where('journal_entries.status', 'posted')
            ->whereDate('journal_entries.journal_date', '<=', $to)
            ->whereIn('accounts.type', ['income', 'expense'])
            ->selectRaw('COALESCE(SUM(journal_lines.credit - journal_lines.debit),0) total')
            ->value('total');
        if (abs($currentEarnings) >= 0.005) {
            $rows[] = ['—', 'Unclosed retained earnings (cumulative)', 'Equity', number_format($currentEarnings,2)];
            $tot['equity'] += $currentEarnings;
        }
        return $this->table('Balance Sheet',['Code','Account','Type','Balance'],$rows,['Assets'=>number_format($tot['asset'],2),'Liabilities'=>number_format($tot['liability'],2),'Equity'=>number_format($tot['equity'],2),'Liabilities + Equity'=>number_format($tot['liability']+$tot['equity'],2)],$r);}

    public function cashFlow(Request $r){[$from,$to]=$this->dates($r);$data=JournalLine::query()->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')->where('journal_entries.status','posted')->join('accounts','accounts.id','=','journal_lines.account_id')->whereBetween('journal_entries.journal_date',[$from,$to])->where('accounts.type','asset')->whereIn('accounts.subtype',['cash','bank'])->when($r->filled('entity_id'),fn($q)=>$q->where('accounts.id',$r->entity_id))->groupBy('accounts.id','accounts.code','accounts.name')->selectRaw('accounts.code,accounts.name,SUM(journal_lines.debit) cash_in,SUM(journal_lines.credit) cash_out')->orderBy('accounts.code')->get();$rows=[];$in=0;$out=0;foreach($data as $x){$in+=(float)$x->cash_in;$out+=(float)$x->cash_out;$rows[]=[$x->code,$x->name,number_format($x->cash_in,2),number_format($x->cash_out,2),number_format((float)$x->cash_in-(float)$x->cash_out,2)];}return $this->table('Cash & Bank Movements (GL)',['Code','Cash/Bank Account','Cash In','Cash Out','Net'],$rows,['Total Cash In'=>number_format($in,2),'Total Cash Out'=>number_format($out,2),'Net Cash Flow'=>number_format($in-$out,2)],$r);}

    public function cashbook(Request $r)
    {
        [$from, $to] = $this->dates($r);
        $accounts = Account::withTrashed()
            ->where('type', 'asset')
            ->whereIn('subtype', ['cash', 'bank'])
            ->orderBy('code')
            ->get();

        $r->validate(['account_id' => 'nullable|integer|min:1']);
        $selectedAccountId = $r->filled('account_id') ? (int) $r->account_id : null;
        abort_if($selectedAccountId && ! $accounts->contains('id', $selectedAccountId), 422, 'Invalid cash/bank account filter.');

        $accountIds = $selectedAccountId ? [$selectedAccountId] : $accounts->pluck('id')->all();

        $opening = 0.0;
        $rows = [];
        $cashIn = 0.0;
        $cashOut = 0.0;

        if ($accountIds !== []) {
            $opening = (float) JournalLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
                ->whereIn('journal_lines.account_id', $accountIds)
                ->where('journal_entries.status', 'posted')
                ->whereDate('journal_entries.journal_date', '<', $from)
                ->selectRaw('COALESCE(SUM(journal_lines.debit - journal_lines.credit), 0) AS opening_balance')
                ->value('opening_balance');

            $lines = JournalLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
                ->whereIn('journal_lines.account_id', $accountIds)
                ->where('journal_entries.status', 'posted')
                ->whereBetween('journal_entries.journal_date', [$from, $to])
                ->select([
                    'journal_lines.id as line_id',
                    'journal_lines.debit',
                    'journal_lines.credit',
                    'journal_lines.description as line_description',
                    'journal_entries.id as journal_entry_id',
                    'journal_entries.journal_date',
                    'journal_entries.journal_no',
                    'journal_entries.reference_type',
                    'journal_entries.reference_no',
                    'journal_entries.description as entry_description',
                    'accounts.code as account_code',
                    'accounts.name as account_name',
                ])
                ->orderBy('journal_entries.journal_date')
                ->orderBy('journal_entries.id')
                ->orderBy('journal_lines.id')
                ->get();

            // Currency amounts in the DB are DECIMAL(?,2). Use integer cents for
            // running totals so repeated fractional operations do not drift.
            $runningCents = (int) round($opening * 100);
            $inCents = 0;
            $outCents = 0;
            foreach ($lines as $line) {
                $debitCents = (int) round((float) $line->debit * 100);
                $creditCents = (int) round((float) $line->credit * 100);
                $debit = $debitCents / 100;
                $credit = $creditCents / 100;
                $inCents += $debitCents;
                $outCents += $creditCents;
                $runningCents += $debitCents - $creditCents;

                $rows[] = [
                    'date' => (string) $line->journal_date,
                    'journal' => $line->journal_no,
                    'account' => trim($line->account_code . ' - ' . $line->account_name),
                    'reference' => $line->reference_no ?: $line->reference_type ?: '—',
                    'description' => $line->line_description ?: $line->entry_description ?: '—',
                    'cash_in' => $debit,
                    'cash_out' => $credit,
                    'balance' => $runningCents / 100,
                ];
            }
            $cashIn = $inCents / 100;
            $cashOut = $outCents / 100;
        }

        $opening = round($opening, 2);
        $closing = round($opening + $cashIn - $cashOut, 2);

        if ($r->boolean('csv')) {
            $csvRows = array_map(fn ($row) => [
                $row['date'], $this->safeLedgerCsv($row['journal']), $this->safeLedgerCsv($row['account']), $this->safeLedgerCsv($row['reference']), $this->safeLedgerCsv($row['description']),
                number_format($row['cash_in'], 2, '.', ''), number_format($row['cash_out'], 2, '.', ''), number_format($row['balance'], 2, '.', ''),
            ], $rows);

            array_unshift($csvRows, [$from, '', '', '', 'Opening Balance', '', '', number_format($opening, 2, '.', '')]);
            $csvRows[] = [$to, '', '', '', 'Closing Balance', number_format($cashIn, 2, '.', ''), number_format($cashOut, 2, '.', ''), number_format($closing, 2, '.', '')];
            return $this->csv('Cashbook', ['Date','Journal','Account','Reference','Description','Cash In','Cash Out','Running Balance'], $csvRows);
        }

        return view('reports.cashbook', compact('from', 'to', 'accounts', 'selectedAccountId', 'opening', 'cashIn', 'cashOut', 'closing', 'rows'));
    }


    /** Posted journal movements for a selected chart-of-accounts GL account. */
    public function generalLedger(Request $r)
    {
        [$from, $to] = $this->dates($r);
        $r->validate(['account_id' => 'nullable|integer|min:1']);
        $accounts = Account::withTrashed()->orderBy('code')->get(['id', 'code', 'name', 'is_active', 'deleted_at']);
        $account = $r->filled('account_id') ? Account::withTrashed()->findOrFail((int) $r->account_id) : null;
        $opening = 0.0;
        $debit = 0.0;
        $credit = 0.0;
        $rows = [];

        if ($account) {
            $ledger = JournalLine::query()
                ->join('journal_entries as j', 'j.id', '=', 'journal_lines.journal_entry_id')
                ->where('j.status', 'posted')
                ->where('journal_lines.account_id', $account->id);
            $opening = round((float) (clone $ledger)
                ->where('j.journal_date', '<', $from)
                ->selectRaw('COALESCE(SUM(journal_lines.debit-journal_lines.credit),0) AS opening')
                ->value('opening'), 2);
            $runningCents = (int) round($opening * 100);
            $lines = (clone $ledger)
                ->whereBetween('j.journal_date', [$from, $to])
                ->selectRaw('journal_lines.id AS line_id, journal_lines.debit, journal_lines.credit, journal_lines.description AS line_description, j.journal_date, j.journal_no, j.reference_no, j.reference_type, j.description AS journal_description')
                ->orderBy('j.journal_date')->orderBy('j.id')->orderBy('journal_lines.id')
                ->cursor();
            $debitCents = 0;
            $creditCents = 0;
            foreach ($lines as $line) {
                $in = (int) round((float) $line->debit * 100);
                $out = (int) round((float) $line->credit * 100);
                $runningCents += $in - $out;
                $debitCents += $in;
                $creditCents += $out;
                $rows[] = [
                    'date' => $line->journal_date,
                    'journal' => $line->journal_no,
                    'reference' => $line->reference_no ?: $line->reference_type ?: '—',
                    'description' => $line->line_description ?: $line->journal_description ?: '—',
                    'debit' => $in / 100,
                    'credit' => $out / 100,
                    'balance' => $runningCents / 100,
                ];
            }
            $debit = $debitCents / 100;
            $credit = $creditCents / 100;
        }
        $closing = round($opening + $debit - $credit, 2);

        if ($r->boolean('csv')) {
            abort_unless($account, 422, 'Select a GL account before exporting.');
            $csvRows = [[$from, '', '', 'Opening Balance', '', '', number_format($opening, 2, '.', '')]];
            foreach ($rows as $row) {
                $csvRows[] = [
                    $row['date'], $this->safeLedgerCsv($row['journal']),
                    $this->safeLedgerCsv($row['reference']), $this->safeLedgerCsv($row['description']),
                    number_format($row['debit'], 2, '.', ''), number_format($row['credit'], 2, '.', ''),
                    number_format($row['balance'], 2, '.', ''),
                ];
            }
            $csvRows[] = [$to, '', '', 'Closing Balance', number_format($debit, 2, '.', ''),
                number_format($credit, 2, '.', ''), number_format($closing, 2, '.', '')];
            return $this->csv('General Ledger', ['Date','Journal','Reference','Description','Debit','Credit','Balance'], $csvRows);
        }
        return view('reports.general-ledger', compact('accounts','account','from','to','opening','debit','credit','closing','rows'));
    }

    public function sales(Request $r){$q=Sale::with('customer','warehouse')->where('status','posted');$this->dateFilter($q,$r,'sale_date');if($r->filled('customer_id'))$q->where('customer_id',$r->customer_id);elseif($r->filled('entity_id'))$q->where('customer_id',$r->entity_id);if($r->filled('warehouse_id'))$q->where('warehouse_id',$r->warehouse_id);$data=$q->orderBy('sale_date')->get();$rows=$data->map(fn($x)=>[$x->sale_date->format('Y-m-d'),$x->invoice_no,$x->customer?->name??'Walk-in',$x->warehouse->name,number_format($x->grand_total,2),number_format($x->paid_amount,2),number_format($x->due_amount,2)])->all();return $this->table('Sales Report',['Date','Invoice','Customer','Warehouse','Total','Paid','Due'],$rows,['Grand Total'=>number_format($data->sum('grand_total'),2),'Due'=>number_format($data->sum('due_amount'),2)],$r);}

    public function purchases(Request $r){$q=Purchase::with('supplier','warehouse')->where('status','posted');$this->dateFilter($q,$r,'purchase_date');if($r->filled('supplier_id'))$q->where('supplier_id',$r->supplier_id);elseif($r->filled('entity_id'))$q->where('supplier_id',$r->entity_id);if($r->filled('warehouse_id'))$q->where('warehouse_id',$r->warehouse_id);$data=$q->orderBy('purchase_date')->get();$rows=$data->map(fn($x)=>[$x->purchase_date->format('Y-m-d'),$x->purchase_no,$x->supplier->name,$x->warehouse->name,number_format($x->grand_total,2),number_format($x->paid_amount,2),number_format($x->due_amount,2)])->all();return $this->table('Purchase Report',['Date','Purchase','Supplier','Warehouse','Total','Paid','Due'],$rows,['Grand Total'=>number_format($data->sum('grand_total'),2),'Due'=>number_format($data->sum('due_amount'),2)],$r);}

    public function customers(Request $r){$data=Customer::with('group')->where('is_active',true)->get()->map(function($c){$bal=(float)$c->ledgerEntries()->selectRaw('COALESCE(SUM(debit),0)-COALESCE(SUM(credit),0) balance')->value('balance');return [$c->code,$c->name,$c->group?->name,$c->phone,number_format($bal,2)];});return $this->table('Customer Report',['Code','Customer','Group','Phone','Receivable / Advance'],$data->all(),[],$r);}
    public function suppliers(Request $r){$data=Supplier::with('group')->where('is_active',true)->get()->map(function($s){$bal=(float)$s->ledgerEntries()->selectRaw('COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0) balance')->value('balance');return [$s->code,$s->name,$s->group?->name,$s->phone,number_format($bal,2)];});return $this->table('Supplier Report',['Code','Supplier','Group','Phone','Payable / Advance'],$data->all(),[],$r);}
    public function products(Request $r){$q=WarehouseStock::with('product.category','product.brand','product.unit','warehouse');if($r->filled('warehouse_id'))$q->where('warehouse_id',$r->warehouse_id);if($r->filled('product_id'))$q->where('product_id',$r->product_id);elseif($r->filled('entity_id'))$q->where('product_id',$r->entity_id);$data=$q->get();$rows=$data->map(fn($x)=>[$x->product->code,$x->product->name,$x->warehouse->name,$x->product->unit->short_name,$x->quantity,$x->product->category?->name,$x->product->brand?->name])->all();return $this->table('Product / Stock Report',['Code','Product','Warehouse','Unit','Stock','Category','Brand'],$rows,[],$r);}
    public function payments(Request $r){[$from,$to]=$this->dates($r);$rows=[];foreach(CustomerPayment::with('customer')->whereBetween('payment_date',[$from,$to])->when($r->filled('entity_id'),fn($q)=>$q->where('customer_id',$r->entity_id))->when($r->filled('filter'),fn($q)=>$q->where('method',$r->filter))->get() as $p)$rows[]=[$p->payment_date->format('Y-m-d'),$p->payment_no,'Customer Receipt',$p->customer->name,$p->method,number_format($p->amount,2)];foreach(SupplierPayment::with('supplier')->whereBetween('payment_date',[$from,$to])->when($r->filled('entity_id'),fn($q)=>$q->where('supplier_id',$r->entity_id))->when($r->filled('filter'),fn($q)=>$q->where('method',$r->filter))->get() as $p)$rows[]=[$p->payment_date->format('Y-m-d'),$p->payment_no,'Supplier Payment',$p->supplier->name,$p->method,number_format($p->amount,2)];usort($rows,fn($a,$b)=>strcmp($a[0],$b[0]));return $this->table('Payment Report',['Date','No','Type','Party','Method','Amount'],$rows,[],$r);}
    public function expenses(Request $r){$q=Expense::with('category')->where('status','posted');$this->dateFilter($q,$r,'expense_date');if($r->filled('category_id'))$q->where('expense_category_id',$r->category_id);elseif($r->filled('entity_id'))$q->where('expense_category_id',$r->entity_id);$data=$q->get();$rows=$data->map(fn($x)=>[$x->expense_date->format('Y-m-d'),$x->expense_no,$x->category->name,$x->description,number_format($x->amount,2)])->all();return $this->table('Expense Report',['Date','No','Category','Description','Amount'],$rows,['Total'=>number_format($data->sum('amount'),2)],$r);}
    public function incomeExpense(Request $r){[$from,$to]=$this->dates($r);$lines=JournalLine::with('entry','account')->whereHas('entry',fn($q)=>$q->where('status','posted')->whereBetween('journal_date',[$from,$to]))->whereHas('account',fn($q)=>$q->whereIn('type',['income','expense']))->get();$groups=$lines->groupBy(fn($l)=>$l->entry->journal_date->format('Y-m'));$data=[];foreach($groups->sortKeys() as $period=>$group){$income=0;$expense=0;foreach($group as $line){if($line->account->type==='income')$income+=(float)$line->credit-(float)$line->debit;else$expense+=(float)$line->debit-(float)$line->credit;}$data[]=[$period,number_format($income,2),number_format($expense,2),number_format($income-$expense,2)];}return $this->table('Income / Expense Report',['Period','Income','Expense','Net'],$data,[],$r);}
    public function registers(Request $r){[$from,$to]=$this->dates($r);$rows=DB::table('journal_entries')->where('status','posted')->whereBetween('journal_date',[$from,$to])->when($r->filled('filter'),fn($q)=>$q->where('reference_type',$r->filter))->orderBy('journal_date')->orderBy('id')->get()->map(fn($x)=>[$x->journal_date,$x->journal_no,$x->reference_type,$x->reference_no,$x->description])->all();return $this->table('Register Report',['Date','Journal','Type','Reference','Description'],$rows,[],$r);}

    public function financial(Request $r)
    {
        return view('reports.financial');
    }

    public function transactionsPackage(Request $r)
    {
        [$from, $to] = $this->dates($r);
        $types = JournalEntry::query()->where('status', 'posted')->whereNotNull('reference_type')
            ->distinct()->orderBy('reference_type')->pluck('reference_type');
        $accounts = Account::withTrashed()->orderBy('code')->get(['id','code','name']);

        $query = JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->where('journal_entries.status', 'posted')
            ->whereBetween('journal_entries.journal_date', [$from, $to])
            ->when($r->filled('type'), fn ($q) => $q->where('journal_entries.reference_type', $r->type))
            ->when($r->filled('account_id'), fn ($q) => $q->where('journal_lines.account_id', $r->account_id))
            ->when($r->filled('q'), function ($q) use ($r) {
                $term = trim((string) $r->q);
                $q->where(function ($x) use ($term) {
                    $x->where('journal_entries.journal_no', 'like', "%{$term}%")
                        ->orWhere('journal_entries.reference_no', 'like', "%{$term}%")
                        ->orWhere('journal_entries.description', 'like', "%{$term}%")
                        ->orWhere('journal_lines.description', 'like', "%{$term}%")
                        ->orWhere('accounts.code', 'like', "%{$term}%")
                        ->orWhere('accounts.name', 'like', "%{$term}%");
                });
            })
            ->select([
                'journal_entries.journal_date', 'journal_entries.journal_no', 'journal_entries.reference_type',
                'journal_entries.reference_no', 'journal_entries.description as entry_description',
                'accounts.code as account_code', 'accounts.name as account_name',
                'journal_lines.description as line_description', 'journal_lines.debit', 'journal_lines.credit',
            ])
            ->orderBy('journal_entries.journal_date')->orderBy('journal_entries.id')->orderBy('journal_lines.id');

        $rows = $query->get();
        $totalDebit = (float) $rows->sum('debit');
        $totalCredit = (float) $rows->sum('credit');

        if ($r->boolean('csv')) {
            $csvRows = $rows->map(fn ($row) => [
                $row->journal_date, $row->journal_no, $row->reference_type ?: '—', $row->reference_no ?: '—',
                $row->account_code.' - '.$row->account_name,
                $row->line_description ?: $row->entry_description ?: '—',
                number_format((float) $row->debit, 2, '.', ''), number_format((float) $row->credit, 2, '.', ''),
            ])->all();
            return $this->csv('Transactions Package', ['Date','Journal','Type','Reference','Account','Description','Debit','Credit'], $csvRows);
        }

        return view('reports.transactions-package', compact('from','to','types','accounts','rows','totalDebit','totalCredit'));
    }

    public function receivablePayable(Request $r)
    {
        $r->validate(['to' => 'sometimes|date_format:Y-m-d']);
        $asOf = $r->input('to', now()->toDateString());
        $search = trim((string) $r->input('q', ''));
        $partyType = $r->input('party_type');
        $rows = collect();

        if (! $partyType || $partyType === 'customer') {
            $customers = DB::table('customers')
                ->leftJoin('customer_ledger_entries', function ($join) use ($asOf) {
                    $join->on('customer_ledger_entries.customer_id', '=', 'customers.id')
                        ->whereDate('customer_ledger_entries.transaction_date', '<=', $asOf);
                })
                
                ->when($search !== '', function ($q) use ($search) {
                    $q->where(function ($x) use ($search) {
                        $x->where('customers.name','like',"%{$search}%")
                            ->orWhere('customers.code','like',"%{$search}%")
                            ->orWhere('customers.phone','like',"%{$search}%");
                    });
                })
                ->groupBy('customers.id','customers.code','customers.name','customers.phone')
                ->selectRaw('customers.id, customers.code, customers.name, customers.phone, COALESCE(SUM(customer_ledger_entries.debit - customer_ledger_entries.credit),0) AS balance')
                ->orderBy('customers.name')->get();

            foreach ($customers as $party) {
                $balance = (float) $party->balance;
                if (abs($balance) < 0.005) continue;
                $rows->push([
                    'party_type' => 'Customer', 'code' => $party->code, 'name' => $party->name, 'phone' => $party->phone,
                    'receivable' => max($balance, 0), 'payable' => 0.0, 'advance' => max(-$balance, 0),
                ]);
            }
        }

        if (! $partyType || $partyType === 'supplier') {
            $suppliers = DB::table('suppliers')
                ->leftJoin('supplier_ledger_entries', function ($join) use ($asOf) {
                    $join->on('supplier_ledger_entries.supplier_id', '=', 'suppliers.id')
                        ->whereDate('supplier_ledger_entries.transaction_date', '<=', $asOf);
                })
                
                ->when($search !== '', function ($q) use ($search) {
                    $q->where(function ($x) use ($search) {
                        $x->where('suppliers.name','like',"%{$search}%")
                            ->orWhere('suppliers.code','like',"%{$search}%")
                            ->orWhere('suppliers.phone','like',"%{$search}%");
                    });
                })
                ->groupBy('suppliers.id','suppliers.code','suppliers.name','suppliers.phone')
                ->selectRaw('suppliers.id, suppliers.code, suppliers.name, suppliers.phone, COALESCE(SUM(supplier_ledger_entries.credit - supplier_ledger_entries.debit),0) AS balance')
                ->orderBy('suppliers.name')->get();

            foreach ($suppliers as $party) {
                $balance = (float) $party->balance;
                if (abs($balance) < 0.005) continue;
                $rows->push([
                    'party_type' => 'Supplier', 'code' => $party->code, 'name' => $party->name, 'phone' => $party->phone,
                    'receivable' => 0.0, 'payable' => max($balance, 0), 'advance' => max(-$balance, 0),
                ]);
            }
        }

        $rows = $rows->sortBy([['party_type','asc'],['name','asc']])->values();
        $totalReceivable = (float) $rows->sum('receivable');
        $totalPayable = (float) $rows->sum('payable');
        $totalAdvance = (float) $rows->sum('advance');
        $netPosition = $totalReceivable - $totalPayable;

        if ($r->boolean('csv')) {
            $csvRows = $rows->map(fn ($row) => [
                $row['party_type'], $row['code'], $row['name'], $row['phone'],
                number_format($row['receivable'],2,'.',''), number_format($row['payable'],2,'.',''), number_format($row['advance'],2,'.',''),
            ])->all();
            return $this->csv('Receivable Payable', ['Party Type','Code','Name','Phone','Receivable','Payable','Advance'], $csvRows);
        }

        return view('reports.receivable-payable', compact('asOf','rows','totalReceivable','totalPayable','totalAdvance','netPosition'));
    }

    public function gapReport(Request $r)
    {
        [$from, $to] = $this->dates($r);

        $sales = ReportMoney::postedSum('sales', $from, $to);
        $salesReturns = ReportMoney::postedReturnSum('sales', $from, $to);
        $purchases = ReportMoney::postedSum('purchases', $from, $to);
        $purchaseReturns = ReportMoney::postedReturnSum('purchases', $from, $to);
        $collections = (float) CustomerPayment::where('status','posted')->whereBetween('payment_date',[$from,$to])->sum('amount');
        $supplierPayments = (float) SupplierPayment::where('status','posted')->whereBetween('payment_date',[$from,$to])->sum('amount');

        $cash = JournalLine::query()
            ->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')
            ->join('accounts','accounts.id','=','journal_lines.account_id')
            ->where('journal_entries.status','posted')->whereBetween('journal_entries.journal_date',[$from,$to])
            ->where('accounts.type','asset')->whereIn('accounts.subtype',['cash','bank'])
            ->selectRaw('COALESCE(SUM(journal_lines.debit),0) cash_in, COALESCE(SUM(journal_lines.credit),0) cash_out')->first();

        $profit = JournalLine::query()
            ->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')
            ->join('accounts','accounts.id','=','journal_lines.account_id')
            ->where('journal_entries.status','posted')->whereBetween('journal_entries.journal_date',[$from,$to])
            ->whereIn('accounts.type',['income','expense'])
            ->selectRaw("COALESCE(SUM(CASE WHEN accounts.type='income' THEN journal_lines.credit-journal_lines.debit ELSE 0 END),0) income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN accounts.type='expense' THEN journal_lines.debit-journal_lines.credit ELSE 0 END),0) expense_total")->first();

        $party = $this->partyPosition($to);
        $netSales = $sales - $salesReturns;
        $netPurchases = $purchases - $purchaseReturns;
        $income = (float) ($profit->income_total ?? 0);
        $expense = (float) ($profit->expense_total ?? 0);
        $cashIn = (float) ($cash->cash_in ?? 0);
        $cashOut = (float) ($cash->cash_out ?? 0);

        $rows = [
            ['name'=>'Trade Volume','left_label'=>'Net Sales','left'=>$netSales,'right_label'=>'Net Purchases','right'=>$netPurchases,'gap'=>$netSales-$netPurchases],
            ['name'=>'Settlement','left_label'=>'Customer Collections','left'=>$collections,'right_label'=>'Supplier Payments','right'=>$supplierPayments,'gap'=>$collections-$supplierPayments],
            ['name'=>'Outstanding','left_label'=>'Receivable','left'=>$party['receivable'],'right_label'=>'Payable','right'=>$party['payable'],'gap'=>$party['receivable']-$party['payable']],
            ['name'=>'Cash Movement','left_label'=>'Cash / Bank In','left'=>$cashIn,'right_label'=>'Cash / Bank Out','right'=>$cashOut,'gap'=>$cashIn-$cashOut],
            ['name'=>'Profitability','left_label'=>'Accounting Income','left'=>$income,'right_label'=>'Accounting Expense','right'=>$expense,'gap'=>$income-$expense],
        ];

        if ($r->boolean('csv')) {
            $csvRows = array_map(fn ($row) => [$row['name'],$row['left_label'],number_format($row['left'],2,'.',''),$row['right_label'],number_format($row['right'],2,'.',''),number_format($row['gap'],2,'.','')], $rows);
            return $this->csv('GAP Report', ['Gap Area','Positive Side','Amount','Negative Side','Amount','Gap'], $csvRows);
        }

        return view('reports.gap', compact('from','to','rows'));
    }

    public function topSheet(Request $r)
    {
        [$from, $to] = $this->dates($r);

        $period = [
            'sales' => ReportMoney::postedSum('sales', $from, $to),
            'sales_returns' => ReportMoney::postedReturnSum('sales', $from, $to),
            'purchases' => ReportMoney::postedSum('purchases', $from, $to),
            'purchase_returns' => ReportMoney::postedReturnSum('purchases', $from, $to),
            'collections' => (float) CustomerPayment::where('status','posted')->whereBetween('payment_date',[$from,$to])->sum('amount'),
            'supplier_payments' => (float) SupplierPayment::where('status','posted')->whereBetween('payment_date',[$from,$to])->sum('amount'),
            'expenses' => (float) Expense::where('status','posted')->whereBetween('expense_date',[$from,$to])->sum('amount'),
        ];

        $profit = JournalLine::query()
            ->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')
            ->join('accounts','accounts.id','=','journal_lines.account_id')
            ->where('journal_entries.status','posted')->whereBetween('journal_entries.journal_date',[$from,$to])
            ->whereIn('accounts.type',['income','expense'])
            ->selectRaw("COALESCE(SUM(CASE WHEN accounts.type='income' THEN journal_lines.credit-journal_lines.debit ELSE 0 END),0) income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN accounts.type='expense' THEN journal_lines.debit-journal_lines.credit ELSE 0 END),0) expense_total")->first();
        $period['income'] = (float) ($profit->income_total ?? 0);
        $period['accounting_expense'] = (float) ($profit->expense_total ?? 0);
        $period['net_profit'] = $period['income'] - $period['accounting_expense'];

        $cashBank = JournalLine::query()
            ->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')
            ->join('accounts','accounts.id','=','journal_lines.account_id')
            ->where('journal_entries.status','posted')->whereDate('journal_entries.journal_date','<=',$to)
            ->where('accounts.type','asset')->whereIn('accounts.subtype',['cash','bank'])
            ->groupBy('accounts.subtype')
            ->selectRaw('accounts.subtype AS subtype, COALESCE(SUM(journal_lines.debit-journal_lines.credit),0) balance')
            ->pluck('balance','subtype');

        $party = $this->partyPosition($to);
        $position = [
            'cash' => (float) ($cashBank['cash'] ?? 0),
            'bank' => (float) ($cashBank['bank'] ?? 0),
            'receivable' => $party['receivable'],
            'payable' => $party['payable'],
            'customer_advance' => $party['customer_advance'],
            'supplier_advance' => $party['supplier_advance'],
            'inventory_value' => $this->currentInventoryValue(),
            'loan_receivable' => $this->loanOutstandingAsOf('lent', $to),
            'loan_payable' => $this->loanOutstandingAsOf('borrowed', $to),
        ];

        if ($r->boolean('csv')) {
            $csvRows = [
                ['Period','Sales',$period['sales']],['Period','Sales Returns',$period['sales_returns']],['Period','Purchases',$period['purchases']],
                ['Period','Purchase Returns',$period['purchase_returns']],['Period','Customer Collections',$period['collections']],['Period','Supplier Payments',$period['supplier_payments']],
                ['Period','Operating Expenses',$period['expenses']],['Period','Accounting Income',$period['income']],['Period','Accounting Expense',$period['accounting_expense']],['Period','Net Profit',$period['net_profit']],
                ['Position','Cash Balance',$position['cash']],['Position','Bank Balance',$position['bank']],['Position','Receivable',$position['receivable']],['Position','Payable',$position['payable']],
                ['Position','Customer Advance',$position['customer_advance']],['Position','Supplier Advance',$position['supplier_advance']],['Position','Current Inventory Value',$position['inventory_value']],
                ['Position','Loan Receivable',$position['loan_receivable']],['Position','Loan Payable',$position['loan_payable']],
            ];
            $csvRows = array_map(fn ($x) => [$x[0],$x[1],number_format((float)$x[2],2,'.','')], $csvRows);
            return $this->csv('Top Sheet', ['Section','Metric','Amount'], $csvRows);
        }

        return view('reports.top-sheet', compact('from','to','period','position'));
    }

    public function dailyPerformance(Request $r)
    {
        $r->validate(['date' => 'sometimes|date_format:Y-m-d']);
        $date = $r->input('date', now()->toDateString());

        $sales = Sale::with('customer','warehouse')->where('status','posted')
            ->whereDate('sale_date',$date)->orderBy('id')->get();
        $purchases = Purchase::with('supplier','warehouse')->where('status','posted')
            ->whereDate('purchase_date',$date)->orderBy('id')->get();
        $receipts = CustomerPayment::with('customer')->where('status','posted')
            ->whereDate('payment_date',$date)->orderBy('id')->get();
        $supplierPayments = SupplierPayment::with('supplier')->where('status','posted')
            ->whereDate('payment_date',$date)->orderBy('id')->get();
        $expenses = Expense::with('category')->where('status','posted')
            ->whereDate('expense_date',$date)->orderBy('id')->get();
        $saleReturns = SaleReturn::with('customer','sale')->where('status','posted')
            ->whereDate('return_date',$date)->orderBy('id')->get();
        $purchaseReturns = PurchaseReturn::with('supplier','purchase')->where('status','posted')
            ->whereDate('return_date',$date)->orderBy('id')->get();

        // Each document is rounded at the saved FX rate, matching the posting layer.
        // Payments/expenses and GL amounts are already recorded in base BDT.
        foreach ($sales as $row) {
            $row->report_base_total = ReportMoney::document($row);
            $row->report_base_paid = ReportMoney::document($row, 'paid_amount');
            $row->report_base_due = ReportMoney::document($row, 'due_amount');
        }
        foreach ($purchases as $row) {
            $row->report_base_total = ReportMoney::document($row);
            $row->report_base_paid = ReportMoney::document($row, 'paid_amount');
            $row->report_base_due = ReportMoney::document($row, 'due_amount');
        }
        foreach ($saleReturns as $row) $row->report_base_total = ReportMoney::returned($row, $row->sale);
        foreach ($purchaseReturns as $row) $row->report_base_total = ReportMoney::returned($row, $row->purchase);

        $profitLines = JournalLine::query()
            ->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')
            ->join('accounts','accounts.id','=','journal_lines.account_id')
            ->where('journal_entries.status','posted')
            ->whereDate('journal_entries.journal_date',$date)
            ->whereIn('accounts.type',['income','expense'])
            ->selectRaw("COALESCE(SUM(CASE WHEN accounts.type='income' THEN journal_lines.credit-journal_lines.debit ELSE 0 END),0) income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN accounts.type='expense' THEN journal_lines.debit-journal_lines.credit ELSE 0 END),0) expense_total")
            ->first();

        $cash = JournalLine::query()
            ->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')
            ->join('accounts','accounts.id','=','journal_lines.account_id')
            ->where('journal_entries.status','posted')
            ->whereDate('journal_entries.journal_date',$date)
            ->where('accounts.type','asset')
            ->whereIn('accounts.subtype',['cash','bank'])
            ->selectRaw('COALESCE(SUM(journal_lines.debit),0) cash_in, COALESCE(SUM(journal_lines.credit),0) cash_out')
            ->first();

        $income = (float) ($profitLines->income_total ?? 0);
        $accountingExpense = (float) ($profitLines->expense_total ?? 0);
        $grossSales = round((float) $sales->sum('report_base_total'), 2);
        $salesReturnsTotal = round((float) $saleReturns->sum('report_base_total'), 2);
        $grossPurchases = round((float) $purchases->sum('report_base_total'), 2);
        $purchaseReturnsTotal = round((float) $purchaseReturns->sum('report_base_total'), 2);
        $metrics = [
            'sales' => $grossSales,
            'net_sales' => round($grossSales - $salesReturnsTotal, 2),
            'sales_count' => $sales->count(),
            'sales_due' => (float) $sales->sum('report_base_due'),
            'purchases' => $grossPurchases,
            'net_purchases' => round($grossPurchases - $purchaseReturnsTotal, 2),
            'purchase_count' => $purchases->count(),
            'purchase_due' => (float) $purchases->sum('report_base_due'),
            'collections' => (float) $receipts->sum('amount'),
            'supplier_payments' => (float) $supplierPayments->sum('amount'),
            'operating_expenses' => (float) $expenses->sum('amount'),
            'sales_returns' => $salesReturnsTotal,
            'purchase_returns' => $purchaseReturnsTotal,
            'cash_in' => (float) ($cash->cash_in ?? 0),
            'cash_out' => (float) ($cash->cash_out ?? 0),
            'accounting_income' => $income,
            'accounting_expense' => $accountingExpense,
            'net_profit' => round($income - $accountingExpense, 2),
        ];

        if ($r->boolean('csv')) {
            // The CSV includes a date-bound summary and each underlying posted document.
            $rows = [];
            foreach ($metrics as $key => $value) $rows[] = ['Summary', $date, $key, '', '', '', number_format((float)$value,2,'.','')];
            foreach ($sales as $x) $rows[] = ['Sale',$date,$x->invoice_no,$x->customer?->name ?? 'Walk-in','', $x->currency_code,number_format($x->report_base_total,2,'.','')];
            foreach ($purchases as $x) $rows[] = ['Purchase',$date,$x->purchase_no,$x->supplier?->name ?? '', '', $x->currency_code,number_format($x->report_base_total,2,'.','')];
            foreach ($saleReturns as $x) $rows[] = ['Sale Return',$date,$x->return_no,$x->customer?->name ?? '', '', $x->sale?->currency_code,number_format($x->report_base_total,2,'.','')];
            foreach ($purchaseReturns as $x) $rows[] = ['Purchase Return',$date,$x->return_no,$x->supplier?->name ?? '', '', $x->purchase?->currency_code,number_format($x->report_base_total,2,'.','')];
            foreach ($receipts as $x) $rows[] = ['Customer Receipt',$date,$x->payment_no,$x->customer?->name ?? '', $x->method,'BDT',number_format((float)$x->amount,2,'.','')];
            foreach ($supplierPayments as $x) $rows[] = ['Supplier Payment',$date,$x->payment_no,$x->supplier?->name ?? '',$x->method,'BDT',number_format((float)$x->amount,2,'.','')];
            foreach ($expenses as $x) $rows[] = ['Expense',$date,$x->expense_no,$x->category?->name ?? '', '', 'BDT',number_format((float)$x->amount,2,'.','')];
            return $this->csv('Daily Performance '.$date, ['Section','Date','Reference / Metric','Party','Method','Document Currency','Amount (BDT)'], $rows);
        }

        return view('reports.daily-performance', compact(
            'date','metrics','sales','purchases','receipts','supplierPayments','expenses','saleReturns','purchaseReturns'
        ));
    }

    public function vouchers(Request $r)
    {
        [$from,$to] = $this->dates($r);
        $rows = JournalEntry::with('creator')
            ->where('status','posted')
            ->whereBetween('journal_date',[$from,$to])
            ->when($r->filled('filter'),fn($q)=>$q->where('reference_type',$r->filter))
            ->when($r->filled('q'),function($q) use ($r){
                $term = trim((string) $r->q);
                $q->where(function($x) use ($term){
                    $x->where('journal_no','like',"%{$term}%")
                      ->orWhere('reference_no','like',"%{$term}%")
                      ->orWhere('description','like',"%{$term}%");
                });
            })
            ->withCount('lines')
            ->withSum('lines as debit_total','debit')
            ->withSum('lines as credit_total','credit')
            ->orderByDesc('journal_date')->orderByDesc('id')
            ->paginate(40)->withQueryString();

        $types = JournalEntry::query()->where('status','posted')->whereNotNull('reference_type')
            ->distinct()->orderBy('reference_type')->pluck('reference_type');

        return view('reports.vouchers',compact('rows','types','from','to'));
    }

    public function voucherPrint(JournalEntry $journal)
    {
        abort_unless($journal->status === 'posted', 404);
        $journal->load('lines.account','creator');
        $voucherDifference = round((float)$journal->lines->sum('debit') - (float)$journal->lines->sum('credit'), 2);
        $voucherBalanced = $journal->lines->count() >= 2 && abs($voucherDifference) < 0.01;
        $settings = Setting::query()->pluck('value','key')->all();

        $type = match ($journal->reference_type) {
            'customer_payment','money_receive' => __('Receipt Voucher'),
            'supplier_payment','expense','payroll_payment','loan_payment','money_payment' => __('Payment Voucher'),
            'sale' => __('Sales Voucher'),
            'purchase' => __('Purchase Voucher'),
            'sale_return','purchase_return' => __('Return Voucher'),
            'manual_journal' => __('Journal Voucher'),
            'money_transfer' => __('Transfer Voucher'),
            default => __('Accounting Voucher'),
        };

        return view('reports.voucher-print',compact('journal','settings','type','voucherDifference','voucherBalanced'));
    }



    private function safeLedgerCsv(?string $value): string
    {
        $value = (string) $value;
        // Spreadsheet applications can interpret fputcsv()-quoted text as a formula.
        return preg_match('/^[\x00-\x20]*[=+@\-]/', $value) ? "'".$value : $value;
    }

    private function clientLedgerCsv(Customer $customer, array $ledger, string $from, string $to): StreamedResponse
    {
        $filename = 'client-ledger-'.preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower($customer->code ?: $customer->name)).'-'.$from.'-to-'.$to.'.csv';
        return response()->streamDownload(function () use ($customer, $ledger, $from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Client Ledger']);
            fputcsv($out, ['Customer', $this->safeLedgerCsv($customer->name)]);
            fputcsv($out, ['Customer Code', $this->safeLedgerCsv($customer->code)]);
            fputcsv($out, ['Phone', $this->safeLedgerCsv($customer->phone)]);
            fputcsv($out, ['Period', $from.' to '.$to]);
            fputcsv($out, []);
            fputcsv($out, ['Opening Balance', number_format((float) $ledger['opening_balance'], 2, '.', '')]);
            fputcsv($out, ['Date','Reference','Type','Description','Debit','Credit','Running Balance']);
            foreach ($ledger['entries'] as $entry) {
                fputcsv($out, [
                    optional($entry->transaction_date)->format('Y-m-d'),
                    $this->safeLedgerCsv($entry->reference_no),
                    $this->safeLedgerCsv($entry->entry_type),
                    $this->safeLedgerCsv($entry->description),
                    number_format((float) $entry->debit, 2, '.', ''),
                    number_format((float) $entry->credit, 2, '.', ''),
                    number_format((float) $entry->running_balance, 2, '.', ''),
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Period Debit', number_format((float) $ledger['period_debit'], 2, '.', '')]);
            fputcsv($out, ['Period Credit', number_format((float) $ledger['period_credit'], 2, '.', '')]);
            fputcsv($out, ['Closing Balance', number_format((float) $ledger['closing_balance'], 2, '.', '')]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function partyPosition(string $asOf): array
    {
        $customerBalances = DB::table('customers')
            ->leftJoin('customer_ledger_entries', function ($join) use ($asOf) {
                $join->on('customer_ledger_entries.customer_id','=','customers.id')
                    ->whereDate('customer_ledger_entries.transaction_date','<=',$asOf);
            })
            
            ->groupBy('customers.id')
            ->selectRaw('COALESCE(SUM(customer_ledger_entries.debit-customer_ledger_entries.credit),0) balance')->pluck('balance');

        $supplierBalances = DB::table('suppliers')
            ->leftJoin('supplier_ledger_entries', function ($join) use ($asOf) {
                $join->on('supplier_ledger_entries.supplier_id','=','suppliers.id')
                    ->whereDate('supplier_ledger_entries.transaction_date','<=',$asOf);
            })
            
            ->groupBy('suppliers.id')
            ->selectRaw('COALESCE(SUM(supplier_ledger_entries.credit-supplier_ledger_entries.debit),0) balance')->pluck('balance');

        return [
            'receivable' => (float) $customerBalances->filter(fn ($x) => (float)$x > 0)->sum(),
            'customer_advance' => (float) $customerBalances->filter(fn ($x) => (float)$x < 0)->sum(fn ($x) => abs((float)$x)),
            'payable' => (float) $supplierBalances->filter(fn ($x) => (float)$x > 0)->sum(),
            'supplier_advance' => (float) $supplierBalances->filter(fn ($x) => (float)$x < 0)->sum(fn ($x) => abs((float)$x)),
        ];
    }

    private function loanOutstandingAsOf(string $direction, string $asOf): float
    {
        $rows = DB::table('loans')
            ->leftJoin('loan_payments', function ($join) use ($asOf) {
                $join->on('loan_payments.loan_id','=','loans.id')
                    ->whereDate('loan_payments.payment_date','<=',$asOf);
            })
            ->where('loans.direction',$direction)
            ->whereDate('loans.start_date','<=',$asOf)
            ->groupBy('loans.id','loans.principal')
            ->selectRaw('GREATEST(loans.principal - COALESCE(SUM(loan_payments.principal_amount),0),0) outstanding')
            ->get();

        return (float) $rows->sum('outstanding');
    }

    private function currentInventoryValue(): float
    {
        $stocks = WarehouseStock::query()->with('product:id,purchase_price,costing_method')
            ->where('quantity','!=',0)->get();
        $layers = DB::table('inventory_cost_layers')->where('quantity_remaining','>',0)
            ->selectRaw('warehouse_id, product_id, SUM(quantity_remaining) layer_qty, SUM(quantity_remaining * unit_cost) layer_value')
            ->groupBy('warehouse_id','product_id')->get()->keyBy(fn ($row) => $row->warehouse_id.'|'.$row->product_id);

        $total = 0.0;
        foreach ($stocks as $stock) {
            if (! $stock->product) continue;
            $quantity = (float) $stock->quantity;
            $fallback = (float) $stock->average_cost > 0 ? (float) $stock->average_cost : (float) $stock->product->purchase_price;
            if ($stock->product->costing_method === 'fifo') {
                $layer = $layers->get($stock->warehouse_id.'|'.$stock->product_id);
                $layerQty = (float) ($layer->layer_qty ?? 0);
                $value = (float) ($layer->layer_value ?? 0);
                if ($quantity > $layerQty) $value += ($quantity - $layerQty) * $fallback;
                $total += $value;
            } else {
                $total += $quantity * $fallback;
            }
        }
        return $total;
    }

    private function table(string $title,array $headers,array $rows,array $totals,Request $request){if($request->boolean('csv'))return $this->csv($title,$headers,$rows);return view('reports.table',compact('title','headers','rows','totals'));}
    private function csv(string $title,array $headers,array $rows):StreamedResponse{return response()->streamDownload(function()use($headers,$rows){$h=fopen('php://output','w');fputcsv($h,$headers);foreach($rows as $row)fputcsv($h,$row);fclose($h);},strtolower(str_replace(' ','-',$title)).'.csv',['Content-Type'=>'text/csv']);}
    private function dates(Request $r): array
    {
        $r->validate(['from' => 'sometimes|date_format:Y-m-d', 'to' => 'sometimes|date_format:Y-m-d']);
        $from = $r->input('from', now()->startOfMonth()->toDateString());
        $to = $r->input('to', now()->toDateString());
        abort_if($from > $to, 422, 'From date must not exceed To date.');
        return [$from, $to];
    }
    private function dateFilter($q,Request $r,string $column):void{[$from,$to]=$this->dates($r);$q->whereBetween($column,[$from,$to]);}
}
