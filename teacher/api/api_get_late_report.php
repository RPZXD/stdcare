<?php
header('Content-Type: application/json');
include_once("../../config/Database.php");
include_once("../../class/Attendance.php");
include_once("../../class/Student.php");

// รับค่า GET
$class = isset($_GET['class']) ? $_GET['class'] : null;
$room = isset($_GET['room']) ? $_GET['room'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

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
if ($startDate && $endDate) {
    $students = $attendance->getStudentsWithAttendanceRange($startDate, $endDate, $class, $room);
} else {
    $date = $date ?: date('Y-m-d');
    $students = $attendance->getStudentsWithAttendance($date, $class, $room);
}

// กรองและตกแต่งข้อมูล
$result = [];
foreach ($students as $stu) {
    if (isset($stu['attendance_status']) && in_array($stu['attendance_status'], ['1', '2', '3', '4', '5', '6'])) {
        // ดึงเบอร์ผู้ปกครอง (ถ้ายังไม่มีจากการ JOIN)
        if (!isset($stu['parent_tel'])) {
            $stu['parent_tel'] = $studentObj->getParentTel($stu['Stu_id']);
        }
        // เพิ่มรายละเอียดสถานะ
        $stu['attendance_status_info'] = status_info($stu['attendance_status']);
        $result[] = $stu;
    }
}

echo json_encode($result);
exit;
