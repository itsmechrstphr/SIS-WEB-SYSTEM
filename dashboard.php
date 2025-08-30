<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user role and redirect to appropriate dashboard
$role = $_SESSION['role'];

switch ($role) {
    case 'admin':
        header('Location: admin_dashboard.php');
        break;
    case 'faculty':
        header('Location: faculty_dashboard.php');
        break;
    case 'student':
        header('Location: student_dashboard.php');
        break;
    default:
        header('Location: index.php?error=Invalid user role');
        break;
}
exit();
?>
