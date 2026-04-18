<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to view the E-Gallery.";
    header("Location: ../../public/login.php");
    exit();
} 

else if ($_SESSION['userRole'] === 'admin') {
    header("Location: ../../public/admin/index.php");
    exit();
}


// Inactivity timeout logic
$timeout_duration = 180; 

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        session_unset();     
        session_destroy();  
        
        session_start(); 
        $_SESSION['error'] = "You were automatically logged out due to 3 minutes of inactivity.";
        header("Location: ../../public/login.php");
        exit();
    }
}

$_SESSION['last_activity'] = time();
?>