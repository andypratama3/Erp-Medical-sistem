<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductGroup;
use App\Models\Manufacture;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('code', 'ALKES-A')->first();
        $group = ProductGroup::where('code', 'DISPOSABLE')->first();
        $manufacture = Manufacture::where('code', 'MFG-001')->first();

        $products = [
            [
                'sku' => 'PRD-001',
                'name' => 'Disposable Syringe 3cc',
                'category_id' => $category->id,
                'product_group_id' => $group->id,
                'manufacture_id' => $manufacture->id,
                'unit' => 'PCS',
                'unit_price' => 2500,
                'description' => 'Syringe sekali pakai 3cc steril',
                'status' => 'active',
            ],
            [
                'sku' => 'PRD-002',
                'name' => 'Disposable Syringe 5cc',
                'category_id' => $category->id,
                'product_group_id' => $group->id,
                'manufacture_id' => $manufacture->id,
                'unit' => 'PCS',
                'unit_price' => 3000,
                'description' => 'Syringe sekali pakai 5cc steril',
                'status' => 'active',
            ],
            [
                'sku' => 'PRD-003',
                'name' => 'Surgical Gloves Size M',
                'category_id' => $category->id,
                'product_group_id' => $group->id,
                'manufacture_id' => $manufacture->id,
                'unit' => 'BOX',
                'unit_price' => 85000,
                'description' => 'Sarung tangan bedah steril ukuran M, isi 50 pasang',
                'status' => 'active',
            ],
            [
                'sku' => 'PRD-004',
                'name' => 'Face Mask 3 Ply',
                'category_id' => $category->id,
                'product_group_id' => $group->id,
                'manufacture_id' => $manufacture->id,
                'unit' => 'BOX',
                'unit_price' => 45000,
                'description' => 'Masker medis 3 lapis, isi 50pcs',
                'status' => 'active',
            ],
            [
                'sku' => 'PRD-005',
                'name' => 'IV Catheter 22G',
                'category_id' => $category->id,
                'product_group_id' => $group->id,
                'manufacture_id' => $manufacture->id,
                'unit' => 'PCS',
                'unit_price' => 8500,
                'description' => 'Kateter IV ukuran 22G steril',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✅ Created ' . count($products) . ' products');
    }
}
