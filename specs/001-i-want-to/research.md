# Phase 0 Research – Vehicle Rental Platform MVP

## Modular Laravel Architecture with Inertia
- **Decision**: Organize backend code under `app/Modules/{Domain}` with dedicated Controllers, Services, and Repositories while using Inertia 2 to bridge Laravel routes and React 19 pages.
- **Rationale**: Domain modules keep feature logic cohesive, simplify ownership between teams, and align with clean architecture boundaries (controllers → services → repositories). Inertia avoids a REST duplication layer and lets us deliver server-driven routing with SPA-like UX.
- **Alternatives Considered**:
  - Flat `app/Http` structure: rejected because modules would sprawl and blur responsibilities.
  - Full API + SPA split: unnecessary overhead for the MVP and duplicates validation/auth layers.

## Role-Based Dashboards & Routing
- **Decision**: Implement Laravel Fortify guards for `web` (users) and `admin`, with middleware to direct Inertia responses to `resources/js/Pages/User/*` or `Admin/*`.
- **Rationale**: Guard-driven routing keeps authorization centralized and prevents accidental privilege escalation; paired dashboards ensure clarity for each persona.
- **Alternatives Considered**:
  - Single dashboard with conditional rendering: rejected to avoid complex conditional logic and UX confusion.
  - Custom auth without Fortify: rejected because Fortify already covers OTP, rate limiting, and CSRF hardening.

## Frontend Architecture & UI Library Usage
- **Decision**: Use Shadcn component exports inside `resources/js/components/ui` plus shared hooks for calendars, wrapping Tailwind utility classes for consistent styling and accessibility.
- **Rationale**: Shadcn gives accessible primitives compatible with Tailwind, accelerating delivery while preserving a modern look; shared hooks keep date-selection logic reusable across car and bike flows.
- **Alternatives Considered**:
  - Rolling bespoke components: slower and risks inconsistent accessibility.
  - Importing an entire design system (e.g., MUI): heavier bundle and styling divergence from Tailwind.

## Database & Scheduling Strategy
- **Decision**: Model PostgreSQL tables (`users`, `admins`, `vehicles`, `rentals`, `rental_items`, `drivers`, `availability`) with foreign keys and cascading rules; use Laravel Scheduler plus queued jobs to auto-transition rentals by end date.
- **Rationale**: Normalized schema supports reporting and future scalability; scheduler ensures rentals advance without manual admin intervention.
- **Alternatives Considered**:
  - Embedding availability inside rentals table: rejected due to difficulty representing maintenance blocks.
  - Relying solely on manual admin updates: increases operational risk and misses deadlines automation.

## Testing Approach
- **Decision**: Adopt Pest for backend unit/feature tests, use Laravel HTTP tests for Inertia responses, and run Vitest with React Testing Library for dashboard and booking flows.
- **Rationale**: Pest matches Laravel ecosystem tooling; Vitest integrates tightly with Vite/React 19 and provides fast component testing without leaving the repo tooling set.
- **Alternatives Considered**:
  - Jest for frontend tests: additional configuration and slower execution compared to Vitest in a Vite project.
  - Cypress E2E for MVP: deferred until later because integration coverage via Pest + RTL covers core flows with lower setup overhead.

## Security & Rate Limiting
- **Decision**: Enforce Laravel throttle middleware (e.g., `throttle:5,60`) on OTP and login endpoints; store OTP codes hashed in database with five-minute expiry; sanitize WhatsApp message payloads.
- **Rationale**: Aligns with constitution’s Proactive Security Hygiene while honoring spec requirements (max five attempts/hour, OTP expiry). Sanitization prevents injection within WhatsApp deeplinks.
- **Alternatives Considered**:
  - Relying on external OTP provider limits: less controllable and inconsistent across environments.
  - Allowing longer OTP lifetimes: increases attack surface for SIM swap or shared device misuse.
