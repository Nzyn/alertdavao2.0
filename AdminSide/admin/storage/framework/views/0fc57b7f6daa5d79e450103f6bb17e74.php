<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AlertDavao - <?php echo $__env->yieldContent('title'); ?></title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
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
                text-decoration: none;
                cursor: pointer;
                transition: color 0.2s ease;
            }
            
            .sidebar-title:hover {
                color: #3b82f6;
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
            
            /* Top Navigation Bar */
            .top-nav {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .user-menu {
                position: relative;
                display: inline-block;
            }
            
            .user-button {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.5rem 1rem;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            
            .user-button:hover {
                background: #f9fafb;
                border-color: #d1d5db;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            
            .user-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 0.875rem;
            }
            
            .user-info {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }
            
            .user-name {
                font-weight: 600;
                font-size: 0.875rem;
                color: #1f2937;
                line-height: 1.2;
            }
            
            .user-email {
                font-size: 0.75rem;
                color: #6b7280;
                line-height: 1.2;
            }
            
            .dropdown-icon {
                width: 16px;
                height: 16px;
                fill: #6b7280;
                transition: transform 0.2s ease;
            }
            
            .user-menu.active .dropdown-icon {
                transform: rotate(180deg);
            }
            
            .dropdown-menu {
                position: absolute;
                top: calc(100% + 0.5rem);
                right: 0;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                min-width: 200px;
                opacity: 0;
                visibility: hidden;
                transform: translateY(-10px);
                transition: all 0.2s ease;
                z-index: 1000;
            }
            
            .user-menu.active .dropdown-menu {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
            
            .dropdown-item {
                padding: 0.75rem 1rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: #374151;
                text-decoration: none;
                transition: background 0.2s ease;
                font-size: 0.875rem;
                border: none;
                background: none;
                width: 100%;
                text-align: left;
                cursor: pointer;
            }
            
            .dropdown-item:hover {
                background: #f3f4f6;
            }
            
            .dropdown-item:first-child {
                border-radius: 8px 8px 0 0;
            }
            
            .dropdown-item:last-child {
                border-radius: 0 0 8px 8px;
                border-top: 1px solid #e5e7eb;
                color: #dc2626;
            }
            
            .dropdown-item:last-child:hover {
                background: #fee2e2;
            }
            
            .dropdown-icon-small {
                width: 18px;
                height: 18px;
                fill: currentColor;
            }
            
            .content-header {
                margin-bottom: 2rem;
            }
            
            .content-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }
            
            .content-subtitle {
                color: #6b7280;
                font-size: 1rem;
            }
            
            .content-body {
                background: white;
                border-radius: 12px;
                padding: 2rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                min-height: 400px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                gap: 1rem;
            }
            
            .placeholder-text {
                color: #6b7280;
                font-size: 1.125rem;
                text-align: center;
            }
            
            .placeholder-icon {
                width: 64px;
                height: 64px;
                opacity: 0.3;
                fill: #6b7280;
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
                
                .user-info {
                    display: none;
                }
                
                .user-button {
                    padding: 0.5rem;
                }
            }
        </style>
        <?php echo $__env->yieldContent('styles'); ?>
    </head>
    <body>
        <div class="dashboard">
            <!-- Sidebar -->
            <nav class="sidebar">
                <div class="sidebar-header">
                    <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-title">AlertDavao</a>
                </div>
                
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                            <svg class="nav-icon" viewBox="0 0 24 24">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('reports')); ?>" class="nav-link <?php echo e(request()->routeIs('reports') ? 'active' : ''); ?>">
                            <svg class="nav-icon" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                            </svg>
                            Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('users')); ?>" class="nav-link <?php echo e(request()->routeIs('users') ? 'active' : ''); ?>">
                            <svg class="nav-icon" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('messages')); ?>" class="nav-link <?php echo e(request()->routeIs('messages') ? 'active' : ''); ?>">
                            <svg class="nav-icon" viewBox="0 0 24 24">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('statistics')); ?>" class="nav-link <?php echo e(request()->routeIs('statistics') ? 'active' : ''); ?>">
                            <svg class="nav-icon" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <rect x="7" y="7" width="3" height="9"/>
                                <rect x="14" y="7" width="3" height="5"/>
                            </svg>
                            Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('view-map')); ?>" class="nav-link <?php echo e(request()->routeIs('view-map') ? 'active' : ''); ?>">
                            <svg class="nav-icon" viewBox="0 0 24 24">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            View Map
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Main Content -->
            <main class="main-content">
                <!-- Top Navigation Bar -->
                <div class="top-nav">
                    <div class="user-menu" id="userMenu">
                        <button class="user-button" onclick="toggleUserMenu()">
                            <div class="user-avatar">
                                <?php echo e(strtoupper(substr(auth()->user()->firstname, 0, 1))); ?><?php echo e(strtoupper(substr(auth()->user()->lastname, 0, 1))); ?>

                            </div>
                            <div class="user-info">
                                <span class="user-name"><?php echo e(auth()->user()->firstname); ?> <?php echo e(auth()->user()->lastname); ?></span>
                                <span class="user-email"><?php echo e(auth()->user()->email); ?></span>
                            </div>
                            <svg class="dropdown-icon" viewBox="0 0 20 20">
                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item">
                                <svg class="dropdown-icon-small" viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                My Profile
                            </a>
                            <a href="#" class="dropdown-item">
                                <svg class="dropdown-icon-small" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M12 1v6m0 6v6m-8.66-4l5.2-3M16.46 9l5.2 3m-5.2 3l5.2-3M4.54 15l5.2 3M1 12h6m6 0h6"/>
                                </svg>
                                Settings
                            </a>
                            <form action="<?php echo e(route('logout')); ?>" method="POST" style="margin: 0;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">
                                    <svg class="dropdown-icon-small" viewBox="0 0 24 24">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
        
        <?php echo $__env->yieldContent('scripts'); ?>
        
        <script>
            function toggleUserMenu() {
                const userMenu = document.getElementById('userMenu');
                userMenu.classList.toggle('active');
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const userMenu = document.getElementById('userMenu');
                const isClickInside = userMenu.contains(event.target);
                
                if (!isClickInside && userMenu.classList.contains('active')) {
                    userMenu.classList.remove('active');
                }
            });
        </script>
    </body>
</html>
<?php /**PATH D:\Codes\Laravel.ReactNative\AlertDavao2.0\AdminSide\admin\resources\views/layouts/app.blade.php ENDPATH**/ ?>