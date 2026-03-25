<?php
session_start(); // Start session
require "db.php"; // Centralized DB connection

if (isset($_POST["Login"])) {

    $student_id = trim($_POST['student_id']);
    $user_password = trim($_POST['password']);

    // ✅ Fetch user by student_id only
    $stmt = mysqli_prepare($conn, "SELECT * FROM grievance_std_CO_db WHERE student_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $student_data = mysqli_fetch_assoc($result);

        // ✅ Verify password
        if (password_verify($user_password, $student_data['password'])) {

            // Store student info in session
            $_SESSION['student_id'] = $student_data['student_id'];
            $_SESSION['full_name']  = $student_data['name'];
            $_SESSION['email']      = $student_data['email'];
            $_SESSION['course']     = $student_data['course'];
            $_SESSION['password']   = $student_data['password'];

            // Redirect to dashboard
            header("Location: std-dashboard.php");
            exit();

        } else {
            echo "<script>alert('❌ Invalid Student ID or Password'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('❌ Invalid Student ID or Password'); window.history.back();</script>";
    }

    mysqli_close($conn);
}
?>