<?php
require_once __DIR__ . '/../../config/Database.php';

// ตรวจสอบ autoload ของ Composer
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'ยังไม่ได้ติดตั้ง phpoffice/phpspreadsheet']);
    exit;
}
require_once $autoloadPath;

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json; charset=utf-8');

if (!isset($_FILES['advisor_excel']) || $_FILES['advisor_excel']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบไฟล์หรืออัปโหลดไฟล์ผิดพลาด']);
    exit;
}

$tmpFile = $_FILES['advisor_excel']['tmp_name'];

try {
    $spreadsheet = IOFactory::load($tmpFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // ตรวจสอบหัวตาราง
    $header = array_map('trim', $rows[0]);
    // รองรับทั้งภาษาไทยและอังกฤษ
    $validHeaders = [
        ['รหัสครู', 'ชั้นปี', 'ห้อง'],
        ['Teach_id', 'Teach_class', 'Teach_room']
    ];
    $isValid = false;
    foreach ($validHeaders as $vh) {
        if (
            mb_strtolower($header[0]) == mb_strtolower($vh[0]) &&
            mb_strtolower($header[1]) == mb_strtolower($vh[1]) &&
            mb_strtolower($header[2]) == mb_strtolower($vh[2])
        ) {
            $isValid = true;
            break;
        }
    }
    if (!$isValid) {
        echo json_encode(['success' => false, 'message' => 'รูปแบบหัวตารางไม่ถูกต้อง']);
        exit;
    }

    // Connect DB
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    $updated = 0;
    $skipped = 0;
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        $teach_id = trim($row[0]);
        $teach_class = trim($row[1]);
        $teach_room = trim($row[2]);
        if ($teach_id === '' || $teach_class === '' || $teach_room === '') {
            $skipped++;
            continue;
        }
        $sql = "UPDATE teacher SET Teach_class = :class, Teach_room = :room WHERE Teach_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':class', $teach_class);
        $stmt->bindParam(':room', $teach_room);
        $stmt->bindParam(':id', $teach_id);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            $updated++;
        } else {
            $skipped++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "อัปเดตข้อมูลสำเร็จ $updated รายการ, ข้าม $skipped รายการ"
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
