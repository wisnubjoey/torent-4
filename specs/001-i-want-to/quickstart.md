# Quickstart — Vehicle Rental Platform MVP

## Prerequisites
- PHP 8.2+ with Composer
- Node.js 18+ with npm
- PostgreSQL 15 (with pgAdmin4 optional)
- Redis (for queues/OTP throttling) — optional but recommended
- WhatsApp Desktop or mobile app for checkout verification

## Environment Setup
1. Clone repository and checkout feature branch:
   ```bash
   git checkout 001-i-want-to
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install Node dependencies:
   ```bash
   npm install
   ```
4. Configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Update `.env` with:
   - `DB_CONNECTION=pgsql`, PostgreSQL credentials and database name
   - `QUEUE_CONNECTION=database` (or redis if available)
   - `FORTIFY_OTP_EXPIRY_MINUTES=5`
   - `FORTIFY_OTP_MAX_ATTEMPTS=5`
   - `WHATSAPP_BASE_URL=https://wa.me`
6. Run migrations and seeders:
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=AdminSeeder
   ```

## Development Workflow
1. Launch scheduler, queue worker, Laravel app, and Vite dev server concurrently:
   ```bash
   composer dev
   ```
2. Access user dashboard at `http://localhost:8000` and admin dashboard at `http://localhost:8000/admin`.
3. Use pgAdmin4 or `psql` to monitor database tables (vehicles, rentals, availability).

## Testing
- Run backend test suite:
  ```bash
  composer test
  ```
- Run frontend component tests:
  ```bash
  npm run test
  ```
- Run lint/format checks:
  ```bash
  vendor/bin/pint
  npm run lint
  npm run format:check
  ```

## OTP & Rate Limiting Verification
- Trigger OTP verification through `/auth/user/otp/verify`; confirm lockout after five failed attempts per hour.
- Ensure OTP expires within five minutes by testing beyond expiry window.

## WhatsApp Checkout Verification
- Create a rental and hit the “Confirm via WhatsApp” action; validate the generated `wa.me` link content and fallback copy-to-clipboard behavior if WhatsApp isn’t installed.

## Scheduler & Automation
- Configure cron entry (e.g., `* * * * * php /path/to/artisan schedule:run`) or run locally:
  ```bash
  php artisan schedule:work
  ```
- Confirm rentals auto-transition from `active` to `completed` when `end_date` passes.
