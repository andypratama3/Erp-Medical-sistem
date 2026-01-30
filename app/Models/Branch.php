<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Branch extends Model
{
    use SoftDeletes;

    protected $table = 'master_branches';

    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'province',
        'phone',
        'email',
        'manager_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ============ RELATIONSHIPS ============

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_branches',
            'branch_id',
            'user_id'
        )
        ->withPivot('is_default')
        ->withTimestamps();
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(ActInvoice::class, 'branch_id');
    }

    public function salesDO(): HasMany
    {
        return $this->hasMany(SalesDO::class, 'branch_id');
    }

    public function collections(): HasMany
    {
        return $this->hasMany(FinCollection::class, 'branch_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FinPayment::class, 'branch_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(ScmDelivery::class, 'branch_id');
    }

    public function stockChecks(): HasMany
    {
        return $this->hasMany(WqsStockCheck::class, 'branch_id');
    }

    // ============ HELPERS ============

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ============ SCOPES ============

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // ============ QUERIES ============

    /**
     * Get total sales for this branch within a date range.
     */
    public function getTotalSales($startDate = null, $endDate = null): float
    {
        $query = $this->salesDO();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->sum('total') ?? 0;
    }
}
