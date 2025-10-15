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

- [ ] T001 Initialize module folder scaffolding: `app/Modules/{User,Admin,Vehicle,Rental}` with placeholder Controllers/Services/Repositories.
- [ ] T002 Configure Laravel Fortify with custom providers for phone + username auth in `config/fortify.php`.
- [ ] T003 [P] Install Shadcn component library baseline into `resources/js/components/ui/` and Tailwind presets.
- [ ] T004 [P] Establish Inertia shared layout (`resources/js/Layouts/AppLayout.tsx`) and register in `app/Providers/InertiaServiceProvider.php`.
- [ ] T005 Create base Vite aliases for module imports in `vite.config.ts` and TypeScript paths in `tsconfig.json`.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story work

- [ ] T006 Create PostgreSQL migrations for `users`, `admins`, `vehicles`, `rentals`, `rental_items`, `drivers`, `rental_driver_assignments`, `availability` per data model.
- [ ] T007 Seed baseline admin account and demo vehicles via `database/seeders/AdminSeeder.php` and `VehicleSeeder.php`.
- [ ] T008 Implement shared repository abstractions (`app/Support/Repositories/BaseRepository.php`) and service provider bindings.
- [ ] T009 Configure rate limiting (`app/Providers/RouteServiceProvider.php`) for OTP/login/bookings aligning with research decisions.
- [ ] T010 Set up global validation + sanitization middleware (`app/Http/Middleware/SanitizeInput.php`) and register in kernel.
- [ ] T011 Wire role-based middleware and guards in `config/auth.php` and route groups in `routes/web.php`.
- [ ] T012 Add shared dashboard metric query builder in `app/Modules/Rental/Services/DashboardMetricService.php`.
- [ ] T013 Establish scheduler + queue worker scripts: define `app/Console/Kernel.php` schedule job stub for rental status updates.

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Phone-Based Registration & Dashboard Access (Priority: P1) 🎯 MVP

**Goal**: Enable phone OTP onboarding and deliver a renter dashboard with quick actions and stats.

**Independent Test**: Verify new phone registration, OTP verification, and dashboard metrics render using seeded rentals.

### Tests for User Story 1 (REQUIRED) ⚠️

- [ ] T014 [P] [US1] Write Pest feature tests for phone registration + OTP lockouts in `tests/Feature/Auth/PhoneRegistrationTest.php`.
- [ ] T015 [P] [US1] Implement Vitest + RTL test for user dashboard quick actions/state counts in `resources/js/Pages/User/Dashboard/__tests__/Dashboard.test.tsx`.

### Implementation for User Story 1

- [ ] T016 [US1] Build Fortify phone registration + login controllers in `app/Modules/User/Controllers/AuthController.php` with OTP workflows.
- [ ] T017 [US1] Create OTP validation request + rate limiter logic in `app/Modules/User/Requests/OtpVerifyRequest.php`.
- [ ] T018 [US1] Implement OTP persistence service (`app/Modules/User/Services/OtpService.php`) respecting expiry + lock rules.
- [ ] T019 [US1] [P] Design user dashboard controller + Inertia response in `app/Modules/User/Controllers/DashboardController.php`.
- [ ] T020 [US1] [P] Build React dashboard page `resources/js/Pages/User/Dashboard/Index.tsx` with quick actions + metric cards.
- [ ] T021 [US1] Add dashboard metric queries in `DashboardMetricService::forUser()` and expose to Inertia props.
- [ ] T022 [US1] Style dashboard using Shadcn cards and Tailwind; ensure accessibility states for buttons.
- [ ] T023 [US1] Document OTP + dashboard flow in `resources/js/Pages/User/Dashboard/README.md`.

**Checkpoint**: At this point, User Story 1 should be fully functional and testable independently

---

## Phase 4: User Story 2 - Vehicle Discovery & Availability Selection (Priority: P1)

