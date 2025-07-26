<?php

namespace Database\Factories;

use App\Models\BusinessRegistration;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessRegistrationFactory extends Factory
{
    protected $model = BusinessRegistration::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $businessType = BusinessType::inRandomOrder()->first() ?? BusinessType::factory()->create();
        $status = $this->faker->randomElement(['pending', 'approved', 'rejected']);

        return [
            'name' => $this->faker->company,
            'business_type_id' => $businessType->id,
            'applicant_id' => $user->id,
            'description' => $this->faker->sentence(8),
            'location' => $this->faker->address,
            'contact_phone' => $this->faker->phoneNumber,
            'contact_email' => $this->faker->safeEmail,
            'document_url' => $this->faker->imageUrl(),
            'status' => $status,
            'approved_by' => $status === 'approved' ? $user->id : null,
            'approved_at' => $status === 'approved' ? now() : null,
        ];
    }
}
