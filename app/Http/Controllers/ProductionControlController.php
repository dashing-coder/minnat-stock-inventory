<?php
namespace App\Http\Controllers;
use App\Models\{Account,Customer,Product,Purchase,Sale,Supplier,Warehouse,WarehouseStock};
use App\Services\PeriodLockService;
use App\Services\Commercial\{PurchaseService,SaleService};
use App\Services\Accounting\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,File,Schema,Storage};
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ProductionControlController extends Controller {
    public function hub(){ return view('production-controls.hub'); }
    public function periods(){ return view('production-controls.periods',['rows'=>DB::table('accounting_periods')->orderByDesc('start_date')->paginate(30)]); }
    public function periodStore(Request $r){$d=$r->validate(['name'=>'required|string|max:100','start_date'=>'required|date','end_date'=>'required|date|after_or_equal:start_date']); if(DB::table('accounting_periods')->where(function($q)use($d){$q->whereBetween('start_date',[$d['start_date'],$d['end_date']])->orWhereBetween('end_date',[$d['start_date'],$d['end_date']])->orWhere(fn($x)=>$x->where('start_date','<=',$d['start_date'])->where('end_date','>=',$d['end_date']));})->exists()) throw ValidationException::withMessages(['start_date'=>__('Accounting periods cannot overlap.')]); DB::table('accounting_periods')->insert($d+['status'=>'open','created_at'=>now(),'updated_at'=>now()]); return back()->with('success',__('Accounting period created.'));}
    public function periodClose(int $id){$p=DB::table('accounting_periods')->where('id',$id)->first();abort_unless($p,404);DB::table('accounting_periods')->where('id',$id)->update(['status'=>'closed','closed_by'=>auth()->id(),'closed_at'=>now(),'updated_at'=>now()]);return back()->with('success',__('Accounting period closed.'));}
    public function periodReopen(int $id){DB::table('accounting_periods')->where('id',$id)->update(['status'=>'open','closed_by'=>null,'closed_at'=>null,'updated_at'=>now()]);return back()->with('success',__('Accounting period reopened.'));}

    /** Historical statement review and incremental clearing; read-only until submitted. */
    public function bank(Request $r, \App\Services\Accounting\BankReconciliationService $service)
    {
        $query = $r->validate([
            'account_id' => 'nullable|integer|exists:accounts,id',
            'statement_date' => 'nullable|date_format:Y-m-d',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
        ]);
        $accountId = isset($query['account_id']) ? (int) $query['account_id'] : null;
        $date = $query['statement_date'] ?? now()->toDateString();
        $from = $query['from'] ?? null;
        $to = $query['to'] ?? null;
        if ($from && $to && $from > $to) {
            throw ValidationException::withMessages(['to' => 'End date must not precede start date.']);
        }
        $accounts = Account::withTrashed()->whereIn('subtype', ['bank', 'cash'])->orderBy('code')->get();
        $account = $accountId ? $accounts->firstWhere('id', $accountId) : null;
        if ($accountId && !$account) abort(422, 'Choose a bank or cash account.');
        $lines = null;
        $system = $previousCleared = 0.0;
        $draft = null;
        $draftSelectedIds = [];
        $selectedRecord = null;
        $selectedItems = null;
        $hasLaterFinalized = false;
        $existingFinalized = false;
        if ($account) {
            $system = $service->systemBalance($accountId, $date);
            $previousCleared = $service->previouslyClearedBalance($accountId, $date);
            $draft = DB::table('bank_reconciliations')->where('account_id', $accountId)
                ->whereDate('statement_date', $date)->where('status', 'draft')->first();
            if ($draft) $draftSelectedIds = DB::table('bank_reconciliation_items')
                ->where('bank_reconciliation_id', $draft->id)->pluck('journal_line_id')->map(fn ($id) => (int) $id)->all();
            $selectedRecord = DB::table('bank_reconciliations')->where('account_id', $accountId)
                ->whereDate('statement_date', $date)->orderByRaw("CASE WHEN status = 'reconciled' THEN 0 ELSE 1 END")
                ->orderByDesc('id')->first();
            if ($selectedRecord) {
                $selectedItems = DB::table('bank_reconciliation_items as bri')
                    ->leftJoin('journal_lines as jl', 'jl.id', '=', 'bri.journal_line_id')
                    ->leftJoin('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
                    ->where('bri.bank_reconciliation_id', $selectedRecord->id)
                    ->select('bri.journal_line_id', 'bri.amount', 'je.journal_date',
                        'je.journal_no', 'je.description', 'je.status as journal_status',
                        'jl.debit', 'jl.credit', 'jl.account_id as line_account_id')
                    ->orderBy('bri.id')->paginate(100, ['*'], 'items_page')->withQueryString();
            }
            $existingFinalized = DB::table('bank_reconciliations')->where('account_id', $accountId)
                ->whereDate('statement_date', $date)->where('status', 'reconciled')->exists();
            $hasLaterFinalized = DB::table('bank_reconciliations')->where('account_id', $accountId)
                ->where('status', 'reconciled')->whereDate('statement_date', '>', $date)->exists();
            $eligible = DB::table('journal_lines as jl')
                ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
                ->where('jl.account_id', $accountId)
                ->where('je.status', 'posted')
                ->whereDate('je.journal_date', '<=', $date)
                ->whereNotIn('jl.id', $service->clearedLineIds($accountId, $date));
            if ($from) $eligible->whereDate('je.journal_date', '>=', $from);
            if ($to) $eligible->whereDate('je.journal_date', '<=', $to);
            $lines = $eligible->orderBy('je.journal_date')->orderBy('jl.id')
                ->select('jl.id', 'je.journal_date', 'je.journal_no', 'je.description',
                    'je.reference_type', 'jl.debit', 'jl.credit')->paginate(100)->withQueryString();
        }
        $rows = DB::table('bank_reconciliations as br')
            ->join('accounts as a', 'a.id', '=', 'br.account_id')
            ->select('br.*', 'a.name as account_name', 'a.code as account_code')
            ->orderByDesc('br.statement_date')->orderByDesc('br.id')->paginate(30, ['*'], 'history_page')
            ->withQueryString();
        return view('production-controls.bank', compact('accounts', 'accountId', 'date',
            'from', 'to', 'lines', 'system', 'previousCleared', 'draft', 'draftSelectedIds',
            'existingFinalized', 'hasLaterFinalized', 'rows', 'account', 'selectedRecord', 'selectedItems'));
    }

    public function bankStore(Request $r, \App\Services\Accounting\BankReconciliationService $service)
    {
        $data = $r->validate([
            'account_id' => 'required|integer|exists:accounts,id',
            'statement_date' => 'required|date_format:Y-m-d|before_or_equal:today',
            // DECIMAL(18,2) without float/scientific-string coercion or fractional truncation.
            'statement_balance' => ['required', 'regex:/^-?[0-9]{1,15}(\.[0-9]{1,2})?$/'],
            'journal_line_ids' => 'nullable|array|max:100',
            'journal_line_ids.*' => 'integer|distinct|exists:journal_lines,id',
            'notes' => 'nullable|string|max:2000',
        ]);
        $result = $service->save($data, auth()->id());
        return redirect()->route('production-controls.bank', [
            'account_id' => $data['account_id'], 'statement_date' => $data['statement_date'],
        ])->with('success', $result['status'] === 'reconciled'
            ? __('Bank statement reconciled and finalized.')
            : __('Draft saved. Difference remaining: :difference. Continue selecting posted statement lines.',
                ['difference' => number_format($result['difference'], 2)]));
    }

    public function bankResetDraft(Request $r, \App\Services\Accounting\BankReconciliationService $service)
    {
        $data = $r->validate([
            'account_id' => 'required|integer|exists:accounts,id',
            'statement_date' => 'required|date_format:Y-m-d',
        ]);
        $service->resetDraft((int) $data['account_id'], $data['statement_date']);
        return redirect()->route('production-controls.bank', $data)
            ->with('success', __('Draft selections cleared. Finalized statements are unchanged.'));
    }

    public function taxes(Request $r){$from=$r->input('from',now()->startOfMonth()->toDateString());$to=$r->input('to',now()->toDateString());$output=(float)DB::table('sales')->where('status','posted')->whereBetween('sale_date',[$from,$to])->sum('tax');$input=(float)DB::table('purchases')->where('status','posted')->whereBetween('purchase_date',[$from,$to])->sum('tax');return view('production-controls.taxes',['rows'=>DB::table('tax_rates')->orderBy('rate')->get(),'products'=>Product::where('is_active',true)->orderBy('name')->get(['id','name','sku','tax_rate']),'outputTax'=>$output,'inputTax'=>$input,'from'=>$from,'to'=>$to]);}
    public function taxStore(Request $r){$d=$r->validate(['code'=>'required|string|max:40|unique:tax_rates,code','name'=>'required|string|max:120','rate'=>'required|numeric|min:0|max:100','type'=>'required|in:vat,tax','is_default'=>'nullable|boolean']);if($r->boolean('is_default'))DB::table('tax_rates')->update(['is_default'=>0]);DB::table('tax_rates')->insert($d+['is_default'=>$r->boolean('is_default'),'is_active'=>1,'created_at'=>now(),'updated_at'=>now()]);return back()->with('success',__('Tax rate saved.'));}
    public function taxAssign(Request $r){$d=$r->validate(['product_id'=>'required|exists:products,id','tax_rate_id'=>'required|exists:tax_rates,id']);$tax=DB::table('tax_rates')->where('id',$d['tax_rate_id'])->where('is_active',1)->first();abort_unless($tax,422);DB::table('products')->where('id',$d['product_id'])->update(['tax_rate'=>$tax->rate,'updated_at'=>now()]);return back()->with('success',__('Product tax rate updated.'));}

    public function approvals(){return view('production-controls.approvals',['rows'=>DB::table('approval_requests')->orderByDesc('created_at')->paginate(40),'enabled'=>(string)DB::table('settings')->where('key','maker_checker_enabled')->value('value')==='1']);}
    public function approvalToggle(Request $r){DB::table('settings')->updateOrInsert(['key'=>'maker_checker_enabled'],['value'=>$r->boolean('enabled')?'1':'0','group'=>'controls','type'=>'boolean','is_public'=>0,'updated_at'=>now(),'created_at'=>now()]);return back()->with('success',__('Maker–Checker setting updated.'));}
    public function approvalDecide(Request $r,int $id){$d=$r->validate(['decision'=>'required|in:approved,rejected','decision_notes'=>'nullable|string|max:2000']);$row=DB::table('approval_requests')->where('id',$id)->first();abort_unless($row,404);if((int)$row->requested_by===(int)auth()->id())throw ValidationException::withMessages(['approval'=>__('Maker and checker must be different users.')]);DB::table('approval_requests')->where('id',$id)->update(['status'=>$d['decision'],'decision_notes'=>$d['decision_notes']??null,'decided_by'=>auth()->id(),'decided_at'=>now(),'updated_at'=>now()]);return back()->with('success',__('Approval decision saved.'));}

    public function orders(Request $r,string $type){abort_unless(in_array($type,['purchase','sales'],true),404);$table=$type==='purchase'?'purchase_orders':'sales_orders';$party=$type==='purchase'?'suppliers':'customers';$partyKey=$type==='purchase'?'supplier_id':'customer_id';$rows=DB::table($table)->leftJoin($party,"$party.id","=","$table.$partyKey")->join('warehouses','warehouses.id','=',"$table.warehouse_id")->select("$table.*","$party.name as party_name",'warehouses.name as warehouse_name')->orderByDesc("$table.id")->paginate(30);return view('production-controls.orders',['type'=>$type,'rows'=>$rows,'parties'=>DB::table($party)->where('is_active',1)->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->get(),'products'=>Product::where('is_active',true)->orderBy('name')->get(),'currencies'=>DB::table('currencies')->where('is_active',1)->orderBy('code')->get()]);}
    public function orderStore(Request $r,string $type){abort_unless(in_array($type,['purchase','sales'],true),404);$partyKey=$type==='purchase'?'supplier_id':'customer_id';$partyTable=$type==='purchase'?'suppliers':'customers';$d=$r->validate([$partyKey=>($type==='purchase'?'required':'nullable')."|exists:$partyTable,id",'warehouse_id'=>'required|exists:warehouses,id','order_date'=>'required|date','expected_date'=>'nullable|date|after_or_equal:order_date','currency_code'=>'required|string|max:10','exchange_rate'=>'required|numeric|gt:0','notes'=>'nullable|string','items'=>'required|array|min:1','items.*.product_id'=>'required|exists:products,id','items.*.quantity'=>'required|numeric|gt:0','items.*.unit_price'=>'required|numeric|min:0']);$table=$type==='purchase'?'purchase_orders':'sales_orders';$itemsTable=$type==='purchase'?'purchase_order_items':'sales_order_items';$fk=$type==='purchase'?'purchase_order_id':'sales_order_id';DB::transaction(function()use($d,$table,$itemsTable,$fk,$partyKey,$type){$grand=collect($d['items'])->sum(fn($x)=>(float)$x['quantity']*(float)$x['unit_price']);$prefix=$type==='purchase'?'PO':'SO';$row=['order_no'=>$prefix.'-TMP-'.uniqid(),$partyKey=>$d[$partyKey]??null,'warehouse_id'=>$d['warehouse_id'],'order_date'=>$d['order_date'],'expected_date'=>$d['expected_date']??null,'status'=>'open','grand_total'=>round($grand,2),'currency_code'=>$d['currency_code'],'exchange_rate'=>$d['exchange_rate'],'notes'=>$d['notes']??null,'created_by'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()];$id=DB::table($table)->insertGetId($row);$orderNo=$prefix.'-'.str_pad((string)$id,6,'0',STR_PAD_LEFT);DB::table($table)->where('id',$id)->update(['order_no'=>$orderNo]);foreach($d['items'] as $item){DB::table($itemsTable)->insert([$fk=>$id,'product_id'=>$item['product_id'],'quantity'=>$item['quantity'],'unit_price'=>$item['unit_price'],'line_total'=>round((float)$item['quantity']*(float)$item['unit_price'],2),'created_at'=>now(),'updated_at'=>now()]);if($type==='sales'){$stock=WarehouseStock::firstOrCreate(['warehouse_id'=>$d['warehouse_id'],'product_id'=>$item['product_id']],['quantity'=>0,'reserved_quantity'=>0]);$stock=WarehouseStock::whereKey($stock->id)->lockForUpdate()->first();$available=(float)$stock->quantity-(float)$stock->reserved_quantity;if($available+1e-7<(float)$item['quantity'])throw ValidationException::withMessages(['items'=>__('Sales Order reservation exceeds available stock for one or more products.')]);$stock->reserved_quantity=(float)$stock->reserved_quantity+(float)$item['quantity'];$stock->save();DB::table('inventory_reservations')->insert(['warehouse_id'=>$d['warehouse_id'],'product_id'=>$item['product_id'],'quantity'=>$item['quantity'],'reference_type'=>'sales_order','reference_id'=>$id,'reference_no'=>$orderNo,'reserved_until'=>$d['expected_date']??null,'status'=>'active','created_by'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);}}});return back()->with('success',__('Order created.'));}

    public function orderConvert(string $type, int $id){
        abort_unless(in_array($type,['purchase','sales'],true),404);
        return DB::transaction(function() use($type,$id){
            $table=$type==='purchase'?'purchase_orders':'sales_orders';
            $itemsTable=$type==='purchase'?'purchase_order_items':'sales_order_items';
            $fk=$type==='purchase'?'purchase_order_id':'sales_order_id';
            $order=DB::table($table)->where('id',$id)->lockForUpdate()->first(); abort_unless($order,404);
            if($order->status==='converted' && $order->converted_id) return back()->with('success',__('Order already converted.'));
            $items=DB::table($itemsTable)->where($fk,$id)->get(); if($items->isEmpty()) throw ValidationException::withMessages(['order'=>__('Order has no items.')]);
            if($type==='purchase'){
                $doc=app(PurchaseService::class)->create([
                    'supplier_id'=>$order->supplier_id,'warehouse_id'=>$order->warehouse_id,'purchase_date'=>$order->order_date,
                    'due_date'=>$order->expected_date,'currency_code'=>$order->currency_code,'exchange_rate'=>$order->exchange_rate,
                    'supplier_invoice_no'=>null,'discount'=>0,'tax'=>0,'shipping'=>0,'other_cost'=>0,'notes'=>'Converted from '.$order->order_no,
                    'items'=>$items->map(fn($x)=>['product_id'=>$x->product_id,'quantity'=>$x->quantity,'free_quantity'=>0,'unit_cost'=>$x->unit_price,'discount_type'=>'fixed','discount_value'=>0,'tax'=>null,'batch_no'=>null,'manufactured_at'=>null,'expiry_date'=>null])->all(),
                ]);
                $route='purchases.show';
            } else {
                $reservations=DB::table('inventory_reservations')->where('reference_type','sales_order')->where('reference_id',$id)->where('status','active')->lockForUpdate()->get();
                foreach($reservations as $res){$stock=WarehouseStock::where('warehouse_id',$res->warehouse_id)->where('product_id',$res->product_id)->lockForUpdate()->first();if($stock){$stock->reserved_quantity=max(0,(float)$stock->reserved_quantity-(float)$res->quantity);$stock->save();}DB::table('inventory_reservations')->where('id',$res->id)->update(['status'=>'released','updated_at'=>now()]);}
                $doc=app(SaleService::class)->create([
                    'customer_id'=>$order->customer_id,'warehouse_id'=>$order->warehouse_id,'sale_date'=>$order->order_date,
                    'due_date'=>$order->expected_date,'currency_code'=>$order->currency_code,'exchange_rate'=>$order->exchange_rate,
                    'discount'=>0,'tax'=>0,'shipping'=>0,'labor_charge'=>0,'notes'=>'Converted from '.$order->order_no,
                    'items'=>$items->map(fn($x)=>['product_id'=>$x->product_id,'quantity'=>$x->quantity,'free_quantity'=>0,'bundle_quantity'=>null,'unit_price'=>$x->unit_price,'price_type'=>'retail','discount_type'=>'fixed','discount_value'=>0,'tax'=>null])->all(),
                ]);
                $route='sales.show';
            }
            DB::table($table)->where('id',$id)->update(['status'=>'converted','converted_id'=>$doc->id,'updated_at'=>now()]);
            return redirect()->route($route,$doc)->with('success',__('Order converted to draft bill.'));
        });
    }

    public function reservations(){return view('production-controls.reservations',['rows'=>DB::table('inventory_reservations')->join('products','products.id','=','inventory_reservations.product_id')->join('warehouses','warehouses.id','=','inventory_reservations.warehouse_id')->select('inventory_reservations.*','products.name as product_name','warehouses.name as warehouse_name')->orderByDesc('inventory_reservations.id')->paginate(30),'products'=>Product::where('is_active',true)->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->get()]);}
    public function reservationStore(Request $r){$d=$r->validate(['warehouse_id'=>'required|exists:warehouses,id','product_id'=>'required|exists:products,id','quantity'=>'required|numeric|gt:0','reference_type'=>'nullable|string|max:50','reference_no'=>'nullable|string|max:100','reserved_until'=>'nullable|date','notes'=>'nullable|string|max:1000']);DB::transaction(function()use($d){$s=WarehouseStock::firstOrCreate(['warehouse_id'=>$d['warehouse_id'],'product_id'=>$d['product_id']],['quantity'=>0,'reserved_quantity'=>0]);$s=WarehouseStock::whereKey($s->id)->lockForUpdate()->first();$available=(float)$s->quantity-(float)$s->reserved_quantity;if($available+1e-7<(float)$d['quantity'])throw ValidationException::withMessages(['quantity'=>__('Reservation exceeds available stock.')]);$s->reserved_quantity=(float)$s->reserved_quantity+(float)$d['quantity'];$s->save();DB::table('inventory_reservations')->insert($d+['status'=>'active','created_by'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);});return back()->with('success',__('Inventory reserved.'));}
    public function reservationRelease(int $id){DB::transaction(function()use($id){$r=DB::table('inventory_reservations')->where('id',$id)->lockForUpdate()->first();abort_unless($r,404);if($r->status!=='active')return;$s=WarehouseStock::where('warehouse_id',$r->warehouse_id)->where('product_id',$r->product_id)->lockForUpdate()->first();if($s){$s->reserved_quantity=max(0,(float)$s->reserved_quantity-(float)$r->quantity);$s->save();}DB::table('inventory_reservations')->where('id',$id)->update(['status'=>'released','updated_at'=>now()]);});return back()->with('success',__('Reservation released.'));}

    public function serials(){return view('production-controls.serials',['rows'=>DB::table('product_serials')->join('products','products.id','=','product_serials.product_id')->leftJoin('warehouses','warehouses.id','=','product_serials.warehouse_id')->select('product_serials.*','products.name as product_name','warehouses.name as warehouse_name')->orderByDesc('product_serials.id')->paginate(40),'products'=>Product::where('is_active',true)->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->get()]);}
    public function serialStore(Request $r){$d=$r->validate(['product_id'=>'required|exists:products,id','warehouse_id'=>'nullable|exists:warehouses,id','serial_no'=>'required|string|max:160|unique:product_serials,serial_no','imei'=>'nullable|string|max:160|unique:product_serials,imei','source_type'=>'nullable|in:purchase,sale,opening','source_id'=>'nullable|integer|min:1']);DB::table('product_serials')->insert($d+['status'=>'in_stock','created_at'=>now(),'updated_at'=>now()]);DB::table('products')->where('id',$d['product_id'])->update(['track_serial'=>1]);return back()->with('success',__('Serial/IMEI registered.'));}
    public function serialUpdate(Request $r,int $id){$d=$r->validate(['status'=>'required|in:in_stock,reserved,sold,damaged,returned','warehouse_id'=>'nullable|exists:warehouses,id','source_type'=>'nullable|in:purchase,sale,return,adjustment','source_id'=>'nullable|integer|min:1']);DB::table('product_serials')->where('id',$id)->update($d+['updated_at'=>now()]);return back()->with('success',__('Serial/IMEI status updated.'));}

    public function currencies(){return view('production-controls.currencies',['rows'=>DB::table('currencies')->orderByDesc('is_base')->orderBy('code')->get(),'rates'=>DB::table('exchange_rates')->orderByDesc('rate_date')->limit(50)->get()]);}
    public function currencyStore(Request $r){$d=$r->validate(['code'=>'required|string|max:10|unique:currencies,code','name'=>'required|string|max:100','symbol'=>'nullable|string|max:12']);DB::table('currencies')->insert($d+['is_base'=>0,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()]);return back()->with('success',__('Currency saved.'));}
    public function rateStore(Request $r){$d=$r->validate(['currency_code'=>'required|exists:currencies,code','rate_date'=>'required|date','rate'=>'required|numeric|gt:0']);DB::table('exchange_rates')->updateOrInsert(['currency_code'=>$d['currency_code'],'rate_date'=>$d['rate_date']],['rate'=>$d['rate'],'updated_at'=>now(),'created_at'=>now()]);return back()->with('success',__('Exchange rate saved.'));}

    public function attachments(){return view('production-controls.attachments',['rows'=>DB::table('document_attachments')->orderByDesc('id')->paginate(40)]);}
    public function attachmentStore(Request $r){$d=$r->validate(['attachable_type'=>'required|in:sale,purchase,expense,customer_payment,supplier_payment','attachable_id'=>'required|integer|min:1','file'=>'required|file|max:10240|mimes:pdf,jpg,jpeg,png,webp,xls,xlsx,doc,docx']);$map=['sale'=>'sales','purchase'=>'purchases','expense'=>'expenses','customer_payment'=>'customer_payments','supplier_payment'=>'supplier_payments'];if(!DB::table($map[$d['attachable_type']])->where('id',$d['attachable_id'])->exists())throw ValidationException::withMessages(['attachable_id'=>__('Referenced document does not exist.')]);$f=$r->file('file');$path=$f->store('attachments/'.$d['attachable_type'],'local');DB::table('document_attachments')->insert(['attachable_type'=>$d['attachable_type'],'attachable_id'=>$d['attachable_id'],'original_name'=>$f->getClientOriginalName(),'stored_path'=>$path,'mime_type'=>$f->getMimeType(),'size'=>$f->getSize(),'uploaded_by'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);return back()->with('success',__('Attachment uploaded.'));}
    public function attachmentDownload(int $id){$a=DB::table('document_attachments')->where('id',$id)->first();abort_unless($a,404);return Storage::disk('local')->download($a->stored_path,$a->original_name);}

    public function assets(){return view('production-controls.assets',['rows'=>DB::table('fixed_assets')->orderByDesc('id')->paginate(30)]);}
    public function assetStore(Request $r){$d=$r->validate(['name'=>'required|string|max:180','purchase_date'=>'nullable|date','cost'=>'required|numeric|min:0','salvage_value'=>'nullable|numeric|min:0','useful_life_months'=>'required|integer|min:1','notes'=>'nullable|string']);$id=DB::table('fixed_assets')->insertGetId($d+['asset_no'=>'AST-TMP-'.uniqid(),'depreciation_method'=>'straight_line','accumulated_depreciation'=>0,'status'=>'active','created_at'=>now(),'updated_at'=>now()]);DB::table('fixed_assets')->where('id',$id)->update(['asset_no'=>'AST-'.str_pad((string)$id,6,'0',STR_PAD_LEFT)]);return back()->with('success',__('Fixed asset saved.'));}
    public function assetDepreciate(int $id){
        return DB::transaction(function() use($id){
            $a=DB::table('fixed_assets')->where('id',$id)->lockForUpdate()->first(); abort_unless($a,404);
            $date=now()->startOfMonth()->toDateString();
            app(PeriodLockService::class)->assertOpen($date);
            if(DB::table('fixed_asset_depreciations')->where('fixed_asset_id',$id)->where('depreciation_date',$date)->exists()) throw ValidationException::withMessages(['depreciation'=>__('Depreciation for this month is already posted.')]);
            $remaining=max(0,(float)$a->cost-(float)$a->salvage_value-(float)$a->accumulated_depreciation);
            $monthly=max(0,((float)$a->cost-(float)$a->salvage_value)/max(1,(int)$a->useful_life_months)); $amount=min($remaining,$monthly);
            if($amount<=0) throw ValidationException::withMessages(['depreciation'=>__('Asset is fully depreciated.')]);
            $depId=DB::table('fixed_asset_depreciations')->insertGetId(['fixed_asset_id'=>$id,'depreciation_date'=>$date,'amount'=>round($amount,2),'created_by'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);
            DB::table('fixed_assets')->where('id',$id)->update(['accumulated_depreciation'=>round((float)$a->accumulated_depreciation+$amount,2),'updated_at'=>now()]);
            app(AccountingService::class)->post('asset_depreciation',$depId,$a->asset_no,$date,'Depreciation '.$a->asset_no,[['account_code'=>'6050','debit'=>$amount,'credit'=>0],['account_code'=>'1310','debit'=>0,'credit'=>$amount]]);
            return back()->with('success',__('Monthly depreciation posted to accounts.'));
        });
    }

    public function planning(){
        $budgets=DB::table('budgets')->join('accounts','accounts.id','=','budgets.account_id')->leftJoin('cost_centers','cost_centers.id','=','budgets.cost_center_id')->leftJoin('projects','projects.id','=','budgets.project_id')->select('budgets.*','accounts.name as account_name','cost_centers.name as cost_center_name','projects.name as project_name')->orderByDesc('budgets.id')->limit(100)->get();
        foreach($budgets as $b){
            $q=DB::table('journal_lines')->join('journal_entries','journal_entries.id','=','journal_lines.journal_entry_id')->where('journal_lines.account_id',$b->account_id)->where('journal_entries.status','posted')->whereBetween('journal_entries.journal_date',[$b->period_start,$b->period_end]); if($b->cost_center_id)$q->where('journal_lines.cost_center_id',$b->cost_center_id); if($b->project_id)$q->where('journal_lines.project_id',$b->project_id); $b->actual_amount=(float)$q->selectRaw('COALESCE(SUM(journal_lines.debit-journal_lines.credit),0) a')->value('a');
            $b->variance=(float)$b->amount-(float)$b->actual_amount;
        }
        return view('production-controls.planning',['costCenters'=>DB::table('cost_centers')->orderBy('code')->get(),'projects'=>DB::table('projects')->leftJoin('cost_centers','cost_centers.id','=','projects.cost_center_id')->select('projects.*','cost_centers.name as cost_center_name')->orderByDesc('projects.id')->get(),'budgets'=>$budgets,'accounts'=>Account::where('is_active',true)->orderBy('code')->get()]);
    }
    public function costCenterStore(Request $r){$d=$r->validate(['code'=>'required|string|max:40|unique:cost_centers,code','name'=>'required|string|max:120']);DB::table('cost_centers')->insert($d+['is_active'=>1,'created_at'=>now(),'updated_at'=>now()]);return back()->with('success',__('Cost center saved.'));}
    public function projectStore(Request $r){$d=$r->validate(['code'=>'required|string|max:40|unique:projects,code','name'=>'required|string|max:160','cost_center_id'=>'nullable|exists:cost_centers,id','start_date'=>'nullable|date','end_date'=>'nullable|date|after_or_equal:start_date']);DB::table('projects')->insert($d+['status'=>'active','created_at'=>now(),'updated_at'=>now()]);return back()->with('success',__('Project saved.'));}
    public function budgetStore(Request $r){$d=$r->validate(['cost_center_id'=>'nullable|exists:cost_centers,id','project_id'=>'nullable|exists:projects,id','account_id'=>'required|exists:accounts,id','period_start'=>'required|date','period_end'=>'required|date|after_or_equal:period_start','amount'=>'required|numeric|min:0']);DB::table('budgets')->insert($d+['created_at'=>now(),'updated_at'=>now()]);return back()->with('success',__('Budget saved.'));}
}
