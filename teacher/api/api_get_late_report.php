<?php
header('Content-Type: application/json');
include_once("../../config/Database.php");
include_once("../../class/Attendance.php");
include_once("../../class/Student.php");

// รับค่า GET
$class = isset($_GET['class']) ? $_GET['class'] : null;
$room = isset($_GET['room']) ? $_GET['room'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
if ($date) {
    $parts = explode('-', $date);
    if (count($parts) === 3) {
        $parts[0] = strval(intval($parts[0]) + 543);
        $date = implode('-', $parts);
    }
}

function status_info($status) {
    switch ($status) {
        case '1': return ['text' => 'มาเรียน', 'color' => 'text-green-600', 'emoji' => '✅'];
        case '2': return ['text' => 'ขาดเรียน', 'color' => 'text-red-600', 'emoji' => '❌'];
        case '3': return ['text' => 'มาสาย', 'color' => 'text-yellow-600', 'emoji' => '🕒'];
        case '4': return ['text' => 'ลาป่วย', 'color' => 'text-blue-600', 'emoji' => '🤒'];
        case '5': return ['text' => 'ลากิจ', 'color' => 'text-purple-600', 'emoji' => '📝'];
        case '6': return ['text' => 'เข้าร่วมกิจกรรม', 'color' => 'text-pink-600', 'emoji' => '🎉'];
        default:  return ['text' => 'ไม่ระบุ', 'color' => 'text-gray-500', 'emoji' => ''];
    }
}


// เชื่อมต่อฐานข้อมูล
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$attendance = new Attendance($db);
$studentObj = new Student($db);

// ดึงข้อมูลนักเรียนพร้อมสถานะการเช็คชื่อ
$students = $attendance->getStudentsWithAttendance($date, $class, $room);

// กรองเฉพาะคนที่ "ขาด" หรือ "มาสาย" (attendance_status = 2 หรือ 3)
$result = [];
foreach ($students as $stu) {
    // 2 = ขาด, 3 = สาย (สมมติฐาน, ปรับตามระบบจริง)
    if (isset($stu['attendance_status']) && in_array($stu['attendance_status'], ['2', '3'])) {
        // ดึงเบอร์ผู้ปกครอง
        $parent_tel = $studentObj->getParentTel($stu['Stu_id']);
        $stu['parent_tel'] = $parent_tel;
        // เพิ่มรายละเอียดสถานะ
        $stu['attendance_status_info'] = status_info($stu['attendance_status']);
        $result[] = $stu;
    }
}

echo json_encode($result);
exit;
