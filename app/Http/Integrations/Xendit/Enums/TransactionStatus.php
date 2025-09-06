<?php

namespace App\Http\Integrations\Xendit\Enums;

enum TransactionStatus: string
{
    /** The transaction is still pending to be processed. This refers to money out-transaction when the amount is still on hold. */
    case PENDING = 'PENDING';

    /** The transaction is successfully sent for money-out or already arrives on money-in. */
    case SUCCESS = 'SUCCESS';

    /** The transaction failed to send/receive. */
    case FAILED = 'FAILED';

    /** The money-in transaction is voided by customer. */
    case VOIDED = 'VOIDED';

    /** The transaction is reversed by Xendit. */
    case REVERSED = 'REVERSED';
}
