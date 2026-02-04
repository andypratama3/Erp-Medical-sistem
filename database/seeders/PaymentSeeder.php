<?php

namespace Database\Seeders;

use App\Models\FINPayment;
use App\Models\FINCollection;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $collections = FINCollection::whereIn('collection_status', ['partial', 'completed'])->get();
        $users = User::all();

        if ($collections->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Skipping PaymentSeeder - missing required data');
            return;
        }

        foreach ($collections->take(12) as $index => $collection) {
            $paymentMethods = ['cash', 'transfer', 'check', 'giro', 'other'];
            $method = $paymentMethods[array_rand($paymentMethods)];

            FINPayment::create([
                'sales_do_id' => $collection->sales_do_id,
                'collection_id' => $collection->id,
                'payment_number' => 'PAY-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'payment_date' => now()->subDays(rand(1, 30)),
                'payment_amount' => $collection->amount_collected,
                'payment_method' => $method,
                'bank_name' => in_array($method, ['transfer', 'check', 'giro']) ?
                              ['BCA', 'Mandiri', 'BNI', 'BRI'][array_rand(['BCA', 'Mandiri', 'BNI', 'BRI'])] : null,
                'account_number' => in_array($method, ['transfer', 'check', 'giro']) ?
                                   rand(1000000000, 9999999999) : null,
                'reference_number' => 'REF-' . rand(100000, 999999),
                'notes' => rand(0, 1) ? 'Payment received and verified' : null,
                'recorded_by' => $users->random()->id,
            ]);
        }
    }
}
