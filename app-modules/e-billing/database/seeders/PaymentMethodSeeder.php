<?php

namespace Modules\EBilling\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\EBilling\Enums\PaymentMethodType;
use Modules\EBilling\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // bank transfer methods
        PaymentMethod::insert([
            [
                'name' => 'BCA',
                'code' => 'TF_BCA',
                'account_number' => '1234567890',
                'need_confirmation' => true,
                'type' => PaymentMethodType::BANK_TRANSFER,
                'brand_logo' => 'assets/images/payment_channels/bca.svg',
            ],
        ]);

        // retail methods
        PaymentMethod::insert([
            [
                'name' => 'Alfamart',
                'code' => 'ALFAMART',
                'type' => PaymentMethodType::RETAIL,
                'brand_logo' => 'assets/images/payment_channels/alfamart.svg',
            ],
            [
                'name' => 'Indomaret',
                'code' => 'INDOMARET',
                'type' => PaymentMethodType::RETAIL,
                'brand_logo' => 'assets/images/payment_channels/indomaret.svg',
            ],
        ]);

        // qr methods
        PaymentMethod::insert([

            [
                'name' => 'QRIS',
                'code' => 'QRIS',
                'type' => PaymentMethodType::QR,
                'brand_logo' => 'assets/images/payment_channels/qris.svg',
            ],
        ]);

        // va methods
        PaymentMethod::insert([
            [
                'name' => 'BCA VA',
                'code' => 'BCA_VIRTUAL_ACCOUNT',
                'type' => PaymentMethodType::VIRTUAL_ACCOUNT,
                'brand_logo' => 'assets/images/payment_channels/bca.svg',
                'prefix_length' => 5,
            ],
            [
                'name' => 'BNI VA',
                'code' => 'BNI_VIRTUAL_ACCOUNT',
                'type' => PaymentMethodType::VIRTUAL_ACCOUNT,
                'brand_logo' => 'assets/images/payment_channels/bni.svg',
                'prefix_length' => 4,
            ],
            [
                'name' => 'BSI VA',
                'code' => 'BSI_VIRTUAL_ACCOUNT',
                'type' => PaymentMethodType::VIRTUAL_ACCOUNT,
                'brand_logo' => 'assets/images/payment_channels/bsi.svg',
                'prefix_length' => 4,
            ],
            [
                'name' => 'Permata VA',
                'code' => 'PERMATA_VIRTUAL_ACCOUNT',
                'type' => PaymentMethodType::VIRTUAL_ACCOUNT,
                'brand_logo' => 'assets/images/payment_channels/permata.svg',
                'prefix_length' => 4,
            ],
        ]);
    }
}
