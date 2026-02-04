<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core System
            PermissionSeeder::class,
            RoleSeeder::class,

            // Master Data
            BranchSeeder::class,
            MasterOfficeSeeder::class,
            MasterDepartmentSeeder::class,
            CategorySeeder::class,
            ProductGroupSeeder::class,
            ManufactureSeeder::class,
            PaymentTermSeeder::class,
            TaxSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            VendorSeeder::class,

            // Users & Access
            UserSeeder::class,

            // Operational Data
            SalesDOSeeder::class,
            StockCheckSeeder::class,
            VehicleSeeder::class,
            DriverSeeder::class,
            DeliverySeeder::class,
            InvoiceSeeder::class,
            CollectionSeeder::class,
            PaymentSeeder::class,

            // Additional Data
            RegAlkesCaseSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
