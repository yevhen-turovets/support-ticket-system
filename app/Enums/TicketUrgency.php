<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum TicketUrgency: string
{
    use HasEnumValues;

    case LOW = 'Low';
    case MEDIUM = 'Medium';
    case HIGH = 'High';
    case CRITICAL = 'Critical';
}
