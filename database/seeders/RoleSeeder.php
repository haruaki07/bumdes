<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
  public function run(): void
  {
    $roles = [
      [
        'name' => 'admin',
        'display_name' => 'Administrator',
        'description' => 'Full access to all features',
      ],
      [
        'name' => 'petugas',
        'display_name' => 'Petugas',
        'description' => 'Staff access to manage daily operations',
      ],
      [
        'name' => 'warga',
        'display_name' => 'Warga',
        'description' => 'Regular citizen access',
      ],
      [
        'name' => 'kelompok_usaha',
        'display_name' => 'Kelompok Usaha',
        'description' => 'Business group access',
      ],
    ];

    foreach ($roles as $role) {
      Role::create($role);
    }
  }
}
