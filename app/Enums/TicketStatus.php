<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum TicketStatus: string
{
    use HasEnumValues;

    case OPEN = 'Open';
    case IN_PROGRESS = 'In Progress';
    case RESOLVED = 'Resolved';
    case CLOSED = 'Closed';
}
