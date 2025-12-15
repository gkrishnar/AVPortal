<?php
// oms_masters.php - Complete Version with Customers, Items & Categories
require_once 'config.php';
ini_set('display_errors', 1); error_reporting(E_ALL);
checkLogin();

// 1. PERMISSION CHECKS
$can_cust = hasPermission('manage_oms_cust');
$can_item = hasPermission('manage_oms_item'); // Also controls Categories

// If user has NEITHER, block access
if (!$can_cust && !$can_item) {
    include 'access_denied.php';
    exit();
}

// 2. TABS LOGIC
// Determine which tab to show first based on permissions
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : ($can_cust ? 'customers' : 'items');

// 3. INIT VARIABLES
$c_id=0; $c_name=""; $c_mobile=""; $c_wa=""; $c_state=""; $c_addr=""; $edit_mode_c=false;
$i_id=0; $i_name=""; $i_code=""; $i_price=""; $i_cat=""; $edit_mode_i=false;
$cat_id=0; $cat_name=""; $edit_mode_cat=false;
$msg = ""; $err = "";

// 4. ACTIONS: DELETE
if (isset($_GET['del_cat']) && $can_item) {
    $id = intval($_GET['del_cat']);
    // Unlink items from this category before deleting
    $conn->query("UPDATE oms_items SET category_id=NULL WHERE category_id=$id"); 
    $conn->query("DELETE FROM oms_categories WHERE id=$id");
    header("Location: oms_masters.php?tab=categories&msg=Category Deleted"); exit;
}
if (isset($_GET['del_item']) && $can_item) {
    $id = intval($_GET['del_item']);
    // Check if item is used in orders
    $check = $conn->query("SELECT id FROM oms_orders WHERE item_id=$id");
    if($check->num_rows > 0) {
        $err = "Cannot delete: Item is used in orders.";
    } else {
        $conn->query("DELETE FROM oms_items WHERE id=$id");
        header("Location: oms_masters.php?tab=items&msg=Item Deleted"); exit;
    }
}
if (isset($_GET['del_cust']) && $can_cust) {
    $id = intval($_GET['del_cust']);
    // Check if customer has orders
    $check = $conn->query("SELECT id FROM oms_orders WHERE customer_id=$id");
    if($check->num_rows > 0) {
        $err = "Cannot delete: Customer has existing orders.";
    } else {
        $conn->query("DELETE FROM oms_customers WHERE id=$id");
        header("Location: oms_masters.php?tab=customers&msg=Customer Deleted"); exit;
    }
}

// 5. ACTIONS: EDIT FETCH
if (isset($_GET['edit_cat']) && $can_item) {
    $active_tab = 'categories'; $id = intval($_GET['edit_cat']);
    $row = $conn->query("SELECT * FROM oms_categories WHERE id=$id")->fetch_assoc();
    if($row) { $edit_mode_cat=true; $cat_id=$row['id']; $cat_name=$row['name']; }
}
if (isset($_GET['edit_item']) && $can_item) {
    $active_tab = 'items'; $id = intval($_GET['edit_item']);
    $row = $conn->query("SELECT * FROM oms_items WHERE id=$id")->fetch_assoc();
    if($row) { $edit_mode_i=true; $i_id=$row['id']; $i_name=$row['name']; $i_code=$row['short_code']; $i_price=$row['unit_price']; $i_cat=$row['category_id']; }
}
if (isset($_GET['edit_cust']) && $can_cust) {
    $active_tab = 'customers'; $id = intval($_GET['edit_cust']);
    $row = $conn->query("SELECT * FROM oms_customers WHERE id=$id")->fetch_assoc();
    if($row) { $edit_mode_c=true; $c_id=$row['id']; $c_name=$row['name']; $c_mobile=$row['mobile']; $c_wa=$row['whatsapp']; $c_state=$row['state']; $c_addr=$row['address']; }
}

