<?php
// Include the database configuration and Teacher class file
require_once '../../config/Database.php'; // Adjust this path according to your setup
require_once '../../class/Teacher.php'; // This file contains the Teacher class

// Create an instance of the Database class
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Check if connection was successful
if ($db === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get the department from the request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Create an instance of the Teacher class
        $mjr = new Teacher($db);

        // Fetch departments (majors) using Teacher class
        $majors = $mjr->getDepartment();

        // Output data as JSON
        header('Content-Type: application/json');
        echo json_encode($majors);

    } catch (Exception $e) {
        // Handle any errors
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // Invalid request method
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
}
?>
