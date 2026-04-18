<?php
session_start();
include("../config/config.php");

header('Content-Type: application/json');

// Force PHP to report database errors as Catchable Exceptions instead of fatal crashes
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // 1. Grab the exact data
        $name = trim($_POST['name'] ?? '');
        $dept_id = (int)($_POST['department_id'] ?? 0);
        $prog_id = (int)($_POST['program_id'] ?? 0);
        $section_id = (int)($_POST['section'] ?? 0);
        $latin = trim($_POST['latin_honor'] ?? '');
        $year = trim($_POST['class_year'] ?? '');
        $quote = trim($_POST['quote'] ?? '');
        
        // Safety net: If session expired during testing, default to Admin #1 to prevent DB constraint crash
        $admin_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1; 

        // 2. Validate
        if (empty($name) || empty($dept_id) || empty($prog_id) || empty($section_id) || empty($quote) || empty($year)) {
            echo json_encode(['status' => 'error', 'message' => "Missing fields. Please make sure Department, Program, and Section are selected properly."]);
            exit();
        }

        // 3. Handle the Image Upload
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'Please select a valid photo.']);
            exit();
        }

        $file = $_FILES['photo'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            echo json_encode(['status' => 'error', 'message' => 'Only JPG and PNG files are allowed.']);
            exit();
        }

        if ($file['size'] > 15 * 1024 * 1024) {
            echo json_encode(['status' => 'error', 'message' => 'File size exceeds 15MB limit.']);
            exit();
        }

        // 4. Move the file securely
        $newFileName = uniqid('student_', true) . '.' . $extension;
        
        // Use __DIR__ to guarantee the exact path to your public folder regardless of where the script runs
        $uploadDir = __DIR__ . '/../../public/admin/assets/img/student/'; 
        
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            
            // 5. Save to Database
            $dbPhotoPath = 'assets/img/student/' . $newFileName;
            $query = "INSERT INTO `student_profiles` (`full_name`, `department_id`, `program_id`, `section_id`, `latin_honor`, `class_year`, `quote`, `photo_path`, `uploaded_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("siiissssi", $name, $dept_id, $prog_id, $section_id, $latin, $year, $quote, $dbPhotoPath, $admin_id);
            $stmt->execute();
            $stmt->close();
                
            // 6. Log the activity
            $action = "Uploaded photo for Student: " . $name;
            $logQuery = "INSERT INTO `activity_logs` (`admin_id`, `action`) VALUES (?, ?)";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->bind_param("is", $admin_id, $action);
            $logStmt->execute();
            $logStmt->close();

            echo json_encode(['status' => 'success', 'message' => 'Student yearbook profile uploaded!']);
            
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file to the image folder.']);
        }
    }
} catch (Exception $e) {
    // IF THE DATABASE CRASHES, CATCH IT AND TELL THE ADMIN WHY INSTEAD OF "NETWORK ERROR"
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database Crash: ' . $e->getMessage()
    ]);
}
?>