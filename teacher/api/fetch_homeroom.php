<?php
require_once "../../config/Database.php";
require_once "../../controllers/HomeroomController.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Homeroom class
$homeroom = new HomeroomController($db);

// Get parameters from request
$class = isset($_GET['class']) ? $_GET['class'] : '';
$room = isset($_GET['room']) ? $_GET['room'] : '';
$term = isset($_GET['term']) ? $_GET['term'] : '';
$pee = isset($_GET['pee']) ? $_GET['pee'] : '';

$response = array('success' => false, 'data' => array());

if (!empty($class) && !empty($room)) {
    $homerooms = $homeroom->getHomerooms($class, $room, $term, $pee);

    if ($homerooms) {
        $response['success'] = true;
        $response['data'] = $homerooms;
    }
}

echo json_encode($response);
?>
