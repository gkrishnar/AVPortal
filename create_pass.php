<?php
// 1. Config & Settings
require_once 'config.php';
checkLogin();
//requirePermission('gatepass_create');

if (!function_exists('hasPermission')) {
    include 'access_denied.php';
    exit();
}

// 2. Check the specific key 'create_gatepass'
if (!hasPermission('create_gatepass')) {
    include 'access_denied.php';
    exit();
}


ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');


// 2. Fetch Purposes (UPDATED: Alphabetical Order)
$purposes = $conn->query("SELECT * FROM purposes ORDER BY purpose_name ASC");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $visitor = $_POST['visitor_name'];
    $mobile = $_POST['mobile_no'];
    $vehicle = $_POST['vehicle_no'];
    $material = $_POST['material_details'];
    $purpose_id = $_POST['purpose_id'];
    $user_id = $_SESSION['user_id'];
    $entry_time = date('Y-m-d H:i:s'); 

    // --- IMAGE UPLOAD LOGIC ---
    $image_filename = NULL;
    
    if (isset($_FILES['visitor_image']) && $_FILES['visitor_image']['error'] == 0) {
        $target_dir = "uploads/";
        $file_ext = pathinfo($_FILES["visitor_image"]["name"], PATHINFO_EXTENSION);
        $new_name = time() . "_" . rand(1000, 9999) . "." . $file_ext;
        $target_file = $target_dir . $new_name;
        
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array(strtolower($file_ext), $allowed_types)) {
            if (move_uploaded_file($_FILES["visitor_image"]["tmp_name"], $target_file)) {
                $image_filename = $new_name;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO gate_passes (visitor_name, mobile_no, vehicle_no, material_details, purpose_id, created_by, entry_time, visitor_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssiiss", $visitor, $mobile, $vehicle, $material, $purpose_id, $user_id, $entry_time, $image_filename);
            
            if ($stmt->execute()) {
                $success = "Gate Pass Created Successfully!";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "DB Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Pass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="card shadow-sm" style="max-width: 800px; margin: auto;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Create New Gate Pass</h5>
            </div>
            <div class="card-body">
                <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
                <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Visitor Name</label>
                            <input type="text" name="visitor_name" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile_no" class="form-control" required pattern="[0-9]{10}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Purpose</label>
                            <select name="purpose_id" class="form-select" required>
                                <option value="">Select Purpose</option>
                                <?php if($purposes) { $purposes->data_seek(0); while($r = $purposes->fetch_assoc()): ?>
                                    <option value="<?php echo $r['id']; ?>"><?php echo $r['purpose_name']; ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vehicle Number</label>
                            <input type="text" name="vehicle_no" class="form-control">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Visitor/ID Image</label>
                            <input type="file" name="visitor_image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Material Details</label>
                        <textarea name="material_details" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Generate Pass</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>