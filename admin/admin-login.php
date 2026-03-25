<?php
session_start();  // IMPORTANT
if (isset($_POST['login'])) {

    $admin_id = trim($_POST['admin_id']);
    $password = trim($_POST['password']);

    // Fixed credentials
    $correctAdminID = "admin";
    $correctPassword = "admin@123";

    if ($admin_id === $correctAdminID && $password === $correctPassword) {
        // CREATE ADMIN SESSION
        $_SESSION['admin_id'] = $admin_id;

        echo "<script>
                alert('Login Successful!');
                window.location.href='admin-dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Invalid Admin ID or Password : LOGIN UNSUCCESSFUL');
                window.history.back();
              </script>";
    }
}
?>