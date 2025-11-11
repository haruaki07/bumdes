<?php

namespace App\Console\Commands;

use App\Models\FundingRequest;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecordInterestIncome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:record {--month= : Month in YYYY-MM format} {--funding-request= : Specific funding request ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record interest income from active funding requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting interest income recording...');

        // Get month parameter or use current month
        $month = $this->option('month')
            ? Carbon::parse($this->option('month'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $fundingRequestId = $this->option('funding-request');

        // Get interest income category (you might want to create a specific category for this)
        // For now, we'll create transactions without category or you can add a new category
        $interestCategory = TransactionCategory::firstOrCreate(
            ['code' => 'INC-INT'],
            [
                'name' => 'Pendapatan Bunga',
                'type' => 'income',
                'description' => 'Pendapatan bunga dari pendanaan',
                'is_active' => true,
                'order' => 99,
            ]
        );

        // Get admin user for created_by
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            $this->error('No admin user found!');

            return 1;
        }

        // Get active funding requests
        $query = FundingRequest::whereIn('status', ['disbursed', 'completed'])
            ->whereNotNull('disbursement_date')
            ->whereNotNull('interest_rate')
            ->where('interest_rate', '>', 0);

        if ($fundingRequestId) {
            $query->where('id', $fundingRequestId);
        }

        $fundingRequests = $query->get();

        if ($fundingRequests->isEmpty()) {
            $this->warn('No active funding requests found.');

            return 0;
        }

        $this->info("Found {$fundingRequests->count()} funding request(s)");

        $totalRecorded = 0;
        $totalAmount = 0;

        DB::beginTransaction();
        try {
            foreach ($fundingRequests as $request) {
                // Calculate interest for the specified month
                $disbursementDate = Carbon::parse($request->disbursement_date);
                $dueDate = $request->due_date ? Carbon::parse($request->due_date) : null;

                // Skip if disbursement is after the target month
                if ($disbursementDate->greaterThan($month->copy()->endOfMonth())) {
                    $this->warn("Skipping FR #{$request->id}: Disbursement date is in the future");

                    continue;
                }

                // Skip if loan is already fully paid before the target month
                if ($dueDate && $dueDate->lessThan($month->copy()->startOfMonth())) {
                    $this->warn("Skipping FR #{$request->id}: Loan completed before target month");

                    continue;
                }

                // Check if we already recorded this month's interest
                $existingRecord = Transaction::where('funding_request_id', $request->id)
                    ->where('transaction_category_id', $interestCategory->id)
                    ->whereYear('transaction_date', $month->year)
                    ->whereMonth('transaction_date', $month->month)
                    ->exists();

                if ($existingRecord) {
                    $this->warn("Skipping FR #{$request->id}: Interest already recorded for this month");

                    continue;
                }

                // Determine calculation period for this month
                $calcStart = $disbursementDate->greaterThan($month) ? $disbursementDate : $month->copy();
                $calcEnd = $month->copy()->endOfMonth();

                if ($dueDate && $dueDate->lessThan($calcEnd)) {
                    $calcEnd = $dueDate;
                }

                $daysInMonth = $calcStart->diffInDays($calcEnd) + 1;
                $amount = $request->disbursed_amount ?? $request->amount;

                // Calculate monthly interest: (Principal × Rate × Days) / 365
                $interest = ($amount * ($request->interest_rate / 100) * $daysInMonth) / 365;

                if ($interest <= 0) {
                    continue;
                }

                // Create transaction record
                $transaction = Transaction::create([
                    'transaction_category_id' => $interestCategory->id,
                    'type' => 'income',
                    'amount' => $interest,
                    'transaction_date' => $month->copy()->endOfMonth(), // Record at end of month
                    'description' => sprintf(
                        'Pendapatan bunga FR #%d - %s (%d hari × %.2f%%)',
                        $request->id,
                        $request->business->name ?? 'N/A',
                        $daysInMonth,
                        $request->interest_rate
                    ),
                    'reference_number' => sprintf('INT-%s-%04d', $month->format('Ym'), $request->id),
                    'funding_request_id' => $request->id,
                    'created_by' => $admin->id,
                    'verified_by' => $admin->id,
                    'verified_at' => now(),
                    'notes' => sprintf(
                        'Auto-generated interest income. Principal: Rp %s, Rate: %.2f%%, Days: %d',
                        number_format($amount, 0, ',', '.'),
                        $request->interest_rate,
                        $daysInMonth
                    ),
                ]);

                $totalRecorded++;
                $totalAmount += $interest;

                $this->info(sprintf(
                    'Recorded FR #%d: Rp %s',
                    $request->id,
                    number_format($interest, 0, ',', '.')
                ));
            }

            DB::commit();

            $this->info('');
            $this->info("✓ Successfully recorded {$totalRecorded} interest transaction(s)");
            $this->info('Total Interest Amount: Rp '.number_format($totalAmount, 0, ',', '.'));

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error recording interest: '.$e->getMessage());

            return 1;
        }
    }
}
