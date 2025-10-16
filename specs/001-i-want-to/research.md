# Phase 0 Research – Vehicle Rental Platform MVP

## Authentication & Authorization Strategy
- **Decision**: Use Laravel Fortify with custom guards for renters (phone + password) and admins (username + password), storing hashed credentials and pairing sensitive actions (registration, password reset) with short-lived OTP confirmation.
- **Rationale**: Password-based auth satisfies security expectations while aligning with the updated requirement; Fortify handles rate limiting, CSRF, and password reset flows with minimal custom code.
- **Alternatives Considered**:
  - Pure OTP sign-in: rejected due to credential policy and persistent session requirements.
  - Building bespoke auth: higher maintenance and weaker security posture than Fortify.

## OTP Lifecycle & Rate Limiting
- **Decision**: Persist hashed OTP codes with five-minute expiry, enforce five attempts per hour before a two-hour lockout, and throttle booking routes using Laravel’s `RateLimiter` facade.
- **Rationale**: Matches clarified requirement while honoring constitution’s security hygiene; hashed storage and strict throttles reduce abuse potential.
- **Alternatives Considered**:
  - External OTP provider throttling only: inconsistent enforcement and more vendor dependencies.
  - Longer OTP lifetime: increases attack window with little usability gain.

## Modular Laravel Architecture
- **Decision**: Organize backend into `app/Modules/{User,Admin,Vehicle,Rental}` with dedicated Controllers, Services, Requests, Repositories, and Policies, wired through service providers.
- **Rationale**: Keeps code cohesive per domain, eases future scaling, and supports clean architecture separation (controllers delegate to services/repositories).
- **Alternatives Considered**:
  - Flat `app/Http` layout: leads to cross-domain coupling.
  - Microservices: overkill for MVP timeline.

## Inertia + React Frontend Composition
- **Decision**: Use Inertia 2 to serve React 19 pages with Shadcn/Tailwind UI primitives, centralizing shared UI in `resources/js/components/ui` and domain hooks.
- **Rationale**: Provides SPA-like UX without separate API maintenance; Shadcn ensures accessible baseline components consistent with product branding.
- **Alternatives Considered**:
  - Full REST + SPA: duplicates validation/auth logic.
  - Other component libraries (MUI, Ant): heavier bundle, styling conflicts with Tailwind.

## Scheduling & Rental Lifecycle Automation
- **Decision**: Depend on Laravel Scheduler plus queued jobs to advance rentals (`pending → active → completed`) based on payment confirmation and end dates, also updating availability slots.
- **Rationale**: Automates lifecycle enforcement and guarantees calendar accuracy without manual admin intervention.
- **Alternatives Considered**:
  - Manual admin updates only: prone to missed deadlines.
  - External cron service: unnecessary for MVP scope.

## Data Persistence & Reporting
- **Decision**: Model PostgreSQL tables for users, admins, rentals, rental_items, vehicles, drivers, availability, and rental history views to support dashboards and historical records.
- **Rationale**: Normalized schema with FK constraints supports auditability, analytics, and future expansion (payments, driver verification).
- **Alternatives Considered**:
  - JSONB availability blobs: complicate querying.
  - Separate analytics database now: overkill for MVP.

## Testing Stack
- **Decision**: Use Pest for backend unit/feature tests, Laravel HTTP tests for Inertia endpoints, Vitest + React Testing Library for component coverage, and Lighthouse for performance/accessibility audits.
- **Rationale**: Aligns with repo tooling, satisfies constitution test mandate, and keeps frontend tests co-located with components.
- **Alternatives Considered**:
  - Jest instead of Vitest: slower and redundant with Vite.
  - Cypress E2E: deferred until MVP stabilizes; current suites cover core flows.
