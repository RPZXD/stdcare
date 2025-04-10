<?php
require_once('../config/Database.php');
require_once('../class/Student.php');

// Instantiate database and student object
$database = new Database("phichaia_student");
$db = $database->getConnection();
$student = new Student($db);

// Get today's attendance records
$data = $student->getTodayAttendanceRecords();

// Format response for DataTables
$response = [
    "data" => $data,
    "recordsTotal" => count($data),
    "recordsFiltered" => count($data)
];

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
