<?php
include_once("../../config/Database.php");
include_once("../../class/Attendance.php");

header('Content-Type: application/json; charset=utf-8');

// Security check
$allowed_referers = [
    'http://localhost/stdcare/officer/',
    'https://std.phichai.ac.th/officer/'
];
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$referer_ok = false;
foreach ($allowed_referers as $allowed) {
    if (strpos($referer, $allowed) === 0) {
        $referer_ok = true;
        break;
    }
}
if (!$referer_ok) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$attendance = new Attendance($db);

// DataTables parameters
$draw = intval($_GET['draw'] ?? 1);
$start = intval($_GET['start'] ?? 0);
$length = intval($_GET['length'] ?? 50);
$search = $_GET['search']['value'] ?? '';
$orderColumn = $_GET['order'][0]['column'] ?? 0;
$orderDir = $_GET['order'][0]['dir'] ?? 'asc';

// Filters
$date = $_GET['date'] ?? date('Y-m-d');
$class = $_GET['class'] ?? null;
$room = $_GET['room'] ?? null;
$term = $_GET['term'] ?? null;
$pee = $_GET['pee'] ?? null;

// Column mapping for ordering
$columns = ['s.Stu_no', 's.Stu_id', 's.Stu_name', 'a.attendance_status', 'a.attendance_time', 'a.reason', 'a.checked_by'];
$orderBy = $columns[$orderColumn] ?? 's.Stu_no';

// Build query
$query = "SELECT 
            s.Stu_id, s.Stu_no, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room,
            a.id AS attendance_id, a.attendance_date, a.attendance_status, a.term, a.year, a.checked_by, a.device_id, a.reason, a.attendance_time,
            (SELECT TIME(scan_timestamp) FROM attendance_log WHERE student_id = s.Stu_id AND DATE(scan_timestamp) = :date_arr AND scan_type = 'arrival' ORDER BY scan_timestamp ASC LIMIT 1) AS arrival_time,
            (SELECT TIME(scan_timestamp) FROM attendance_log WHERE student_id = s.Stu_id AND DATE(scan_timestamp) = :date_lv AND scan_type = 'leave' ORDER BY scan_timestamp DESC LIMIT 1) AS leave_time
          FROM student s
          LEFT JOIN student_attendance a ON s.Stu_id = a.student_id AND a.attendance_date = :date
          WHERE s.Stu_status = 1";

$params = [':date' => $date, ':date_arr' => $date, ':date_lv' => $date];

if (!empty($class)) {
    $query .= " AND s.Stu_major = :class";
    $params[':class'] = $class;
}
if (!empty($room)) {
    $query .= " AND s.Stu_room = :room";
    $params[':room'] = $room;
}

// Search filter
if (!empty($search)) {
    $query .= " AND (s.Stu_no LIKE :search OR s.Stu_id LIKE :search OR CONCAT(s.Stu_pre, s.Stu_name, ' ', s.Stu_sur) LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$query .= " ORDER BY $orderBy $orderDir";

// Get total records (without filters except active students)
$totalQuery = "SELECT COUNT(*) as total FROM student s WHERE s.Stu_status = 1";
if (!empty($class)) {
    $totalQuery .= " AND s.Stu_major = :class";
}
if (!empty($room)) {
    $totalQuery .= " AND s.Stu_room = :room";
}
$totalStmt = $db->prepare($totalQuery);
$totalParams = [];
if (!empty($class)) $totalParams[':class'] = $class;
if (!empty($room)) $totalParams[':room'] = $room;
$totalStmt->execute($totalParams);
$recordsTotal = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get filtered records count
$filteredQuery = "SELECT COUNT(*) as filtered FROM student s
                  LEFT JOIN student_attendance a ON s.Stu_id = a.student_id AND a.attendance_date = :date
                  WHERE s.Stu_status = 1";

$filteredParams = [':date' => $date];

if (!empty($class)) {
    $filteredQuery .= " AND s.Stu_major = :class";
    $filteredParams[':class'] = $class;
}
if (!empty($room)) {
    $filteredQuery .= " AND s.Stu_room = :room";
    $filteredParams[':room'] = $room;
}

// Search filter for filtered count
if (!empty($search)) {
    $filteredQuery .= " AND (s.Stu_no LIKE :search OR s.Stu_id LIKE :search OR CONCAT(s.Stu_pre, s.Stu_name, ' ', s.Stu_sur) LIKE :search)";
    $filteredParams[':search'] = '%' . $search . '%';
}

$filteredStmt = $db->prepare($filteredQuery);
$filteredStmt->execute($filteredParams);
$recordsFiltered = $filteredStmt->fetch(PDO::FETCH_ASSOC)['filtered'];

// Add LIMIT for pagination
$query .= " LIMIT " . intval($start) . ", " . intval($length);

$stmt = $db->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format data for DataTables
$data = [];
$stats = ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0];

