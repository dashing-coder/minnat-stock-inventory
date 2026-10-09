<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['company_name', 'M/S Afrin Traders', 'general', 'string', true],
            ['company_phone', '01932-223333, 01970-479940, 01916-223333', 'general', 'string', true],
            ['company_email', '', 'general', 'string', true],
            ['company_address', 'Abdul Molla Super Market, Baluchar Bazar (under Dutch-Bangla Bank), Sirajdikhan, Munshiganj', 'general', 'string', true],
            ['currency', 'BDT', 'general', 'string', true],
            ['date_format', 'Y-m-d', 'general', 'string', true],
            ['timezone', 'Asia/Dhaka', 'general', 'string', false],

            ['company_name_bn', 'মেসার্স আফরিন ট্রেডার্স', 'memo', 'string', true],
            ['company_owner', 'Shamsul Molla', 'memo', 'string', true],
            ['company_owner_bn', 'শামছুল মোল্লা', 'memo', 'string', true],
            ['company_address_bn', 'আব্দুল মোল্লা সুপার মার্কেট, বালুচর বাজার, (ডাচ বাংলা ব্যাংকের নিচে) সিরাজদিখান, মুন্সীগঞ্জ', 'memo', 'string', true],
            ['memo_business_description_en', 'Dealer and retailer of TMT rod, angle, flat bar, deformed bar, binding wire, chemical, bitumen, cement and construction materials.', 'memo', 'string', true],
            ['memo_business_description_bn', 'TMT 400/500/600 গ্রেডের রড, এঙ্গেল, ফ্লাটবার, ডিফর্ম বার, বাইন্ডিং তার, কেমিকেল, বিটুমিন, সিমেন্ট ও নির্মাণ সামগ্রীর পাইকারী ও খুচরা বিক্রেতা।', 'memo', 'string', true],
            ['memo_footer_en', 'Digital weighing is available here.', 'memo', 'string', true],
            ['memo_footer_bn', 'এখানে ডিজিটাল পদ্ধতিতে ওজন দেওয়া হয়।', 'memo', 'string', true],

            ['invoice_prefix', 'SAL', 'document', 'string', false],
            ['purchase_prefix', 'PUR', 'document', 'string', false],
            ['allow_negative_stock', '0', 'inventory', 'boolean', false],
            ['low_stock_notifications', '1', 'inventory', 'boolean', false],
        ];

        foreach ($rows as [$key, $value, $group, $type, $public]) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'type' => $type, 'is_public' => $public]
            );
        }
    }
}
