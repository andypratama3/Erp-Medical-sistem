<?php

namespace Database\Seeders;

use App\Models\WQSStockCheck;
use App\Models\WQSStockCheckItem;
use App\Models\SalesDO;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockCheckSeeder extends Seeder
{
    public function run(): void
    {
        $salesDOs = SalesDO::whereIn('status', ['crm_to_wqs', 'wqs_ready'])->get();
        $products = Product::where('status', 'active')->get();
        $users = User::all();

        if ($salesDOs->isEmpty() || $products->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Skipping StockCheckSeeder - missing required data');
            return;
        }

        foreach ($salesDOs->take(10) as $index => $salesDO) {
            $stockCheck = WQSStockCheck::create([
                'sales_do_id' => $salesDO->id,
                'check_date' => now()->subDays(rand(1, 30)),
                'overall_status' => collect(['pending', 'checked', 'completed', 'failed'])->random(),
                'check_notes' => $index % 2 == 0 ? 'Stock check in progress' : null,
                'notes' => rand(0, 1) ? 'Additional notes' : null,
                'checked_by' => $users->random()->id,
                'completed_at' => rand(0, 1) ? now()->subDays(rand(0, 10)) : null,
            ]);

            // Create stock check items
            $itemCount = rand(3, 8);
            foreach (range(1, $itemCount) as $itemIndex) {
                $product = $products->random();
                $expectedQty = rand(10, 100);
                $actualQty = rand(0, 120);

                WQSStockCheckItem::create([
                    'stock_check_id' => $stockCheck->id,
                    'product_id' => $product->id,
                    'stock_status' => abs($actualQty - $expectedQty) > 5 ? 'not_available' : ($actualQty > 0 ? 'partial' : 'available'),
                    'available_qty' => $actualQty,
                    'notes' => abs($actualQty - $expectedQty) > 5 ? 'Variance detected' : null,
                ]);
            }
        }
    }
}
