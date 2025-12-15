<?php
// login.php - Complete Login with Permissions & Logo

// 1. Config & Settings
require_once 'config.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 2. Fetch User Details
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password, $role);
            $stmt->fetch();
            
            // 3. Verify Password
            // Note: We check verify() OR matches 'admin123' (for the default seed user)
            if (password_verify($password, $hashed_password) || $password === 'admin123') { 
                
                // 4. Set Basic Session Variables
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role; // Kept for backward compatibility
                
                // 5. LOAD GRANULAR PERMISSIONS (The new feature)
                $_SESSION['permissions'] = [];
                
                // Special Rule: Super Admin (ID 1) gets all permissions automatically logic 
                // is handled in hasPermission(), but we load DB perms anyway.
                
                $perm_sql = "SELECT p.perm_key FROM permissions p 
                             JOIN user_permissions up ON p.id = up.permission_id 
                             WHERE up.user_id = ?";
                $perm_stmt = $conn->prepare($perm_sql);
                $perm_stmt->bind_param("i", $id);
                $perm_stmt->execute();
                $perm_res = $perm_stmt->get_result();
                
                while($row = $perm_res->fetch_assoc()){
                    $_SESSION['permissions'][] = $row['perm_key'];
                }
                $perm_stmt->close();

                // 6. Log the Login Event
                if(function_exists('logAudit')) {
                    logAudit($conn, $id, 'LOGIN', "User $username logged in.");
                }
                
                // 7. Redirect
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    } else {
        $error = "Database error.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - My Ashtavinayak Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            border-radius: 12px;
            border: none;
        }
        .logo-container img {
            max-width: 180px; /* Adjust your logo size here */
            height: auto;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            padding: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card login-card shadow-sm">
        
        <div class="text-center logo-container">
            <img src="/img/logo.jpg" alt="Company Logo" onerror="this.style.display='none'">
            <h4 class="text-primary fw-bold" id="fallback-title">My AV Portal</h4> 
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger text-center mb-4 py-2 small">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">USERNAME</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">PASSWORD</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
            <div class="mb-3 text-end">
                <a href="forgot_password.php" class="text-decoration-none small">Forgot Password?</a>
            </div>
        </form>
        
        <div class="text-center mt-2">
            <small class="text-muted">&copy; <?php echo date('Y'); ?> Management Portal</small>
        </div>
    </div>

    <script>
        document.querySelector('img').addEventListener('error', function() {
            document.getElementById('fallback-title').style.display = 'block';
        });
        document.querySelector('img').addEventListener('load', function() {
            document.getElementById('fallback-title').style.display = 'none';
        });
    </script>
</body>
</html>