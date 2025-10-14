<?php

namespace Modules\EBilling\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\EBilling\Enums\CustomerStatus;
use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Invoice;

class CreateBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'e-billing:create-bills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create bills/invoices for customers based on their due date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = now();
        Customer::whereNull('invoice_number')
            ->whereNowOrPast('next_billing_date')
            ->whereStatus(CustomerStatus::ACTIVE)
            ->each(function (Customer $customer) use ($currentDate) {
                try {
                    DB::beginTransaction();

                    $count = Invoice::whereMonth('created_at', $currentDate->month)
                        ->whereYear('created_at', $currentDate->year)
                        ->count();

                    $invoiceNumber = Invoice::generateInvoiceNumber($currentDate->copy(), $count);

                    if ($currentDate > $customer->grace_period_end_date) {
                        $this->warn("Customer ID {$customer->customer_id} is past the grace period. Deactivating customer...");
                        $customer->update(['status' => CustomerStatus::INACTIVE]);
                        DB::commit();

                        return;
                    }

                    $this->info("Creating bill for customer ID {$customer->customer_id}...");
                    $invoice = Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'due_date' => $customer->due_date,
                        'grace_period_end_date' => $customer->grace_period_end_date,
                        'period_start_date' => $customer->period_start_date,
                        'period_end_date' => $customer->period_end_date,
                        'customer_id' => $customer->id,
                        'customer_detail' => $customer,
                        'package_id' => $customer->package_id,
                        'package_detail' => $customer->package,
                        'amount' => $customer->package->price,
                        'status' => InvoiceStatus::UNPAID,
                    ]);

                    $customer->update([
                        'invoice_number' => $invoice->invoice_number,
                    ]);

                    DB::commit();
                    $this->info("Bill created successfully for customer ID {$customer->id} with invoice number {$invoice->invoice_number}.");
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            });
    }
}
