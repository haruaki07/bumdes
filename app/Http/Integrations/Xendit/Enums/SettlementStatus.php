<?php

namespace App\Http\Integrations\Xendit\Enums;

enum SettlementStatus: string
{
    /** Transaction amount has not been settled to merchant's balance. */
    case PENDING = 'PENDING';

    /** Transaction has been settled early to merchant's balance. */
    case EARLY_SETTLED = 'EARLY_SETTLED';

    /** Transaction has been settled to merchant's balance. */
    case SETTLED = 'SETTLED';
}
