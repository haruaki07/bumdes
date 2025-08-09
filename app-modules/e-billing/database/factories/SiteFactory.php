<?php

namespace Modules\EBilling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\EBilling\Models\Site;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\EBilling\Models\Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'description' => $this->faker->sentence
        ];
    }
}
