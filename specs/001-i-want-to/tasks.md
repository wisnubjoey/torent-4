---
description: "Task list for Vehicle Rental Platform MVP implementation"
---

# Tasks: Vehicle Rental Platform MVP

**Input**: Design documents from `/specs/001-i-want-to/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: The examples below include test tasks. Tests are REQUIRED — every user story must deliver unit and integration coverage before merge.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`
- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions
- Backend modules live in `app/Modules/{Domain}`
- Frontend pages live in `resources/js/Pages/{Role}`
- Shared UI components live in `resources/js/Components`
- Database assets live in `database/migrations|seeders|factories`
- Tests live in `tests/Feature|Unit|Browser`

<!-- 
  ============================================================================
  IMPORTANT: Tasks below are authoritative work items for this feature.
  ============================================================================
-->

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and baseline tooling from the constitution

- [X] T001 Scaffold domain module directories `app/Modules/{User,Admin,Vehicle,Rental}` and register module service providers in `config/app.php`.
- [X] T002 Install and configure Laravel Fortify/Breeze inertia scaffolding for phone+password (user) and username+password (admin) guards.
- [X] T003 [P] Initialize Redis/queue configuration for rate limiting and queued jobs in `.env`, `config/database.php`, and `config/queue.php`.
- [X] T004 [P] Install Shadcn component library and generate base UI primitives into `resources/js/Components/ui/*` with Tailwind integration.
- [X] T005 [P] Establish shared Inertia layout (`resources/js/Layouts/AppLayout.tsx`) and register in `app/Providers/InertiaServiceProvider.php`.
- [X] T006 Configure Vite + TypeScript aliases for module paths in `vite.config.ts` and `tsconfig.json`.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story work

- [ ] T007 Create PostgreSQL migrations for core tables and materialized views per data-model (`users`, `admins`, `vehicles`, `rentals`, `rental_items`, `drivers`, `rental_driver_assignments`, `availability`, `rental_events`, `rental_history_view`, `dashboard_metrics_view`).
- [ ] T008 Seed baseline admin account, demo vehicles, and sample availability in `database/seeders/AdminSeeder.php` and `VehicleSeeder.php`.
- [ ] T009 Implement shared repository abstractions and service bindings (`app/Support/Repositories/BaseRepository.php`, module providers).
- [ ] T010 Configure Laravel rate limiters for auth + booking routes in `app/Providers/RouteServiceProvider.php` honoring 5 attempts/hour OTP rule.
- [ ] T011 Add global sanitization middleware (`app/Http/Middleware/SanitizeInput.php`) and register in HTTP kernel.
- [ ] T012 Wire role-based guards/middleware and route groups in `config/auth.php` and `routes/web.php` (user vs admin prefixes).
- [ ] T013 Implement scheduler registration (`app/Console/Kernel.php`) and stub queued job classes for rental lifecycle automation.
- [ ] T014 Create shared dashboard metrics service skeleton `app/Modules/Rental/Services/DashboardMetricService.php` returning aggregated counts/upcoming deadlines.

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Phone-Based Registration & Dashboard Access (Priority: P1) 🎯 MVP

**Goal**: Provide secure phone+password onboarding with OTP confirmation and a renter dashboard showing quick actions and personal rental stats.

**Independent Test**: Sign up with phone+password, confirm OTP, log in, and verify dashboard quick actions plus metric counts using seeded rentals.

### Tests for User Story 1 (REQUIRED) ⚠️

- [ ] T015 [P] [US1] Write Pest feature tests for registration/login/OTP lockouts in `tests/Feature/Auth/PhonePasswordAuthTest.php`.
- [ ] T016 [P] [US1] Write Pest feature tests for dashboard metrics API in `tests/Feature/User/DashboardMetricsTest.php`.
- [ ] T017 [P] [US1] Implement Vitest + RTL test for dashboard quick actions and accessibility in `resources/js/Pages/User/Dashboard/__tests__/Dashboard.test.tsx`.

### Implementation for User Story 1

- [ ] T018 [US1] Implement Fortify actions for phone registration, password login, and OTP confirmation in `app/Modules/User/Controllers/AuthController.php` and related handlers.
- [ ] T019 [US1] Create OTP persistence + hashing service in `app/Modules/User/Services/OtpService.php` with expiry + lock logic.
- [ ] T020 [US1] Add OTP verification request + validation rules in `app/Modules/User/Requests/OtpVerifyRequest.php`.
- [ ] T021 [US1] Build dashboard controller delivering metrics/history excerpts in `app/Modules/User/Controllers/DashboardController.php`.
- [ ] T022 [US1] Extend `DashboardMetricService::forUser()` to compute pending/active/completed counts and most recent rentals.
- [ ] T023 [US1] [P] Create React dashboard page `resources/js/Pages/User/Dashboard/Index.tsx` with quick action cards and metric tiles.
- [ ] T024 [US1] [P] Style dashboard components using Shadcn primitives and ensure keyboard-focus states.
- [ ] T025 [US1] Document auth + dashboard flow in `docs/features/auth-dashboard.md` for QA reference.

