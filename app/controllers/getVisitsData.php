<?php
session_start();
include("../config/config.php");
header('Content-Type: application/json');

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'monthly';

$labels = [];
$data = [];

// FIXED: Added JOIN and WHERE u.role = 'user' to permanently exclude Admins
if ($filter === 'daily') {
    // Shows the last 7 days
    $query = "SELECT DATE_FORMAT(v.visit_time, '%a') as label, COUNT(v.id) as count 
              FROM user_visits v
              JOIN user u ON v.user_id = u.id
              WHERE v.visit_time >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
              AND u.role = 'user'
              GROUP BY DATE(v.visit_time) ORDER BY DATE(v.visit_time)";
              
} elseif ($filter === 'weekly') {
    // Shows the last 4 weeks
    $query = "SELECT CONCAT('Week ', CEIL(DAY(v.visit_time)/7)) as label, COUNT(v.id) as count 
              FROM user_visits v
              JOIN user u ON v.user_id = u.id
              WHERE v.visit_time >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) 
              AND u.role = 'user'
              GROUP BY YEAR(v.visit_time), MONTH(v.visit_time), WEEK(v.visit_time) ORDER BY v.visit_time";
              
} else {
    // Monthly (This year)
    $query = "SELECT DATE_FORMAT(v.visit_time, '%b') as label, COUNT(v.id) as count 
              FROM user_visits v
              JOIN user u ON v.user_id = u.id
              WHERE YEAR(v.visit_time) = YEAR(CURDATE()) 
              AND u.role = 'user'
              GROUP BY MONTH(v.visit_time) ORDER BY MONTH(v.visit_time)";
}

if (isset($conn)) {
    $result = $conn->query($query);
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $labels[] = $row['label'];
            $data[] = $row['count'];
        }
    }
}

// Fallback logic if the database is currently empty (so the chart doesn't break)
if (empty($labels)) {
    if ($filter === 'daily') {
        $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $data = [0, 0, 0, 0, 0, 0, 0];
    } elseif ($filter === 'weekly') {
        $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        $data = [0, 0, 0, 0];
    } else {
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    }
}

echo json_encode(['labels' => $labels, 'data' => $data]);
?>