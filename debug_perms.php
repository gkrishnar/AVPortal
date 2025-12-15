<?php
// debug_perms.php
require_once 'config.php';
checkLogin();

$user_id = $_SESSION['user_id'];
echo "<h1>Permission Debugger</h1>";
echo "<p><strong>Logged in User ID:</strong> $user_id</p>";
echo "<p><strong>Username:</strong> " . $_SESSION['username'] . "</p>";

// 1. Check Session Permissions
echo "<h3>1. Permissions currently in Session:</h3>";
if (isset($_SESSION['permissions'])) {
    echo "<pre>" . print_r($_SESSION['permissions'], true) . "</pre>";
} else {
    echo "<p style='color:red;'>No permissions found in Session.</p>";
}

// 2. Check Database Permissions (The Source of Truth)
echo "<h3>2. Permissions found in Database for User #$user_id:</h3>";
$sql = "SELECT p.perm_key, p.description 
        FROM permissions p 
        JOIN user_permissions up ON p.id = up.permission_id 
        WHERE up.user_id = $user_id";
$res = $conn->query($sql);

if ($res->num_rows > 0) {
    echo "<ul>";
    while ($row = $res->fetch_assoc()) {
        echo "<li><strong>" . $row['perm_key'] . "</strong> (" . $row['description'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red;'>No permissions found in Database for this user!</p>";
}

// 3. Test the specific Key
$test_key = 'create_gatepass';
echo "<h3>3. Testing Key: '$test_key'</h3>";
if (hasPermission($test_key)) {
    echo "<h2 style='color:green;'>SUCCESS: System says YES, you have access.</h2>";
} else {
    echo "<h2 style='color:red;'>FAILED: System says NO access.</h2>";
    echo "<p><strong>Fix:</strong> Ensure '$test_key' is in the list above.</p>";
}
?>