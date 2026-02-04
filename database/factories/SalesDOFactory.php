<?php

namespace Database\Factories;

use App\Models\SalesDO;
use App\Models\Customer;
use App\Models\MasterOffice;
use App\Models\PaymentTerm;
use App\Models\Tax;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesDOFactory extends Factory
{
    protected $model = SalesDO::class;

    public function definition(): array
    {
        return [
            'do_code' => 'DO-' . $this->faker->unique()->numerify('######'),
            'tracking_code' => 'TRK-' . $this->faker->numerify('######'),
            'do_date' => $this->faker->date(),
            'customer_id' => Customer::factory(),
            'office_id' => MasterOffice::factory(),
            'branch_id' => Branch::factory(),
            'shipping_address' => $this->faker->address(),
            'pic_customer' => $this->faker->name(),
            'payment_term_id' => PaymentTerm::factory(),
            'tax_id' => Tax::factory(),
            'subtotal' => $subtotal = $this->faker->randomFloat(2, 1000000, 10000000),
            'tax_amount' => $taxAmount = $subtotal * 0.11,
            'grand_total' => $subtotal + $taxAmount,
            'status' => 'crm_to_wqs',
            'notes_crm' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
