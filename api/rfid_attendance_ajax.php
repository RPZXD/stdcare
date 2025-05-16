<?php
require_once dirname(__DIR__) . '/config/Database.php';

date_default_timezone_set('Asia/Bangkok');

function getStudentInfoByRfidCode($db, $rfid_code) {
    $sql = "SELECT s.*, r.rfid_code 
            FROM student_rfid r 
            INNER JOIN student s ON r.stu_id = s.Stu_id 
            WHERE r.rfid_code = :rfid_code 
            LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':rfid_code', $rfid_code);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function handleRfidAttendance($db, $rfid_code, $term, $year) {
    $student = getStudentInfoByRfidCode($db, $rfid_code);
    if ($student) {
        $date = date('Y-m-d');
        $date_parts = explode('-', $date);
        if (count($date_parts) === 3) {
            $date_parts[0] = (string)(((int)$date_parts[0]) + 543);
            $date_thai = implode('-', $date_parts);
        } else {
            $date_thai = $date;
        }
        $time = date('H:i:s');
        $status = '1';
        $checked_by = 'rfid';
        $device_id = 1;

        $stmt = $db->prepare("SELECT id FROM student_attendance WHERE student_id = :stu_id AND attendance_date = :date");
        $stmt->execute([':stu_id' => $student['Stu_id'], ':date' => $date_thai]);
        if ($stmt->fetch()) {
            return [
                'type' => 'warning',
                'msg' => '🟡 นักเรียนคนนี้เช็คชื่อแล้ววันนี้',
                'student' => $student
            ];
        } else {
            $stmt2 = $db->prepare("INSERT INTO student_attendance (student_id, attendance_date, attendance_time, attendance_status, term, year, checked_by, device_id) VALUES (:stu_id, :date, :time, :status, :term, :year, :checked_by, :device_id)");
            $result = $stmt2->execute([
                ':stu_id' => $student['Stu_id'],
                ':date' => $date_thai,
                ':time' => $time,
                ':status' => $status,
                ':term' => $term,
                ':year' => $year,
                ':checked_by' => $checked_by,
                ':device_id' => $device_id
            ]);
            if ($result) {
                return [
                    'type' => 'success',
                    'msg' => '✅ เช็คชื่อสำเร็จ!',
                    'student' => $student
                ];
            } else {
                return [
                    'type' => 'error',
                    'msg' => '❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล',
                    'student' => $student
                ];
            }
        }
    } else {
        return [
            'type' => 'error',
            'msg' => '❌ ไม่พบนักเรียนในระบบ',
            'student' => null
        ];
    }
}

header('Content-Type: application/json; charset=utf-8');
$db = (new Database("phichaia_student"))->getConnection();

$rfid_code = isset($_POST['rfid_code']) ? trim($_POST['rfid_code']) : '';
$term = isset($_POST['term']) ? $_POST['term'] : '1';
$year = isset($_POST['year']) ? $_POST['year'] : date('Y');

echo json_encode(handleRfidAttendance($db, $rfid_code, $term, $year));
