<?php
session_start();
include("../config/config.php"); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type']; 
    $id = $_POST['id'];
    $itemName = $_POST['name']; // The actual name (e.g. "Engineering")

    if (empty($type) || empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing information for deletion.']);
        exit();
    }

    if ($type === 'department') {
        // Prevent deleting if it has active programs
        $chk1 = $conn->prepare("SELECT id FROM programs WHERE department_id = ? LIMIT 1");
        $chk1->bind_param("i", $id); $chk1->execute();
        if ($chk1->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete: There are Programs attached to this Department.']);
            exit();
        }
        $query = "DELETE FROM departments WHERE id = ?";
        
    } elseif ($type === 'program') {
        // (Add your section/student checks here if you have them, else just delete)
        $query = "DELETE FROM programs WHERE id = ?";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid deletion type.']);
        exit();
    }

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            
            // --- LOG THE ACTIVITY WITH THE ACTUAL NAME ---
            if (isset($_SESSION['user_id'])) {
                $admin_id = $_SESSION['user_id'];
                $itemTypeStr = ucfirst($type);
                $action = "Deleted $itemTypeStr: $itemName";
                
                $logQuery = "INSERT INTO `activity_logs` (`admin_id`, `action`) VALUES (?, ?)";
                $logStmt = $conn->prepare($logQuery);
                if ($logStmt) {
                    $logStmt->bind_param("is", $admin_id, $action);
                    $logStmt->execute();
                    $logStmt->close();
                }
            }

            echo json_encode(['status' => 'success', 'message' => ucfirst($type) . ' deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete from database.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database query error.']);
    }

   // --- DELETE CLASS YEAR ---
    if ($type === 'class_year') {
        $id = $_POST['id'] ?? 0;
        $itemName = $_POST['name'] ?? 'Unknown Year'; 

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM class_years WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // ---> NEW: SAFE SESSION CHECK FOR ACTIVITY LOG <---
            if (session_status() === PHP_SESSION_NONE) {
                session_start(); 
            }

            if (isset($_SESSION['user_id'])) {
                $admin_id = $_SESSION['user_id'];
                $action = "Deleted Class Year: " . $itemName;
                
                $logStmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action) VALUES (?, ?)");
                $logStmt->bind_param("is", $admin_id, $action);
                $logStmt->execute();
                $logStmt->close();
            }

            echo json_encode(['status' => 'success', 'message' => 'Class Year deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error. Failed to delete year.']);
        }
        $stmt->close();
        exit;
    }
}
?>