<?php
// Include the database configuration and required classes
require_once '../../config/Database.php'; // Adjust this path according to your setup
require_once '../../class/Behavior.php'; // This file contains the Behavior class
include_once("../../class/UserLogin.php"); // This file contains the UserLogin class

// Create an instance of the Database class
$database = new Database("phichaia_student");
$db = $database->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Check if connection was successful
if ($db === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Check if request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get class and room from the request
    $class = isset($_GET['class']) ? htmlspecialchars($_GET['class']) : '';
    $room = isset($_GET['room']) ? htmlspecialchars($_GET['room']) : '';

    // Check if required parameters are provided
    if (empty($class) || empty($room)) {
        http_response_code(400);
        echo json_encode(['error' => 'Class and room parameters are required']);
        exit();
    }

    try {
        // Create an instance of the Behavior class
        $behavior = new Behavior($db);

        // Fetch behaviors based on class, room, term, and pee
        $behaviors = $behavior->getScoreBehaviorsClassTA($class, $room, $term, $pee);

        // Output data as JSON
        header('Content-Type: application/json');
        echo json_encode($behaviors);

    } catch (Exception $e) {
        // Handle any errors
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
    }
} else {
    // Request method is not GET
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
