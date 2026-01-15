<?php
require_once "../../config/Database.php";
require_once "../../class/BoardParent.php";
require_once __DIR__ . "/helpers/upload_helper.php";

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
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน (รหัสนักเรียน, ชื่อ, ที่อยู่, เบอร์โทร, ตำแหน่ง)']);
        exit;
    }

    $photo = null;
    // Check file upload with proper error handling
    if (isset($_FILES['image1'])) {
        $error = $_FILES['image1']['error'];
        if ($error !== UPLOAD_ERR_NO_FILE) {
            if ($error !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => getUploadErrorMessage($error, 'รูปภาพ')]);
                exit;
            }
            if ($_FILES['image1']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'รูปภาพมีขนาดใหญ่เกินไป (สูงสุด 5MB)']);
                exit;
            }
            
            $uploadDir = "../uploads/photopar/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $photo = uniqid() . "_" . basename($_FILES['image1']['name']);
            $uploadPath = $uploadDir . $photo;

            if (!move_uploaded_file($_FILES['image1']['tmp_name'], $uploadPath)) {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกรูปภาพได้ กรุณาลองใหม่']);
                exit;
            }
        }
    }

    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $BoardParent = new BoardParent($db);

    try {
        $BoardParent->insertBoardParent($stu_id, $name, $address, $tel, $pos, $photo, $major, $room, $teacherid, $term, $pee);
        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
}
?>
