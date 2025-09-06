<?php

namespace Modules\EBilling\Listeners;

use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Events\InvoicePaid;
use Modules\EBilling\Models\Customer;

class UpdateCustomerBill
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice;
        $customer = Customer::where('invoice_number', $invoice->invoice_number)->first();
        if ($customer) {
            if ($invoice->status === InvoiceStatus::PAID) {
                $customer->invoice_number = null;
                $customer->next_billing_date = Customer::getNextBillingDate($customer);
                $customer->save();
            }
        }
    }
}
