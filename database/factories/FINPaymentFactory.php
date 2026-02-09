<?php


// FINPaymentFactory
namespace Database\Factories;

use App\Models\FINPayment;
use App\Models\SalesDO;
use App\Models\FINCollection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FINPaymentFactory extends Factory
{
    protected $model = FINPayment::class;

    public function definition(): array
    {
        $method = $this->faker->randomElement(['cash', 'transfer', 'check', 'giro', 'other']);

        return [
            'sales_do_id' => SalesDO::factory(),
            'collection_id' => FINCollection::factory(),
            'payment_number' => 'PAY-' . $this->faker->unique()->numerify('######'),
            'payment_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'payment_amount' => $this->faker->randomFloat(2, 100000, 5000000),
            'payment_method' => $method,
            'bank_name' => in_array($method, ['transfer', 'check', 'giro']) ? $this->faker->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']) : null,
            'account_number' => in_array($method, ['transfer', 'check', 'giro']) ? $this->faker->numerify('##########') : null,
            'reference_number' => 'REF-' . $this->faker->numerify('######'),
            'notes' => $this->faker->optional()->sentence(),
            'recorded_by' => User::factory(),
        ];
    }
}
