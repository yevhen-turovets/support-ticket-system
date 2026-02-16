<?php

namespace App\Repositories\Contracts;

use App\Models\Ticket;

interface TicketRepositoryInterface
{
    public function create(array $data): Ticket;

    public function findById(int $id): ?Ticket;
}
