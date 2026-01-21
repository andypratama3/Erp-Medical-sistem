<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SCMDriver extends Model
{
    use SoftDeletes;

    protected $table = 'scm_drivers';

    protected $fillable = [
        'code',
        'name',
        'phone',
        'license_number',
        'vehicle_type',
        'vehicle_number',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function deliveries(): HasMany
    {
        return $this->hasMany(SCMDelivery::class, 'driver_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} - {$this->vehicle_number}";
    }
}