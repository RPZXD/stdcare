<?php
require_once "../../config/Database.php";
require_once "../../controllers/HomeroomController.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Homeroom class
// Use controller which wraps the model
$homeroom = new HomeroomController($db);

// Get POST data
$type = $_POST['type'];
$title = $_POST['title'];
$detail = $_POST['detail'];
$result = $_POST['result'];
$class = $_POST['class'];
$room = $_POST['room'];
$term = $_POST['term'];
$pee = $_POST['pee'];
$date = date("Y-m-d");

// Function to generate random string
function generateRandomString($length = 6) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Handle file uploads
$image1 = null;
$image2 = null;

if (isset($_FILES['image1']) && $_FILES['image1']['error'] == 0) {
    $image1 = $date . '-' . generateRandomString() . '.' . pathinfo($_FILES['image1']['name'], PATHINFO_EXTENSION);
    $image1Path = '../uploads/homeroom/' . $image1;
    move_uploaded_file($_FILES['image1']['tmp_name'], $image1Path);
}

if (isset($_FILES['image2']) && $_FILES['image2']['error'] == 0) {
    $image2 = $date . '-' . generateRandomString() . '.' . pathinfo($_FILES['image2']['name'], PATHINFO_EXTENSION);
    $image2Path = '../uploads/homeroom/' . $image2;
    move_uploaded_file($_FILES['image2']['tmp_name'], $image2Path);
}

// Insert data through controller
if ($homeroom->insertHomeroom($type, $title, $detail, $result, $date, $class, $room, $term, $pee, $image1, $image2)) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false));
}
?>
