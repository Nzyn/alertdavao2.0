@extends('layouts.app')

@section('title', 'Reports')

@section('styles')
<style>
    .reports-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .reports-title-section h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    
    .reports-title-section p {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .search-box {
        position: relative;
        width: 300px;
    }
    
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        fill: #9ca3af;
    }
    
    .reports-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .reports-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .reports-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .reports-table th {
        padding: 0.75rem 0.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        user-select: none;
        position: relative;
    }
    
    .reports-table th:hover {
        background: #f3f4f6;
    }
    
    .reports-table th.sortable::after {
        content: '\2195';
        margin-left: 0.5rem;
        opacity: 0.3;
    }
    
    .reports-table th.sorted-asc::after {
        content: '\2191';
        margin-left: 0.5rem;
        opacity: 1;
    }
    
    .reports-table th.sorted-desc::after {
        content: '\2193';
        margin-left: 0.5rem;
        opacity: 1;
    }
    
    .reports-table td {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .reports-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .reports-table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .reports-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Column widths */
    .reports-table th:nth-child(1),
    .reports-table td:nth-child(1) { width: 80px; } /* Report ID */
    
    .reports-table th:nth-child(2),
    .reports-table td:nth-child(2) { width: 120px; } /* User */
    
    .reports-table th:nth-child(3),
    .reports-table td:nth-child(3) { width: 100px; } /* Type */
    
    .reports-table th:nth-child(4),
    .reports-table td:nth-child(4) { width: 200px; } /* Description */
    
    .reports-table th:nth-child(5),
    .reports-table td:nth-child(5) { width: 120px; } /* Date Reported */
    
    .reports-table th:nth-child(6),
    .reports-table td:nth-child(6) { width: 120px; } /* Updated At */
    
    .reports-table th:nth-child(7),
    .reports-table td:nth-child(7) { width: 130px; } /* Status */
    
    .reports-table th:nth-child(8),
    .reports-table td:nth-child(8) { width: 120px; } /* Action */
    
    .status-badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    
    .status-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .status-badge.investigating {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .status-badge.resolved {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-select {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        font-size: 0.75rem;
        background-color: white;
        cursor: pointer;
        width: 100%;
        max-width: 120px;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        color: #6b7280;
        margin-right: 0.25rem;
        width: 36px;
        height: 36px;
    }
    
    .action-btn:hover {
        background: #f9fafb;
        border-color: #3b82f6;
        color: #3b82f6;
    }
    
    .report-id {
        font-family: 'Courier New', monospace;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .no-results {
        text-align: center;
        padding: 3rem 1rem;
        color: #9ca3af;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        padding: 1.5rem;
        gap: 0.5rem;
    }
    
    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .pagination a {
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }
    
    .pagination a:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
    
    .pagination .active {
        background: #3b82f6;
        color: white;
        border: 1px solid #3b82f6;
    }
    
    .pagination .disabled {
        color: #d1d5db;
        cursor: not-allowed;
    }
    
    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .modal-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        max-width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        width: 800px;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }
    
    .modal-overlay.active .modal-content {
        transform: translateY(0);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #9ca3af;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }
    
    .modal-close:hover {
        background: #f3f4f6;
        color: #1f2937;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .report-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .detail-item {
        margin-bottom: 1rem;
    }
    
    .detail-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        font-size: 0.875rem;
        color: #1f2937;
    }
    
    .media-container {
        margin-top: 1.5rem;
    }
    
    .media-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
    }
    
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .media-item {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        position: relative;
        cursor: pointer;
    }
    
    .media-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        transition: transform 0.2s ease;
    }
    
    .media-item:hover img {
        transform: scale(1.05);
    }
    
    .enlarge-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .media-item:hover .enlarge-icon {
        opacity: 1;
    }
    
    .media-item.video {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        height: 150px;
    }
    
    .media-placeholder {
        width: 100%;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #6b7280;
    }
    
    /* Image Lightbox */
    .lightbox-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10001;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
    }
    
    .lightbox-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .lightbox-content {
        max-width: 90%;
        max-height: 90vh;
    }
    
    .lightbox-content img {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 8px;
    }
    
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    
    .lightbox-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    
    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    .lightbox-prev {
        left: 20px;
    }
    
    .lightbox-next {
        right: 20px;
    }
    
    @media (max-width: 768px) {
        .reports-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .search-box {
            width: 100%;
        }
        
        .reports-table-container {
            overflow-x: auto;
        }
        
        .reports-table {
            min-width: 850px;
        }
        
        .report-details-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* PDF Styles */
    .pdf-header {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .alertWelcome {
        color: "#1D3557";
        text-align: center;
        font-size: 24px;
        font-weight: bold;
    }
    
    .davao {
        color: black;
        margin-left: 5px;
        font-size: 30px;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="reports-header">
    <div class="reports-title-section">
        <h1>Reports</h1>
        <p>Manage and view all incident reports</p>
    </div>
    
    <div class="search-box">
        <svg class="search-icon" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
        </svg>
        <input 
            type="text" 
            class="search-input" 
            placeholder="Search reports..." 
            id="searchInput"
            onkeyup="searchReports()"
        >
    </div>
</div>

<div class="reports-table-container">
    <table class="reports-table" id="reportsTable">
        <thead>
            <tr>
                <th class="sortable" data-column="0" style="width: 80px;" onclick="sortTable(0)">Report ID</th>
                <th class="sortable" data-column="1" style="width: 120px;" onclick="sortTable(1)">User</th>
                <th class="sortable" data-column="2" style="width: 100px;" onclick="sortTable(2)">Type</th>
                <th class="sortable" data-column="3" style="width: 200px;" onclick="sortTable(3)">Title</th>
                <th class="sortable" data-column="4" style="width: 120px;" onclick="sortTable(4)">Date Reported</th>
                <th class="sortable" data-column="5" style="width: 120px;" onclick="sortTable(5)">Updated At</th>
                <th class="sortable" data-column="6" style="width: 130px;" onclick="sortTable(6)">Status</th>
                <th style="width: 120px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            <tr data-report-id="{{ $report->report_id }}">
                <td class="report-id">{{ str_pad($report->report_id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>
                    @if($report->user)
                        {{ substr($report->user->firstname, 0, 1) }}. {{ substr($report->user->lastname, 0, 1) }}.
                    @else
                        Unknown
                    @endif
                </td>
                <td>{{ \Illuminate\Support\Str::limit($report->report_type ?? 'N/A', 10) }}</td>
                <td>{{ \Illuminate\Support\Str::limit($report->title, 30) }}</td>
                <td>{{ $report->created_at->timezone('Asia/Manila')->format('m/d/Y H:i') }}</td>
                <td>{{ $report->updated_at->timezone('Asia/Manila')->format('m/d/Y H:i') }}</td>
                <td>
                    <?php $reportId = $report->report_id; $status = $report->status; ?>
                    <select class="status-select" onchange="updateStatus(<?php echo $reportId; ?>, this.value)" data-original-status="<?php echo $status; ?>">
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="investigating" <?php echo $status === 'investigating' ? 'selected' : ''; ?>>Investigating</option>
                        <option value="resolved" <?php echo $status === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                </td>
                <td>
                    <?php $reportId = $report->report_id; ?>
                    <button class="action-btn" title="View Details" onclick="showReportDetails(<?php echo $reportId; ?>)">
                        <svg class="action-icon" viewBox="0 0 24 24" width="18" height="18">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </button>
                    <button class="action-btn" title="Download Report" onclick="downloadReport(<?php echo $reportId; ?>)">
                        <svg class="action-icon" viewBox="0 0 24 24" width="18" height="18">
                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                        </svg>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="no-results">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.3;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                    </svg>
                    <p>No reports found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Report Details Modal -->
<div class="modal-overlay" id="reportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Report Details</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Image Lightbox -->
<div class="lightbox-overlay" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
    <button class="lightbox-nav lightbox-prev" onclick="changeImage(-1)">&#8249;</button>
    <div class="lightbox-content">
        <img id="lightboxImage" src="" alt="Enlarged view">
    </div>
    <button class="lightbox-nav lightbox-next" onclick="changeImage(1)">&#8250;</button>
</div>

<!-- Pagination -->
@if($reports->hasPages())
<div class="pagination">
    {{-- Previous Page Link --}}
    @if ($reports->onFirstPage())
        <span class="disabled">&lsaquo;</span>
    @else
        <a href="{{ $reports->previousPageUrl() }}">&lsaquo;</a>
    @endif

    {{-- Pagination Elements --}}
    @for ($i = 1; $i <= $reports->lastPage(); $i++)
        @if ($i == $reports->currentPage())
            <span class="active">{{ $i }}</span>
        @else
            <a href="{{ $reports->url($i) }}">{{ $i }}</a>
        @endif
    @endfor

    {{-- Next Page Link --}}
    @if ($reports->hasMorePages())
        <a href="{{ $reports->nextPageUrl() }}">&rsaquo;</a>
    @else
        <span class="disabled">&rsaquo;</span>
    @endif
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// Global variables for lightbox
let currentImages = [];
let currentImageIndex = 0;

// Initialize jsPDF
const { jsPDF } = window.jspdf;

function searchReports() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('reportsTable');
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        let txtValue = tr[i].textContent || tr[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}

let sortDirections = {};

function sortTable(columnIndex) {
    const table = document.getElementById('reportsTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = Array.from(tbody.getElementsByTagName('tr'));
    const headers = table.getElementsByTagName('th');
    
    // Toggle sort direction
    if (!sortDirections[columnIndex]) {
        sortDirections[columnIndex] = 'asc';
    } else if (sortDirections[columnIndex] === 'asc') {
        sortDirections[columnIndex] = 'desc';
    } else {
        sortDirections[columnIndex] = 'asc';
    }
    
    const direction = sortDirections[columnIndex];
    
    // Remove sort classes from all headers
    for (let i = 0; i < headers.length; i++) {
        headers[i].classList.remove('sorted-asc', 'sorted-desc');
    }
    
    // Add sort class to current header
    headers[columnIndex].classList.add(direction === 'asc' ? 'sorted-asc' : 'sorted-desc');
    
    // Sort rows
    rows.sort((a, b) => {
        let aValue = a.getElementsByTagName('td')[columnIndex]?.textContent.trim() || '';
        let bValue = b.getElementsByTagName('td')[columnIndex]?.textContent.trim() || '';
        
        // Handle numeric values (Report ID)
        if (columnIndex === 0) {
            aValue = parseInt(aValue) || 0;
            bValue = parseInt(bValue) || 0;
        }
        // Handle dates (columns 4 and 5)
        else if (columnIndex === 4 || columnIndex === 5) {
            aValue = new Date(aValue).getTime() || 0;
            bValue = new Date(bValue).getTime() || 0;
        }
        
        if (aValue < bValue) return direction === 'asc' ? -1 : 1;
        if (aValue > bValue) return direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

function updateStatus(reportId, status) {
    // Store reference to the select element before the fetch call
    const selectElement = event.target;
    
    fetch(`/reports/${reportId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update successful - no need to update UI since we're using a select dropdown
            // The select already shows the current status
            // Update the original status attribute
            selectElement.setAttribute('data-original-status', status);
            
            alert('Status updated successfully');
        } else {
            alert('Failed to update status: ' + (data.message || 'Unknown error'));
            // Revert to original status
            selectElement.value = selectElement.getAttribute('data-original-status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status: ' + (error.message || 'Unknown error'));
        // Revert to original status
        selectElement.value = selectElement.getAttribute('data-original-status');
    });
}

function showReportDetails(reportId) {
    fetch(`/reports/${reportId}/details`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const report = data.data;
            const modalBody = document.getElementById('modalBody');
            
            // Format the content for the modal
            let mediaContent = '';
            if (report.media && report.media.length > 0) {
                mediaContent = `
                    <div class="media-container">
                        <h3 class="media-title">Media Files</h3>
                        <div class="media-grid">
                `;
                
                // Create an array of full media URLs for the lightbox
                const fullMediaUrls = report.media.map(media => {
                    return media.media_url.startsWith('/evidence/') 
                        ? `http://localhost:3000${media.media_url}` 
                        : media.media_url;
                });
                
                report.media.forEach((media, index) => {
                    // Construct the full URL for the media file
                    const fullMediaUrl = media.media_url.startsWith('/evidence/') 
                        ? `http://localhost:3000${media.media_url}` 
                        : media.media_url;
                    
                    const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(media.media_type.toLowerCase());
                    const isVideo = ['mp4', 'mov', 'avi'].includes(media.media_type.toLowerCase());
                    
                    if (isImage) {
                        // Escape quotes in the JSON string for the onclick handler
                        const fullMediaUrlsJson = JSON.stringify(fullMediaUrls).replace(/"/g, '&quot;');
                        mediaContent += `
                            <div class="media-item" onclick="openLightbox(${index}, '${fullMediaUrlsJson}')">
                                <img src="${fullMediaUrl}" alt="Report media" onerror="this.src='https://placehold.co/200x150?text=Image+Not+Found'">
                                <div class="enlarge-icon">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                        <path d="M15 3l2.3 2.3-2.89 2.87 1.42 1.42L18.7 6.7 21 9V3zM3 9l2.3-2.3 2.87 2.89 1.42-1.42L6.7 5.3 9 3H3zm6 12l-2.3-2.3 2.89-2.87-1.42-1.42L5.3 17.3 3 15v6zm12-6l-2.3 2.3-2.87-2.89-1.42 1.42 2.89 2.87L15 21h6z"/>
                                    </svg>
                                </div>
                            </div>
                        `;
                    } else if (isVideo) {
                        mediaContent += `
                            <div class="media-item video">
                                <svg viewBox="0 0 24 24" width="48" height="48" fill="currentColor">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <span>Video File</span>
                            </div>
                        `;
                    } else {
                        mediaContent += `
                            <div class="media-item">
                                <div class="media-placeholder">
                                    <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                    </svg>
                                </div>
                            </div>
                        `;
                    }
                });
                
                mediaContent += `
                        </div>
                    </div>
                `;
            } else {
                mediaContent = '<p>No media files available for this report.</p>';
            }
            
            modalBody.innerHTML = `
                <div class="detail-item">
                    <div class="detail-label">Title</div>
                    <div class="detail-value">${report.title || 'No title provided'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Location</div>
                    <div class="detail-value">${getLocationDisplay(report)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Description</div>
                    <div class="detail-value">${report.description || 'No description provided'}</div>
                </div>
                <div class="report-details-grid">
                    <div>
                        <div class="detail-item">
                            <div class="detail-label">Report ID</div>
                            <div class="detail-value">${report.report_id.toString().padStart(5, '0')}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Report Type</div>
                            <div class="detail-value">${report.report_type || 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="status-badge ${report.status}">${report.status}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="detail-item">
                            <div class="detail-label">Date Reported</div>
                            <div class="detail-value">${new Date(report.created_at).toLocaleString('en-US', { timeZone: 'Asia/Manila' })}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Last Updated</div>
                            <div class="detail-value">${new Date(report.updated_at).toLocaleString('en-US', { timeZone: 'Asia/Manila' })}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">User</div>
                            <div class="detail-value">
                                ${report.is_anonymous ? 'Anonymous' : (report.user ? report.user.firstname + ' ' + report.user.lastname : 'Unknown User')}
                            </div>
                        </div>
                    </div>
                </div>
                ${mediaContent}
            `;
            
            // Show the modal
            document.getElementById('reportModal').classList.add('active');
        } else {
            alert('Failed to load report details: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading report details: ' + error.message);
    });
}

function getLocationDisplay(report) {
    // Check if location object exists with coordinates
    if (report.location) {
        const lat = report.location.latitude;
        const lng = report.location.longitude;
        const barangay = report.location.barangay || '';
        
        // Build location string with coordinates
        if (lat !== null && lat !== undefined && lng !== null && lng !== undefined) {
            if (barangay && barangay !== 'Unknown' && !barangay.startsWith('Lat:')) {
                return barangay + ' (' + lat.toFixed(4) + ', ' + lng.toFixed(4) + ')';
            } else {
                return '(' + lat.toFixed(4) + ', ' + lng.toFixed(4) + ')';
            }
        }
    }
    
    // Fallback: Check direct properties (shouldn't happen with current setup)
    if (report.latitude && report.longitude) {
        return '(' + report.latitude.toFixed(4) + ', ' + report.longitude.toFixed(4) + ')';
    }
    
    // No location data found
    return 'No location provided';
}

function closeModal() {
    document.getElementById('reportModal').classList.remove('active');
}

function downloadReport(reportId) {
    fetch(`/reports/${reportId}/details`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const report = data.data;
            generatePDF(report);
        } else {
            alert('Failed to load report details: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading report details: ' + error.message);
    });
}

function generatePDF(report) {
    // Create a temporary HTML element for rendering
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.left = '-9999px';
    tempContainer.style.width = '800px';
    tempContainer.style.padding = '20px';
    tempContainer.style.fontFamily = 'Arial, sans-serif';
    tempContainer.style.backgroundColor = 'white';
    
    // Get location display
    const locationDisplay = getLocationDisplay(report);
    
    // Get user display (anonymous or actual name)
    const userDisplay = report.is_anonymous ? 'Anonymous' : (report.user ? report.user.firstname + ' ' + report.user.lastname : 'Unknown User');
    
    // Create HTML content for the PDF
    tempContainer.innerHTML = `
        <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1D3557; padding-bottom: 20px;">
            <div class="alertWelcome">Alert</div>
            <div class="davao">Davao</div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">Title</div>
            <div style="margin-bottom: 15px; padding-left: 10px;">${report.title || 'No title provided'}</div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">Location</div>
            <div style="margin-bottom: 15px; padding-left: 10px;">${locationDisplay}</div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">Description</div>
            <div style="margin-bottom: 15px; padding-left: 10px;">${report.description || 'No description provided'}</div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">Report ID</div>
                <div style="margin-bottom: 15px; padding-left: 10px;">${report.report_id.toString().padStart(5, '0')}</div>
                
                <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">Report Type</div>
                <div style="margin-bottom: 15px; padding-left: 10px;">${report.report_type || 'N/A'}</div>
                
                <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">Status</div>
                <div style="margin-bottom: 15px; padding-left: 10px;">${report.status}</div>
            </div>
            <div>
                <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">Date Reported</div>
                <div style="margin-bottom: 15px; padding-left: 10px;">${new Date(report.created_at).toLocaleString('en-US', { timeZone: 'Asia/Manila' })}</div>
                
                <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">Last Updated</div>
                <div style="margin-bottom: 15px; padding-left: 10px;">${new Date(report.updated_at).toLocaleString('en-US', { timeZone: 'Asia/Manila' })}</div>
                
                <div style="font-weight: bold; margin-bottom: 5px; color: #1D3557;">User</div>
                <div style="margin-bottom: 15px; padding-left: 10px;">${userDisplay}</div>
            </div>
        </div>
        
        <div id="images-container" style="margin-top: 20px;"></div>
    `;
    
    // Add images container
    const imagesContainer = tempContainer.querySelector('#images-container');
    if (report.media && report.media.length > 0) {
        const imagesTitle = document.createElement('div');
        imagesTitle.style.fontWeight = 'bold';
        imagesTitle.style.marginBottom = '15px';
        imagesTitle.style.color = '#1D3557';
        imagesTitle.textContent = 'Attached Images';
        imagesContainer.appendChild(imagesTitle);
        
        // Add placeholder for images
        const imagesPlaceholder = document.createElement('div');
        imagesPlaceholder.id = 'pdf-images';
        imagesPlaceholder.style.display = 'flex';
        imagesPlaceholder.style.flexWrap = 'wrap';
        imagesPlaceholder.style.gap = '10px';
        imagesContainer.appendChild(imagesPlaceholder);
    }
    
    document.body.appendChild(tempContainer);
    
    // Load and insert images before capturing
    const imagePromises = [];
    if (report.media && report.media.length > 0) {
        const imagesPlaceholder = tempContainer.querySelector('#pdf-images');
        
        report.media.forEach((mediaItem) => {
            // Only process image files
            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes((mediaItem.media_type || '').toLowerCase());
            
            if (!isImage) {
                return; // Skip non-image files
            }
            
            const promise = new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => {
                    const container = document.createElement('div');
                    container.style.marginBottom = '8px';
                    container.style.width = '100%';
                    
                    const imgElement = document.createElement('img');
                    imgElement.src = img.src;
                    imgElement.style.maxWidth = '40%';
                    imgElement.style.height = 'auto';
                    imgElement.style.border = '1px solid #ccc';
                    imgElement.style.borderRadius = '4px';
                    
                    container.appendChild(imgElement);
                    imagesPlaceholder.appendChild(container);
                    console.log('Image loaded successfully:', mediaItem.media_url);
                    resolve();
                };
                img.onerror = () => {
                    console.error('Failed to load image:', mediaItem.media_url);
                    resolve(); // Resolve instead of reject to continue with other images
                };
                
                // Construct the full image URL
                const imageUrl = mediaItem.media_url.startsWith('/evidence/') 
                    ? `http://localhost:3000${mediaItem.media_url}` 
                    : mediaItem.media_url;
                
                console.log('Loading image from:', imageUrl);
                img.src = imageUrl;
            });
            
            imagePromises.push(promise);
        });
    }
    
    // Wait for all images to load before generating PDF
    Promise.allSettled(imagePromises).then(() => {
        // Use html2canvas to capture the content
        html2canvas(tempContainer, {
            scale: 2, // Higher quality
            useCORS: true,
            logging: false,
            allowTaint: true
        }).then(canvas => {
            // Create PDF
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgWidth = 210; // A4 width in mm
            const pageHeight = 297; // A4 height in mm
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;
            
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
            
            // Add new pages if content is too long
            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }
            
            // Save the PDF
            const fileName = `report_${report.report_id.toString().padStart(5, '0')}.pdf`;
            pdf.save(fileName);
            
            // Clean up
            document.body.removeChild(tempContainer);
        }).catch(error => {
            console.error('Error generating PDF:', error);
            alert('Error generating PDF: ' + error.message);
            document.body.removeChild(tempContainer);
        });
    });
}

// Lightbox functions
function openLightbox(index, imagesJson) {
    // Parse the JSON string back to an array
    currentImages = JSON.parse(imagesJson.replace(/&quot;/g, '"'));
    currentImageIndex = index;
    document.getElementById('lightboxImage').src = currentImages[currentImageIndex];
    document.getElementById('lightbox').classList.add('active');
    
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    
    // Re-enable background scrolling
    document.body.style.overflow = '';
}

function changeImage(direction) {
    currentImageIndex += direction;
    
    // Handle boundaries
    if (currentImageIndex < 0) {
        currentImageIndex = currentImages.length - 1;
    } else if (currentImageIndex >= currentImages.length) {
        currentImageIndex = 0;
    }
    
    document.getElementById('lightboxImage').src = currentImages[currentImageIndex];
}

// Close modals when clicking outside
document.getElementById('reportModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeModal();
    }
});

document.getElementById('lightbox').addEventListener('click', function(event) {
    if (event.target === this) {
        closeLightbox();
    }
});

// Keyboard navigation for lightbox
document.addEventListener('keydown', function(event) {
    // Only handle keyboard events when lightbox is open
    if (document.getElementById('lightbox').classList.contains('active')) {
        switch(event.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                changeImage(-1);
                break;
            case 'ArrowRight':
                changeImage(1);
                break;
        }
    }
});
</script>
@endsection