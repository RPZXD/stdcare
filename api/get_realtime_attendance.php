<?php
require_once('../config/Database.php');
require_once('../class/Student.php');

// Instantiate database and student object
$database = new Database_User();
$db = $database->getConnection();
$student = new Student($db);

// Get real-time student information filtered by device if provided
$device = isset($_GET['device']) ? $_GET['device'] : '';
$data = $student->getRealTimeStudentInfo($device);

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>