<?php
require_once 'config.php';
$msg = ""; $err = "";
$token = isset($_GET['token']) ? $_GET['token'] : "";
$valid_token = false;

// 1. Verify Token on Load
if ($token) {
    $now = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > ?");
    $stmt->bind_param("ss", $token, $now);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $valid_token = true;
    } else {
        $err = "Invalid or expired link.";
    }
}

// 2. Handle Password Update
if (isset($_POST['update_pass']) && $valid_token) {
    $new_pass = $_POST['new_password'];
    $hash = password_hash($new_pass, PASSWORD_DEFAULT);
    
    // Update Password & Clear Token
    $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE reset_token=?");
    $stmt->bind_param("ss", $hash, $token);
    
    if ($stmt->execute()) {
        $msg = "Password updated successfully! <a href='login.php'>Login Now</a>";
        $valid_token = false; // Hide form after success
    } else {
        $err = "Database error.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h4 class="text-center mb-3">Reset Password</h4>
        
        <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
        <?php if($err) echo "<div class='alert alert-danger'>$err</div>"; ?>

        <?php if($valid_token): ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="text" name="new_password" class="form-control" required placeholder="Enter new password">
            </div>
            <button type="submit" name="update_pass" class="btn btn-success w-100">Update Password</button>
        </form>
        <?php elseif(!$msg): ?>
            <div class="text-center">
                <a href="login.php" class="btn btn-secondary">Back to Login</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>