<?php

namespace App\Models;

use App\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsTo};

class SCMDriver extends Model
{
    use SoftDeletes, HasBranchScope;

    protected $table = 'scm_drivers';

    protected $fillable = [
        'branch_id',
        'code',
        'name',
        'phone',
        'email',
        'license_number',
        'license_expiry',
        'vehicle_type',
        'vehicle_number',
        'vehicle_capacity',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
        'license_expiry' => 'date',
    ];

    // Relationships
    public function deliveries(): HasMany
    {
        return $this->hasMany(SCMDelivery::class, 'driver_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
                    ->whereDoesntHave('deliveries', function($q) {
                        $q->whereIn('delivery_status', ['scheduled', 'on_route']);
                    });
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} - {$this->vehicle_number}";
    }

    public function getIsLicenseExpiringSoonAttribute(): bool
    {
        if (!$this->license_expiry) {
            return false;
        }

        return $this->license_expiry->diffInDays(now()) <= 30;
    }

    public function getIsLicenseExpiredAttribute(): bool
    {
        if (!$this->license_expiry) {
            return false;
        }

        return $this->license_expiry->isPast();
    }
}
