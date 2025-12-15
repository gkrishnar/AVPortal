<?php
// oms_dispatch_schedule.php - Today's Dispatch with Actions
require_once 'config.php';
date_default_timezone_set('Asia/Kolkata');
checkLogin();

// 1. PERMISSION CHECK
if(!function_exists('hasPermission')) { include 'access_denied.php'; exit; }
if(!hasPermission('oms_dispatch_schedule')) { include 'access_denied.php'; exit; }

$msg = ""; $err = ""; $today = date('Y-m-d');

// 2. HANDLE ACTIONS
// A. Postpone Selected
if (isset($_POST['remove_from_today']) && !empty($_POST['remove_ids'])) {
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $ids = implode(',', array_map('intval', $_POST['remove_ids'])); 
    $conn->query("UPDATE oms_orders SET dispatch_date = '$tomorrow' WHERE id IN ($ids)");
    $msg = "Selected orders moved to Tomorrow.";
}

// B. Mark Single Order as Shipped
if (isset($_GET['action']) && $_GET['action'] == 'Shipped' && isset($_GET['id'])) {
    $oid = intval($_GET['id']);
    $ord = $conn->query("SELECT item_id, quantity FROM oms_orders WHERE id=$oid AND status='Pending'")->fetch_assoc();
    
    if ($ord) {
        $stk = $conn->query("SELECT current_stock FROM oms_items WHERE id=".$ord['item_id'])->fetch_assoc();
        
        if ($stk && $stk['current_stock'] >= $ord['quantity']) {
            // Deduct Stock
            $conn->query("UPDATE oms_items SET current_stock = current_stock - ".$ord['quantity']." WHERE id=".$ord['item_id']);
            // Log Transaction
            $conn->query("INSERT INTO oms_stock_logs (item_id, txn_type, quantity, remarks, user_id) VALUES (".$ord['item_id'].", 'Sale', ".$ord['quantity'].", 'Dispatch Sch #$oid', ".$_SESSION['user_id'].")");
            // Update Status
            $conn->query("UPDATE oms_orders SET status='Shipped' WHERE id=$oid");
            $msg = "Order #$oid marked as Shipped.";
        } else {
            $err = "Error: Insufficient Stock for Order #$oid";
        }
    }
}

// 3. FETCH DATA (TODAY ONLY)
$sql_today = "SELECT o.*, c.name as cname, i.name as iname, i.current_stock 
              FROM oms_orders o 
              LEFT JOIN oms_customers c ON o.customer_id = c.id
              LEFT JOIN oms_items i ON o.item_id = i.id
              WHERE o.status = 'Pending' AND o.dispatch_date = '$today'
              ORDER BY c.name ASC";
$res_today = $conn->query($sql_today);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Today's Dispatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid px-4">
        <h3 class="mb-3">Daily Dispatch Schedule</h3>
        <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
        <?php if($err) echo "<div class='alert alert-danger'>$err</div>"; ?>

        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Scheduled for Today (<?php echo date('d-M-Y'); ?>)</h5>
                <button class="btn btn-sm btn-light text-primary fw-bold" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print List
                </button>
            </div>
            <div class="card-body p-0">
                <form method="post">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">Sel</th>
                                <th>Customer</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Stock Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($res_today->num_rows > 0): ?>
                                <?php while($row = $res_today->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="remove_ids[]" value="<?php echo $row['id']; ?>" class="form-check-input border-secondary">
                                    </td>
                                    <td class="fw-bold"><?php echo $row['cname']; ?></td>
                                    <td><?php echo $row['iname']; ?></td>
                                    <td class="fw-bold fs-5"><?php echo $row['quantity']; ?></td>
                                    <td>
                                        <?php if($row['current_stock'] >= $row['quantity']): ?>
                                            <span class="badge bg-success">In Stock (<?php echo $row['current_stock']; ?>)</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Low Stock (<?php echo $row['current_stock']; ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="viewOrder(
                                                    '<?php echo $row['id']; ?>', 
                                                    '<?php echo htmlspecialchars($row['cname']); ?>', 
                                                    '<?php echo htmlspecialchars($row['iname']); ?>', 
                                                    '<?php echo $row['quantity']; ?>',
                                                    '<?php echo $row['price']; ?>'
                                                )" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <a href="?action=Shipped&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Mark Shipped" 
                                               onclick="return confirm('Confirm Dispatch? Stock will be deducted.')">
                                                <i class="bi bi-box-seam"></i> Ship
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center p-4 text-muted">No orders scheduled for today.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <?php if($res_today->num_rows > 0): ?>
                    <div class="p-3 bg-light border-top">
                        <button type="submit" name="remove_from_today" class="btn btn-outline-danger btn-sm" onclick="return confirm('Move selected orders to Tomorrow?')">
                            <i class="bi bi-arrow-right-circle"></i> Postpone Selected to Tomorrow
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Order Details #<span id="v_id"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr><th>Customer</th><td id="v_cust"></td></tr>
                        <tr><th>Item</th><td id="v_item"></td></tr>
                        <tr><th>Quantity</th><td id="v_qty" class="fw-bold"></td></tr>
                        <tr><th>Price</th><td id="v_price"></td></tr>
                        <tr><th>Status</th><td><span class="badge bg-warning">Pending Dispatch</span></td></tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewOrder(id, cust, item, qty, price) {
            document.getElementById('v_id').innerText = id;
            document.getElementById('v_cust').innerText = cust;
            document.getElementById('v_item').innerText = item;
            document.getElementById('v_qty').innerText = qty;
            document.getElementById('v_price').innerText = price;
            
            var modal = new bootstrap.Modal(document.getElementById('viewModal'));
            modal.show();
        }
    </script>
</body>
</html>