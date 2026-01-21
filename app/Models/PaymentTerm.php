<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentTerm extends Model
{
    use SoftDeletes;

    protected $table = 'master_payment_terms';

    protected $fillable = [
        'code',
        'name',
        'days',
        'description',
        'status',
    ];

    protected $casts = [
        'days' => 'integer',
        'status' => 'string',
    ];

    // Relationships
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
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

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->days} days)";
    }
}