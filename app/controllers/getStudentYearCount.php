<?php

include_once("../../app/middleware/admin.php");
include_once("../../app/config/config.php");

header('Content-Type: application/json');

if (isset($_GET['year']) && isset($conn)) {
    $year = $_GET['year'];
    
    $query = "SELECT COUNT(id) as count FROM student_profiles WHERE class_year = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'count' => $row['count']]);
        } else {
            echo json_encode(['success' => false, 'count' => 0]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database query failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>