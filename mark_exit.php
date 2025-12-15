<?php
// mark_exit.php

// 1. Set Timezone
date_default_timezone_set('Asia/Kolkata');

require 'config.php';
checkLogin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $exit_time = date('Y-m-d H:i:s');

    // 2. Update the record
    $stmt = $conn->prepare("UPDATE gate_passes SET exit_time = ? WHERE id = ?");
    $stmt->bind_param("si", $exit_time, $id);
    
    if ($stmt->execute()) {
        logAudit($conn, $_SESSION['user_id'], 'MARK_EXIT', "Marked exit for Pass ID: $id");
        // Redirect back to reports with success
        header("Location: reports.php?msg=Exit Marked Successfully");
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: reports.php");
}
?>