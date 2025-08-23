<?php

namespace App\Http\Integrations\Xendit\Enums;

enum TransactionType: string
{
    /** The disbursement of money-out transaction. */
    case DISBURSEMENT = 'DISBURSEMENT';

    /** The payment that includes all variation of money-in transaction. */
    case PAYMENT = 'PAYMENT';

    /** The remittance pay-out transaction. */
    case REMITTANCE_PAYOUT = 'REMITTANCE_PAYOUT';

    /** The transfer transaction between xendit account. This can be transfer in or out. */
    case TRANSFER = 'TRANSFER';

    /** A refund transaction created to refund amount from money-in transaction */
    case REFUND = 'REFUND';

    /** A withdrawal transaction for money-out operations */
    case WITHDRAWAL = 'WITHDRAWAL';

    /** A top-up transaction for adding money to account balance */
    case TOPUP = 'TOPUP';

    /** Balance conversion transactions between different currencies */
    case CONVERSION = 'CONVERSION';
}
