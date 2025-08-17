<?php

namespace Modules\EBilling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\EBilling\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brandsWithModels = [
            'TP-Link' => ['TD-8817', 'TD-W9970', 'Archer VR600'],
            'Netgear' => ['DM200', 'CM500', 'CM1000', 'Nighthawk M1 (MR1100)'],
            'Cisco' => ['DPC3008', 'DPC3825', 'Cisco 887VA', 'Cisco 880 Series'],
            'D-Link' => ['DSL-2750U', 'DSL-2877AL', 'DSL-3900', 'DSL-5300'],
            'Huawei' => ['E3372', 'B310', 'B618', '5G CPE Pro 2 (H122-373)'],
            'KingType' => ['KT-4110', 'KT-5100', 'GPON ONT HG323RW', 'EW45'],
        ];

        $brand = $this->faker->randomElement(array_keys($brandsWithModels));

        $model = $this->faker->randomElement($brandsWithModels[$brand]);

        return [
            'brand' => $brand,
            'model' => $model,
        ];
    }
}
