<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchProductStock extends Model
{
    protected $table = 'branch_product_stock';

    protected $fillable = [
        'branch_id',
        'product_id',
        'stock_quantity',
        'reserved_quantity',
        'available_quantity',
        'min_stock',
        'max_stock',
        'reorder_point',
        'warehouse_location',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'available_quantity' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'reorder_point' => 'integer',
    ];

    /**
     * Get the branch that owns this stock
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the product this stock belongs to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->min_stock;
    }

    /**
     * Check if stock needs reordering
     */
    public function needsReorder(): bool
    {
        return $this->available_quantity <= $this->reorder_point;
    }

    /**
     * Check if stock is over maximum
     */
    public function isOverStock(): bool
    {
        return $this->stock_quantity >= $this->max_stock;
    }

    /**
     * Get stock status
     */
    public function getStockStatus(): string
    {
        if ($this->available_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } elseif ($this->isOverStock()) {
            return 'over_stock';
        } else {
            return 'normal';
        }
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('available_quantity', '<=', 'min_stock');
    }

    /**
     * Scope for out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('available_quantity', '<=', 0);
    }

    /**
     * Scope for specific branch
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
