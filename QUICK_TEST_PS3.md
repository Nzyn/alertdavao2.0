# Quick Test: PS3 Real-Time Updates

## 5-Minute Test Plan

### Prerequisites
- Backend running: `npm start` (in UserSide/backends)
- Admin panel running: `php artisan serve` (in AdminSide/admin)
- Database seeded with PS3 and officers

### Test Flow

```
START
  ↓
[1] Open MySQL/Database client
  ↓
  SELECT * FROM police_officers WHERE station_id = 3;
  → Should see at least 1 officer
  ↓
[2] Open admin panel in browser
  ↓
  http://localhost:8000
  → Log in as PS3 officer
  ↓
[3] Navigate to Reports page
  ↓
[4] Open browser console (F12)
  ↓
  Look for: "✓ Police officer assigned to station: 3"
  Look for: "✅ WebSocket connected successfully"
  ↓
[5] Submit test report from UserSide app
  ↓
  Location: Must be in PS3 barangay area
  ↓
[6] Watch Reports page
  ↓
  New report appears within 1-2 seconds?
  YES ✓ → FIX WORKS
  NO ✗ → Debug below
  ↓
END
```

## Expected Console Output

```
✓ Police officer assigned to station: 3
🔌 Connecting to WebSocket: ws://localhost:3000/ws?stationId=3&userId=5&role=police
✅ WebSocket connected successfully
   ClientID: 42
   Total clients: 1
```

## If Test Fails

### No console messages at all?
```
→ Check if reports page loads
→ Check if police officer is logged in
→ Press F12 then refresh page
```

### Shows "No station assigned"?
```
→ Officer is not in police_officers table
→ OR officer's station_id is NULL

Fix:
INSERT INTO police_officers (user_id, station_id, rank, status, assigned_since)
VALUES (5, 3, 'Officer', 'active', NOW());
```

### Shows "stationId=null" in WebSocket URL?
```
→ ReportController fix might not be applied
→ OR database query is not finding the officer

Check:
- ReportController.php line 98 has PoliceOfficer::where()
- police_officers table has correct data
- User's role is exactly 'police' (case-sensitive)
```

### WebSocket connects but no report appears?
```
→ Report might not be assigned to PS3

Check:
1. What barangay was the report location?
2. Is that barangay mapped to PS3 in locations table?

SELECT barangay, station_id FROM locations 
WHERE barangay LIKE '%Barangay%';
```

### Report appears later but not instantly?
```
→ WebSocket is working but there's delay
→ Check browser console for errors
→ Check backend console for broadcast logs

Look for in backend console:
📢 Broadcasting new report to station 3
✅ Sent to client 42
```

## Debug Commands

### Check PS3 exists
```sql
SELECT * FROM police_stations WHERE station_id = 3;
```

### Check PS3 officers
```sql
SELECT po.officer_id, po.user_id, u.firstname, u.lastname, u.role
FROM police_officers po
JOIN users u ON po.user_id = u.id
WHERE po.station_id = 3;
```

### Check PS3 barangays
```sql
SELECT DISTINCT barangay FROM locations WHERE station_id = 3;
```

### Check recent reports for PS3
```sql
SELECT r.report_id, r.title, r.assigned_station_id, l.barangay
FROM reports r
JOIN locations l ON r.location_id = l.location_id
WHERE r.assigned_station_id = 3
ORDER BY r.created_at DESC
LIMIT 5;
```

## Success Indicators

✓ Officer sees only PS3 reports (not all reports)
✓ New report appears within 2 seconds
✓ Console shows station 3 in WebSocket URL
✓ Backend logs show "Broadcasting new report to station 3"
✓ Report doesn't appear if submitted in different station's area

## What to Share If Still Broken

1. Browser console output (right-click → Inspect → Console tab)
2. Backend console output when report is submitted
3. Database query results for police_officers
4. Database query results for locations (barangay → station mapping)
5. Network tab showing fetch/WebSocket calls

---

**Time Expected:** 5 minutes
**Success Rate:** Should be 100% if all fixes applied correctly
