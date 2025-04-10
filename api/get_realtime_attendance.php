<?php
require_once('../config/Database.php');
require_once('../class/Student.php');

// Instantiate database and student object
$database = new Database("phichaia_student");
$db = $database->getConnection();
$student = new Student($db);

// Implement caching to reduce redundant queries
$cacheKey = 'realtime_attendance_' . (isset($_GET['device']) ? $_GET['device'] : 'all');
$cacheFile = '../cache/' . $cacheKey . '.json';
$cacheTime = 30; // Cache duration in seconds

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    $data = json_decode(file_get_contents($cacheFile), true);
} else {
    // Get real-time student information filtered by device if provided
    $device = isset($_GET['device']) ? $_GET['device'] : '';
    $data = $student->getRealTimeStudentInfo($device);

    // Save to cache
    file_put_contents($cacheFile, json_encode($data));
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>