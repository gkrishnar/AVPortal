<?php
// oms_orders.php - Pending Orders Only
require_once 'config.php';
date_default_timezone_set('Asia/Kolkata');
checkLogin();

// 1. PERMISSION CHECKS
$can_view   = hasPermission('oms_orders_view');
$can_manage = hasPermission('oms_orders_manage'); 
$can_status = hasPermission('oms_orders_status'); 

if (!$can_view && !$can_manage && !$can_status) { include 'access_denied.php'; exit(); }

$msg = ""; $err = "";

// 2. HANDLE ACTIONS
if ($can_manage) {
    // Save Order Logic
    if (isset($_POST['save_order'])) {
        $cust = $_POST['customer_id'];
        $item = $_POST['item_id'];
        $qty = $_POST['quantity'];
        $price = $_POST['price'];
        $date = $_POST['dispatch_date'];
        $order_id = intval($_POST['order_id']);

        if ($order_id > 0) {
            $stmt = $conn->prepare("UPDATE oms_orders SET customer_id=?, item_id=?, quantity=?, price=?, dispatch_date=? WHERE id=?");
            $stmt->bind_param("iiidsi", $cust, $item, $qty, $price, $date, $order_id);
            $stmt->execute();
            $msg = "Order Updated";
        } else {
            $stmt = $conn->prepare("INSERT INTO oms_orders (customer_id, item_id, quantity, price, dispatch_date) VALUES (?,?,?,?,?)");
            $stmt->bind_param("iiids", $cust, $item, $qty, $price, $date);
            $stmt->execute();
            $msg = "Order Created";
        }
    }
    // Delete Logic
    if (isset($_GET['delete'])) {
        $did = intval($_GET['delete']);
        $conn->query("DELETE FROM oms_orders WHERE id=$did AND status='Pending'");
        header("Location: oms_orders.php?msg=Deleted"); exit;
    }
}

if ($can_status) {
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $oid = intval($_GET['id']);
        $action = $_GET['action']; 
        
        if ($action == 'Shipped') {
            $ord = $conn->query("SELECT item_id, quantity FROM oms_orders WHERE id=$oid")->fetch_assoc();
            $stk = $conn->query("SELECT current_stock FROM oms_items WHERE id=".$ord['item_id'])->fetch_assoc();
            if ($stk && $stk['current_stock'] >= $ord['quantity']) {
                $conn->query("UPDATE oms_items SET current_stock = current_stock - ".$ord['quantity']." WHERE id=".$ord['item_id']);
                $conn->query("INSERT INTO oms_stock_logs (item_id, txn_type, quantity, remarks, user_id) VALUES (".$ord['item_id'].", 'Sale', ".$ord['quantity'].", 'Order #$oid', ".$_SESSION['user_id'].")");
                $conn->query("UPDATE oms_orders SET status='Shipped' WHERE id=$oid");
            } else { $err = "Insufficient Stock!"; }
        }
        header("Location: oms_orders.php"); exit;
    }
}

// 3. FETCH DATA (PENDING ONLY)
$sql = "SELECT o.*, c.name as cname, i.name as iname, i.current_stock 
        FROM oms_orders o 
        LEFT JOIN oms_customers c ON o.customer_id = c.id 
        LEFT JOIN oms_items i ON o.item_id = i.id 
        WHERE o.status = 'Pending' 
        ORDER BY c.name ASC, o.dispatch_date ASC";
$result = $conn->query($sql);

$grouped_data = [];
while($row = $result->fetch_assoc()) {
    $cid = $row['customer_id'];
    if (!isset($grouped_data[$cid])) { $grouped_data[$cid] = ['name' => $row['cname'], 'orders' => []]; }
    $grouped_data[$cid]['orders'][] = $row;
}

