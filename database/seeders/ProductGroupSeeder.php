<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductGroup;
use Illuminate\Database\Seeder;

class ProductGroupSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first();

        $groups = [
            ['code' => 'SURGICAL', 'name' => 'Surgical Instruments', 'description' => 'Instrumen bedah','category_id' => $category->id],
            ['code' => 'DIAGNOSTIC', 'name' => 'Diagnostic Equipment', 'description' => 'Peralatan diagnostik','category_id' => $category->id],
            ['code' => 'THERAPY', 'name' => 'Therapy Equipment', 'description' => 'Peralatan terapi','category_id' => $category->id],
            ['code' => 'DENTAL', 'name' => 'Dental Equipment', 'description' => 'Peralatan dental','category_id' => $category->id],
            ['code' => 'LAB', 'name' => 'Laboratory Equipment', 'description' => 'Peralatan laboratorium','category_id' => $category->id],
            ['code' => 'DISPOSABLE', 'name' => 'Disposable Products', 'description' => 'Produk sekali pakai','category_id' => $category->id],
            ['code' => 'IMPLANT', 'name' => 'Implants', 'description' => 'Implan medis','category_id' => $category->id],
            ['code' => 'REHAB', 'name' => 'Rehabilitation Equipment', 'description' => 'Peralatan rehabilitasi','category_id' => $category->id],
        ];

        foreach ($groups as $group) {
            ProductGroup::create($group);
        }

        $this->command->info('✅ Created ' . count($groups) . ' product groups');
    }
}
