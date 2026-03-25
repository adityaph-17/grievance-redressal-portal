    <?php
    // Database connection
    $host = "localhost";
    $username = "root";
    $db_password = "";
    $db = "grievance_db";

    $conn = mysqli_connect($host, $username, $db_password, $db);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    ?>