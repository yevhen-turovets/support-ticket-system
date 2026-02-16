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
