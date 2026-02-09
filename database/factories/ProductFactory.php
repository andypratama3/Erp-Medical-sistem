<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{5}'),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'category_id' => \App\Models\Category::factory(),
            'unit_id' => \App\Models\Unit::factory(),
            'purchase_price' => $this->faker->randomFloat(2, 10000, 1000000),
            'selling_price' => $this->faker->randomFloat(2, 15000, 1500000),
            'stock' => $this->faker->numberBetween(0, 1000),
            'min_stock' => $this->faker->numberBetween(0, 100),
            'max_stock' => $this->faker->numberBetween(101, 1000),
            'is_active' => $this->faker->boolean(),
            'created_by' => \App\Models\User::factory(),
            'updated_by' => \App\Models\User::factory(),
        ];
    }
}
