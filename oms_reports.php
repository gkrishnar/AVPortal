<?php
// 1. Config & Settings
require_once 'config.php';
ini_set('display_errors', 1); error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');
checkLogin();

if(function_exists('requirePermission')) { requirePermission('oms_reports_view'); }

$today = date('Y-m-d');

// --- 1. CALCULATE STATISTICS (THE NUMBERS FOR CARDS) ---


// i. Pending Orders Count
$pending_cnt = $conn->query("SELECT COUNT(*) as c FROM oms_orders WHERE status='Pending'")->fetch_assoc()['c'];

// ii. Pending Buckets (Total Quantity of Pending Items)
$pending_qty = $conn->query("SELECT SUM(quantity) as q FROM oms_orders WHERE status='Pending'")->fetch_assoc()['q'];

// iii. Today Stock Movement (Count of logs today)
$move_cnt = $conn->query("SELECT COUNT(*) as c FROM oms_stock_logs WHERE DATE(log_date) = '$today'")->fetch_assoc()['c'];

// iv. Current Available Stock (Sum of all items stock)
$curr_stock = $conn->query("SELECT SUM(current_stock) as s FROM oms_items")->fetch_assoc()['s'];

// v. Today Dispatches (Qty)
$today_disp = $conn->query("SELECT SUM(quantity) as q FROM oms_orders WHERE status IN ('Shipped', 'Delivered') AND dispatch_date = '$today'")->fetch_assoc()['q'];

// vi. Weekly Dispatch (Qty)
$week_disp = $conn->query("SELECT SUM(quantity) as q FROM oms_orders WHERE status IN ('Shipped', 'Delivered') AND YEARWEEK(dispatch_date, 1) = YEARWEEK(CURDATE(), 1)")->fetch_assoc()['q'];

// vii. Monthly Dispatch (Qty)
$month_disp = $conn->query("SELECT SUM(quantity) as q FROM oms_orders WHERE status IN ('Shipped', 'Delivered') AND MONTH(dispatch_date) = MONTH(CURDATE()) AND YEAR(dispatch_date) = YEAR(CURDATE())")->fetch_assoc()['q'];

// viii. Total Inventory Value
$stock_val = $conn->query("SELECT SUM(current_stock * unit_price) as val FROM oms_items")->fetch_assoc()['val'];


// --- 2. FETCH DATA FOR MODALS (THE DETAILS) ---

// Modal 1: Pending Orders List
$res_pending = $conn->query("SELECT o.*, c.name as cname, i.name as iname FROM oms_orders o JOIN oms_customers c ON o.customer_id=c.id JOIN oms_items i ON o.item_id=i.id WHERE o.status='Pending'");

// Modal 2: Pending Buckets (Grouped by Item)
$res_buckets = $conn->query("SELECT i.name, SUM(o.quantity) as total_qty FROM oms_orders o JOIN oms_items i ON o.item_id=i.id WHERE o.status='Pending' GROUP BY o.item_id");

// Modal 3: Today Movement Logs
$res_logs = $conn->query("SELECT l.*, i.name FROM oms_stock_logs l JOIN oms_items i ON l.item_id=i.id WHERE DATE(l.log_date) = '$today' ORDER BY l.id DESC");

// Modal 4: Current Stock List
$res_stock = $conn->query("SELECT * FROM oms_items ORDER BY name");

// Modal 5, 6, 7: Dispatches (Using a helper function query for cleanliness)
function getDispatchQuery($conn, $period) {
    $date_cond = "";
    if($period == 'today') $date_cond = "AND o.dispatch_date = CURDATE()";
    if($period == 'week')  $date_cond = "AND YEARWEEK(o.dispatch_date, 1) = YEARWEEK(CURDATE(), 1)";
    if($period == 'month') $date_cond = "AND MONTH(o.dispatch_date) = MONTH(CURDATE()) AND YEAR(o.dispatch_date) = YEAR(CURDATE())";
    
    return $conn->query("SELECT o.*, c.name as cname, i.name as iname FROM oms_orders o JOIN oms_customers c ON o.customer_id=c.id JOIN oms_items i ON o.item_id=i.id WHERE o.status IN ('Shipped','Delivered') $date_cond ORDER BY o.dispatch_date DESC");
}

$res_disp_today = getDispatchQuery($conn, 'today');
$res_disp_week  = getDispatchQuery($conn, 'week');
$res_disp_month = getDispatchQuery($conn, 'month');

