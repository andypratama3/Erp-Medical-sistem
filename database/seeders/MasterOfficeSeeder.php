<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterOffice;

class MasterOfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            [
                'code' => 'JKT-01',
                'name' => 'Jakarta Head Office',
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'phone' => '021-5551234',
                'email' => 'jakarta@rmi.co.id',
                'status' => 'active',
            ],
            [
                'code' => 'BDG-01',
                'name' => 'Bandung Branch',
                'address' => 'Jl. Asia Afrika No. 45',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40111',
                'phone' => '022-4445678',
                'email' => 'bandung@rmi.co.id',
                'status' => 'active',
            ],
            [
                'code' => 'SBY-01',
                'name' => 'Surabaya Branch',
                'address' => 'Jl. Tunjungan No. 78',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60275',
                'phone' => '031-3339012',
                'email' => 'surabaya@rmi.co.id',
                'status' => 'active',
            ],
            [
                'code' => 'MKS-01',
                'name' => 'Makassar Branch',
                'address' => 'Jl. AP Pettarani No. 23',
                'city' => 'Makassar',
                'province' => 'Sulawesi Selatan',
                'postal_code' => '90221',
                'phone' => '0411-8881234',
                'email' => 'makassar@rmi.co.id',
                'status' => 'active',
            ],
            [
                'code' => 'MDN-01',
                'name' => 'Medan Branch',
                'address' => 'Jl. Gatot Subroto No. 56',
                'city' => 'Medan',
                'province' => 'Sumatera Utara',
                'postal_code' => '20115',
                'phone' => '061-7775678',
                'email' => 'medan@rmi.co.id',
                'status' => 'active',
            ],
        ];

        foreach ($offices as $office) {
            MasterOffice::create($office);
        }

        $this->command->info('✅ Created ' . count($offices) . ' offices');
    }
}
