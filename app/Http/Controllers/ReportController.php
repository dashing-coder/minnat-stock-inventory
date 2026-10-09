<?php
namespace App\Http\Controllers;

use App\Models\Account;use App\Models\Customer;use App\Models\CustomerPayment;use App\Models\Expense;use App\Models\JournalLine;use App\Models\Product;use App\Models\Purchase;use App\Models\Sale;use App\Models\Supplier;use App\Models\SupplierPayment;use App\Models\WarehouseStock;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;use Symfony\Component\HttpFoundation\StreamedResponse;
class ReportController extends Controller
{
    public function index(){return view('reports.index');}

    public function profitLoss(Request $r){[$from,$to]=$this->dates($r);$accounts=Account::whereIn('type',['income','expense'])->where('is_active',true)->when($r->filled('entity_id'),fn($q)=>$q->where('id',$r->entity_id))->orderBy('code')->get();$rows=[];$income=0;$expense=0;foreach($accounts as $a){$s=JournalLine::query()->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')->where('journal_lines.account_id',$a->id)->whereBetween('journal_entries.journal_date',[$from,$to])->selectRaw('COALESCE(SUM(journal_lines.debit),0) debit, COALESCE(SUM(journal_lines.credit),0) credit')->first();$amount=$a->type==='income'?(float)$s->credit-(float)$s->debit:(float)$s->debit-(float)$s->credit;if(abs($amount)<0.005)continue;$rows[]=[$a->code,$a->name,ucfirst($a->type),number_format($amount,2)];if($a->type==='income')$income+=$amount;else$expense+=$amount;}return $this->table('Profit & Loss',['Code','Account','Type','Amount'],$rows,['Total Income'=>number_format($income,2),'Total Expense'=>number_format($expense,2),'Net Profit'=>number_format($income-$expense,2)],$r);}

    public function trialBalance(Request $r){[$from,$to]=$this->dates($r);$data=JournalLine::query()->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')->join('accounts','accounts.id','=','journal_lines.account_id')->whereBetween('journal_entries.journal_date',[$from,$to])->when($r->filled('entity_id'),fn($q)=>$q->where('accounts.id',$r->entity_id))->groupBy('accounts.id','accounts.code','accounts.name')->orderBy('accounts.code')->selectRaw('accounts.code, accounts.name, SUM(journal_lines.debit) debit, SUM(journal_lines.credit) credit')->get();$rows=[];$td=0;$tc=0;foreach($data as $x){$d=(float)$x->debit;$c=(float)$x->credit;$net=$d-$c;$cd=$net>0?$net:0;$cc=$net<0?abs($net):0;$td+=$cd;$tc+=$cc;$rows[]=[$x->code,$x->name,number_format($d,2),number_format($c,2),number_format($cd,2),number_format($cc,2)];}return $this->table('Trial Balance',['Code','Account','Debit','Credit','Closing Debit','Closing Credit'],$rows,['Closing Debit'=>number_format($td,2),'Closing Credit'=>number_format($tc,2)],$r);}

    public function balanceSheet(Request $r)
    {
        $to = $r->input('to', now()->toDateString());

        $data = JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
            ->where('journal_entries.status', 'posted')
            ->whereDate('journal_entries.journal_date', '<=', $to)
            ->whereIn('accounts.type', ['asset', 'liability', 'equity'])
            ->when($r->filled('entity_id'), fn ($q) => $q->where('accounts.id', $r->entity_id))
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type')
            ->orderBy('accounts.type')
            ->orderBy('accounts.code')
            ->selectRaw('accounts.code,accounts.name,accounts.type,SUM(journal_lines.debit) debit,SUM(journal_lines.credit) credit')
            ->get();

        $rows = [];
        $tot = ['asset' => 0.0, 'liability' => 0.0, 'equity' => 0.0];

        foreach ($data as $x) {
            $amount = $x->type === 'asset'
                ? (float) $x->debit - (float) $x->credit
                : (float) $x->credit - (float) $x->debit;
            $tot[$x->type] += $amount;
            $rows[] = [$x->code, $x->name, ucfirst($x->type), number_format($amount, 2)];
        }

        // Income and expense accounts are temporary accounts. For a balance sheet,
        // cumulative P&L through the report date must be closed into equity.
        if (!$r->filled('entity_id')) {
            $cumulativeEarnings = (float) JournalLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id')
                ->where('journal_entries.status', 'posted')
                ->whereDate('journal_entries.journal_date', '<=', $to)
                ->whereIn('accounts.type', ['income', 'expense'])
                ->selectRaw('COALESCE(SUM(journal_lines.credit - journal_lines.debit), 0) AS earnings')
                ->value('earnings');

            if (abs($cumulativeEarnings) >= 0.005) {
                $tot['equity'] += $cumulativeEarnings;
                $rows[] = ['P&L', 'Cumulative Earnings', 'Equity', number_format($cumulativeEarnings, 2)];
            }
        }

        $liabilitiesAndEquity = $tot['liability'] + $tot['equity'];
        $difference = $tot['asset'] - $liabilitiesAndEquity;

        return $this->table(
            'Balance Sheet',
            ['Code', 'Account', 'Type', 'Balance'],
            $rows,
            [
                'Assets' => number_format($tot['asset'], 2),
                'Liabilities' => number_format($tot['liability'], 2),
                'Equity' => number_format($tot['equity'], 2),
                'Liabilities + Equity' => number_format($liabilitiesAndEquity, 2),
                'Difference' => number_format($difference, 2),
            ],
            $r
        );
    }

    public function cashFlow(Request $r){[$from,$to]=$this->dates($r);$data=JournalLine::query()->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')->join('accounts','accounts.id','=','journal_lines.account_id')->whereBetween('journal_entries.journal_date',[$from,$to])->where('accounts.type','asset')->whereIn('accounts.subtype',['cash','bank'])->when($r->filled('entity_id'),fn($q)=>$q->where('accounts.id',$r->entity_id))->groupBy('accounts.id','accounts.code','accounts.name')->selectRaw('accounts.code,accounts.name,SUM(journal_lines.debit) cash_in,SUM(journal_lines.credit) cash_out')->orderBy('accounts.code')->get();$rows=[];$in=0;$out=0;foreach($data as $x){$in+=(float)$x->cash_in;$out+=(float)$x->cash_out;$rows[]=[$x->code,$x->name,number_format($x->cash_in,2),number_format($x->cash_out,2),number_format((float)$x->cash_in-(float)$x->cash_out,2)];}return $this->table('Cash Flow',['Code','Cash/Bank Account','Cash In','Cash Out','Net'],$rows,['Total Cash In'=>number_format($in,2),'Total Cash Out'=>number_format($out,2),'Net Cash Flow'=>number_format($in-$out,2)],$r);}

    public function sales(Request $r){$q=Sale::with('customer','warehouse')->where('status','posted');$this->dateFilter($q,$r,'sale_date');if($r->filled('customer_id'))$q->where('customer_id',$r->customer_id);elseif($r->filled('entity_id'))$q->where('customer_id',$r->entity_id);if($r->filled('warehouse_id'))$q->where('warehouse_id',$r->warehouse_id);$data=$q->orderBy('sale_date')->get();$rows=$data->map(fn($x)=>[$x->sale_date->format('Y-m-d'),$x->invoice_no,$x->customer?->name??'Walk-in',$x->warehouse->name,number_format($x->grand_total,2),number_format($x->paid_amount,2),number_format($x->due_amount,2)])->all();return $this->table('Sales Report',['Date','Invoice','Customer','Warehouse','Total','Paid','Due'],$rows,['Grand Total'=>number_format($data->sum('grand_total'),2),'Due'=>number_format($data->sum('due_amount'),2)],$r);}

    public function purchases(Request $r){$q=Purchase::with('supplier','warehouse')->where('status','posted');$this->dateFilter($q,$r,'purchase_date');if($r->filled('supplier_id'))$q->where('supplier_id',$r->supplier_id);elseif($r->filled('entity_id'))$q->where('supplier_id',$r->entity_id);if($r->filled('warehouse_id'))$q->where('warehouse_id',$r->warehouse_id);$data=$q->orderBy('purchase_date')->get();$rows=$data->map(fn($x)=>[$x->purchase_date->format('Y-m-d'),$x->purchase_no,$x->supplier->name,$x->warehouse->name,number_format($x->grand_total,2),number_format($x->paid_amount,2),number_format($x->due_amount,2)])->all();return $this->table('Purchase Report',['Date','Purchase','Supplier','Warehouse','Total','Paid','Due'],$rows,['Grand Total'=>number_format($data->sum('grand_total'),2),'Due'=>number_format($data->sum('due_amount'),2)],$r);}

    public function customers(Request $r){$data=Customer::with('group')->where('is_active',true)->get()->map(function($c){$bal=(float)$c->ledgerEntries()->selectRaw('COALESCE(SUM(debit),0)-COALESCE(SUM(credit),0) balance')->value('balance');return [$c->code,$c->name,$c->group?->name,$c->phone,number_format($bal,2)];});return $this->table('Customer Report',['Code','Customer','Group','Phone','Receivable / Advance'],$data->all(),[],$r);}
    public function suppliers(Request $r){$data=Supplier::with('group')->where('is_active',true)->get()->map(function($s){$bal=(float)$s->ledgerEntries()->selectRaw('COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0) balance')->value('balance');return [$s->code,$s->name,$s->group?->name,$s->phone,number_format($bal,2)];});return $this->table('Supplier Report',['Code','Supplier','Group','Phone','Payable / Advance'],$data->all(),[],$r);}
    public function products(Request $r){$q=WarehouseStock::with('product.category','product.brand','product.unit','warehouse');if($r->filled('warehouse_id'))$q->where('warehouse_id',$r->warehouse_id);if($r->filled('product_id'))$q->where('product_id',$r->product_id);elseif($r->filled('entity_id'))$q->where('product_id',$r->entity_id);$data=$q->get();$rows=$data->map(fn($x)=>[$x->product->code,$x->product->name,$x->warehouse->name,$x->product->unit->short_name,$x->quantity,$x->product->category?->name,$x->product->brand?->name])->all();return $this->table('Product / Stock Report',['Code','Product','Warehouse','Unit','Stock','Category','Brand'],$rows,[],$r);}
    public function payments(Request $r){[$from,$to]=$this->dates($r);$rows=[];foreach(CustomerPayment::with('customer')->whereBetween('payment_date',[$from,$to])->when($r->filled('entity_id'),fn($q)=>$q->where('customer_id',$r->entity_id))->when($r->filled('filter'),fn($q)=>$q->where('method',$r->filter))->get() as $p)$rows[]=[$p->payment_date->format('Y-m-d'),$p->payment_no,'Customer Receipt',$p->customer->name,$p->method,number_format($p->amount,2)];foreach(SupplierPayment::with('supplier')->whereBetween('payment_date',[$from,$to])->when($r->filled('entity_id'),fn($q)=>$q->where('supplier_id',$r->entity_id))->when($r->filled('filter'),fn($q)=>$q->where('method',$r->filter))->get() as $p)$rows[]=[$p->payment_date->format('Y-m-d'),$p->payment_no,'Supplier Payment',$p->supplier->name,$p->method,number_format($p->amount,2)];usort($rows,fn($a,$b)=>strcmp($a[0],$b[0]));return $this->table('Payment Report',['Date','No','Type','Party','Method','Amount'],$rows,[],$r);}
    public function expenses(Request $r){$q=Expense::with('category')->where('status','posted');$this->dateFilter($q,$r,'expense_date');if($r->filled('category_id'))$q->where('expense_category_id',$r->category_id);elseif($r->filled('entity_id'))$q->where('expense_category_id',$r->entity_id);$data=$q->get();$rows=$data->map(fn($x)=>[$x->expense_date->format('Y-m-d'),$x->expense_no,$x->category->name,$x->description,number_format($x->amount,2)])->all();return $this->table('Expense Report',['Date','No','Category','Description','Amount'],$rows,['Total'=>number_format($data->sum('amount'),2)],$r);}
    public function incomeExpense(Request $r){[$from,$to]=$this->dates($r);$lines=JournalLine::with('entry','account')->whereHas('entry',fn($q)=>$q->whereBetween('journal_date',[$from,$to]))->whereHas('account',fn($q)=>$q->whereIn('type',['income','expense']))->get();$groups=$lines->groupBy(fn($l)=>$l->entry->journal_date->format('Y-m'));$data=[];foreach($groups->sortKeys() as $period=>$group){$income=0;$expense=0;foreach($group as $line){if($line->account->type==='income')$income+=(float)$line->credit-(float)$line->debit;else$expense+=(float)$line->debit-(float)$line->credit;}$data[]=[$period,number_format($income,2),number_format($expense,2),number_format($income-$expense,2)];}return $this->table('Income / Expense Report',['Period','Income','Expense','Net'],$data,[],$r);}
    public function registers(Request $r){[$from,$to]=$this->dates($r);$rows=DB::table('journal_entries')->whereBetween('journal_date',[$from,$to])->when($r->filled('filter'),fn($q)=>$q->where('reference_type',$r->filter))->orderBy('journal_date')->orderBy('id')->get()->map(fn($x)=>[$x->journal_date,$x->journal_no,$x->reference_type,$x->reference_no,$x->description])->all();return $this->table('Register Report',['Date','Journal','Type','Reference','Description'],$rows,[],$r);}

    private function table(string $title,array $headers,array $rows,array $totals,Request $request){if($request->boolean('csv'))return $this->csv($title,$headers,$rows);return view('reports.table',compact('title','headers','rows','totals'));}
    private function csv(string $title,array $headers,array $rows):StreamedResponse{return response()->streamDownload(function()use($headers,$rows){$h=fopen('php://output','w');fputcsv($h,$headers);foreach($rows as $row)fputcsv($h,$row);fclose($h);},strtolower(str_replace(' ','-',$title)).'.csv',['Content-Type'=>'text/csv']);}
    private function dates(Request $r):array{return [$r->input('from',now()->startOfMonth()->toDateString()),$r->input('to',now()->toDateString())];}
    private function dateFilter($q,Request $r,string $column):void{[$from,$to]=$this->dates($r);$q->whereBetween($column,[$from,$to]);}
}
