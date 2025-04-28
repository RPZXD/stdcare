<?php
session_start();
header('Content-Type: application/json');

// ตรวจสอบสิทธิ์ครู (อาจเพิ่มการตรวจสอบ session หรือสิทธิ์เพิ่มเติม)
if (!isset($_SESSION['Teacher_login'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

require_once("../../config/Database.php");

$stu_id = $_POST['Stu_id'] ?? '';
if (!$stu_id) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสนักเรียน']);
    exit;
}

// ตรวจสอบไฟล์
if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบไฟล์หรือเกิดข้อผิดพลาด']);
    exit;
}

$file = $_FILES['profile_pic'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ jpg, jpeg, png, gif, webp']);
    exit;
}

// ตั้งชื่อไฟล์ใหม่เป็น Stu_id.นามสกุล
$newFileName = $stu_id . '.' . $ext;
$uploadDir = '../../photo/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
$targetPath = $uploadDir . $newFileName;

// ลบไฟล์เดิมถ้ามี (เฉพาะไฟล์ที่ชื่อ Stu_id.*)
foreach (glob($uploadDir . $stu_id . '.*') as $oldFile) {
    @unlink($oldFile);
}

// ย้ายไฟล์ (เขียนทับได้)
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(['success' => false, 'message' => 'อัปโหลดไฟล์ไม่สำเร็จ']);
    exit;
}

// อัปเดตฐานข้อมูล
$db = new Database("phichaia_student");
$conn = $db->getConnection();

$stmt = $conn->prepare("UPDATE student SET Stu_picture = :pic WHERE Stu_id = :id");
$stmt->bindParam(":pic", $newFileName);
$stmt->bindParam(":id", $stu_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    @unlink($targetPath);
    echo json_encode(['success' => false, 'message' => 'บันทึกข้อมูลไม่สำเร็จ']);
}
