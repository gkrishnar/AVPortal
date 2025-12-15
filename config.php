<?php
session_start();

$host = 'localhost';
$db   = 'avagro_portal';
$user = 'avagro_portal';
$pass = 'ohl#?pskKGYAnzed'; // Your DB password

$conn = new mysqli($host, $user, $pass, $db);

// Check if connection already exists to avoid reconnecting
if (!isset($conn)) {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

// --- THIS IS THE FIX ---
// Only define the function if it doesn't exist yet
if (!function_exists('logAudit')) {
    function logAudit($conn, $userId, $action, $details) {
        $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $action, $details);
        $stmt->execute();
    }
}

// Helper functions (Wrap these too if they cause errors, but usually logAudit is the culprit)
if (!function_exists('checkLogin')) {
    function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
    }
}

if (!function_exists('checkAdmin')) {
    function checkAdmin() {
        if ($_SESSION['role'] !== 'admin') {
            die("<div class='alert alert-danger'>Access Denied. Admins only.</div>");
        }
    }
}


// ... [Existing connection code] ...

if (!function_exists('hasPermission')) {
    // Check if the logged-in user has a specific permission
    function hasPermission($key) {
        // Super Admin (ID 1) always has access (Optional safeguard)
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1) return true;

        if (isset($_SESSION['permissions']) && in_array($key, $_SESSION['permissions'])) {
            return true;
        }
        return false;
    }
}

// Helper to strictly block access to a page with a nice UI
if (!function_exists('requirePermission')) {
    function requirePermission($key) {
        if (!hasPermission($key)) {
            // Output a styled HTML Error Page
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Access Denied</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
            </head>
            <body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
                
                <div class="card shadow-lg border-0 rounded-4" style="max-width: 450px; width: 100%;">
                    <div class="card-body text-center p-5">
                        
                        <div class="mb-4">
                            <i class="bi bi-shield-lock-fill text-danger" style="font-size: 5rem;"></i>
                        </div>

                        <h2 class="fw-bold text-dark">Access Denied</h2>
                        
                        <p class="text-muted mt-3">
                            You do not have the required permissions to access this feature.
                        </p>

                        <div class="alert alert-danger py-2 mt-3 d-inline-block">
                            <i class="bi bi-code-slash me-1"></i> Missing: <strong>' . htmlspecialchars($key) . '</strong>
                        </div>

                        <div class="mt-4">
                            <a href="index.php" class="btn btn-dark w-100 py-2 fw-bold">
                                <i class="bi bi-arrow-left me-2"></i> Go Back to Dashboard
                            </a>
                        </div>

                    </div>
                </div>

            </body>
            </html>';
            
            // Stop script execution immediately
            exit(); 
        }
    }
}
?>