<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum TicketCategory: string
{
    use HasEnumValues;

    case BILLING = 'Billing';
    case TECHNICAL = 'Technical';
    case ACCOUNT = 'Account';
    case GENERAL = 'General';
}
