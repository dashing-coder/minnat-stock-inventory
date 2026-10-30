<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
class PeriodLockService {
    public function assertOpen(string $date): void {
        if(!Schema::hasTable('accounting_periods')) return;
        $exists=DB::table('accounting_periods')->exists(); if(!$exists) return;
        $period=DB::table('accounting_periods')->whereDate('start_date','<=',$date)->whereDate('end_date','>=',$date)->first();
        if(!$period) throw ValidationException::withMessages(['period'=>__('No accounting period covers :date.', ['date'=>$date])]);
        if($period->status!=='open') throw ValidationException::withMessages(['period'=>__('Accounting period is closed for :date.', ['date'=>$date])]);
    }
}
