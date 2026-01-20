<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceList extends Model
{
    protected $fillable = [
        'product_id',
        'price_code',
        'buy_price',
        'sell_price',
        'margin',
        'discount',
        'effective_date',
        'expired_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'margin' => 'decimal:2',
        'discount' => 'decimal:2',
        'effective_date' => 'date',
        'expired_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEffective($query)
    {
        return $query->where('effective_date', '<=', now())
                     ->where(function($q) {
                         $q->whereNull('expired_date')
                           ->orWhere('expired_date', '>=', now());
                     });
    }

    public function getFormattedBuyPriceAttribute()
    {
        return 'Rp ' . number_format($this->buy_price, 0, ',', '.');
    }

    public function getFormattedSellPriceAttribute()
    {
        return 'Rp ' . number_format($this->sell_price, 0, ',', '.');
    }

    public function getFormattedMarginAttribute()
    {
        return number_format($this->margin, 2) . '%';
    }

    public static function canViewBuyPrice()
    {
        if (!auth()->check()) {
            return false;
        }

        $allowedRoles = ['PQP', 'Finance', 'Admin', 'Superadmin'];
        return auth()->user()->hasAnyRole($allowedRoles);
    }

    public static function canViewSellPrice()
    {
        if (!auth()->check()) {
            return false;
        }

        $allowedRoles = ['CRM', 'MPR', 'PQP', 'Finance', 'Admin', 'Superadmin'];
        return auth()->user()->hasAnyRole($allowedRoles);
    }

    protected static function booted()
    {
        static::creating(function ($priceList) {
            if (empty($priceList->price_code)) {
                $priceList->price_code = 'PRC-' . strtoupper(uniqid());
            }

            if ($priceList->buy_price && $priceList->sell_price) {
                $priceList->margin = (($priceList->sell_price - $priceList->buy_price) / $priceList->buy_price) * 100;
            }
        });

        static::updating(function ($priceList) {
            if ($priceList->buy_price && $priceList->sell_price) {
                $priceList->margin = (($priceList->sell_price - $priceList->buy_price) / $priceList->buy_price) * 100;
            }
        });

        static::created(function ($priceList) {
            AuditLog::log('created', $priceList, null, $priceList->toArray(), 'price_list');
        });

        static::updated(function ($priceList) {
            AuditLog::log('updated', $priceList, $priceList->getOriginal(), $priceList->getChanges(), 'price_list');
        });

        static::deleted(function ($priceList) {
            AuditLog::log('deleted', $priceList, $priceList->toArray(), null, 'price_list');
        });
    }
}
