<?php
session_start();
require "db.php";

if (isset($_POST["send_otp"])) {

    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);

    // Verify student_id + email
    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM grievance_std_CO_db WHERE student_id = ? AND email = ?"
    );
    mysqli_stmt_bind_param($stmt, "ss", $student_id, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        
        $student = mysqli_fetch_assoc($result);

        // Save details to session
        $_SESSION['reset_student_id'] = $student['student_id'];
        $_SESSION['reset_email']      = $student['email'];

        // Redirect to OTP verification
        header("Location: forgot-password-verify.php");
        exit();

    } else {
        echo "<script>alert('Invalid Student ID or Email'); window.history.back();</script>";
    }
}
?>


<!-- OTP Form HTML forgot password -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Verification</title>
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

      <form action="" method="post">
        <h2>FORGOT PASSWORD RESET</h2>
        <h3>Enter Details</h3>
         <label for="student_id">Student ID [Enrollment]</label>
        <input type="text" name="student_id" id="student_id" required placeholder="Enter your student ID">

         <label for="email">Email</label>
        <input type="email" name="email" placeholder="Enter your registered email" required>



        <button type="submit" name="send_otp" class="btn-primary">Send OTP</button>
      </form>

    </div>
  </section>
</body>
</html>