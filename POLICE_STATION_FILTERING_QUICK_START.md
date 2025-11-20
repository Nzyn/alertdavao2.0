# Police Station Report Filtering - Quick Start

## What Changed?

Reports are now automatically assigned to police stations based on the barangay where they're reported from. Police officers only see reports for their assigned station.

## How It Works

### 1. Report Creation
When a user submits a report:
- System determines which barangay it's in based on location coordinates
- Report is automatically assigned to that barangay's police station
- Example: Report from Talomo → Auto-assigned to PS3 Talomo

### 2. Report Viewing
When police officers log in:
- They only see reports assigned to their station
- Admins see all reports (unchanged)
- Example: Officer at Station 1 (Sta. Ana) sees reports from Poblacion District & Agdao only

### 3. Dashboard Metrics
Report counts are filtered by station for police officers:
- **Total Reports**: Shows only their station's reports
- **Pending Reports**: Only their station
- **Investigating Reports**: Only their station
- **Resolved Reports**: Only their station

## Barangay to Police Station Mappings

| Barangay | Police Station |
|----------|---|
| Poblacion District | PS1 Sta. Ana |
| Talomo | PS3 Talomo |
| Buhangin | PS5 Buhangin |
| Paquibato | PS7 Paquibato |
| Toril | PS8 Toril |
| Tugbok | PS9 Tugbok |
| Baguio | PS11 Baguio |
| Agdao | PS1 Sta. Ana |
| Matina | PS3 Talomo |
| Lanang | PS5 Buhangin |

## For Administrators

### Assigning Stations to Police Officers

1. Go to **Users** page
2. Find a police officer
3. Click the **Assign to Police Station** button
4. Select their assigned station from the dropdown
5. Click **Assign Station**

The officer will now only see reports from that station.

### Assigning Reports to New Barangays

If you need to add more barangay-to-station mappings:

1. Edit `MapBarangaysToStations` command
2. Add entry to `$barangayStationMap` array:
   ```php
   'Barangay Name' => 5, // Station ID
   ```
3. Run: `php artisan app:map-barangays-to-stations`

## For Police Officers

### Viewing Your Reports

1. Log in with your police officer account
2. Go to **Reports** page
3. You'll see only reports for your assigned station
4. Dashboard automatically shows your station's metrics

### Viewing Report Details

Click **View Details** on any report to see:
- Full location with barangay name
- Address where incident was reported
- Exact coordinates (latitude, longitude)
- All attached media

## Commands Available

### Assign All Existing Reports
```bash
php artisan app:assign-existing-reports
```
Automatically assigns all unassigned reports to their stations.

### Map Barangays to Stations
```bash
php artisan app:map-barangays-to-stations
```
Creates the barangay-to-station mappings.

## Testing

### Check if report filtering works:
- Login as police officer
- Verify you see only your station's reports
- Check dashboard counts match your station's reports

### Check if new reports get assigned:
- Admin creates/submits a report for a barangay
- Check that `assigned_station_id` matches that barangay's station

## Troubleshooting

**Police officer sees no reports?**
- Ensure officer is assigned to a station (check Users → Assign to Police Station)
- Ensure reports exist with valid locations for that station
- Run: `php artisan app:assign-existing-reports` to assign existing reports

**Report shows wrong station?**
- Check barangay name in location table
- Verify mapping in `MapBarangaysToStations` command
- Ensure location has valid coordinates (latitude & longitude not null)

**Location not showing barangay in details?**
- Check that location has a barangay name (not just coordinates)
- Example: Should be "Talomo" not "Lat: 7.xxx, Lng: 125.xxx"

## Files Modified

- `app/Models/Report.php` - Added station relationship
- `app/Models/Location.php` - Added station relationship
- `app/Http/Controllers/ReportController.php` - Filtering logic
- `app/Http/Controllers/DashboardController.php` - Metrics filtering
- `resources/views/reports.blade.php` - Enhanced location display
- `database/migrations/2025_11_21_042213_add_station_id_to_locations_table.php` - Database schema
- `app/Console/Commands/MapBarangaysToStations.php` - Barangay mapping
- `app/Console/Commands/AssignExistingReports.php` - Bulk report assignment
