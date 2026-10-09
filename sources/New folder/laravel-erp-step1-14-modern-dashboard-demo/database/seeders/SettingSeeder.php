<?php
namespace Database\Seeders;
use App\Models\Setting;use Illuminate\Database\Seeder;
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $rows=[
            ['company_name','Laravel ERP','general','string',true],['company_phone','','general','string',true],['company_email','','general','string',true],['company_address','','general','string',true],
            ['currency','USD','general','string',true],['date_format','Y-m-d','general','string',true],['timezone','UTC','general','string',false],
            ['invoice_prefix','SAL','document','string',false],['purchase_prefix','PUR','document','string',false],['allow_negative_stock','0','inventory','boolean',false],['low_stock_notifications','1','inventory','boolean',false],
        ];
        foreach($rows as [$key,$value,$group,$type,$public]) Setting::updateOrCreate(['key'=>$key],['value'=>$value,'group'=>$group,'type'=>$type,'is_public'=>$public]);
    }
}
