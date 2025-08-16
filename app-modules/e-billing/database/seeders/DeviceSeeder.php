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
        Device::insert([
            [
                'brand' => 'Huawei',
                'model' => 'HG8245H5',
            ],
            [
                'brand' => 'KingType',
                'model' => 'EW45',
            ],
        ]);
    }
}
