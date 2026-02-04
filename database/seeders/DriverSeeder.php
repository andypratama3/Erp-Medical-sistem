<?php

namespace Database\Seeders;

use App\Models\SCMDriver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'code' => 'DRV-001',
                'name' => 'Ahmad Supardi',
                'phone' => '081234567890',
                'license_number' => 'A-1234567890',
                'vehicle_type' => 'Truck',
                'vehicle_number' => 'B 1234 XYZ',
                'status' => 'active',
            ],
            [
                'code' => 'DRV-002',
                'name' => 'Budi Santoso',
                'phone' => '081234567891',
                'license_number' => 'B-0987654321',
                'vehicle_type' => 'Van',
                'vehicle_number' => 'B 5678 ABC',
                'status' => 'active',
            ],
            [
                'code' => 'DRV-003',
                'name' => 'Cahyo Prabowo',
                'phone' => '081234567892',
                'license_number' => 'C-1122334455',
                'vehicle_type' => 'Truck',
                'vehicle_number' => 'B 9012 DEF',
                'status' => 'active',
            ],
            [
                'code' => 'DRV-004',
                'name' => 'Dedi Kurniawan',
                'phone' => '081234567893',
                'license_number' => 'D-5566778899',
                'vehicle_type' => 'Pickup',
                'vehicle_number' => 'B 3456 GHI',
                'status' => 'active',
            ],
            [
                'code' => 'DRV-005',
                'name' => 'Eko Prasetyo',
                'phone' => '081234567894',
                'license_number' => 'E-9988776655',
                'vehicle_type' => 'Van',
                'vehicle_number' => 'B 7890 JKL',
                'status' => 'on_leave',
            ],
        ];

        foreach ($drivers as $driver) {
            SCMDriver::create($driver);
        }
    }
}
