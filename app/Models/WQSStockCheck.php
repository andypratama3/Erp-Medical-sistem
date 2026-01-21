<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WQSStockCheck extends Model
{
    protected $table = 'wqs_stock_checks';

    protected $fillable = [
        'sales_do_id',
        'check_status',
        'checked_at',
        'checked_by',
        'stock_notes',
        'stock_details',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'stock_details' => 'array',
        'check_status' => 'string',
    ];

    // Relationships
    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class);
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'checked_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('check_status', 'pending');
    }

    public function scopeAvailable($query)
    {
        return $query->where('check_status', 'available');
    }
}