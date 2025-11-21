# AdminSide Database Migration Guide

## Overview
This guide walks you through migrating the AdminSide Laravel database to match the new AlertDavao 2.0 schema.

---

## New Migrations Created

### 1. `2025_10_17_000001_create_police_stations_table.php`
Creates the `police_stations` table with:
- station_id (PK)
- station_name
- address
- latitude, longitude
- contact_number
- timestamps

### 2. `2025_10_17_000002_create_police_officers_table.php`
Creates the `police_officers` table with:
- officer_id (PK)
- user_id (FK to users, unique)
- station_id (FK to police_stations)
- assigned_since (date)
- rank (nullable)
- status (active/on_leave/retired)
- timestamps

### 3. `2025_10_17_000003_update_users_table_add_station_id.php`
Adds to `users` table:
- station_id (FK to police_stations, nullable)

### 4. `2025_10_17_000004_update_reports_table_add_assigned_station.php`
Adds to `reports` table:
- assigned_station_id (FK to police_stations, nullable)

### 5. `2025_10_17_000005_update_verifications_table_add_documents.php`
Adds to `verifications` table:
- id_selfie (varchar)
- billing_document (varchar)

---

## Seeder Created

### `PoliceStationsSeeder.php`
Seeds 19 police stations across Davao City:
- PS1 Sta. Ana
- PS2 San Pedro
- PS3 Talomo
- PS4 Sasa
- PS5 Buhangin
- PS6 Bunawan
- PS7 Paquibato
- PS8 Toril
- PS9 Tugbok
- PS10 Calinan
- PS11 Baguio
- PS12 Marilog
- PS13 Mandug
- PS15 Ecoland
- PS16 Maa
- PS17 Baliok
- PS18 Bajada
- PS19 Eden (no address/coordinates yet)
- PS20 Los Amigos

Each station includes:
- Full address
- GPS coordinates (latitude/longitude)
- Contact numbers

---

## Migration Steps

### Step 1: Navigate to AdminSide
```powershell
cd AdminSide/admin
```

### Step 2: Check Current Migration Status
```powershell
php artisan migrate:status
```

### Step 3: Run New Migrations
```powershell
php artisan migrate
```

This will execute all pending migrations in order:
1. Create police_stations table
2. Create police_officers table
3. Add station_id to users table
4. Add assigned_station_id to reports table
5. Add verification documents to verifications table

### Step 4: Seed Police Stations Data
```powershell
php artisan db:seed --class=PoliceStationsSeeder
```

Or seed everything (including roles):
```powershell
php artisan db:seed
```

---

## Verification Commands

### Check Tables Created
```powershell
php artisan tinker
```
Then run:
```php
DB::table('police_stations')->count();
// Should return: 19

DB::table('police_stations')->select('station_name')->get();
// Should list all 19 stations
```

### Verify Foreign Keys
```sql
-- Run in MySQL console
USE alertdavao;

SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    information_schema.KEY_COLUMN_USAGE
WHERE
    TABLE_SCHEMA = 'alertdavao'
    AND REFERENCED_TABLE_NAME = 'police_stations';
```

Expected results:
- `users.station_id` → `police_stations.station_id`
- `reports.assigned_station_id` → `police_stations.station_id`
- `police_officers.station_id` → `police_stations.station_id`

---

## Rollback (If Needed)

### Rollback Last Migration Batch
```powershell
php artisan migrate:rollback
```

### Rollback Specific Migration
```powershell
php artisan migrate:rollback --step=1
```

### Rollback All Migrations
```powershell
php artisan migrate:reset
```

---

## Database Schema Overview

### Updated Tables

#### `users`
```
- id (existing)
- firstname (existing)
- lastname (existing)
- contact (existing)
- email (existing)
- password (existing)
- address (existing)
- latitude (existing)
- longitude (existing)
- station_id (NEW) ← FK to police_stations
- is_verified (existing)
- created_at (existing)
- updated_at (existing)
```

#### `reports`
```
- report_id (existing)
- user_id (existing)
- location_id (existing)
- assigned_station_id (NEW) ← FK to police_stations
- title (existing)
- report_type (existing)
- description (existing)
- date_reported (existing)
- status (existing)
- is_anonymous (existing)
- created_at (existing)
- updated_at (existing)
```

#### `verifications`
```
- verification_id (existing)
- user_id (existing)
- otp_code (existing)
- expiration (existing)
- status (existing)
- id_selfie (NEW)
- billing_document (NEW)
- created_at (existing)
- updated_at (existing)
```

### New Tables

#### `police_stations`
```
- station_id (PK)
- station_name
- address
- latitude
- longitude
- contact_number
- created_at
- updated_at
```

#### `police_officers`
```
- officer_id (PK)
- user_id (FK to users, unique)
- station_id (FK to police_stations)
- assigned_since
- rank
- status
- created_at
- updated_at
```

---

## Testing

### Test Police Stations API
After migration, test the endpoint:
```
GET http://localhost:8000/api/police-stations
```

### Sample Eloquent Queries
```php
// Get all police stations
$stations = DB::table('police_stations')->get();

// Get stations with coordinates
$stationsWithCoords = DB::table('police_stations')
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->get();

// Get user's assigned station
$user = DB::table('users')->find(1);
if ($user->station_id) {
    $station = DB::table('police_stations')
        ->where('station_id', $user->station_id)
        ->first();
}

// Get reports assigned to a station
$reports = DB::table('reports')
    ->where('assigned_station_id', 1)
    ->get();
```

---

## Common Issues & Solutions

### Issue: Migration fails with "Table already exists"
**Solution:** The migration checks if columns exist before adding. If table exists, run:
```powershell
php artisan migrate:fresh --seed
```
⚠️ WARNING: This drops all tables and recreates them. Use only in development!

### Issue: Foreign key constraint fails
**Solution:** Ensure parent tables exist first:
1. `police_stations` must exist before running other migrations
2. Check migration timestamps - they run in order

### Issue: Seeder fails with duplicate entry
**Solution:** Truncate the table first:
```php
DB::table('police_stations')->truncate();
```
Then run seeder again.

---

## Next Steps

After successful migration:

1. ✅ Verify all tables exist
2. ✅ Verify foreign keys are set
3. ✅ Verify 19 police stations are seeded
4. ✅ Test API endpoints
5. ⏳ Create Laravel models for new tables
6. ⏳ Build admin controllers for police management
7. ⏳ Create views for station management
8. ⏳ Implement station assignment logic

---

## Files Created

### Migrations
- `AdminSide/admin/database/migrations/2025_10_17_000001_create_police_stations_table.php`
- `AdminSide/admin/database/migrations/2025_10_17_000002_create_police_officers_table.php`
- `AdminSide/admin/database/migrations/2025_10_17_000003_update_users_table_add_station_id.php`
- `AdminSide/admin/database/migrations/2025_10_17_000004_update_reports_table_add_assigned_station.php`
- `AdminSide/admin/database/migrations/2025_10_17_000005_update_verifications_table_add_documents.php`

### Seeders
- `AdminSide/admin/database/seeders/PoliceStationsSeeder.php`

### Updated
- `AdminSide/admin/database/seeders/DatabaseSeeder.php`

---

## Status: Ready to Migrate ✅

Run these commands in order:
```powershell
cd AdminSide/admin
php artisan migrate
php artisan db:seed --class=PoliceStationsSeeder
```

**Estimated time:** 2-3 minutes
**Risk level:** Low (all migrations check for existing columns)
