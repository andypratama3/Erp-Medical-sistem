<?php



// TaskBoardFactory
namespace Database\Factories;

use App\Models\TaskBoard;
use App\Models\SalesDO;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskBoardFactory extends Factory
{
    protected $model = TaskBoard::class;

    public function definition(): array
    {
        return [
            'sales_do_id' => SalesDO::factory(),
            'module' => $this->faker->randomElement(['wqs', 'scm', 'act', 'fin']),
            'task_status' => $this->faker->randomElement(['pending', 'in_progress', 'on_hold', 'completed', 'rejected']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'assigned_to' => User::factory(),
            'assigned_by' => User::factory(),
            'assigned_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'started_at' => $this->faker->optional()->dateTimeBetween('-5 days', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
