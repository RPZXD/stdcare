<?php
session_start();
require_once "../../config/Database.php";
require_once "../../class/StudentVisit.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Get the class and room from the request
$class = isset($_GET['class']) ? intval($_GET['class']) : null;
$room = isset($_GET['room']) ? intval($_GET['room']) : null;
$pee = isset($_GET['pee']) ? intval($_GET['pee']) : null;

// Validate input
if (is_null($class) || is_null($room)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid input. Class and room are required."
    ]);
    exit;
}


try {
    // Initialize the StudentVisit class
    $studentVisit = new StudentVisit($db);

    // Fetch students with visit status
    $students = $studentVisit->fetchStudentsWithVisitStatus($class, $room, $pee);

    // Return the data as JSON
    echo json_encode([
        "success" => true,
        "data" => $students
    ]);
} catch (Exception $e) {
    // Handle errors
    echo json_encode([
        "success" => false,
        "message" => "An error occurred while fetching data.",
        "error" => $e->getMessage()
    ]);
}
?>