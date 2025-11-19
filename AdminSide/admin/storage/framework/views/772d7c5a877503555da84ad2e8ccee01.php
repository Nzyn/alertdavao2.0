

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
    
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #ffffff;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        margin-top: 0.25rem;
    }
    
    .dropdown-content a {
        color: #374151;
        padding: 0.75rem 1rem;
        text-decoration: none;
        display: block;
        font-size: 0.875rem;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .dropdown-content a:last-child {
        border-bottom: none;
    }
    
    .dropdown-content a:hover {
        background-color: #f9fafb;
    }
    
    .dropdown:hover .dropdown-content {
        display: block;
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
                <th>User ID</th>
                <th>Type</th>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Date of Registration</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="user-id"><?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></td>
                <td><?php echo e($user->station_id ? 'Officer' : 'User'); ?></td>
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
                    <div class="dropdown">
                        <button class="dropdown-btn" title="Actions">
                            <svg class="action-icon" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="1"/>
                                <circle cx="12" cy="5" r="1"/>
                                <circle cx="12" cy="19" r="1"/>
                            </svg>
                        </button>
                        <div class="dropdown-content">
                            <a href="#" data-user-id="<?php echo e($user->id); ?>" class="flag-user-link">
                                <svg class="action-icon" style="margin-right: 0.5rem;" viewBox="0 0 24 24">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                                    <line x1="4" y1="22" x2="4" y2="15"/>
                                </svg>
                                Flag User
                            </a>
                            <?php if(!$user->station_id): ?>
                            <a href="#" data-user-id="<?php echo e($user->id); ?>" class="promote-user-link">
                                <svg class="action-icon" style="margin-right: 0.5rem;" viewBox="0 0 24 24">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                Promote to Officer
                            </a>
                            <?php endif; ?>
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

function flagUser(userId) {
    if (confirm('Are you sure you want to flag this user?')) {
        // Make AJAX call to flag the user
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
                // Refresh the page to show updated status
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

function promoteToOfficer(userId) {
    if (confirm('Are you sure you want to promote this user to officer?')) {
        // Make AJAX call to promote the user
        fetch('/users/' + userId + '/promote', {
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
                alert('User has been promoted to officer successfully');
                // Refresh the page to show updated type
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while promoting the user to officer');
        });
    }
}

// Add event listeners after the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for flag user links
    document.querySelectorAll('.flag-user-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            flagUser(userId);
        });
    });
    
    // Add event listeners for promote user links
    document.querySelectorAll('.promote-user-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            promoteToOfficer(userId);
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Codes\Backups\AlertDavao2.0\AdminSide\admin\resources\views/users.blade.php ENDPATH**/ ?>