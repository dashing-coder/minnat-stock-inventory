<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierLedgerEntry extends Model
{
    protected $fillable = [
        'supplier_id','transaction_date','entry_type','reference_no','source_type','source_id',
        'debit','credit','description','created_by'
    ];
    protected function casts(): array { return ['transaction_date'=>'date','debit'=>'decimal:2','credit'=>'decimal:2']; }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
