<?php
require_once("../../config/Database.php");
require_once("../../class/Student.php");

// ตรวจสอบ autoload ของ Composer
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบไลบรารี phpoffice/phpspreadsheet']);
    exit;
}
require_once $autoloadPath;

use PhpOffice\PhpSpreadsheet\IOFactory;

// ตรวจสอบไฟล์
if (!isset($_FILES['number_excel']) || $_FILES['number_excel']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเลือกไฟล์ Excel ที่ต้องการอัปโหลด']);
    exit;
}

// อัปโหลดไฟล์ไปยัง temp
$tmpFile = $_FILES['number_excel']['tmp_name'];

// โหลดไฟล์ Excel
try {
    $spreadsheet = IOFactory::load($tmpFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอ่านไฟล์ Excel ได้: ' . $e->getMessage()]);
    exit;
}

// ตรวจสอบหัวตาราง
$expectedHeaders = [
    'Stu_id','Stu_no','Stu_password','Stu_sex','Stu_pre','Stu_name','Stu_sur','Stu_major','Stu_room','Stu_nick','Stu_birth','Stu_religion','Stu_blood','Stu_addr','Stu_phone',
    'Father_name','Father_occu','Father_income','Mother_name','Mother_occu','Mother_income','Par_name','Par_relate','Par_occu','Par_income','Par_addr','Par_phone','Risk_group','Stu_status','Stu_citizenid'
];

// รองรับกรณีหัวตารางเป็นภาษาไทย (เช่น export จาก update_number_sample.php)
$thaiHeaders = [
    'เลขประจำตัว', 'เลขที่', 'รหัสผ่าน', 'เพศ', 'คำนำหน้า', 'ชื่อ', 'สกุล', 'ชั้นปี', 'ห้อง', 'ชื่อเล่น', 'วันเกิด', 'ศาสนา', 'กรุ๊ปเลือด', 'ที่อยู่', 'เบอร์โทร',
    'ชื่อบิดา', 'อาชีพบิดา', 'รายได้บิดา', 'ชื่อมารดา', 'อาชีพมารดา', 'รายได้มารดา', 'ชื่อผู้ปกครอง', 'ความสัมพันธ์', 'อาชีพผู้ปกครอง', 'รายได้ผู้ปกครอง', 'ที่อยู่ผู้ปกครอง', 'เบอร์ผู้ปกครอง', 'กลุ่มเสี่ยง', 'สถานะ', 'เลขบัตรประชาชน'
];

// ป้องกัน trim(null) deprecated
$headerRow = [];
foreach (array_values($rows[1] ?? []) as $v) {
    $headerRow[] = is_null($v) ? '' : trim($v);
}

// ตรวจสอบหัวตารางแบบยืดหยุ่น (กรณี Excel แทรกคอลัมน์ว่างท้าย/ต้น หรือแถว header ไม่ตรง index)
$headerRowFiltered = array_values(array_filter($headerRow, function($v) { return $v !== ''; }));

$isHeaderEng = ($headerRowFiltered === $expectedHeaders);
$isHeaderThai = ($headerRowFiltered === $thaiHeaders);

if (!$isHeaderEng && !$isHeaderThai) {
    echo json_encode(['success' => false, 'message' => 'รูปแบบหัวตารางไม่ถูกต้อง กรุณาดาวน์โหลดไฟล์ตัวอย่างใหม่']);
    exit;
}

// ถ้าเป็นหัวตารางภาษาไทย ให้ map ชื่อไทย -> ชื่อฟิลด์ฐานข้อมูล
$fieldMap = [];
if ($isHeaderThai) {
    foreach ($thaiHeaders as $idx => $th) {
        $fieldMap[$th] = $expectedHeaders[$idx];
    }
}

// เชื่อมต่อฐานข้อมูล
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// เตรียมอัปเดตข้อมูล
$successCount = 0;
$errorCount = 0;
$errorList = [];

for ($i = 2; $i <= count($rows); $i++) {
    $row = $rows[$i];
    if (empty($row['A'])) continue; // ข้ามถ้าไม่มี Stu_id

    // เตรียมข้อมูล
    $data = [];
    $col = 'A';
    foreach ($expectedHeaders as $idx => $field) {
        // ไม่ว่า header จะเป็นไทยหรืออังกฤษ ให้ map ตาม expectedHeaders
        $data[$field] = isset($row[$col]) ? trim($row[$col]) : null;
        $col++;
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    try {
        $set = [];
        $params = [];
        foreach ($expectedHeaders as $field) {
            if ($field !== 'Stu_id') {
                $set[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        $params[] = $data['Stu_id'];
        $sql = "UPDATE student SET " . implode(',', $set) . " WHERE Stu_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        if ($stmt->rowCount() > 0) {
            $successCount++;
        } else {
            $errorCount++;
            $errorList[] = $data['Stu_id'];
        }
    } catch (Exception $e) {
        $errorCount++;
        $errorList[] = $data['Stu_id'];
    }
}

$message = "อัปเดตข้อมูลสำเร็จ $successCount รายการ";
if ($errorCount > 0) {
    $message .= " | ไม่สำเร็จ $errorCount รายการ: " . implode(', ', $errorList);
}

echo json_encode([
    'success' => $successCount > 0,
    'message' => $message
]);
exit;
