@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8fafc;
                color: #1f2937;
                line-height: 1.6;
            }
            
            .dashboard {
                display: flex;
                min-height: 100vh;
            }
            
            /* Sidebar Styles */
            .sidebar {
                width: 250px;
                background: white;
                padding: 2rem 0;
                position: fixed;
                height: 100vh;
                left: 0;
                top: 0;
                z-index: 1000;
                border-right: 1px solid #e5e7eb;
                box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
            }
            
            .sidebar-header {
                padding: 0 1.5rem;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .sidebar-title {
                color: #1D3557;
                font-size: 1.25rem;
                font-weight: 700;
                margin: 0;
            }
            
            .sidebar-close {
                background: none;
                border: none;
                color: #6b7280;
                font-size: 1.25rem;
                margin-left: auto;
                cursor: pointer;
            }
            
            .nav-menu {
                list-style: none;
                padding: 0;
            }
            
            .nav-item {
                margin: 0.25rem 0;
            }
            
            .nav-link {
                display: flex;
                align-items: center;
                padding: 0.875rem 1.5rem;
                color: #6b7280;
                text-decoration: none;
                transition: all 0.3s ease;
                gap: 0.75rem;
                border-radius: 0.375rem;
                margin: 0.125rem 0.75rem;
            }
            
            .nav-link:hover,
            .nav-link.active {
                background: #f3f4f6;
                color: #1D3557;
                border-left: 3px solid #3b82f6;
                font-weight: 500;
            }
            
            .nav-icon {
                width: 20px;
                height: 20px;
                fill: currentColor;
            }
            
            /* Main Content */
            .main-content {
                margin-left: 250px;
                padding: 2rem;
                width: calc(100% - 250px);
            }
            
            .content-header {
                margin-bottom: 2rem;
            }
            
            .content-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }
            
            /* Stats Cards */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2rem;
            }
            
            .stat-card {
                background: white;
                padding: 1.5rem;
                border-radius: 12px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                border-left: 4px solid #e5e7eb;
                transition: all 0.3s ease;
                text-decoration: none;
                display: block;
                color: inherit;
                cursor: pointer;
            }
            
            .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            }
            
            .stat-card.total {
                border-left-color: #3b82f6;
            }
            
            .stat-card.verified {
                border-left-color: #10b981;
            }
            
            .stat-card.pending {
                border-left-color: #f59e0b;
            }
            
            .stat-title {
                font-size: 0.875rem;
                color: #6b7280;
                margin-bottom: 0.5rem;
                text-transform: uppercase;
                font-weight: 500;
            }
            
            .stat-value {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 0.25rem;
            }
            
            /* Dashboard Grid */
            .dashboard-grid {
                display: block;
                margin-bottom: 2rem;
            }
            
            .priority-section {
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                border: 3px solid #3b82f6;
                width: 100%;
            }
            
            .section-title {
                font-size: 1.125rem;
                font-weight: 600;
                margin-bottom: 1rem;
                color: #1f2937;
            }
            
            .priority-content {
                display: grid;
                grid-template-columns: 300px 1fr 300px;
                gap: 2rem;
                align-items: center;
            }
            
            .priority-cases {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .priority-item {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.875rem;
            }
            
            .priority-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                flex-shrink: 0;
            }
            
            .priority-dot.high {
                background-color: #dc2626;
            }
            
            .priority-dot.medium {
                background-color: #f59e0b;
            }
            
            .priority-dot.low {
                background-color: #10b981;
            }
            
            .priority-text {
                font-weight: 500;
            }
            
            .priority-count {
                font-weight: 700;
                margin-left: auto;
            }
            
            .map-placeholder {
                background: #f3f4f6;
                border-radius: 8px;
                height: 250px;
                min-height: 250px;
                width: 100%;
                position: relative;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }
            
            #dashboard-mini-map {
                height: 100%;
                width: 100%;
                border-radius: 8px;
                z-index: 1;
            }
            
            .mini-map-controls {
                position: absolute;
                top: 10px;
                right: 10px;
                z-index: 1000;
                display: flex;
                gap: 5px;
            }
            
            .mini-map-btn {
                background: white;
                border: none;
                padding: 6px 10px;
                border-radius: 4px;
                font-size: 0.75rem;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                font-weight: 500;
                color: #374151;
                transition: all 0.2s;
            }
            
            .mini-map-btn:hover {
                background: #f3f4f6;
                color: #3b82f6;
            }
            
            .mini-map-btn.active {
                background: #3b82f6;
                color: white;
            }
            
            .gender-chart {
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            
            .pie-chart {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                margin: 1rem auto;
                background: conic-gradient(
                    #3b82f6 0deg 180deg,
                    #10b981 180deg 300deg,
                    #6b7280 300deg 360deg
                );
                position: relative;
            }
            
            .pie-chart::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 60px;
                height: 60px;
                background: white;
                border-radius: 50%;
            }
            
            .chart-legend {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                align-items: center;
            }
            
            .legend-item {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.875rem;
            }
            
            .legend-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
            }
            
            .legend-dot.male {
                background-color: #3b82f6;
            }
            
            .legend-dot.female {
                background-color: #10b981;
            }
            
            .legend-dot.others {
                background-color: #6b7280;
            }
            
            /* Bottom Chart */
            .bottom-chart {
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            
            .line-chart {
                height: 200px;
                background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 200" fill="none"><rect width="400" height="200" fill="white"/><polyline points="50,150 100,130 150,120 200,110 250,100 350,80" stroke="%233b82f6" stroke-width="3" fill="none"/><circle cx="50" cy="150" r="4" fill="%233b82f6"/><circle cx="100" cy="130" r="4" fill="%233b82f6"/><circle cx="150" cy="120" r="4" fill="%233b82f6"/><circle cx="200" cy="110" r="4" fill="%233b82f6"/><circle cx="250" cy="100" r="4" fill="%233b82f6"/><circle cx="350" cy="80" r="4" fill="%233b82f6"/></svg>');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #6b7280;
            }
            
            @media (max-width: 768px) {
                .sidebar {
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }
                
                .main-content {
                    margin-left: 0;
                    width: 100%;
                }
                
                .dashboard-grid {
                    grid-template-columns: 1fr;
                }
                
                .priority-content {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }
            }
        </style>
@endsection

@section('content')
    <div class="content-header">
        <h1 class="content-title">Dashboard Overview</h1>
    </div>
                
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <a href="{{ route('reports') }}" class="stat-card total">
                        <div class="stat-title">Total Reports</div>
                        <div class="stat-value">84</div>
                    </a>
                    <a href="{{ route('reports', ['status' => 'investigating']) }}" class="stat-card verified">
                        <div class="stat-title">Verified Reports</div>
                        <div class="stat-value">26</div>
                    </a>
                    <a href="{{ route('reports', ['status' => 'pending']) }}" class="stat-card pending">
                        <div class="stat-title">Pending Reports</div>
                        <div class="stat-value">47</div>
                    </a>
                </div>
                
                <!-- Priority Cases and Map -->
                <div class="dashboard-grid">
                    <div class="priority-section">
                        <h2 class="section-title">Priority Cases</h2>
                        <div class="priority-content">
                            <div class="priority-cases">
                                <div class="priority-item">
                                    <div class="priority-dot high"></div>
                                    <span class="priority-text">High Risk:</span>
                                    <span class="priority-count">15</span>
                                </div>
                                <div class="priority-item">
                                    <div class="priority-dot medium"></div>
                                    <span class="priority-text">Medium Risk:</span>
                                    <span class="priority-count">45</span>
                                </div>
                                <div class="priority-item">
                                    <div class="priority-dot low"></div>
                                    <span class="priority-text">Low Risk:</span>
                                    <span class="priority-count">60</span>
                                </div>
                            </div>
                            
                            <div class="map-placeholder">
                                <div class="mini-map-controls">
                                    <button class="mini-map-btn active" id="mini-all-btn" onclick="miniShowAll()">All</button>
                                    <button class="mini-map-btn" id="mini-high-btn" onclick="miniFilterHigh()">High</button>
                                    <button class="mini-map-btn" id="mini-medium-btn" onclick="miniFilterMedium()">Med</button>
                                    <button class="mini-map-btn" id="mini-low-btn" onclick="miniFilterLow()">Low</button>
                                </div>
                                <div id="dashboard-mini-map"></div>
                            </div>
                            
                            <div class="gender-chart">
                                <h3 class="section-title">Victims by Gender</h3>
                                <div class="pie-chart"></div>
                                <div class="chart-legend">
                                    <div class="legend-item">
                                        <div class="legend-dot male"></div>
                                        <span>Male</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-dot female"></div>
                                        <span>Female</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-dot others"></div>
                                        <span>Others</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Trends -->
                <div class="bottom-chart">
                    <h2 class="section-title">Monthly Trends</h2>
                    <div class="line-chart">
                        <span>Monthly Report Trends Chart</span>
                    </div>
                </div>
@endsection

@section('scripts')
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
    // Global variables for mini map
    let miniMap;
    let miniMarkers = [];
    let miniReports = [];
    let miniHighRiskIcon, miniMediumRiskIcon, miniLowRiskIcon;
    
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            // Initialize the mini map centered on Davao City
            miniMap = L.map('dashboard-mini-map', {
                zoomControl: false,
                attributionControl: false
            }).setView([7.1907, 125.4553], 12);
    
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '',
                maxZoom: 18,
            }).addTo(miniMap);
            
            // Define custom icons for different risk levels (smaller for mini map)
            miniHighRiskIcon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background-color: #dc2626; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });
            
            miniMediumRiskIcon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background-color: #f59e0b; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });
            
            miniLowRiskIcon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background-color: #10b981; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });
            
            // Load initial data
            loadMiniMapReports();
        }, 100);
    });
    
    // Function to load reports from API
    function loadMiniMapReports() {
        fetch('{{ route("api.reports") }}')
            .then(response => response.json())
            .then(data => {
                miniReports = data.reports;
                updateMiniMapMarkers(data.reports);
                updateMiniStats(data.stats);
            })
            .catch(error => {
                console.error('Error loading mini map reports:', error);
            });
    }
    
    // Function to update map markers
    function updateMiniMapMarkers(reports) {
        // Clear existing markers
        miniMarkers.forEach(marker => {
            miniMap.removeLayer(marker);
        });
        miniMarkers = [];
        
        // Add new markers
        reports.forEach(report => {
            let icon;
            if (report.risk_level === 'high') icon = miniHighRiskIcon;
            else if (report.risk_level === 'medium') icon = miniMediumRiskIcon;
            else icon = miniLowRiskIcon;
            
            const marker = L.marker([report.latitude, report.longitude], { icon: icon })
                .addTo(miniMap)
                .bindPopup(`
                    <div style="font-size: 0.75rem;">
                        <strong style="font-size: 0.875rem;">${report.title}</strong><br>
                        <strong>Risk:</strong> ${report.risk_level.toUpperCase()}<br>
                        <strong>Location:</strong> ${report.location_name}<br>
                        <strong>Status:</strong> ${report.status}
                    </div>
                `);
            
            marker.riskLevel = report.risk_level;
            marker.reportData = report;
            miniMarkers.push(marker);
        });
    }
    
    // Function to update statistics in the priority cases section
    function updateMiniStats(stats) {
        const priorityCounts = document.querySelectorAll('.priority-count');
        if (priorityCounts.length >= 3) {
            priorityCounts[0].textContent = stats.high || 0;
            priorityCounts[1].textContent = stats.medium || 0;
            priorityCounts[2].textContent = stats.low || 0;
        }
    }
    
    // Filter functions for mini map
    function miniShowAll() {
        miniMarkers.forEach(marker => marker.addTo(miniMap));
        updateMiniMapButtons('all');
    }
    
    function miniFilterHigh() {
        miniMarkers.forEach(marker => {
            if (marker.riskLevel === 'high') {
                marker.addTo(miniMap);
            } else {
                miniMap.removeLayer(marker);
            }
        });
        updateMiniMapButtons('high');
    }
    
    function miniFilterMedium() {
        miniMarkers.forEach(marker => {
            if (marker.riskLevel === 'medium') {
                marker.addTo(miniMap);
            } else {
                miniMap.removeLayer(marker);
            }
        });
        updateMiniMapButtons('medium');
    }
    
    function miniFilterLow() {
        miniMarkers.forEach(marker => {
            if (marker.riskLevel === 'low') {
                marker.addTo(miniMap);
            } else {
                miniMap.removeLayer(marker);
            }
        });
        updateMiniMapButtons('low');
    }
    
    function updateMiniMapButtons(active) {
        document.querySelectorAll('.mini-map-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        if (active === 'all') {
            document.getElementById('mini-all-btn').classList.add('active');
        } else if (active === 'high') {
            document.getElementById('mini-high-btn').classList.add('active');
        } else if (active === 'medium') {
            document.getElementById('mini-medium-btn').classList.add('active');
        } else if (active === 'low') {
            document.getElementById('mini-low-btn').classList.add('active');
        }
    }
</script>
@endsection
