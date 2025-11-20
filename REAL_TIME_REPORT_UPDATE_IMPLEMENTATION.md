# Real-Time Report Updates Implementation Guide

## Overview
This implementation adds WebSocket-based real-time report updates so that when a user submits a report, it automatically appears in the police dashboard for assigned officers without needing to refresh.

## Architecture

### 1. WebSocket Server Setup (Node.js Backend)
- Use `ws` library (already installed in UserSide)
- Create WebSocket server alongside Express
- Maintain active client connections per police station
- Broadcast new reports to assigned officers

### 2. Update Flow
1. User submits report → Node.js API (submitReport)
2. Report saved to DB with assigned station_id
3. Node.js emits WebSocket message to police officers in that station
4. Police dashboard receives update → triggers alert/refresh
5. New report appears in real-time in the admin dashboard

### 3. Components to Create/Modify

#### Backend Files:
- `UserSide/backends/server.js` - Add WebSocket server initialization
- `UserSide/backends/handleReport.js` - Emit WebSocket event on report creation
- `UserSide/backends/handleWebSocket.js` - NEW: WebSocket connection management
- `UserSide/backends/handleNewFeatures.js` - Add broadcast function

#### Frontend Files:
- `AdminSide/admin/resources/views/welcome.blade.php` - Add WebSocket client
- `AdminSide/admin/resources/views/reports.blade.php` - Add real-time report list updates

## Implementation Steps

### Step 1: Install Dependencies (if needed)
```bash
cd alertdavao2.0/UserSide/backends
npm install ws
```

### Step 2: Create WebSocket Handler Module
Create `UserSide/backends/handleWebSocket.js` to manage:
- Client connections
- Connection per station
- Broadcasting messages
- Client cleanup

### Step 3: Update Express Server
Modify `UserSide/backends/server.js` to:
- Initialize WebSocket server
- Export broadcast function
- Attach WebSocket to HTTP server

### Step 4: Update Report Handler
Modify `UserSide/backends/handleReport.js` to:
- Import broadcast function
- Emit "new_report" event after successful submission
- Include station_id in broadcast

### Step 5: Update Admin Dashboard Frontend
Modify `AdminSide/admin/resources/views/welcome.blade.php` to:
- Connect to WebSocket on page load
- Listen for "new_report" events
- Refresh report data when notified
- Show notification to user

### Step 6: Update Reports List View
Modify `AdminSide/admin/resources/views/reports.blade.php` to:
- Connect to WebSocket
- Listen for report updates
- Auto-scroll to new report
- Highlight new report briefly

## Deployment Checklist

- [ ] Install `ws` package in backends
- [ ] Create handleWebSocket.js
- [ ] Update server.js with WebSocket setup
- [ ] Update handleReport.js to emit events
- [ ] Update welcome.blade.php with WebSocket client
- [ ] Update reports.blade.php with WebSocket client
- [ ] Test with single report submission
- [ ] Test with multiple officers
- [ ] Verify station-based filtering
- [ ] Check connection stability
- [ ] Monitor memory usage during long connections
- [ ] Add error handling and reconnection logic

## Security Considerations

1. Only broadcast to officers assigned to the station
2. Verify officer's station before sending data
3. Use token-based authentication for WebSocket connections
4. Sanitize report data before broadcast
5. Rate limit broadcasts to prevent flooding
6. Close connections on logout

## Fallback Strategy

If WebSocket disconnects:
1. Auto-reconnect every 3 seconds
2. Fall back to polling interval (10 seconds)
3. Alert user of connection status
4. Resume WebSocket when available

## Performance Metrics

- Connection establishment: < 100ms
- Message delivery: < 50ms
- Memory per connection: ~5KB
- Max connections per server: ~10,000
