<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('parent_product_id')->nullable()->after('id')->constrained('products')->nullOnDelete();
            $table->string('variant_name', 180)->nullable()->after('name');
            $table->json('variant_attributes')->nullable()->after('variant_name');
            $table->boolean('track_batch')->default(false)->after('track_stock');
            $table->boolean('track_expiry')->default(false)->after('track_batch');
            $table->string('costing_method', 20)->default('average')->after('track_expiry');
            $table->index(['parent_product_id', 'is_active']);
        });

        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->decimal('average_cost', 18, 4)->default(0)->after('reserved_quantity');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            $uniqueIndex = $this->mysqlUniquePurchaseProductIndex();
            if ($uniqueIndex !== null) {
                DB::statement(sprintf(
                    'ALTER TABLE `purchase_items` DROP INDEX `%s`',
                    str_replace('`', '``', $uniqueIndex)
                ));
            }
            if (! $this->mysqlPurchaseIndexExists('purchase_items_purchase_product_index')) {
                DB::statement('ALTER TABLE `purchase_items` ADD INDEX `purchase_items_purchase_product_index` (`purchase_id`, `product_id`)');
            }
        } else {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->dropUnique(['purchase_id', 'product_id']);
                $table->index(['purchase_id','product_id'], 'purchase_items_purchase_product_index');
            });
        }

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('free_quantity', 18, 4)->default(0)->after('quantity');
            $table->string('batch_no', 100)->nullable()->after('free_quantity');
            $table->date('manufactured_at')->nullable()->after('batch_no');
            $table->date('expiry_date')->nullable()->after('manufactured_at');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('free_quantity', 18, 4)->default(0)->after('quantity');
        });

        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('batch_no', 100);
            $table->date('manufactured_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->decimal('quantity', 18, 4)->default(0);
            $table->decimal('unit_cost', 18, 4)->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->unique(['warehouse_id', 'product_id', 'batch_no'], 'inv_batch_unique');
            $table->index(['expires_at', 'status']);
        });

        Schema::create('inventory_cost_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('inventory_batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->string('source_type', 50)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->date('transaction_date');
            $table->decimal('original_quantity', 18, 4);
            $table->decimal('quantity_remaining', 18, 4);
            $table->decimal('unit_cost', 18, 4);
            $table->timestamps();
            $table->index(['warehouse_id', 'product_id', 'transaction_date'], 'cost_layer_lookup');
            $table->index(['source_type', 'source_id']);
        });

        // Seed existing stock into the new valuation engine so upgrades begin with
        // a complete opening cost layer instead of treating legacy quantity as unknown.
        $legacyStocks = DB::table('warehouse_stocks')
            ->join('products','products.id','=','warehouse_stocks.product_id')
            ->where('warehouse_stocks.quantity','>',0)
            ->get([
                'warehouse_stocks.id AS stock_id',
                'warehouse_stocks.warehouse_id',
                'warehouse_stocks.product_id',
                'warehouse_stocks.quantity',
                'products.purchase_price',
            ]);

        foreach ($legacyStocks as $stock) {
            $unitCost=(float)$stock->purchase_price;
            DB::table('warehouse_stocks')->where('id',$stock->stock_id)->update(['average_cost'=>$unitCost]);
            DB::table('inventory_cost_layers')->insert([
                'warehouse_id'=>$stock->warehouse_id,
                'product_id'=>$stock->product_id,
                'inventory_batch_id'=>null,
                'source_type'=>'migration_opening',
                'source_id'=>$stock->stock_id,
                'transaction_date'=>now()->toDateString(),
                'original_quantity'=>$stock->quantity,
                'quantity_remaining'=>$stock->quantity,
                'unit_cost'=>$unitCost,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);
        }

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_no', 50)->unique();
            $table->string('direction', 20);
            $table->string('counterparty', 200);
            $table->string('phone', 50)->nullable();
            $table->decimal('principal', 18, 2);
            $table->string('method', 30)->default('cash');
            $table->decimal('interest_rate', 8, 4)->default(0);
            $table->unsignedInteger('installment_count')->default(1);
            $table->date('start_date');
            $table->date('due_date')->nullable();
            $table->decimal('outstanding_principal', 18, 2);
            $table->decimal('interest_paid', 18, 2)->default(0);
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['direction', 'status']);
            $table->index('due_date');
        });

        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('installment_no');
            $table->date('due_date');
            $table->decimal('principal_due',18,2)->default(0);
            $table->decimal('interest_due',18,2)->default(0);
            $table->decimal('principal_paid',18,2)->default(0);
            $table->decimal('interest_paid',18,2)->default(0);
            $table->string('status',20)->default('pending');
            $table->timestamps();
            $table->unique(['loan_id','installment_no']);
            $table->index(['due_date','status']);
        });

        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->string('payment_no', 50)->unique();
            $table->date('payment_date');
            $table->decimal('principal_amount', 18, 2)->default(0);
            $table->decimal('interest_amount', 18, 2)->default(0);
            $table->string('method', 30)->default('cash');
            $table->string('reference', 150)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['loan_id', 'payment_date']);
        });

        $now = now();
        foreach ([
            ['1300', 'Loan Receivable', 'asset', 'loan_receivable'],
            ['2200', 'Loan Payable', 'liability', 'loan_payable'],
            ['4110', 'Interest Income', 'income', 'interest'],
            ['6050', 'Interest Expense', 'expense', 'interest'],
        ] as [$code, $name, $type, $subtype]) {
            DB::table('accounts')->updateOrInsert(
                ['code' => $code],
                ['name' => $name, 'type' => $type, 'subtype' => $subtype, 'parent_id' => null, 'is_system' => 1, 'is_active' => 1, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
        Schema::dropIfExists('loan_installments');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('inventory_cost_layers');
        Schema::dropIfExists('inventory_batches');
        Schema::table('sale_items', fn (Blueprint $table) => $table->dropColumn('free_quantity'));
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['free_quantity','batch_no','manufactured_at','expiry_date']);
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            if ($this->mysqlPurchaseIndexExists('purchase_items_purchase_product_index')) {
                DB::statement('ALTER TABLE `purchase_items` DROP INDEX `purchase_items_purchase_product_index`');
            }
            if ($this->mysqlUniquePurchaseProductIndex() === null) {
                DB::statement('ALTER TABLE `purchase_items` ADD UNIQUE INDEX `purchase_items_purchase_id_product_id_unique` (`purchase_id`, `product_id`)');
            }
        } else {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->dropIndex('purchase_items_purchase_product_index');
                $table->unique(['purchase_id','product_id']);
            });
        }
        Schema::table('warehouse_stocks', fn (Blueprint $table) => $table->dropColumn('average_cost'));
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['parent_product_id']);
            $table->dropIndex(['parent_product_id','is_active']);
            $table->dropColumn(['parent_product_id','variant_name','variant_attributes','track_batch','track_expiry','costing_method']);
        });
    }

    private function mysqlUniquePurchaseProductIndex(): ?string
    {
        $indexes = DB::select('SHOW INDEX FROM `purchase_items`');
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
            if (array_values($columns) === ['purchase_id', 'product_id']) {
                return $name;
            }
        }

        return null;
    }

    private function mysqlPurchaseIndexExists(string $indexName): bool
    {
        foreach (DB::select('SHOW INDEX FROM `purchase_items`') as $index) {
            $name = (string) ($index->Key_name ?? $index->key_name ?? '');
            if ($name === $indexName) {
                return true;
            }
        }

        return false;
    }

};
