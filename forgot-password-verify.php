<?php
session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If user directly opens this page without form
if (!isset($_SESSION['reset_student_id']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$full_name = $_SESSION['full_name'];

// Step 1: Generate & send OTP on first load
if (!isset($_SESSION['sent_otp'])) {

    $otp = rand(100000, 999999);

    $_SESSION['sent_otp'] = $otp;

    // Send Email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = ‘YOUR_EMAIL';
        $mail->Password   = ‘YOUR_EMAIL_PASSWORD';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('halneaditya07@gmail.com', 'College Grievance Portal');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Your Password Reset OTP";
        $mail->Body = "
    <h3>Hello,</h3>
    <p>Your OTP for resetting your College Grievance Portal password is:</p>
    <h2 style='color: #b30000;'>$otp</h2>
    <p>Please do NOT share this OTP with anyone. It is valid for 10 minutes.</p>
    <br>
    <p>Regards,<br><strong>College Grievance Portal</strong></p>
";

        $mail->send();
        echo "<script>alert('OTP send to email!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('OTP sending failed!');</script>";
    }
}


// Step 2: Verify OTP
if (isset($_POST["verify_otp"])) {

    $user_otp = trim($_POST['otp']);

    if ($user_otp == $_SESSION['sent_otp']) {

        // OTP successful
        unset($_SESSION['sent_otp']); // remove old otp

        header("Location: forgot-password-new.php");
        exit();
    } else {
        echo "<script>alert('Invalid OTP');</script>";
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
            <h2>Verify OTP</h2>

            <form action="" method="post">
                <label>Enter OTP</label>
                <input type="text" name="otp" required placeholder="Enter the OTP sent to your email">
                <button type="submit" name="verify_otp" class="btn-primary">Verify OTP</button>
            </form>
        </div>
    </section>

</body>

</html>