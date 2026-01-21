<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesDOItem extends Model
{
    protected $table = 'sales_do_items';

    protected $fillable = [
        'sales_do_id',
        'product_id',
        'product_name',
        'unit',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'subtotal',
        'tax_amount',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Methods
    public function calculateAmounts(): void
    {
        $this->subtotal = $this->quantity * $this->unit_price;

        if ($this->discount_percent > 0) {
            $this->discount_amount = ($this->subtotal * $this->discount_percent) / 100;
        }

        $afterDiscount = $this->subtotal - $this->discount_amount;
        $this->tax_amount = $afterDiscount * 0.11; // 11% tax
        $this->total = $afterDiscount + $this->tax_amount;
    }
}