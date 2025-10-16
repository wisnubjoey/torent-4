# Data Model — Vehicle Rental Platform MVP

## Overview
- **Database**: PostgreSQL 15
- **Strategy**: Normalized schema with UUID primary keys, audited timestamps, foreign keys with cascading rules where safe, and materialized views for dashboard metrics and historical reporting.
- **Naming**: Snake_case for tables/columns; enums captured via PostgreSQL `CHECK` constraints or Laravel enum casting.

## Core Tables

### users
- **Fields**: `id`, `phone_number` (unique, E.164), `password_hash`, `otp_verified_at`, `otp_attempts`, `otp_locked_until`, `remember_token`, timestamps.
- **Relationships**: `hasMany` rentals, `hasMany` rental_history_views (via materialized view).
- **Constraints**: enforce phone uniqueness, ensure lock reset when `otp_locked_until` < now.

### admins
- **Fields**: `id`, `username` (unique), `password_hash`, `last_login_at`, timestamps.
- **Relationships**: `hasMany` admin_activity_logs (future extension).

### vehicles
- **Fields**: `id`, `slug` (unique), `name`, `type` (`car`/`motorcycle`), `description`, `base_rate`, `capacity`, `image_url`, `is_active`, timestamps.
- **Relationships**: `hasMany` rental_items, `hasMany` availability blocks.
- **Constraints**: `capacity >= 1`, enforce unique slug.

### rentals
- **Fields**: `id`, `user_id`, `status` (`pending`, `active`, `completed`), `start_date`, `end_date`, `driver_requested`, `total_amount`, `payment_confirmed_at`, `completed_at`, `whatsapp_message`, timestamps.
- **Relationships**: `belongsTo` user, `hasMany` rental_items, `hasMany` rental_events, `hasOne` rental_driver_assignment.
- **Constraints**: `end_date >= start_date`; status transitions managed via application layer + `rental_events`.

### rental_items
- **Fields**: `id`, `rental_id`, `vehicle_id`, `quantity`, `rate_snapshot`, timestamps.
- **Relationships**: `belongsTo` rental, `belongsTo` vehicle.
- **Constraints**: `quantity >= 1`; foreign keys restrict deletion of referenced vehicles when active rentals exist.

### drivers
- **Fields**: `id`, `name`, `phone_number`, `license_number`, `is_active`, `notes`, timestamps.
- **Relationships**: `hasMany` rental_driver_assignments.
- **Constraints**: Unique phone and license numbers.

### rental_driver_assignments
- **Fields**: `id`, `rental_id`, `driver_id`, `assigned_at`, `released_at`, timestamps.
- **Relationships**: `belongsTo` rental, `belongsTo` driver.
- **Constraints**: `released_at` ≥ `assigned_at`.

### availability
- **Fields**: `id`, `vehicle_id`, `start_date`, `end_date`, `status` (`available`/`unavailable`), `reason` (`rental`, `maintenance`, `manual_block`), `rental_id` nullable, timestamps.
- **Relationships**: `belongsTo` vehicle, `belongsTo` rental (optional).
- **Constraints**: Prevent overlapping `unavailable` ranges per vehicle; require `rental_id` when reason=`rental`.

### rental_events
- **Fields**: `id`, `rental_id`, `old_status`, `new_status`, `changed_by` (admin id nullable), `comment`, timestamps.
- **Purpose**: Track audit trail for FR-008 compliance and analytics.

### rental_history_view (materialized)
- **Columns**: `rental_id`, `user_id`, `vehicle_summary`, `period`, `status`, `driver_requested`, `completed_at`, `total_amount`.
- **Usage**: Powers FR-010 (historical booking access) for dashboards; refreshed nightly or on-demand after status changes.

### dashboard_metrics_view (materialized)
- **Columns**: aggregated pending/active/completed counts, upcoming returns within 48h, available vehicle totals.
- **Usage**: Populates user/admin dashboards quickly without heavy joins.

## State Machines

### Rental Status
- Initial: `pending`.
- Transitions:
  - `pending → active` when payment confirmed (manual admin change or automated scheduler).
  - `active → completed` when end date reached or admin marks done.
- Guard Conditions:
  - Transition to `active` requires `payment_confirmed_at` and valid availability locks.
  - Transition to `completed` releases availability, records `completed_at`, and appends `rental_events` entry.

## Validation Rules
- Phone numbers must match E.164 and pass rate-limit checks before OTP dispatch.
- Passwords require minimum length 8, at least one number, one letter.
- WhatsApp messages sanitized to strip newline injection or special characters that break the deep link.
- Availability submissions reject overlapping ranges or invalid date order.
- Driver assignment allowed only when `driver_requested = true` and driver active.

## Data Integrity & Cascades
- Deleting a user cascades to rentals (MVP assumption) but archives rental history entries.
- Vehicles cannot be deleted if future availability exists; use soft deletes (`is_active=false`) instead.
- Scheduler updates availability and rental history view after each status change to maintain dashboard accuracy.