**Checkpoint**: At this point, User Story 1 should be fully functional and testable independently

---

## Phase 4: User Story 2 - Vehicle Discovery & Availability Selection (Priority: P1)

**Goal**: Allow renters to browse vehicles, review availability calendars, configure rentals by date, and request an optional driver.

**Independent Test**: From dashboard, open vehicle catalog, view detail calendar, select available range, toggle driver, and confirm booking summary updates correctly.

### Tests for User Story 2 (REQUIRED) ⚠️

- [ ] T026 [P] [US2] Write Pest unit tests covering availability overlap calculations in `tests/Unit/Services/AvailabilityServiceTest.php`.
- [ ] T027 [P] [US2] Write Pest feature tests for vehicle listing/filtering endpoints in `tests/Feature/Vehicles/VehicleBrowseTest.php`.
- [ ] T028 [P] [US2] Implement Vitest + RTL test for calendar selection + driver toggle UI in `resources/js/Pages/User/Vehicles/__tests__/VehicleDetail.test.tsx`.

### Implementation for User Story 2

- [ ] T029 [US2] Implement availability repository/service logic in `app/Modules/Vehicle/Services/AvailabilityService.php` referencing `availability` table.
- [ ] T030 [US2] Build vehicle controller + filters in `app/Modules/Vehicle/Controllers/VehicleController.php` aligning with `/vehicles` contracts.
- [ ] T031 [US2] [P] Add Inertia pages `resources/js/Pages/User/Vehicles/Index.tsx` and `Show.tsx` with data loaders.
- [ ] T032 [US2] [P] Develop shared calendar component `resources/js/Components/calendars/VehicleAvailabilityCalendar.tsx` with disabled + tooltip states.
- [ ] T033 [US2] Implement booking draft hook/store `resources/js/hooks/useBookingDraft.ts` handling dates, quantities, driver flag.
- [ ] T034 [US2] Validate booking inputs via `app/Modules/Rental/Requests/CreateRentalRequest.php` enforcing date logic + driver flag.
- [ ] T035 [US2] Integrate availability checks in `app/Modules/Rental/Services/RentalService.php` when staging rentals.
- [ ] T036 [US2] Generate WhatsApp message builder utility `resources/js/lib/buildWhatsAppMessage.ts` returning structured summary.
- [ ] T037 [US2] Provide empty/maintenance states and accessibility copy for unavailable calendars in React pages.
- [ ] T038 [US2] Update contracts routing definitions in `routes/web.php` for `/vehicles` and `/vehicles/{id}`.

**Checkpoint**: At this point, User Stories 1 AND 2 should both work independently

---

## Phase 5: User Story 3 - Booking Confirmation & Admin Oversight (Priority: P1)

**Goal**: Enable booking submission with WhatsApp checkout (including fallback), admin dashboards with CRUD, deadline alerts, and historical rental visibility.

**Independent Test**: Complete a booking, open WhatsApp link (or fallback), have admin confirm payment to activate rental, observe auto-complete on end date, view upcoming deadlines and history on dashboards.

### Tests for User Story 3 (REQUIRED) ⚠️

- [ ] T039 [P] [US3] Write Pest feature tests for rental lifecycle transitions and scheduler job in `tests/Feature/Rentals/RentalLifecycleTest.php`.
- [ ] T040 [P] [US3] Write Pest feature tests for admin vehicle CRUD + availability management in `tests/Feature/Admin/AdminVehicleManagementTest.php`.
- [ ] T041 [P] [US3] Write Pest feature tests for historical rentals and deadline alerts in `tests/Feature/Admin/AdminDashboardHistoryTest.php`.
- [ ] T042 [P] [US3] Implement Vitest + RTL tests for admin dashboard interactions in `resources/js/Pages/Admin/Dashboard/__tests__/Dashboard.test.tsx`.

### Implementation for User Story 3

