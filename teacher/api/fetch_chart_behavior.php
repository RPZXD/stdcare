<?php
header('Content-Type: application/json');
require_once("../../config/Database.php");
require_once("../../class/Behavior.php");

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$term = $_GET['term'] ?? '';
$pee = $_GET['pee'] ?? '';

if ($term === '') {
    $term = 1;
}
if ($pee === '') {
    $pee = date('Y');
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// ดึงข้อมูลพฤติกรรมแบบ group by behavior_type เฉพาะเทอมนี้
$stmt = $db->prepare("
    SELECT behavior_type, COUNT(*) AS count_total
    FROM behavior
    WHERE behavior_term = :term
      AND behavior_pee = :pee
      AND stu_id IN (
        SELECT Stu_id FROM student WHERE Stu_major = :class AND Stu_room = :room AND Stu_status = 1
      )
    GROUP BY behavior_type
    ORDER BY count_total DESC
");
$stmt->execute([
    ':term' => $term,
    ':pee' => $pee,
    ':class' => $class,
    ':room' => $room
]);

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'behavior_type' => $row['behavior_type'],
        'count_total' => (int)$row['count_total']
    ];
}

echo json_encode($data);