?>
<!DOCTYPE html>
<html>
<head>
    <title>OMS Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .report-card { cursor: pointer; transition: transform 0.2s; border: none; }
        .report-card:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .card-icon { font-size: 2rem; opacity: 0.8; }
        .bg-gradient-primary { background: linear-gradient(45deg, #007bff, #0056b3); }
        .bg-gradient-danger { background: linear-gradient(45deg, #dc3545, #a71d2a); }
        .bg-gradient-success { background: linear-gradient(45deg, #28a745, #1e7e34); }
        .bg-gradient-warning { background: linear-gradient(45deg, #ffc107, #d39e00); }
        .bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #117a8b); }
        .bg-gradient-dark { background: linear-gradient(45deg, #343a40, #23272b); }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container py-4">
        <h3 class="mb-4">Order & Inventory Dashboard</h3>
        
        <div class="row g-4">
            
            <div class="col-md-4">
                <a href="oms_stock_report.php" class="text-decoration-none">
                    <div class="card text-white bg-secondary report-card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase">Stock Valuation</h6>
                        <h2 class="mb-0 fw-bold"><?php echo number_format($stock_val); ?></h2>
                        <small class="text-light">Click for Detailed Report</small>
                    </div>
                    <i class="bi bi-cash-stack card-icon"></i>
                </div>
            </div>
            </a>
        </div>
            
            <div class="col-md-3">
                <div class="card text-white bg-gradient-danger report-card h-100" data-bs-toggle="modal" data-bs-target="#modalPendingOrders">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Pending Orders</h6>
                            <h2 class="mb-0 fw-bold"><?php echo intval($pending_cnt); ?></h2>
                        </div>
                        <i class="bi bi-exclamation-circle-fill card-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-gradient-warning report-card h-100" data-bs-toggle="modal" data-bs-target="#modalPendingBuckets">
                    <div class="card-body d-flex justify-content-between align-items-center text-dark">
                        <div>
                            <h6 class="card-title text-uppercase">Pending Buckets</h6>
                            <h2 class="mb-0 fw-bold"><?php echo intval($pending_qty); ?> <small class="fs-6">Qty</small></h2>
                        </div>
                        <i class="bi bi-bucket-fill card-icon text-dark"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-gradient-info report-card h-100" data-bs-toggle="modal" data-bs-target="#modalTodayMovement">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Stock Movement</h6>
                            <h2 class="mb-0 fw-bold"><?php echo intval($move_cnt); ?> <small class="fs-6">Logs</small></h2>
                            <small>Today</small>
                        </div>
                        <i class="bi bi-arrow-left-right card-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-gradient-primary report-card h-100" data-bs-toggle="modal" data-bs-target="#modalCurrentStock">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Available Stock</h6>
                            <h2 class="mb-0 fw-bold"><?php echo intval($curr_stock); ?> <small class="fs-6">Total</small></h2>
                        </div>
                        <i class="bi bi-box-seam-fill card-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-gradient-success report-card h-100" data-bs-toggle="modal" data-bs-target="#modalTodayDisp">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Today Dispatches</h6>
                            <h2 class="mb-0 fw-bold"><?php echo intval($today_disp); ?> <small class="fs-6">Qty</small></h2>
                        </div>
                        <i class="bi bi-truck card-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-gradient-dark report-card h-100" data-bs-toggle="modal" data-bs-target="#modalWeekDisp">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Weekly Dispatches</h6>
                            <h2 class="mb-0 fw-bold"><?php echo intval($week_disp); ?> <small class="fs-6">Qty</small></h2>
                        </div>
                        <i class="bi bi-calendar-week card-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-secondary report-card h-100" data-bs-toggle="modal" data-bs-target="#modalMonthDisp">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Monthly Dispatches</h6>
                            <h2 class="mb-0 fw-bold"><?php echo intval($month_disp); ?> <small class="fs-6">Qty</small></h2>
                        </div>
                        <i class="bi bi-calendar-month card-icon"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalPendingOrders" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white"><h5 class="modal-title">Pending Orders</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-0">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>ID</th><th>Customer</th><th>Item</th><th>Qty</th><th>Date</th></tr></thead>
                        <tbody><?php while($r=$res_pending->fetch_assoc()): ?><tr><td><?php echo $r['id']; ?></td><td><?php echo $r['cname']; ?></td><td><?php echo $r['iname']; ?></td><td><?php echo $r['quantity']; ?></td><td><?php echo $r['dispatch_date']; ?></td></tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPendingBuckets" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark"><h5 class="modal-title">Pending Buckets (By Item)</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead><tr><th>Item Name</th><th>Total Pending Qty</th></tr></thead>
                        <tbody><?php while($r=$res_buckets->fetch_assoc()): ?><tr><td><?php echo $r['name']; ?></td><td class="fw-bold"><?php echo $r['total_qty']; ?></td></tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTodayMovement" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white"><h5 class="modal-title">Today's Stock Logs</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-0">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Time</th><th>Item</th><th>Type</th><th>Qty</th><th>Remarks</th></tr></thead>
                        <tbody><?php while($r=$res_logs->fetch_assoc()): ?><tr><td><?php echo date('H:i', strtotime($r['log_date'])); ?></td><td><?php echo $r['name']; ?></td><td><?php echo $r['txn_type']; ?></td><td><?php echo $r['quantity']; ?></td><td><?php echo $r['remarks']; ?></td></tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCurrentStock" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white"><h5 class="modal-title">Current Inventory</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-0">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Item</th><th>Code</th><th>Current Stock</th></tr></thead>
                        <tbody><?php while($r=$res_stock->fetch_assoc()): ?><tr class="<?php echo ($r['current_stock']<10)?'table-danger':''; ?>"><td><?php echo $r['name']; ?></td><td><?php echo $r['short_code']; ?></td><td class="fw-bold"><?php echo $r['current_stock']; ?></td></tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php function renderDispatchModal($id, $title, $data, $bg) { ?>
    <div class="modal fade" id="<?php echo $id; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header <?php echo $bg; ?> text-white"><h5 class="modal-title"><?php echo $title; ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-0">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>ID</th><th>Customer</th><th>Item</th><th>Qty</th><th>Disp. Date</th></tr></thead>
                        <tbody><?php 
                        if($data->num_rows > 0) {
                            while($r=$data->fetch_assoc()): ?><tr><td><?php echo $r['id']; ?></td><td><?php echo $r['cname']; ?></td><td><?php echo $r['iname']; ?></td><td><?php echo $r['quantity']; ?></td><td><?php echo $r['dispatch_date']; ?></td></tr><?php endwhile; 
                        } else { echo "<tr><td colspan='5' class='text-center p-3'>No records found.</td></tr>"; }
                        ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php renderDispatchModal('modalTodayDisp', 'Dispatched Today', $res_disp_today, 'bg-success'); ?>

    <?php renderDispatchModal('modalWeekDisp', 'Dispatched This Week', $res_disp_week, 'bg-dark'); ?>

    <?php renderDispatchModal('modalMonthDisp', 'Dispatched This Month', $res_disp_month, 'bg-secondary'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>