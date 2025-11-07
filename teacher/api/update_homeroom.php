<?php
require_once "../../config/Database.php";
require_once "../../controllers/HomeroomController.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $type = $_POST['type'] ?? null;
    $title = $_POST['title'] ?? null;
    $detail = $_POST['detail'] ?? null;
    $result = $_POST['result'] ?? null;
    $date = date("Y-m-d");

    if (!$id || !$type || !$title || !$detail || !$result) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

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

    $database = new Database("phichaia_student");
    $db = $database->getConnection();
    $homeroom = new HomeroomController($db);

    // Fetch existing homeroom data
    $existingHomeroom = $homeroom->getHomeroomById($id);
    if ($existingHomeroom) {
        $existingHomeroom = $existingHomeroom[0];
        $image1 = $image1 ?? $existingHomeroom['h_pic1'];
        $image2 = $image2 ?? $existingHomeroom['h_pic2'];
    }

    if ($homeroom->updateHomeroom($id, $type, $title, $detail, $result, $image1, $image2)) {
        echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตข้อมูลได้']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
