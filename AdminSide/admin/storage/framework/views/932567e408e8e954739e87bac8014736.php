<?php $__env->startSection('title', 'Users'); ?>

<?php $__env->startSection('styles'); ?>
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
    }
    
    .role-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .role-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #ffffff;
        min-width: 140px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        margin-top: 0.25rem;
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="user-id"><?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></td>
                <td><?php echo e(ucfirst($user->role)); ?></td>
                <td><?php echo e($user->firstname); ?> <?php echo e($user->lastname); ?></td>
                <td><?php echo e($user->email); ?></td>
                <td class="address-cell"><?php echo e($user->address ?? 'N/A'); ?></td>
                <td><?php echo e($user->created_at->timezone('Asia/Manila')->format('m/d/Y')); ?></td>
                <td>
                    <span class="status-badge <?php echo e($user->is_verified ? 'verified' : 'pending'); ?>">
                        <?php echo e($user->is_verified ? 'Verified' : 'Pending'); ?>

                    </span>
                </td>
                <td>
                    <div class="action-buttons-group">
                        <button class="action-btn danger flag-user-btn" data-user-id="<?php echo e($user->id); ?>" title="Flag User">
                            <svg class="action-icon" viewBox="0 0 24 24">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                                <line x1="4" y1="22" x2="4" y2="15"/>
                            </svg>
                        </button>
                        <div class="role-dropdown">
                            <button class="dropdown-btn role-toggle-btn" data-user-id="<?php echo e($user->id); ?>" title="Change Role">
                                <svg class="action-icon" viewBox="0 0 24 24" style="transform: rotate(90deg);">
                                    <path d="M5 3v18l9-9L5 3z"/>
                                </svg>
                            </button>
                            <div class="role-dropdown-content">
                                <a href="#" data-user-id="<?php echo e($user->id); ?>" class="role-option" data-role="user">User</a>
                                <a href="#" data-user-id="<?php echo e($user->id); ?>" class="role-option" data-role="police">Police</a>
                                <a href="#" data-user-id="<?php echo e($user->id); ?>" class="role-option" data-role="admin">Admin</a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="8" class="no-results">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.3;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <p>No users found</p>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Codes\AlertDavao2.0\AdminSide\admin\resources\views/users.blade.php ENDPATH**/ ?>