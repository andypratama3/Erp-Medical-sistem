<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\MasterOffice;
use Illuminate\Database\Seeder;
use App\Models\MasterDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $department = MasterDepartment::first();
        $masterOffice = MasterOffice::first();

        Employee::create([
            'name' => 'John Doe',
            'employee_code' => 'EMP001',
            'employee_name' => 'John Doe',
            'dept_code' => $department->code,
            'level_type' => 'level_1',
            'grade' => 'A',
            'payroll_status' => 'active',
            'payroll_level' => 'A',
            'job_title' => 'John Doe',
            'nik' => '1234567890',
            'npwp' => '1234567890',
            'bpjs_tk_no' => '1234567890',
            'bpjs_kes_no' => '1234567890',
            'education' => 'John Doe',
            'office_code' => $masterOffice->code,
            'join_year' => '2022',
            'join_month' => '01',
            'phone' => '08123456789',
            'email' => 'John Doe',
            'bank_name' => 'Bank BCA',
            'bank_branch' => 'Bandar Lampung',
            'bank_account_name' => 'John Doe',
            'bank_account_number' => '1234567890',
            'status' => 'active',
            'note' => 'John Doe',
        ]);
    }
}
       