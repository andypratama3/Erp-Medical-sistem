<?php

namespace App\Models;

use App\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SCMDelivery extends Model
{
    use SoftDeletes, HasBranchScope;

    protected $table = 'scm_deliveries';

    protected $fillable = [
        'branch_id',
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
        'shipping_address',
        'tracking_number',
        'notes',
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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

    public function scopePending($query)
    {
        return $query->where('delivery_status', 'pending');
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

    public function getStatusBadgeAttribute(): array
    {
        return [
            'value' => $this->delivery_status,
            'label' => ucfirst(str_replace('_', ' ', $this->delivery_status)),
            'color' => match($this->delivery_status) {
                'pending' => 'gray',
                'scheduled' => 'yellow',
                'on_route' => 'blue',
                'delivered' => 'green',
                'failed' => 'red',
                'cancelled' => 'gray',
                default => 'gray',
            }
        ];
    }
}
