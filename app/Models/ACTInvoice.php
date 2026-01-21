<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ACTInvoice extends Model
{
    protected $table = 'act_invoices';

    protected $fillable = [
        'sales_do_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'faktur_pajak_number',
        'faktur_pajak_date',
        'subtotal',
        'tax_amount',
        'total',
        'invoice_status',
        'tukar_faktur_at',
        'tukar_faktur_pic',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'faktur_pajak_date' => 'date',
        'tukar_faktur_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'invoice_status' => 'string',
    ];

    // Relationships
    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(FINCollection::class, 'invoice_id');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('invoice_status', 'draft');
    }

    public function scopeIssued($query)
    {
        return $query->where('invoice_status', 'issued');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('invoice_status', ['completed']);
    }

    // Accessors
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->due_date || $this->invoice_status === 'completed') {
            return 0;
        }

        $overdue = now()->diffInDays($this->due_date, false);
        return $overdue < 0 ? abs($overdue) : 0;
    }

    public function getAgingCategoryAttribute(): string
    {
        $days = $this->days_overdue;

        if ($days === 0) return 'Current';
        if ($days <= 30) return '1-30 Days';
        if ($days <= 60) return '31-60 Days';
        if ($days <= 90) return '61-90 Days';
        return 'Over 90 Days';
    }
}