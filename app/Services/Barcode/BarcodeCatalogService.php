<?php

namespace App\Services\Barcode;

use App\Models\Product;
use InvalidArgumentException;

/**
 * Scan-code policy shared by product entry, variants, label printing and audit.
 * The POS uses case-insensitive barcode/SKU/product-code aliases for each product.
 */
class BarcodeCatalogService
{
    /** Matches the POS keyboard-wedge scanner's normalization rules. */
    public function scanKey(?string $raw): string
    {
        $key = strtoupper(trim((string) $raw));
        if (strlen($key) > 2 && str_starts_with($key, '*') && str_ends_with($key, '*')) {
            $key = substr($key, 1, -1); // Code 39 start/stop wrappers, not business data.
        }
        return $key;
    }

    public function aliases(Product $product): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn ($value) => $this->scanKey($value),
            [$product->barcode, $product->sku, $product->code]
        ), fn ($value) => $value !== '')));
    }

    /**
     * Detect cross-COLUMN collisions, including case/whitespace differences.
     * Existing UNIQUE constraints protect within each column; they do not prevent
     * product A's barcode equalling product B's SKU or generated product code.
     */
    public function firstCollision(string $raw, ?int $excludeId = null): ?Product
    {
        $key = $this->scanKey($raw);
        if ($key === '') {
            return null;
        }
        // Backticks work on supported MySQL/MariaDB and SQLite installations.
        $lookup = [$key, '*'.$key.'*'];
        return Product::query()->when($excludeId !== null, fn ($query) => $query->whereKeyNot($excludeId))
            ->where(function ($query) use ($lookup) {
                foreach (['barcode', 'sku', 'code'] as $column) {
                    $query->orWhereRaw("UPPER(TRIM(`{$column}`)) IN (?, ?)", $lookup);
                }
            })->first(['id','name','sku','barcode','code']);
    }

    /** Unique per product across every scan alias. Does not modify historical data. */
    public function checkInput(array $data, ?Product $current = null): array
    {
        $errors = [];
        foreach (['sku', 'barcode'] as $column) {
            $raw = trim((string) ($data[$column] ?? ''));
            if ($raw === '') {
                continue;
            }
            $key = $this->scanKey($raw);
            if (preg_match('/[\x00-\x1F\x7F]/', $raw)) {
                $errors[$column] = 'Scanner identifiers must not contain control characters.';
                continue;
            }
            // Generated PRD-000001 aliases are reserved to avoid future ID collisions.
            if (preg_match('/^PRD-[0-9]{6,}$/', $key) && $key !== $this->scanKey($current?->code)) {
                $errors[$column] = 'This identifier is reserved for generated product codes. Use a different SKU or barcode.';
                continue;
            }
            $other = $this->firstCollision($raw, $current?->id);
            if ($other) {
                $errors[$column] = "Scanner identifier already belongs to product #{$other->id} ({$other->name}). SKU, barcode and product codes must not overlap across products.";
            }
        }
        return $errors;
    }

    /**
     * Barcode takes priority. A non-Code39 SKU can still be represented by the
     * generated PRD code, and that exact value is exposed on the preview page.
     * We NEVER silently replace an explicitly configured, invalid barcode.
     */
    public function printableCode(Product $product, Code39Barcode $generator): array
    {
        if (trim((string) $product->barcode) !== '') {
            return ['code' => $generator->normalize((string) $product->barcode), 'source' => 'Barcode'];
        }
        if (trim((string) $product->sku) !== '') {
            try {
                return ['code' => $generator->normalize((string) $product->sku), 'source' => 'SKU'];
            } catch (InvalidArgumentException) {
                // An otherwise valid SKU may contain characters not in Code 39.
            }
        }
        return ['code' => $generator->normalize((string) $product->code), 'source' => 'Product code'];
    }
}
