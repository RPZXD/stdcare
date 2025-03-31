<?php
require_once "../../config/Database.php";
require_once "../../class/Homeroom.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Homeroom class
$homeroom = new Homeroom($db);

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

// Insert data into database
$query = "INSERT INTO tb_homeroom (th_id, h_topic, h_detail, h_result, h_major, h_room, h_term, h_pee, h_date, h_pic1, h_pic2)
          VALUES (:type, :title, :detail, :result, :class, :room, :term, :pee, :date, :image1, :image2)";

$stmt = $db->prepare($query);
$stmt->bindParam(':type', $type);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':detail', $detail);
$stmt->bindParam(':result', $result);
$stmt->bindParam(':class', $class);
$stmt->bindParam(':room', $room);
$stmt->bindParam(':term', $term);
$stmt->bindParam(':pee', $pee);
$stmt->bindParam(':date', $date);
$stmt->bindParam(':image1', $image1);
$stmt->bindParam(':image2', $image2);

if ($stmt->execute()) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false));
}
?>
