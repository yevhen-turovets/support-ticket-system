<?php

namespace App\Http\Controllers;

use App\DTO\Ticket\CreateTicketDTO;
use App\Http\Requests\StoreTicketRequest;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService
    ) {
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $createTicketDTO = new CreateTicketDTO(
            title: $validated['title'],
            description: $validated['description']
        );

        $ticket = $this->ticketService->create($createTicketDTO);

        return response()->json([
            'id' => $ticket->id,
            'status' => $ticket->status,
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $ticket = $this->ticketService->findById($id);

        if ($ticket === null) {
            return response()->json(['message' => 'Ticket not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($ticket);
    }
}
