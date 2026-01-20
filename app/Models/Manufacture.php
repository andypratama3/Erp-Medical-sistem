<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacture extends Model
{
    protected $table = 'manufactures';

    protected $fillable = [
        'name',
        'code',
        'country',
        'email',
        'phone',
        'address',
        'status'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
