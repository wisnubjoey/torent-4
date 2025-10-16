# Implementation Plan: Vehicle Rental Platform MVP

**Branch**: `001-i-want-to` | **Date**: 2025-10-14 | **Spec**: specs/001-i-want-to/spec.md
**Input**: Feature specification from `/specs/001-i-want-to/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. Consult the project command guide (if configured) for the execution workflow.

## Summary

Deliver a Laravel 12 + Inertia 2 + React 19 rental platform where renters book cars or motorcycles with optional drivers, while admins manage fleet availability and rental lifecycles. Implement modular Laravel modules (User, Admin, Vehicle, Rental), role-based dashboards using Shadcn/Tailwind UI, PostgreSQL persistence, phone-number authentication with passwords plus OTP confirmation, and WhatsApp checkout links backed by scheduler-driven status automation.

## Technical Context

<!--
  ACTION REQUIRED: Replace the content in this section with the technical details
  for the project. The structure here is presented in advisory capacity to guide
  the iteration process.
-->

**Language/Version**: PHP 8.2 (Laravel 12), TypeScript/React 19, TailwindCSS  
**Primary Dependencies**: Laravel Fortify/Breeze (custom guards), Inertia 2, Shadcn UI, Vite 7, Laravel Scheduler/Queue, Pest, Vitest + React Testing Library, Laravel Pint, Prettier, ESLint  
**Storage**: PostgreSQL 15 (managed with pgAdmin4), Redis (rate limiting + queues, optional)  
**Testing**: Pest (unit + feature), Laravel HTTP tests, Vitest + React Testing Library for frontend components, Lighthouse for UX performance audits  
**Target Platform**: Web (responsive desktop + mobile browsers)
**Project Type**: Web application (Laravel backend bridged to React via Inertia)  
**Performance Goals**: Dashboard server response <500 ms, vehicle detail page fully interactive <3 s on 4G, WhatsApp checkout success ≥80% with fallback, OTP verification round-trip ≤2 min  
**Constraints**: OTP expires in 5 minutes; limit 5 OTP attempts/hour with two-hour lockout; password authentication required for users/admins; single-app-server MVP, no external payment gateway  
**Scale/Scope**: Target launch for ≤500 active users, ≤200 vehicles, ≤10 concurrent admins; architecture must leave room for future payment + driver verification integrations

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Code Quality Discipline**: Automate Pint, ESLint, and Prettier in CI; enforce domain-driven module boundaries (`app/Modules/*`), shared hooks/components for availability logic, and review checklist to flag duplication.
- **Test Coverage for Every Feature**: Required suites: Pest unit (services, repositories), Pest feature (auth, rentals, availability, admin flows), Laravel HTTP tests for Inertia responses, Vitest + RTL for dashboards and booking UI. CI fails on any red suite.
- **Accessible, Responsive UX**: Tailwind breakpoints sm→2xl, reusable Shadcn components with aria props, keyboard-accessible calendars, color contrast ≥ WCAG AA, include Lighthouse accessibility scans ≥90.
- **Lean Performance Delivery**: Lazy-load media/cards, leverage Vite image optimization, chunk React routes, cap initial JS bundle <700 KB, monitor bundle analyzer, run Lighthouse performance ≥85.
- **Proactive Security Hygiene**: Use Laravel Form Requests for validation, sanitize WhatsApp payloads, enforce rate limit middleware for auth + booking, store secrets in `.env`, enable CSRF middleware, hash OTP codes, log access with PII scrubbing.

**Gate Assessment**: PASS — planned processes satisfy all constitutional principles; deviations will require explicit mitigation if discovered later.

## Project Structure

### Documentation (this feature)

```
specs/[###-feature]/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)
<!--
  ACTION REQUIRED: Replace the placeholder tree below with the concrete layout
  for this feature. Delete unused options and expand the chosen structure with
  real paths (e.g., apps/admin, packages/something). The delivered plan must
  not include Option labels.
-->

```
app/
└── Modules/
    ├── User/
    │   ├── Controllers/
    │   ├── Requests/
    │   ├── Services/
    │   ├── Repositories/
    │   └── Policies/
    ├── Admin/
    │   ├── Controllers/
    │   ├── Requests/
    │   ├── Services/
    │   ├── Repositories/
    │   └── Policies/
    ├── Vehicle/
    │   ├── Controllers/
    │   ├── Services/
    │   ├── Repositories/
    │   └── Policies/
    └── Rental/
        ├── Controllers/
        ├── Requests/
        ├── Services/
        ├── Repositories/
        ├── Jobs/
        └── Events/

resources/js/
├── Layouts/
├── Pages/
│   ├── Auth/
│   ├── User/
│   │   └── Dashboard/
│   └── Admin/
│       ├── Dashboard/
│       └── Vehicles/
├── Components/
│   ├── ui/ (Shadcn exports)
│   ├── calendars/
│   ├── booking/
│   └── charts/
└── hooks/

database/
├── migrations/
├── seeders/
└── factories/

tests/
├── Feature/
│   ├── Auth/
│   ├── Rentals/
│   └── Admin/
├── Unit/
│   ├── Services/
│   ├── Repositories/
│   └── Policies/
└── Browser/ (Inertia/RTL integration adapters)
```

**Structure Decision**: Adopt Laravel domain modules under `app/Modules/*` to encapsulate controllers/services/repositories per bounded context, mirror role-specific React pages in `resources/js/Pages/{Auth|User|Admin}`, centralize shared components/hooks, and organize tests into Feature/Unit/Browser suites to meet constitutional quality gates.

## Complexity Tracking

*Fill ONLY if Constitution Check has violations that must be justified*

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| [e.g., 4th project] | [current need] | [why 3 projects insufficient] |
| [e.g., Repository pattern] | [specific problem] | [why direct DB access insufficient] |
