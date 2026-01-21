<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RegAlkesCase extends Model
{
    use SoftDeletes;

    protected $table = 'reg_alkes_cases';

    protected $fillable = [
        'case_number',
        'manufacture_id',
        'manufacture_name',
        'country_of_origin',
        'case_type',
        'submission_date',
        'target_date',
        'nie_issued_date',
        'nie_number',
        'case_status',
        'notes',
        'total_skus',
        'imported_skus',
        'active_skus',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'target_date' => 'date',
        'nie_issued_date' => 'date',
        'total_skus' => 'integer',
        'imported_skus' => 'integer',
        'active_skus' => 'integer',
        'case_type' => 'string',
        'case_status' => 'string',
    ];

    // Relationships
    public function manufacture(): BelongsTo
    {
        return $this->belongsTo(Manufacture::class, 'manufacture_id');
    }

    public function caseItems(): HasMany
    {
        return $this->hasMany(RegAlkesCaseItem::class, 'case_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(DocumentUpload::class, 'documentable');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('case_status', 'case_draft');
    }

    public function scopeWaitingNIE($query)
    {
        return $query->where('case_status', 'waiting_nie');
    }

    public function scopePQP($query)
    {
        return $query->where('case_type', 'pqp');
    }

    public function scopeHRL($query)
    {
        return $query->where('case_type', 'hrl');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->case_status) {
            'case_draft' => 'Draft',
            'case_submitted' => 'Submitted',
            'waiting_nie' => 'Waiting NIE',
            'nie_issued' => 'NIE Issued',
            'sku_imported' => 'SKU Imported',
            'sku_active' => 'SKU Active',
            'cancelled' => 'Cancelled',
            default => $this->case_status,
        };
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_skus === 0) return 0;
        return ($this->active_skus / $this->total_skus) * 100;
    }
}