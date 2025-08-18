<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BusinessTypeSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Operator',
            'email' => 'operator@mail.com',
            'password' => Hash::make('123'),
            'role' => 'operator',
        ]);

        User::factory()->create([
            'name' => 'Warga',
            'email' => 'warga@mail.com',
            'password' => Hash::make('123'),
            'role' => 'warga',
        ]);

        $this->call([
            BusinessAndRegistrationSeeder::class,
        ]);
    }
}
