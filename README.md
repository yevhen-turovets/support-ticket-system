# Ticket System (Laravel)

Minimal setup and run instructions for this project.

## Requirements

- Docker Desktop (or Docker Engine + Docker Compose)
- Git

## Run With Laravel Sail

1. Clone the repository and go to the project directory.
2. Copy environment file:

```bash
cp .env.example .env
```

3. Install PHP dependencies (including Sail):

```bash
composer install
```

4. Start containers:

```bash
./vendor/bin/sail up -d
```

5. Generate app key:

```bash
./vendor/bin/sail artisan key:generate
```

6. Run database migrations:

```bash
./vendor/bin/sail artisan migrate
```

7. Install frontend dependencies and build assets:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Application URL: `http://localhost`

## AI & Queue Setup

Set these values in `.env`:

```bash
OPENAI_API_KEY=your_api_key
OPENAI_MODEL=your_model
QUEUE_CONNECTION=database
```

Make sure migrations are applied:

```bash
./vendor/bin/sail artisan migrate
```

Start a queue worker (required for AI processing jobs):

```bash
# Local
php artisan queue:work

# Sail
./vendor/bin/sail artisan queue:work
```

Note: `POST /api/tickets` dispatches background AI processing. If no worker is running, `category`, `sentiment`, `suggested_reply`, and `urgency` will remain unset.

## Useful Sail Commands

```bash
# Stop containers
./vendor/bin/sail down

# Start containers in foreground
./vendor/bin/sail up

# Run tests
./vendor/bin/sail test

# Open a shell in the app container
./vendor/bin/sail shell
```
