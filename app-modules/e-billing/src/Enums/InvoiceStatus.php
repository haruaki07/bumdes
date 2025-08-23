<?php

namespace Modules\EBilling\Enums;

enum InvoiceStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case EXPIRED = 'expired';
}
