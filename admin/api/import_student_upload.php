<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../class/Student.php';

$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'ยังไม่ได้ติดตั้ง phpoffice/phpspreadsheet']);
    exit;
}
require_once $autoloadPath;

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json; charset=utf-8');

if (!isset($_FILES['student_excel']) || $_FILES['student_excel']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบไฟล์หรืออัปโหลดไฟล์ผิดพลาด']);
    exit;
}

$tmpFile = $_FILES['student_excel']['tmp_name'];

try {
    $spreadsheet = IOFactory::load($tmpFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // ตรวจสอบหัวตาราง
    $header = array_map('trim', $rows[0]);
    $validHeaders = [
        ['เลขประจำตัว', 'คำนำหน้า', 'ชื่อ', 'สกุล', 'ชั้น', 'ห้อง', 'เลขที่']
    ];
    $isValid = false;
    foreach ($validHeaders as $vh) {
        if (
            mb_strtolower($header[0]) == mb_strtolower($vh[0]) &&
            mb_strtolower($header[1]) == mb_strtolower($vh[1]) &&
            mb_strtolower($header[2]) == mb_strtolower($vh[2]) &&
            mb_strtolower($header[3]) == mb_strtolower($vh[3]) &&
            mb_strtolower($header[4]) == mb_strtolower($vh[4]) &&
            mb_strtolower($header[5]) == mb_strtolower($vh[5]) &&
            mb_strtolower($header[6]) == mb_strtolower($vh[6])
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
    $student = new Student($db);

    $inserted = 0;
    $skipped = 0;
    $reactivated = 0;
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        $std_id = trim($row[0]);
        $std_pre = trim($row[1]);
        $std_name = trim($row[2]);
        $std_sur = trim($row[3]);
        $std_class = trim($row[4]);
        $std_room = trim($row[5]);
        $std_no = trim($row[6]);
        if ($std_id === '' || $std_pre === '' || $std_name === '' || $std_sur === '' || $std_class === '' || $std_room === '' || $std_no === '') {
            $skipped++;
            continue;
        }
        // ตรวจสอบซ้ำ
        $exists = $student->getStudentById($std_id);
        if ($std_class == '4') {
            if ($exists) {
                // ถ้าเป็น ม.4 และมีอยู่แล้ว ให้เปลี่ยน Stu_status = 1 และอัปเดตคำนำหน้า ชื่อ สกุล ด้วย
                $updateStmt = $db->prepare("UPDATE student SET Stu_status = 1, Stu_major = :class, Stu_room = :room, Stu_no = :no, Stu_pre = :pre, Stu_name = :name, Stu_sur = :sur WHERE Stu_id = :id");
                $updateStmt->bindParam(':class', $std_class);
                $updateStmt->bindParam(':room', $std_room);
                $updateStmt->bindParam(':no', $std_no);
                $updateStmt->bindParam(':pre', $std_pre);
                $updateStmt->bindParam(':name', $std_name);
                $updateStmt->bindParam(':sur', $std_sur);
                $updateStmt->bindParam(':id', $std_id);
                if ($updateStmt->execute()) {
                    $reactivated++;
                } else {
                    $skipped++;
                }
                continue;
            }
            // ถ้าไม่มี ให้เพิ่มเข้าไปใหม่ (เหมือนปกติ)
        } else {
            if ($exists) {
                $skipped++;
                continue;
            }
        }

        // กำหนด property สำหรับ insert
        $student->StuId = $std_id;
        $student->StuNo = $std_no;
        $student->StuPass = $std_id; // ตั้งรหัสผ่านเริ่มต้นเป็นเลขประจำตัว

        // กำหนด StuSex ตาม Stu_pre
        switch ($std_pre) {
            case 'เด็กชาย':
            case 'นาย':
                $student->StuSex = '1';
                break;
            case 'เด็กหญิง':
            case 'นางสาว':
                $student->StuSex = '2';
                break;
            default:
                $student->StuSex = '';
        }

        $student->PreStu = $std_pre;
        $student->NameStu = $std_name;
        $student->SurStu = $std_sur;
        $student->StuClass = $std_class;
        $student->StuRoom = $std_room;
        $student->NickName = '';
        $student->Birth = '0000-00-00';
        $student->Religion = '';
        $student->Blood = '';
        $student->Addr = '';
        $student->Phone = '';

        if ($student->create()) {
            $inserted++;
        } else {
            $skipped++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "นำเข้าข้อมูลสำเร็จ $inserted รายการ, อัปเดตสถานะ ม.4 เดิม $reactivated รายการ, ข้าม $skipped รายการ"
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
