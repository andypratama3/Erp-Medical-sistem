<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\PaymentTerm;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTerm = PaymentTerm::first();

        $vendors = [
            [
                'code' => 'VEND-001',
                'name' => 'PT Kimia Farma Trading & Distribution',
                'legal_name' => 'PT Kimia Farma Trading & Distribution',
                'npwp' => '01.234.567.8-901',
                'address' => 'Jl. Industri No. 10',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10610',
                'phone' => '021-3841234',
                'email' => 'sales@kftd.co.id',
                'contact_person' => 'Andi Wijaya',
                'contact_phone' => '0812-1111-2222',
                'payment_term_id' => $paymentTerm?->id,
                'vendor_type' => 'distributor',
                'status' => 'active',
                'notes' => 'Distributor resmi produk farmasi',
            ],
            [
                'code' => 'VEND-002',
                'name' => 'PT Kalbe Farma Tbk',
                'legal_name' => 'PT Kalbe Farma Tbk',
                'npwp' => '01.234.567.8-902',
                'address' => 'Jl. Let. Jend Suprapto Kav. 4',
                'city' => 'Jakarta Timur',
                'province' => 'DKI Jakarta',
                'postal_code' => '10510',
                'phone' => '021-42873888',
                'email' => 'procurement@kalbe.co.id',
                'contact_person' => 'Rina Saputri',
                'contact_phone' => '0813-2222-3333',
                'payment_term_id' => $paymentTerm?->id,
                'vendor_type' => 'manufacturer',
                'status' => 'active',
                'notes' => 'Produsen obat nasional',
            ],
            [
                'code' => 'VEND-003',
                'name' => 'PT Indofarma Global Medika',
                'legal_name' => 'PT Indofarma Global Medika',
                'npwp' => '01.234.567.8-903',
                'address' => 'Jl. Indofarma No. 1',
                'city' => 'Bekasi',
                'province' => 'Jawa Barat',
                'postal_code' => '17530',
                'phone' => '021-88398765',
                'email' => 'sales@indofarma.co.id',
                'contact_person' => 'Budi Santoso',
                'contact_phone' => '0814-3333-4444',
                'payment_term_id' => $paymentTerm?->id,
                'vendor_type' => 'manufacturer',
                'status' => 'active',
                'notes' => 'Vendor BUMN farmasi',
            ],
            [
                'code' => 'VEND-004',
                'name' => 'CV Sumber Medika',
                'legal_name' => 'CV Sumber Medika',
                'npwp' => '01.234.567.8-904',
                'address' => 'Jl. Raya Bogor KM 26',
                'city' => 'Depok',
                'province' => 'Jawa Barat',
                'postal_code' => '16426',
                'phone' => '021-77889900',
                'email' => 'admin@sumbermedika.co.id',
                'contact_person' => 'Dewi Lestari',
                'contact_phone' => '0815-4444-5555',
                'payment_term_id' => null,
                'vendor_type' => 'supplier',
                'status' => 'inactive',
                'notes' => 'Vendor alat kesehatan skala menengah',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(
                ['code' => $vendor['code']],
                $vendor
            );
        }
    }
}
