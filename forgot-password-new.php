<?php
session_start();
require "db.php";

// Redirect if student not verified
if (!isset($_SESSION['reset_student_id'])) {
    header("Location: forgot-password.php");
    exit();
}

if (isset($_POST["reset_password"])) {

    $new_pass = trim($_POST['password']);
    $confirm_pass = trim($_POST['confirm_password']);

    if ($new_pass !== $confirm_pass) {
        echo "<script>alert('❌ Passwords do not match');</script>";
    } else {

        $student_id = $_SESSION['reset_student_id'];

        // Hash the new password securely
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE grievance_std_CO_db SET password = ? WHERE student_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "ss", $hashed_pass, $student_id);

        if (mysqli_stmt_execute($stmt)) {

            // Clear session
            unset($_SESSION['reset_student_id']);
            unset($_SESSION['reset_email']);

            echo "<script>
                    alert('✅ Password Updated Successfully!');
                    window.location='std-login.html';
                  </script>";
        } else {
            echo "<script>alert('❌ Error updating password');</script>";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Set New Password</title>
  <link rel="stylesheet" href="css/std-login-style.css">
</head>

<body>
  <!-- Header -->
  <header class="top-bar">
    <div class="logo-area">
      <img src="images/Logo1-removebg-preview.png" alt="College Logo" class="logo-icon">
      <span class="logo-text">Govt Polytechnic Jintur</span>
    </div>
  </header>
  
  <section class="login-section">
      <div class="login-card">
          <h2>Set New Password</h2>

        <form action="" method="post">
    <label for="password">New Password</label>
    <input type="password" name="password" id="password"
        placeholder="Enter strong password (A,a,0,@)"
        pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,15}$"
        title="Password must be 8-15 characters and include letters, numbers, and symbols"
        required>

    <label for="confirm_password">Confirm Password</label>
    <input type="password" name="confirm_password" id="confirm_password"
        placeholder="Re-enter password"
        required>

    <button type="submit" name="reset_password" class="btn-primary">Update Password</button>
</form>
      </div>
  </section>
</body>
</html>