- [ ] T043 [US3] Implement rental creation + WhatsApp link controller methods in `app/Modules/Rental/Controllers/RentalController.php`.
- [ ] T044 [US3] [P] Build WhatsApp checkout button with fallback copy-to-clipboard in `resources/js/Components/booking/WhatsAppCheckoutButton.tsx`.
- [ ] T045 [US3] Implement `/rentals/{id}/whatsapp` route + sanitization helper ensuring safe deeplinks.
- [ ] T046 [US3] Develop admin dashboard controller/views in `app/Modules/Admin/Controllers/DashboardController.php` & `resources/js/Pages/Admin/Dashboard/Index.tsx` displaying metrics, deadlines, history list.
- [ ] T047 [US3] [P] Implement admin vehicles management pages `resources/js/Pages/Admin/Vehicles/Index.tsx` with CRUD forms and policy checks.
- [ ] T048 [US3] Implement admin rental status update endpoint `/admin/rentals/{id}/status` with guard rails.
- [ ] T049 [US3] Configure scheduler job `app/Modules/Rental/Jobs/AutoCompleteRentals.php` + queue listener to transition statuses and refresh views.
- [ ] T050 [US3] [P] Implement admin availability controller `app/Modules/Admin/Controllers/AvailabilityController.php` handling manual blocks.
- [ ] T051 [US3] Log rental events in `app/Modules/Rental/Services/RentalStatusLogger.php` with audit trail entries.
- [ ] T052 [US3] Implement rental history repository/service (`app/Modules/Rental/Services/RentalHistoryService.php`) powering `/user/rentals/history` and admin timeline.
- [ ] T053 [US3] Add Inertia page `resources/js/Pages/User/Rentals/History.tsx` listing historical rentals with filters.
- [ ] T054 [US3] Integrate upcoming deadline calculations in `DashboardMetricService::forAdmin()` and surface alerts in UI.
- [ ] T055 [US3] Update quickstart documentation (`quickstart.md`) with WhatsApp fallback, deadline alerts, and history verification steps.
- [ ] T056 [US3] Add browser/integration harness (e.g., Laravel Dusk or Inertia testing helpers) to verify WhatsApp redirect + fallback at `tests/Browser/Booking/WhatsAppCheckoutTest.php`.

**Checkpoint**: All user stories should now be independently functional

---

## Phase N: Polish & Cross-Cutting Concerns

**Purpose**: Repository-wide improvements following story completion

- [ ] T057 [P] Update README/AGENTS.md with module usage, auth changes, and testing commands.
- [ ] T058 [P] Run Lighthouse audits and capture metrics in `docs/performance-report.md` ensuring ≥85 score.
- [ ] T059 Conduct security review checklist (validation, CSRF, rate limits) and document in `docs/security-review.md`.
- [ ] T060 [P] Wire analytics/logging hooks in `app/Modules/Rental/Services/AnalyticsEmitter.php` for rental events + WhatsApp conversions.

---

## Dependencies & Execution Order

- **Setup (Phase 1)**: No dependencies — runs first.
- **Foundational (Phase 2)**: Depends on Setup completion — BLOCKS all user stories.
- **User Story 1 (Phase 3)**: Requires Phase 2 completion; enables authenticated access and dashboard metrics.
- **User Story 2 (Phase 4)**: Depends on Phase 3 (dashboard navigation) and foundational availability schema.
- **User Story 3 (Phase 5)**: Depends on Phases 3 & 4 (booking draft + availability) to complete lifecycle flows.
- **Polish (Final Phase)**: Depends on all user stories finishing.

### Parallel Opportunities

- Phase 1 tasks T003–T005 can run alongside module scaffolding once Fortify install starts.
- In Phase 3, backend metric work (T022) and React dashboard (T023) can proceed in parallel.
- In Phase 4, availability service (T029) and calendar UI (T032) are parallelizable streams.
- In Phase 5, admin UI (T046/T047) can develop while scheduler/job logic (T049) and history services (T052) advance.
- All test tasks marked [P] may run parallel once relevant functionality is implemented.

### Parallel Example: User Story 3

```bash
# Run admin dashboard UI and rental history services in parallel once endpoints exist
Task: "Develop admin dashboard controller/views in app/Modules/Admin/Controllers/DashboardController.php & resources/js/Pages/Admin/Dashboard/Index.tsx"
Task: "[P] Implement admin vehicles management pages resources/js/Pages/Admin/Vehicles/Index.tsx with CRUD forms and policy checks"
Task: "Implement rental history repository/service (app/Modules/Rental/Services/RentalHistoryService.php) powering /user/rentals/history and admin timeline"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1 Setup.
2. Complete Phase 2 Foundational tasks.
3. Deliver User Story 1 (auth + dashboard) and run associated tests.
4. Validate OTP/password flows and dashboard metrics before continuing.

### Incremental Delivery

1. Ship US1 for early feedback on onboarding and dashboard metrics.
2. Add US2 to unlock availability browsing and booking configuration.
3. Layer US3 to finalize checkout, admin oversight, and automation.

### Parallel Team Strategy

1. Backend-focused dev handles modules, repositories, and scheduler.
2. Frontend-focused dev builds React pages/components with RTL coverage.
3. QA/automation engineer writes Pest/Vitest suites and browser harness.
4. Daily sync on shared services (Dashboard metrics, RentalService) to manage dependencies.
