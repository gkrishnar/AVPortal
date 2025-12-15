<?php
// 1. Config & Settings
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

checkLogin();
// Ensure user has permission to view reports
if(function_exists('requirePermission')) {
    requirePermission('gatepass_report');
}

// 2. Query
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$query = "SELECT gp.*, p.purpose_name, u.username 
          FROM gate_passes gp 
          LEFT JOIN purposes p ON gp.purpose_id = p.id 
          LEFT JOIN users u ON gp.created_by = u.id
          WHERE DATE(gp.entry_time) = '$date_filter'
          ORDER BY gp.entry_time DESC";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .time-text { font-size: 0.9rem; font-weight: 500; }
        /* Thumbnail Style */
        .visitor-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid #ddd;
            transition: transform 0.2s;
        }
        .visitor-thumb:hover {
            transform: scale(1.1);
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid px-4">
        <h3>Daily Gate Pass Report</h3>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <form method="get" class="row g-3 mb-4">
            <div class="col-auto"><label class="col-form-label">Select Date:</label></div>
            <div class="col-auto"><input type="date" name="date" class="form-control" value="<?php echo $date_filter; ?>"></div>
            <div class="col-auto"><button type="submit" class="btn btn-primary">Filter</button></div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th class="text-center">Image</th> <th>Entry Time</th>
                        <th>Visitor</th>
                        <th>Mobile</th>
                        <th>Vehicle</th>
                        <th>Purpose</th>
                        <th>Exit Time</th>
                        <th style="width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): 
                            // Safe Date Formatting
                            $entryDisplay = (!empty($row['entry_time']) && $row['entry_time'] != '0000-00-00 00:00:00') 
                                ? date('d-m-Y h:i A', strtotime($row['entry_time'])) 
                                : 'N/A';
                                
                            $exitDisplay = (!empty($row['exit_time'])) 
                                ? date('d-m-Y h:i A', strtotime($row['exit_time'])) 
                                : 'Inside';
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            
                            <td class="text-center">
                                <?php if(!empty($row['visitor_image'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['visitor_image']); ?>" 
                                         class="visitor-thumb shadow-sm"
                                         title="Click to Zoom"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#imagePreviewModal"
                                         data-src="uploads/<?php echo htmlspecialchars($row['visitor_image']); ?>">
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>

                            <td class="time-text"><?php echo $entryDisplay; ?></td>
                            <td><?php echo htmlspecialchars($row['visitor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['mobile_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['vehicle_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['purpose_name']); ?></td>
                            <td>
                                <?php if (!empty($row['exit_time'])): ?>
                                    <span class='badge bg-success'><?php echo $exitDisplay; ?></span>
                                <?php else: ?>
                                    <span class='badge bg-warning text-dark'>Inside</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-info btn-sm text-white view-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewModal"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-visitor="<?php echo htmlspecialchars($row['visitor_name']); ?>"
                                            data-mobile="<?php echo htmlspecialchars($row['mobile_no']); ?>"
                                            data-vehicle="<?php echo htmlspecialchars($row['vehicle_no']); ?>"
                                            data-purpose="<?php echo htmlspecialchars($row['purpose_name']); ?>"
                                            data-material="<?php echo htmlspecialchars($row['material_details']); ?>"
                                            data-entry="<?php echo $entryDisplay; ?>"
                                            data-exit="<?php echo $exitDisplay; ?>"
                                            data-user="<?php echo htmlspecialchars($row['username']); ?>"
                                            data-image="<?php echo htmlspecialchars($row['visitor_image']); ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>

                                    <?php if (empty($row['exit_time'])): ?>
                                        <a href="mark_exit.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm Exit?')">Exit</a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Done</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="10" class="text-center">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Gate Pass Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <table class="table table-bordered">
                                <tr><th class="bg-light w-50">Pass ID</th><td id="m_id"></td></tr>
                                <tr><th class="bg-light">Visitor Name</th><td id="m_visitor"></td></tr>
                                <tr><th class="bg-light">Mobile No</th><td id="m_mobile"></td></tr>
                                <tr><th class="bg-light">Vehicle No</th><td id="m_vehicle"></td></tr>
                                <tr><th class="bg-light">Purpose</th><td id="m_purpose"></td></tr>
                                <tr><th class="bg-light">Entry Time</th><td id="m_entry"></td></tr>
                                <tr><th class="bg-light">Exit Time</th><td id="m_exit"></td></tr>
                                <tr><th class="bg-light">Created By</th><td id="m_user"></td></tr>
                                <tr><th class="bg-light">Material</th><td id="m_material"></td></tr>
                            </table>
                        </div>
                        <div class="col-md-5 text-center">
                            <h6>Visitor Image</h6>
                            <div class="border p-2 bg-light">
                                <img id="m_image" src="" class="img-fluid" style="max-height: 250px; display:none;">
                                <p id="m_no_image" class="text-muted mt-5">No Image Available</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center p-0 position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2 bg-white" data-bs-dismiss="modal"></button>
                    <img src="" id="preview_img_full" class="img-fluid rounded shadow-lg" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logic for DETAILS Modal
        const viewModal = document.getElementById('viewModal');
        viewModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            document.getElementById('m_id').textContent = button.getAttribute('data-id');
            document.getElementById('m_visitor').textContent = button.getAttribute('data-visitor');
            document.getElementById('m_mobile').textContent = button.getAttribute('data-mobile');
            document.getElementById('m_vehicle').textContent = button.getAttribute('data-vehicle');
            document.getElementById('m_purpose').textContent = button.getAttribute('data-purpose');
            document.getElementById('m_material').textContent = button.getAttribute('data-material');
            document.getElementById('m_entry').textContent = button.getAttribute('data-entry');
            document.getElementById('m_exit').textContent = button.getAttribute('data-exit');
            document.getElementById('m_user').textContent = button.getAttribute('data-user');

            const imgName = button.getAttribute('data-image');
            const imgEl = document.getElementById('m_image');
            const noImgEl = document.getElementById('m_no_image');
            if (imgName) {
                imgEl.src = "uploads/" + imgName; imgEl.style.display = 'block'; noImgEl.style.display = 'none';
            } else {
                imgEl.src = ""; imgEl.style.display = 'none'; noImgEl.style.display = 'block';
            }
        });

        // Logic for IMAGE PREVIEW Modal
        const imgModal = document.getElementById('imagePreviewModal');
        imgModal.addEventListener('show.bs.modal', event => {
            const trigger = event.relatedTarget;
            const src = trigger.getAttribute('data-src');
            document.getElementById('preview_img_full').src = src;
        });
    </script>
</body>
</html>