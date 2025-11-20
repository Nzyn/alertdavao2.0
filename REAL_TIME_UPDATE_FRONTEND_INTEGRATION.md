# Real-Time Report Updates - Frontend Integration Guide

## Files Modified

### 1. AdminSide/admin/resources/views/welcome.blade.php (Dashboard)
Add WebSocket client to auto-refresh map and statistics when new reports arrive.

**Location to add code:** After the scripts section (before closing body tag)

```html
<!-- WebSocket Status Indicator -->
<div id="ws-status" class="ws-status" style="position: fixed; bottom: 20px; right: 20px; padding: 10px 15px; border-radius: 20px; background: #ddd; z-index: 9999; font-size: 12px; font-weight: bold;">
  🔌 Connecting...
</div>

<style>
  .ws-status {
    transition: all 0.3s ease;
  }
  .ws-connected {
    background: #4CAF50;
    color: white;
  }
  .ws-disconnected {
    background: #f44336;
    color: white;
  }
</style>

<!-- Include WebSocket Client -->
<script src="{{ asset('js/websocket-client.js') }}"></script>

<script>
// Initialize WebSocket client
const stationId = {{ Auth::user()->police_station_id ?? 'null' }};
const userId = {{ Auth::id() }};
const userRole = "{{ Auth::user()->role }}";

if (stationId) {
  const wsClient = new ReportWebSocketClient(stationId, userId, userRole);
  
  // Listen for new reports
  wsClient.on('new_report', function(report) {
    console.log('🔔 Updating dashboard with new report:', report);
    
    // Update map with new report
    if (typeof loadMiniMapReports === 'function') {
      loadMiniMapReports();
    }
    
    // Update statistics
    if (typeof loadDashboardStats === 'function') {
      loadDashboardStats();
    }
    
    // Highlight the new report on the map
    setTimeout(() => {
      highlightReportOnMap(report.report_id);
    }, 1000);
  });
  
  // Listen for report updates
  wsClient.on('report_updated', function(update) {
    console.log('📝 Report updated:', update);
    
    // Refresh map to show updated status
    if (typeof loadMiniMapReports === 'function') {
      loadMiniMapReports();
    }
  });
  
  // Listen for connection status
  wsClient.on('connected', function() {
    console.log('✅ WebSocket connected, ready for real-time updates');
  });
  
  // Listen for max reconnect failed
  wsClient.on('max_reconnect_failed', function() {
    console.warn('⚠️  WebSocket connection failed, falling back to polling');
    // Implement polling fallback here (every 10 seconds)
    setInterval(() => {
      if (typeof loadMiniMapReports === 'function') {
        loadMiniMapReports();
      }
    }, 10000);
  });
  
  // Connect to WebSocket
  wsClient.connect();
  
  // Cleanup on page unload
  window.addEventListener('beforeunload', function() {
    wsClient.disconnect();
  });
} else {
  console.log('⚠️  Not a police officer, WebSocket not initialized');
}

// Helper function to highlight new report on map
function highlightReportOnMap(reportId) {
  // Find the marker for this report on the map
  const marker = window.reportMarkers && window.reportMarkers.find(m => m.reportId === reportId);
  if (marker) {
    // Bounce animation
    marker.setIcon(L.icon({
      iconUrl: '/images/marker-alert.png',
      iconSize: [32, 32],
      className: 'new-report-marker'
    }));
    
    // After 5 seconds, revert to normal
    setTimeout(() => {
      marker.setIcon(L.icon({
        iconUrl: '/images/marker-default.png',
        iconSize: [32, 32]
      }));
    }, 5000);
  }
}

// Helper function to load dashboard stats
function loadDashboardStats() {
  // Reload the stats section
  fetch('{{ route("dashboard") }}')
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const statsElement = doc.querySelector('[data-stats]');
      if (statsElement && document.querySelector('[data-stats]')) {
        document.querySelector('[data-stats]').innerHTML = statsElement.innerHTML;
      }
    })
    .catch(error => console.error('Error loading stats:', error));
}
</script>
```

### 2. AdminSide/admin/resources/views/reports.blade.php (Reports List)
Add WebSocket client to auto-refresh the reports list and highlight new reports.

**Location to add code:** After the scripts section (before closing body tag)

