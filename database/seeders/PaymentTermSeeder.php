<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentTerm;

class PaymentTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = [
            [
                'code' => 'COD',
                'name' => 'Cash On Delivery',
                'days' => 0,
                'description' => 'Pembayaran tunai saat barang diterima',
                'status' => 'active',
            ],
            [
                'code' => 'NET-7',
                'name' => 'Net 7 Days',
                'days' => 7,
                'description' => 'Pembayaran maksimal 7 hari',
                'status' => 'active',
            ],
            [
                'code' => 'NET-14',
                'name' => 'Net 14 Days',
                'days' => 14,
                'description' => 'Pembayaran maksimal 14 hari',
                'status' => 'active',
            ],
            [
                'code' => 'NET-30',
                'name' => 'Net 30 Days',
                'days' => 30,
                'description' => 'Pembayaran maksimal 30 hari',
                'status' => 'active',
            ],
            [
                'code' => 'NET-60',
                'name' => 'Net 60 Days',
                'days' => 60,
                'description' => 'Pembayaran maksimal 60 hari',
                'status' => 'inactive',
            ],
        ];

        foreach ($terms as $term) {
            PaymentTerm::updateOrCreate(
                ['code' => $term['code']],
                $term
            );
        }
    }
}
