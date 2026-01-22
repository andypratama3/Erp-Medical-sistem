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
use App\Helpers\SalesDOHelper;

class SalesDO extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_do';

    protected $fillable = [
        'do_code',
        'tracking_code',
        'customer_id',
        'office_id',
        'do_date',
        'pic_customer',
        'shipping_address',
        'payment_term_id',
        'tax_id',
        'subtotal',
        'tax_amount',
        'grand_total',
        'status',
        'notes_crm',
        'created_by',
        'updated_by',
        'submitted_by',
        'submitted_at',
    ];

    protected $casts = [
        'do_date' => 'date',
        'submitted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /* ================= RELATIONSHIPS ================= */

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
        return $this->hasMany(SalesDOItem::class, 'sales_do_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(DocumentUpload::class, 'documentable');
    }

    public function taskBoards(): HasMany {
        return $this->hasMany(TaskBoard::class, 'sales_do_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /* ================= SCOPES ================= */

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUnsubmitted($query)
    {
        return $query->whereNull('submitted_at');
    }

    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    /* ================= ACCESSORS ================= */

    public function getStatusConfigAttribute(): array
    {
        return SalesDOHelper::getStatusConfigByKey($this->status);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_config['label'];
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status_config['badge_class'];
    }

    public function getProgressPercentageAttribute(): int
    {
        return $this->status_config['progress'] ?? 0;
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return SalesDOHelper::formatCurrency($this->grand_total);
    }

    /* ================= BUSINESS LOGIC ================= */

    public function canBeSubmitted(): bool
    {
        return SalesDOHelper::isSubmittable($this->status)
            && $this->items()->exists();
    }

    public function canBeEdited(): bool
    {
        return SalesDOHelper::isEditable($this->status);
    }

    public function canBeDeleted(): bool
    {
        return SalesDOHelper::isDeletable($this->status);
    }

    /* ================= EVENTS ================= */

    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by ??= auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
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
