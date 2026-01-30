<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core seeders - harus dijalankan pertama
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            BranchSeeder::class, // Branch seeder harus setelah User

            // Master data seeders
            MasterOfficeSeeder::class,
            MasterDepartmentSeeder::class,
            CategorySeeder::class,
            ProductGroupSeeder::class,
            ManufactureSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            VendorSeeder::class,
            TaxSeeder::class,
            PaymentTermSeeder::class,

            // Transaction seeders
            SalesDOSeeder::class,
        ]);

        $this->command->info('✅ All seeders completed successfully!');
    }
}
