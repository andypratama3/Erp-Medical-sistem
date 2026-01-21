<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterDepartment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'office_id',
        'code',
        'name',
        'head_name',
        'phone',
        'email',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function office(): BelongsTo
    {
        return $this->belongsTo(MasterOffice::class, 'office_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}