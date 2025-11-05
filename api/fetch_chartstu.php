<?php
require_once('../config/Database.php');

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');

try {
    $database = new Database("phichaia_student");
    $db = $database->getConnection();

    // รับค่าจาก Query String
    $level = isset($_GET['level']) ? $_GET['level'] : '1-3';
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    
    $levels = explode('-', $level);
    $majorStart = (int)$levels[0];
    $majorEnd = (int)$levels[1];
    
    $labels = [];
    $datasets = [
        'present' => [],    // มาเรียน (status = 1)
        'absent' => [],     // ขาดเรียน (status = 2)
        'late' => [],       // มาสาย (status = 3)
        'sick' => [],       // ลาป่วย (status = 4)
        'business' => [],   // ลากิจ (status = 5)
        'activity' => []    // กิจกรรม (status = 6)
    ];

    // ดึงข้อมูลห้องเรียนทั้งหมดในระดับชั้นที่เลือก
    $stmt = $db->prepare("
        SELECT DISTINCT Stu_major, Stu_room 
        FROM student 
        WHERE Stu_status = 1 AND Stu_major BETWEEN ? AND ?
        ORDER BY Stu_major, Stu_room
    ");
    $stmt->execute([$majorStart, $majorEnd]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rooms as $room) {
        $major = $room['Stu_major'];
        $roomNum = $room['Stu_room'];
        $labels[] = "ม.$major/$roomNum";

        // นับจำนวนนักเรียนแต่ละสถานะในห้องนี้
        $stmt = $db->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN sa.attendance_status = 1 THEN 1 ELSE 0 END), 0) as present,
                COALESCE(SUM(CASE WHEN sa.attendance_status = 2 THEN 1 ELSE 0 END), 0) as absent,
                COALESCE(SUM(CASE WHEN sa.attendance_status = 3 THEN 1 ELSE 0 END), 0) as late,
                COALESCE(SUM(CASE WHEN sa.attendance_status = 4 THEN 1 ELSE 0 END), 0) as sick,
                COALESCE(SUM(CASE WHEN sa.attendance_status = 5 THEN 1 ELSE 0 END), 0) as business,
                COALESCE(SUM(CASE WHEN sa.attendance_status = 6 THEN 1 ELSE 0 END), 0) as activity
            FROM student s
            LEFT JOIN student_attendance sa ON s.Stu_id = sa.student_id AND sa.attendance_date = ?
            WHERE s.Stu_status = 1 AND s.Stu_major = ? AND s.Stu_room = ?
        ");
        $stmt->execute([$date, $major, $roomNum]);
        $counts = $stmt->fetch(PDO::FETCH_ASSOC);

        $datasets['present'][] = (int)$counts['present'];
        $datasets['absent'][] = (int)$counts['absent'];
        $datasets['late'][] = (int)$counts['late'];
        $datasets['sick'][] = (int)$counts['sick'];
        $datasets['business'][] = (int)$counts['business'];
        $datasets['activity'][] = (int)$counts['activity'];
    }

    // สร้าง response สำหรับ Chart.js
    $response = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => '✅ มาเรียน',
                'backgroundColor' => '#10b981',
                'borderColor' => '#059669',
                'borderWidth' => 1,
                'data' => $datasets['present']
            ],
            [
                'label' => '❌ ขาดเรียน',
                'backgroundColor' => '#ef4444',
                'borderColor' => '#dc2626',
                'borderWidth' => 1,
                'data' => $datasets['absent']
            ],
            [
                'label' => '🕒 มาสาย',
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#d97706',
                'borderWidth' => 1,
                'data' => $datasets['late']
            ],
            [
                'label' => '🤒 ลาป่วย',
                'backgroundColor' => '#3b82f6',
                'borderColor' => '#2563eb',
                'borderWidth' => 1,
                'data' => $datasets['sick']
            ],
            [
                'label' => '📝 ลากิจ',
                'backgroundColor' => '#8b5cf6',
                'borderColor' => '#7c3aed',
                'borderWidth' => 1,
                'data' => $datasets['business']
            ],
            [
                'label' => '🎉 กิจกรรม',
                'backgroundColor' => '#ec4899',
                'borderColor' => '#db2777',
                'borderWidth' => 1,
                'data' => $datasets['activity']
            ]
        ]
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
