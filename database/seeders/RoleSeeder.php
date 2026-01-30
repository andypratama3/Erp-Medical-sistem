<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;


class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // SUPERADMIN - Full Access
        $superadmin = Role::create(['name' => 'superadmin']);
        $owner = Role::create(['name' => 'owner','guard_name'=> 'web']);
        $superadmin->givePermissionTo(Permission::all());

        // MANAGER - View All, Limited Edit
        $manager = Role::create(['name' => 'manager','guard_name'=> 'web']);
        $manager->givePermissionTo([
            'dashboard.view',
            'master.view',
            'crm.view',
            'wqs.view',
            'scm.view',
            'act.view',
            'fin.view',
            'reg_alkes.view',
        ]);

        // STAFF CRM - CRM Only
        $staffCRM = Role::create(['name' => 'staff_crm','guard_name'=> 'web']);
        $staffCRM->givePermissionTo([
            'dashboard.view',
            'master.view',
            'crm.view',
            'crm.create',
            'crm.edit',
            'crm.submit',
        ]);

        // STAFF WQS - WQS Only
        $staffWQS = Role::create(['name' => 'staff_wqs','guard_name'=> 'web']);
        $staffWQS->givePermissionTo([
            'dashboard.view',
            'master.view',
            'wqs.view',
            'wqs.process',
        ]);

        // STAFF SCM - SCM Only
        $staffSCM = Role::create(['name' => 'staff_scm','guard_name'=> 'web']);
        $staffSCM->givePermissionTo([
            'dashboard.view',
            'master.view',
            'scm.view',
            'scm.process',
            'scm.deliver',
        ]);

        // STAFF ACT - ACT Only
        $staffACT = Role::create(['name' => 'staff_act','guard_name'=> 'web']);
        $staffACT->givePermissionTo([
            'dashboard.view',
            'master.view',
            'act.view',
            'act.process',
            'act.invoice',
        ]);

        // STAFF FIN - FIN Only
        $staffFIN = Role::create(['name' => 'staff_fin','guard_name'=> 'web']);
        $staffFIN->givePermissionTo([
            'dashboard.view',
            'master.view',
            'fin.view',
            'fin.process',
            'fin.collect',
        ]);

        // STAFF REG ALKES - REG ALKES Only
        $staffRegAlkes = Role::create(['name' => 'staff_reg_alkes','guard_name'=> 'web']);
        $staffRegAlkes->givePermissionTo([
            'dashboard.view',
            'master.view',
            'reg_alkes.view',
            'reg_alkes.create',
            'reg_alkes.process',
            'reg_alkes.import',
        ]);

        $this->command->info('✅ Created 8 roles with permissions');
    }
}
