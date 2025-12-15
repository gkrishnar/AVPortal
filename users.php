<?php
// users.php - Manage Users with Hierarchical Permissions
require_once 'config.php';
ini_set('display_errors', 1); error_reporting(E_ALL);
checkLogin();
if(function_exists('requirePermission')) {
    requirePermission('user_manage');
}

$msg = ""; $err = "";
$username = ""; $password = ""; $edit_id = 0;
$user_perms = [];

// --- ACTIONS (Delete, Save, Reset Pass) ---
// (Kept exact same logic as before to ensure stability)

if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    if ($del_id == 1) { 
        $err = "Cannot delete Super Admin.";
    } else {
        $conn->query("DELETE FROM user_permissions WHERE user_id=$del_id");
        $conn->query("DELETE FROM users WHERE id=$del_id");
        $msg = "User deleted.";
    }
}

if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = $conn->query("SELECT username FROM users WHERE id=$edit_id");
    if($r = $res->fetch_assoc()) {
        $username = $r['username'];
        $p_res = $conn->query("SELECT permission_id FROM user_permissions WHERE user_id=$edit_id");
        while($pr = $p_res->fetch_assoc()) {
            $user_perms[] = $pr['permission_id'];
        }
    }
}

if (isset($_POST['save_user'])) {
    $u_name = $_POST['username'];
    $u_pass = $_POST['password'] ?? ''; 
    $u_perms = isset($_POST['perms']) ? array_unique($_POST['perms']) : [];
    $e_id = intval($_POST['edit_id']);

    if ($e_id > 0) {
        $conn->query("UPDATE users SET username='$u_name' WHERE id=$e_id");
        $conn->query("DELETE FROM user_permissions WHERE user_id=$e_id");
        if (!empty($u_perms)) {
            $stmt = $conn->prepare("INSERT IGNORE INTO user_permissions (user_id, permission_id) VALUES (?, ?)");
            foreach ($u_perms as $pid) {
                $stmt->bind_param("ii", $e_id, $pid);
                $stmt->execute();
            }
        }
        $msg = "User updated successfully.";
    } else {
        if(empty($u_pass)) {
            $err = "Password is required for new users.";
        } else {
            $hash = password_hash($u_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'operator')");
            $stmt->bind_param("ss", $u_name, $hash);
            if ($stmt->execute()) {
                $new_id = $conn->insert_id;
                if (!empty($u_perms)) {
                    $p_stmt = $conn->prepare("INSERT IGNORE INTO user_permissions (user_id, permission_id) VALUES (?, ?)");
                    foreach ($u_perms as $pid) {
                        $p_stmt->bind_param("ii", $new_id, $pid);
                        $p_stmt->execute();
                    }
                }
                $msg = "User created successfully.";
            } else {
                $err = "Error: " . $conn->error;
            }
        }
    }
    if ($msg) { $username = ""; $user_perms = []; $edit_id = 0; }
}

if (isset($_POST['reset_password_btn'])) {
    $r_uid = intval($_POST['reset_user_id']);
    $r_pass = $_POST['new_password'];
    if ($r_uid > 0 && !empty($r_pass)) {
        $hash = password_hash($r_pass, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hash' WHERE id=$r_uid");
        $msg = "Password reset successfully.";
    }
}

// --- PERMISSION GROUPING LOGIC ---
// We fetch all permissions and organize them into categories manually for better UI
$all_perms_grouped = [
    'Gate Pass System' => [],
    'Attendance System' => [],
    'Order Management' => [],
    'Admin & Masters' => []
];

$res_p = $conn->query("SELECT * FROM permissions ORDER BY id ASC");
while($p = $res_p->fetch_assoc()) {
    $k = $p['perm_key'];
    
    // Categorize based on key prefix
    if (strpos($k, 'gatepass_') === 0) {
        $all_perms_grouped['Gate Pass System'][] = $p;
    } elseif (strpos($k, 'attendance_') === 0) {
        $all_perms_grouped['Attendance System'][] = $p;
    } elseif (strpos($k, 'oms_') === 0) {
        $all_perms_grouped['Order Management'][] = $p;
    } else {
        $all_perms_grouped['Admin & Masters'][] = $p; // employee_manage, user_manage, etc.
    }
}
$all_users = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .perm-group-header { font-size: 0.85rem; font-weight: bold; text-transform: uppercase; color: #6c757d; border-bottom: 2px solid #dee2e6; margin-bottom: 10px; padding-bottom: 5px; }
        .perm-item { margin-bottom: 8px; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid px-4">
        <div class="row">
            
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo $edit_id ? "Edit User & Privileges" : "Create User"; ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
                        <?php if($err) echo "<div class='alert alert-danger'>$err</div>"; ?>

                        <form method="post">
                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                                </div>
                            </div>

                            <?php if(!$edit_id): ?>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <?php endif; ?>

                            <label class="form-label fw-bold mb-3">Assign Feature Access:</label>
                            
                            <div class="row">
                                <?php foreach($all_perms_grouped as $group_name => $perms): ?>
                                    <?php if(!empty($perms)): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="p-3 border rounded bg-light h-100">
                                            <div class="perm-group-header"><?php echo $group_name; ?></div>
                                            <?php foreach($perms as $p): 
                                                $checked = in_array($p['id'], $user_perms) ? 'checked' : '';
                                            ?>
                                                <div class="form-check perm-item">
                                                    <input class="form-check-input" type="checkbox" name="perms[]" value="<?php echo $p['id']; ?>" id="p_<?php echo $p['id']; ?>" <?php echo $checked; ?>>
                                                    <label class="form-check-label" for="p_<?php echo $p['id']; ?>">
                                                        <?php echo $p['description']; ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-3">
                                <button type="submit" name="save_user" class="btn btn-success w-100 fw-bold">
                                    <?php echo $edit_id ? "Save Changes" : "Create User"; ?>
                                </button>
                                <?php if($edit_id): ?>
                                    <a href="users.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Existing Users</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Username</th>
                                    <th>Permissions</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($all_users): while($u = $all_users->fetch_assoc()): 
                                    $cnt = $conn->query("SELECT COUNT(*) as c FROM user_permissions WHERE user_id=".$u['id'])->fetch_assoc()['c'];
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?php echo $cnt; ?> Assigned</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="users.php?edit=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark" 
                                                    data-bs-toggle="modal" data-bs-target="#resetPassModal"
                                                    data-uid="<?php echo $u['id']; ?>" data-uname="<?php echo htmlspecialchars($u['username']); ?>">
                                                <i class="bi bi-key"></i>
                                            </button>
                                            <?php if($u['id'] != 1): ?>
                                                <a href="users.php?delete=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete user?');"><i class="bi bi-trash"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resetPassModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="reset_user_id" id="reset_user_id">
                        <p>Reset password for: <strong id="reset_username_display"></strong></p>
                        <input type="text" name="new_password" class="form-control" placeholder="New password" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="reset_password_btn" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const resetModal = document.getElementById('resetPassModal');
        resetModal.addEventListener('show.bs.modal', event => {
            const btn = event.relatedTarget;
            document.getElementById('reset_user_id').value = btn.getAttribute('data-uid');
            document.getElementById('reset_username_display').textContent = btn.getAttribute('data-uname');
        });
    </script>
</body>
</html>