// 6. ACTIONS: SAVE
if (isset($_POST['save_cat']) && $can_item) {
    $name = $_POST['cat_name']; $cid = intval($_POST['cat_id']);
    if ($cid > 0) $conn->query("UPDATE oms_categories SET name='$name' WHERE id=$cid");
    else $conn->query("INSERT INTO oms_categories (name) VALUES ('$name')");
    header("Location: oms_masters.php?tab=categories&msg=Category Saved"); exit;
}
if (isset($_POST['save_item']) && $can_item) {
    $name=$_POST['i_name']; $code=$_POST['i_code']; $price=$_POST['i_price']; 
    $cat = !empty($_POST['i_cat']) ? $_POST['i_cat'] : 'NULL'; 
    $iid=intval($_POST['i_id']);

    if ($iid > 0) $conn->query("UPDATE oms_items SET name='$name', short_code='$code', unit_price='$price', category_id=$cat WHERE id=$iid");
    else $conn->query("INSERT INTO oms_items (name, short_code, unit_price, category_id) VALUES ('$name', '$code', '$price', $cat)");
    header("Location: oms_masters.php?tab=items&msg=Item Saved"); exit;
}
if (isset($_POST['save_customer']) && $can_cust) {
    $name=$_POST['c_name']; $mob=$_POST['c_mobile']; $wa=$_POST['c_wa']; $state=$_POST['c_state']; $addr=$_POST['c_addr']; $cid=intval($_POST['c_id']);
    
    if ($cid > 0) {
        $stmt = $conn->prepare("UPDATE oms_customers SET name=?, mobile=?, whatsapp=?, state=?, address=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $mob, $wa, $state, $addr, $cid);
    } else {
        $stmt = $conn->prepare("INSERT INTO oms_customers (name, mobile, whatsapp, state, address) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $name, $mob, $wa, $state, $addr);
    }
    $stmt->execute();
    header("Location: oms_masters.php?tab=customers&msg=Customer Saved"); exit;
}

// Fetch Categories for Dropdowns
$cats = $conn->query("SELECT * FROM oms_categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid px-4">
        <h3>Masters Management</h3>
        <?php 
        if(isset($_GET['msg'])) echo "<div class='alert alert-success'>{$_GET['msg']}</div>"; 
        if($err) echo "<div class='alert alert-danger'>$err</div>"; 
        ?>

        <ul class="nav nav-tabs mb-4">
            <?php if($can_cust): ?>
                <li class="nav-item"><a class="nav-link <?php echo $active_tab=='customers'?'active fw-bold':''; ?>" href="?tab=customers"><i class="bi bi-people"></i> Customers</a></li>
            <?php endif; ?>
            <?php if($can_item): ?>
                <li class="nav-item"><a class="nav-link <?php echo $active_tab=='items'?'active fw-bold':''; ?>" href="?tab=items"><i class="bi bi-box-seam"></i> Items</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $active_tab=='categories'?'active fw-bold':''; ?>" href="?tab=categories"><i class="bi bi-tags"></i> Categories</a></li>
            <?php endif; ?>
        </ul>

        <?php if($active_tab == 'customers' && $can_cust): ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header <?php echo $edit_mode_c ? 'bg-warning text-dark' : 'bg-primary text-white'; ?>">
                            <?php echo $edit_mode_c ? "Edit Customer" : "Add Customer"; ?>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="c_id" value="<?php echo $c_id; ?>">
                                <div class="mb-2"><label>Name</label><input type="text" name="c_name" class="form-control" value="<?php echo $c_name; ?>" required></div>
                                <div class="mb-2"><label>Mobile</label><input type="text" name="c_mobile" class="form-control" value="<?php echo $c_mobile; ?>" required></div>
                                <div class="mb-2"><label>Whatsapp</label><input type="text" name="c_wa" class="form-control" value="<?php echo $c_wa; ?>"></div>
                                <div class="mb-2"><label>State</label><input type="text" name="c_state" class="form-control" value="<?php echo $c_state; ?>"></div>
                                <div class="mb-3"><label>Address</label><textarea name="c_addr" class="form-control" rows="2"><?php echo $c_addr; ?></textarea></div>
                                
                                <button type="submit" name="save_customer" class="btn w-100 <?php echo $edit_mode_c ? 'btn-warning' : 'btn-primary'; ?>">
                                    <?php echo $edit_mode_c ? 'Update' : 'Save'; ?>
                                </button>
                                <?php if($edit_mode_c): ?>
                                    <a href="?tab=customers" class="btn btn-secondary w-100 mt-2">Cancel</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Contacts</th>
                                        <th>Location</th>
                                        <th style="width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $res = $conn->query("SELECT * FROM oms_customers ORDER BY name ASC");
                                    while($row=$res->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo $row['name']; ?></td>
                                        <td>
                                            <i class="bi bi-phone"></i> <?php echo $row['mobile']; ?>
                                            <?php if($row['whatsapp']): ?><br><i class="bi bi-whatsapp text-success"></i> <?php echo $row['whatsapp']; ?><?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="d-block text-muted"><?php echo $row['state']; ?></small>
                                            <small><?php echo substr($row['address'], 0, 30); ?>...</small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="oms_orders.php?customer_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success" title="Add Order"><i class="bi bi-cart-plus"></i></a>
                                                <a href="?tab=customers&edit_cust=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                                <a href="?tab=customers&del_cust=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?');"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif($active_tab == 'items' && $can_item): ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">Add/Edit Item</div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="i_id" value="<?php echo $i_id; ?>">
                                <div class="mb-2"><label>Category</label>
                                    <select name="i_cat" class="form-select">
                                        <option value="">-- No Category --</option>
                                        <?php $cats->data_seek(0); while($c=$cats->fetch_assoc()): ?>
                                            <option value="<?php echo $c['id']; ?>" <?php echo ($i_cat==$c['id'])?'selected':''; ?>><?php echo $c['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-2"><label>Name</label><input type="text" name="i_name" class="form-control" value="<?php echo $i_name; ?>" required></div>
                                <div class="mb-2"><label>Short Code</label><input type="text" name="i_code" class="form-control" value="<?php echo $i_code; ?>"></div>
                                <div class="mb-3"><label>Price</label><input type="number" step="0.01" name="i_price" class="form-control" value="<?php echo $i_price; ?>"></div>
                                <button type="submit" name="save_item" class="btn w-100 <?php echo $edit_mode_i ? 'btn-warning' : 'btn-success'; ?>">Save Item</button>
                                <?php if($edit_mode_i): ?>
                                    <a href="?tab=items" class="btn btn-secondary w-100 mt-2">Cancel</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="table-dark"><tr><th>Category</th><th>Item</th><th>Price</th><th>Stock</th><th>Action</th></tr></thead>
                                <tbody>
                                    <?php 
                                    $res = $conn->query("SELECT i.*, c.name as cat_name FROM oms_items i LEFT JOIN oms_categories c ON i.category_id=c.id ORDER BY c.name, i.name");
                                    while($r=$res->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $r['cat_name'] ?: '<em class="text-muted">None</em>'; ?></td>
                                        <td><?php echo $r['name']; ?> <small class="text-muted">(<?php echo $r['short_code']; ?>)</small></td>
                                        <td><?php echo $r['unit_price']; ?></td>
                                        <td><?php echo $r['current_stock']; ?></td>
                                        <td>
                                            <a href="?tab=items&edit_item=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                            <a href="?tab=items&del_item=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif($active_tab == 'categories' && $can_item): ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">Manage Categories</div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>">
                                <div class="mb-3"><label>Category Name</label><input type="text" name="cat_name" class="form-control" value="<?php echo $cat_name; ?>" required></div>
                                <button type="submit" name="save_cat" class="btn w-100 <?php echo $edit_mode_cat ? 'btn-warning' : 'btn-info text-white'; ?>">
                                    <?php echo $edit_mode_cat ? 'Update' : 'Save Category'; ?>
                                </button>
                                <?php if($edit_mode_cat): ?>
                                    <a href="?tab=categories" class="btn btn-secondary w-100 mt-2">Cancel</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Action</th></tr></thead>
                                <tbody>
                                    <?php $cats->data_seek(0); while($c=$cats->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $c['id']; ?></td>
                                        <td><?php echo $c['name']; ?></td>
                                        <td>
                                            <a href="?tab=categories&edit_cat=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                            <a href="?tab=categories&del_cat=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete category? Items will be Uncategorized.')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>