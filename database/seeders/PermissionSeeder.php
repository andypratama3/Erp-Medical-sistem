<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;


class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Master Data Permissions
            'master.view',
            'master.create',
            'master.edit',
            'master.delete',

            // CRM Permissions
            'crm.view',
            'crm.create',
            'crm.edit',
            'crm.delete',
            'crm.submit',

            // WQS Permissions
            'wqs.view',
            'wqs.process',

            // SCM Permissions
            'scm.view',
            'scm.process',
            'scm.deliver',

            // ACT Permissions
            'act.view',
            'act.process',
            'act.invoice',

            // FIN Permissions
            'fin.view',
            'fin.process',
            'fin.collect',

            // REG ALKES Permissions
            'reg_alkes.view',
            'reg_alkes.create',
            'reg_alkes.process',
            'reg_alkes.import',

            // Dashboard Permission
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ Created ' . count($permissions) . ' permissions');
    }
}
