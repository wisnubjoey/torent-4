# Repository Guidelines

## Project Structure & Module Organization
Laravel backend code lives in `app/` with HTTP controllers, services, and jobs grouped by feature. Blade is not used; Inertia React views sit under `resources/js` with shared UI in `resources/js/Components` and layouts in `resources/js/Layouts`. Public assets compile to `public/build` via Vite. Config files stay in `config/`, database migrations and seeders in `database/`, and PHP tests in `tests/`. Frontend build settings live in `vite.config.ts`; Tailwind defaults come from `components.json`.

## Build, Test, and Development Commands
- `composer install && npm install` – bootstrap PHP and Node dependencies.
- `cp .env.example .env && php artisan key:generate` – stage a new environment; update DB credentials before migrating.
- `php artisan migrate --force` – apply schema changes to the current database.
- `composer dev` – run Laravel, the queue listener, Pail logs, and Vite in one process manager.
- `npm run dev` – launch Vite only when PHP is running separately.
- `npm run build` / `npm run build:ssr` – produce production bundles; SSR build feeds `composer dev:ssr`.

## Coding Style & Naming Conventions
Run `vendor/bin/pint` to enforce PSR-12 with Laravel-tailored rules; PHP files use 4-space indentation. TypeScript and JSX should follow Prettier defaults (2 spaces, semicolons) and ESLint React guidance; ensure `npm run format:check` and `npm run lint` pass before pushing. Name controllers as `*Controller`, request classes as `*Request`, and React components in `PascalCase.tsx`. Keep Inertia pages under `resources/js/Pages/*` with folders that mirror Laravel route names, and use the shared Inertia layout (`Layouts/AppLayout`) registered via `InertiaServiceProvider`.

## Testing Guidelines
Pest drives backend tests; place feature cases in `tests/Feature` and unit cases in `tests/Unit`. Mirror routes or services in filenames (e.g., `UserRegistrationTest.php`). Use database factories and `RefreshDatabase` when touching persistence. Execute the suite with `composer test` or `php artisan test`; narrow scope with `php artisan test --filter=UserRegistration`. Target high coverage on auth and onboarding flows.

## Commit & Pull Request Guidelines
The existing history uses short, imperative commit messages (e.g., “Add onboarding flow”); keep scope tight and prefer one feature or fix per commit. Reference related issues in the body when applicable. Pull requests should include a concise behavior summary, screenshots or GIFs for UI changes, database/backfill notes, and explicit testing evidence. Request review from someone familiar with the area and confirm the suite is green before assigning.
