<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    MorphMany
};
use Illuminate\Database\Eloquent\SoftDeletes;

class WQSStockCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wqs_stock_checks';

    protected $fillable = [
        'sales_do_id',
        'check_date',
        'overall_status',
        'check_notes',
        'checked_by',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'check_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /* ================= CONSTANTS ================= */

    public const STOCK_STATUS = [
        'available' => 'Available',
        'partial' => 'Partial',
        'not_available' => 'Not Available',
    ];

    public const OVERALL_STATUS = [
        'pending' => 'Pending',
        'checked' => 'Checked',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class, 'sales_do_id');
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WQSStockCheckItem::class, 'stock_check_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(DocumentUpload::class, 'documentable');
    }

    /* ================= SCOPES ================= */

    public function scopeBySalesdo($query, int $salesDoId)
    {
        return $query->where('sales_do_id', $salesDoId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('overall_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('overall_status', 'pending');
    }

    public function scopeChecked($query)
    {
        return $query->where('overall_status', 'checked');
    }

    public function scopeCompleted($query)
    {
        return $query->where('overall_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('overall_status', 'failed');
    }

    public function scopeLatestChecks($query)
    {
        return $query->latest('check_date');
    }

    /* ================= ACCESSORS ================= */

    public function getStatusLabelAttribute(): string
    {
        return self::OVERALL_STATUS[$this->overall_status] ?? $this->overall_status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->overall_status) {
            'pending' => 'yellow',
            'checked' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            default => 'gray',
        };
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->overall_status === 'completed';
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items()->count();
    }

    public function getAvailableItemsAttribute(): int
    {
        return $this->items()
            ->where('stock_status', 'available')
            ->count();
    }

    public function getPartialItemsAttribute(): int
    {
        return $this->items()
            ->where('stock_status', 'partial')
            ->count();
    }

    public function getNotAvailableItemsAttribute(): int
    {
        return $this->items()
            ->where('stock_status', 'not_available')
            ->count();
    }

    public function getCompletionPercentageAttribute(): int
    {
        $total = $this->total_items;
        if ($total === 0) {
            return 0;
        }

        return round(($this->available_items / $total) * 100);
    }

    public function getStatusSummaryAttribute(): array
    {
        return [
            'total' => $this->total_items,
            'available' => $this->available_items,
            'partial' => $this->partial_items,
            'not_available' => $this->not_available_items,
            'completion_percentage' => $this->completion_percentage,
        ];
    }

    /* ================= BUSINESS LOGIC ================= */

    /**
     * Add stock check item
     */
    public function addItem(
        int $productId,
        string $stockStatus,
        int $availableQty,
        string $notes = ''
    ): WQSStockCheckItem {
        return $this->items()->create([
            'product_id' => $productId,
            'stock_status' => $stockStatus,
            'available_qty' => $availableQty,
            'notes' => $notes,
        ]);
    }

    /**
     * Check if all items are verified
     */
    public function isFullyChecked(): bool
    {
        $doItems = $this->salesDO->items()->count();
        $checkedItems = $this->items()->count();

        return $doItems > 0 && $doItems === $checkedItems;
    }

    /**
     * Check if all items are available
     */
    public function allAvailable(): bool
    {
        return $this->items()
            ->where('stock_status', '!=', 'available')
            ->doesntExist();
    }

    /**
     * Mark as completed
     */
    public function markCompleted(): bool
    {
        if (!$this->isFullyChecked()) {
            return false;
        }

        return $this->update([
            'overall_status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark as failed (stock issues)
     */
    public function markFailed(string $reason = ''): bool
    {
        $notes = "FAILED: {$reason} - " . now()->format('Y-m-d H:i:s');

        return $this->update([
            'overall_status' => 'failed',
            'notes' => ($this->notes ?? '') . "\n" . $notes,
        ]);
    }

    /**
     * Get items with issues
     */
    public function getProblematicItems()
    {
        return $this->items()
            ->whereIn('stock_status', ['partial', 'not_available'])
            ->get();
    }

    /**
     * Generate stock check report
     */
    public function getReport(): array
    {
        return [
            'stock_check_id' => $this->id,
            'sales_do' => [
                'code' => $this->salesDO->do_code,
                'customer' => $this->salesDO->customer?->name,
                'office' => $this->salesDO->office?->name,
            ],
            'check_info' => [
                'date' => $this->check_date->format('Y-m-d'),
                'checked_by' => $this->checkedBy?->name,
                'status' => $this->status_label,
            ],
            'summary' => $this->status_summary,
            'items' => $this->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'stock_status' => $item->stock_status_label,
                    'available_qty' => $item->available_qty,
                    'notes' => $item->notes,
                ];
            }),
            'problematic_items' => $this->getProblematicItems()->map(function ($item) {
                return [
                    'product' => $item->product->name,
                    'status' => $item->stock_status,
                    'available_qty' => $item->available_qty,
                    'notes' => $item->notes,
                ];
            }),
        ];
    }

    /* ================= EVENTS ================= */

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->overall_status ??= 'pending';
            $model->check_date ??= now()->date();
        });

        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                $model->items()->forceDelete();
                $model->documents()->forceDelete();
            }
        });

        static::restoring(function ($model) {
            $model->items()->withTrashed()->restore();
            $model->documents()->withTrashed()->restore();
        });
    }
}
