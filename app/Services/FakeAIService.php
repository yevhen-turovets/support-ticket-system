<?php

namespace App\Services;

use App\Services\Contracts\AIServiceInterface;

class FakeAIService implements AIServiceInterface
{
    public function analyzeTicket(string $text): array
    {
        sleep(2);

        return [
            'category' => 'General',
            'sentiment' => 'Neutral',
            'reply' => 'Thank you for your message. Our team will review your request shortly.',
            'urgency' => 'Low',
        ];
    }
}
