<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $permissions = [
        'production_controls.view','production_controls.manage','approvals.view','approvals.manage',
        'orders.view','orders.manage','bank_reconciliation.view','bank_reconciliation.manage',
        'taxes.view','taxes.manage','planning.view','planning.manage','assets.view','assets.manage'
    ];

    public function up(): void {
        Schema::create('accounting_periods', function(Blueprint $t){
            $t->id(); $t->string('name',100); $t->date('start_date'); $t->date('end_date');
            $t->string('status',20)->default('open'); $t->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('closed_at')->nullable(); $t->timestamps(); $t->unique(['start_date','end_date']); $t->index(['status','start_date','end_date']);
        });
        Schema::create('bank_reconciliations', function(Blueprint $t){
            $t->id(); $t->foreignId('account_id')->constrained('accounts')->restrictOnDelete(); $t->date('statement_date');
            $t->decimal('statement_balance',18,2); $t->decimal('system_balance',18,2); $t->decimal('cleared_balance',18,2)->default(0);
            $t->decimal('difference',18,2)->default(0); $t->string('status',20)->default('draft'); $t->text('notes')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $t->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('closed_at')->nullable(); $t->timestamps(); $t->index(['account_id','statement_date']);
        });
        Schema::create('bank_reconciliation_items', function(Blueprint $t){
            $t->id(); $t->foreignId('bank_reconciliation_id')->constrained()->cascadeOnDelete(); $t->foreignId('journal_line_id')->constrained('journal_lines')->cascadeOnDelete();
            $t->decimal('amount',18,2); $t->timestamps(); $t->unique(['bank_reconciliation_id','journal_line_id']);
        });
        Schema::create('tax_rates', function(Blueprint $t){
            $t->id(); $t->string('code',40)->unique(); $t->string('name',120); $t->decimal('rate',8,4)->default(0); $t->string('type',20)->default('vat');
            $t->boolean('is_default')->default(false); $t->boolean('is_active')->default(true); $t->timestamps();
        });
        Schema::create('approval_requests', function(Blueprint $t){
            $t->id(); $t->string('approvable_type',80); $t->unsignedBigInteger('approvable_id'); $t->string('reference_no',120)->nullable();
            $t->string('status',20)->default('pending'); $t->text('reason')->nullable(); $t->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamp('decided_at')->nullable(); $t->text('decision_notes')->nullable();
            $t->timestamps(); $t->unique(['approvable_type','approvable_id']); $t->index(['status','created_at']);
        });
        foreach ([['purchase_orders','supplier_id'],['sales_orders','customer_id']] as [$name,$party]) {
            Schema::create($name, function(Blueprint $t) use($name,$party){
                $t->id(); $t->string('order_no',50)->unique();
                if($party==='supplier_id') $t->foreignId($party)->constrained('suppliers')->restrictOnDelete(); else $t->foreignId($party)->nullable()->constrained('customers')->nullOnDelete();
                $t->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete(); $t->date('order_date'); $t->date('expected_date')->nullable();
                $t->string('status',20)->default('open'); $t->decimal('grand_total',18,2)->default(0); $t->string('currency_code',10)->default('BDT');
                $t->decimal('exchange_rate',18,6)->default(1); $t->text('notes')->nullable(); $t->unsignedBigInteger('converted_id')->nullable();
                $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps();
            });
        }
        Schema::create('purchase_order_items', function(Blueprint $t){$t->id();$t->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->decimal('quantity',18,4);$t->decimal('unit_price',18,4);$t->decimal('line_total',18,2);$t->timestamps();});
        Schema::create('sales_order_items', function(Blueprint $t){$t->id();$t->foreignId('sales_order_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->decimal('quantity',18,4);$t->decimal('unit_price',18,4);$t->decimal('line_total',18,2);$t->timestamps();});
        Schema::create('inventory_reservations', function(Blueprint $t){
            $t->id(); $t->foreignId('warehouse_id')->constrained()->restrictOnDelete(); $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->decimal('quantity',18,4); $t->string('reference_type',50)->nullable(); $t->unsignedBigInteger('reference_id')->nullable(); $t->string('reference_no',100)->nullable();
            $t->date('reserved_until')->nullable(); $t->string('status',20)->default('active'); $t->text('notes')->nullable(); $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps();
            $t->index(['warehouse_id','product_id','status']);
        });
        Schema::create('product_serials', function(Blueprint $t){
            $t->id();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $t->string('serial_no',160)->unique();$t->string('imei',160)->nullable()->unique();$t->string('status',20)->default('in_stock');
            $t->string('source_type',50)->nullable();$t->unsignedBigInteger('source_id')->nullable();$t->timestamps();$t->index(['product_id','status']);
        });
        Schema::create('currencies', function(Blueprint $t){$t->id();$t->string('code',10)->unique();$t->string('name',100);$t->string('symbol',12)->nullable();$t->boolean('is_base')->default(false);$t->boolean('is_active')->default(true);$t->timestamps();});
        Schema::create('exchange_rates', function(Blueprint $t){$t->id();$t->string('currency_code',10);$t->date('rate_date');$t->decimal('rate',18,6);$t->timestamps();$t->unique(['currency_code','rate_date']);});
        Schema::create('document_attachments', function(Blueprint $t){$t->id();$t->string('attachable_type',60);$t->unsignedBigInteger('attachable_id');$t->string('original_name');$t->string('stored_path');$t->string('mime_type',120)->nullable();$t->unsignedBigInteger('size')->default(0);$t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();$t->index(['attachable_type','attachable_id']);});
        Schema::create('cost_centers', function(Blueprint $t){$t->id();$t->string('code',40)->unique();$t->string('name',120);$t->boolean('is_active')->default(true);$t->timestamps();});
        Schema::create('projects', function(Blueprint $t){$t->id();$t->string('code',40)->unique();$t->string('name',160);$t->foreignId('cost_center_id')->nullable()->constrained()->nullOnDelete();$t->date('start_date')->nullable();$t->date('end_date')->nullable();$t->string('status',20)->default('active');$t->timestamps();});
        Schema::create('budgets', function(Blueprint $t){$t->id();$t->foreignId('cost_center_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('project_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('account_id')->constrained()->restrictOnDelete();$t->date('period_start');$t->date('period_end');$t->decimal('amount',18,2);$t->timestamps();$t->index(['period_start','period_end']);});
        Schema::create('fixed_assets', function(Blueprint $t){$t->id();$t->string('asset_no',50)->unique();$t->string('name',180);$t->date('purchase_date')->nullable();$t->decimal('cost',18,2);$t->decimal('salvage_value',18,2)->default(0);$t->unsignedInteger('useful_life_months')->default(60);$t->string('depreciation_method',30)->default('straight_line');$t->decimal('accumulated_depreciation',18,2)->default(0);$t->string('status',20)->default('active');$t->text('notes')->nullable();$t->timestamps();});
        Schema::create('fixed_asset_depreciations', function(Blueprint $t){$t->id();$t->foreignId('fixed_asset_id')->constrained()->cascadeOnDelete();$t->date('depreciation_date');$t->decimal('amount',18,2);$t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();$t->unique(['fixed_asset_id','depreciation_date']);});

        Schema::table('products', fn(Blueprint $t)=>$t->boolean('track_serial')->default(false)->after('track_stock'));
        Schema::table('sales', function(Blueprint $t){$t->string('currency_code',10)->default('BDT')->after('status');$t->decimal('exchange_rate',18,6)->default(1)->after('currency_code');});
        Schema::table('purchases', function(Blueprint $t){$t->string('currency_code',10)->default('BDT')->after('status');$t->decimal('exchange_rate',18,6)->default(1)->after('currency_code');});
        Schema::table('expenses', function(Blueprint $t){$t->foreignId('cost_center_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('project_id')->nullable()->constrained()->nullOnDelete();});
        Schema::table('journal_lines', function(Blueprint $t){$t->foreignId('cost_center_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('project_id')->nullable()->constrained()->nullOnDelete();});

        foreach ([['1300','Fixed Assets','asset','fixed_asset'],['1310','Accumulated Depreciation','asset','contra_asset'],['6050','Depreciation Expense','expense','depreciation']] as $a) DB::table('accounts')->insertOrIgnore(['code'=>$a[0],'name'=>$a[1],'type'=>$a[2],'subtype'=>$a[3],'is_system'=>1,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('currencies')->insertOrIgnore(['code'=>'BDT','name'=>'Bangladeshi Taka','symbol'=>'৳','is_base'=>1,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()]);
        foreach ([['TAX-0','Zero Rated',0],['VAT-5','VAT 5%',5],['VAT-7.5','VAT 7.5%',7.5],['VAT-10','VAT 10%',10],['VAT-15','VAT 15%',15]] as $r) DB::table('tax_rates')->insertOrIgnore(['code'=>$r[0],'name'=>$r[1],'rate'=>$r[2],'type'=>'vat','is_default'=>0,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('settings')->updateOrInsert(['key'=>'maker_checker_enabled'],['value'=>'0','group'=>'controls','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('settings')->updateOrInsert(['key'=>'credit_limit_enforced'],['value'=>'1','group'=>'controls','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('settings')->updateOrInsert(['key'=>'auto_payment_terms'],['value'=>'1','group'=>'controls','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('settings')->updateOrInsert(['key'=>'auto_product_tax'],['value'=>'1','group'=>'controls','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now()]);

        $now=now(); foreach($this->permissions as $p) DB::table('permissions')->insertOrIgnore(['name'=>$p,'guard_name'=>'web','created_at'=>$now,'updated_at'=>$now]);
        $pids=DB::table('permissions')->whereIn('name',$this->permissions)->pluck('id');
        $rids=DB::table('roles')->whereIn('name',['Super Admin','Admin'])->pluck('id');
        foreach($rids as $rid) foreach($pids as $pid) DB::table('role_has_permissions')->insertOrIgnore(['permission_id'=>$pid,'role_id'=>$rid]);
    }
    public function down(): void {
        Schema::table('journal_lines', function(Blueprint $t){$t->dropConstrainedForeignId('project_id');$t->dropConstrainedForeignId('cost_center_id');});
        Schema::table('expenses', function(Blueprint $t){$t->dropConstrainedForeignId('project_id');$t->dropConstrainedForeignId('cost_center_id');});
        Schema::table('purchases', fn(Blueprint $t)=>$t->dropColumn(['currency_code','exchange_rate']));
        Schema::table('sales', fn(Blueprint $t)=>$t->dropColumn(['currency_code','exchange_rate']));
        Schema::table('products', fn(Blueprint $t)=>$t->dropColumn('track_serial'));
        foreach(['fixed_asset_depreciations','fixed_assets','budgets','projects','cost_centers','document_attachments','exchange_rates','currencies','product_serials','inventory_reservations','sales_order_items','purchase_order_items','sales_orders','purchase_orders','approval_requests','tax_rates','bank_reconciliation_items','bank_reconciliations','accounting_periods'] as $x) Schema::dropIfExists($x);
        $pids=DB::table('permissions')->whereIn('name',$this->permissions)->pluck('id'); if($pids->isNotEmpty()){DB::table('role_has_permissions')->whereIn('permission_id',$pids)->delete();DB::table('model_has_permissions')->whereIn('permission_id',$pids)->delete();DB::table('permissions')->whereIn('id',$pids)->delete();}
    }
};
