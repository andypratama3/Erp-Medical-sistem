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
        'do_code',
        'tracking_code',
        'do_date',
        'customer_id',
        'office_id',
        'shipping_address',
        'pic_customer',
        'payment_term_id',
        'tax_id',
        'subtotal',
        'tax_amount',
        'grand_total',
        'status',
        'notes_crm',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'do_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
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

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesDOItem::class,'sales_do_id','id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(DocumentUpload::class, 'documentable');
    }

    public function taskBoards(): HasMany
    {
        return $this->hasMany(TaskBoard::class, 'sales_do_id','id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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
    public function getFormattedGrandTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'crm_to_wqs' => 'CRM to WQS',
            'wqs_ready' => 'WQS Ready',
            'wqs_on_hold' => 'WQS On Hold',
            'scm_on_delivery' => 'On Delivery',
            'scm_delivered' => 'Delivered',
            'act_tukar_faktur' => 'Tukar Faktur',
            'act_invoiced' => 'Invoiced',
            'fin_on_collect' => 'On Collection',
            'fin_paid' => 'Paid',
            'fin_overdue' => 'Overdue',
            default => $this->status,
        };
    }
}
