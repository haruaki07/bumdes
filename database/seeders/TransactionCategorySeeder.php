<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // INCOME CATEGORIES (PENDAPATAN)
            [
                'name' => 'Pendapatan Operasional',
                'code' => 'INC-OPR',
                'type' => 'income',
                'description' => 'Pendapatan dari kegiatan operasional BUMDes',
                'order' => 1,
                'subcategories' => [
                    [
                        'name' => 'Pendapatan Jasa',
                        'code' => 'INC-OPR-JSA',
                        'type' => 'income',
                        'description' => 'Pendapatan dari jasa yang diberikan',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Pendapatan Penjualan',
                        'code' => 'INC-OPR-PJL',
                        'type' => 'income',
                        'description' => 'Pendapatan dari penjualan produk',
                        'order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Pendapatan Non-Operasional',
                'code' => 'INC-NON',
                'type' => 'income',
                'description' => 'Pendapatan di luar kegiatan operasional',
                'order' => 2,
                'subcategories' => [
                    [
                        'name' => 'Bantuan/Hibah',
                        'code' => 'INC-NON-HBH',
                        'type' => 'income',
                        'description' => 'Pendapatan dari bantuan atau hibah',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Pendapatan Lain-lain',
                        'code' => 'INC-NON-LLN',
                        'type' => 'income',
                        'description' => 'Pendapatan lain yang tidak terkategori',
                        'order' => 2,
                    ],
                ],
            ],

            // EXPENSE CATEGORIES (BEBAN)
            [
                'name' => 'Beban Operasional',
                'code' => 'EXP-OPR',
                'type' => 'expense',
                'description' => 'Beban untuk kegiatan operasional BUMDes',
                'order' => 1,
                'subcategories' => [
                    [
                        'name' => 'Gaji dan Tunjangan',
                        'code' => 'EXP-OPR-GJI',
                        'type' => 'expense',
                        'description' => 'Beban gaji dan tunjangan karyawan',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Biaya Listrik',
                        'code' => 'EXP-OPR-LST',
                        'type' => 'expense',
                        'description' => 'Beban biaya listrik',
                        'order' => 2,
                    ],
                    [
                        'name' => 'Biaya Air',
                        'code' => 'EXP-OPR-AIR',
                        'type' => 'expense',
                        'description' => 'Beban biaya air',
                        'order' => 3,
                    ],
                    [
                        'name' => 'Biaya Telepon & Internet',
                        'code' => 'EXP-OPR-TEL',
                        'type' => 'expense',
                        'description' => 'Beban biaya komunikasi',
                        'order' => 4,
                    ],
                    [
                        'name' => 'Biaya Transportasi',
                        'code' => 'EXP-OPR-TRP',
                        'type' => 'expense',
                        'description' => 'Beban biaya transportasi',
                        'order' => 5,
                    ],
                    [
                        'name' => 'Biaya Pemeliharaan',
                        'code' => 'EXP-OPR-PMH',
                        'type' => 'expense',
                        'description' => 'Beban pemeliharaan aset dan fasilitas',
                        'order' => 6,
                    ],
                ],
            ],
            [
                'name' => 'Beban Administrasi',
                'code' => 'EXP-ADM',
                'type' => 'expense',
                'description' => 'Beban untuk kegiatan administrasi',
                'order' => 2,
                'subcategories' => [
                    [
                        'name' => 'Biaya ATK',
                        'code' => 'EXP-ADM-ATK',
                        'type' => 'expense',
                        'description' => 'Beban alat tulis kantor',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Biaya Cetak & Fotokopi',
                        'code' => 'EXP-ADM-CTK',
                        'type' => 'expense',
                        'description' => 'Beban pencetakan dan fotokopi',
                        'order' => 2,
                    ],
                    [
                        'name' => 'Biaya Perizinan',
                        'code' => 'EXP-ADM-IZN',
                        'type' => 'expense',
                        'description' => 'Beban pengurusan perizinan',
                        'order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Beban Lain-lain',
                'code' => 'EXP-OTH',
                'type' => 'expense',
                'description' => 'Beban lain yang tidak terkategori',
                'order' => 3,
                'subcategories' => [
                    [
                        'name' => 'Beban Penyusutan',
                        'code' => 'EXP-OTH-PST',
                        'type' => 'expense',
                        'description' => 'Beban penyusutan aset',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Beban Miscellaneous',
                        'code' => 'EXP-OTH-MSC',
                        'type' => 'expense',
                        'description' => 'Beban lain-lain',
                        'order' => 2,
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $subcategories = $categoryData['subcategories'] ?? [];
            unset($categoryData['subcategories']);

            // Create parent category
            $parent = TransactionCategory::create($categoryData);

            // Create subcategories
            foreach ($subcategories as $subData) {
                $subData['parent_id'] = $parent->id;
                TransactionCategory::create($subData);
            }
        }

        $this->command->info('Transaction categories seeded successfully!');
    }
}
