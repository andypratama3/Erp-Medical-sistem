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
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
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
            SalesDOSeeder::class,
        ]);

        $this->command->info('✅ All seeders completed successfully!');
    }
}
