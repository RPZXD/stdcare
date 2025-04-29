<?php
require_once("../../config/Database.php");
require_once("../../class/UserLogin.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);

$term = $user->getTerm();
$pee = $user->getPee();


$stu_id = isset($_GET['stu_id']) ? $_GET['stu_id'] : '';

if (!$stu_id) {
    echo json_encode(['error' => 'Missing stu_id']);
    exit;
}


// ดึงข้อมูลการมาเรียนทั้งหมดของนักเรียน
$stmt = $db->prepare("SELECT attendance_date, attendance_status, reason 
                      FROM student_attendance 
                      WHERE student_id = :stu_id 
                      AND attendance_term = :term
                        AND attendance_year = :year
                      ORDER BY attendance_date DESC");
$stmt->execute([':stu_id' => $stu_id, ':term' => $term, ':year' => $pee]);
$records = [];
$summary = [
    'present' => 0,
    'late' => 0,
    'absent' => 0,
    'sick' => 0,
    'activity' => 0,
    'event' => 0,
    'total' => 0
];

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

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = strval($row['attendance_status']);
    // นับสรุป
    if ($status === '1') $summary['present']++;
    elseif ($status === '2') $summary['absent']++;
    elseif ($status === '3') $summary['late']++;
    elseif ($status === '4') $summary['sick']++;
    elseif ($status === '5') $summary['activity']++;
    elseif ($status === '6') $summary['event']++;
    // เพิ่มข้อมูลใน records
    $info = status_info($status);
    $records[] = [
        'attendance_date' => $row['attendance_date'],
        'attendance_status' => $row['attendance_status'],
        'reason' => $row['reason'],
        'status_text' => $info['text'],
        'status_color' => $info['color'],
        'status_emoji' => $info['emoji']
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'summary' => $summary,
    'records' => $records
]);
