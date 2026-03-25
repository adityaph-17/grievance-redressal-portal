<?php
session_start();
require "db.php";
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: std-login.html");
    exit();
}

$session_student_id       = $_SESSION['student_id'];
$session_student_password = $_SESSION['password']; // hashed password from DB
$email                    = $_SESSION['email'];    // student email

/* -------------------------------------------------------------
   STEP 1 → SHOW ID + PASSWORD FORM
------------------------------------------------------------- */
if (!isset($_POST['verify']) && !isset($_POST['verify_otp'])):
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Account</title>
    <link rel="stylesheet" href="css/std-login-style.css">
</head>
<body>
<header class="top-bar">
    <div class="logo-area">
        <img src="images/Logo1-removebg-preview.png" class="logo-icon">
        <span class="logo-text">Govt Polytechnic Jintur</span>
    </div>
</header>

<div class="login-section">
    <div class="login-card">
        <h2 style="color:#b30000;">⚠ Delete Account</h2>
        <p style="color:red;">This will permanently delete your account and complaints.</p>

        <form method="POST">
            <input type="text" name="student_id_input" placeholder="Student ID" required>
            <input type="password" name="password_input" placeholder="Password" required>

            <button type="submit" name="verify" class="btn-primary">Verify & Send OTP</button>
        </form>
    </div>
</div>
</body>
</html>
<?php
exit();
endif;

/* -------------------------------------------------------------
   STEP 2 → CHECK ID & PASSWORD → SEND OTP
------------------------------------------------------------- */
if (isset($_POST['verify'])) {

    $student_id_input = trim($_POST['student_id_input']);
    $password_input   = trim($_POST['password_input']);

    // ✅ Use password_verify for hashed password
if ($student_id_input == $session_student_id && password_verify($password_input, $session_student_password)) {

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store OTP in session
        $_SESSION['delete_otp'] = $otp;

        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'halneaditya07@gmail.com';
            $mail->Password   = 'qnnmcnlpfyzinjky';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('halneaditya07@gmail.com', 'Grievance Portal');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Account Deletion OTP";
            $mail->Body = "
                Hello,<br><br>
                Your OTP for account deletion is: <strong>$otp</strong><br>
                Do NOT share this OTP with anyone.<br><br>
                - College Grievance Portal
            ";

            $mail->send();

             echo "<script>
                alert('✅ OTP sent to your email!');
              </script>";

        } catch (Exception $e) {
            echo "<script>alert('❌ Email sending failed!'); window.location.href='std-dashboard.php';</script>";
            exit();
        }

        // Show OTP form
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Verify OTP</title>
            <link rel="stylesheet" href="css/std-login-style.css">
        </head>
        <body>
        <header class="top-bar">
            <div class="logo-area">
                <img src="images/Logo1-removebg-preview.png" class="logo-icon">
                <span class="logo-text">Govt Polytechnic Jintur</span>
            </div>
        </header>

        <div class="login-section">
            <div class="login-card">
                <h2>Enter OTP</h2>
                <p>An OTP was sent to your email.</p>

                <form method="POST">
                    <input type="text" name="otp_input" placeholder="Enter OTP" required>
                    <button type="submit" name="verify_otp" class="btn-primary">Confirm Delete</button>
                </form>
            </div>
        </div>
        </body>
        </html>
        <?php
        exit();

    } else {
        echo "<script>
                alert('❌ Incorrect Student ID or Password!');
                window.location.href='std-delete.php';
              </script>";
        exit();
    }
}

/* -------------------------------------------------------------
   STEP 3 → VERIFY OTP → DELETE ACCOUNT
------------------------------------------------------------- */
if (isset($_POST['verify_otp'])) {

    $entered_otp = trim($_POST['otp_input']);
    $session_otp = $_SESSION['delete_otp'] ?? '';

    if ($entered_otp != $session_otp) {
        echo "<script>
                alert('❌ Invalid OTP! Account not deleted.');
                window.location.href='std-delete.php';
              </script>";
        exit();
    }

    // OTP Verified → Delete Account and complaints
    mysqli_begin_transaction($conn);

    try {
       // Delete complaints
$stmt1 = mysqli_prepare($conn, "DELETE FROM complaints WHERE student_id = ?");
mysqli_stmt_bind_param($stmt1, "s", $session_student_id);
mysqli_stmt_execute($stmt1);
mysqli_stmt_close($stmt1);

// Delete student account
$stmt2 = mysqli_prepare($conn, "DELETE FROM grievance_std_CO_db WHERE student_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt2, "s", $session_student_id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

        mysqli_commit($conn);

        // Send deletion confirmation email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'halneaditya07@gmail.com';
            $mail->Password   = 'qnnmcnlpfyzinjky';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('halneaditya07@gmail.com', 'Grievance Portal');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Account Deleted Successfully";
            $mail->Body = "
                Hello,<br><br>
                Your account has been permanently deleted.<br>
                All complaints associated with your ID have also been removed.<br><br>
                - College Grievance Portal
            ";

            $mail->send();
        } catch (Exception $e) {
            // silent
        }

        session_unset();
        session_destroy();

        echo "<script>
                alert('✅ Account and all complaints deleted successfully!');
                window.location.href='index.html';
              </script>";
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<h3>Error: " . $e->getMessage() . "</h3>";
        exit();
    }
}

mysqli_close($conn);
?>