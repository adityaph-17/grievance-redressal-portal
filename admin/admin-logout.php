<?php
session_start();
session_unset();
session_destroy();

// Prevent browser back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

header("Location: /grievance-portal/index.html");
exit();
?>