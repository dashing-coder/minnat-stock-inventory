<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Database\Seeders\{RolePermissionSeeder,AccountSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** Real database/HTTP assertions: run on XAMPP with the test DB, not production data. */
class PosCheckoutFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AccountSeeder::class);
        DB::table('settings')->insert([
            'key'=>'auto_product_tax','value'=>'1','group'=>'sales','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now(),
        ]);
    }

    public function test_checkout_posts_one_invoice_once_on_retry_with_same_token(): void
    {
        [$warehouse, $product] = $this->fixture(10);
        $admin = User::factory()->create(); $admin->assignRole('Admin');
        $payload = $this->payload($warehouse, $product, 2);

        $this->actingAs($admin)->post(route('pos.store'), $payload)->assertSessionHasNoErrors()->assertRedirect();
        $this->actingAs($admin)->post(route('pos.store'), $payload)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(1, Sale::count());
        $sale = Sale::firstOrFail();
        $this->assertSame('posted', $sale->status);
        $this->assertEqualsWithDelta(214, (float)$sale->paid_amount, 0.001);
        $this->assertEqualsWithDelta(214, (float)$sale->grand_total, 0.001); // 2x100 + 7% VAT
        $this->assertEqualsWithDelta(8, (float)WarehouseStock::where('product_id',$product->id)->value('quantity'), 0.0001);
        $this->assertSame(1, DB::table('pos_checkout_requests')->count());
        $this->assertSame(1, DB::table('stock_movements')->where('source_type','sale')->where('source_id',$sale->id)->count());
        $this->assertSame(1, DB::table('journal_entries')->where('reference_type','sale')->where('reference_id',$sale->id)->count());
    }

    public function test_insufficient_stock_rolls_back_checkout_and_creates_no_draft_or_journal(): void
    {
        [$warehouse, $product] = $this->fixture(1);
        $admin = User::factory()->create(); $admin->assignRole('Admin');
        $this->actingAs($admin)->post(route('pos.store'), $this->payload($warehouse, $product, 2))->assertSessionHasErrors();
        $this->assertSame(0, Sale::count());
        $this->assertSame(0, DB::table('pos_checkout_requests')->count());
        $this->assertEqualsWithDelta(1, (float)WarehouseStock::where('product_id',$product->id)->value('quantity'), 0.0001);
        $this->assertSame(0, DB::table('journal_entries')->where('reference_type','sale')->count());
    }

    public function test_reusing_token_with_different_basket_is_rejected(): void
    {
        [$warehouse, $product] = $this->fixture(10);
        $admin = User::factory()->create(); $admin->assignRole('Admin');
        $first = $this->payload($warehouse, $product, 1);
        $this->actingAs($admin)->post(route('pos.store'), $first)->assertSessionHasNoErrors();
        $first['items'][0]['quantity'] = 2;
        $this->actingAs($admin)->post(route('pos.store'), $first)->assertSessionHasErrors('checkout_token');
        $this->assertSame(1, Sale::count());
    }

    public function test_checkout_requires_posting_permission_not_just_create_permission(): void
    {
        [$warehouse, $product] = $this->fixture(10);
        $user = User::factory()->create();
        $user->givePermissionTo(['sales.create','sales.view']);
        $this->actingAs($user)->post(route('pos.store'), $this->payload($warehouse, $product, 1))->assertForbidden();
        $this->assertSame(0, Sale::count());
    }

    public function test_maker_checker_blocks_direct_pos_without_orphan_draft(): void
    {
        [$warehouse, $product] = $this->fixture(10);
        $admin = User::factory()->create(); $admin->assignRole('Admin');
        DB::table('settings')->insert([
            'key'=>'maker_checker_enabled','value'=>'1','group'=>'controls','type'=>'boolean','is_public'=>0,'created_at'=>now(),'updated_at'=>now(),
        ]);
        $this->actingAs($admin)->post(route('pos.store'), $this->payload($warehouse, $product, 1))->assertSessionHasErrors('approval');
        $this->assertSame(0, Sale::count());
    }

    private function fixture(float $stock): array
    {
        $unit = Unit::create(['name'=>'Piece','short_name'=>'PCS','is_active'=>true]);
        $warehouse = Warehouse::create(['code'=>'POS-TEST','name'=>'POS Test Warehouse','is_active'=>true]);
        $product = Product::create([
            'sku'=>'SKU-POS-TEST','barcode'=>'8900000000001','name'=>'POS Test Product','unit_id'=>$unit->id,
            'purchase_price'=>50,'selling_price'=>100,'tax_rate'=>7,'track_stock'=>true,
            'allow_negative_stock'=>false,'costing_method'=>'average','is_active'=>true,
        ]);
        WarehouseStock::create(['warehouse_id'=>$warehouse->id,'product_id'=>$product->id,
            'quantity'=>$stock,'reserved_quantity'=>0,'average_cost'=>50]);
        return [$warehouse,$product];
    }

    private function payload(Warehouse $warehouse, Product $product, float $qty): array
    {
        return [
            'checkout_token'=>(string)Str::uuid(),
            'warehouse_id'=>$warehouse->id,
            'items'=>[['product_id'=>$product->id,'quantity'=>$qty,'free_quantity'=>0,
                'unit_price'=>100,'price_type'=>'retail']],
        ];
    }
}
