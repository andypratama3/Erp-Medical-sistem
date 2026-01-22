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
        'line_number',
        'product_sku',
        'product_name',
        'unit',
        'qty_ordered',
        'qty_delivered',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'line_total',
    ];

    protected $casts = [
        'qty_ordered' => 'integer',
        'qty_delivered' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class, 'sales_do_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
