<?php
session_start();
require "db.php";

// Redirect if student not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: std-login.html");
    exit();
}

// Check form submission
if (isset($_POST['complaint_id'], $_POST['reopen_reason'])) {

    $complaint_id = $_POST['complaint_id'];
    $reason = trim($_POST['reopen_reason']);

    $sql = "UPDATE complaints 
            SET status = 'Reopened',
                reopen_reason = ?,
                reopened_at = NOW()
            WHERE id = ?";

      $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $reason, $complaint_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: track-complaint.php");
        exit();
    } else {
        echo "Failed to reopen complaint.";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request.";
}
?>