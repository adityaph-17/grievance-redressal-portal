<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin-login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "grievance_db");
if ($conn->connect_error) die("Connection Failed: " . $conn->connect_error);

// ---------------- DELETE OLD RESOLVED COMPLAINTS ----------------
if (isset($_POST['delete_old_resolved'])) {
    $delete_sql = "DELETE FROM complaints 
                   WHERE status='Resolved' 
                   AND submitted_at <= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>
                alert('All resolved complaints older than 1 YEAR have been deleted.');
                window.location.href='admin-dashboard.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Error deleting old complaints: ".$conn->error."');
              </script>";
    }
}

// ---------------- DELETE OLD REJECTED COMPLAINTS ----------------
if (isset($_POST['delete_old_rejected'])) {
    $delete_sql = "DELETE FROM complaints 
                   WHERE status='Rejected' 
                   AND submitted_at <= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>
                alert('All rejected complaints older than 1 YEAR have been deleted.');
                window.location.href='admin-dashboard.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Error deleting old complaints: ".$conn->error."');
              </script>";
    }
}

// ---------------- UPDATE STATUS ----------------
if (isset($_POST['update_status'])) {
    $cid = $_POST['complaint_id'];
    $new_status = $_POST['status'];

    $update = $conn->prepare("UPDATE complaints SET status=? WHERE id=?");
    $update->bind_param("si", $new_status, $cid);
    $update->execute();

    header("Location: admin-dashboard.php");
    exit();
}

// ---------------- SEARCH + FILTER + SHOW REOPENED ----------------
$search = $_GET['search'] ?? "";
$filter = $_GET['filter'] ?? "All";
$show_reopened = $_GET['show'] ?? "";

$query = "SELECT * FROM complaints WHERE 1";

// Filter for reopened complaints only
if ($show_reopened === "reopened") {
    $query .= " AND status='Reopened'";
} else {
    if (!empty($search)) {
        $query .= " AND id=" . intval($search);
    }

    if ($filter !== "All") {
        $query .= " AND status='" . $conn->real_escape_string($filter) . "'";
    }
}

$query .= " ORDER BY submitted_at DESC";
$complaints = $conn->query($query);

// ---------------- SUMMARY COUNTS ----------------
$total = $pending = $progress = $resolved = $rejected = $reopened = 0;

