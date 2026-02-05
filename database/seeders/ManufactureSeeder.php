<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Manufacture;

class ManufactureSeeder extends Seeder
{
    public function run(): void
    {
        
        $manufactures = [
            [
                'code' => 'MFG-001',
                'name' => 'PT. Medika Sejahtera',
                'country' => 'Indonesia',
                'address' => 'Jl. Industri Raya No. 45, Jakarta',
                'city' => 'Jakarta',
                'phone' => '021-8881234',
                'email' => 'info@medikasejahtera.co.id',
                'status' => 'active',
            ],
            [
                'code' => 'MFG-002',
                'name' => 'Johnson & Johnson Medical',
                'country' => 'United States',
                'address' => '1 Johnson & Johnson Plaza, New Brunswick',
                'city' => 'New Jersey',
                'phone' => '+1-732-524-0400',
                'email' => 'jnj@medical.com',
                'status' => 'active',
            ],
            [
                'code' => 'MFG-003',
                'name' => 'Siemens Healthineers',
                'country' => 'Germany',
                'address' => 'Henkestraße 127, Erlangen',
                'city' => 'Bavaria',
                'phone' => '+49-9131-84-0',
                'email' => 'info@siemens-healthineers.com',
                'status' => 'active',
            ],
            [
                'code' => 'MFG-004',
                'name' => 'GE Healthcare',
                'country' => 'United States',
                'address' => '3000 N Grandview Blvd, Waukesha',
                'city' => 'Wisconsin',
                'phone' => '+1-262-544-3011',
                'email' => 'contact@ge.com',
                'status' => 'active',
            ],
            [
                'code' => 'MFG-005',
                'name' => 'Philips Healthcare',
                'country' => 'Netherlands',
                'address' => 'Amstelplein 2, Amsterdam',
                'city' => 'Amsterdam',
                'phone' => '+31-20-5977-977',
                'email' => 'info@philips.com',
                'status' => 'active',
            ],
            [
                'code' => 'MFG-006',
                'name' => 'Mindray Medical',
                'country' => 'China',
                'address' => 'Mindray Building, Keji 12th Road South',
                'city' => 'Shenzhen',
                'phone' => '+86-755-8188-8998',
                'email' => 'service@mindray.com',
                'status' => 'active',
            ],
            [
                'code' => 'MFG-007',
                'name' => 'B. Braun Melsungen',
                'country' => 'Germany',
                'address' => 'Carl-Braun-Straße 1, Melsungen',
                'city' => 'Hesse',
                'phone' => '+49-5661-71-0',
                'email' => 'info@bbraun.com',
                'status' => 'active',
            ],
            [
                'code' => 'MFG-008',
                'name' => 'Terumo Corporation',
                'country' => 'Japan',
                'address' => '44-1, 2-chome, Hatagaya, Shibuya-ku',
                'city' => 'Tokyo',
                'phone' => '+81-3-3374-8111',
                'email' => 'contact@terumo.co.jp',
                'status' => 'active',
            ],
        ];

        foreach ($manufactures as $manufacture) {
            Manufacture::create($manufacture);
        }

        $this->command->info('✅ Created ' . count($manufactures) . ' manufactures');
    }
}
