<?php
// oms_history.php - Order History (Shipped/Delivered)
require_once 'config.php';
date_default_timezone_set('Asia/Kolkata');
checkLogin();

// 1. PERMISSION CHECKS (Same as Orders)
$can_view   = hasPermission('oms_orders_view');
$can_status = hasPermission('oms_orders_status'); 

if (!$can_view && !hasPermission('oms_orders_manage') && !$can_status) { include 'access_denied.php'; exit(); }

// 2. HANDLE ACTIONS (Only Status Update: Delivered)
if ($can_status && isset($_GET['action']) && $_GET['action'] == 'Delivered' && isset($_GET['id'])) {
    $oid = intval($_GET['id']);
    $conn->query("UPDATE oms_orders SET status='Delivered' WHERE id=$oid");
    header("Location: oms_history.php"); exit;
}

// 3. FETCH DATA (SHIPPED OR DELIVERED ONLY)
$sql = "SELECT o.*, c.name as cname, i.name as iname 
        FROM oms_orders o 
        LEFT JOIN oms_customers c ON o.customer_id = c.id 
        LEFT JOIN oms_items i ON o.item_id = i.id 
        WHERE o.status IN ('Shipped', 'Delivered') 
        ORDER BY o.dispatch_date DESC";
$result = $conn->query($sql);

$grouped_data = [];
while($row = $result->fetch_assoc()) {
    $cid = $row['customer_id'];
    if (!isset($grouped_data[$cid])) { $grouped_data[$cid] = ['name' => $row['cname'], 'orders' => []]; }
    $grouped_data[$cid]['orders'][] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Order History (Shipped / Delivered)</h3>
            <a href="oms_orders.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Back to Pending Orders</a>
        </div>

        <div class="card shadow-sm border-secondary">
            <div class="card-header bg-secondary text-white">Archives</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Customer Name</th>
                            <th class="text-center">Total History</th>
                            <th>Last Activity</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($grouped_data)): ?>
                            <?php foreach($grouped_data as $cid => $data): 
                                $order_count = count($data['orders']);
                                $last_date = $data['orders'][0]['dispatch_date'];
                            ?>
                            <tr>
                                <td class="fw-bold fs-5"><?php echo $data['name']; ?></td>
                                <td class="text-center"><span class="badge bg-secondary rounded-pill px-3"><?php echo $order_count; ?></span></td>
                                <td><?php echo date('d-M-y', strtotime($last_date)); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-outline-dark fw-bold" onclick="showCustomerOrders(<?php echo $cid; ?>, '<?php echo htmlspecialchars($data['name']); ?>')">
                                        <i class="bi bi-list"></i> View History
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center p-4">No historical orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="hidden_order_tables" style="display:none;">
        <?php foreach($grouped_data as $cid => $data): ?>
            <div id="table_content_<?php echo $cid; ?>">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr><th>S.No</th><th>Date</th><th>Item</th><th>Qty</th><th>Price</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php $sno=1; foreach($data['orders'] as $o): ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td><?php echo date('d-M-y', strtotime($o['dispatch_date'])); ?></td>
                            <td><?php echo $o['iname']; ?></td>
                            <td class="fw-bold"><?php echo $o['quantity']; ?></td>
                            <td><?php echo number_format($o['price'], 2); ?></td>
                            <td>
                                <?php 
                                $bg = ($o['status']=='Shipped') ? 'info' : 'success';
                                echo "<span class='badge bg-$bg'>{$o['status']}</span>"; 
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-info" title="View Details" onclick="viewDetails(<?php echo $o['id']; ?>, '<?php echo $o['iname']; ?>', <?php echo $o['quantity']; ?>, '<?php echo $o['price']; ?>')"><i class="bi bi-eye"></i></button>

                                    <?php if($can_status && $o['status'] == 'Shipped'): ?>
                                        <a href="?action=Delivered&id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-success" title="Mark Delivered" onclick="return confirm('Mark as Delivered?')"><i class="bi bi-check-lg"></i></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="modal fade" id="listModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">History: <span id="listModalCustName" class="text-warning"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0"><div class="table-responsive" id="listModalBody"></div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white"><h5 class="modal-title">Order Details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p><strong>Order ID:</strong> #<span id="v_id"></span></p>
                    <p><strong>Item:</strong> <span id="v_item"></span></p>
                    <p><strong>Quantity:</strong> <span id="v_qty"></span></p>
                    <p><strong>Price:</strong> <span id="v_price"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showCustomerOrders(cid, cname) {
            document.getElementById('listModalBody').innerHTML = document.getElementById('table_content_' + cid).innerHTML;
            document.getElementById('listModalCustName').innerText = cname;
            new bootstrap.Modal(document.getElementById('listModal')).show();
        }
        function viewDetails(id, item, qty, price) {
            var listModal = bootstrap.Modal.getInstance(document.getElementById('listModal'));
            if(listModal) listModal.hide(); // Hide list modal to show detail modal

            $('#v_id').text(id); $('#v_item').text(item); $('#v_qty').text(qty); $('#v_price').text(price);
            new bootstrap.Modal(document.getElementById('viewModal')).show();
        }
    </script>
</body>
</html>