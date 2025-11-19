

<?php $__env->startSection('title', 'View Map'); ?>

<?php $__env->startSection('styles'); ?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

<style>
    .map-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .map-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .map-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }
    
    .map-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .control-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: white;
        color: #6b7280;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .control-btn:hover {
        background: #f9fafb;
        border-color: #3b82f6;
        color: #3b82f6;
    }
    
    .control-btn.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    
    .date-filters {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .filter-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .filter-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
        min-width: 120px;
    }
    
    .filter-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .filter-button {
        padding: 0.5rem 1rem;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s;
        align-self: flex-end;
    }
    
    .filter-button:hover {
        background: #2563eb;
    }
    
    .reset-button {
        padding: 0.5rem 1rem;
        background: #6b7280;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s;
        align-self: flex-end;
    }
    
    .reset-button:hover {
        background: #4b5563;
    }
    
    .layer-control {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .layer-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        color: #374151;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }
    
    .layer-btn:hover {
        background: #f3f4f6;
        border-color: #3b82f6;
    }
    
    .layer-btn.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    
    #map {
        height: 600px;
        width: 100%;
        z-index: 1;
    }
    
    .map-legend {
        padding: 1.5rem;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    
    .legend-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.75rem;
    }
    
    .legend-items {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .legend-marker {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
    }
    
    .legend-marker.high {
        background-color: #dc2626;
    }
    
    .legend-marker.medium {
        background-color: #f59e0b;
    }
    
    .legend-marker.low {
        background-color: #10b981;
    }
    
    .map-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .map-stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-left: 3px solid #e5e7eb;
    }
    
    .map-stat-card.high {
        border-left-color: #dc2626;
    }
    
    .map-stat-card.medium {
        border-left-color: #f59e0b;
    }
    
    .map-stat-card.low {
        border-left-color: #10b981;
    }
    
    .map-stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    
    .map-stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    /* Custom Leaflet Popup Styling */
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .leaflet-popup-content {
        margin: 1rem;
        font-size: 0.875rem;
    }
    
    .popup-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .popup-details {
        color: #6b7280;
        line-height: 1.5;
    }
    
    @media (max-width: 768px) {
        #map {
            height: 400px;
        }
        
        .map-controls {
            width: 100%;
        }
        
        .control-btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-header">
    <h1 class="content-title">View Map</h1>
</div>

<!-- Date Filters -->
<div class="date-filters">
    <div class="filter-group">
        <label class="filter-label">Year</label>
        <select id="filter-year" class="filter-input">
            <option value="">All Years</option>
            <option value="2025">2025</option>
            <option value="2024">2024</option>
            <option value="2023">2023</option>
        </select>
    </div>
    
    <div class="filter-group">
        <label class="filter-label">Month</label>
        <select id="filter-month" class="filter-input">
            <option value="">All Months</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </div>
    
    <div class="filter-group">
        <label class="filter-label">Date From</label>
        <input type="date" id="filter-date-from" class="filter-input">
    </div>
    
    <div class="filter-group">
        <label class="filter-label">Date To</label>
        <input type="date" id="filter-date-to" class="filter-input">
    </div>
    
    <div class="filter-group">
        <label class="filter-label">Status</label>
        <select id="filter-status" class="filter-input">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="investigating">Investigating</option>
            <option value="resolved">Resolved</option>
        </select>
    </div>
    
    <button onclick="applyFilters()" class="filter-button">Apply Filters</button>
    <button onclick="resetFilters()" class="reset-button">Reset</button>
</div>

<!-- Map Statistics -->
<div class="map-stats">
    <div class="map-stat-card high">
        <div class="map-stat-label">High Risk Incidents</div>
        <div class="map-stat-value" id="high-risk-count">0</div>
    </div>
    <div class="map-stat-card medium">
        <div class="map-stat-label">Medium Risk Incidents</div>
        <div class="map-stat-value" id="medium-risk-count">0</div>
    </div>
    <div class="map-stat-card low">
        <div class="map-stat-label">Low Risk Incidents</div>
        <div class="map-stat-value" id="low-risk-count">0</div>
    </div>
</div>

