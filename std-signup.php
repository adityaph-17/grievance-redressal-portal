<?php
session_start();
require "db.php";
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST["Signup"])) {

    // Sanitize inputs
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $student_id = trim($_POST['student_id']);
    $email = htmlspecialchars(trim($_POST['email']));
    $course = htmlspecialchars(trim($_POST['course']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Password match check
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match'); window.history.back();</script>";
        exit();
    }

    // ✅ Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check duplicates
   $check_stmt = mysqli_prepare($conn, "SELECT * FROM grievance_std_CO_db WHERE student_id=? OR email=?");
mysqli_stmt_bind_param($check_stmt, "ss", $student_id, $email);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    echo "<script>alert('Student ID or Email already exists'); window.history.back();</script>";
    exit();
}
mysqli_stmt_close($check_stmt);

    // Generate OTP
    $otp = rand(100000, 999999);

    // Store signup data & OTP in session
    $_SESSION['signup_data'] = [
        'full_name' => $full_name,
        'student_id' => $student_id,
        'email' => $email,
        'course' => $course,
        'password' => $hashed_password, // store hashed password
        'otp' => $otp,
    ];

    // Send OTP email
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
        $mail->Subject = "OTP Verification - Grievance Portal";
        
        // ✅ Simple and short email body
        $mail->Body = "
            Hello {$full_name},<br><br>
            Your OTP for account signup is: <strong>$otp</strong><br>
            Please do not share this OTP with anyone.<br><br>
            - College Grievance Portal
        ";

        $mail->send();

        // Redirect to OTP verification page
        echo "<script>
                alert('✅ OTP sent to your email!');
                window.location.href='signup-otp-verify.php';
              </script>";
        exit();

    } catch (Exception $e) {
        echo "<script>alert('❌ Email sending failed! Try again'); window.history.back();</script>";
    }

    mysqli_close($conn);
}
?>