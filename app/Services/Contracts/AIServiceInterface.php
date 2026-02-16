<?php

namespace App\Services\Contracts;

interface AIServiceInterface
{
    public function analyzeTicket(string $text): array;
}
