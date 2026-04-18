<?php
session_start();
include("../config/config.php"); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prog_id = $_POST['program_id'];
    $section_name = trim($_POST['name']);

    if (empty($prog_id) || empty($section_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Program and Section Name are required.']);
        exit();
    }

    // Check for duplicates
    $checkQuery = "SELECT id FROM `sections` WHERE `program_id` = ? AND `name` = ? LIMIT 1";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("is", $prog_id, $section_name);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This section already exists under this program.']);
        exit();
    }
    $checkStmt->close();

    // Insert into DB
    $query = "INSERT INTO `sections` (`program_id`, `name`) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("is", $prog_id, $section_name);
        if ($stmt->execute()) {
            
            // Log Activity
            if (isset($_SESSION['user_id'])) {
                $action = "Added new Section: " . $section_name;
                $logQuery = "INSERT INTO `activity_logs` (`admin_id`, `action`) VALUES (?, ?)";
                $logStmt = $conn->prepare($logQuery);
                if ($logStmt) {
                    $logStmt->bind_param("is", $_SESSION['user_id'], $action);
                    $logStmt->execute();
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Section added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save section.']);
        }
        $stmt->close();
    }
}
?>