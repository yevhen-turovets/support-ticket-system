<?php

namespace Tests\Feature;

use App\Jobs\ProcessTicketAIJob;
use App\Models\Ticket;
use App\Services\Contracts\AIServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_created(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/tickets', [
            'title' => 'Test Ticket',
            'description' => 'This is a test description',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'status'])
            ->assertJson([
                'status' => 'Open',
            ]);

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'status' => 'Open',
        ]);

        Queue::assertDispatched(ProcessTicketAIJob::class);
    }

    public function test_process_ticket_ai_job_updates_ticket_with_enrichment_data(): void
    {
        $this->app->instance(AIServiceInterface::class, new class implements AIServiceInterface
        {
            public function analyzeTicket(string $text): array
            {
                return [
                    'category' => 'General',
                    'sentiment' => 'Neutral',
                    'reply' => 'Mocked AI reply',
                    'urgency' => 'Low',
                ];
            }
        });

        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'This is a test description',
            'status' => 'Open',
        ]);

        $job = new ProcessTicketAIJob($ticket->id);
        $job->handle($this->app->make(AIServiceInterface::class));

        $ticket->refresh();

        $this->assertSame('General', $ticket->category);
        $this->assertSame('Neutral', $ticket->sentiment);
        $this->assertSame('Mocked AI reply', $ticket->suggested_reply);
        $this->assertSame('Low', $ticket->urgency);
    }

    public function test_ticket_is_not_updated_if_ai_response_is_invalid(): void
    {
        $this->app->instance(AIServiceInterface::class, new class implements AIServiceInterface
        {
            public function analyzeTicket(string $text): array
            {
                return [
                    'category' => 'General',
                    'sentiment' => 'Neutral',
                    'reply' => 'Invalid payload without urgency',
                ];
            }
        });

        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'This is a test description',
            'status' => 'Open',
        ]);

        $job = new ProcessTicketAIJob($ticket->id);
        $job->handle($this->app->make(AIServiceInterface::class));

        $ticket->refresh();

        $this->assertNull($ticket->category);
        $this->assertNull($ticket->sentiment);
        $this->assertNull($ticket->suggested_reply);
        $this->assertNull($ticket->urgency);
    }
}