<!-- Map Container -->
<div class="map-container">
    <div class="map-header">
        <h2 class="map-title">Davao City - Incident Locations</h2>
        <div>
            <div class="layer-control">
                <button class="layer-btn active" id="map-view-btn" onclick="switchToMapView()">
                    <svg style="width: 14px; height: 14px; fill: currentColor; display: inline; margin-right: 4px; vertical-align: middle;" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    Map View
                </button>
                <button class="layer-btn" id="satellite-view-btn" onclick="switchToSatelliteView()">
                    <svg style="width: 14px; height: 14px; fill: currentColor; display: inline; margin-right: 4px; vertical-align: middle;" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                    Satellite
                </button>
            </div>
            <div class="map-controls">
            <button class="control-btn active" onclick="showAllMarkers()">
                <svg style="width: 16px; height: 16px; fill: currentColor;" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                </svg>
                All
            </button>
            <button class="control-btn" onclick="filterMarkers('high')">
                High Risk
            </button>
            <button class="control-btn" onclick="filterMarkers('medium')">
                Medium Risk
            </button>
            <button class="control-btn" onclick="filterMarkers('low')">
                Low Risk
            </button>
            </div>
        </div>
    </div>
    
    <div id="map"></div>
    
    <div class="map-legend">
        <div class="legend-title">Incident Risk Levels</div>
        <div class="legend-items">
            <div class="legend-item">
                <div class="legend-marker high"></div>
                <span>High Risk - Immediate attention required</span>
            </div>
            <div class="legend-item">
                <div class="legend-marker medium"></div>
                <span>Medium Risk - Monitoring needed</span>
            </div>
            <div class="legend-item">
                <div class="legend-marker low"></div>
                <span>Low Risk - Resolved or minor</span>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
    // Global variables
    let map;
    let markers = [];
    let allReports = [];
    let highRiskIcon, mediumRiskIcon, lowRiskIcon;
    let streetLayer, satelliteLayer;
    
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Add small delay to ensure map container is ready
        setTimeout(function() {
            // Initialize the map centered on Davao City
            map = L.map('map').setView([7.1907, 125.4553], 13);
    
    // Add Street Map layer (OpenStreetMap)
    streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);
    
    // Add Satellite layer (Esri World Imagery)
    satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 18,
    });
    
    // Define custom icons for different risk levels
    highRiskIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #dc2626; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    
    mediumRiskIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #f59e0b; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    
    lowRiskIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #10b981; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    
    // Load initial data
    loadReports();
        }, 100); // End setTimeout
    }); // End DOMContentLoaded
    
    // Function to load reports from API
    function loadReports(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        const url = '<?php echo e(route("api.reports")); ?>' + (params ? '?' + params : '');
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                allReports = data.reports;
                updateMapMarkers(data.reports);
                updateStatistics(data.stats);
            })
            .catch(error => {
                console.error('Error loading reports:', error);
            });
    }
    
    // Function to update map markers
    function updateMapMarkers(reports) {
        // Clear existing markers
        markers.forEach(marker => {
            map.removeLayer(marker);
        });
        markers = [];
        
        // Add new markers
        reports.forEach(report => {
            let icon;
            if (report.risk_level === 'high') icon = highRiskIcon;
            else if (report.risk_level === 'medium') icon = mediumRiskIcon;
            else icon = lowRiskIcon;
            
            const marker = L.marker([report.latitude, report.longitude], { icon: icon })
                .addTo(map)
                .bindPopup(`
                    <div class="popup-title">${report.title}</div>
                    <div class="popup-details">
                        <strong>Risk Level:</strong> ${report.risk_level.toUpperCase()}<br>
                        <strong>Location:</strong> ${report.location_name}<br>
                        <strong>Status:</strong> ${report.status}<br>
                        <strong>Date:</strong> ${new Date(report.date_reported).toLocaleDateString()}<br>
                        <strong>Reporter:</strong> ${report.reporter}<br>
                        <strong>Description:</strong> ${report.description}
                    </div>
                `);
            
            marker.riskLevel = report.risk_level;
            marker.reportData = report;
            markers.push(marker);
        });
    }
    
    // Function to update statistics
    function updateStatistics(stats) {
        document.getElementById('high-risk-count').textContent = stats.high || 0;
        document.getElementById('medium-risk-count').textContent = stats.medium || 0;
        document.getElementById('low-risk-count').textContent = stats.low || 0;
    }
    
    // Date filter functions
    function applyFilters() {
        const filters = {
            year: document.getElementById('filter-year').value,
            month: document.getElementById('filter-month').value,
            date_from: document.getElementById('filter-date-from').value,
            date_to: document.getElementById('filter-date-to').value,
            status: document.getElementById('filter-status').value
        };
        
        // Remove empty filters
        Object.keys(filters).forEach(key => {
            if (filters[key] === '') {
                delete filters[key];
            }
        });
        
        loadReports(filters);
    }
    
    function resetFilters() {
        document.getElementById('filter-year').value = '';
        document.getElementById('filter-month').value = '';
        document.getElementById('filter-date-from').value = '';
        document.getElementById('filter-date-to').value = '';
        document.getElementById('filter-status').value = '';
        
        loadReports();
    }
    
    // Risk level filter functions
    function showAllMarkers() {
        markers.forEach(marker => {
            marker.addTo(map);
        });
        updateActiveButton('all');
    }
    
    function filterMarkers(riskLevel) {
        markers.forEach(marker => {
            if (marker.riskLevel === riskLevel) {
                marker.addTo(map);
            } else {
                map.removeLayer(marker);
            }
        });
        updateActiveButton(riskLevel);
    }
    
    function updateActiveButton(active) {
        document.querySelectorAll('.control-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        if (active === 'all') {
            document.querySelector('.control-btn').classList.add('active');
        } else {
            const buttons = document.querySelectorAll('.control-btn');
            buttons.forEach(btn => {
                if (btn.textContent.toLowerCase().includes(active)) {
                    btn.classList.add('active');
                }
            });
        }
    }
    
    // Layer switching functions
    function switchToMapView() {
        if (map.hasLayer(satelliteLayer)) {
            map.removeLayer(satelliteLayer);
        }
        if (!map.hasLayer(streetLayer)) {
            map.addLayer(streetLayer);
        }
        document.getElementById('map-view-btn').classList.add('active');
        document.getElementById('satellite-view-btn').classList.remove('active');
    }
    
    function switchToSatelliteView() {
        if (map.hasLayer(streetLayer)) {
            map.removeLayer(streetLayer);
        }
        if (!map.hasLayer(satelliteLayer)) {
            map.addLayer(satelliteLayer);
        }
        document.getElementById('satellite-view-btn').classList.add('active');
        document.getElementById('map-view-btn').classList.remove('active');
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Codes\Laravel.ReactNative\AlertDavao2.0\AdminSide\admin\resources\views/view-map.blade.php ENDPATH**/ ?>