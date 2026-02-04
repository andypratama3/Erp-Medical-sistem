<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Manufacture;
use Illuminate\Support\Str;
use App\Models\RegAlkesCase;
use Illuminate\Database\Seeder;
use App\Models\RegAlkesCaseItem;

class RegAlkesCaseSeeder extends Seeder
{
    public function run(): void
    {
        $manufactures = Manufacture::all();
        $products = Product::all();
        $users = User::all();

        if ($manufactures->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Skipping RegAlkesCaseSeeder - missing required data');
            return;
        }

        foreach (range(1, 10) as $i) {
            $manufacture = $manufactures->random();

            $case = RegAlkesCase::create([
                'branch_id'            => Branch::first()->id,
                'case_number'       => 'ALKES-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'manufacture_id'    => $manufacture->id,
                'manufacture_name'  => $manufacture->name,
                'country_of_origin' => $manufacture->country ?? 'Indonesia',
                'case_type'         => collect(['pqp', 'hrl', 'renewal'])->random(),
                'submission_date'   => now()->subDays(rand(5, 60)),
                'target_date'       => now()->addDays(rand(30, 120)),
                'nie_issued_date'   => rand(0, 1) ? now()->subDays(rand(1, 20)) : null,
                'nie_number'        => rand(0, 1) ? 'NIE-' . Str::upper(Str::random(8)) : null,
                'case_status'       => collect([
                    'case_draft',
                    'case_submitted',
                    'waiting_nie',
                    'nie_issued',
                    'sku_imported',
                    'sku_active',
                ])->random(),
                'notes'             => rand(0, 1) ? 'Dummy alkes registration case' : null,
                'total_skus'        => 0,
                'imported_skus'     => 0,
                'active_skus'       => 0,
                'created_by'        => $users->random()->id,
                'updated_by'        => $users->random()->id,
            ]);

            /** ===============================
             *  CREATE CASE ITEMS
             *  =============================== */
            $itemsCount = rand(2, 6);
            $activeCount = 0;

            foreach (range(1, $itemsCount) as $j) {
                $product = $products->isNotEmpty() ? $products->random() : null;
                $status = collect(['pending', 'registered', 'active', 'expired'])->random();

                if ($status === 'active') {
                    $activeCount++;
                }

                RegAlkesCaseItem::create([
                    'branch_id'            => Branch::first()->id,
                    'case_id'           => $case->id,
                    'product_id'        => $product?->id,
                    'product_name'      => $product?->name ?? 'Medical Device ' . $j,
                    'catalog_number'    => rand(0, 1) ? 'CAT-' . rand(1000, 9999) : null,
                    'akl_akd_number'    => rand(0, 1) ? 'AKL-' . rand(100000, 999999) : null,
                    'akl_akd_expiry'    => rand(0, 1) ? now()->addYears(rand(1, 5)) : null,
                    'registration_type' => collect(['AKL', 'AKD'])->random(),
                    'item_status'       => $status,
                    'notes'             => null,
                ]);
            }

            /** ===============================
             *  UPDATE COUNTER DI CASE
             *  =============================== */
            $case->update([
                'total_skus'    => $itemsCount,
                'imported_skus' => $itemsCount, // asumsi semua sudah di-import
                'active_skus'   => $activeCount,
            ]);
        }
    }
}
