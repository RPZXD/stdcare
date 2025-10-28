<?php
require_once('../config/Database.php');

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    // --- (เพิ่ม/แก้ไข) ดึงการตั้งค่าเวลา ---
    $settings_stmt = $conn->query("SELECT setting_key, setting_value FROM time_settings");
    $settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $arrival_late_time = $settings['arrival_late_time'] ?? '08:00:00';
    $arrival_absent_time = $settings['arrival_absent_time'] ?? '10:00:00'; // <-- (ใหม่)
    $leave_early_time = $settings['leave_early_time'] ?? '15:40:00'; // <-- (แก้ไข)
    // --- (สิ้นสุดการแก้ไข) ---

    $today_gregorian = date('Y-m-d');

    // --- (ปรับปรุง) แก้ไข SQL CASE และใช้ Parameters ---
    $stmt = $conn->prepare(
        "SELECT
            l.student_id,
            DATE_FORMAT(l.scan_timestamp, '%H:%i:%s') AS time,
            s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room,
            CASE
                WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) <= :arrival_late THEN '<span class=\\\"inline-block px-2 py-1 bg-green-200 rounded text-green-700\\\">มาเรียน</span>'
                WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_late AND TIME(l.scan_timestamp) <= :arrival_absent THEN '<span class=\\\"inline-block px-2 py-1 bg-yellow-200 rounded text-yellow-700\\\">มาสาย</span>'
                WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_absent THEN '<span class=\\\"inline-block px-2 py-1 bg-red-200 rounded text-red-700\\\">ขาดเรียน</span>'
                WHEN l.scan_type = 'leave' AND TIME(l.scan_timestamp) < :leave_early THEN '<span class=\\\"inline-block px-2 py-1 bg-blue-200 rounded text-blue-700\\\">กลับก่อน</span>'
                WHEN l.scan_type = 'leave' THEN '<span class=\\\"inline-block px-2 py-1 bg-purple-200 rounded text-purple-700\\\">กลับปกติ</span>'
                ELSE 'ไม่ระบุ'
            END as status
        FROM attendance_log l
        INNER JOIN student s ON l.student_id = s.Stu_id
        WHERE DATE(l.scan_timestamp) = :today
        ORDER BY l.scan_timestamp DESC"
    );

    // --- (ปรับปรุง) Bind Parameters ---
    $stmt->execute([
        ':today' => $today_gregorian,
        ':arrival_late' => $arrival_late_time,
        ':arrival_absent' => $arrival_absent_time,
        ':leave_early' => $leave_early_time,
    ]);
    // --- (สิ้นสุดการปรับปรุง) ---
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($rows as $row) {
        $data[] = [
            'student_id' => $row['student_id'],
            'fullname'   => $row['Stu_pre'].$row['Stu_name'] . '  ' . $row['Stu_sur'],
            'class'      => $row['Stu_major'] . '/' . $row['Stu_room'],
            'time'       => $row['time'],
            'status'     => $row['status'],
        ];
    }

    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage(), 'data' => []]);
}
?>