<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'ALKES-A', 'name' => 'Alat Kesehatan Kelas A', 'description' => 'Alkes risiko rendah'],
            ['code' => 'ALKES-B', 'name' => 'Alat Kesehatan Kelas B', 'description' => 'Alkes risiko sedang'],
            ['code' => 'ALKES-C', 'name' => 'Alat Kesehatan Kelas C', 'description' => 'Alkes risiko tinggi'],
            ['code' => 'ALKES-D', 'name' => 'Alat Kesehatan Kelas D', 'description' => 'Alkes risiko sangat tinggi'],
            ['code' => 'PKRT', 'name' => 'Perbekalan Kesehatan Rumah Tangga', 'description' => 'PKRT'],
            ['code' => 'IVD', 'name' => 'In Vitro Diagnostic', 'description' => 'Alat diagnostik in vitro'],
            ['code' => 'ELEKTRO', 'name' => 'Elektromedik', 'description' => 'Peralatan elektromedik'],
            ['code' => 'IMAGING', 'name' => 'Medical Imaging', 'description' => 'Peralatan pencitraan medis'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ Created ' . count($categories) . ' categories');
    }
}
