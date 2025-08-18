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
        $bandwidthOptions = [10, 20, 30, 50, 100]; // Mbps
        $bandwidth = $this->faker->randomElement($bandwidthOptions);

        $priceMapping = [
            10 => [150000, 250000],
            20 => [200000, 300000],
            30 => [250000, 400000],
            50 => [350000, 600000],
            100 => [500000, 1000000],
        ];

        [$minPrice, $maxPrice] = $priceMapping[$bandwidth];
        $step = 25000;
        $price = $this->faker->numberBetween($minPrice / $step, $maxPrice / $step) * $step;

        return [
            'name' => $this->faker->randomElement([
                'Basic',
                'Standar',
                'Premium',
                'Family',
                'Bisnis',
            ])." {$bandwidth} Mbps",

            'description' => $this->faker->optional()->randomElement([
                'Internet cepat dan stabil untuk kebutuhan sehari-hari.',
                'Cocok untuk streaming film dan belajar online.',
                'Pilihan tepat untuk keluarga dengan banyak perangkat.',
                'Ideal untuk usaha kecil dan menengah.',
            ]),

            'bandwidth' => $bandwidth,
            'price' => $price,
            'due' => $this->faker->numberBetween(1, 30),
        ];
    }
}
