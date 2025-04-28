<?php
header('Content-Type: application/json');
require_once("../../config/Database.php");

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$date = $_GET['date'] ?? '';

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// สถานะการมาเรียน (1=มา, 2=ขาด, 3=สาย, 4=ลา, 5=กิจกรรม, 6=มา(สาย))
$statusMap = [
    1 => 'มา',
    2 => 'ขาด',
    3 => 'สาย',
    4 => 'ลา',
    5 => 'กิจกรรม',
    6 => 'มา(สาย)'
];

$query = "
    SELECT 
        a.attendance_status, 
        COUNT(*) AS count_total
    FROM student s
    LEFT JOIN student_attendance a
        ON s.Stu_id = a.student_id AND a.attendance_date = :date
    WHERE s.Stu_major = :class
      AND s.Stu_room = :room
      AND s.Stu_status = 1
    GROUP BY a.attendance_status
    ORDER BY a.attendance_status ASC
";
$stmt = $db->prepare($query);
$stmt->execute([
    ':class' => $class,
    ':room' => $room,
    ':date' => $date
]);

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = $row['attendance_status'];
    $status_name = $statusMap[$status] ?? 'ไม่ระบุ';
    $data[] = [
        'status' => $status,
        'status_name' => $status_name,
        'count_total' => (int)$row['count_total']
    ];
}

echo json_encode($data);

