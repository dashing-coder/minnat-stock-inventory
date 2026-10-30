<?php

namespace App\Services\Commercial;

use App\Models\ProductDelivery;

/** Read-only physical delivery reconciliation against posted source sales and purchases. */
class DeliveryChallanAuditService
{
    public function run(): array
    {
        $issues = [];
        $counts = ['delivery_records' => 0, 'drafts' => 0, 'posted' => 0, 'posted_items' => 0];
        $postedBySourceLine = [];
        $sources = [];
        $report = function (string $code, ProductDelivery $delivery, ?int $itemId = null) use (&$issues): void {
            if (count($issues) < 100) {
                $issues[] = [
                    'issue' => $code,
                    'delivery_id' => $delivery->id,
                    'delivery_no' => $delivery->delivery_no,
                    'source_item_id' => $itemId,
                ];
            }
        };

        ProductDelivery::query()
            ->with(['items', 'sale.items', 'purchase.items'])
            ->orderBy('id')->chunkById(200, function ($deliveries) use (&$counts, &$postedBySourceLine, &$sources, $report) {
                foreach ($deliveries as $delivery) {
                    $counts['delivery_records']++;
                    if ($delivery->status === 'draft') {
                        $counts['drafts']++;
                        continue; // Never count a draft as physically delivered.
                    }
                    if ($delivery->status !== 'posted') {
                        $report('unknown_status', $delivery);
                        continue;
                    }
                    $counts['posted']++;
                    $isSale = $delivery->delivery_type === 'sale';
                    if (!in_array($delivery->delivery_type, ['sale', 'purchase'], true)) {
                        $report('invalid_type', $delivery);
                        continue;
                    }
                    $source = $isSale ? $delivery->sale : $delivery->purchase;
                    if (!$source || $source->status !== 'posted') {
                        $report('missing_or_unposted_source', $delivery);
                        continue;
                    }
                    if ((int)$source->warehouse_id !== (int)$delivery->warehouse_id) {
                        $report('warehouse_mismatch', $delivery);
                    }
                    $sourceKey = $delivery->delivery_type . ':' . $source->id;
                    $sources[$sourceKey] = $source;
                    if ($delivery->items->isEmpty()) {
                        $report('posted_without_items', $delivery);
                    }
                    $sourceItems = $source->items->keyBy('id');
                    foreach ($delivery->items as $item) {
                        $counts['posted_items']++;
                        $original = $sourceItems->get($item->source_item_id);
                        if (!$original || (int)$original->product_id !== (int)$item->product_id) {
                            $report('source_line_or_product_mismatch', $delivery, (int)$item->source_item_id);
                            continue;
                        }
                        if ((float)$item->quantity <= 0 || round((float)$item->quantity, 4) !== (float)$item->quantity) {
                            $report('invalid_quantity', $delivery, (int)$item->source_item_id);
                            continue;
                        }
                        $key = $sourceKey . ':' . $original->id;
                        $postedBySourceLine[$key] = ($postedBySourceLine[$key] ?? 0) + (float)$item->quantity;
                    }
                }
            });

        // Every source is compared once, even if its posted deliveries were created in separate batches.
        foreach ($sources as $sourceKey => $source) {
            foreach ($source->items as $item) {
                $key = $sourceKey . ':' . $item->id;
                $posted = round((float)($postedBySourceLine[$key] ?? 0), 4);
                $allowed = round((float)$item->quantity + (float)($item->free_quantity ?? 0), 4);
                if ($posted > $allowed) {
                    if (count($issues) < 100) {
                        $issues[] = ['issue' => 'delivered_exceeds_ordered', 'source' => $sourceKey,
                            'source_item_id' => $item->id, 'posted_quantity' => $posted, 'allowed_quantity' => $allowed];
                    }
                }
            }
        }
        return ['pass' => $issues === [], 'counts' => $counts, 'issues_sample' => $issues,
            'note' => 'Drafts do not count as delivered. Results are read-only; max 100 examples.'];
    }
}
