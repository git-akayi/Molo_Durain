<?php
session_start();
include("../config/config.php"); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type']; 

    if ($type === 'department') {
        $name = trim($_POST['name']);
        $abbr = trim($_POST['abbreviation']);

        if (empty($name) || empty($abbr)) {
            echo json_encode(['status' => 'error', 'message' => 'Name and Abbreviation are required.']);
            exit();
        }

        $checkQuery = "SELECT id FROM `departments` WHERE `name` = ? OR `abbreviation` = ? LIMIT 1";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ss", $name, $abbr);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'A department with this name or abbreviation already exists.']);
            exit();
        }
        $checkStmt->close();

        $query = "INSERT INTO `departments` (`name`, `abbreviation`) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ss", $name, $abbr);
            if ($stmt->execute()) {
                
                // --- LOG THIS ACTIVITY ---
                if (isset($_SESSION['user_id'])) {
                    $action = "Added new Department: " . $name;
                    $logQuery = "INSERT INTO `activity_logs` (`admin_id`, `action`) VALUES (?, ?)";
                    $logStmt = $conn->prepare($logQuery);
                    if ($logStmt) {
                        $logStmt->bind_param("is", $_SESSION['user_id'], $action);
                        $logStmt->execute();
                        $logStmt->close();
                    }
                }

                echo json_encode(['status' => 'success', 'message' => 'Department added successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add department.']);
            }
            $stmt->close();
        }
    } 
    elseif ($type === 'program') {
        $dept_id = $_POST['department_id'];
        $name = trim($_POST['name']);
        $abbr = trim($_POST['abbreviation']);

        if (empty($dept_id) || empty($name) || empty($abbr)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit();
        }

        $checkQuery = "SELECT id FROM `programs` WHERE `department_id` = ? AND (`name` = ? OR `abbreviation` = ?) LIMIT 1";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("iss", $dept_id, $name, $abbr);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This program already exists under the selected department.']);
            exit();
        }
        $checkStmt->close();

        $query = "INSERT INTO `programs` (`department_id`, `name`, `abbreviation`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("iss", $dept_id, $name, $abbr);
            if ($stmt->execute()) {

                // --- LOG THIS ACTIVITY ---
                if (isset($_SESSION['user_id'])) {
                    $action = "Added new Program: " . $name;
                    $logQuery = "INSERT INTO `activity_logs` (`admin_id`, `action`) VALUES (?, ?)";
                    $logStmt = $conn->prepare($logQuery);
                    if ($logStmt) {
                        $logStmt->bind_param("is", $_SESSION['user_id'], $action);
                        $logStmt->execute();
                        $logStmt->close();
                    }
                }

                echo json_encode(['status' => 'success', 'message' => 'Program added successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add program.']);
            }
            $stmt->close();
        }
    }

// --- ADD CLASS YEAR ---
    if ($type === 'class_year') {
        $year = $_POST['year'] ?? '';

        if (empty($year)) {
            echo json_encode(['status' => 'error', 'message' => 'Year is required']);
            exit;
        }

        // Check if year already exists to prevent duplicates
        $check = $conn->prepare("SELECT id FROM class_years WHERE year = ?");
        $check->bind_param("s", $year);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This class year already exists']);
            exit;
        }
        $check->close();

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO class_years (year) VALUES (?)");
        $stmt->bind_param("s", $year);
        
        if ($stmt->execute()) {
            // ---> NEW: SAFE SESSION CHECK FOR ACTIVITY LOG <---
            if (session_status() === PHP_SESSION_NONE) {
                session_start(); 
            }
            
            if (isset($_SESSION['user_id'])) {
                $admin_id = $_SESSION['user_id'];
                $action = "Added new Class Year: " . $year;
                
                $logStmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action) VALUES (?, ?)");
                $logStmt->bind_param("is", $admin_id, $action);
                $logStmt->execute();
                $logStmt->close();
            }

            echo json_encode(['status' => 'success', 'message' => 'Class Year added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error. Failed to add year.']);
        }
        $stmt->close();
        exit;
    }
}
?>