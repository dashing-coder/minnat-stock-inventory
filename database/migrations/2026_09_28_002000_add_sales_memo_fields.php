<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sales', 'labor_charge')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->decimal('labor_charge', 18, 2)->default(0)->after('shipping');
            });
        }

        if (! Schema::hasColumn('sale_items', 'bundle_quantity')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->decimal('bundle_quantity', 18, 4)->nullable()->after('quantity');
            });
        }

        $now = now();
        $settings = [
            ['key' => 'company_name_bn', 'value' => 'মেসার্স আফরিন ট্রেডার্স', 'group' => 'memo', 'type' => 'string', 'is_public' => true],
            ['key' => 'company_owner', 'value' => 'Shamsul Molla', 'group' => 'memo', 'type' => 'string', 'is_public' => true],
            ['key' => 'company_owner_bn', 'value' => 'শামছুল মোল্লা', 'group' => 'memo', 'type' => 'string', 'is_public' => true],
            ['key' => 'company_phone', 'value' => '01932-223333, 01970-479940, 01916-223333', 'group' => 'general', 'type' => 'string', 'is_public' => true],
            ['key' => 'company_address_bn', 'value' => 'আব্দুল মোল্লা সুপার মার্কেট, বালুচর বাজার, (ডাচ বাংলা ব্যাংকের নিচে) সিরাজদিখান, মুন্সীগঞ্জ', 'group' => 'memo', 'type' => 'string', 'is_public' => true],
            ['key' => 'memo_business_description_en', 'value' => 'Dealer and retailer of TMT rod, angle, flat bar, deformed bar, binding wire, chemical, bitumen, cement and construction materials.', 'group' => 'memo', 'type' => 'string', 'is_public' => true],
            ['key' => 'memo_business_description_bn', 'value' => 'TMT 400/500/600 গ্রেডের রড, এঙ্গেল, ফ্লাটবার, ডিফর্ম বার, বাইন্ডিং তার, কেমিকেল, বিটুমিন, সিমেন্ট ও নির্মাণ সামগ্রীর পাইকারী ও খুচরা বিক্রেতা।', 'group' => 'memo', 'type' => 'string', 'is_public' => true],
            ['key' => 'memo_footer_en', 'value' => 'Digital weighing is available here.', 'group' => 'memo', 'type' => 'string', 'is_public' => true],
            ['key' => 'memo_footer_bn', 'value' => 'এখানে ডিজিটাল পদ্ধতিতে ওজন দেওয়া হয়।', 'group' => 'memo', 'type' => 'string', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            if (! $exists) {
                DB::table('settings')->insert($setting + ['created_at' => $now, 'updated_at' => $now]);
            }
        }

        // Preserve genuine admin customizations, but replace blank/legacy demo values
        // so a newly deployed memo prints the real Afrin Traders identity by default.
        DB::table('settings')
            ->where('key', 'company_name')
            ->where(function ($query) {
                $query->whereNull('value')
                    ->orWhere('value', '')
                    ->orWhereIn('value', ['Laravel ERP', 'Afrin Traders']);
            })
            ->update(['value' => 'M/S Afrin Traders', 'updated_at' => $now]);

        DB::table('settings')
            ->where('key', 'company_phone')
            ->where(function ($query) {
                $query->whereNull('value')->orWhere('value', '');
            })
            ->update([
                'value' => '01932-223333, 01970-479940, 01916-223333',
                'updated_at' => $now,
            ]);

        DB::table('settings')
            ->where('key', 'company_address')
            ->where(function ($query) {
                $query->whereNull('value')->orWhere('value', '');
            })
            ->update([
                'value' => 'Abdul Molla Super Market, Baluchar Bazar (under Dutch-Bangla Bank), Sirajdikhan, Munshiganj',
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'company_name_bn',
            'company_owner',
            'company_owner_bn',
            'company_address_bn',
            'memo_business_description_en',
            'memo_business_description_bn',
            'memo_footer_en',
            'memo_footer_bn',
        ])->delete();

        if (Schema::hasColumn('sale_items', 'bundle_quantity')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropColumn('bundle_quantity');
            });
        }

        if (Schema::hasColumn('sales', 'labor_charge')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('labor_charge');
            });
        }
    }
};
