<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SCMDelivery extends Model
{
    protected $table = 'scm_deliveries';

    protected $fillable = [
        'sales_do_id',
        'driver_id',
        'delivery_date',
        'departure_time',
        'arrival_time',
        'delivery_status',
        'receiver_name',
        'receiver_position',
        'received_at',
        'delivery_notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'received_at' => 'datetime',
        'delivery_status' => 'string',
    ];

    // Relationships
    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(SCMDriver::class, 'driver_id');
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('delivery_status', 'scheduled');
    }

    public function scopeOnRoute($query)
    {
        return $query->where('delivery_status', 'on_route');
    }

    public function scopeDelivered($query)
    {
        return $query->where('delivery_status', 'delivered');
    }

    // Accessors
    public function getDeliveryDurationAttribute(): ?string
    {
        if (!$this->departure_time || !$this->arrival_time) {
            return null;
        }

        $duration = $this->departure_time->diffInMinutes($this->arrival_time);
        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        return "{$hours}h {$minutes}m";
    }
}