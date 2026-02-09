<?php

// SCMDeliveryFactory
namespace Database\Factories;

use App\Models\SCMDelivery;
use App\Models\SalesDO;
use App\Models\SCMDriver;
use App\Models\Vehicle;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class SCMDeliveryFactory extends Factory
{
    protected $model = SCMDelivery::class;

    public function definition(): array
    {
        return [
            'sales_do_id' => SalesDO::factory(),
            'branch_id' => Branch::factory(),
            'delivery_code' => 'DEL-' . $this->faker->unique()->numerify('######'),
            'driver_id' => SCMDriver::factory(),
            'vehicle_id' => Vehicle::factory(),
            'scheduled_date' => $this->faker->dateTimeBetween('now', '+7 days'),
            'departure_time' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
            'arrival_time' => $this->faker->optional()->dateTimeBetween('-1 day', 'now'),
            'delivery_status' => $this->faker->randomElement(['scheduled', 'in_transit', 'delivered', 'failed']),
            'delivery_address' => $this->faker->address(),
            'recipient_name' => $this->faker->name(),
            'recipient_phone' => $this->faker->phoneNumber(),
            'delivery_notes' => $this->faker->optional()->sentence(),
        ];
    }
}
