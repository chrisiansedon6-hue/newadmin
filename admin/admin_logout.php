<?php
include 'connect.php';
session_start();

// Clear session variables
$_SESSION = [];

// Destroy session
session_unset();
session_destroy();

// Optional: Set a logout message (if you use flash messages)
session_start();
$_SESSION['message'] = 'You have been logged out successfully.';

// Redirect to login page
header('location:index.php');
exit();
?>