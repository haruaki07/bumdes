<?php

namespace Modules\EBilling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\EBilling\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Admin E-Billing',
                'email' => 'admin@mail.com',
                'password' => Hash::make('123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Operator E-Billing',
                'email' => 'operator@mail.com',
                'password' => Hash::make('123'),
                'role' => 'operator',
            ],
        ]);
    }
}
