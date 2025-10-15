# Feature Specification: Vehicle Rental Platform MVP

**Feature Branch**: `001-i-want-to`  
**Created**: 2025-10-14  
**Status**: Draft  
**Input**: User description: "I want to build a web-based vehicle rental platform for both cars and motorcycles, with integrated dashboards for both users and administrators. The main goal is to provide a simple yet functional MVP focusing on usability and management clarity.

Users can register and log in using their phone number only (no email or social login). From the user dashboard, they can see quick actions — primarily "Rent Car" and "Rent Bike" — along with basic statistics of their rentals. Each vehicle has availability data synced with a calendar that indicates available and unavailable dates. Users can also optionally rent a driver. Checkout redirects users to WhatsApp with a pre-filled wrapped message summarizing their order (e.g., one car and two motorcycles). 

Admins can manage the system from an internal dashboard, including CRUD operations for vehicles, updating rental statuses (Pending → Active → Completed), managing available schedules, and tracking deadlines. Payment confirmation updates the order state to Active. The entire workflow should be synchronized between the user and admin dashboards.

For the MVP, focus on:
- A simple, modern landing page.
- A clean dashboard for both roles (user & admin).
- Basic CRUD for vehicles.
- Basic booking and WhatsApp checkout flow.
- Simple state management (Pending → Active → Completed).

Do not focus yet on complex payment gateways, microservices, or driver verification systems — keep it lean and local for MVP delivery."

## Clarifications

### Session 2025-10-14

- Q: What rate limiting applies to phone OTP attempts? → A: Allow up to five OTP attempts per hour, then lock phone number for two hours.

## User Scenarios & Testing *(mandatory)*

<!--
  IMPORTANT: User stories should be PRIORITIZED as user journeys ordered by importance.
  Each user story/journey must be INDEPENDENTLY TESTABLE - meaning if you implement just ONE of them,
  you should still have a viable MVP (Minimum Viable Product) that delivers value.
  
  Assign priorities (P1, P2, P3, etc.) to each story, where P1 is the most critical.
  Think of each story as a standalone slice of functionality that can be:
  - Developed independently
  - Tested independently
  - Deployed independently
  - Demonstrated to users independently
-->

Each story MUST call out the unit and integration tests that will prove it and highlight accessibility, performance, and security
acceptance checks demanded by the constitution.

### User Story 1 - Phone-Based Registration & Dashboard Access (Priority: P1)

Users register and sign in using their phone number, then land on a dashboard showing quick rental actions and personal rental stats.

**Why this priority**: Without frictionless onboarding and a visibility hub, users cannot access rentals or understand their status; this unlocks all other flows.

**Independent Test**: Start with an unregistered phone number, complete the OTP verification, and confirm the dashboard displays quick actions and accurate rental counts for that user.

**Acceptance Scenarios**:

1. **Given** a visitor without an account, **When** they submit a valid phone number and verification code, **Then** a new profile is created and the user is redirected to the dashboard.  
2. **Given** a returning renter with historical bookings, **When** they sign in, **Then** the dashboard displays current pending/active/completed counts and quick access buttons for “Rent Car” and “Rent Bike”.

---

### User Story 2 - Vehicle Discovery & Availability Selection (Priority: P1)

Users explore cars and motorcycles, review availability calendars, and configure rental details including optional driver support.

**Why this priority**: Visibility into inventory and schedules is the core decision-making tool for renters; bookings cannot proceed without it.

**Independent Test**: From the dashboard, navigate to a vehicle, choose dates within available slots, optionally add a driver, and verify that the booking summary reflects the selection.

**Acceptance Scenarios**:

1. **Given** a vehicle with existing blocked dates, **When** the user opens its calendar, **Then** unavailable dates are clearly marked, disabled for selection, and show schedule reasons on hover or tap.  
2. **Given** a renter choosing start/end dates and driver preference, **When** they proceed to confirmation, **Then** the system generates a concise order summary with vehicle count, dates, and driver toggle.

---

### User Story 3 - Booking Confirmation & Admin Oversight (Priority: P1)

Users finalize bookings through a WhatsApp handoff while administrators manage fleet data, schedules, and rental status transitions.

**Why this priority**: A synchronized user/admin workflow ensures rentals progress from request to completion and keeps fleet management aligned with customer commitments.

**Independent Test**: Create a booking, trigger the WhatsApp redirect with a structured message, and confirm that an admin can update the rental through Pending → Active → Completed while both dashboards stay in sync.

**Acceptance Scenarios**:

1. **Given** a newly submitted booking awaiting payment, **When** the admin records payment confirmation, **Then** the rental status moves to Active in both admin tables and the user dashboard.  
2. **Given** an Active rental reaching its end date, **When** the admin marks it Completed, **Then** the user receives updated stats and the rental history records the completion timestamp.

---

### Edge Cases

- Booking attempts on dates already fully booked for the selected vehicle must be blocked with suggestions for the next closest availability.  
- Phone numbers reused without completion of verification must not create duplicate accounts; the system should resume the prior verification flow.  
- Admin attempts to delete a vehicle tied to upcoming rentals should be prevented, prompting them to reassign or complete affected bookings.  
- WhatsApp unavailable on the user device should trigger fallback options (copy message to clipboard and display contact instructions).

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST allow users to register and authenticate using phone number verification without requiring email or social login, enforcing a maximum of five OTP attempts per phone number per hour before a two-hour lockout.  
- **FR-002**: System MUST present a user dashboard showing quick actions to start car or bike rentals and metrics for pending, active, and completed bookings.  
- **FR-003**: System MUST provide vehicle catalogs for cars and motorcycles with filters, detail pages, imagery, pricing, and availability calendars.  
- **FR-004**: System MUST prevent selection of unavailable dates and reflect availability updates in real time when admins adjust schedules.  
- **FR-005**: System MUST allow renters to configure bookings with start/end dates, vehicle counts, and optional driver inclusion before checkout.  
- **FR-006**: System MUST generate a structured WhatsApp message summarizing the booking (vehicle types, quantities, dates, driver choice) and redirect the user to WhatsApp to finalize the request.  
- **FR-007**: System MUST provide an admin dashboard enabling CRUD for vehicles, schedule management, and oversight of all rental orders.  
- **FR-008**: System MUST support rental status transitions of Pending → Active → Completed with audit logs, and ensure updates appear for both user and admin views within two minutes.  
- **FR-009**: System MUST notify admins of upcoming deadlines (e.g., return dates) through dashboard indicators or alerts.  
- **FR-010**: System MUST maintain historical booking data accessible to users and admins for reporting and reconciliation.

### Key Entities *(include if feature involves data)*

- **User**: Represents renter or admin profiles, including phone number, role, and basic contact preferences.  
- **Vehicle**: Captures catalog entries for cars and motorcycles with type, model details, capacity, imagery references, pricing, and status.  
- **ScheduleSlot**: Defines blocks of unavailable time for a specific vehicle, including reason (booking, maintenance) and associated rental if applicable.  
- **RentalOrder**: Stores the renter, selected vehicles, driver option, rental window, current status, audit timestamps, and generated WhatsApp summary.  
- **DashboardMetric**: Aggregated counts and deadline indicators consumed by dashboards (virtual entity derived from underlying records).

### Assumptions & Dependencies

- Phone verification leverages SMS-based codes delivered through an existing provider or a simple OTP gateway configured for the MVP.  
- WhatsApp handoff relies on deep-link URLs and requires the user device to have WhatsApp installed; fallback instructions satisfy users without the app.  
- Admin roles are pre-assigned by maintainers during onboarding; no self-service admin registration is included.  
- Reporting beyond dashboard summaries is deferred to future iterations; export features are out of scope.

### Constitutional Guarantees *(mandatory)*

- **Code Quality Discipline**: Teams will apply repository-standard linters and formatters, keep booking logic modular across services/components, and extract shared availability checks to reusable utilities.  
- **Test Coverage for Every Feature**: Each story introduces unit tests for phone auth, availability calculation, and status transitions plus integration coverage exercising dashboard flows and booking lifecycle.  
- **Accessible, Responsive UX**: Landing and dashboards adapt to mobile and desktop breakpoints, calendars support keyboard navigation, and all interactive elements include accessible labels and contrast-compliant styling.  
- **Lean Performance Delivery**: Vehicle media uses optimized assets with lazy loading, dashboards only fetch essentials on load, and bundle size targets remain within established performance budgets.  
- **Proactive Security Hygiene**: Phone inputs, booking details, and admin forms undergo strict validation and sanitization; role-based access separates user/admin capabilities and no sensitive data appears in logs.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 95% of phone registrations complete and land on the dashboard within two minutes of initiation.  
- **SC-002**: 90% of vehicle detail pages display availability calendars and media in under three seconds on a 4G mobile connection.  
- **SC-003**: 100% of confirmed rentals follow the Pending → Active → Completed lifecycle with status changes appearing on both dashboards within two minutes.  
- **SC-004**: At least 80% of initiated bookings successfully open WhatsApp with a correctly populated summary, with the remainder using the provided fallback without churn complaints.
