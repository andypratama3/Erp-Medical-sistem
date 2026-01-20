<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Manufacture;
use App\Models\ProductGroup;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $bmhp = Category::create([
            'name' => 'BMHP',
            'code' => 'BMHP-001',
            'description' => 'Barang Material Habis Pakai',
            'status' => 'active',
        ]);

        $alkes = Category::create([
            'name' => 'Alat Kesehatan',
            'code' => 'ALK-001',
            'description' => 'Alat Kesehatan Medis',
            'status' => 'active',
        ]);

        // Create Manufactures
        $manufacture1 = Manufacture::create([
            'name' => 'PT. Medical Supplier Indonesia',
            'code' => 'MSI-001',
            'country' => 'Indonesia',
            'email' => 'info@medicalsupplier.co.id',
            'phone' => '021-12345678',
            'address' => 'Jakarta, Indonesia',
            'status' => 'active',
        ]);

        $manufacture2 = Manufacture::create([
            'name' => 'Global Medical Equipment',
            'code' => 'GME-001',
            'country' => 'Singapore',
            'email' => 'contact@globalmed.sg',
            'phone' => '+65-98765432',
            'address' => 'Singapore',
            'status' => 'active',
        ]);

        // Create Product Groups
        $suctionGroup = ProductGroup::create([
            'name' => 'SUCTION_CATHETER',
            'code' => 'SC-001',
            'description' => 'Suction Catheter Products',
            'status' => 'active',
        ]);

        // Create Sample Products
        Product::create([
            'sku' => 'ALK-001',
            'name' => 'Alkes A',
            'type' => 'SINGLE',
            'unit' => 'unit',
            'barcode' => null,
            'manufacture_id' => $manufacture1->id,
            'category_id' => $alkes->id,
            'product_group_id' => $suctionGroup->id,
            'stock_qty' => 100,
            'current_stock' => 100,
            'akl_akd' => 'AKL.123456789',
            'akl_reg_no' => 'AKL.12345678901',
            'status' => 'active',
        ]);

        Product::create([
            'sku' => 'OBT-002',
            'name' => 'Obat B',
            'type' => 'SINGLE',
            'unit' => 'unit',
            'barcode' => null,
            'manufacture_id' => $manufacture2->id,
            'category_id' => $bmhp->id,
            'product_group_id' => null,
            'stock_qty' => 50,
            'current_stock' => 50,
            'status' => 'active',
        ]);

        Product::create([
            'sku' => 'OBT-001',
            'name' => 'Obat A',
            'type' => 'SINGLE',
            'unit' => 'unit',
            'barcode' => '8991234567890',
            'manufacture_id' => $manufacture1->id,
            'category_id' => $bmhp->id,
            'product_group_id' => null,
            'stock_qty' => 75,
            'current_stock' => 75,
            'akl_akd' => 'AKL.12345678901',
            'status' => 'active',
        ]);
    }
}
