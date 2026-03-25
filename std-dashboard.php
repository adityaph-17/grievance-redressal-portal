<?php
session_start();
require "db.php";

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
  header("Location: std-login.html");
  exit();
}

// Fetch student data
$student_id = $_SESSION['student_id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM grievance_std_CO_db WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$student_data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>College Residential Grievance Portal</title>
  <link rel="stylesheet" href="css/std-dashboard.css">
</head>

<body>
<div class="layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="profile-section">
      <div class="profile-pic">
        <span>👤</span>
      </div>
      <h3><?php echo htmlspecialchars($student_data['name']); ?></h3>
    </div>

    <hr>

    <nav class="sidebar-menu">
      <a href="std-dashboard.php">🏠 Dashboard</a>
      <a href="std-logout.php" onclick="return confirm('Are you sure you want to logout?');">🚪 Logout</a>
      <a href="std-delete.php" onclick="return confirm('Are you sure you want to Delete Account?');">❌ Delete Account</a>
       <a href="forgot-password.php" onclick="return confirm('Are you sure you want to Reset Account Password?');">🔄 Password Reset</a>
    </nav>
  </aside>

  <!-- Overlay (OUTSIDE sidebar) -->
  <div class="overlay" onclick="toggleSidebar()"></div>

  <!-- Main Content -->
  <main class="main-content">
  <!-- Top Bar -->
  <header class="top-bar">
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <div class="logo-area">
      <img src="/grievance-portal/images/Logo1-removebg-preview.png" alt="College Logo" class="logo-icon">
      <span class="logo-text">Govt Polytechnic Jintur</span>
    </div>

    <div class="top-title-buttons">
      <!-- <a href="std-logout.php" onclick="return confirm('Are you sure you want to logout?');">
        <button class="btn primary">Logout</button>
      </a> -->
      <!-- <a href="std-delete.php" onclick="return confirm('Are you sure you want to Delete Account?');">
        <button class="btn primary" name="Delete">Delete Account</button>
      </a>-->
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <h1>👋 Hello Student, <?php echo htmlspecialchars($student_data['name']); ?> </h1>
    <p>Track your complaints, submit new ones, and view updates instantly.</p>
    <a href="submit-complaint.php"><button class="btn primary">Submit Complaint</button></a>
    <a href="track-complaint.php"><button class="btn primary">Track complaints</button></a>
  </section>

  <section class="section how-it-works">
    <h2>Services</h2><br>
    <div class="steps">
      <div class="icon-row">
        <div><span>📝</span> Submit</div>
        <div><span>📊</span> Track</div>
        <div><span>🔔</span> Notifications</div>
        <div><span>📄</span> Acknowledgment</div>
        <div><span>☎️</span> Support</div>
      </div>
    </div>
  </section>

  <!-- How it Works -->
  <section class="section how-it-works ">
    <h2>How it Works</h2><br>
    <div class="steps">
      <div class="step">
        <div class="step-number">1</div>
        <p>Submit complaint</p>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <p>Admin reviews</p>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <p>Student tracks status</p>
      </div>
      <div class="step">
        <div class="step-number">4</div>
        <p> Resolution provided</p>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="section">
    <h2>Information</h2> <br>
    <div class="card-grid">

      <div class="card">
        <div class="icon-circle">
          <img src="images/month.png" alt="icon">
        </div>
        <h4>Complaints Resolved <br> within Month</h4>
      </div>

      <div class="card">
        <div class="icon-circle"><img src="images/download-removebg-preview.png" alt="icon"></div>
        <h4>Up to 9 Active <br> Categories</h4>
      </div>
      <div class="card">
        <div class="icon-circle"><img src="images/timely-response-rate-removebg-preview.png" alt="icon"></div>
        <h4>Avg Resolution Time <br> 3-5 Days</h4>
      </div>
      <div class="card">
        <div class="icon-circle"><img src="images/support-removebg-preview.png" alt="icon"></div>
        <h4>Support Availability <br>9 AM-5 PM</h4>
      </div>
    </div>
  </section>

  <!-- Acc Operation-->
  <!-- <section class="section how-it-works">
    <h2>Account Operation</h2><br>
    <div class="steps">
      <div class="icon-row">
        <a href="std-logout.php" onclick="return confirm('Are you sure you want to logout?');">
          <button class="btn primary">Logout</button>
        </a>
        <a href="std-delete.php" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');"><button class="btn primary">Delete Account</button></a>
      </div>
    </div>
  </section> -->

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-top">
      <div class="contact-support">
        <h2>Contact &amp; Support</h2>
        <p>office Number : 00000 00000 <br>Email : abc@gmail.com</p>
      </div>
      <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms &amp; Conditions</a>
        <a href="#">Help &amp; FAQ</a>
      </div>
    </div>
  </footer>

    </main>
</div>

<script>
  function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.overlay').classList.toggle('active');
  }
</script>

</body>

</html>