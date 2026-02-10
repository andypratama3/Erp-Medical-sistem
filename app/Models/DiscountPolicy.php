<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountPolicy extends Model
{
    protected $table = 'master_discount_policies';

    protected $fillable = [
        'department_code',
        'level_name',
        'segment',
        'max_discount_percent',
        'notes',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }   
}
