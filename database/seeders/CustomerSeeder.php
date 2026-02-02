<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\PaymentTerm;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $paymentTerm = PaymentTerm::first();
        $branch = Branch::first();

        $customers = [
            [
                'code' => 'CUST-001',
                'branch_id' => $branch->id,
                'name' => 'RS Siloam Hospitals',
                'legal_name' => 'PT. Siloam Hospitals',
                'npwp' => '01.123.456.7-900',
                'address' => 'Jl. Garnisun Dalam No. 2-3',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
                'phone' => '021-3006-5200',
                'mobile' => '0812-3456-7890',
                'email' => 'procurement@siloamhospitals.com',
                'contact_person' => 'Dr. Budi Santoso',
                'contact_phone' => '0812-3456-7890',
                'payment_term_id' => $paymentTerm?->id,
                'credit_limit' => 10000000,
                'credit_days' => 30,
                'customer_type' => 'hospital',
                'status' => 'active',
                'notes' => 'Customer resmi RS Siloam Hospitals',
            ],
            [
                'code' => 'CUST-002',
                'branch_id' => $branch->id,
                'name' => 'RS Mayapada Hospital',
                'legal_name' => 'PT. Mayapada Hospital',
                'npwp' => '01.123.456.7-901',
                'address' => 'Jl. Lebak Bulus I Kav. 29',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12440',
                'phone' => '021-2921-7777',
                'mobile' => '0813-8765-4321',
                'email' => 'purchasing@mayapadahospital.com',
                'contact_person' => 'Ibu Siti Rahayu',
                'contact_phone' => '0813-8765-4321',
                'payment_term_id' => $paymentTerm?->id,
                'credit_limit' => 10000000,
                'credit_days' => 30,
                'customer_type' => 'hospital',
                'status' => 'active',
                'notes' => 'Customer Resmi RS Mayapada Hospital',
            ],
            [
                'code' => 'CUST-003',
                'branch_id' => $branch->id,
                'name' => 'RSUD Tarakan',
                'legal_name' => 'PT. UD Tarakan',
                'npwp' => '01.123.456.7-902',
                'address' => 'Jl. Kyai Caringin No. 7',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10440',
                'phone' => '021-4247-0808',
                'mobile' => '0816-1234-5678',
                'email' => 'logistik@rsudtarakan.go.id',
                'contact_person' => 'Dr. Ahmad Fauzi',
                'contact_phone' => '0816-1234-5678',
                'payment_term_id' => $paymentTerm?->id,
                'credit_limit' => 10000000,
                'credit_days' => 30,
                'customer_type' => 'clinic',
                'status' => 'active',
                'notes' => 'Customer Resmi RSUD Tarakan',
            ],
            [
                'code' => 'CUST-004',
                'branch_id' => $branch->id,
                'name' => 'Klinik Kimia Farma',
                'legal_name' => 'PT. Kimia Farma',
                'npwp' => '01.123.456.7-903',
                'address' => 'Jl. Veteran No. 9',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
                'phone' => '021-3841-923',
                'mobile' => '0817-2345-6789',
                'email' => 'purchasing@kimiafarma.co.id',
                'contact_person' => 'Ibu Rina Melati',
                'contact_phone' => '0817-2345-6789',
                'payment_term_id' => $paymentTerm?->id,
                'credit_limit' => 10000000,
                'credit_days' => 30,
                'customer_type' => 'clinic',
                'status' => 'active',
                'notes' => 'Customer Resmi Klinik Kimia Farma',
            ],
            [
                'code' => 'CUST-005',
                'branch_id' => $branch->id,
                'name' => 'PT. Indomobil Sukses',
                'legal_name' => 'PT. Indomobil Sukses',
                'npwp' => '01.123.456.7-904',
                'address' => 'Jl. Raya Mangga Dua No. 8',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12980',
                'phone' => '021-2960-0000',
                'mobile' => '0818-1234-5670',
                'email' => 'procurement@indomobil.co.id',
                'contact_person' => 'Dr. Arief Budiman',
                'contact_phone' => '0818-1234-5670',
                'payment_term_id' => $paymentTerm?->id,
                'credit_limit' => 10000000,
                'credit_days' => 30,
                'customer_type' => 'clinic',
                'status' => 'active',
                'notes' => 'Customer Resmi PT. Indomobil Sukses',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}

