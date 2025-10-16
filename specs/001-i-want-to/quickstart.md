# Quickstart — Vehicle Rental Platform MVP

## Prerequisites
- PHP 8.2+, Composer
- Node.js 18+, npm
- PostgreSQL 15 (pgAdmin4 optional)
- Redis (recommended for queues/rate limiting)
- WhatsApp Desktop/mobile for checkout validation

## Environment Setup
1. Checkout feature branch:
   ```bash
   git checkout 001-i-want-to
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with:
   - `DB_CONNECTION=pgsql` and credentials
   - `QUEUE_CONNECTION=database` (or redis)
   - `CACHE_STORE=redis` (optional for rate limiting)
   - `OTP_EXPIRY_MINUTES=5`, `OTP_MAX_ATTEMPTS=5`
   - `WHATSAPP_BASE_URL=https://wa.me`
4. Run migrations and seeders:
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=AdminSeeder
   php artisan db:seed --class=VehicleSeeder
   ```

## Development Workflow
1. Launch dev services:
   ```bash
   composer dev
   ```
   This starts Laravel server, queue worker, scheduler, logs, and Vite.
2. Access dashboards:
   - User dashboard: `http://localhost:8000`
   - Admin dashboard: `http://localhost:8000/admin`
3. Manage PostgreSQL data via pgAdmin4 or `psql`.

## Testing
- Backend suites:
  ```bash
  composer test
  ```
- Frontend component tests:
  ```bash
  npm run test
  ```
- Static analysis/lint:
  ```bash
  vendor/bin/pint
  npm run lint
  npm run format:check
  ```
- Performance accessibility audit:
  ```bash
  npm run build && npm run analyze:lighthouse
  ```

## Feature Verification
- **Authentication**: Register with phone + password, confirm OTP; verify lockout after 5 failed attempts in an hour.
- **Vehicle Availability**: Seed rentals, confirm calendars disable blocked dates and suggest alternatives.
- **Booking & WhatsApp**: Create booking, trigger WhatsApp link, test fallback copy if app unavailable.
- **Admin Oversight**: Confirm CRUD on vehicles, status transitions (`pending → active → completed`), deadline alerts, and historical rentals view.
- **Scheduler**: Run locally:
  ```bash
  php artisan schedule:work
  ```
  Validate automatic completion and availability release at rental end dates.
