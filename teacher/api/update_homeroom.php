<?php
require_once "../../config/Database.php";
require_once "../../controllers/HomeroomController.php";
require_once __DIR__ . "/helpers/upload_helper.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $type = $_POST['type'] ?? null;
    $title = $_POST['title'] ?? null;
    $detail = $_POST['detail'] ?? null;
    $result = $_POST['result'] ?? null;
    $date = date("Y-m-d");

    if (!$id || !$type || !$title || !$detail || !$result) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน (ประเภท, หัวข้อ, รายละเอียด, ผลการดำเนินงาน)']);
        exit;
    }

    // Function to generate random string
    function generateRandomString($length = 6) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    // Handle file uploads with proper error checking
    $image1 = null;
    $image2 = null;

    // Validate and process image1
    if (isset($_FILES['image1'])) {
        $error = $_FILES['image1']['error'];
        if ($error !== UPLOAD_ERR_NO_FILE) {
            if ($error !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => getUploadErrorMessage($error, 'รูปภาพที่ 1')]);
                exit;
            }
            if ($_FILES['image1']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'รูปภาพที่ 1 มีขนาดใหญ่เกินไป (สูงสุด 5MB)']);
                exit;
            }
            $image1 = $date . '-' . generateRandomString() . '.' . pathinfo($_FILES['image1']['name'], PATHINFO_EXTENSION);
            $image1Path = '../uploads/homeroom/' . $image1;
            if (!move_uploaded_file($_FILES['image1']['tmp_name'], $image1Path)) {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกรูปภาพที่ 1 ได้']);
                exit;
            }
        }
    }

    // Validate and process image2
    if (isset($_FILES['image2'])) {
        $error = $_FILES['image2']['error'];
        if ($error !== UPLOAD_ERR_NO_FILE) {
            if ($error !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => getUploadErrorMessage($error, 'รูปภาพที่ 2')]);
                exit;
            }
            if ($_FILES['image2']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'รูปภาพที่ 2 มีขนาดใหญ่เกินไป (สูงสุด 5MB)']);
                exit;
            }
            $image2 = $date . '-' . generateRandomString() . '.' . pathinfo($_FILES['image2']['name'], PATHINFO_EXTENSION);
            $image2Path = '../uploads/homeroom/' . $image2;
            if (!move_uploaded_file($_FILES['image2']['tmp_name'], $image2Path)) {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกรูปภาพที่ 2 ได้']);
                exit;
            }
        }
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