$customers = $conn->query("SELECT * FROM oms_customers ORDER BY name ASC");
$q_items = $conn->query("SELECT i.*, c.name as cat_name FROM oms_items i LEFT JOIN oms_categories c ON i.category_id = c.id ORDER BY c.name ASC, i.name ASC");
$items_data = [];
while($r=$q_items->fetch_assoc()) { $items_data[] = $r; }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="d-inline-block">Pending Orders</h3>
                <a href="oms_history.php" class="btn btn-outline-secondary ms-3"><i class="bi bi-clock-history"></i> View History (Shipped/Delivered)</a>
            </div>
            
            <?php if($can_manage): ?>
                <button class="btn btn-primary px-4 fw-bold" onclick="openOrderModal('add')"><i class="bi bi-plus-lg"></i> Add New Order</button>
            <?php endif; ?>
        </div>

        <?php if($msg || isset($_GET['msg'])) echo "<div class='alert alert-success'>".($msg ?: $_GET['msg'])."</div>"; ?>
        <?php if($err) echo "<div class='alert alert-danger'>$err</div>"; ?>

        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning text-dark fw-bold">Pending Actions</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Customer Name</th>
                            <th class="text-center">Pending Orders</th>
                            <th>Dispatch Due Date</th>
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
                                <td class="text-center"><span class="badge bg-danger rounded-pill px-3"><?php echo $order_count; ?></span></td>
                                <td><?php echo date('d-M-y', strtotime($last_date)); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-outline-primary fw-bold" onclick="showCustomerOrders(<?php echo $cid; ?>, '<?php echo htmlspecialchars($data['name']); ?>')">
                                        <i class="bi bi-list-check"></i> Process Orders
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center p-4">No pending orders. All caught up!</td></tr>
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
                        <tr><th>S.No</th><th>Date</th><th>Item</th><th>Qty</th><th>Price</th><th>Action</th></tr>
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
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-info" onclick="openOrderModal('view', <?php echo $o['id']; ?>, <?php echo $o['customer_id']; ?>, <?php echo $o['item_id']; ?>, <?php echo $o['quantity']; ?>, '<?php echo $o['price']; ?>', '<?php echo $o['dispatch_date']; ?>')"><i class="bi bi-eye"></i></button>
                                    
                                    <?php if($can_manage): ?>
                                        <button class="btn btn-sm btn-outline-warning" onclick="openOrderModal('edit', <?php echo $o['id']; ?>, <?php echo $o['customer_id']; ?>, <?php echo $o['item_id']; ?>, <?php echo $o['quantity']; ?>, '<?php echo $o['price']; ?>', '<?php echo $o['dispatch_date']; ?>')"><i class="bi bi-pencil"></i></button>
                                        <a href="?delete=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
                                    <?php endif; ?>

                                    <?php if($can_status): ?>
                                        <a href="?action=Shipped&id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-primary" title="Mark Shipped" onclick="return confirm('Confirm Dispatch?')"><i class="bi bi-box-seam"></i></a>
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
                    <h5 class="modal-title">Orders: <span id="listModalCustName" class="text-warning"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0"><div class="table-responsive" id="listModalBody"></div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white"><h5 class="modal-title" id="modalTitle">Order</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" name="order_id" id="order_id" value="0">
                        <fieldset id="modalFields">
                            <div class="mb-3"><label>Customer</label><select name="customer_id" id="customer_id" class="form-select select2" style="width:100%;" required><option value="">Select</option><?php if($customers){$customers->data_seek(0); while($c=$customers->fetch_assoc()){ echo "<option value='{$c['id']}'>{$c['name']}</option>"; }} ?></select></div>
                            <div class="mb-3"><label>Item</label>
                                <select name="item_id" id="item_id" class="form-select select2" style="width:100%;" required>
                                    <option value="">-- Select --</option>
                                    <?php 
                                    $current_cat = "";
                                    foreach($items_data as $i) {
                                        $cat = $i['cat_name'] ?: 'Uncategorized';
                                        if ($cat != $current_cat) { if ($current_cat != "") echo "</optgroup>"; echo "<optgroup label='" . htmlspecialchars($cat) . "'>"; $current_cat = $cat; }
                                        echo "<option value='{$i['id']}'>{$i['name']} (Stock: {$i['current_stock']})</option>";
                                    }
                                    if ($current_cat != "") echo "</optgroup>";
                                    ?>
                                </select>
                            </div>
                            <div class="row"><div class="col-6 mb-3"><label>Qty</label><input type="number" name="quantity" id="quantity" class="form-control" required></div><div class="col-6 mb-3"><label>Price</label><input type="number" step="0.01" name="price" id="price" class="form-control" required></div></div>
                            <div class="mb-3"><label>Dispatch Date</label><input type="date" name="dispatch_date" id="dispatch_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                        </fieldset>
                        <?php if($can_manage): ?><button type="submit" name="save_order" id="saveBtn" class="btn btn-success w-100">Save</button><?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() { $('#orderModal .select2').select2({ theme: "bootstrap-5", dropdownParent: $('#orderModal') }); });
        function showCustomerOrders(cid, cname) {
            document.getElementById('listModalBody').innerHTML = document.getElementById('table_content_' + cid).innerHTML;
            document.getElementById('listModalCustName').innerText = cname;
            new bootstrap.Modal(document.getElementById('listModal')).show();
        }
        function openOrderModal(mode, id=0, cust='', item='', qty='', price='', date='') {
            var listModal = bootstrap.Modal.getInstance(document.getElementById('listModal'));
            if(listModal) listModal.hide();

            var isView = (mode === 'view');
            $('#order_id').val(id); $('#customer_id').val(cust).trigger('change'); $('#item_id').val(item).trigger('change'); $('#quantity').val(qty); $('#price').val(price); $('#dispatch_date').val(date || '<?php echo date("Y-m-d"); ?>');
            $('#modalFields').prop('disabled', isView); $('#saveBtn').toggle(!isView);
            $('#modalTitle').text(isView ? 'Details #'+id : (mode==='edit' ? 'Edit Order #'+id : 'New Order'));
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        }
    </script>
</body>
</html>