<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use SoftDeletes;

    protected $table = 'master_vendors';

    protected $fillable = [
        'code',
        'name',
        'legal_name',
        'npwp',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'contact_person',
        'contact_phone',
        'payment_term_id',
        'vendor_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
        'vendor_type' => 'string',
    ];

    // Relationships
    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }
}