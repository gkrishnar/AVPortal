<?php
// 1. Config & Settings
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

checkLogin();
requirePermission('gatepass_master');



// 2. Add Purpose Logic
if (isset($_POST['add'])) {
    $p_name = $_POST['purpose_name'];
    // Prevent empty entries
    if (!empty(trim($p_name))) {
        // Use prepared statement for security
        $stmt = $conn->prepare("INSERT INTO purposes (purpose_name) VALUES (?)");
        $stmt->bind_param("s", $p_name);
        $stmt->execute();
        
        if (function_exists('logAudit')) {
            logAudit($conn, $_SESSION['user_id'], 'ADD_PURPOSE', "Added purpose: $p_name");
        }
    }
}

// 3. Delete Purpose Logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Sanitize ID
    $conn->query("DELETE FROM purposes WHERE id=$id");
    
    if (function_exists('logAudit')) {
        logAudit($conn, $_SESSION['user_id'], 'DELETE_PURPOSE', "Deleted purpose ID: $id");
    }
    header("Location: purposes.php");
    exit();
}

// 4. Fetch Purposes (ALPHABETICAL ORDER)
$result = $conn->query("SELECT * FROM purposes ORDER BY purpose_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Purposes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Manage Purposes (Master)</h3>
            <a href="index.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
        </div>
        
        <div class="card p-3 mb-4 bg-light shadow-sm">
            <form method="post" class="d-flex gap-2">
                <input type="text" name="purpose_name" class="form-control" placeholder="Enter New Purpose" required>
                <button type="submit" name="add" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add</button>
            </form>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th>Purpose Name</th>
                    <th style="width: 15%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['purpose_name']); ?></td>
                        <td>
                            <a href="purposes.php?delete=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this purpose?')">
                               <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center text-muted">No purposes found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>