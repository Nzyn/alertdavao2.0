-- ============================================================
-- Fix: Assign existing reports to their correct stations
-- ============================================================
-- This script assigns all unassigned reports to their proper
-- police stations based on their location's barangay mapping

-- Step 1: Show reports that will be updated
SELECT 
    r.report_id, 
    r.title, 
    l.barangay, 
    l.station_id,
    CASE 
        WHEN r.assigned_station_id IS NULL THEN 'NULL'
        WHEN r.assigned_station_id = 0 THEN 'ZERO'
        ELSE CAST(r.assigned_station_id AS CHAR)
    END AS current_assignment
FROM reports r
LEFT JOIN locations l ON r.location_id = l.location_id
WHERE r.assigned_station_id IS NULL 
   OR r.assigned_station_id = 0
ORDER BY r.created_at DESC
LIMIT 20;

-- Step 2: Update reports with location-based station assignment
UPDATE reports r
JOIN locations l ON r.location_id = l.location_id
SET r.assigned_station_id = l.station_id
WHERE l.station_id IS NOT NULL
  AND (r.assigned_station_id IS NULL OR r.assigned_station_id = 0);

-- Step 3: Count reports still unassigned
SELECT COUNT(*) as unassigned_reports
FROM reports
WHERE assigned_station_id IS NULL OR assigned_station_id = 0;

-- Step 4: Show remaining unassigned reports (if any)
SELECT 
    r.report_id,
    r.title,
    r.location_id,
    l.barangay,
    l.station_id
FROM reports r
LEFT JOIN locations l ON r.location_id = l.location_id
WHERE r.assigned_station_id IS NULL 
   OR r.assigned_station_id = 0
ORDER BY r.created_at DESC;

-- Step 5: Show summary by station
SELECT 
    COALESCE(r.assigned_station_id, 0) as station_id,
    COALESCE(ps.station_name, 'Unassigned') as station_name,
    COUNT(r.report_id) as report_count
FROM reports r
LEFT JOIN police_stations ps ON r.assigned_station_id = ps.station_id
GROUP BY r.assigned_station_id
ORDER BY station_id;
