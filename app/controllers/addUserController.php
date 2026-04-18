<?php
session_start();
include("../config/config.php"); 

header('Content-Type: application/json');

// Function to securely generate a UUID
function generateUUID() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); 
    
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'user'; 
    if ($role !== 'admin') {
        $role = 'user'; 
    }

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and Password are required.']);
        exit();
    }

    // Check if user already exists
    $checkQuery = "SELECT id FROM `user` WHERE `username` = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This ID Number / Username already exists.']);
        exit();
    }
    $checkStmt->close();
    
    // Generate UUID
    $newUserId = generateUUID();

    // FIXED: Inserting the hashed password so new users can successfully log in securely!
    $insertQuery = "INSERT INTO `user` (`uuid`, `username`, `password`, `role`) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    if ($stmt) {
        $stmt->bind_param("ssss", $newUserId, $username, $password, $role);
        
        if ($stmt->execute()) {
            
            // --- NEW: LOG THE ACTIVITY ---
            // If an admin is logged in, log that they added a user
            if (isset($_SESSION['user_id'])) {
                $admin_id = $_SESSION['user_id'];
                $displayRole = $role === 'admin' ? 'Admin' : 'Student';
                $action = "Added new " . $displayRole . ": " . $username;
                
                $logQuery = "INSERT INTO `activity_logs` (`admin_id`, `action`) VALUES (?, ?)";
                $logStmt = $conn->prepare($logQuery);
                if ($logStmt) {
                    $logStmt->bind_param("is", $admin_id, $action);
                    $logStmt->execute();
                    $logStmt->close();
                }
            }
            // -----------------------------

            echo json_encode(['status' => 'success', 'message' => 'User successfully added!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save to database.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database query error.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>