<?php

namespace Modules\EBilling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\EBilling\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'bandwidth' => $this->faker->randomElement([10, 20, 50, 100]),
            'price' => $this->faker->randomFloat(2, 100000, 500000),
            'due' => $this->faker->numberBetween(1, 30),
        ];
    }
}
