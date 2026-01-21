<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use SoftDeletes;

    protected $table = 'master_tax';

    protected $fillable = [
        'code',
        'name',
        'rate',
        'description',
        'status',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'status' => 'string',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getRatePercentAttribute(): string
    {
        return number_format($this->rate, 2) . '%';
    }
}