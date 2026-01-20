<?php

namespace Database\Seeders;

use App\Models\Manufacture;
use Illuminate\Database\Seeder;

class ManufactureSeeder extends Seeder
{
    public function run(): void
    {
        $manufactures = [
            [
                'name' => 'PT. Kimia Farma',
                'code' => 'KF',
                'country' => 'Indonesia',
                'email' => 'info@kimiafarma.co.id',
                'phone' => '021-4892808',
                'address' => 'Jakarta, Indonesia',
                'status' => 'active'
            ],
            [
                'name' => 'PT. Kalbe Farma',
                'code' => 'KALBE',
                'country' => 'Indonesia',
                'email' => 'info@kalbe.co.id',
                'phone' => '021-4263333',
                'address' => 'Jakarta, Indonesia',
                'status' => 'active'
            ],
            [
                'name' => 'PT. Sanbe Farma',
                'code' => 'SANBE',
                'country' => 'Indonesia',
                'email' => 'info@sanbe.co.id',
                'phone' => '022-5201234',
                'address' => 'Bandung, Indonesia',
                'status' => 'active'
            ],
            [
                'name' => 'Johnson & Johnson',
                'code' => 'JNJ',
                'country' => 'USA',
                'email' => 'contact@jnj.com',
                'phone' => '+1-732-524-0400',
                'address' => 'New Jersey, USA',
                'status' => 'active'
            ],
            [
                'name' => 'Siemens Healthineers',
                'code' => 'SIEMENS',
                'country' => 'Germany',
                'email' => 'info@siemens-healthineers.com',
                'phone' => '+49-9131-84-0',
                'address' => 'Erlangen, Germany',
                'status' => 'active'
            ],
        ];

        foreach ($manufactures as $manufacture) {
            Manufacture::create($manufacture);
        }
    }
}
