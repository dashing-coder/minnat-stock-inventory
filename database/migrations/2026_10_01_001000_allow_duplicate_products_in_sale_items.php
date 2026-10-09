<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            $uniqueIndex = $this->mysqlUniqueSaleProductIndex();

            if ($uniqueIndex !== null) {
                DB::statement(sprintf(
                    'ALTER TABLE `sale_items` DROP INDEX `%s`',
                    str_replace('`', '``', $uniqueIndex)
                ));
            }

            if (! $this->mysqlIndexExists('sale_items_sale_id_product_id_index')) {
                DB::statement(
                    'ALTER TABLE `sale_items` ADD INDEX `sale_items_sale_id_product_id_index` (`sale_id`, `product_id`)'
                );
            }

            return;
        }

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropUnique(['sale_id', 'product_id']);
            $table->index(['sale_id', 'product_id'], 'sale_items_sale_id_product_id_index');
        });
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            if ($this->mysqlIndexExists('sale_items_sale_id_product_id_index')) {
                DB::statement(
                    'ALTER TABLE `sale_items` DROP INDEX `sale_items_sale_id_product_id_index`'
                );
            }

            if ($this->mysqlUniqueSaleProductIndex() === null) {
                DB::statement(
                    'ALTER TABLE `sale_items` ADD UNIQUE INDEX `sale_items_sale_id_product_id_unique` (`sale_id`, `product_id`)'
                );
            }

            return;
        }

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex('sale_items_sale_id_product_id_index');
            $table->unique(['sale_id', 'product_id']);
        });
    }

    private function mysqlUniqueSaleProductIndex(): ?string
    {
        $indexes = DB::select('SHOW INDEX FROM `sale_items`');
        $grouped = [];

        foreach ($indexes as $index) {
            $name = (string) ($index->Key_name ?? $index->key_name ?? '');
            $column = (string) ($index->Column_name ?? $index->column_name ?? '');
            $sequence = (int) ($index->Seq_in_index ?? $index->seq_in_index ?? 0);
            $nonUnique = (int) ($index->Non_unique ?? $index->non_unique ?? 1);

            if ($name === '' || $name === 'PRIMARY') {
                continue;
            }

            $grouped[$name]['non_unique'] = $nonUnique;
            $grouped[$name]['columns'][$sequence] = $column;
        }

        foreach ($grouped as $name => $definition) {
            if (($definition['non_unique'] ?? 1) !== 0) {
                continue;
            }

            $columns = $definition['columns'] ?? [];
            ksort($columns);

            if (array_values($columns) === ['sale_id', 'product_id']) {
                return $name;
            }
        }

        return null;
    }

    private function mysqlIndexExists(string $indexName): bool
    {
        foreach (DB::select('SHOW INDEX FROM `sale_items`') as $index) {
            $name = (string) ($index->Key_name ?? $index->key_name ?? '');

            if ($name === $indexName) {
                return true;
            }
        }

        return false;
    }
};
