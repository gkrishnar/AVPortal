<?php
// oms_orders_report.php - Detailed Order Report with Filters
require_once 'config.php';
ini_set('display_errors', 1); error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');
checkLogin();

if(function_exists('requirePermission')) { requirePermission('oms_reports_view'); }


// --- 1. FILTERS ---
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-t');
$cust_filter = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// --- 2. QUERY BUILDER ---
$sql = "SELECT o.*, c.name as cname, i.name as iname, cat.name as cat_name 
        FROM oms_orders o 
        LEFT JOIN oms_customers c ON o.customer_id = c.id
        LEFT JOIN oms_items i ON o.item_id = i.id
        LEFT JOIN oms_categories cat ON i.category_id = cat.id 
        WHERE o.dispatch_date BETWEEN '$from_date' AND '$to_date'";

if ($cust_filter > 0) {
    $sql .= " AND o.customer_id = $cust_filter";
}
if (!empty($status_filter)) {
    $sql .= " AND o.status = '$status_filter'";
}

$sql .= " ORDER BY o.dispatch_date DESC";
$result = $conn->query($sql);

// Fetch Customers for Filter
$cust_list = $conn->query("SELECT id, name FROM oms_customers ORDER BY name");

// Init Totals
$total_qty = 0;
$total_val = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order List Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container .select2-selection--single { height: 38px !important; }
        .select2-container--bootstrap-5 .select2-selection { border-color: #dee2e6; }
        .filter-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid px-4">
        <h3>Detailed Order Report</h3>
        
        <div class="filter-box mb-4 shadow-sm">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Customer</label>
                    <select name="customer_id" class="form-select select2">
                        <option value="">All Customers</option>
                        <?php while($c = $cust_list->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($cust_filter == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo $c['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo ($status_filter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Shipped" <?php echo ($status_filter == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                        <option value="Delivered" <?php echo ($status_filter == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="oms_orders_report.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Order ID</th>
                                <th>Dispatch Date</th>
                                <th>Customer</th>
                                <th>Category</th>
                                <th>Item Name</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): 
                                    $amount = $row['quantity'] * $row['price'];
                                    $total_qty += $row['quantity'];
                                    $total_val += $amount;
                                ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($row['dispatch_date'])); ?></td>
                                    <td><?php echo $row['cname']; ?></td>
                                    <td><?php echo $row['cat_name'] ?: '-'; ?></td>
                                    <td><?php echo $row['iname']; ?></td>
                                    <td class="text-end fw-bold"><?php echo $row['quantity']; ?></td>
                                    <td class="text-end"><?php echo number_format($row['price'], 2); ?></td>
                                    <td class="text-end"><?php echo number_format($amount, 2); ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $bg = 'warning';
                                        if($row['status']=='Shipped') $bg = 'info';
                                        if($row['status']=='Delivered') $bg = 'success';
                                        ?>
                                        <span class="badge bg-<?php echo $bg; ?>"><?php echo $row['status']; ?></span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <tr class="table-secondary fw-bold">
                                    <td colspan="4" class="text-end">TOTALS:</td>
                                    <td class="text-end"><?php echo number_format($total_qty); ?></td>
                                    <td></td>
                                    <td class="text-end"><?php echo number_format($total_val, 2); ?></td>
                                    <td></td>
                                </tr>

                            <?php else: ?>
                                <tr><td colspan="8" class="text-center p-4">No records found matching filters.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "Select Customer"
            });
        });
    </script>
</body>
</html>