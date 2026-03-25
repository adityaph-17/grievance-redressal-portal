<?php
session_start();
require 'db.php'; // Use centralized DB connection

// Redirect if student not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: std-login.html");
    exit();
}

// Debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Session data
$full_name  = $_SESSION['full_name'];
$student_id = $_SESSION['student_id'];
$email      = $_SESSION['email'];
$course     = $_SESSION['course'];

// Initialize error message
$error_message = "";

if (isset($_POST["submit-complaint"])) {

    // Complaint data
    $category    = trim($_POST['complaint_category']);
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);

    /* ===============================
       FILE UPLOAD HANDLING
       =============================== */
    $attachment = NULL;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {

        $uploadDir = __DIR__ . "/uploads/";

        if (!is_dir($uploadDir)) {
            die("Failed to create uploads directory. Check permissions.");
        }

        $originalName = basename($_FILES['attachment']['name']);
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg','jpeg','png','pdf','doc','docx'];

        if (!in_array($fileExt, $allowedTypes)) {
            $error_message = "Invalid file type. Only JPG, PNG, PDF, DOC/DOCX allowed.";
        } else {
            // Check file size
            $fileSizeKB = $_FILES['attachment']['size'] / 1024; // size in KB
            if ($fileSizeKB < 20 || $fileSizeKB > 256) {
                $error_message = "File size must be between <span style='color:red;'>20 KB</span> and <span style='color:red;'>256 KB</span>. Your file is " . round($fileSizeKB, 2) . " KB.";
            } else {
                // Generate unique filename to avoid conflicts
                $fileName = time() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $originalName);
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
                    $attachment = $fileName;
                } else {
                    $error_message = "File upload failed. Check folder permissions.";
                }
            }
        }
    }

    // Only insert if no error
    if ($error_message === "") {
        $sql = "INSERT INTO complaints 
                (full_name, student_id, email, course, complaint_category, title, description, attachment)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssssss",
            $full_name,
            $student_id,
            $email,
            $course,
            $category,
            $title,
            $description,
            $attachment
        );

        if ($stmt->execute()) {
            echo "<script>
                    alert('Complaint Submitted Successfully!');
                    window.location.href='std-dashboard.php';
                  </script>";
            exit();
        } else {
            $error_message = "Error submitting complaint. Please try again.";
             die("Prepare failed: " . $stmt->error);
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Complaint</title>
<link rel="stylesheet" href="css/submit-complaint.css">
</head>
<body>

<header class="top-bar">
    <div class="logo-area">
        <img src="/grievance-portal/images/Logo1-removebg-preview.png" class="logo-icon">
        <span class="logo-text">Govt Polytechnic Jintur</span>
    </div>
</header>

<section class="complaint-section">
<div class="complaint-card">
<h2>Submit Your Complaint</h2>

<!-- Show file size or upload errors -->
<?php if($error_message !== "") { ?>
    <p style="color:red; font-weight:bold;"><?php echo $error_message; ?></p>
<?php } ?>

<form action="submit-complaint.php" method="post" enctype="multipart/form-data">

<hr><b>Student Information</b><hr>

<label>Full Name</label>
<input type="text" value="<?= htmlspecialchars($full_name) ?>" readonly>

<label>Student ID</label>
<input type="text" value="<?= htmlspecialchars($student_id) ?>" readonly>

<label>Email</label>
<input type="email" value="<?= htmlspecialchars($email) ?>" readonly>

<label>Course</label>
<input type="text" value="<?= htmlspecialchars($course) ?>" readonly>

<hr><b>Complaint Information</b><hr>

<label>Complaint Category</label>
<select name="complaint_category" required>
    <option value="">-- Select Complaint Category --</option>
    <option>Hostel & Residential</option>
    <option>Water / Electricity / Infrastructure</option>
    <option>Cleanliness & Hygiene</option>
    <option>Academic Issues</option>
    <option>Staff / Faculty Related</option>
    <option>Harassment, Ragging & Safety</option>
    <option>Administrative / Fee / Documentation</option>
    <option>Transport Issues</option>
    <option>IT / Technical Issues</option>
    <option>Other Complaints</option>
</select>

<label>Complaint Subject</label>
<input type="text" name="title" required>

<label>Complaint Description</label>
<textarea name="description" rows="4" maxlength="250" required></textarea>

<label>Upload Supporting Document (Optional)</label>
<input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">

<button type="submit" name="submit-complaint" class="btn-primary">
    Submit Complaint
</button>

<button type="button" class="btn-primary" onclick="window.location.href='std-dashboard.php'">
    Back
</button>
</form>
</div>
</section>

</body>
</html>