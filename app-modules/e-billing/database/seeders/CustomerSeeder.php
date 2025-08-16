<?php

namespace Modules\EBilling\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Device;
use Modules\EBilling\Models\Package;
use Modules\EBilling\Models\Site;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::all();
        $packages = Package::all();
        $devices = Device::all();

        Customer::factory()
            ->state(function (array $attributes) use ($sites, $packages, $devices) {
                return [
                    'site_id' => $sites->random()->id,
                    'package_id' => $packages->random()->id,
                    'device_id' => $devices->random()->id,
                ];
            })
            ->count(10)
            ->create();
    }
}
