<?php


// WQSStockCheckFactory
namespace Database\Factories;

use App\Models\WQSStockCheck;
use App\Models\SalesDO;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class WQSStockCheckFactory extends Factory
{
    protected $model = WQSStockCheck::class;

    public function definition(): array
    {
        return [
            'sales_do_id' => SalesDO::factory(),
            'branch_id' => Branch::factory(),
            'check_code' => 'SC-' . $this->faker->unique()->numerify('######'),
            'check_date' => $this->faker->date(),
            'checker_id' => User::factory(),
            'check_status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'failed']),
            'total_items' => $total = $this->faker->numberBetween(5, 20),
            'checked_items' => $this->faker->numberBetween(0, $total),
            'problematic_items' => $this->faker->numberBetween(0, 5),
            'notes' => $this->faker->optional()->sentence(),
            'started_at' => $this->faker->optional()->dateTimeBetween('-7 days', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-3 days', 'now'),
        ];
    }
}