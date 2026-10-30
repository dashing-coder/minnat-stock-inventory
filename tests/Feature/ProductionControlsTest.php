<?php
namespace Tests\Feature;

use App\Models\{Account,Customer,Product,Unit,User,Warehouse,WarehouseStock};
use App\Services\ApprovalService;
use App\Services\Commercial\SaleService;
use App\Services\PeriodLockService;
use Database\Seeders\{AccountSeeder,RolePermissionSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductionControlsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AccountSeeder::class);
        DB::table('settings')->insertOrIgnore([
            ['key'=>'maker_checker_enabled','value'=>'0','group'=>'controls','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'credit_limit_enforced','value'=>'1','group'=>'controls','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'auto_product_tax','value'=>'1','group'=>'controls','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    public function test_closed_accounting_period_blocks_posting_date(): void
    {
        DB::table('accounting_periods')->insert(['name'=>'Oct','start_date'=>'2026-10-01','end_date'=>'2026-10-31','status'=>'closed','created_at'=>now(),'updated_at'=>now()]);
        $this->expectException(ValidationException::class);
        app(PeriodLockService::class)->assertOpen('2026-10-15');
    }

    public function test_inventory_reservation_reduces_available_quantity(): void
    {
        [$warehouse,$product]=$this->stockFixture(10);
        $user=User::factory()->create();$user->assignRole('Admin');
        $this->actingAs($user)->post(route('production-controls.reservations.store'),[
            'warehouse_id'=>$warehouse->id,'product_id'=>$product->id,'quantity'=>4,
        ])->assertSessionHasNoErrors();
        $stock=WarehouseStock::where('warehouse_id',$warehouse->id)->where('product_id',$product->id)->firstOrFail();
        $this->assertSame(4.0,(float)$stock->reserved_quantity);
        $this->assertSame(6.0,$stock->available_quantity);
    }

    public function test_credit_limit_is_enforced_when_sale_is_posted(): void
    {
        [$warehouse,$product]=$this->stockFixture(10);
        $user=User::factory()->create();$this->actingAs($user);
        $customer=Customer::create(['name'=>'Credit Client','opening_balance'=>0,'opening_balance_type'=>'receivable','credit_limit'=>100,'is_active'=>1]);
        $sale=app(SaleService::class)->create([
            'customer_id'=>$customer->id,'warehouse_id'=>$warehouse->id,'sale_date'=>now()->toDateString(),'currency_code'=>'BDT','exchange_rate'=>1,
            'discount'=>0,'tax'=>0,'shipping'=>0,'labor_charge'=>0,
            'items'=>[['product_id'=>$product->id,'quantity'=>1,'unit_price'=>150,'discount_type'=>'fixed','discount_value'=>0,'tax'=>0]],
        ]);
        $this->expectException(ValidationException::class);
        app(SaleService::class)->post($sale);
    }

    public function test_maker_cannot_approve_own_request(): void
    {
        $maker=User::factory()->create();$maker->assignRole('Admin');$this->actingAs($maker);
        DB::table('settings')->where('key','maker_checker_enabled')->update(['value'=>'1']);
        DB::table('approval_requests')->insert(['approvable_type'=>'sale','approvable_id'=>1,'reference_no'=>'SAL-1','status'=>'pending','requested_by'=>$maker->id,'created_at'=>now(),'updated_at'=>now()]);
        $row=DB::table('approval_requests')->first();
        $this->post(route('production-controls.approvals.decide',$row->id),['decision'=>'approved'])->assertSessionHasErrors('approval');
    }

    public function test_sales_order_reserves_stock_and_can_convert_to_draft_sale(): void
    {
        [$warehouse,$product]=$this->stockFixture(10);
        $admin=User::factory()->create();$admin->assignRole('Admin');
        $this->actingAs($admin)->post(route('production-controls.orders.store','sales'),[
            'warehouse_id'=>$warehouse->id,'order_date'=>now()->toDateString(),'currency_code'=>'BDT','exchange_rate'=>1,
            'items'=>[['product_id'=>$product->id,'quantity'=>3,'unit_price'=>100]],
        ])->assertSessionHasNoErrors();
        $order=DB::table('sales_orders')->first();
        $this->assertSame(3.0,(float)WarehouseStock::where('warehouse_id',$warehouse->id)->where('product_id',$product->id)->value('reserved_quantity'));
        $this->actingAs($admin)->post(route('production-controls.orders.convert',['sales',$order->id]))->assertRedirect();
        $this->assertSame('converted',DB::table('sales_orders')->where('id',$order->id)->value('status'));
    }

    private function stockFixture(float $qty): array
    {
        $unit=Unit::create(['name'=>'Piece-'.uniqid(),'short_name'=>'PCS','is_active'=>1]);
        $warehouse=Warehouse::create(['code'=>'W'.uniqid(),'name'=>'Main','is_active'=>1]);
        $product=Product::create(['sku'=>'SKU'.uniqid(),'name'=>'Product','unit_id'=>$unit->id,'purchase_price'=>50,'selling_price'=>100,'track_stock'=>1,'is_active'=>1]);
        WarehouseStock::create(['warehouse_id'=>$warehouse->id,'product_id'=>$product->id,'quantity'=>$qty,'reserved_quantity'=>0]);
        return [$warehouse,$product];
    }
}
