<?php
require_once "../../config/Database.php";
require_once "../../class/BoardParent.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stu_id = $_POST['stu_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $address = $_POST['address'] ?? null;
    $tel = $_POST['tel'] ?? null;
    $pos = $_POST['pos'] ?? null;
    $major = $_POST['major'] ?? null;
    $room = $_POST['room'] ?? null;
    $teacherid = $_POST['teacherid'] ?? null;
    $term = $_POST['term'] ?? null;
    $pee = $_POST['pee'] ?? null;

    if (!$stu_id || !$name || !$address || !$tel || !$pos) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $photo = null;
    if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/photopar/";
        $photo = uniqid() . "_" . basename($_FILES['image1']['name']);
        $uploadPath = $uploadDir . $photo;

        if (!move_uploaded_file($_FILES['image1']['tmp_name'], $uploadPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload photo']);
            exit;
        }
    }

    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $BoardParent = new BoardParent($db);

    try {
        $BoardParent->insertBoardParent($stu_id, $name, $address, $tel, $pos, $photo, $major, $room, $teacherid, $term, $pee);
        echo json_encode(['success' => true, 'message' => 'Data inserted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to insert data: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