$result_all = $conn->query("SELECT * FROM complaints");
while ($row_summary = $result_all->fetch_assoc()) {
    $total++;
    switch ($row_summary['status']) {
        case "Pending": $pending++; break;
        case "In Progress": $progress++; break;
        case "Resolved": $resolved++; break;
        case "Rejected": $rejected++; break;
        case "Reopened": $reopened++; break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Manage Complaints</title>
<link rel="stylesheet" href="/grievance-portal/css/track-complaint.css">
<style>
    .reopened-row { background-color: #fff3cd; } /* Light yellow for reopened */
    .table-container table th, .table-container table td { text-align: center; vertical-align: middle; }
</style>
</head>
<body>

<!-- Top Bar -->
<header class="top-bar">
    <div class="logo-area">
        <img src="/grievance-portal/images/Logo1-removebg-preview.png" class="logo-icon">
        <span class="logo-text">Govt Polytechnic Jintur</span>
    </div>
    <div class="logo-area">
        <h3>Admin Dashboard</h3>
    </div>
    <div class="top-title-buttons">
        <a href="admin-logout.php" onclick="return confirm('Are you sure you want to logout?');">
            <button class="btn primary">Logout</button>
        </a>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <h1>Complaint Management</h1>
    <p>View, track, and update complaints submitted by students.</p>
</section>

<!-- Summary Cards -->
<section class="section">
    <h2>Summary</h2><br>
    <div class="card-grid">
        <div class="card"><h3>Total Complaints</h3><p class="stat-number"><?= $total; ?></p></div>
        <div class="card"><h3>Pending</h3><p class="stat-number pending"><?= $pending; ?></p></div>
        <div class="card"><h3>In Progress</h3><p class="stat-number progress"><?= $progress; ?></p></div>
        <div class="card"><h3>Resolved</h3><p class="stat-number resolved"><?= $resolved; ?></p></div>
        <div class="card"><h3>Rejected</h3><p class="stat-number rejected"><?= $rejected; ?></p></div>
        <div class="card"><h3>Reopened</h3><p class="stat-number reopened"><?= $reopened; ?></p></div>
    </div>
</section>



<!-- Delete Old Complaints Buttons -->
<section class="section">
    <form method="POST" action="">
        <button class="btn primary" name="delete_old_resolved" style="color: #FFA500;"
            onclick="return confirm('⚠️ Are you sure you want to delete all resolved complaints older than 1 YEAR?');">
            Delete RESOLVED Complaints Older Than 1 YEAR
        </button>
    </form><br>
    <form method="POST" action="">
        <button class="btn primary" name="delete_old_rejected" style="color: #FFD700;"
            onclick="return confirm('Are you sure you want to delete all rejected complaints older than 1 YEAR?');">
            Delete REJECTED Complaints Older Than 1 YEAR
        </button>
    </form>
</section>

<!-- Search + Filter Section -->
<section class="section">
    <h2>Search and Filter</h2><br>
    <form class="search-filter-bar" method="GET" action="">
        <input type="text" name="search" placeholder="Search by Complaint ID" value="<?= htmlspecialchars($search); ?>">
        <button class="btn primary" type="submit" name="search_btn">Search</button>

        <select name="filter">
            <option value="All" <?= ($filter=="All")?"selected":"" ?>>All</option>
            <option value="Pending" <?= ($filter=="Pending")?"selected":"" ?>>Pending</option>
            <option value="In Progress" <?= ($filter=="In Progress")?"selected":"" ?>>In Progress</option>
            <option value="Resolved" <?= ($filter=="Resolved")?"selected":"" ?>>Resolved</option>
            <option value="Rejected" <?= ($filter=="Rejected")?"selected":"" ?>>Rejected</option>
            <option value="Reopened" <?= ($filter=="Reopened")?"selected":"" ?>>Reopened</option>
        </select>
        <button class="btn primary" type="submit" name="filter_btn">Apply</button>
    </form>
</section>

<!-- Complaints Table -->
<section class="section">
    <h2>All Complaints</h2><br>
    <!-- Show Reopened Complaints Button -->
<section class="section">
    <a href="admin-dashboard.php?show=reopened"><button class="btn primary">Show Reopened Complaints</button></a><br><br>
    <a href="admin-dashboard.php"><button class="btn secondary">Show All Complaints</button></a>
</section>

    <div class="table-container">
        <table>
           <thead>
    <tr>
        <th>Complaint ID</th>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Title</th>
        <th>Category</th>
        <th>Description</th>
        <th>Attachment</th>
        <th>Status</th>
        <th>Reopen Reason</th>
        <th>Reopened At</th>
        <th>Submitted On</th>
        <th>Change Status</th>
    </tr>
</thead>
           <tbody>
<?php while($row = $complaints->fetch_assoc()) { 
    $row_class = ($row['status']=='Reopened') ? 'reopened-row' : '';
?>
<tr class="<?= $row_class; ?>">
    <td><?= $row['id']; ?></td>
    <td><?= $row['student_id']; ?></td>
    <td><?= $row['full_name']; ?></td>
    <td><?= htmlspecialchars($row['title']); ?></td>
    <td><?= htmlspecialchars($row['complaint_category']); ?></td>
    <td><?= htmlspecialchars($row['description']); ?></td>
    <td>
        <?php if (!empty($row['attachment'])) { ?>
            <a href="../uploads/<?= $row['attachment']; ?>" target="_blank">View</a>
        <?php } else { echo "—"; } ?>
    </td>
    <td>
        <span class="status 
            <?php 
                if ($row['status']=="Pending") echo 'pending';
                elseif ($row['status']=="In Progress") echo 'progress';
                elseif ($row['status']=="Rejected") echo 'rejected';
                elseif ($row['status']=="Reopened") echo 'reopened';
                else echo 'resolved';
            ?>">
            <?= $row['status']; ?>
        </span>
    </td>
    <!-- New Columns for Reopen -->
    <td><?= !empty($row['reopen_reason']) ? htmlspecialchars($row['reopen_reason']) : "—"; ?></td>
    <td><?= !empty($row['reopened_at']) ? date("d M Y", strtotime($row['reopened_at'])) : "—"; ?></td>

    <td><?= date("d M Y", strtotime($row['submitted_at'])); ?></td>
    <td>
        <form method="POST" class="status-form">
            <input type="hidden" name="complaint_id" value="<?= $row['id']; ?>">
            <select name="status">
                <option value="Pending" <?= ($row['status']=="Pending")?"selected":"" ?>>Pending</option>
                <option value="In Progress" <?= ($row['status']=="In Progress")?"selected":"" ?>>In Progress</option>
                <option value="Resolved" <?= ($row['status']=="Resolved")?"selected":"" ?>>Resolved</option>
                <option value="Rejected" <?= ($row['status']=="Rejected")?"selected":"" ?>>Rejected</option>
            </select>
            <button class="btn small" name="update_status" type="submit">Update</button>
        </form>
    </td>
</tr>
<?php } ?>
</tbody>
        </table>
    </div>
</section>

<footer class="footer">
</footer>
</body>
</html>