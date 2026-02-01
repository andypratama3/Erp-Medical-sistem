<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryTracking extends Model
{
    protected $fillable = [
        'delivery_id',
        'latitude',
        'longitude',
        'status',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function delivery()
    {
        return $this->belongsTo(SCMDelivery::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
