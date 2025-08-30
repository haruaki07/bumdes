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
        Customer::whereNotNull('next_billing_date')
            ->whereNull('invoice_number')
            ->whereDate('next_billing_date', '<=', now())
            ->whereStatus(CustomerStatus::ACTIVE)
            ->each(function (Customer $customer) use ($currentDate) {
                try {
                    $this->info("Creating bill for customer ID {$customer->customer_id}...");
                    DB::beginTransaction();

                    $count = Invoice::whereMonth('created_at', $currentDate->month)
                        ->whereYear('created_at', $currentDate->year)
                        ->count();

                    $invoiceNumber = 'INV'.$currentDate->format('Ym').str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                    $invoice = Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'customer_id' => $customer->id,
                        'customer_detail' => $customer->toJson(),
                        'package_id' => $customer->package_id,
                        'package_detail' => $customer->package->toJson(),
                        'amount' => $customer->package->price,
                        'status' => InvoiceStatus::UNPAID,
                    ]);

                    $customer->update([
                        'invoice_number' => $invoice->invoice_number,
                        'next_billing_date' => Customer::getNextBillingDate($customer),
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
