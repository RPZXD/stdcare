<?php
require_once('../config/Database.php');

header('Content-Type: application/json');

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    $today_gregorian = date('Y-m-d');
    $today_parts = explode('-', $today_gregorian);
    $today_buddhist = ($today_parts[0] + 543) . '-' . $today_parts[1] . '-' . $today_parts[2];

    // ตัวอย่างการได้ค่าเวลา
    $now = date('H:i:s');

    // ดึงข้อมูลการเช็คชื่อวันนี้ (ล่าสุดก่อน)
    $stmt = $conn->prepare(
        "SELECT a.student_id, a.attendance_time AS time, a.attendance_status, 
                s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room
         FROM student_attendance a
         INNER JOIN student s ON a.student_id = s.Stu_id
         WHERE a.attendance_date = :today
         ORDER BY a.attendance_time DESC"
    );
    $stmt->execute([':today' => $today_buddhist]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($rows as $row) {
        $data[] = [
            'student_id' => $row['student_id'],
            'fullname' => $row['Stu_name'] . ' ' . $row['Stu_sur'],
            'class' => $row['Stu_major'] . '/' . $row['Stu_room'],
            'time' => date('H:i:s', strtotime($row['time'])),
            'status' => ($row['attendance_status'] == '1') ? 'มาเรียน' : 'ขาดเรียน'
        ];
    }

    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    echo json_encode(['data' => [], 'error' => $e->getMessage()]);
    exit;
}
