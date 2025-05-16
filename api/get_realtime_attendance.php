<?php
require_once dirname(__DIR__) . '/config/Database.php';

header('Content-Type: application/json; charset=utf-8');

date_default_timezone_set('Asia/Bangkok');

$db = (new Database("phichaia_student"))->getConnection();
$today = date('Y-m-d');
$date_parts = explode('-', $today);
if (count($date_parts) === 3) {
    $date_parts[0] = (string)(((int)$date_parts[0]) + 543);
    $today_thai = implode('-', $date_parts);
} else {
    $today_thai = $today;
}
$sql = "SELECT a.*, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_no, r.rfid_code, s.Stu_picture
        FROM student_attendance a
        INNER JOIN student s ON a.student_id = s.Stu_id
        LEFT JOIN student_rfid r ON r.stu_id = s.Stu_id
        WHERE a.attendance_date = :today
        ORDER BY a.attendance_time DESC";
$stmt = $db->prepare($sql);
$stmt->bindParam(':today', $today_thai);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output format: { "data": [ ...rows... ] }
echo json_encode(['data' => $rows]);
?>