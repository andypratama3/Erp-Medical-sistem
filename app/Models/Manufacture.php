<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacture extends Model
{
    use SoftDeletes;

    protected $table = 'master_manufactures';

    protected $fillable = [
        'code',
        'name',
        'country',
        'address',
        'city',
        'phone',
        'email',
        'website',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'manufacture_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ManufactureDoc::class, 'manufacture_id');
    }

    public function regAlkesCases(): HasMany
    {
        return $this->hasMany(RegAlkesCase::class, 'manufacture_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}