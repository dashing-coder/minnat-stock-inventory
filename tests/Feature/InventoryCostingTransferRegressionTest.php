<?php

namespace Tests\Feature;

use App\Models\InventoryBatch;
use App\Models\InventoryCostLayer;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Inventory\InventoryAuditService;
use App\Services\Inventory\InventoryCostingService;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryCostingTransferRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_fifo_transfer_preserves_receipt_layers_and_movement_quantities(): void
    {
        [$from, $to, $product] = $this->fixture('fifo');
        $this->receive($from, $product, 10, 100);
        $this->receive($from, $product, 10, 200);
        $transfer = $this->transfer($from, $to, $product, 15);

        app(StockTransferService::class)->post($transfer);

        $this->assertSame('posted', $transfer->fresh()->status);
        $this->assertEqualsWithDelta(5, $this->stock($from, $product), 0.00001);
        $this->assertEqualsWithDelta(15, $this->stock($to, $product), 0.00001);
        $fragments = InventoryCostLayer::where('warehouse_id', $to->id)->orderBy('id')->get();
        $this->assertCount(2, $fragments);
        $this->assertEqualsWithDelta(10, (float) $fragments[0]->quantity_remaining, 0.00001);
        $this->assertEqualsWithDelta(100, (float) $fragments[0]->unit_cost, 0.00001);
        $this->assertEqualsWithDelta(5, (float) $fragments[1]->quantity_remaining, 0.00001);
        $this->assertEqualsWithDelta(200, (float) $fragments[1]->unit_cost, 0.00001);

        $out = StockMovement::where('source_type', 'stock_transfer')->where('source_id', $transfer->id)->where('warehouse_id', $from->id)->sum('quantity_out');
        $in = StockMovement::where('source_type', 'stock_transfer')->where('source_id', $transfer->id)->where('warehouse_id', $to->id)->sum('quantity_in');
        $this->assertEqualsWithDelta(15, (float) $out, 0.00001);
        $this->assertEqualsWithDelta(15, (float) $in, 0.00001);
    }

    public function test_average_cost_in_destination_includes_every_transfer_fragment(): void
    {
        [$from, $to, $product] = $this->fixture('average');
        $this->receive($from, $product, 10, 100);
        $this->receive($from, $product, 10, 200);
        $this->receive($to, $product, 10, 300);
        app(StockTransferService::class)->post($this->transfer($from, $to, $product, 15));

        // Destination: (10 * 300 + 15 * 150) / 25 = 210.
        $this->assertEqualsWithDelta(210, (float) WarehouseStock::where('warehouse_id', $to->id)->where('product_id', $product->id)->value('average_cost'), 0.0001);
    }

    public function test_batch_transfer_keeps_batch_numbers_and_expiry_dates(): void
    {
        [$from, $to, $product] = $this->fixture('fifo', true);
        $expiry1 = now()->addMonths(4)->toDateString();
        $expiry2 = now()->addMonths(9)->toDateString();
        $this->receive($from, $product, 5, 100, 'LOT-1', $expiry1);
        $this->receive($from, $product, 5, 200, 'LOT-2', $expiry2);
        app(StockTransferService::class)->post($this->transfer($from, $to, $product, 7));

        $this->assertEqualsWithDelta(5, (float) InventoryBatch::where('warehouse_id', $to->id)->where('batch_no', 'LOT-1')->value('quantity'), 0.0001);
        $this->assertEqualsWithDelta(2, (float) InventoryBatch::where('warehouse_id', $to->id)->where('batch_no', 'LOT-2')->value('quantity'), 0.0001);
        $this->assertSame($expiry1, InventoryBatch::where('warehouse_id', $to->id)->where('batch_no', 'LOT-1')->firstOrFail()->expires_at->toDateString());
        $this->assertSame($expiry2, InventoryBatch::where('warehouse_id', $to->id)->where('batch_no', 'LOT-2')->firstOrFail()->expires_at->toDateString());
        $this->assertEqualsWithDelta(3, $this->stock($from, $product), 0.0001);
        $this->assertEqualsWithDelta(7, $this->stock($to, $product), 0.0001);
    }

    public function test_expired_batch_cannot_be_transferred_and_transaction_rolls_back(): void
    {
        [$from, $to, $product] = $this->fixture('fifo', true);
        $this->receive($from, $product, 5, 100, 'EXP-1', now()->subDay()->toDateString());
        $transfer = $this->transfer($from, $to, $product, 3);
        try {
            app(StockTransferService::class)->post($transfer);
            $this->fail('An expired-only batch transfer must not post.');
        } catch (ValidationException $e) {
            $this->assertEqualsWithDelta(5, $this->stock($from, $product), 0.0001);
            $this->assertEqualsWithDelta(0, $this->stock($to, $product), 0.0001);
            $this->assertSame('draft', $transfer->fresh()->status);
        }
    }

    public function test_read_only_audit_reports_stock_movement_disagreement(): void
    {
        [$from, , $product] = $this->fixture('fifo');
        $this->receive($from, $product, 10, 100);
        WarehouseStock::where('warehouse_id', $from->id)->where('product_id', $product->id)->update(['quantity' => 8]);

        $audit = app(InventoryAuditService::class)->run();
        $this->assertFalse($audit['checks']['warehouse_matches_stock_movements']);
        $this->assertGreaterThan(0, $audit['values']['mismatch_counts']['movement_mismatch']);
    }

    private function fixture(string $method, bool $batch = false): array
    {
        $unit = Unit::create(['name' => 'Piece', 'short_name' => 'PCS', 'is_active' => true]);
        $from = Warehouse::create(['code' => 'WH-A', 'name' => 'A', 'is_active' => true]);
        $to = Warehouse::create(['code' => 'WH-B', 'name' => 'B', 'is_active' => true]);
        $product = Product::create([
            'code' => 'ITEM-1', 'sku' => 'SKU-1', 'name' => 'Inventory Item',
            'unit_id' => $unit->id, 'purchase_price' => 100, 'selling_price' => 150,
            'track_stock' => true, 'track_batch' => $batch, 'track_expiry' => $batch,
            'costing_method' => $method, 'allow_negative_stock' => false, 'is_active' => true,
        ]);
        return [$from, $to, $product];
    }

    private function receive(Warehouse $warehouse, Product $product, float $qty, float $price, ?string $batchNo = null, ?string $expiry = null): void
    {
        app(InventoryCostingService::class)->receive($warehouse, $product, $qty, $price, now()->toDateString(), 'test', null, $batchNo, null, $expiry);
        app(InventoryService::class)->stockIn($warehouse, $product, $qty, 'opening', null, null, null, $price);
    }

    private function transfer(Warehouse $from, Warehouse $to, Product $product, float $qty): StockTransfer
    {
        $transfer = StockTransfer::create([
            'transfer_no' => 'T-' . uniqid(), 'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id, 'transfer_date' => now()->toDateString(), 'status' => 'draft',
        ]);
        $transfer->items()->create(['product_id' => $product->id, 'quantity' => $qty, 'unit_cost' => 0]);
        return $transfer;
    }

    private function stock(Warehouse $warehouse, Product $product): float
    {
        return (float) (WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->value('quantity') ?? 0);
    }
}