foreach ($students as $std) {
    $status = $std['attendance_status'];
    if ($status == '1') $stats['present']++;
    else if ($status == '2') $stats['absent']++;
    else if ($status == '3') $stats['late']++;
    else if (in_array($status, ['4', '5'])) $stats['leave']++;

    $data[] = [
        'stu_no' => $std['Stu_no'],
        'stu_id' => $std['Stu_id'],
        'stu_name' => $std['Stu_pre'] . $std['Stu_name'] . ' ' . $std['Stu_sur'],
        'attendance_status' => $std['attendance_status'],
        'arrival_time' => $std['arrival_time'],
        'leave_time' => $std['leave_time'],
        'reason' => $std['reason'],
        'checked_by' => $std['checked_by'],
        'attendance_time' => $std['attendance_time'],
        'has_attendance' => !empty($std['attendance_id'])
    ];
}

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => intval($recordsTotal),
    'recordsFiltered' => intval($recordsFiltered),
    'data' => $data,
    'stats' => $stats
]);
exit;

function getStatusBadge($status) {
    if (empty($status)) {
        return '<span class="status-badge inline-block px-3 py-1.5 bg-gray-200 rounded-full text-gray-600">⚠️ ยังไม่เช็ค</span>';
    }
    
    switch ($status) {
        case '1':
            return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full text-white shadow-md">✅ มาเรียน</span>';
        case '2':
            return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-red-400 to-rose-500 rounded-full text-white shadow-md">❌ ขาดเรียน</span>';
        case '3':
            return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full text-white shadow-md">⏰ มาสาย</span>';
        case '4':
            return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-400 to-cyan-500 rounded-full text-white shadow-md">🏥 ลาป่วย</span>';
        case '5':
            return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-full text-white shadow-md">📄 ลากิจ</span>';
        case '6':
            return '<span class="status-badge inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-pink-400 to-fuchsia-500 rounded-full text-white shadow-md">🎯 กิจกรรมโรงเรียน</span>';
        default:
            return '<span class="status-badge inline-block px-3 py-1.5 bg-gray-200 rounded-full text-gray-600">➖</span>';
    }
}

function getCheckedByBadge($std) {
    if (!empty($std['checked_by'])) {
        if ($std['checked_by'] === 'system' || $std['checked_by'] === 'teacher') {
            return '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full text-white text-sm font-medium shadow-md">👨‍🏫 ครูที่ปรึกษา</span>';
        } elseif ($std['checked_by'] === 'officer') {
            return '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-purple-400 to-violet-500 rounded-full text-white text-sm font-medium shadow-md">👮 เจ้าหน้าที่</span>';
        } elseif ($std['checked_by'] === 'rfid' || $std['checked_by'] === 'RFID') {
            $time = !empty($std['attendance_time']) ? date('H:i', strtotime($std['attendance_time'])) : null;
            $timeStr = $time ? ' <span class="text-xs opacity-90">(' . htmlspecialchars($time) . ')</span>' : '';
            return '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full text-white text-sm font-medium shadow-md">💳 สแกนบัตร' . $timeStr . '</span>';
        } else {
            return '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-200 rounded-full text-gray-700 text-sm font-medium">' . htmlspecialchars($std['checked_by']) . '</span>';
        }
    } else {
        return '<span class="text-gray-400">➖</span>';
    }
}