<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            $this->command->warn('No admin user found. Please create an admin user first.');

            return;
        }

        // Get categories
        $incomeJasa = TransactionCategory::where('code', 'INC-OPR-JSA')->first();
        $incomePenjualan = TransactionCategory::where('code', 'INC-OPR-PJL')->first();
        $incomeHibah = TransactionCategory::where('code', 'INC-NON-HBH')->first();

        $expenseGaji = TransactionCategory::where('code', 'EXP-OPR-GJI')->first();
        $expenseListrik = TransactionCategory::where('code', 'EXP-OPR-LST')->first();
        $expenseAir = TransactionCategory::where('code', 'EXP-OPR-AIR')->first();
        $expenseTelepon = TransactionCategory::where('code', 'EXP-OPR-TEL')->first();
        $expenseAtk = TransactionCategory::where('code', 'EXP-ADM-ATK')->first();

        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Sample transactions for current month
        $transactions = [
            // Income transactions - Current Month
            [
                'transaction_category_id' => $incomeJasa->id,
                'type' => 'income',
                'amount' => 5000000,
                'transaction_date' => $currentMonth->copy()->subDays(5),
                'description' => 'Pendapatan jasa konsultasi usaha',
                'reference_number' => 'INC-'.$currentMonth->format('Ym').'-001',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $currentMonth->copy()->subDays(4),
            ],
            [
                'transaction_category_id' => $incomePenjualan->id,
                'type' => 'income',
                'amount' => 8500000,
                'transaction_date' => $currentMonth->copy()->subDays(3),
                'description' => 'Penjualan produk kerajinan',
                'reference_number' => 'INC-'.$currentMonth->format('Ym').'-002',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $currentMonth->copy()->subDays(2),
            ],
            [
                'transaction_category_id' => $incomeHibah->id,
                'type' => 'income',
                'amount' => 10000000,
                'transaction_date' => $currentMonth->copy()->subDays(10),
                'description' => 'Hibah dari pemerintah desa',
                'reference_number' => 'INC-'.$currentMonth->format('Ym').'-003',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $currentMonth->copy()->subDays(9),
            ],

            // Expense transactions - Current Month
            [
                'transaction_category_id' => $expenseGaji->id,
                'type' => 'expense',
                'amount' => 6000000,
                'transaction_date' => $currentMonth->copy()->startOfMonth(),
                'description' => 'Gaji pegawai bulan '.$currentMonth->format('F Y'),
                'reference_number' => 'EXP-'.$currentMonth->format('Ym').'-001',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $currentMonth->copy()->startOfMonth()->addDay(),
            ],
            [
                'transaction_category_id' => $expenseListrik->id,
                'type' => 'expense',
                'amount' => 500000,
                'transaction_date' => $currentMonth->copy()->subDays(7),
                'description' => 'Biaya listrik kantor',
                'reference_number' => 'EXP-'.$currentMonth->format('Ym').'-002',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $currentMonth->copy()->subDays(6),
            ],
            [
                'transaction_category_id' => $expenseAir->id,
                'type' => 'expense',
                'amount' => 200000,
                'transaction_date' => $currentMonth->copy()->subDays(7),
                'description' => 'Biaya air PDAM',
                'reference_number' => 'EXP-'.$currentMonth->format('Ym').'-003',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $currentMonth->copy()->subDays(6),
            ],
            [
                'transaction_category_id' => $expenseTelepon->id,
                'type' => 'expense',
                'amount' => 400000,
                'transaction_date' => $currentMonth->copy()->subDays(5),
                'description' => 'Biaya internet dan telepon',
                'reference_number' => 'EXP-'.$currentMonth->format('Ym').'-004',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $currentMonth->copy()->subDays(4),
            ],
            [
                'transaction_category_id' => $expenseAtk->id,
                'type' => 'expense',
                'amount' => 350000,
                'transaction_date' => $currentMonth->copy()->subDays(8),
                'description' => 'Pembelian alat tulis kantor',
                'reference_number' => 'EXP-'.$currentMonth->format('Ym').'-005',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $currentMonth->copy()->subDays(7),
            ],

            // Sample transactions for last month
            [
                'transaction_category_id' => $incomeJasa->id,
                'type' => 'income',
                'amount' => 4500000,
                'transaction_date' => $lastMonth->copy()->subDays(5),
                'description' => 'Pendapatan jasa konsultasi usaha',
                'reference_number' => 'INC-'.$lastMonth->format('Ym').'-001',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $lastMonth->copy()->subDays(4),
            ],
            [
                'transaction_category_id' => $incomePenjualan->id,
                'type' => 'income',
                'amount' => 7200000,
                'transaction_date' => $lastMonth->copy()->subDays(10),
                'description' => 'Penjualan produk kerajinan',
                'reference_number' => 'INC-'.$lastMonth->format('Ym').'-002',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $lastMonth->copy()->subDays(9),
            ],
            [
                'transaction_category_id' => $expenseGaji->id,
                'type' => 'expense',
                'amount' => 6000000,
                'transaction_date' => $lastMonth->copy()->startOfMonth(),
                'description' => 'Gaji pegawai bulan '.$lastMonth->format('F Y'),
                'reference_number' => 'EXP-'.$lastMonth->format('Ym').'-001',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $lastMonth->copy()->startOfMonth()->addDay(),
            ],
            [
                'transaction_category_id' => $expenseListrik->id,
                'type' => 'expense',
                'amount' => 480000,
                'transaction_date' => $lastMonth->copy()->subDays(8),
                'description' => 'Biaya listrik kantor',
                'reference_number' => 'EXP-'.$lastMonth->format('Ym').'-002',
                'created_by' => $admin->id,
                'verified_by' => $admin->id,
                'verified_at' => $lastMonth->copy()->subDays(7),
            ],
        ];

        foreach ($transactions as $transaction) {
            Transaction::create($transaction);
        }

        $this->command->info('Sample transactions seeded successfully!');
        $this->command->info('Created '.count($transactions).' sample transactions');
    }
}
