<?php
session_start();
include("../config/config.php");
header('Content-Type: application/json');

// Shield against database crashes
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sysName = $_POST['system_name'] ?? 'USTP E-Gallery';
        $defYear = $_POST['default_class_year'] ?? '2029';
        $maintenance = $_POST['maintenance_mode'] ?? '0';

        // This query inserts the setting, or UPDATES it if it already exists in the table!
        $query = "INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $conn->prepare($query);
        
        $settings = [
            'system_name' => $sysName,
            'default_class_year' => $defYear,
            'maintenance_mode' => $maintenance
        ];

        // Loop through and save all 3 settings
        foreach($settings as $key => $val) {
            $stmt->bind_param("ss", $key, $val);
            $stmt->execute();
        }
        $stmt->close();
        
        // Log Activity
        if (isset($_SESSION['user_id'])) {
            $logQuery = "INSERT INTO `activity_logs` (`admin_id`, `action`) VALUES (?, 'Updated General System Settings')";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->bind_param("i", $_SESSION['user_id']);
            $logStmt->execute();
            $logStmt->close();
        }

        echo json_encode(['status' => 'success', 'message' => 'Global settings updated successfully!']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>