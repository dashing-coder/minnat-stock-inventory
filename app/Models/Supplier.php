<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'code','supplier_group_id','name','company_name','contact_person','phone','alternate_phone','email',
        'address','city','district','country','opening_balance','opening_balance_type','payment_terms_days',
        'tax_number','notes','is_active'
    ];
    protected function casts(): array { return ['opening_balance'=>'decimal:2','is_active'=>'boolean']; }
    public function group(): BelongsTo { return $this->belongsTo(SupplierGroup::class, 'supplier_group_id'); }
    public function ledgerEntries(): HasMany { return $this->hasMany(SupplierLedgerEntry::class); }
    public function getCurrentBalanceAttribute(): string
    {
        return (string) $this->ledgerEntries()
            ->selectRaw('COALESCE(SUM(credit),0) - COALESCE(SUM(debit),0) AS balance')
            ->value('balance');
    }
}
