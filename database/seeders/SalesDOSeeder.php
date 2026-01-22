<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesDO;
use App\Models\SalesDOItem;
use App\Models\Customer;
use App\Models\MasterOffice;
use App\Models\PaymentTerm;
use App\Models\Tax;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesDOSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::first();
        $office = MasterOffice::first();
        $paymentTerm = PaymentTerm::first();
        $tax = Tax::first();
        $user = User::first();
        $products = Product::take(3)->get();

        if (!$customer || !$office || !$user || $products->isEmpty()) {
            $this->command->warn('❌ Seeder dibatalkan: master data belum lengkap');
            return;
        }

        DB::beginTransaction();

        try {
            /* ============================
            CREATE SALES DO
            ============================ */
            $salesDO = SalesDO::create([
                'do_code' => 'DO-CRM-0001',
                'tracking_code' => 'DO-CRM-0001',
                'do_date' => Carbon::now()->toDateString(),
                'customer_id' => $customer->id,
                'office_id' => $office->id,
                'shipping_address' => 'Jl. Contoh Alamat Pengiriman No. 123, Jakarta',
                'pic_customer' => 'Budi Santoso',
                'payment_term_id' => $paymentTerm?->id,
                'tax_id' => $tax?->id,
                'subtotal' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'status' => 'crm_to_wqs',
                'notes_crm' => 'Seeder initial Sales DO',
                'created_by' => $user->id,
            ]);

            /* ============================
            CREATE ITEMS
            ============================ */
            $subtotal = 0;
            $lineNumber = 1;

            foreach ($products as $product) {
                $qty = rand(1, 5);
                $unitPrice = $product->unit_price ?? rand(50_000, 200_000);
                $discountPercent = rand(0, 10);

                $lineTotal = $qty * $unitPrice;
                $discountAmount = $lineTotal * ($discountPercent / 100);
                $finalLineTotal = $lineTotal - $discountAmount;

                SalesDOItem::create([
                    'sales_do_id' => $salesDO->id,
                    'product_id' => $product->id,
                    'line_number' => $lineNumber++,
                    'product_sku' => $product->sku,
                    'product_name' => $product->name,
                    'unit' => $product->unit ?? 'PCS',
                    'qty_ordered' => $qty,
                    'qty_delivered' => 0,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'line_total' => $finalLineTotal,
                ]);

                $subtotal += $finalLineTotal;
            }

            /* ============================
            UPDATE TOTALS
            ============================ */
            $taxAmount = 0;
            if ($tax) {
                $taxAmount = $subtotal * ($tax->rate / 100);
            }

            $salesDO->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $subtotal + $taxAmount,
            ]);

            DB::commit();

            $this->command->info('✅ Sales DO & Items seeded successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('❌ Seeder gagal: ' . $e->getMessage());
        }
    }
}
