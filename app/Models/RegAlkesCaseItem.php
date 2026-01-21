<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegAlkesCaseItem extends Model
{
    protected $table = 'reg_alkes_case_items';

    protected $fillable = [
        'case_id',
        'product_id',
        'product_name',
        'catalog_number',
        'akl_akd_number',
        'akl_akd_expiry',
        'registration_type',
        'item_status',
        'notes',
    ];

    protected $casts = [
        'akl_akd_expiry' => 'date',
        'registration_type' => 'string',
        'item_status' => 'string',
    ];

    // Relationships
    public function regAlkesCase(): BelongsTo
    {
        return $this->belongsTo(RegAlkesCase::class, 'case_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('item_status', 'active');
    }

    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->where('akl_akd_expiry', '<=', now()->addDays($days))
            ->where('akl_akd_expiry', '>=', now());
    }

    // Accessors
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->akl_akd_expiry) return null;
        return now()->diffInDays($this->akl_akd_expiry, false);
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->akl_akd_expiry) return false;
        return $this->akl_akd_expiry->isPast();
    }
}