<?php
require_once 'config.php';
$msg = ""; $err = "";

if (isset($_POST['send_reset'])) {
    $email = $_POST['email'];
    
    // 1. Check if email exists
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($row = $res->fetch_assoc()) {
        // 2. Generate Token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
        
        // 3. Save Token
        $upd = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE email=?");
        $upd->bind_param("sss", $token, $expires, $email);
        $upd->execute();
        
        // 4. PREPARE EMAIL
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
        
        $to = $email;
        $subject = "Password Reset Request";
        
        // HTML Message
        $message = "
        <html>
        <head>
          <title>Password Reset</title>
        </head>
        <body>
          <p>Hi " . htmlspecialchars($row['username']) . ",</p>
          <p>You requested a password reset. Click the link below to set a new password:</p>
          <p><a href='" . $reset_link . "' style='background:#0d6efd;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Reset Password</a></p>
          <p><small>This link expires in 1 hour.</small></p>
        </body>
        </html>
        ";

        // IMPORTANT: Headers to avoid Spam
        // REPLACE 'info@ashtavinayak-agro.com' with a REAL email created in your cPanel
        $from_email = "krishna@ashtavinayak-agro.com"; 
        
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Ashtavinayak Portal <" . $from_email . ">" . "\r\n";
        $headers .= "Reply-To: " . $from_email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // 5. SEND
        if (mail($to, $subject, $message, $headers)) {
            $msg = "Reset link sent to your email. (Check Spam folder too)";
        } else {
            // ERROR HANDLER
            $err = "Server failed to send email.";
            
            // --- DEBUG MODE (Remove this line when live!) ---
            $err .= "<br><strong>DEBUG LINK:</strong> <a href='$reset_link'>Click here to Reset</a>";
        }
    } else {
        $err = "Email not found in system.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h4 class="text-center mb-3">Forgot Password</h4>
        
        <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
        <?php if($err) echo "<div class='alert alert-danger'>$err</div>"; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Enter Registered Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" name="send_reset" class="btn btn-primary w-100">Send Reset Link</button>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>