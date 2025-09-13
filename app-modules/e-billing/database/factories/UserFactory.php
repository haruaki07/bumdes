<?php

namespace Modules\EBilling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\EBilling\Enums\UserRole;
use Modules\EBilling\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'role' => UserRole::OPERATOR, // default role
            'remember_token' => Str::random(10),
        ];
    }
}
