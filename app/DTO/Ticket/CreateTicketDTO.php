<?php

namespace App\DTO\Ticket;

class CreateTicketDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $description
    ) {
    }
}
