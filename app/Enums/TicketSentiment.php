<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum TicketSentiment: string
{
    use HasEnumValues;

    case POSITIVE = 'Positive';
    case NEUTRAL = 'Neutral';
    case NEGATIVE = 'Negative';
}
