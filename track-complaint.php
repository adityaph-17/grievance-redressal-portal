<?php
session_start();
require 'db.php'; // centralized DB connection

// If not logged in, redirect to login page
if (!isset($_SESSION['student_id'])) {
    header("Location: std-login.html");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch all complaints of logged-in student
$stmt = $conn->prepare("SELECT * FROM complaints WHERE student_id = ? ORDER BY submitted_at DESC");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$complaints = $stmt->get_result();

// Summary counts
$total = $pending = $progress = $resolved = $rejected = 0;

$rows = [];
while ($row = $complaints->fetch_assoc()) {
    $total++;
    if ($row['status'] == "Pending") $pending++;
    elseif ($row['status'] == "In Progress") $progress++;
    elseif ($row['status'] == "Resolved") $resolved++;
    elseif ($row['status'] == "Rejected") $rejected++;
    elseif ($row['status'] == "Reopened") $progress++;

    $rows[] = $row; // store for table
}

// Reset pointer for table display
$complaints->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Complaints</title>
    <link rel="stylesheet" href="css/track-complaint.css">
</head>
<body>

<!-- Top Bar -->
<header class="top-bar">
    <div class="logo-area">
        <img src="/grievance-portal/images/Logo1-removebg-preview.png" class="logo-icon">
        <span class="logo-text">Govt Polytechnic Jintur</span>
    </div>

    <div class="top-title-buttons">
        <a href="std-dashboard.php"><button class="btn primary">Back</button></a>
    </div>
</header>

<!-- Welcome Section -->
<section class="hero">
    <h1>Your Complaint Dashboard</h1>
    <p>Track your complaints, submit new ones, and view updates instantly.</p>
    <a href="submit-complaint.php"><button class="btn primary">Submit New Complaint</button></a>
</section>

<!-- Summary Section -->
<section class="section">
    <h2>Your Complaint Summary</h2><br>
    <div class="card-grid">
        <div class="card">
            <h3>Total Complaints</h3>
            <p class="stat-number"><?php echo $total; ?></p>
        </div>
        <div class="card">
            <h3>Pending</h3>
            <p class="stat-number pending"><?php echo $pending; ?></p>
        </div>
        <div class="card">
            <h3>In Progress</h3>
            <p class="stat-number progress"><?php echo $progress; ?></p>
        </div>
        <div class="card">
            <h3>Resolved</h3>
            <p class="stat-number resolved"><?php echo $resolved; ?></p>
        </div>
        <div class="card">
            <h3>Rejected</h3>
            <p class="stat-number rejected"><?php echo $rejected; ?></p>
        </div>
    </div>
</section>

<!-- Complaints Table -->
<section class="section">
    <h2>Your Complaints</h2><br>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Submitted On</th>
                    <th>Attachment</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td><?= htmlspecialchars($row['complaint_category']); ?></td>

                    <td>
                        <span class="status 
                            <?php 
                                if ($row['status'] == 'Pending') echo 'pending';
                                elseif ($row['status'] == 'In Progress') echo 'progress';
                                elseif ($row['status'] == 'Rejected') echo 'rejected';
                                elseif ($row['status'] == 'Reopened') echo 'progress';
                                else echo 'resolved';
                            ?>">
                            <?= $row['status']; ?>
                        </span>
                    </td>

                    <td><?= date("d M Y", strtotime($row['submitted_at'])); ?></td>

                    <td>
                        <?php if (!empty($row['attachment'])) { ?>
                            <a href="uploads/<?= $row['attachment'] ?>" target="_blank">View</a>
                        <?php } else { ?>
                            —
                        <?php } ?>
                    </td>

                    <td>
                        <?php if ($row['status'] == 'Resolved') { ?>
                        <form action="reopen-complaint.php" method="POST">
                            <input type="hidden" name="complaint_id" value="<?= $row['id']; ?>">
                            <textarea name="reopen_reason" placeholder="Reason for reopening" required></textarea>
                            <button type="submit" class="btn primary">Reopen</button>
                        </form>
                        <?php } elseif ($row['status'] == 'Reopened') { ?>
                            <span>Reopened</span>
                        <?php } else { ?>
                            <span>—</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>

        </table>
    </div>
</section>

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

</body>
</html>