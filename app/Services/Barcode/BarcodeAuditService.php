<?php

namespace App\Services\Barcode;

use App\Models\Product;
use InvalidArgumentException;

/** Read-only checks: no migrations, writes, stock movement, or data repair. */
class BarcodeAuditService
{
    public function run(): array
    {
        $issues = [
            'ambiguous_active_scan_identifiers' => [],
            'invalid_explicit_code39_barcodes' => [],
            'missing_printable_scan_value' => [],
        ];
        $advisories = ['requires_wider_than_60mm_label' => [], 'products_without_stock_tracking' => 0];
        $codes = [];
        $seen = 0;
        $generator = new Code39Barcode();
        $catalog = new BarcodeCatalogService();

        foreach (Product::query()->where('is_active', true)->orderBy('id')->cursor() as $product) {
            if (! $product->track_stock) {
                $advisories['products_without_stock_tracking']++;
                continue; // POS and barcode printing exclude non-stock products.
            }
            $seen++;
            foreach ($catalog->aliases($product) as $alias) {
                $codes[$alias][(int)$product->id] = true;
            }
            try {
                if (trim((string)$product->barcode) !== '') {
                    $generator->normalize((string)$product->barcode);
                }
                $printable = $catalog->printableCode($product, $generator);
                $requiredWidth = $generator->minimumLabelWidthMm($printable['code']);
                if ($requiredWidth > 60) {
                    $advisories['requires_wider_than_60mm_label'][] = [
                        'product_id' => $product->id, 'code' => $printable['code'],
                        'minimum_width_mm' => $requiredWidth,
                    ];
                }
            } catch (InvalidArgumentException $exception) {
                $key = trim((string) $product->barcode) !== ''
                    ? 'invalid_explicit_code39_barcodes' : 'missing_printable_scan_value';
                $issues[$key][] = ['product_id' => $product->id, 'name' => $product->name, 'reason' => $exception->getMessage()];
            }
        }

        foreach ($codes as $code => $productIds) {
            if (count($productIds) > 1) {
                $issues['ambiguous_active_scan_identifiers'][] = [
                    'code' => $code, 'product_ids' => array_keys($productIds),
                ];
            }
        }
        $counts = array_map('count', $issues);
        return [
            'pass' => array_sum($counts) === 0,
            'checked_active_stock_products' => $seen,
            'issues' => $counts,
            'issue_examples' => array_map(fn ($records) => array_slice($records, 0, 25), $issues),
            'advisories' => [
                'requires_wider_than_60mm_label' => count($advisories['requires_wider_than_60mm_label']),
                'examples' => array_slice($advisories['requires_wider_than_60mm_label'], 0, 25),
                'products_without_stock_tracking' => $advisories['products_without_stock_tracking'],
            ],
            'note' => 'Read-only active-catalogue check. Passing does not certify a physical scanner/printer, historical POS postings, or EAN/UPC check-digit validity.',
        ];
    }
}
