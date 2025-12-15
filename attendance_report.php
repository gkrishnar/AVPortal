<?php
// 1. Config & Settings
require_once 'config.php';
ini_set('display_errors', 1); error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');
checkLogin();

// 2. Filters
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'daily';
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$month_filter = isset($_GET['month']) ? $_GET['month'] : date('n'); // 1-12
$year_filter = isset($_GET['year']) ? $_GET['year'] : date('Y');
$emp_id_filter = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;

// Helper: Seconds to HH:MM
function secondsToTime($seconds) {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    return sprintf('%02dh %02dm', $h, $m);
}

// 3. Fetch Employees (For Dropdown)
$emp_list = $conn->query("SELECT id, name FROM employees ORDER BY name ASC");

// 4. DATA PROCESSING
$report_data = [];
$total_period_seconds = 0;
$subject_name = "";
$present_dates = []; // Stores details for calendar mapping

if ($report_type == 'monthly' && $emp_id_filter > 0) {
    // --- MONTHLY (CALENDAR) MODE ---
    
    // Get Employee Info
    $e_res = $conn->query("SELECT name, designation FROM employees WHERE id=$emp_id_filter");
    if($e_row = $e_res->fetch_assoc()) {
        $subject_name = $e_row['name'];
    }

    // Fetch Attendance
    $sql = "SELECT * FROM attendance 
            WHERE employee_id = $emp_id_filter 
            AND MONTH(work_date) = '$month_filter' 
            AND YEAR(work_date) = '$year_filter' 
            ORDER BY work_date ASC, punch_in ASC";
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
        $d = $row['work_date'];
        
        // Calculate Duration
        $in = new DateTime($row['punch_in']);
        $out = $row['punch_out'] ? new DateTime($row['punch_out']) : null;
        
        $duration_val = 0;
        $time_str = $in->format('H:i') . " - Active";
        
        if ($out) {
            $interval = $in->diff($out);
            $duration_val = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
            $total_period_seconds += $duration_val;
            $time_str = $in->format('H:i') . " - " . $out->format('H:i');
        }

        // Store data keyed by Date (Y-m-d)
        if (!isset($present_dates[$d])) {
            $present_dates[$d] = [
                'sessions' => [],
                'total_time' => 0
            ];
        }
        $present_dates[$d]['sessions'][] = $time_str;
        $present_dates[$d]['total_time'] += $duration_val;
    }

} else {
    // --- DAILY (TABLE) MODE ---
    $subject_name = "Date: " . date('d-M-Y', strtotime($date_filter));
    $sql = "SELECT a.*, e.name, e.designation FROM attendance a 
            JOIN employees e ON a.employee_id = e.id 
            WHERE a.work_date = '$date_filter' ORDER BY e.name ASC";
    $result = $conn->query($sql);
    
    while($row = $result->fetch_assoc()) {
        $eid = $row['employee_id'];
        if (!isset($report_data[$eid])) {
            $report_data[$eid] = [
                'display' => htmlspecialchars($row['name'] ?? ''),
                'designation' => htmlspecialchars($row['designation'] ?? ''),
                'sessions' => [],
                'total_sec' => 0,
                'active' => false
            ];
        }
        $in = new DateTime($row['punch_in']);
        $out = $row['punch_out'] ? new DateTime($row['punch_out']) : null;
        $str = "Active";
        if($out) {
            $diff = $in->diff($out);
            $sec = ($diff->h*3600)+($diff->i*60)+$diff->s;
            $report_data[$eid]['total_sec'] += $sec;
            $str = $diff->format('%H:%I');
        } else { $report_data[$eid]['active'] = true; }

        $report_data[$eid]['sessions'][] = [
            'in' => $in->format('h:i A'),
            'out' => $out ? $out->format('h:i A') : 'Active',
            'dur' => $str
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .filter-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; }
        
        /* CALENDAR STYLES */
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; }
        .cal-header { background: #343a40; color: white; padding: 10px; text-align: center; font-weight: bold; }
        .cal-day { 
            height: 100px; border: 1px solid #dee2e6; padding: 5px; position: relative; 
            background: #fff; transition: 0.2s;
        }
        .cal-day:hover { transform: scale(1.02); z-index: 5; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .date-num { font-weight: bold; position: absolute; top: 5px; right: 10px; }
        
        .bg-present { background-color: #d1e7dd !important; border-color: #badbcc; } /* Light Green */
        .bg-absent { background-color: #f8d7da !important; border-color: #f5c2c7; } /* Light Red */
        .bg-na { background-color: #f8f9fa !important; } /* Gray for future/empty */

        .status-badge { font-size: 0.75rem; margin-top: 25px; display: block; }
        .session-info { font-size: 0.7rem; color: #555; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h3>Attendance Reports</h3>
        
        <div class="filter-box mb-4 shadow-sm">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Report Type</label>
                    <select name="report_type" id="report_type" class="form-select" onchange="toggleFilters()">
                        <option value="daily" <?php echo ($report_type == 'daily') ? 'selected' : ''; ?>>Daily (List)</option>
                        <option value="monthly" <?php echo ($report_type == 'monthly') ? 'selected' : ''; ?>>Monthly (Calendar)</option>
                    </select>
                </div>

                <div class="col-md-3 daily-filter">
                    <label class="form-label">Select Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo $date_filter; ?>">
                </div>

                <div class="col-md-3 monthly-filter" style="display:none;">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">-- Select --</option>
                        <?php if ($emp_list) { $emp_list->data_seek(0); while($e = $emp_list->fetch_assoc()): ?>
                            <option value="<?php echo $e['id']; ?>" <?php echo ($emp_id_filter == $e['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($e['name']); ?>
                            </option>
                        <?php endwhile; } ?>
                    </select>
                </div>
                <div class="col-md-2 monthly-filter" style="display:none;">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo ($month_filter == $m) ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $m, 10)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2 monthly-filter" style="display:none;">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <?php for($y=2024; $y<=2030; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo ($year_filter == $y) ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">View</button>
                </div>
            </form>
        </div>

        <?php if($report_type == 'monthly'): ?>
            <?php if($emp_id_filter == 0): ?>
                <div class="alert alert-warning">Please select an employee to generate the calendar.</div>
            <?php else: ?>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-primary mb-0">
                        <?php echo $subject_name; ?> - <?php echo date('F Y', mktime(0,0,0,$month_filter, 1, $year_filter)); ?>
                    </h4>
                    <div>
                        <span class="badge bg-success fs-6 me-2 p-2">
                            Days Present: <?php echo count($present_dates); ?>
                        </span>
                        <span class="badge bg-dark fs-6 p-2">
                            Total Hours: <?php echo secondsToTime($total_period_seconds); ?>
                        </span>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <div class="calendar-grid">
                            <div class="cal-header">Sun</div><div class="cal-header">Mon</div><div class="cal-header">Tue</div>
                            <div class="cal-header">Wed</div><div class="cal-header">Thu</div><div class="cal-header">Fri</div><div class="cal-header">Sat</div>
                            
                            <?php
                            // Calendar Logic
                            $total_days = cal_days_in_month(CAL_GREGORIAN, $month_filter, $year_filter);
                            $first_day_timestamp = mktime(0, 0, 0, $month_filter, 1, $year_filter);
                            $start_day_of_week = date('w', $first_day_timestamp); // 0 (Sun) to 6 (Sat)

                            // 1. Empty slots before 1st of month
                            for($i = 0; $i < $start_day_of_week; $i++) {
                                echo "<div class='cal-day bg-light'></div>";
                            }

                            // 2. Days Loop
                            for($day = 1; $day <= $total_days; $day++) {
                                $current_date = sprintf('%04d-%02d-%02d', $year_filter, $month_filter, $day);
                                $is_present = isset($present_dates[$current_date]);
                                $is_future = strtotime($current_date) > time();
                                
                                // Determine Class
                                $css_class = "bg-absent"; // Default Red
                                $status_text = "<span class='text-danger fw-bold'>Absent</span>";
                                
                                if ($is_present) {
                                    $css_class = "bg-present"; // Green
                                    $hrs = secondsToTime($present_dates[$current_date]['total_time']);
                                    $status_text = "<span class='text-success fw-bold'>Present</span><br><small>($hrs)</small>";
                                } elseif ($is_future) {
                                    $css_class = "bg-na"; // Gray
                                    $status_text = "";
                                }

                                echo "<div class='cal-day $css_class'>";
                                echo "<span class='date-num'>$day</span>";
                                echo "<div class='status-badge text-center'>$status_text</div>";
                                
                                // Show timings if present
                                if ($is_present) {
                                    echo "<div class='session-info text-center mt-1'>";
                                    foreach($present_dates[$current_date]['sessions'] as $s) {
                                        echo "<div>$s</div>";
                                    }
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                            ?>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-present text-dark border me-2">Green = Present</span>
                            <span class="badge bg-absent text-dark border me-2">Red = Absent</span>
                            <span class="badge bg-na text-dark border">Gray = N/A (Future)</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <h5 class="text-primary mb-3">Log for: <?php echo date('d-M-Y', strtotime($date_filter)); ?></h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr><th>Employee</th><th>Status</th><th>Sessions</th><th>Total Hours</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($report_data)): ?>
                            <?php foreach($report_data as $row): ?>
                            <tr>
                                <td><strong><?php echo $row['display']; ?></strong><br><small class="text-muted"><?php echo $row['designation']; ?></small></td>
                                <td><?php echo $row['active'] ? '<span class="badge bg-success">In</span>' : '<span class="badge bg-secondary">Out</span>'; ?></td>
                                <td>
                                    <ul class="list-unstyled mb-0 small">
                                    <?php foreach($row['sessions'] as $s): ?>
                                        <li>In: <?php echo $s['in']; ?> - Out: <?php echo $s['out']; ?> (<?php echo $s['dur']; ?>)</li>
                                    <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td class="fw-bold text-primary"><?php echo secondsToTime($row['total_sec']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center p-4">No records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleFilters() {
            var type = document.getElementById('report_type').value;
            var daily = document.querySelectorAll('.daily-filter');
            var monthly = document.querySelectorAll('.monthly-filter');
            if (type === 'daily') {
                daily.forEach(el => el.style.display = 'block');
                monthly.forEach(el => el.style.display = 'none');
            } else {
                daily.forEach(el => el.style.display = 'none');
                monthly.forEach(el => el.style.display = 'block');
            }
        }
        window.onload = toggleFilters;
    </script>
</body>
</html>