<?php

namespace App\Http\Integrations\Xendit\Enums;

enum ChannelCategory: string
{
    /** Channel category for BANK (applies to DISBURSEMENT and REMITTANCE_PAYOUT). */
    case BANK = 'BANK';

    /** Channel category for CARDS (applies to PAYMENT). */
    case CARDS = 'CARDS';

    /** Channel category for CARDLESS_CREDIT (applies to PAYMENT). */
    case CARDLESS_CREDIT = 'CARDLESS_CREDIT';

    /** Channel category for CASH (applies to DISBURSEMENT and REMITTANCE_PAYOUT). */
    case CASH = 'CASH';

    /** Channel category for DIRECT_DEBIT (applies to PAYMENT). */
    case DIRECT_DEBIT = 'DIRECT_DEBIT';

    /** Channel category for EWALLET (applies to PAYMENT). */
    case EWALLET = 'EWALLET';

    /** Channel category for PAYLATER (applies to PAYMENT). */
    case PAYLATER = 'PAYLATER';

    /** Channel category for QR_CODE (applies to PAYMENT). */
    case QR_CODE = 'QR_CODE';

    /** Channel category for RETAIL_OUTLET (applies to PAYMENT). */
    case RETAIL_OUTLET = 'RETAIL_OUTLET';

    /** Channel category for VIRTUAL_ACCOUNT (applies to PAYMENT). */
    case VIRTUAL_ACCOUNT = 'VIRTUAL_ACCOUNT';

    /** Channel category for XENPLATFORM (applies to TRANSFER). */
    case XENPLATFORM = 'XENPLATFORM';

    /** Channel category for OTHER (applies to CONVERSION). */
    case OTHER = 'OTHER';
}
