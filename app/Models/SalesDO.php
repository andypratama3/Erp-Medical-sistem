<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SalesDO extends Model
{
    use SoftDeletes;

    protected $table = 'sales_do';

    protected $fillable = [
        'do_number',
        'do_date',
        'customer_id',
        'office_id',
        'customer_address',
        'customer_phone',
        'customer_pic',
        'payment_term_id',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'tax_amount',
        'total',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'do_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => 'string',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(MasterOffice::class);
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesDOItem::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(DocumentUpload::class, 'documentable');
    }

    public function taskBoards(): HasMany
    {
        return $this->hasMany(TaskBoard::class);
    }

    public function wqsStockCheck(): HasMany
    {
        return $this->hasMany(WQSStockCheck::class);
    }

    public function scmDelivery(): HasMany
    {
        return $this->hasMany(SCMDelivery::class);
    }

    public function actInvoice(): HasMany
    {
        return $this->hasMany(ACTInvoice::class);
    }

    public function finCollections(): HasMany
    {
        return $this->hasMany(FINCollection::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['crm_to_wqs', 'wqs_ready']);
    }

    // Accessors
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'crm_to_wqs' => 'To WQS',
            'wqs_ready' => 'Stock Ready',
            'scm_on_delivery' => 'On Delivery',
            'scm_delivered' => 'Delivered',
            'act_tukar_faktur' => 'Tukar Faktur',
            'act_invoiced' => 'Invoiced',
            'fin_on_collect' => 'On Collection',
            'fin_paid' => 'Paid',
            'cancelled' => 'Cancelled',
            default => $this->status,
        };
    }

    // Methods
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax_amount = $this->items->sum('tax_amount');
        $this->total = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }
}