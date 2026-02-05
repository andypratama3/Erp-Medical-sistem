<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('code', 'SBY')->first();

        $vehicles = [
            [
                'branch_id' => $branch->id,
                'plate_number' => 'B 1234 XYZ',
                'brand' => 'Mitsubishi',
                'model' => 'Colt Diesel',
                'year' => 2020,
                'color' => null,
                'capacity_weight' => 5000,
                'capacity_volume' => null,
                'fuel_type' => 'diesel',
                'driver_id' => null,
                'insurance_number' => null,
                'insurance_expiry' => null,
                'tax_expiry' => null,
                'last_service_date' => null,
                'next_service_date' => null,
                'odometer_reading' => null,
                'notes' => null,
                'status' => 'active',
            ],
            [
                'branch_id' => $branch->id,
                'plate_number' => 'B 5678 ABC',
                'brand' => 'Toyota',
                'model' => 'Hiace',
                'year' => 2021,
                'color' => null,
                'capacity_weight' => 1500,
                'capacity_volume' => null,
                'fuel_type' => 'diesel',
                'driver_id' => null,
                'insurance_number' => null,
                'insurance_expiry' => null,
                'tax_expiry' => null,
                'last_service_date' => null,
                'next_service_date' => null,
                'odometer_reading' => null,
                'notes' => null,
                'status' => 'active',
            ],
            [
                'branch_id' => $branch->id,
                'plate_number' => 'B 9012 DEF',
                'brand' => 'Isuzu',
                'model' => 'Elf',
                'year' => 2019,
                'color' => null,
                'capacity_weight' => 4000,
                'capacity_volume' => null,
                'fuel_type' => 'diesel',
                'driver_id' => null,
                'insurance_number' => null,
                'insurance_expiry' => null,
                'tax_expiry' => null,
                'last_service_date' => null,
                'next_service_date' => null,
                'odometer_reading' => null,
                'notes' => null,
                'status' => 'in_use',
            ],
            [
                'branch_id' => $branch->id,
                'plate_number' => 'B 3456 GHI',
                'brand' => 'Suzuki',
                'model' => 'Carry',
                'year' => 2022,
                'color' => null,
                'capacity_weight' => 800,
                'capacity_volume' => null,
                'fuel_type' => 'gasoline',
                'driver_id' => null,
                'insurance_number' => null,
                'insurance_expiry' => null,
                'tax_expiry' => null,
                'last_service_date' => null,
                'next_service_date' => null,
                'odometer_reading' => null,
                'notes' => null,
                'status' => 'active',
            ],
            [
                'branch_id' => $branch->id,
                'plate_number' => 'B 7890 JKL',
                'brand' => 'Daihatsu',
                'model' => 'Gran Max',
                'year' => 2021,
                'color' => null,
                'capacity_weight' => 1000,
                'capacity_volume' => null,
                'fuel_type' => 'gasoline',
                'driver_id' => null,
                'insurance_number' => null,
                'insurance_expiry' => null,
                'tax_expiry' => null,
                'last_service_date' => null,
                'next_service_date' => null,
                'odometer_reading' => null,
                'notes' => null,
                'status' => 'maintenance',
                ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
