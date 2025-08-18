<?php

namespace Modules\EBilling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\EBilling\Enums\CustomerStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\EBilling\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => $this->faker->unique()->randomNumber(5, true).'@wificlp.id',
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'longitude' => $this->faker->optional()->longitude(),
            'latitude' => $this->faker->optional()->latitude(),
            'map_url' => $this->faker->optional()->url(),
            'due' => $this->faker->numberBetween(1, 31),
            'serial_number' => $this->faker->optional()->bothify('HWTC####????'),
            'mac_address' => $this->faker->optional()->macAddress(),
            'registration_date' => $this->faker->dateTime(),
            'status' => $this->faker->randomElement(CustomerStatus::cases()),
        ];
    }
}
