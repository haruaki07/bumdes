<?php

namespace App\Http\Integrations\Xendit\Enums;

enum Currency: string
{
    case IDR = 'IDR';
    case PHP = 'PHP';
    case USD = 'USD';
    case VND = 'VND';
    case THB = 'THB';
    case MYR = 'MYR';
    case SGD = 'SGD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case HKD = 'HKD';
    case AUD = 'AUD';
}
