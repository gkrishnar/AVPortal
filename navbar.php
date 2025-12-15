<?php
// navbar.php - Reorganized by Modules
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Helper function to prevent errors if permission system isn't loaded
if (!function_exists('hasPermission')) {
    function hasPermission($perm) { return true; } // Default allow if system fails (Safety)
}

// Get User Name
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-grid-3x3-gap-fill"></i> MyPortal
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">

                <?php if(hasPermission('attendance_mark') || hasPermission('attendance_view')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Attendance</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="attendance.php"><i class="bi bi-clock"></i> Mark/View Attendance</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Gatepass</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php"><i class="bi bi-card-list"></i> Dashboard & Passes</a></li>
                        <?php if(hasPermission('create_gatepass')): ?>
                            <li><a class="dropdown-item" href="create_pass.php"><i class="bi bi-plus-circle"></i> Create New Pass</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if(hasPermission('oms_orders_view') || hasPermission('oms_orders_manage') || hasPermission('oms_orders_status') || hasPermission('oms_dispatch_schedule')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Orders</a>
                    <ul class="dropdown-menu">
                        <?php if(hasPermission('oms_orders_view') || hasPermission('oms_orders_manage') || hasPermission('oms_orders_status')): ?>
                            <li><a class="dropdown-item" href="oms_orders.php"><i class="bi bi-cart"></i> Orders List</a></li>
                        <?php endif; ?>

                        <?php if(hasPermission('oms_dispatch_schedule')): ?>
                            <li><a class="dropdown-item" href="oms_dispatch_schedule.php"><i class="bi bi-truck"></i> Daily Dispatch Schedule</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(hasPermission('oms_stock_manage')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Inventory</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="oms_stock.php"><i class="bi bi-box-seam"></i> Manual Stock In/Out</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(hasPermission('gatepass_report') || hasPermission('attendance_report') || hasPermission('oms_report') || hasPermission('oms_stock_report_view')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Reports</a>
                    <ul class="dropdown-menu">
                        
                        <?php if(hasPermission('gatepass_report')): ?>
                            <li><h6 class="dropdown-header">Gatepass</h6></li>
                            <li><a class="dropdown-item" href="reports.php">Gatepass Report</a></li>
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        
                        <?php if(hasPermission('attendance_report')): ?>
                            <li><h6 class="dropdown-header">Attendance</h6></li>
                            <li><a class="dropdown-item" href="attendance_report.php">Attendance Report</a></li>
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>

                        <?php if(hasPermission('oms_report') || hasPermission('oms_stock_report_view')): ?>
                            <li><h6 class="dropdown-header">Inventory & Orders</h6></li>
                            <?php if(hasPermission('oms_report')): ?>
                                <li><a class="dropdown-item" href="oms_reports.php">Inventory Dashboard</a></li>
                                <li><a class="dropdown-item" href="oms_orders_report.php">Order History Report</a></li>
                            <?php endif; ?>
                            <?php if(hasPermission('oms_stock_report_view')): ?>
                                <li><a class="dropdown-item" href="oms_stock_report.php">Current Stock Report</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(hasPermission('manage_users') || hasPermission('manage_masters') || hasPermission('manage_oms_cust') || hasPermission('manage_oms_item')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Masters & Admin</a>
                    <ul class="dropdown-menu">
                        <?php if(hasPermission('manage_masters')): ?>
                            <li><a class="dropdown-item" href="masters.php">Gatepass Masters</a></li>
                        <?php endif; ?>

                        <?php if(hasPermission('manage_oms_cust') || hasPermission('manage_oms_item')): ?>
                            <li><a class="dropdown-item" href="oms_masters.php">Inventory Masters (Items/Cust)</a></li>
                        <?php endif; ?>
                        
                        <?php if(hasPermission('manage_users')): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="users.php"><i class="bi bi-people-fill"></i> System Users</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user_name); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>