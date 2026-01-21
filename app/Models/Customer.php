<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'master_customers';

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
        'mobile',
        'email',
        'contact_person',
        'contact_phone',
        'payment_term_id',
        'credit_limit',
        'credit_days',
        'customer_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'credit_days' => 'integer',
        'status' => 'string',
        'customer_type' => 'string',
    ];

    // Relationships
    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function salesDOs(): HasMany
    {
        return $this->hasMany(SalesDO::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeHospital($query)
    {
        return $query->where('customer_type', 'hospital');
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
