<?php
session_start();
require "db.php";
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Redirect if no signup data
if (!isset($_SESSION['signup_data'])) {
    header("Location: std-signup.php");
    exit();
}

// OTP Verification
if (isset($_POST['verify_otp'])) {

    $entered_otp = trim($_POST['otp_input']);
    $signup_data = $_SESSION['signup_data'];
    $email = $signup_data['email'];

    // Verify OTP
    if ($entered_otp == $signup_data['otp']) {

      $sql = "INSERT INTO grievance_std_CO_db 
        (name, student_id, email, course, password) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $signup_data['full_name'],
    $signup_data['student_id'],
    $signup_data['email'],
    $signup_data['course'],
    $signup_data['password']
);

if (mysqli_stmt_execute($stmt)) {

            echo "<script>
                    alert('✅ Signup Successful!');
                    window.location.href='std-login.html';
                  </script>";

            ob_flush();
            flush();

            // ---------------- SEND EMAIL ----------------
            try {
                $mail = new PHPMailer(true);

                // SMTP config
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = ‘YOUR_EMAIL';
                $mail->Password   = ‘YOUR_EMAIL_PASSWORD';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                // Email headers
                $mail->setFrom('halneaditya07@gmail.com', 'College Grievance Portal');
                $mail->addAddress($email);
                $mail->isHTML(true);

                // Email Content
                $mail->Subject = "Signup Successful - Grievance Portal";
                $mail->Body = "
Hello {$signup_data['full_name']},<br><br>
Your account has been successfully created on the College Grievance Portal.<br>
Student ID: <strong>{$signup_data['student_id']}</strong><br>
Course: <strong>{$signup_data['course']}</strong><br><br>
- College Grievance Portal
";

                $mail->send();
            } catch (Exception $e) {
                // Email failed silently
            }
        } else {
            echo "DB Error: " . mysqli_error($conn);
        }

        unset($_SESSION['signup_data']); // Delete OTP session
      mysqli_stmt_close($stmt);
    } else {
        echo "<script>
            alert('❌ Incorrect OTP, try again');
            window.history.back();
        </script>";
    }
}
?>

<!-- OTP Form HTML -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="css/std-login-style.css">
</head>

<body>

 <header class="top-bar">
    <div class="logo-area">
      <img src="/grievance-portal/images/Logo1-removebg-preview.png" alt="College Logo" class="logo-icon">
      <span class="logo-text">Govt Polytechnic Jintur</span>
    </div>
  </header>

    <section class="login-section">
        <div class="login-card">
            <h2>Verify Signup OTP</h2>

            <form action="" method="post">
                <h3>Enter OTP</h3>
                <p>An OTP was sent to your email.</p>

                <input type="text" name="otp_input" placeholder="Enter OTP" required>

                <button type="submit" name="verify_otp" class="btn-primary">Verify OTP</button>
            </form>

        </div>
    </section>
</body>

</html>