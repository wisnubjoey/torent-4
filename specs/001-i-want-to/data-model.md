# Data Model — Vehicle Rental Platform MVP

## Overview
- **Database**: PostgreSQL 15
- **Schema Strategy**: Normalized domain tables with foreign keys, cascading deletes where appropriate, and unique constraints to enforce business rules.
- **Naming Convention**: Snake_case table and column names; UUID primary keys (default Laravel `uuid` casting) for externally exposed identifiers.

## Entities

### users
- **Purpose**: Stores renter accounts authenticated via phone number.
- **Key Fields**:
  - `id` (uuid, PK)
  - `phone_number` (string, unique, E.164)
  - `password_hash` (string, bcrypt-hashed password)
  - `otp_verified_at` (timestamp nullable)
  - `otp_attempts` (integer default 0)
  - `otp_locked_until` (timestamp nullable)
  - `created_at`, `updated_at`
- **Indexes**: unique index on `phone_number`; composite index on `(otp_locked_until, phone_number)` for rate limiting lookup.
- **Relationships**: `hasMany` rentals
- **Validation**:
  - Phone numbers must match E.164 pattern.
  - Password min 8 chars, contains at least one digit.

### admins
- **Purpose**: Represents administrative accounts with extended privileges.
- **Key Fields**:
  - `id` (uuid, PK)
  - `username` (string, unique)
  - `password_hash` (string)
  - `last_login_at` (timestamp nullable)
  - `created_at`, `updated_at`
- **Relationships**: None direct; interacts with rentals via audits.
- **Validation**: Username alphanumeric 4–32 chars; password same policy as users.

### vehicles
- **Purpose**: Catalog of rentable cars and motorcycles.
- **Key Fields**:
  - `id` (uuid, PK)
  - `slug` (string, unique)
  - `name` (string)
  - `type` (enum: `car`, `motorcycle`)
  - `description` (text)
  - `base_rate` (decimal(10,2))
  - `capacity` (integer)
  - `image_url` (string nullable)
  - `is_active` (boolean default true)
  - `created_at`, `updated_at`
- **Relationships**: `hasMany` rental_items; `hasMany` availability entries.
- **Validation**: Base rate ≥ 0; capacity ≥ 1; type restricted to enum.

### rentals
- **Purpose**: Primary booking record tying user to one or more vehicles.
- **Key Fields**:
  - `id` (uuid, PK)
  - `user_id` (uuid FK → users.id on delete cascade)
  - `status` (enum: `pending`, `active`, `completed`)
  - `start_date`, `end_date` (date)
  - `driver_requested` (boolean)
  - `whatsapp_message` (text)
  - `total_amount` (decimal(12,2))
  - `payment_confirmed_at` (timestamp nullable)
  - `completed_at` (timestamp nullable)
  - `created_at`, `updated_at`
- **Relationships**: `belongsTo` user; `hasMany` rental_items; `hasOne` driver assignment (optional).
- **Validation**:
  - `end_date` ≥ `start_date`.
  - Status transitions follow allowed graph (see Lifecycle).

### rental_items
- **Purpose**: Line items linking rentals to specific vehicles and quantities.
- **Key Fields**:
  - `id` (uuid, PK)
  - `rental_id` (uuid FK → rentals.id on delete cascade)
  - `vehicle_id` (uuid FK → vehicles.id on restrict delete)
  - `quantity` (integer ≥1)
  - `rate_snapshot` (decimal(10,2))
  - `created_at`, `updated_at`
- **Relationships**: `belongsTo` rental; `belongsTo` vehicle.
- **Validation**: Quantity ≥1; vehicle must be active at booking time.

### drivers
- **Purpose**: Optional pool of drivers assignable to rentals.
- **Key Fields**:
  - `id` (uuid, PK)
  - `name` (string)
  - `phone_number` (string unique, E.164)
  - `license_number` (string unique)
  - `is_active` (boolean)
  - `notes` (text nullable)
  - `created_at`, `updated_at`
- **Relationships**: `hasMany` rental_driver_assignments (see below).

### rental_driver_assignments
- **Purpose**: Join table linking drivers to rentals when requested.
- **Key Fields**:
  - `id` (uuid, PK)
  - `rental_id` (uuid FK → rentals.id on delete cascade)
  - `driver_id` (uuid FK → drivers.id on restrict delete)
  - `assigned_at` (timestamp)
  - `released_at` (timestamp nullable)
- **Validation**: Ensure driver is active; rental has `driver_requested = true`.

### availability
- **Purpose**: Tracks blocked or available ranges for vehicles.
- **Key Fields**:
  - `id` (uuid, PK)
  - `vehicle_id` (uuid FK → vehicles.id on delete cascade)
  - `start_date`, `end_date` (date)
  - `status` (enum: `available`, `unavailable`)
  - `reason` (enum: `rental`, `maintenance`, `manual_block`)
  - `rental_id` (uuid FK nullable → rentals.id on delete set null)
  - `created_at`, `updated_at`
- **Validation**:
  - Date ranges must not overlap for the same vehicle with identical status.
  - When status=`unavailable` AND reason=`rental`, `rental_id` required.

## State Machines

### Rental Status Transitions
- **Initial**: `pending`
- **Transitions**:
  - `pending` → `active` (triggered by admin payment confirmation OR scheduler when payment timestamp present)
  - `active` → `completed` (scheduler on `end_date` or admin manual completion)
  - Backwards transitions disallowed; cancellation handled via future extension (not in MVP)
- **Guards**:
  - Transition to `active` requires `payment_confirmed_at` set.
  - Transition to `completed` sets `completed_at` and frees associated availability slots.

## Derived Views & Metrics
- **DashboardMetric** (virtual): aggregated counts
  - Pending rentals per user
  - Active rentals per user
  - Fleet availability summary (active rentals, available vehicles, upcoming returns)
  - Backed by SQL views or query scopes

## Data Integrity Rules
- Cascade delete rentals when a user is deleted (MVP assumption).
- Restrict deleting vehicles that have future availability blocks or active rentals.
- Enforce unique constraint on `(vehicle_id, start_date, end_date, status)` to prevent duplicate availability entries.
- Ensure OTP attempts reset when `otp_locked_until` is reached or OTP verified.
