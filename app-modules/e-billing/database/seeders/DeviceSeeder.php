<?php

namespace Modules\EBilling\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\EBilling\Models\Device;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Device::factory()
            ->count(4)
            ->create();
    }
}
