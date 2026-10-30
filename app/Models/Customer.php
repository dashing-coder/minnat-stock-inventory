<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'code','customer_group_id','market_hierarchy_id','name','company_name','contact_person','phone','alternate_phone','email',
        'address','city','district','country','opening_balance','opening_balance_type','credit_limit',
        'payment_terms_days','tax_number','notes','is_active'
    ];
    protected function casts(): array
    {
        return ['opening_balance'=>'decimal:2','credit_limit'=>'decimal:2','is_active'=>'boolean'];
    }
    public function group(): BelongsTo { return $this->belongsTo(CustomerGroup::class, 'customer_group_id'); }
    public function marketHierarchy(): BelongsTo { return $this->belongsTo(MarketHierarchy::class); }
    public function ledgerEntries(): HasMany { return $this->hasMany(CustomerLedgerEntry::class); }
    public function getCurrentBalanceAttribute(): string
    {
        return (string) $this->ledgerEntries()
            ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) AS balance')
            ->value('balance');
    }
}
