# Smart Support Ticket System

## Project Overview
This project is a Laravel-based backend API for creating support tickets and enriching them with AI-generated metadata. When a ticket is created, the system stores it immediately and dispatches a background job that uses an AI service (OpenAI or fake driver) to classify `category`, `sentiment`, `urgency`, and generate `suggested_reply`.

Core technologies:
- Laravel 11
- Queue-based asynchronous processing
- OpenAI integration through a service interface

## Setup Instructions

### Local PHP setup
1. Install dependencies:
```bash
composer install
```
2. Create environment file:
```bash
cp .env.example .env
```
3. Generate app key:
```bash
php artisan key:generate
```
4. Configure database credentials in `.env`.
5. Run migrations:
```bash
php artisan migrate
```

### Laravel Sail setup
1. Start containers:
```bash
./vendor/bin/sail up -d
```
2. Run migrations inside container:
```bash
./vendor/bin/sail artisan migrate
```

## .env Configuration
Set at least the following values:

```env
OPENAI_API_KEY=your_openai_api_key
AI_DRIVER=openai
QUEUE_CONNECTION=database
```

`AI_DRIVER` supports:
- `openai`
- `fake`

## How to Run Migrations
```bash
php artisan migrate
```

## How to Run Queue Worker
```bash
php artisan queue:work
```

## How to Run Tests
Run the full test suite:

```bash
php artisan test
```

Run a specific test class:

```bash
php artisan test --filter=TicketApiTest
```

If you are using Sail:

```bash
./vendor/bin/sail test
```

## API Endpoints

### POST `/api/tickets`
Example request body:

```json
{
  "title": "Login issue",
  "description": "I cannot log into my account."
}
```

### GET `/api/tickets/{id}`
Returns the ticket by ID, including AI-enriched fields when processing is completed.

## Prompt Strategy
The AI prompt enforces strict output behavior:
- JSON-only response (no markdown, no code blocks, no explanations)
- Fixed schema for `category`, `sentiment`, `reply`, `urgency`
- Runtime schema/key validation after response parsing
- Fallback protection: invalid AI responses are rejected and not persisted

## Asynchronous Processing
AI processing runs in a queued job (`ProcessTicketAIJob`) after ticket creation. This keeps the create endpoint responsive and avoids blocking API requests on external AI latency.

Benefits:
- Faster API response for ticket creation
- Better resilience when AI provider is slow or temporarily unavailable
- Clear separation between request handling and enrichment logic

## Switching AI Driver
Use `AI_DRIVER` in `.env`:

```env
AI_DRIVER=fake
# or
AI_DRIVER=openai
```

- `fake`: deterministic mock response for local/testing flows
- `openai`: real OpenAI-powered ticket analysis
