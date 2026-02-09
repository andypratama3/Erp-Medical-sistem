<?php
namespace Database\Factories;

use App\Models\FINCollection;
use App\Models\SalesDO;
use App\Models\ACTInvoice;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FINCollectionFactory extends Factory
{
    protected $model = FINCollection::class;

    public function definition(): array
    {
        return [
            'sales_do_id' => SalesDO::factory(),
            'invoice_id' => ACTInvoice::factory(),
            'branch_id' => Branch::factory(),
            'collection_number' => 'COL-' . $this->faker->unique()->numerify('######'),
            'collection_status' => $this->faker->randomElement(['pending', 'in_progress', 'partial', 'completed', 'overdue']),
            'total_amount' => $totalAmount = $this->faker->randomFloat(2, 1000000, 10000000),
            'collected_amount' => $this->faker->randomFloat(2, 0, $totalAmount),
            'outstanding_amount' => $totalAmount,
            'started_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-15 days', 'now'),
            'collector_id' => User::factory(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}