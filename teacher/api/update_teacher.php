<?php
require_once "../../config/Database.php";
require_once "../../class/Teacher.php";

// เชื่อมต่อฐานข้อมูล
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$teacher = new Teacher($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $teach_id = $_POST['Teach_id'] ?? null;
    $teach_name = $_POST['Teach_name'] ?? null;
    $teach_sex = $_POST['Teach_sex'] ?? null;
    $teach_birth = $_POST['Teach_birth'] ?? null;
    $teach_addr = $_POST['Teach_addr'] ?? null;
    $teach_major = $_POST['Teach_major'] ?? null;
    $teach_phone = $_POST['Teach_phone'] ?? null;
    $teach_class = $_POST['Teach_class'] ?? null;
    $teach_room = $_POST['Teach_room'] ?? null;

    if (!$teach_id || !$teach_name) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit;
    }

    // ตรวจสอบไฟล์อัปโหลด
    if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/phototeach/";
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($_FILES['image1']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง']);
            exit;
        }

        $fileMime = mime_content_type($_FILES['image1']['tmp_name']);
        if (!in_array($fileMime, ['image/jpeg', 'image/png'])) {
            echo json_encode(['success' => false, 'message' => 'ไฟล์ไม่ใช่รูปภาพที่ถูกต้อง']);
            exit;
        }

        $fileName = $teach_id . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image1']['tmp_name'], $targetFilePath)) {
            chmod($targetFilePath, 0644); // กำหนดสิทธิ์ไฟล์
            $teach_photo = $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปโหลดรูปภาพได้']);
            exit;
        }
    } else {
        $teach_photo = $_POST['Teach_photo'] ?? null;
    }

    // อัปเดตข้อมูล
    $result = $teacher->updateTeacher(
        $teach_id,
        $teach_name,
        $teach_sex,
        $teach_birth,
        $teach_addr,
        $teach_major,
        $teach_phone,
        $teach_class,
        $teach_room,
        $teach_photo
    );

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
}
?>
