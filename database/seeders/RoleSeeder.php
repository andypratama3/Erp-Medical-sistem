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
        $admin = Role::firstOrCreate(['name'=> 'admin']);
        $owner = Role::create(['name' => 'owner','guard_name'=> 'web']);
        $superadmin->givePermissionTo(Permission::all());


        $admin->givePermissionTo(Permission::all());

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
        $staffCRM = Role::create(['name' => 'crm','guard_name'=> 'web']);
        $staffCRM->givePermissionTo([
            'dashboard.view',
            'master.view',
            'crm.view',
            'crm.create',
            'crm.edit',
            'crm.submit',
        ]);
        
        // Sales  
        $salesDO = Role::create(['name' => 'sales','guard_name'=> 'web']);
        $salesDO->givePermissionTo([
            'dashboard.view',
            'master.view',
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
        ]);


        // STAFF WQS - WQS Only
        $staffWQS = Role::create(['name' => 'wqs','guard_name'=> 'web']);
        $staffWQS->givePermissionTo([
            'dashboard.view',
            'master.view',
            'wqs.view',
            'wqs.process',
        ]);

        // STAFF SCM - SCM Only
        $staffSCM = Role::create(['name' => 'scm','guard_name'=> 'web']);
        $staffSCM->givePermissionTo([
            'dashboard.view',
            'master.view',
            'scm.view',
            'scm.process',
            'scm.deliver',
        ]);

        // STAFF ACT - ACT Only
        $staffACT = Role::create(['name' => 'act','guard_name'=> 'web']);
        $staffACT->givePermissionTo([
            'dashboard.view',
            'master.view',
            'act.view',
            'act.process',
            'act.invoice',
        ]);

        // STAFF FIN - FIN Only
        $staffFIN = Role::create(['name' => 'fin','guard_name'=> 'web']);
        $staffFIN->givePermissionTo([
            'dashboard.view',
            'master.view',
            'fin.view',
            'fin.process',
            'fin.collect',
        ]);

        // STAFF REG ALKES - REG ALKES Only
        $staffRegAlkes = Role::create(['name' => 'reg_alkes','guard_name'=> 'web']);
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
