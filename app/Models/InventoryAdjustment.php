<?php

namespace App\Models;

use App\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasBranchScope;

    protected $fillable = [
        'branch_id',
        'product_id',
        'adjustment_type',
        'quantity',
        'old_quantity',
        'new_quantity',
        'reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'old_quantity' => 'integer',
        'new_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
