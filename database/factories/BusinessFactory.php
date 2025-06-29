<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use App\Models\BusinessType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
  protected $model = Business::class;

  public function definition(): array
  {
    $user = User::inRandomOrder()->first() ?? User::factory()->create();
    $businessType = BusinessType::inRandomOrder()->first() ?? BusinessType::factory()->create();
    return [
      'name' => $this->faker->company,
      'business_type_id' => $businessType->id,
      'owner_id' => $user->id,
      'description' => $this->faker->sentence(8),
      'location' => $this->faker->address,
      'contact_phone' => $this->faker->phoneNumber,
      'contact_email' => $this->faker->safeEmail,
      'status' => $this->faker->randomElement(['active', 'inactive']),
    ];
  }
}
