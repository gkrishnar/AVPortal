<?php
// 1. Config & Settings
require_once 'config.php';
date_default_timezone_set('Asia/Kolkata');
checkLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gate Pass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .dashboard-card { transition: transform 0.2s; border: none; }
        .dashboard-card:hover { transform: translateY(-5px); }
        .card-icon { font-size: 2.5rem; opacity: 0.8; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    
            <?php if (isset($_POST['quick_gatepass']) && hasPermission('create_pass')) {
                $v_name = $_POST['visitor_name'];
                $company = $_POST['company_name'];
                $mobile = $_POST['mobile_no'];
                $purpose = $_POST['purpose'];
                $visit_type = $_POST['visit_type'];
                $created_by = $_SESSION['user_id'];
                
                // Auto-generate Pass ID (e.g., GP-1001)
                $last_id_q = $conn->query("SELECT id FROM gate_passes ORDER BY id DESC LIMIT 1");
                $next_id = ($last_id_q->num_rows > 0) ? $last_id_q->fetch_assoc()['id'] + 1 : 1001;
                $pass_id = "GP-" . $next_id;
            
                $stmt = $conn->prepare("INSERT INTO gate_passes (pass_id, visitor_name, company_name, mobile_no, purpose, visit_type, created_by, in_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'Active')");
                $stmt->bind_param("ssssssi", $pass_id, $v_name, $company, $mobile, $purpose, $visit_type, $created_by);
                
                if ($stmt->execute()) {
                    $msg = "Gatepass Generated Successfully: <strong>$pass_id</strong>";
                } else {
                    $err = "Error creating pass: " . $conn->error;
                }
            }
            ?>


    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard</h2>
            <span class="text-muted"><?php echo date('l, d F Y'); ?></span>
        </div>

        <div class="row g-4">
            
            
            
            <?php if(hasPermission('oms_dispatch_schedule')): ?>
            <?php 
                $today_date = date('Y-m-d');
                
                // 1. Get Total Count for Today
                $cnt_q = $conn->query("SELECT COUNT(*) as total FROM oms_orders WHERE status='Pending' AND dispatch_date='$today_date'");
                $total_today = ($cnt_q) ? $cnt_q->fetch_assoc()['total'] : 0;

                // 2. Get Top 5 Orders
                $limit = 5;
                $res_dash = $conn->query("SELECT o.*, c.name as cname, i.name as iname 
                                          FROM oms_orders o 
                                          LEFT JOIN oms_customers c ON o.customer_id = c.id 
                                          LEFT JOIN oms_items i ON o.item_id = i.id 
                                          WHERE o.status='Pending' AND o.dispatch_date='$today_date' 
                                          ORDER BY o.id ASC LIMIT $limit");
            ?>
            <div class="col-12 mt-4">
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-truck"></i> Today's Dispatch Schedule</h5>
                        <span class="badge bg-dark"><?php echo $total_today; ?> Orders Pending</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($res_dash && $res_dash->num_rows > 0): ?>
                                        <?php while($row = $res_dash->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $row['cname']; ?></td>
                                            <td><?php echo $row['iname']; ?></td>
                                            <td><?php echo $row['quantity']; ?></td>
                                            <td class="text-end">
                                                <a href="oms_dispatch_schedule.php" class="btn btn-sm btn-outline-dark fw-bold">
                                                    Process <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center p-3 text-muted">No pending dispatches for today.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <?php if($total_today > $limit): ?>
                    <div class="card-footer bg-light text-center">
                        <a href="oms_dispatch_schedule.php" class="btn btn-primary btn-sm fw-bold px-4">
                            Show More (<?php echo $total_today - $limit; ?> remaining) <i class="bi bi-chevron-down"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            
            <?php if(hasPermission('create_gatepass')): ?>
            
            
            
            <div class="col-md-4">
                    <div class="card text-white bg-dark h-100 shadow-sm dashboard-card">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title fw-bold">New Visitor</h5>
                                    <h2 class="display-6 fw-bold mb-0"><?php echo $count; ?></h2>
                                    <small>Entries Today</small>
                                </div>
                                <i class="bi bi-bar-chart-line-fill card-icon"></i>
                            </div>
                            <a href="create_pass.php" class="btn btn-light text-success fw-bold mt-3 stretched-link">
                                View Details <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if(hasPermission('gatepass_report')): ?>
                <?php
                // Only run query if they have permission to see reports
                $today = date('Y-m-d');
                $sql = "SELECT COUNT(*) as cnt FROM gate_passes WHERE DATE(entry_time) = '$today'";
                $res = $conn->query($sql);
                $count = ($res && $row = $res->fetch_assoc()) ? $row['cnt'] : 0;
                ?>
                <div class="col-md-4">
                    <div class="card text-white bg-success h-100 shadow-sm dashboard-card">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title fw-bold">Visitor Reports</h5>
                                    <h2 class="display-6 fw-bold mb-0"><?php echo $count; ?></h2>
                                    <small>Entries Today</small>
                                </div>
                                <i class="bi bi-bar-chart-line-fill card-icon"></i>
                            </div>
                            <a href="reports.php" class="btn btn-light text-success fw-bold mt-3 stretched-link">
                                View Details <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(hasPermission('attendance_mark')): ?>
            <div class="col-md-4">
                <div class="card text-white bg-info h-100 shadow-sm dashboard-card">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title fw-bold">Attendance</h5>
                                <p class="card-text small">Mark Employee In / Out.</p>
                            </div>
                            <i class="bi bi-clock-history card-icon"></i>
                        </div>
                        <a href="attendance.php" class="btn btn-light text-info fw-bold mt-3 stretched-link">
                            Mark Attendance <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if(hasPermission('oms_orders_view') || hasPermission('oms_orders_manage') || hasPermission('oms_stock_manage') || hasPermission('oms_orders_status')): ?>
    
            <div class="col-md-4">
                <div class="card text-white bg-dark h-100 shadow-sm dashboard-card">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title fw-bold">Order Mgmt</h5>
                                <p class="card-text small">Manage Orders, Stock & Customers.</p>
                            </div>
                            <i class="bi bi-cart-check-fill card-icon"></i>
                        </div>
                        <a href="oms_orders.php" class="btn btn-light text-dark fw-bold mt-3 stretched-link">
                            Go to Orders <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            

            <?php if(hasPermission('employee_manage')): ?>
            <div class="col-md-4">
                <div class="card text-white bg-secondary h-100 shadow-sm dashboard-card">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title fw-bold">Employee Master</h5>
                                <p class="card-text small">Add or Edit Staff details.</p>
                            </div>
                            <i class="bi bi-people-fill card-icon"></i>
                        </div>
                        <a href="employees.php" class="btn btn-light text-secondary fw-bold mt-3 stretched-link">
                            Manage Staff <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            
            <?php if(hasPermission('oms_stock_report_view')): ?>
            <div class="col-md-4">
                <div class="card text-white bg-success h-100 shadow-sm dashboard-card">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title fw-bold">Current Stock</h5>
                                <?php 
                                    // Fetch live total stock count
                                    $stk_q = $conn->query("SELECT SUM(current_stock) as total FROM oms_items");
                                    $stk_total = ($stk_q && $r = $stk_q->fetch_assoc()) ? $r['total'] : 0;
                                ?>
                                <h3 class="mb-0"><?php echo number_format($stk_total); ?></h3>
                                <p class="card-text small">Items Available</p>
                            </div>
                            <i class="bi bi-boxes card-icon"></i>
                        </div>
                        <a href="oms_stock_report.php" class="btn btn-light text-dark fw-bold mt-3 stretched-link">
                            View Report <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            

            <?php if(hasPermission('user_manage')): ?>
            <div class="col-md-4">
                <div class="card text-white bg-warning h-100 shadow-sm dashboard-card">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title fw-bold text-dark">System Users</h5>
                                <p class="card-text small text-dark">Manage logins & permissions.</p>
                            </div>
                            <i class="bi bi-gear-fill card-icon text-dark"></i>
                        </div>
                        <a href="users.php" class="btn btn-light text-warning fw-bold mt-3 stretched-link">
                            Access Control <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            
            

        </div>
        
        <div class="mt-5 text-center text-muted">
            <small>System Version 1.0 &copy; <?php echo date('Y'); ?></small>
        </div>
    </div>

<?php if(hasPermission('create_gatepass')): ?>
<div class="modal fade" id="gatepassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-badge"></i> Create New Gatepass</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="index.php">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Visitor Name</label>
                        <input type="text" name="visitor_name" class="form-control" required placeholder="Enter Full Name">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mobile No</label>
                            <input type="text" name="mobile_no" class="form-control" required placeholder="10-digit Number" pattern="[0-9]{10}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Visit Type</label>
                            <select name="visit_type" class="form-select">
                                <option value="Official">Official</option>
                                <option value="Personal">Personal</option>
                                <option value="Interview">Interview</option>
                                <option value="Delivery">Delivery</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Company / From</label>
                        <input type="text" name="company_name" class="form-control" placeholder="Company Name or Place">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Purpose</label>
                        <textarea name="purpose" class="form-control" rows="2" required placeholder="Reason for visit..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="quick_gatepass" class="btn btn-primary fw-bold">
                            <i class="bi bi-check-circle"></i> Generate Pass
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>