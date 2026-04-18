<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to access the Admin Dashboard.";
    header("Location: ../../public/login.php");
    exit();
} 

else if ($_SESSION['userRole'] !== 'admin') {
    $_SESSION['error'] = "Access denied. You do not have admin permissions.";
    header("Location: ../../public/login.php");
    exit();
}

