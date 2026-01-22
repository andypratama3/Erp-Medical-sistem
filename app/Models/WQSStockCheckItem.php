<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WQSStockCheckItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wqs_stock_check_items';

    protected $fillable = [
        'stock_check_id',
        'product_id',
        'stock_status',
        'available_qty',
        'notes',
    ];

    /* ================= CONSTANTS ================= */

    public const STOCK_STATUS = [
        'available' => 'Available',
        'partial' => 'Partial Available',
        'not_available' => 'Not Available',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function stockCheck(): BelongsTo
    {
        return $this->belongsTo(WQSStockCheck::class, 'stock_check_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function doItem(): BelongsTo
    {
        return $this->belongsTo(SalesDOItem::class, 'product_id', 'product_id')
                    ->where('sales_do_id', function($query) {
                        $query->select('sales_do_id')
                              ->from('wqs_stock_checks')
                              ->where('id', $this->stock_check_id);
                    });
    }

    /* ================= SCOPES ================= */

    public function scopeByStockCheck($query, int $stockCheckId)
    {
        return $query->where('stock_check_id', $stockCheckId);
    }

    public function scopeAvailable($query)
    {
        return $query->where('stock_status', 'available');
    }

    public function scopePartial($query)
    {
        return $query->where('stock_status', 'partial');
    }

    public function scopeNotAvailable($query)
    {
        return $query->where('stock_status', 'not_available');
    }

    public function scopeWithIssues($query)
    {
        return $query->whereIn('stock_status', ['partial', 'not_available']);
    }

    /* ================= ACCESSORS ================= */

    public function getStockStatusLabelAttribute(): string
    {
        return self::STOCK_STATUS[$this->stock_status] ?? $this->stock_status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'available' => 'green',
            'partial' => 'yellow',
            'not_available' => 'red',
            default => 'gray',
        };
    }

    public function getHasIssueAttribute(): bool
    {
        return in_array($this->stock_status, ['partial', 'not_available']);
    }

    /**
     * Get comparison with DO item
     */
    public function getComparisonWithDOAttribute(): array
    {
        $doItem = SalesDOItem::where('sales_do_id', $this->stockCheck->sales_do_id)
                             ->where('product_id', $this->product_id)
                             ->first();

        if (!$doItem) {
            return [
                'do_qty' => 0,
                'check_qty' => $this->available_qty,
                'match' => false,
                'shortage' => $this->available_qty,
            ];
        }

        $shortage = $doItem->qty_ordered - $this->available_qty;

        return [
            'do_qty' => $doItem->qty_ordered,
            'check_qty' => $this->available_qty,
            'match' => $doItem->qty_ordered === $this->available_qty,
            'shortage' => $shortage,
            'has_shortage' => $shortage > 0,
        ];
    }

    /* ================= BUSINESS LOGIC ================= */

    /**
     * Check if quantity matches DO
     */
    public function qtyMatches(): bool
    {
        return $this->comparison_with_do['match'];
    }

    /**
     * Get shortage quantity
     */
    public function getShortageQty(): int
    {
        return max(0, $this->comparison_with_do['shortage']);
    }

    /**
     * Mark as fully available
     */
    public function markAvailable(int $qty): bool
    {
        return $this->update([
            'stock_status' => 'available',
            'available_qty' => $qty,
        ]);
    }

    /**
     * Mark as partially available
     */
    public function markPartial(int $qty, string $reason = ''): bool
    {
        $note = "PARTIAL: {$reason} - Available: {$qty} - " . now()->format('Y-m-d H:i:s');

        return $this->update([
            'stock_status' => 'partial',
            'available_qty' => $qty,
            'notes' => ($this->notes ?? '') . "\n" . $note,
        ]);
    }

    /**
     * Mark as not available
     */
    public function markNotAvailable(string $reason = ''): bool
    {
        $note = "NOT AVAILABLE: {$reason} - " . now()->format('Y-m-d H:i:s');

        return $this->update([
            'stock_status' => 'not_available',
            'available_qty' => 0,
            'notes' => ($this->notes ?? '') . "\n" . $note,
        ]);
    }

    /**
     * Check if needs investigation
     */
    public function needsInvestigation(): bool
    {
        return $this->has_issue || !$this->qtyMatches();
    }

    /**
     * Get investigation details
     */
    public function getInvestigationDetails(): array
    {
        return [
            'product' => $this->product->name,
            'sku' => $this->product->sku,
            'stock_status' => $this->stock_status_label,
            'available_qty' => $this->available_qty,
            'do_qty' => $this->comparison_with_do['do_qty'],
            'shortage' => $this->getShortageQty(),
            'has_shortage' => $this->comparison_with_do['has_shortage'],
            'notes' => $this->notes,
        ];
    }
}
