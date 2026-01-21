<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterDepartment;
use App\Models\MasterOffice;

class MasterDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $offices = MasterOffice::all();

        $departmentTemplates = [
            ['code' => 'CRM', 'name' => 'Customer Relationship Management'],
            ['code' => 'WQS', 'name' => 'Warehouse & Quality Stock'],
            ['code' => 'SCM', 'name' => 'Supply Chain Management'],
            ['code' => 'ACT', 'name' => 'Accounting'],
            ['code' => 'FIN', 'name' => 'Finance'],
            ['code' => 'REG', 'name' => 'Registration ALKES'],
        ];

        $count = 0;
        foreach ($offices as $office) {
            foreach ($departmentTemplates as $dept) {
                MasterDepartment::create([
                    'office_id' => $office->id,
                    'code' => $dept['code'] . '-' . $office->code,
                    'name' => $dept['name'] . ' - ' . $office->name,
                    'status' => 'active',
                ]);
                $count++;
            }
        }

        $this->command->info('✅ Created ' . $count . ' departments');
    }
}
