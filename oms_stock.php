<?php
require_once 'config.php';
checkLogin();

// Optional Permission Check
if(function_exists('requirePermission')) { requirePermission('oms_stock_manage'); }
$msg = "";
$msg_type = "";

if (isset($_POST['update_stock'])) {
    $item_id = $_POST['item_id'];
    $type = $_POST['txn_type']; // Add or Dispatch
    $qty = intval($_POST['quantity']);
    $remarks = $_POST['remarks'];
    $uid = $_SESSION['user_id'];

    if ($item_id && $qty > 0) {
        // Update Master Stock
        if ($type == 'Add') {
            $conn->query("UPDATE oms_items SET current_stock = current_stock + $qty WHERE id=$item_id");
        } else {
            // Optional: Check if stock is sufficient before dispatch
            $check = $conn->query("SELECT current_stock FROM oms_items WHERE id=$item_id")->fetch_assoc();
            if ($check['current_stock'] < $qty) {
                $msg = "Error: Insufficient stock!";
                $msg_type = "danger";
            } else {
                $conn->query("UPDATE oms_items SET current_stock = current_stock - $qty WHERE id=$item_id");
            }
        }

        if (empty($msg_type)) { // Only proceed if no error above
            // Insert Log
            $stmt = $conn->prepare("INSERT INTO oms_stock_logs (item_id, txn_type, quantity, remarks, user_id) VALUES (?,?,?,?,?)");
            $stmt->bind_param("isisi", $item_id, $type, $qty, $remarks, $uid);
            
            if ($stmt->execute()) {
                $msg = "Stock Updated Successfully";
                $msg_type = "success";
            } else {
                $msg = "Database Error: " . $stmt->error;
                $msg_type = "danger";
            }
        }
    } else {
        $msg = "Invalid Quantity or Item selected.";
        $msg_type = "danger";
    }
}

$items = $conn->query("SELECT * FROM oms_items ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <style>
        /* Fix for Select2 height in Bootstrap 5 */
        .select2-container .select2-selection--single {
            height: 38px !important;
        }
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #dee2e6;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Manual Stock Entry</h4>
            </div>
            <div class="card-body">
                <?php if(!empty($msg)) echo "<div class='alert alert-$msg_type'>$msg</div>"; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Item</label>
                        <select name="item_id" id="item_id" class="form-select select2" style="width:100%;" required>
    <option value="">-- Select Item --</option>
    <?php 
    // Fetch Items Grouped by Category
    $q_items = $conn->query("SELECT i.*, c.name as cat_name 
                             FROM oms_items i 
                             LEFT JOIN oms_categories c ON i.category_id = c.id 
                             ORDER BY c.name ASC, i.name ASC");
    
    $current_cat = "";
    if($q_items) {
        while($i = $q_items->fetch_assoc()) {
            $cat = $i['cat_name'] ? $i['cat_name'] : 'Uncategorized';
            
            // If category changes, close old group and open new one
            if ($cat != $current_cat) {
                if ($current_cat != "") echo "</optgroup>";
                echo "<optgroup label='" . htmlspecialchars($cat) . "'>";
                $current_cat = $cat;
            }
            
            echo "<option value='{$i['id']}'>{$i['name']} (Stock: {$i['current_stock']})</option>";
        }
        if ($current_cat != "") echo "</optgroup>"; // Close last group
    }
    ?>
</select>



                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Action</label>
                            <select name="txn_type" class="form-select">
                                <option value="Add">Add Stock (In)</option>
                                <option value="Dispatch">Dispatch/Damage (Out)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required min="1">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" placeholder="e.g. New Purchase / Broken / Adjustment">
                    </div>
                    
                    <button type="submit" name="update_stock" class="btn btn-dark w-100 py-2">Update Stock</button>
                </form>
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
                placeholder: "Search for an item..."
            });
        });
    </script>
</body>
</html>