```html
<!-- WebSocket Status Indicator -->
<div id="ws-status" class="ws-status" style="position: fixed; bottom: 20px; right: 20px; padding: 10px 15px; border-radius: 20px; background: #ddd; z-index: 9999; font-size: 12px; font-weight: bold;">
  🔌 Connecting...
</div>

<style>
  .ws-status {
    transition: all 0.3s ease;
  }
  .ws-connected {
    background: #4CAF50;
    color: white;
  }
  .ws-disconnected {
    background: #f44336;
    color: white;
  }
  
  .new-report-row {
    animation: highlightRow 1s ease-out;
  }
  
  @keyframes highlightRow {
    0% {
      background-color: #fff3cd;
      transform: scale(1.02);
    }
    100% {
      background-color: transparent;
      transform: scale(1);
    }
  }
</style>

<!-- Include WebSocket Client -->
<script src="{{ asset('js/websocket-client.js') }}"></script>

<script>
// Initialize WebSocket client
const stationId = {{ Auth::user()->police_station_id ?? 'null' }};
const userId = {{ Auth::id() }};
const userRole = "{{ Auth::user()->role }}";

if (stationId) {
  const wsClient = new ReportWebSocketClient(stationId, userId, userRole);
  
  // Listen for new reports
  wsClient.on('new_report', function(report) {
    console.log('🔔 New report received:', report);
    
    // Reload reports list
    reloadReportsList();
    
    // Show success toast
    if (typeof showToast === 'function') {
      showToast('New Report', `Report #${report.report_id} - ${report.title}`, 'success');
    }
  });
  
  // Listen for report updates
  wsClient.on('report_updated', function(update) {
    console.log('📝 Report status updated:', update);
    
    // Find and update the specific report row
    updateReportRow(update.report_id, update.status);
  });
  
  // Connect to WebSocket
  wsClient.connect();
  
  // Cleanup on page unload
  window.addEventListener('beforeunload', function() {
    wsClient.disconnect();
  });
}

// Function to reload reports list via AJAX
function reloadReportsList() {
  fetch('{{ route("reports.index") }}')
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newTableBody = doc.querySelector('tbody');
      const currentTableBody = document.querySelector('tbody');
      
      if (newTableBody && currentTableBody) {
        // Highlight new rows
        newTableBody.querySelectorAll('tr').forEach(row => {
          row.classList.add('new-report-row');
        });
        
        currentTableBody.innerHTML = newTableBody.innerHTML;
        
        // Re-attach event listeners if needed
        attachRowEventListeners();
      }
    })
    .catch(error => console.error('Error reloading reports:', error));
}

// Function to update a specific report row
function updateReportRow(reportId, newStatus) {
  const row = document.querySelector(`tr[data-report-id="${reportId}"]`);
  if (row) {
    const statusCell = row.querySelector('[data-status]');
    if (statusCell) {
      statusCell.textContent = newStatus;
      statusCell.className = `status-badge status-${newStatus}`;
    }
  }
}

// Re-attach event listeners to table rows
function attachRowEventListeners() {
  // Reattach any click handlers or other event listeners
  // that were on the table rows
  document.querySelectorAll('tbody tr').forEach(row => {
    row.addEventListener('click', function() {
      // Handle row click (e.g., open modal)
    });
  });
}
</script>
```

### 3. Create CSS File for WebSocket Status
Create `AdminSide/admin/resources/css/websocket.css`

```css
/* WebSocket Connection Status */
.ws-status {
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.ws-status.ws-connected {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  color: white;
  animation: pulse 2s infinite;
}

.ws-status.ws-disconnected {
  background: linear-gradient(135deg, #f44336 0%, #da190b 100%);
  color: white;
}

@keyframes pulse {
  0%, 100% {
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
  }
  50% {
    box-shadow: 0 2px 12px rgba(76, 175, 80, 0.6);
  }
}

/* New Report Highlighting */
.new-report-row {
  animation: highlightRow 1s ease-out;
}

@keyframes highlightRow {
  0% {
    background-color: #fff3cd;
    transform: scale(1.02);
  }
  100% {
    background-color: transparent;
    transform: scale(1);
  }
}

/* Alert Icon */
.new-report-indicator {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #ff9800;
  margin-right: 5px;
  animation: blink 1s infinite;
}

@keyframes blink {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
```

## How to Include CSS in Blade Template

Add this to your Blade template's `<head>` section:

```html
<link rel="stylesheet" href="{{ asset('css/websocket.css') }}">
```

## Testing Real-Time Updates

1. **In one browser tab:** Open the police dashboard/reports list
2. **In another browser tab:** Submit a new report through the user app
3. **Expected behavior:** 
   - The new report appears in the police dashboard within 1-2 seconds
   - No manual refresh needed
   - A notification shows up
   - The new report row is highlighted briefly

## Troubleshooting

### Connection shows as "Disconnected"
- Check if backend server is running on port 3000
- Check browser console for errors
- Verify firewall allows WebSocket connections
- Check CORS settings

### New reports not appearing
- Open browser DevTools console
- Look for WebSocket messages
- Verify `stationId` is being passed correctly
- Check if report is assigned to the correct station

### High memory usage
- WebSocket clients should use < 5KB per connection
- If memory is high, close old browser tabs
- Check for reconnection loops in console

## Fallback Polling (if WebSocket fails)
The frontend automatically falls back to polling every 10 seconds if:
- WebSocket connection fails after 5 reconnect attempts
- Network is unstable
- Server doesn't support WebSocket

## Security Notes

1. Station-based filtering is handled server-side
2. Only officers assigned to a station receive its reports
3. WebSocket token-based auth can be added for production
4. Rate limiting prevents flooding
