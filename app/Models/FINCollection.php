<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FINCollection extends Model
{
    protected $table = 'fin_collections';

    protected $fillable = [
        'sales_do_id',
        'invoice_id',
        'collection_number',
        'collection_date',
        'amount_collected',
        'payment_method',
        'payment_reference',
        'collection_status',
        'notes',
        'collected_by',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'amount_collected' => 'decimal:2',
        'payment_method' => 'string',
        'collection_status' => 'string',
    ];

    // Relationships
    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ACTInvoice::class, 'invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FINPayment::class, 'collection_id');
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'collected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('collection_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('collection_status', 'completed');
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_collected, 0, ',', '.');
    }
}