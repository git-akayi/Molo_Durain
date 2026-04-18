<?php
// 1. Start the session so PHP knows which session to destroy
session_start();

// 2. Clear all the session variables (like $_SESSION['user_id'])
session_unset();

// 3. Completely destroy the session
session_destroy();

// 4. Send them back to the unified login page
header("Location: ../../public/login.php");
exit();
?>