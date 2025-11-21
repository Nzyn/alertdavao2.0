@extends('layouts.app')

@section('title', 'Users')

@section('styles')
<style>
    .users-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .users-title-section h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    
    .users-title-section p {
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
    
    .users-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .users-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .users-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .users-table th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        user-select: none;
        position: relative;
    }
    
    .users-table th:hover {
        background: #f3f4f6;
    }
    
    .users-table th.sortable::after {
        content: '\2195';
        margin-left: 0.5rem;
        opacity: 0.3;
    }
    
    .users-table th.sorted-asc::after {
        content: '\2191';
        margin-left: 0.5rem;
        opacity: 1;
    }
    
    .users-table th.sorted-desc::after {
        content: '\2193';
        margin-left: 0.5rem;
        opacity: 1;
    }
    
    .users-table td {
        padding: 1rem 1.5rem;
        font-size: 0.875rem;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .users-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .users-table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .users-table tbody tr:last-child td {
        border-bottom: none;
    }
    
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
    
    .status-badge.verified {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-badge.flagged {
        background-color: #fee2e2;
        color: #991b1b;
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
    }
    
    .action-btn:hover {
        background: #f9fafb;
        border-color: #3b82f6;
        color: #3b82f6;
    }
    
    .action-btn.danger:hover {
        border-color: #ef4444;
        color: #ef4444;
    }
    
    .action-icon {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }
    
    .user-id {
        font-family: 'Courier New', monospace;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .no-results {
        text-align: center;
        padding: 3rem 1rem;
        color: #9ca3af;
    }
    
    .action-buttons-group {
        display: flex;
        gap: 0.25rem;
        align-items: center;
        position: relative;
        z-index: 10;
    }
    
    .role-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .role-dropdown-content {
        display: none;
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        background-color: #ffffff;
        min-width: 140px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1000;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }
    
    .role-dropdown-content.active {
        display: block;
    }
    
    .role-option {
        color: #374151;
        padding: 0.75rem 1rem;
        text-decoration: none;
        display: block;
        font-size: 0.875rem;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .role-option:last-child {
        border-bottom: none;
    }
    
    .role-option:hover {
        background-color: #f9fafb;
        color: #3b82f6;
    }
    
    .dropdown-btn {
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
    }
    
    .dropdown-btn:hover {
        background: #f9fafb;
        border-color: #3b82f6;
        color: #3b82f6;
    }
    
    .address-cell {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .modal-overlay {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .modal-overlay.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background-color: #fefefe;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow: visible;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }
    
    .modal-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-close-btn:hover {
        color: #1f2937;
    }
    
    .station-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 10;
    }
    
    .station-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .modal-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }
    
    .btn-cancel {
        padding: 0.75rem 1.5rem;
        border: 1px solid #d1d5db;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        transition: all 0.2s ease;
    }
    
    .btn-cancel:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }
    
    .btn-assign {
        padding: 0.75rem 1.5rem;
        border: none;
        background: #3b82f6;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-assign:hover {
        background: #2563eb;
    }
    
    .btn-assign:disabled {
        background: #d1d5db;
        cursor: not-allowed;
    }
    
    .assign-station-btn {
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
        title: 'Assign to Station';
    }
    
    .assign-station-btn:hover {
        background: #f9fafb;
        border-color: #3b82f6;
        color: #3b82f6;
    }
    
    @media (max-width: 768px) {
        .users-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .search-box {
            width: 100%;
        }
        
        .users-table-container {
            overflow-x: auto;
        }
        
        .users-table {
            min-width: 800px;
        }
    }
</style>
@endsection

@section('content')
<div class="users-header">
    <div class="users-title-section">
        <h1>Users</h1>
        <p>Manage user accounts and permissions</p>
    </div>
    
    <div class="search-box">
        <svg class="search-icon" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
        </svg>
        <input 
            type="text" 
            class="search-input" 
            placeholder="Search users..." 
            id="searchInput"
            onkeyup="searchUsers()"
        >
    </div>
</div>

<div class="users-table-container">
    <table class="users-table" id="usersTable">
        <thead>
            <tr>
                <th class="sortable" onclick="sortUsersTable(0)">User ID</th>
                <th class="sortable" onclick="sortUsersTable(1)">Type</th>
                <th class="sortable" onclick="sortUsersTable(2)">Name</th>
                <th class="sortable" onclick="sortUsersTable(3)">Email</th>
                <th class="sortable" onclick="sortUsersTable(4)">Address</th>
                <th class="sortable" onclick="sortUsersTable(5)">Date of Registration</th>
                <th class="sortable" onclick="sortUsersTable(6)">Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="user-id">{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ ucfirst($user->role) }}</td>
                <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                <td>{{ $user->email }}</td>
                <td class="address-cell">{{ $user->address ?? 'N/A' }}</td>
                <td>{{ $user->created_at->timezone('Asia/Manila')->format('m/d/Y') }}</td>
                <td>
                    <span class="status-badge {{ $user->is_verified ? 'verified' : 'pending' }}">
                        {{ $user->is_verified ? 'Verified' : 'Pending' }}
                    </span>
                </td>
                <td>
                    <div class="action-buttons-group">
                        <button class="action-btn danger flag-user-btn" data-user-id="{{ $user->id }}" title="Flag User">
                            <svg class="action-icon" viewBox="0 0 24 24">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                                <line x1="4" y1="22" x2="4" y2="15"/>
                            </svg>
                        </button>
                        @if($user->role === 'admin' || $user->role === 'police')
                        <button class="action-btn assign-station-btn" data-user-id="{{ $user->id }}" title="Assign to Police Station">
                            <svg class="action-icon" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-5-9h10v2H7z"/>
                            </svg>
                        </button>
                        @endif
                        <div class="role-dropdown">
                            <button class="dropdown-btn role-toggle-btn" data-user-id="{{ $user->id }}" title="Change Role">
                                <svg class="action-icon" viewBox="0 0 24 24" style="transform: rotate(90deg);">
                                    <path d="M5 3v18l9-9L5 3z"/>
                                </svg>
                            </button>
                            <div class="role-dropdown-content">
                                <a href="#" data-user-id="{{ $user->id }}" class="role-option" data-role="user">User</a>
                                <a href="#" data-user-id="{{ $user->id }}" class="role-option" data-role="police">Police</a>
                                <a href="#" data-user-id="{{ $user->id }}" class="role-option" data-role="admin">Admin</a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="no-results">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.3;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <p>No users found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Assign Station Modal -->
<div id="assignStationModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Assign User to Police Station</h2>
            <button class="modal-close-btn" id="modalCloseBtn">&times;</button>
        </div>
        
        <div>
            <label for="stationSelect" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; font-size: 0.875rem;">Police Station</label>
            <select id="stationSelect" class="station-select">
                <option value="">-- Select a Police Station --</option>
            </select>
        </div>
        
        <div class="modal-buttons">
            <button class="btn-cancel" id="cancelBtn">Cancel</button>
            <button class="btn-assign" id="assignBtn">Assign Station</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function searchUsers() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('usersTable');
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

let usersSortDirections = {};

function sortUsersTable(columnIndex) {
    const table = document.getElementById('usersTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = Array.from(tbody.getElementsByTagName('tr'));
    const headers = table.getElementsByTagName('th');
    
    // Toggle sort direction
    if (!usersSortDirections[columnIndex]) {
        usersSortDirections[columnIndex] = 'asc';
    } else if (usersSortDirections[columnIndex] === 'asc') {
        usersSortDirections[columnIndex] = 'desc';
    } else {
        usersSortDirections[columnIndex] = 'asc';
    }
    
    const direction = usersSortDirections[columnIndex];
    
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
        
        // Handle numeric values (User ID)
        if (columnIndex === 0) {
            aValue = parseInt(aValue) || 0;
            bValue = parseInt(bValue) || 0;
        }
        // Handle dates (column 5)
        else if (columnIndex === 5) {
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

function flagUser(userId) {
    if (confirm('Are you sure you want to flag this user?')) {
        fetch('/users/' + userId + '/flag', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User has been flagged successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while flagging the user');
        });
    }
}

function changeRole(userId, newRole) {
    if (confirm('Are you sure you want to change this user\'s role to "' + newRole + '"?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            alert('Security token not found. Please refresh the page and try again.');
            return;
        }
        
        console.log('Changing role for user', userId, 'to', newRole);
        
        fetch('/users/' + userId + '/change-role', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ role: newRole })
        })
        .then(async response => {
            const data = await response.json();
            console.log('Response status:', response.status);
            console.log('Response data:', data);
            
            if (data.success) {
                alert('User role has been changed to "' + newRole + '" successfully');
                location.reload();
            } else {
                const errorMsg = data.message || 'Unknown error occurred';
                console.error('API error:', errorMsg);
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
                alert('Error: ' + errorMsg);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An error occurred while changing the user role: ' + error.message);
        });
    }
}

// Load police stations for modal
async function loadPoliceStations() {
    try {
        const response = await fetch('/api/police-stations', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            const stationSelect = document.getElementById('stationSelect');
            stationSelect.innerHTML = '<option value="">-- Select a Police Station --</option>';
            
            if (data.success && data.data) {
                // Sort stations by station number (lowest to highest)
                const sortedStations = data.data.sort((a, b) => {
                    const aNum = parseInt(a.station_name?.match(/\d+/)?.[0] || a.name?.match(/\d+/)?.[0] || 0);
                    const bNum = parseInt(b.station_name?.match(/\d+/)?.[0] || b.name?.match(/\d+/)?.[0] || 0);
                    return aNum - bNum;
                });
                
                sortedStations.forEach(station => {
                    const option = document.createElement('option');
                    option.value = station.station_id || station.id;
                    option.textContent = station.station_name || station.name;
                    stationSelect.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error loading police stations:', error);
        alert('Failed to load police stations');
    }
}

function openAssignStationModal(userId) {
    document.getElementById('assignStationModal').classList.add('active');
    document.getElementById('assignBtn').setAttribute('data-user-id', userId);
    document.getElementById('stationSelect').value = '';
    loadPoliceStations();
}

function closeAssignStationModal() {
    document.getElementById('assignStationModal').classList.remove('active');
}

function assignStationToAdmin() {
    const userId = document.getElementById('assignBtn').getAttribute('data-user-id');
    const stationId = document.getElementById('stationSelect').value;
    
    if (!stationId) {
        alert('Please select a police station');
        return;
    }
    
    if (!confirm('Are you sure you want to assign this user to the selected police station?')) {
        return;
    }
    
    const assignBtn = document.getElementById('assignBtn');
    assignBtn.disabled = true;
    assignBtn.textContent = 'Assigning...';
    
    fetch('/users/' + userId + '/assign-station', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ station_id: stationId })
    })
    .then(async response => {
        const data = await response.json();
        console.log('Response status:', response.status);
        console.log('Response data:', data);
        
        if (data.success) {
            alert('Admin user has been assigned to the police station successfully');
            closeAssignStationModal();
            location.reload();
        } else {
            const errorMsg = data.message || 'Unknown error occurred';
            console.error('API error:', errorMsg);
            alert('Error: ' + errorMsg);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('An error occurred while assigning the user: ' + error.message);
    })
    .finally(() => {
        assignBtn.disabled = false;
        assignBtn.textContent = 'Assign Station';
    });
}

// Add event listeners after the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Flag user button click handler
    document.querySelectorAll('.flag-user-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            flagUser(userId);
        });
    });
    
    // Assign station button click handler
    document.querySelectorAll('.assign-station-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            openAssignStationModal(userId);
        });
    });
    
    // Modal button handlers
    document.getElementById('modalCloseBtn').addEventListener('click', closeAssignStationModal);
    document.getElementById('cancelBtn').addEventListener('click', closeAssignStationModal);
    document.getElementById('assignBtn').addEventListener('click', assignStationToAdmin);
    
    // Close modal when clicking outside
    document.getElementById('assignStationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAssignStationModal();
        }
    });
    
    // Role toggle button click handler - toggle dropdown visibility
    document.querySelectorAll('.role-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Close all other dropdowns
            document.querySelectorAll('.role-dropdown-content').forEach(dropdown => {
                if (dropdown !== this.nextElementSibling) {
                    dropdown.classList.remove('active');
                }
            });
            
            // Toggle current dropdown
            const dropdown = this.nextElementSibling;
            dropdown.classList.toggle('active');
        });
    });
    
    // Role option click handler
    document.querySelectorAll('.role-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            const newRole = this.getAttribute('data-role');
            changeRole(userId, newRole);
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.role-dropdown') && !e.target.closest('.role-toggle-btn')) {
            document.querySelectorAll('.role-dropdown-content').forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
});
</script>
@endsection