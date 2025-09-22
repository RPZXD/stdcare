<?php
require_once('../config/Database.php');

header('Content-Type: application/json');

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    $today_gregorian = date('Y-m-d');

    $stmt = $conn->prepare(
        "SELECT
            l.student_id,
            DATE_FORMAT(l.scan_timestamp, '%H:%i:%s') AS time,
            s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture,
            CASE
                WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) <= '08:00:00' THEN 'มาเรียน'
                WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > '08:00:00' THEN 'มาสาย'
                WHEN l.scan_type = 'leave' AND TIME(l.scan_timestamp) < '15:50:00' THEN 'กลับก่อน'
                WHEN l.scan_type = 'leave' THEN 'กลับปกติ'
                ELSE 'ไม่ระบุ'
            END as status
        FROM attendance_log l
        INNER JOIN student s ON l.student_id = s.Stu_id
        WHERE DATE(l.scan_timestamp) = :today
        ORDER BY l.scan_timestamp DESC
        LIMIT 1"
    );
    $stmt->execute([':today' => $today_gregorian]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $data = [
            'student_id' => $row['student_id'],
            'fullname'   => $row['Stu_name'] . ' ' . $row['Stu_sur'],
            'class'      => $row['Stu_major'] . '/' . $row['Stu_room'],
            'photo'      => !empty($row['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.$row['Stu_picture'] : 'https://std.phichai.ac.th/dist/img/logo-phicha.png',
            'time'       => $row['time'],
            'status'     => $row['status']
        ];
        echo json_encode($data);
    } else {
        echo json_encode(null); // Return null if no scans today
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>