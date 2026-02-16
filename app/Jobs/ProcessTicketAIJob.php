<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\Contracts\AIServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTicketAIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $ticketId
    ) {
    }

    public function handle(AIServiceInterface $aiService): void
    {
        $ticket = Ticket::find($this->ticketId);

        if ($ticket === null) {
            return;
        }

        if ($ticket->category !== null) {
            return;
        }

        $analysis = $aiService->analyzeTicket($ticket->description);

        $ticket->category = $analysis['category'];
        $ticket->sentiment = $analysis['sentiment'];
        $ticket->suggested_reply = $analysis['reply'];
        $ticket->urgency = $analysis['urgency'];
        $ticket->save();
    }
}
