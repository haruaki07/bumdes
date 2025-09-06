<?php

namespace App\Http\Integrations\Xendit\Enums;

enum CashFlow: string
{
    case MONEY_IN = 'MONEY_IN';
    case MONEY_OUT = 'MONEY_OUT';
}
