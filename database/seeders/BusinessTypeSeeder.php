<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
  public function run(): void
  {
    $types = [
      [
        'name' => 'Toko Kelontong',
        'description' => 'Usaha retail skala kecil yang menjual kebutuhan sehari-hari',
        'is_active' => true,
      ],
      [
        'name' => 'Warung Makan',
        'description' => 'Usaha kuliner skala kecil',
        'is_active' => true,
      ],
      [
        'name' => 'Jasa Internet',
        'description' => 'Penyedia layanan internet untuk warga',
        'is_active' => true,
      ],
      [
        'name' => 'Pertanian',
        'description' => 'Usaha di bidang pertanian dan perkebunan',
        'is_active' => true,
      ],
      [
        'name' => 'Peternakan',
        'description' => 'Usaha di bidang peternakan',
        'is_active' => true,
      ],
    ];

    foreach ($types as $type) {
      BusinessType::create($type);
    }
  }
}
