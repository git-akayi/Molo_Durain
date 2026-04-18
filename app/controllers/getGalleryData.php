<?php
include("../config/config.php");
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// 1. Fetch Latin Honors
if ($action === 'latin') {
    $year = $_GET['year'] ?? '';
    
    // Grabs students where latin_honor is NOT empty and NOT "None"
    $query = "SELECT sp.full_name, sp.latin_honor, sp.photo_path, p.abbreviation as prog_abbr
              FROM student_profiles sp
              LEFT JOIN programs p ON sp.program_id = p.id
              WHERE sp.class_year = ? AND sp.latin_honor != 'None' AND sp.latin_honor IS NOT NULL AND sp.latin_honor != ''
              ORDER BY sp.full_name ASC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $data = [];
    while($row = $res->fetch_assoc()) { $data[] = $row; }
    echo json_encode($data);
} 

// 2. Fetch Students by Section
elseif ($action === 'students') {
    $section = $_GET['section'] ?? '';
    $year = $_GET['year'] ?? '';
    
    $query = "SELECT sp.full_name, sp.quote, sp.photo_path
              FROM student_profiles sp
              LEFT JOIN sections s ON sp.section_id = s.id
              WHERE s.name = ? AND sp.class_year = ?
              ORDER BY sp.full_name ASC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $section, $year);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $data = [];
    while($row = $res->fetch_assoc()) { $data[] = $row; }
    echo json_encode($data);
} 

// 3. Fetch Sections by Program
elseif ($action === 'sections') {
    $program = $_GET['program'] ?? '';
    
    $query = "SELECT s.name FROM sections s JOIN programs p ON s.program_id = p.id WHERE p.name = ? ORDER BY s.name ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $program);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $data = [];
    while($row = $res->fetch_assoc()) { $data[] = $row; }
    echo json_encode($data);
}
?>