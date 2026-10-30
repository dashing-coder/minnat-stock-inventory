<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $newPermissions = [
        'client_setup.view',
        'reorder_setup.view','reorder_setup.edit',
        'business_days.view','business_days.manage',
        'user_modules.view','user_modules.edit',
        'market_hierarchy.view','market_hierarchy.create','market_hierarchy.edit','market_hierarchy.delete',
        'barcode_print.view',
    ];

    public function up(): void
    {
        Schema::create('business_days', function (Blueprint $table) {
            $table->id();
            $table->date('business_date')->unique();
            $table->string('status', 20)->default('open');
            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'business_date']);
        });

        Schema::create('market_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('market_hierarchies')->restrictOnDelete();
            $table->string('name', 180);
            $table->string('code', 60)->nullable()->unique();
            $table->string('level_type', 30)->default('market');
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['parent_id', 'sort_order']);
            $table->index(['level_type', 'is_active']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('market_hierarchy_id')->nullable()->after('customer_group_id')
                ->constrained('market_hierarchies')->nullOnDelete();
        });

        // Preserve current operation after deployment. Administrators can close/open from Settings.
        DB::table('business_days')->insert([
            'business_date' => now()->toDateString(),
            'status' => 'open',
            'opened_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $now = now();
        foreach ($this->newPermissions as $name) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permissionIds = DB::table('permissions')->whereIn('name', $this->newPermissions)->pluck('id');
        $roleIds = DB::table('roles')->whereIn('name', ['Super Admin','Admin'])->pluck('id');
        foreach ($roleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('market_hierarchy_id');
        });
        Schema::dropIfExists('market_hierarchies');
        Schema::dropIfExists('business_days');

        $permissionIds = DB::table('permissions')->whereIn('name', $this->newPermissions)->pluck('id');
        if ($permissionIds->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }
    }
};
