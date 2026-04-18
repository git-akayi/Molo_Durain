<?php
session_start();
include("../config/config.php");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $loginQuery = "SELECT `id`, `uuid`, `username`, `password`, `role` FROM `user` WHERE `username` = ? LIMIT 1";
    $stmt = $conn->prepare($loginQuery);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();

            if (password_verify($password, $data['password'])) {
                
                session_regenerate_id(true); 

                $_SESSION['user_id'] = $data['id']; 
                $_SESSION['userRole'] = $data['role'];
                $_SESSION['authUser'] = [
                    'user_id' => $data['id'],
                    'uuid' => $data['uuid'],
                    'username' => $data['username']
                ];

                // The Traffic Cop - Redirect based on role
                if ($data['role'] === 'admin') {
                    
                    // Admin Activity Log
                    $action = "Logged into the system";
                    $logQuery = "INSERT INTO `activity_logs` (`admin_id`, `action`) VALUES (?, ?)";
                    $logStmt = $conn->prepare($logQuery);
                    if ($logStmt) {
                        $logStmt->bind_param("is", $data['id'], $action); 
                        $logStmt->execute();
                        $logStmt->close();
                    }

                    header("Location: ../../public/admin/index.php");
                    exit();
                    
                } else {
                    
                    // --- FIXED: ONLY NORMAL USERS GET LOGGED IN THE VISITS TABLE ---
                    $visitQuery = "INSERT INTO `user_visits` (`user_id`) VALUES (?)";
                    $visitStmt = $conn->prepare($visitQuery);
                    if ($visitStmt) {
                        $visitStmt->bind_param("i", $data['id']);
                        $visitStmt->execute();
                        $visitStmt->close();
                    }
                    // ---------------------------------------------------------------

                    header("Location: ../../public/user/index.php");
                    exit();
                }

            } else {
                $_SESSION['error'] = "Invalid username or password.";
                header("Location: ../../public/login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid username or password.";
            header("Location: ../../public/login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "A database error occurred. Please try again.";
        header("Location: ../../public/login.php");
        exit();
    }
}
?>