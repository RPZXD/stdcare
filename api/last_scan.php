<?php
require_once('../config/Database.php');

header('Content-Type: application/json');

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    $device_id = isset($_GET['device_id']) ? intval($_GET['device_id']) : 1;

    // แปลงวันที่เป็น พ.ศ.
    $today_gregorian = date('Y-m-d');
    $today_parts = explode('-', $today_gregorian);
    $today_buddhist = ($today_parts[0] + 543) . '-' . $today_parts[1] . '-' . $today_parts[2];

    // ดึงข้อมูลการสแกนล่าสุดวันนี้ เฉพาะ device_id นี้
    $stmt = $conn->prepare(
        "SELECT a.student_id, a.attendance_time AS time, a.attendance_status, 
                s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture
         FROM student_attendance a
         INNER JOIN student s ON a.student_id = s.Stu_id
         WHERE a.attendance_date = :today AND a.device_id = :device_id
         ORDER BY a.attendance_time DESC
         LIMIT 1"
    );
    $stmt->execute([':today' => $today_buddhist, ':device_id' => $device_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $fullname = $row['Stu_name'] . ' ' . $row['Stu_sur'];
        $class = $row['Stu_major'] . '/' . $row['Stu_room'];
        $photo = !empty($row['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.$row['Stu_picture'] : 'https://std.phichai.ac.th/dist/img/logo-phicha.png';
        $status = ($row['attendance_status'] == '1') ? 'มาเรียน' : 'ขาดเรียน';

        echo json_encode([
            'student_id' => $row['student_id'],
            'fullname' => $fullname,
            'class' => $class,
            'photo' => $photo,
            'time' => $row['time'],
            'status' => $status
        ]);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