**Goal**: Let renters browse cars/motorcycles, inspect availability, choose dates, and configure driver option.

**Independent Test**: Start from dashboard, open vehicle detail, select available dates, toggle driver, and see accurate booking summary validation.

### Tests for User Story 2 (REQUIRED) ⚠️

- [ ] T024 [P] [US2] Write Pest unit tests for availability overlap logic in `tests/Unit/Services/AvailabilityServiceTest.php`.
- [ ] T025 [P] [US2] Add Pest feature tests covering vehicle listing + filtering in `tests/Feature/Vehicles/VehicleBrowseTest.php`.
- [ ] T026 [P] [US2] Implement Vitest + RTL test for calendar selection + driver toggle in `resources/js/Pages/User/Vehicles/__tests__/VehicleDetail.test.tsx`.

### Implementation for User Story 2

- [ ] T027 [US2] Implement availability repository + service in `app/Modules/Vehicle/Services/AvailabilityService.php`.
- [ ] T028 [US2] Build vehicle listing controller + filters in `app/Modules/Vehicle/Controllers/VehicleController.php`.
- [ ] T029 [US2] [P] Create Inertia routes + pages `resources/js/Pages/User/Vehicles/Index.tsx` and `Show.tsx` with calendar integration.
- [ ] T030 [US2] [P] Develop shared calendar component in `resources/js/Components/calendars/VehicleAvailabilityCalendar.tsx`.
- [ ] T031 [US2] Add driver option handling in booking draft store `resources/js/hooks/useBookingDraft.ts`.
- [ ] T032 [US2] Generate WhatsApp message builder utility in `resources/js/lib/buildWhatsAppMessage.ts`.
- [ ] T033 [US2] Validate server-side selection via Form Request `app/Modules/Rental/Requests/CreateRentalRequest.php`.
- [ ] T034 [US2] Integrate availability checks within rental service `app/Modules/Rental/Services/RentalService.php`.
- [ ] T035 [US2] Update contract implementation for `/vehicles` and `/vehicles/{id}` endpoints in `routes/web.php`.
- [ ] T036 [US2] Provide user-facing empty/maintenance states in UI with accessible messaging.

**Checkpoint**: At this point, User Stories 1 AND 2 should both work independently

---

## Phase 5: User Story 3 - Booking Confirmation & Admin Oversight (Priority: P1)

**Goal**: Complete booking submission, WhatsApp checkout, and synchronized admin controls for rentals, availability, and analytics.

**Independent Test**: Submit booking, open WhatsApp link, admin confirms payment (status → Active), scheduler auto-completes at end date, dashboards sync.

### Tests for User Story 3 (REQUIRED) ⚠️

- [ ] T037 [P] [US3] Add Pest feature tests for rental lifecycle transitions in `tests/Feature/Rentals/RentalLifecycleTest.php`.
- [ ] T038 [P] [US3] Write Pest feature tests for admin vehicle CRUD and availability management in `tests/Feature/Admin/AdminVehicleManagementTest.php`.
- [ ] T039 [P] [US3] Create Vitest + RTL test for admin dashboard stats + status update interactions in `resources/js/Pages/Admin/Dashboard/__tests__/Dashboard.test.tsx`.

### Implementation for User Story 3

