<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pigs>
 */
class PigsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'pig_id' => 'PIG-' . Str::padLeft($this->faker->unique()->numberBetween(1, 9999), 4, '0'),
            'pen_number' => $this->faker->numberBetween(1, 10),
            'date_bought' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'cost' => $this->faker->numberBetween(4000, 5000),
            'breed' => $this->faker->randomElement(['Landrace', 'Duroc', 'Yorkshire', 'Berkshire', 'Native']),
            'expected_sell_date' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'starting_weight' => $this->faker->numberBetween(10, 20),
            'current_weight' => $this->faker->numberBetween(20, 100),
        ];
    }
}
