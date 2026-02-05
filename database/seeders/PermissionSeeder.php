<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create Permissions for Dashboard
        Permission::firstOrCreate(['name' => 'view-dashboard']);

        // Create Permissions for Data Master
        Permission::firstOrCreate(['name' => 'view-master-data']);
        Permission::firstOrCreate(['name' => 'view-branches']);
        Permission::firstOrCreate(['name' => 'view-offices']);
        Permission::firstOrCreate(['name' => 'view-departments']);
        Permission::firstOrCreate(['name' => 'view-customers']);
        Permission::firstOrCreate(['name' => 'view-vendors']);
        Permission::firstOrCreate(['name' => 'view-manufactures']);
        Permission::firstOrCreate(['name' => 'view-products']);
        Permission::firstOrCreate(['name' => 'view-taxes']);
        Permission::firstOrCreate(['name' => 'view-payment-terms']);

        // Create Permissions for CRM
        Permission::firstOrCreate(['name' => 'view-crm']);
        Permission::firstOrCreate(['name' => 'view-sales']);
        Permission::firstOrCreate(['name' => 'view-customers']);
        Permission::firstOrCreate(['name' => 'view-vendors']);
        Permission::firstOrCreate(['name' => 'create-sales']);
        Permission::firstOrCreate(['name' => 'edit-sales']);
        Permission::firstOrCreate(['name' => 'delete-sales']);


        // Create Permissions for WQS
        Permission::firstOrCreate(['name' => 'view-wqs']);
        Permission::firstOrCreate(['name' => 'view-wqs-tasks']);
        Permission::firstOrCreate(['name' => 'view-stock-checks']);
        Permission::firstOrCreate(['name' => 'view-inventory']);

        // Create Permissions for SCM
        Permission::firstOrCreate(['name' => 'view-scm']);
        Permission::firstOrCreate(['name' => 'view-drivers']);
        Permission::firstOrCreate(['name' => 'view-scm-tasks']);
        Permission::firstOrCreate(['name' => 'view-deliveries']);
        Permission::firstOrCreate(['name' => 'view-vehicles']);

        // Create Permissions for ACT
        Permission::firstOrCreate(['name' => 'view-act']);
        Permission::firstOrCreate(['name' => 'view-act-tasks']);
        Permission::firstOrCreate(['name' => 'view-invoices']);

        // Create Permissions for FIN
        Permission::firstOrCreate(['name' => 'view-fin']);
        Permission::firstOrCreate(['name' => 'view-fin-tasks']);
        Permission::firstOrCreate(['name' => 'view-collections']);
        Permission::firstOrCreate(['name' => 'view-aging']);

        // Create Permissions for Settings
        Permission::firstOrCreate(['name' => 'manage-users']);
        Permission::firstOrCreate(['name' => 'manage-roles']);
        Permission::firstOrCreate(['name' => 'manage-permissions']);

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $salesRole = Role::firstOrCreate(['name' => 'sales']);
        $warehouseRole = Role::firstOrCreate(['name' => 'warehouse']);
        $logisticsRole = Role::firstOrCreate(['name' => 'logistics']);
        $accountingRole = Role::firstOrCreate(['name' => 'accounting']);
        $financeRole = Role::firstOrCreate(['name' => 'finance']);

        // Assign All Permissions to Admin
        $adminPermissions = Permission::all();
        $adminRole->syncPermissions($adminPermissions);

        // Assign Permissions to Manager
        $managerPermissions = [
            'view-dashboard',
            'view-master-data',
            'view-branches',
            'view-offices',
            'view-departments',
            'view-customers',
            'view-vendors',
            'view-manufactures',
            'view-products',
            'view-taxes',
            'view-payment-terms',
            'view-crm',
            'view-sales',
            'view-wqs',
            'view-wqs-tasks',
            'view-stock-checks',
            'view-inventory',
            'view-scm',
            'view-drivers',
            'view-scm-tasks',
            'view-deliveries',
            'view-vehicles',
            'view-act',
            'view-act-tasks',
            'view-invoices',
            'view-fin',
            'view-fin-tasks',
            'view-collections',
            'view-aging',
        ];
        $managerRole->syncPermissions($managerPermissions);

        // Assign Permissions to Sales
        $salesPermissions = [
            'view-dashboard',
            'view-master-data',
            'view-crm',
            'view-sales',
            'view-customers',
            'create-sales',
            'edit-sales',
            'delete-sales',
        ];
        $salesRole->syncPermissions($salesPermissions);

        // Assign Permissions to Warehouse
        $warehousePermissions = [
            'view-dashboard',
            'view-wqs',
            'view-wqs-tasks',
            'view-stock-checks',
            'view-inventory',
            'view-products',
        ];
        $warehouseRole->syncPermissions($warehousePermissions);

        // Assign Permissions to Logistics
        $logisticsPermissions = [
            'view-dashboard',
            'view-scm',
            'view-drivers',
            'view-scm-tasks',
            'view-deliveries',
            'view-vehicles',
        ];
        $logisticsRole->syncPermissions($logisticsPermissions);

        // Assign Permissions to Accounting
        $accountingPermissions = [
            'view-dashboard',
            'view-act',
            'view-act-tasks',
            'view-invoices',
        ];
        $accountingRole->syncPermissions($accountingPermissions);

        // Assign Permissions to Finance
        $financePermissions = [
            'view-dashboard',
            'view-fin',
            'view-fin-tasks',
            'view-collections',
            'view-aging',
        ];
        $financeRole->syncPermissions($financePermissions);
    }
}