<?php
header('Content-Type: application/json');
require_once("../../config/Database.php");

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$date = $_GET['date'] ?? '';

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// สถานะการมาเรียน (1=มาเรียน, 2=ขาดเรียน, 3=มาสาย, 4=ลาป่วย, 5=ลากิจ, 6=กิจกรรม)
$statusMap = [
    1 => 'มาเรียน',
    2 => 'ขาดเรียน',
    3 => 'มาสาย',
    4 => 'ลาป่วย',
    5 => 'ลากิจ',
    6 => 'กิจกรรม'
];

$query = "
    SELECT 
        COALESCE(a.attendance_status, 0) as attendance_status, 
        COUNT(*) AS count_total
    FROM student s
    LEFT JOIN student_attendance a
        ON s.Stu_id = a.student_id AND a.attendance_date = :date
    WHERE s.Stu_major = :class
      AND s.Stu_room = :room
      AND s.Stu_status = 1
    GROUP BY COALESCE(a.attendance_status, 0)
    ORDER BY COALESCE(a.attendance_status, 0) ASC
";
$stmt = $db->prepare($query);
$stmt->execute([
    ':class' => $class,
    ':room' => $room,
    ':date' => $date
]);

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = (int)$row['attendance_status'];
    // 0 means no attendance record yet
    if ($status === 0) {
        $status_name = 'ยังไม่เช็คชื่อ';
    } else {
        $status_name = $statusMap[$status] ?? 'ไม่ทราบสถานะ';
    }
    $data[] = [
        'status' => $status,
        'status_name' => $status_name,
        'count_total' => (int)$row['count_total']
    ];
}

echo json_encode($data);
