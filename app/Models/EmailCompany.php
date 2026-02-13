<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCompany extends Model
{
    protected $fillable = [
        'scope_type',
        'dept_code',
        'office_code',
        'email_local',
        'email_domain',
        'email_full',
        'note',
        'is_primary',
        'status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class, 'dept_code', 'code');
    }   
}
