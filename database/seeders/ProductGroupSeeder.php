<?php

namespace Database\Seeders;

use App\Models\ProductGroup;
use Illuminate\Database\Seeder;

class ProductGroupSeeder extends Seeder
{
    public function run(): void
    {
        $productGroups = [
            ['name' => 'SUCTION_CATHETER', 'code' => 'SC', 'description' => 'Suction Catheter Group', 'status' => 'active'],
            ['name' => 'SURGICAL_INSTRUMENTS', 'code' => 'SI', 'description' => 'Surgical Instruments Group', 'status' => 'active'],
            ['name' => 'DIAGNOSTIC_TOOLS', 'code' => 'DT', 'description' => 'Diagnostic Tools Group', 'status' => 'active'],
            ['name' => 'CONSUMABLES', 'code' => 'CONS', 'description' => 'Medical Consumables Group', 'status' => 'active'],
            ['name' => 'IMPLANTS', 'code' => 'IMP', 'description' => 'Medical Implants Group', 'status' => 'active'],
        ];

        foreach ($productGroups as $group) {
            ProductGroup::create($group);
        }
    }
}
