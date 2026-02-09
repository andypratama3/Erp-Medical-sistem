<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'master_employees';
    
    protected $fillable = [
        'name',
        'employee_code',
        'employee_name',
        'dept_code',
        'level_type',
        'grade',
        'payroll_status',
        'payroll_level',
        'job_title',
        'nik',
        'npwp',
        'bpjs_tk_no',
        'bpjs_kes_no',
        'education',
        'office_code',
        'join_year',
        'join_month',
        'phone',
        'email',
        'bank_name',
        'bank_branch',
        'bank_account_name',
        'bank_account_number',
        'status',
        'note',
    ];


    public function office()
    {
        return $this->belongsTo(MasterOffice::class, 'office_code', 'code');
    }

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class, 'dept_code', 'code');
    }
}
