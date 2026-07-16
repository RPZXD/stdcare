<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/SettingModel.php';
use App\DatabaseUsers;
use App\Models\SettingModel;

date_default_timezone_set('Asia/Bangkok');

try {
    $db = new DatabaseUsers();
    $pdo = $db->getPDO();
    $settingsModel = new SettingModel($pdo);
    $timeSettings = $settingsModel->getAllTimeSettings();

    // 1. DataTables parameters
    $draw = intval($_GET['draw'] ?? 0);
    $start = intval($_GET['start'] ?? 0);
    $length = intval($_GET['length'] ?? 10);
    
    $orderColumnIndex = $_GET['order'][0]['column'] ?? 3; // default to scan_timestamp
    $orderColumnName = $_GET['columns'][$orderColumnIndex]['data'] ?? 'scan_timestamp';
    $orderDir = $_GET['order'][0]['dir'] ?? 'DESC';

    // Map column names to DB columns
    $columnMap = [
        'student_id' => 'l.student_id',
        'fullname' => 's.Stu_name',
        'class' => 's.Stu_major',
        'scan_timestamp' => 'l.scan_timestamp',
        'scan_type' => 'l.scan_type',
        'status' => 'status'
    ];
    $dbOrderColumn = $columnMap[$orderColumnName] ?? 'l.scan_timestamp';

    // 2. Filters
    $searchQuery = trim($_GET['search']['value'] ?? '');
    $dateFrom = trim($_GET['date_from'] ?? '');
    $dateTo = trim($_GET['date_to'] ?? '');
    $scanTypeFilter = trim($_GET['scan_type'] ?? '');

    // 3. Base Query (Filter by WEEKDAY: 5 = Saturday, 6 = Sunday)
    $baseQuery = "FROM attendance_log l 
                  INNER JOIN student s ON l.student_id = s.Stu_id
                  WHERE WEEKDAY(l.scan_timestamp) IN (5, 6)";
    
    $whereClause = "";
    $params = [];

    if ($searchQuery !== '') {
        $whereClause .= " AND (l.student_id LIKE :search OR s.Stu_name LIKE :search OR s.Stu_sur LIKE :search)";
        $params[':search'] = "%$searchQuery%";
    }
    if ($dateFrom !== '') {
        $whereClause .= " AND l.scan_timestamp >= :date_from";
        $params[':date_from'] = $dateFrom . ' 00:00:00';
    }
    if ($dateTo !== '') {
        $whereClause .= " AND l.scan_timestamp <= :date_to";
        $params[':date_to'] = $dateTo . ' 23:59:59';
    }
    if ($scanTypeFilter !== '') {
        $whereClause .= " AND l.scan_type = :scan_type";
        $params[':scan_type'] = $scanTypeFilter;
    }

    // 4. Counts
    $totalStmt = $pdo->query("SELECT COUNT(l.id) as total " . $baseQuery);
    $totalRecords = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $filteredStmt = $pdo->prepare("SELECT COUNT(l.id) as total " . $baseQuery . $whereClause);
    $filteredStmt->execute($params);
    $filteredRecords = $filteredStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 5. Fetch Page Data
    $arrival_late = $timeSettings['arrival_late_time'] ?? '08:00:00';
    $arrival_absent = $timeSettings['arrival_absent_time'] ?? '10:00:00';
    $leave_early = $timeSettings['leave_early_time'] ?? '15:40:00';

    $sql = "SELECT 
                l.student_id,
                l.scan_type,
                l.scan_timestamp,
                s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room,
                CASE
                    WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) <= :arrival_late THEN 'normal_arrival'
                    WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_late AND TIME(l.scan_timestamp) <= :arrival_absent THEN 'late_arrival'
                    WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_absent THEN 'absent_arrival'
                    WHEN l.scan_type = 'leave' AND TIME(l.scan_timestamp) < :leave_early THEN 'early_leave'
                    WHEN l.scan_type = 'leave' THEN 'normal_leave'
                    ELSE 'unknown'
                END as status
            " . $baseQuery . $whereClause . "
            ORDER BY $dbOrderColumn $orderDir 
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $length, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $start, PDO::PARAM_INT);
    $stmt->bindValue(':arrival_late', $arrival_late);
    $stmt->bindValue(':arrival_absent', $arrival_absent);
    $stmt->bindValue(':leave_early', $leave_early);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($rows as $row) {
        $timestamp = strtotime($row['scan_timestamp']);
        $thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        $thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        
        $dayName = $thaiDays[date('w', $timestamp)];
        $d = date('j', $timestamp);
        $m = $thaiMonths[date('n', $timestamp) - 1];
        $y = date('Y', $timestamp) + 543;
        $timeStr = date('H:i:s', $timestamp);

        $formattedDate = "วัน{$dayName}ที่ {$d} {$m} {$y} ({$timeStr})";

        // PDPA Masking: Student ID (Keep first 5, mask last 3 or similar)
        $stuId = $row['student_id'];
        $len = strlen($stuId);
        if ($len > 3) {
            $maskedId = substr($stuId, 0, $len - 3) . '***';
        } else {
            $maskedId = '***';
        }

        // PDPA Masking: Surname (Keep first char, rest ***)
        $surname = $row['Stu_sur'] ?? '';
        if (!empty($surname)) {
            $maskedSurname = mb_substr($surname, 0, 1, 'UTF-8') . '***';
        } else {
            $maskedSurname = '***';
        }

        $fullname = ($row['Stu_pre'] ?? '') . $row['Stu_name'] . ' ' . $maskedSurname;

        $data[] = [
            'student_id' => $maskedId,
            'fullname' => $fullname,
            'class' => htmlspecialchars($row['Stu_major'] . '/' . $row['Stu_room']),
            'scan_timestamp' => $row['scan_timestamp'],
            'formatted_date' => $formattedDate,
            'scan_type' => $row['scan_type'] === 'arrival' ? '🔵 เข้าเรียน' : '🔴 ออกโรงเรียน',
            'status_type' => $row['status']
        ];
    }

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'draw' => $draw ?? 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}
?>
