<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tax;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            [
                'code' => 'PPN-11',
                'name' => 'PPN 11%',
                'rate' => 11.00,
                'description' => 'Pajak Pertambahan Nilai 11%',
                'status' => 'active',
            ],
            [
                'code' => 'PPN-12',
                'name' => 'PPN 12%',
                'rate' => 12.00,
                'description' => 'Pajak Pertambahan Nilai 12%',
                'status' => 'inactive',
            ],
            [
                'code' => 'PPH-21',
                'name' => 'PPh Pasal 21',
                'rate' => 5.00,
                'description' => 'Pajak Penghasilan Pasal 21',
                'status' => 'active',
            ],
            [
                'code' => 'PPH-23',
                'name' => 'PPh Pasal 23',
                'rate' => 2.00,
                'description' => 'Pajak Penghasilan Pasal 23',
                'status' => 'active',
            ],
            [
                'code' => 'NON-TAX',
                'name' => 'Non Pajak',
                'rate' => 0.00,
                'description' => 'Tidak dikenakan pajak',
                'status' => 'active',
            ],
        ];

        foreach ($taxes as $tax) {
            Tax::updateOrCreate(
                ['code' => $tax['code']],
                $tax
            );
        }
    }
}
