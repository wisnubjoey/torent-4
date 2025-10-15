# Implementation Plan: Vehicle Rental Platform MVP

**Branch**: `001-i-want-to` | **Date**: 2025-10-14 | **Spec**: specs/001-i-want-to/spec.md
**Input**: Feature specification from `/specs/001-i-want-to/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. Consult the project command guide (if configured) for the execution workflow.

## Summary

Deliver a Laravel 12 + Inertia 2 + React 19 platform that lets renters book cars and motorcycles with WhatsApp checkout while admins manage fleet availability and rental states. Implement modular Laravel modules (Vehicle, Rental, User, Admin), role-specific dashboards using Shadcn/Tailwind UI, PostgreSQL persistence, OTP-gated phone authentication, and scheduler-driven status automation aligned with the MVP success criteria.

## Technical Context

<!--
  ACTION REQUIRED: Replace the content in this section with the technical details
  for the project. The structure here is presented in advisory capacity to guide
  the iteration process.
-->

**Language/Version**: PHP 8.2 (Laravel 12), TypeScript/React 19, TailwindCSS  
**Primary Dependencies**: Laravel Fortify (OTP + auth guards), Inertia 2, Shadcn UI components, Vite 7, Laravel Scheduler/Queue, Pest, React Testing Library + Vitest  
**Storage**: PostgreSQL 15 (managed through pgAdmin4)  
**Testing**: Pest (unit + feature), Laravel HTTP tests, Vitest + React Testing Library for frontend  
**Target Platform**: Web (responsive desktop + mobile browsers)
**Project Type**: Web application (Laravel backend with React SPA via Inertia)  
**Performance Goals**: Dashboard load <500 ms server time; vehicle detail pages render calendar/media <3 s on 4G; WhatsApp handoff success ≥80%  
**Constraints**: OTP expires within 5 minutes; max five OTP attempts/hour with two-hour lockout; no external payment providers; MVP deployable on single app server  
**Scale/Scope**: Initial MVP supporting ~500 active users, fleet ≤200 vehicles, concurrent admin ops ≤10

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Code Quality Discipline**: Enforce `vendor/bin/pint`, `npm run format:check`, and `npm run lint`; centralize shared logic in `app/Modules/*/Services` and reusable React hooks/components; code review checklist includes duplication scan.
- **Test Coverage for Every Feature**: Backend Pest unit + feature suites for auth, rentals, scheduler; frontend component/integration tests (React Testing Library) for dashboards and booking flow; CI blocks on failing suites.
- **Accessible, Responsive UX**: Tailwind responsive breakpoints (sm/md/lg/xl), enforce WCAG AA contrast, keyboard-accessible calendars and buttons, reuse Shadcn primitives located in `resources/js/components/ui`.
- **Lean Performance Delivery**: Lazy-load vehicle media via `loading="lazy"` and React suspense chunks, cap initial JS bundle <700 KB, compress images through Vite plugins, monitor Lighthouse performance ≥85 on landing/dashboard.
- **Proactive Security Hygiene**: Laravel Form Request validation for all inputs, sanitized Rich text (if any) via Laravel helper, rate limit auth/booking routes, OTP secrets stored in `.env`, ensure CSRF middleware active for all forms.

**Gate Assessment**: PASS — planned architecture, testing strategy, and optimization steps satisfy all constitutional principles with no outstanding violations.

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
    │   ├── Services/
    │   ├── Repositories/
    │   └── Requests/
    ├── Admin/
    │   ├── Controllers/
    │   ├── Services/
    │   ├── Repositories/
    │   └── Requests/
    ├── Vehicle/
    │   ├── Controllers/
    │   ├── Services/
    │   ├── Repositories/
    │   └── Policies/
    └── Rental/
        ├── Controllers/
        ├── Services/
        ├── Repositories/
        ├── Jobs/
        └── Policies/

resources/js/
├── Pages/
│   ├── User/
│   │   └── Dashboard/
│   ├── Admin/
│   │   ├── Dashboard/
│   │   └── Vehicles/
│   └── Auth/
├── Components/
│   ├── ui/ (Shadcn exports)
│   ├── calendars/
│   └── dashboards/
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
│   └── Repositories/
└── Browser/ (Inertia/RTL integration adapters)
```

**Structure Decision**: Adopt Laravel module directories under `app/Modules/*` to group controllers/services/repositories per domain, mirror React role-based pages under `resources/js/Pages/{User|Admin}`, and organize tests into Feature/Unit/Browser suites aligning with constitution mandates.

## Complexity Tracking

*Fill ONLY if Constitution Check has violations that must be justified*

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| [e.g., 4th project] | [current need] | [why 3 projects insufficient] |
| [e.g., Repository pattern] | [specific problem] | [why direct DB access insufficient] |
