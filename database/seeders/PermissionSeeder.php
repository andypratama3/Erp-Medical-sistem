<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'view_dashboard', 'guard_name'=> 'web'],
            ['name' => 'view_analytics', 'guard_name'=> 'web'],
            ['name' => 'export_dashboard_data', 'guard_name'=> 'web'],

            // Permissions Management
            ['name' => 'view_permissions', 'guard_name'=> 'web'],
            ['name' => 'create_permission', 'guard_name'=> 'web'],
            ['name' => 'edit_permission', 'guard_name'=> 'web'],
            ['name' => 'delete_permission', 'guard_name'=> 'web'],

            // Roles Management
            ['name' => 'view_roles', 'guard_name'=> 'web'],
            ['name' => 'create_role', 'guard_name'=> 'web'],
            ['name' => 'edit_role', 'guard_name'=> 'web'],
            ['name' => 'delete_role', 'guard_name'=> 'web'],
            ['name' => 'assign_roles', 'guard_name'=> 'web'],

            // Users Management
            ['name' => 'view_users', 'guard_name'=> 'web'],
            ['name' => 'create_user', 'guard_name'=> 'web'],
            ['name' => 'edit_user', 'guard_name'=> 'web'],
            ['name' => 'delete_user', 'guard_name'=> 'web'],
            ['name' => 'activate_user', 'guard_name'=> 'web'],
            ['name' => 'deactivate_user', 'guard_name'=> 'web'],

            // Datasets Management
            ['name' => 'view_datasets', 'guard_name'=> 'web'],
            ['name' => 'create_dataset', 'guard_name'=> 'web'],
            ['name' => 'edit_dataset', 'guard_name'=> 'web'],
            ['name' => 'delete_dataset', 'guard_name'=> 'web'],
            ['name' => 'import_dataset', 'guard_name'=> 'web'],
            ['name' => 'export_dataset', 'guard_name'=> 'web'],
            ['name' => 'download_dataset_template', 'guard_name'=> 'web'],

            // Maps/GIS
            ['name' => 'view_maps', 'guard_name'=> 'web'],
            ['name' => 'edit_maps', 'guard_name'=> 'web'],
            ['name' => 'manage_boundaries', 'guard_name'=> 'web'],
            ['name' => 'export_map_data', 'guard_name'=> 'web'],

            // Fertilizer Transactions
            ['name' => 'view_transactions', 'guard_name'=> 'web'],
            ['name' => 'create_transaction', 'guard_name'=> 'web'],
            ['name' => 'edit_transaction', 'guard_name'=> 'web'],
            ['name' => 'delete_transaction', 'guard_name'=> 'web'],
            ['name' => 'approve_transaction', 'guard_name'=> 'web'],
            ['name' => 'reject_transaction', 'guard_name'=> 'web'],

            // Products Management (Future ERP Module)
            ['name' => 'view_products', 'guard_name'=> 'web'],
            ['name' => 'create_product', 'guard_name'=> 'web'],
            ['name' => 'edit_product', 'guard_name'=> 'web'],
            ['name' => 'delete_product', 'guard_name'=> 'web'],
            ['name' => 'manage_inventory', 'guard_name'=> 'web'],

            // Reports & Analytics
            ['name' => 'view_reports', 'guard_name'=> 'web'],
            ['name' => 'create_report', 'guard_name'=> 'web'],
            ['name' => 'export_reports', 'guard_name'=> 'web'],
            ['name' => 'view_audit_logs', 'guard_name'=> 'web'],

            // Settings
            ['name' => 'manage_settings', 'guard_name'=> 'web'],
            ['name' => 'manage_system_config', 'guard_name'=> 'web'],
            ['name' => 'view_system_logs', 'guard_name'=> 'web'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']]
            );
        }
    }
}
