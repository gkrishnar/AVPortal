<?php
// employees.php - Full Employee CRUD
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

checkLogin();
requirePermission('employee_manage');



$msg = "";
$err = "";
$edit_state = false;

// Initialize variables for the form
$name = ""; $mobile = ""; $alt_mobile = ""; $whatsapp = ""; 
$email = ""; $address = ""; $joining_date = date('Y-m-d'); $id = 0;

// --- ACTION: DELETE ---
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $conn->query("DELETE FROM employees WHERE id=$del_id");
    header("Location: employees.php?msg=Employee Deleted Successfully");
    exit();
}

// --- ACTION: FETCH FOR EDIT ---
if (isset($_GET['edit'])) {
    $edit_state = true;
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM employees WHERE id=$id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $name = $row['name'];
        $mobile = $row['mobile_no'];
        $alt_mobile = $row['alt_mobile'];
        $whatsapp = $row['whatsapp_no'];
        $email = $row['email'];
        $address = $row['address'];
        $joining_date = $row['joining_date'];
    }
}

// --- ACTION: SUBMIT (ADD or UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $alt_mobile = $_POST['alt_mobile'];
    $whatsapp = $_POST['whatsapp'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $joining_date = $_POST['joining_date'];

    if (isset($_POST['update'])) {
        // UPDATE EXISTING
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE employees SET name=?, mobile_no=?, alt_mobile=?, whatsapp_no=?, email=?, address=?, joining_date=? WHERE id=?");
        $stmt->bind_param("sssssssi", $name, $mobile, $alt_mobile, $whatsapp, $email, $address, $joining_date, $id);
        if ($stmt->execute()) {
            header("Location: employees.php?msg=Employee Updated Successfully");
            exit();
        } else {
            $err = "Error updating: " . $conn->error;
        }
    } else {
        // CREATE NEW
        $stmt = $conn->prepare("INSERT INTO employees (name, mobile_no, alt_mobile, whatsapp_no, email, address, joining_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $mobile, $alt_mobile, $whatsapp, $email, $address, $joining_date);
        if ($stmt->execute()) {
            $msg = "Employee Added Successfully";
            // Clear variables after success
            $name = ""; $mobile = ""; $alt_mobile = ""; $whatsapp = ""; $email = ""; $address = "";
        } else {
            $err = "Error adding: " . $conn->error;
        }
    }
}

// Fetch All Employees for List
$employees = $conn->query("SELECT * FROM employees ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo $edit_state ? "Edit Employee" : "Add New Employee"; ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if($msg || isset($_GET['msg'])) echo "<div class='alert alert-success'>" . ($msg ?: $_GET['msg']) . "</div>"; ?>
                        <?php if($err) echo "<div class='alert alert-danger'>$err</div>"; ?>

                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            
                            <div class="mb-2">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile" class="form-control" value="<?php echo $mobile; ?>" required pattern="[0-9]{10}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Alternate Mobile</label>
                                    <input type="text" name="alt_mobile" class="form-control" value="<?php echo $alt_mobile; ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Whatsapp No</label>
                                    <input type="text" name="whatsapp" class="form-control" value="<?php echo $whatsapp; ?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Joining Date</label>
                                    <input type="date" name="joining_date" class="form-control" value="<?php echo $joining_date; ?>">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"><?php echo $address; ?></textarea>
                            </div>

                            <?php if ($edit_state): ?>
                                <button type="submit" name="update" class="btn btn-warning w-100 text-white">Update Employee</button>
                                <a href="employees.php" class="btn btn-secondary w-100 mt-2">Cancel Edit</a>
                            <?php else: ?>
                                <button type="submit" name="save" class="btn btn-success w-100">Save Employee</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Employee List</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact Info</th>
                                        <th>Joining Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($employees->num_rows > 0): ?>
                                        <?php while($row = $employees->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                                                <small class="text-muted">ID: <?php echo $row['id']; ?></small>
                                            </td>
                                            <td style="font-size: 0.9rem;">
                                                <i class="bi bi-telephone"></i> <?php echo $row['mobile_no']; ?><br>
                                                <?php if($row['whatsapp_no']): ?>
                                                    <i class="bi bi-whatsapp text-success"></i> <?php echo $row['whatsapp_no']; ?><br>
                                                <?php endif; ?>
                                                <?php if($row['email']): ?>
                                                    <i class="bi bi-envelope"></i> <?php echo $row['email']; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo date('d-M-Y', strtotime($row['joining_date'])); ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="employees.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#viewModal<?php echo $row['id']; ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <a href="employees.php?delete=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Are you sure you want to delete this employee?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>

                                                <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Details: <?php echo $row['name']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Mobile:</strong> <?php echo $row['mobile_no']; ?></p>
                                                                <p><strong>Alt Mobile:</strong> <?php echo $row['alt_mobile']; ?></p>
                                                                <p><strong>Whatsapp:</strong> <?php echo $row['whatsapp_no']; ?></p>
                                                                <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                                                                <p><strong>Joining Date:</strong> <?php echo date('d-M-Y', strtotime($row['joining_date'])); ?></p>
                                                                <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($row['address'])); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center p-3">No employees found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>