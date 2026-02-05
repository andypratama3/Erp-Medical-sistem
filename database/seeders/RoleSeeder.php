<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // OWNER - Full Access
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $owner->syncPermissions(Permission::all());

        // SUPERADMIN - Full Access
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadmin->syncPermissions(Permission::all());

        // ADMIN - Full Access
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // MANAGER - View All, Limited Edit
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        // STAFF CRM - CRM Only
        $crm = Role::firstOrCreate(['name' => 'crm', 'guard_name' => 'web']);

        // SALES - Sales Orders
        $sales = Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
        // STAFF WQS - Warehouse Quality System
        $wqs = Role::firstOrCreate(['name' => 'wqs', 'guard_name' => 'web']);

        // STAFF SCM - Supply Chain Management
        $scm = Role::firstOrCreate(['name' => 'scm', 'guard_name' => 'web']);
       

        // STAFF ACT - Accounting
        $act = Role::firstOrCreate(['name' => 'act', 'guard_name' => 'web']);
     

        // STAFF FIN - Finance
        $fin = Role::firstOrCreate(['name' => 'fin', 'guard_name' => 'web']);
       
        // STAFF REG ALKES - Regulatory & Alkes
        $regAlkes = Role::firstOrCreate(['name' => 'reg_alkes', 'guard_name' => 'web']);
       

        $this->command->info('✅ Created 9 roles with permissions');
    }
}