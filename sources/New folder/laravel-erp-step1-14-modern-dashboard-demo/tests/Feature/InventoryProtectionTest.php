<?php
namespace Tests\Feature;
use App\Models\Product;use App\Models\Unit;use App\Models\Warehouse;use App\Services\Inventory\InventoryService;use Illuminate\Foundation\Testing\RefreshDatabase;use RuntimeException;use Tests\TestCase;
class InventoryProtectionTest extends TestCase
{
    use RefreshDatabase;
    public function test_negative_stock_is_blocked(): void
    {
        $u=Unit::create(['name'=>'Piece','short_name'=>'PCS','is_active'=>1]);$w=Warehouse::create(['code'=>'W1','name'=>'Main','is_active'=>1]);$p=Product::create(['code'=>'P1','sku'=>'SKU1','name'=>'Test','unit_id'=>$u->id,'purchase_price'=>1,'selling_price'=>2,'track_stock'=>1,'allow_negative_stock'=>0,'is_active'=>1]);
        $this->expectException(RuntimeException::class);app(InventoryService::class)->stockOut($w,$p,1,'sale');
    }
}
