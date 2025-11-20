# Report Rerouting Implementation Summary

## Overview
Implemented automatic report rerouting based on barangay locations to their assigned police stations. Police officers now only see reports for their assigned station, and the dashboard counts are filtered accordingly.

## Changes Made

### 1. Database Schema Updates
- **Migration**: `2025_11_21_042213_add_station_id_to_locations_table.php`
  - Added `station_id` column to `locations` table
  - Created foreign key relationship to `police_stations` table
  - Allows mapping of barangays to their respective police stations

### 2. Model Updates

#### Location Model (`app/Models/Location.php`)
- Added `station_id` to fillable attributes
- Added `station()` relationship to PoliceStation model
- Location now has a direct relationship to its assigned police station

#### Report Model (`app/Models/Report.php`)
- Added `station()` relationship to access the assigned police station
- Reports can now easily access their assigned station through the location's station mapping

### 3. Controller Updates

#### ReportController (`app/Http/Controllers/ReportController.php`)
**assignReportToStation() Method**
- Updated to use barangay-to-station mapping via `location.station_id`
- Fallback logic: If location doesn't have station_id, match by barangay name
- Auto-assigns reports when created based on the location's barangay

**index() Method (Report Listing)**
- Police officers: ONLY see reports assigned to their station
- Police without assigned station: See no reports
- Admin users: See all reports (unchanged)
- All reports must have valid coordinates to appear

#### DashboardController (`app/Http/Controllers/DashboardController.php`)
- Report counts are now filtered based on user role
- Police officers see only their station's report counts
- Dashboard shows:
  - `totalReports`: Count for user's station (if police) or all (if admin)
  - `investigatingReports`: Filtered by user's station (if police)
  - `pendingReports`: Filtered by user's station (if police)
  - `resolvedReports`: Filtered by user's station (if police)
  - `totalUsers` & `totalPoliceOfficers`: Global counts (unchanged)

#### BarangayController (`app/Http/Controllers/BarangayController.php`)
- Added `getPoliceStations()` method
- Returns all police stations with their details
- Used by admin interface for station assignment

### 4. Seeding & Mapping

#### MapBarangaysToStations Command (`app/Console/Commands/MapBarangaysToStations.php`)
- Console command to map barangays to police stations
- Handles barangay names that exist in the database:
  - Poblacion District → PS1 Sta. Ana (Station ID: 1)
  - Talomo → PS3 Talomo (Station ID: 3)
  - Buhangin → PS5 Buhangin (Station ID: 5)
  - Paquibato → PS7 Paquibato (Station ID: 7)
  - Toril → PS8 Toril (Station ID: 8)
  - Tugbok → PS9 Tugbok (Station ID: 9)
  - Baguio → PS11 Baguio (Station ID: 11)
  - Agdao → PS1 Sta. Ana (Station ID: 1)
  - Matina → PS3 Talomo (Station ID: 3)
  - Lanang → PS5 Buhangin (Station ID: 5)

**Run with:**
```bash
php artisan app:map-barangays-to-stations
```

### 5. Frontend Updates

#### Reports View (`resources/views/reports.blade.php`)
**getLocationDisplay() Function**
- Updated to show detailed location information:
  - Barangay name (if valid)
  - Address from reporters_address field
  - Latitude and Longitude coordinates
- Example output: "Talomo - Sample Street Address (7.0554, 125.5463)"

## How It Works

### Report Creation Flow
1. User submits a report with location and coordinates
2. Report is created in database with `location_id` and automatic timestamp
3. `assignReportToStation()` is called:
   - Checks if location has `station_id` assigned → Use it
   - If not, find other locations with same barangay that have `station_id`
   - Update both report's `assigned_station_id` and location's `station_id`
4. Report is now associated with the correct police station

### Report Viewing Flow
1. Police officer logs in and views reports dashboard
2. Dashboard queries reports WHERE `assigned_station_id` = police_officer's `station_id`
3. Report list only shows reports for their assigned station
4. Report details display full location info including barangay and address

## Verification Steps

1. **Check database mapping:**
   ```bash
   php artisan tinker --execute "echo \App\Models\Location::select('location_id', 'barangay', 'station_id')->get();"
   ```

2. **Verify report assignments:**
   ```bash
   php artisan tinker --execute "echo \App\Models\Report::select('report_id', 'assigned_station_id', 'location_id')->get();"
   ```

3. **Test police officer filtering:**
   - Login as police officer assigned to specific station
   - Verify only that station's reports appear
   - Check dashboard counts are correct for that station only

4. **Test admin view:**
   - Login as admin
   - Verify all reports appear (with valid locations)

## Key Features

✅ Automatic report-to-station assignment based on barangay location
✅ Police officers see only their station's reports
✅ Dashboard metrics are role-aware and station-filtered
✅ Location details include barangay, address, and coordinates
✅ Fallback logic for unmapped locations (by barangay name matching)
✅ Admin users see all reports regardless of station
✅ Foreign key constraints maintain data integrity

## API Endpoints

- `GET /api/police-stations` - List all police stations (Admin route)
- Route protected by auth middleware
- Used by admin interface for police station assignment

## Notes

- Police officers without assigned `station_id` will see no reports
- Reports without valid coordinates are excluded from all queries
- Barangay-to-station mapping can be extended in `MapBarangaysToStations` command
- Use polygon coordinates for future enhanced geofencing if needed
