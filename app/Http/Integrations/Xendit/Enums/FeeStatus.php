<?php

namespace App\Http\Integrations\Xendit\Enums;

enum FeeStatus: string
{
    /** Fee processing is pending. */
    case PENDING = 'PENDING';

    /** Fee processing is completed. */
    case COMPLETED = 'COMPLETED';

    /** Fee processing is canceled. */
    case CANCELED = 'CANCELED';

    /** Fee processing is reversed. */
    case REVERSED = 'REVERSED';

    /** No fees are applicable for this transaction. */
    case NOT_APPLICABLE = 'NOT_APPLICABLE';
}
