<?php

namespace Database\Factories;

use App\Models\BusinessType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessTypeFactory extends Factory
{
    protected $model = BusinessType::class;

    public function definition(): array
    {
        $types = [
            ['name' => 'Toko Kelontong', 'description' => 'Usaha retail skala kecil yang menjual kebutuhan sehari-hari'],
            ['name' => 'Warung Makan', 'description' => 'Usaha kuliner skala kecil'],
            ['name' => 'Jasa Internet', 'description' => 'Penyedia layanan internet untuk warga'],
            ['name' => 'Pertanian', 'description' => 'Usaha di bidang pertanian dan perkebunan'],
            ['name' => 'Peternakan', 'description' => 'Usaha di bidang peternakan'],
            ['name' => 'Kerajinan', 'description' => 'Usaha pembuatan kerajinan tangan'],
            ['name' => 'Jasa', 'description' => 'Usaha penyedia jasa umum'],
        ];

        $type = $this->faker->randomElement($types);

        return [
            'name' => $type['name'].' '.$this->faker->unique()->numberBetween(1, 1000),
            'description' => $type['description'],
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
