<?php

namespace Modules\EBilling\Listeners;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Events\InvoicePaid;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\User;
use Modules\EBilling\Notifications\InvoicePaid as InvoicePaidNotification;

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
        if ($invoice->customer && $invoice->status === InvoiceStatus::PAID) {
            $invoice->customer->invoice_number = null;
            $invoice->customer->next_billing_date = Customer::getNextBillingDate($invoice->customer, $invoice->period_end_date);
            $invoice->customer->save();

            $notifiables = User::all()->filter(fn ($user) => Gate::forUser($user)->allows('view', $invoice));
            Notification::send($notifiables, new InvoicePaidNotification($invoice));
        }
    }
}
