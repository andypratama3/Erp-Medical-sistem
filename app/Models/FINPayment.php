<?php

namespace App\Models;

use App\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FINPayment extends Model
{
    use HasBranchScope;
    
    protected $table = 'fin_payments';

    protected $fillable = [
        'branch_id',
        'sales_do_id',
        'collection_id',
        'payment_number',
        'payment_date',
        'payment_amount',
        'payment_method',
        'bank_name',
        'account_number',
        'reference_number',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'payment_amount' => 'decimal:2',
        'payment_method' => 'string',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    
    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(FINCollection::class, 'collection_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->payment_amount, 0, ',', '.');
    }
}