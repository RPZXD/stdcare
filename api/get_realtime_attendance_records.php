<?php
require_once('../config/Database.php');
require_once('../class/Student.php');

// Instantiate database and student object
$database = new Database_User();
$db = $database->getConnection();
$student = new Student($db);

// Get today's attendance records filtered by device if provided

$data = $student->getTodayAttendanceRecords();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
