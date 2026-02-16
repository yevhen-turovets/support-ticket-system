<?php

namespace App\Services;

use App\DTO\Ticket\CreateTicketDTO;
use App\Jobs\ProcessTicketAIJob;
use App\Models\Ticket;
use App\Repositories\Contracts\TicketRepositoryInterface;

class TicketService
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository
    ) {
    }

    public function create(CreateTicketDTO $createTicketDTO): Ticket
    {
        $ticket = $this->ticketRepository->create([
            'title' => $createTicketDTO->title,
            'description' => $createTicketDTO->description,
            'status' => 'Open',
        ]);

        ProcessTicketAIJob::dispatch($ticket->id);

        return $ticket;
    }

    public function findById(int $id): ?Ticket
    {
        return $this->ticketRepository->findById($id);
    }
}
