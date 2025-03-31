<?php
require_once "../../config/Database.php";
require_once "../../class/Homeroom.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Homeroom class
$homeroom = new Homeroom($db);

// Get parameters from request
$id = isset($_GET['id']) ? $_GET['id'] : '';

$response = array('success' => false, 'data' => array());

if (!empty($id)) {
    $homerooms = $homeroom->fetchHomeroomById($id);

    if ($homerooms) {
        $response['success'] = true;
        $response['data'] = $homerooms;
    } else {
        $response['message'] = 'No data found';
    }
} else {
    $response['message'] = 'Invalid ID';
}

echo json_encode($response);
?>