- [ ] T040 [US3] Implement rental creation endpoint + WhatsApp link generator in `app/Modules/Rental/Controllers/RentalController.php`.
- [ ] T041 [US3] [P] Build WhatsApp confirmation button component in `resources/js/Components/booking/WhatsAppCheckoutButton.tsx`.
- [ ] T042 [US3] Develop admin dashboard controller in `app/Modules/Admin/Controllers/DashboardController.php` using metrics service.
- [ ] T043 [US3] [P] Create admin Inertia pages `resources/js/Pages/Admin/Dashboard/Index.tsx` and `resources/js/Pages/Admin/Vehicles/Index.tsx`.
- [ ] T044 [US3] Implement admin rental status update endpoint mapped to `/admin/rentals/{id}/status`.
- [ ] T045 [US3] Wire Laravel scheduler job `app/Modules/Rental/Jobs/AutoCompleteRentals.php` and register in console kernel.
- [ ] T046 [US3] [P] Implement admin availability management UI + controller `app/Modules/Admin/Controllers/AvailabilityController.php`.
- [ ] T047 [US3] Ensure audit logging for rental transitions in `app/Modules/Rental/Services/RentalStatusLogger.php`.
- [ ] T048 [US3] Connect driver assignment flow, optional, via `app/Modules/Rental/Controllers/DriverAssignmentController.php`.
- [ ] T049 [US3] Update quickstart docs with WhatsApp + scheduler validation steps (`quickstart.md` additions).
- [ ] T050 [US3] Add browser/integration test harness bootstrap (e.g., Dusk or Inertia testing utils) if needed for WhatsApp redirection verification.

**Checkpoint**: All user stories should now be independently functional

---

## Phase N: Polish & Cross-Cutting Concerns

**Purpose**: Repository-wide improvements following story completion

- [ ] T051 [P] Update README/AGENTS.md with module usage, commands, and dashboards overview.
- [ ] T052 [P] Run Lighthouse audits and optimize media sizes documented in `docs/performance-report.md`.
- [ ] T053 Conduct security review checklist (validation, CSRF, rate limits) and document in `docs/security-review.md`.
- [ ] T054 [P] Finalize analytics instrumentation hooks (logging) for rentals in `app/Modules/Rental/Services/AnalyticsEmitter.php`.

---

## Dependencies & Execution Order

- **Setup (Phase 1)**: No dependencies - can start immediately.
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories.
- **User Story 1 (Phase 3)**: Requires Foundational phase. Unlocks authentication and dashboard flows.
- **User Story 2 (Phase 4)**: Depends on Phase 3 only for authenticated dashboard entry point; availability services rely on migrations from Phase 2.
- **User Story 3 (Phase 5)**: Depends on Phases 3 and 4 for booking creation and availability integration.
- **Polish (Final Phase)**: Depends on completion of all targeted user stories.

### Parallel Opportunities

- Phase 1 tasks T003 and T004 can proceed in parallel with backend scaffolding.
- Within Phase 3, UI (T020) and backend metrics (T021) can run concurrently once controllers stubbed.
- Phase 4 calendar UI (T030) and driver toggle logic (T031) are parallelizable.
- Phase 5 admin UI (T043) can proceed while scheduler/job logic (T045) is implemented.
- Testing tasks marked [P] across all phases can run in parallel after relevant code sections exist.

### Parallel Example: User Story 2

```bash
# Execute availability service and calendar UI workstreams simultaneously
Task: "Implement availability repository + service in app/Modules/Vehicle/Services/AvailabilityService.php"
Task: "[P] Create Inertia routes + pages resources/js/Pages/User/Vehicles/Index.tsx and Show.tsx with calendar integration"
Task: "[P] Develop shared calendar component in resources/js/Components/calendars/VehicleAvailabilityCalendar.tsx"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1 Setup.
2. Complete Phase 2 Foundational (critical infrastructure).
3. Deliver Phase 3 (US1) to enable phone onboarding and dashboard metrics.
4. Validate OTP + dashboard flow end to end before proceeding.

### Incremental Delivery

1. Deploy US1 once stable for early feedback on authentication/dashboard.
2. Add US2 for availability browsing and booking configuration; release once independent tests pass.
3. Layer US3 for WhatsApp checkout and admin management, ensuring scheduler automation works.

### Parallel Team Strategy

1. Team A handles backend modules (Services, Controllers) per story.
2. Team B builds React pages/components with RTL coverage.
3. Dedicated QA/tester executes Pest/Vitest suites as tasks complete.
4. Sync daily on shared services (Dashboard metrics, RentalService) to avoid merge conflicts.
