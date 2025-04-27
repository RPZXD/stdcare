<?php
function readJsonLog($filepath, $actionType) {
    if (!file_exists($filepath)) return [];
    $json = file_get_contents($filepath);
    $data = json_decode($json, true);
    if (!is_array($data)) return [];
    foreach ($data as &$row) {
        $row['log_type'] = $actionType;
    }
    return $data;
}
$loginLogs = readJsonLog(__DIR__ . '/../logs/login.json', 'login');
$logoutLogs = readJsonLog(__DIR__ . '/../logs/logout.json', 'logout');
$allLogs = array_merge($loginLogs, $logoutLogs);

// Filter logic
$userIdFilter = trim($_GET['user_id'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');
$actionFilter = trim($_GET['action'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo = trim($_GET['date_to'] ?? '');

$filteredLogs = array_filter($allLogs, function($log) use ($userIdFilter, $roleFilter, $actionFilter, $statusFilter, $dateFrom, $dateTo) {
    if ($userIdFilter !== '' && (!isset($log['user_id']) || stripos((string)$log['user_id'], $userIdFilter) === false)) return false;
    if ($roleFilter !== '' && (!isset($log['role']) || stripos((string)$log['role'], $roleFilter) === false)) return false;
    if ($actionFilter !== '') {
        if ($actionFilter === 'login') {
            if (!isset($log['log_type']) || $log['log_type'] !== 'login' || !in_array($log['action_type'] ?? '', ['login_success', 'Student login successful'])) return false;
        } elseif ($actionFilter === 'logout') {
            if (!isset($log['log_type']) || $log['log_type'] !== 'logout') return false;
        } elseif ($actionFilter === 'login_attempt') {
            if (!isset($log['log_type']) || $log['log_type'] !== 'login' || ($log['action_type'] ?? '') !== 'login_attempt') return false;
        }
    }
    if ($statusFilter !== '') {
        if ($statusFilter === 'success' && ($log['status_code'] ?? 0) != 200) return false;
        if ($statusFilter === 'fail' && ($log['status_code'] ?? 0) == 200) return false;
    }
    if ($dateFrom !== '') {
        $logDate = substr($log['access_time'] ?? '', 0, 10);
        if ($logDate < $dateFrom) return false;
    }
    if ($dateTo !== '') {
        $logDate = substr($log['access_time'] ?? '', 0, 10);
        if ($logDate > $dateTo) return false;
    }
    return true;
});

// Sort
usort($filteredLogs, function($a, $b) {
    return strtotime($b['access_time']) <=> strtotime($a['access_time']);
});

// DataTables server-side
$draw = intval($_GET['draw'] ?? 1);
$start = intval($_GET['start'] ?? 0);
$length = intval($_GET['length'] ?? 10);
$recordsTotal = count($allLogs);
$recordsFiltered = count($filteredLogs);

// Paging
$pagedLogs = array_slice($filteredLogs, $start, $length);

// Format for DataTables
$data = [];
foreach ($pagedLogs as $log) {
    $datetime = htmlspecialchars($log['access_time'] ?? '');
    $userId = htmlspecialchars($log['user_id'] ?? '-');
    $role = htmlspecialchars($log['role'] ?? '-');
    $ip = htmlspecialchars($log['ip_address'] ?? '-');
    $action = ($log['log_type'] === 'login')
        ? (($log['action_type'] ?? '') === 'login_success' || ($log['action_type'] ?? '') === 'Student login successful' ? '<span class="text-green-600 font-semibold">Login</span>' : '<span class="text-yellow-600 font-semibold">Login Attempt</span>')
        : '<span class="text-red-600 font-semibold">Logout</span>';
    $status = ($log['status_code'] ?? 0) == 200
        ? '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Success</span>'
        : '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Fail</span>';
    $message = htmlspecialchars($log['message'] ?? '');
    $data[] = [
        'datetime' => $datetime,
        'userId' => $userId,
        'role' => $role,
        'ip' => $ip,
        'action' => $action,
        'status' => $status,
        'message' => $message
    ];
}

header('Content-Type: application/json');
echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
]);
