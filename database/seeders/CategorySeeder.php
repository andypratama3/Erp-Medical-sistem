<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'BMHP', 'code' => 'BMHP', 'description' => 'Bahan Medis Habis Pakai', 'status' => 'active'],
            ['name' => 'Alat Kesehatan', 'code' => 'ALKES', 'description' => 'Alat Kesehatan Medis', 'status' => 'active'],
            ['name' => 'Obat-obatan', 'code' => 'OBT', 'description' => 'Obat dan Farmasi', 'status' => 'active'],
            ['name' => 'Alat Diagnostik', 'code' => 'DIAG', 'description' => 'Alat Diagnostik dan Laboratorium', 'status' => 'active'],
            ['name' => 'Peralatan Medis', 'code' => 'PRLTMED', 'description' => 'Peralatan Medis Umum', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
