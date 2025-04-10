<?php
require_once '../config/Database.php';
require_once '../config/Setting.php';
require_once '../class/Student.php';
require_once '../class/Utils.php';

// Initialize database connection
$database = new Database("phichaia_student");
$db = $database->getConnection();

// Initialize Student class
$student = new Student($db);

// Set the character encoding to UTF-8
header('Content-Type: application/json; charset=utf-8');

// Check if the 'class' parameter is set
if (!isset($_GET['class'])) {
    die("Error: 'class' parameter is missing.");
}

// Sanitize the 'class' parameter to prevent SQL injection
$class = filter_var($_GET['class'], FILTER_SANITIZE_NUMBER_INT);

// Get today's date in Thai format
$date = Utils::convertToThaiDatePlusNum(date("Y-m-d"));

try {

    // Fetch study status count
    $data = $student->getStudyStatusCount($class, $date);

    // Define color mappings for each status
    $statusColors = array(
        '1' => '#28a745', // Green
        '2' => '#dc3545', // Red
        '3' => '#ffc107', // Yellow
        '4' => '#17a2b8', // Blue
        '5' => '#6c757d', // Gray
        '6' => '#343a40'  // Dark Gray
    );

    // Process the fetched data
    $formattedData = array();
    foreach ($data as $row) {
        $formattedData[] = array(
            'label' => Utils::strstatusck($row['Study_status']),
            'value' => (int)$row['count'],
            'color' => isset($statusColors[$row['Study_status']]) ? $statusColors[$row['Study_status']] : '#000000' // Default to black if no color defined
        );
    }

    // Return data as JSON
    echo json_encode($formattedData, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // Handle errors
    die("Error: " . $e->getMessage());
}

// Function to generate random hex color
function getRandomColor() {
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}
?>
