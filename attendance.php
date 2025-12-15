<?php
// 1. ENABLE ERROR REPORTING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Config & Settings
require_once 'config.php';
date_default_timezone_set('Asia/Kolkata');
checkLogin();
requirePermission('attendance_mark');


$today = date('Y-m-d');
$msg = "";
$msg_type = ""; 

// 3. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = $_POST['employee_id'];
    $now = date('Y-m-d H:i:s');

    // A. Find LATEST record
    $check_sql = "SELECT id, punch_out FROM attendance 
                  WHERE employee_id = ? AND work_date = ? 
                  ORDER BY id DESC LIMIT 1";
                  
    $stmt = $conn->prepare($check_sql);
    if ($stmt) {
        $stmt->bind_param("is", $emp_id, $today);
        $stmt->execute();
        $res = $stmt->get_result();
        $last_record = $res->fetch_assoc();
        $stmt->close();
    } else {
        die("Database Query Error: " . $conn->error);
    }

    // B. PUNCH IN
    if (isset($_POST['punch_in'])) {
        if (!$last_record || $last_record['punch_out'] != null) {
            $ins = $conn->prepare("INSERT INTO attendance (employee_id, punch_in, work_date) VALUES (?, ?, ?)");
            $ins->bind_param("iss", $emp_id, $now, $today);
            if($ins->execute()){
                $msg = "Punch IN Successful!";
                $msg_type = "success";
            } else {
                $msg = "Error: " . $ins->error;
                $msg_type = "danger";
            }
        } else {
            $msg = "Error: Employee is already INSIDE.";
            $msg_type = "danger";
        }
    }

    // C. PUNCH OUT
    if (isset($_POST['punch_out'])) {
        if ($last_record && $last_record['punch_out'] == null) {
            $last_id = $last_record['id'];
            $upd = $conn->prepare("UPDATE attendance SET punch_out = ? WHERE id = ?");
            $upd->bind_param("si", $now, $last_id);
            if($upd->execute()){
                $msg = "Punch OUT Successful!";
                $msg_type = "warning"; 
            } else {
                $msg = "Error: " . $upd->error;
                $msg_type = "danger";
            }
        } else {
            $msg = "Error: Employee is already OUT.";
            $msg_type = "danger";
        }
    }
}

// 4. Fetch Logs
$log_sql = "SELECT a.*, e.name, e.designation 
            FROM attendance a 
            JOIN employees e ON a.employee_id = e.id 
            WHERE a.work_date = '$today' 
            ORDER BY a.punch_in DESC";
$logs = $conn->query($log_sql);

// 5. Fetch Employees
$emp_list = $conn->query("SELECT * FROM employees ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Mark Attendance</h5>
                    </div>
                    <div class="card-body">
                        <?php if($msg) echo "<div class='alert alert-$msg_type'>$msg</div>"; ?>
                        
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Select Employee</label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <?php 
                                    if ($emp_list) {
                                        $emp_list->data_seek(0);
                                        while($e = $emp_list->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['name'] ?? ''); ?></option>
                                    <?php 
                                        endwhile; 
                                    } 
                                    ?>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="punch_in" class="btn btn-success py-2">
                                    <i class="bi bi-box-arrow-in-right"></i> PUNCH IN
                                </button>
                                <button type="submit" name="punch_out" class="btn btn-warning py-2 text-dark">
                                    <i class="bi bi-box-arrow-right"></i> PUNCH OUT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Activity Log (<?php echo date('d-M-Y'); ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0 align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Action Type</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($logs && $logs->num_rows > 0): ?>
                                        <?php while($row = $logs->fetch_assoc()): 
                                            $has_out = !empty($row['punch_out']);
                                            $in_time = date('h:i A', strtotime($row['punch_in']));
                                            $out_time = $has_out ? date('h:i A', strtotime($row['punch_out'])) : null;
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row['name'] ?? ''); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($row['designation'] ?? ''); ?></small>
                                            </td>
                                            <td>
                                                <div class="text-success fw-bold">
                                                    <i class="bi bi-arrow-right-circle"></i> IN: <?php echo $in_time; ?>
                                                </div>
                                                <?php if($has_out): ?>
                                                <div class="text-danger fw-bold mt-1">
                                                    <i class="bi bi-arrow-left-circle"></i> OUT: <?php echo $out_time; ?>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if($has_out){
                                                    $start = new DateTime($row['punch_in']);
                                                    $end = new DateTime($row['punch_out']);
                                                    echo $start->diff($end)->format('%h h %i m');
                                                } else {
                                                    echo "Running...";
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $has_out ? '<span class="badge bg-secondary">Completed</span>' : '<span class="badge bg-success">Active</span>'; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center p-4">No punches recorded today.</td></tr>
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