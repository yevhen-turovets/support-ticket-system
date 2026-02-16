<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\Contracts\AIServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

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

        try {
            $analysis = $aiService->analyzeTicket($ticket->description);
        } catch (Throwable $exception) {
            Log::error('Ticket AI processing failed.', [
                'ticket_id' => $this->ticketId,
                'error' => $exception->getMessage(),
            ]);

            return;
        }

        foreach (['category', 'sentiment', 'reply', 'urgency'] as $key) {
            if (!array_key_exists($key, $analysis) || !is_string($analysis[$key])) {
                Log::error('Ticket AI response is missing required keys.', [
                    'ticket_id' => $this->ticketId,
                    'missing_or_invalid_key' => $key,
                ]);

                return;
            }
        }

        if (!in_array($analysis['urgency'], ['Low', 'Medium', 'High'], true)) {
            Log::error('Ticket AI response has invalid urgency value.', [
                'ticket_id' => $this->ticketId,
                'urgency' => $analysis['urgency'],
            ]);

            return;
        }

        $ticket->category = $analysis['category'];
        $ticket->sentiment = $analysis['sentiment'];
        $ticket->suggested_reply = $analysis['reply'];
        $ticket->urgency = $analysis['urgency'];
        $ticket->save();
    }
}
