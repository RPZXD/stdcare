<?php
// ตั้งค่า header เป็น JSON
header('Content-Type: application/json');

// เรียกใช้คลาสเชื่อมต่อฐานข้อมูลตัวใหม่
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
use App\DatabaseUsers;

// (Optional) ตั้งค่า Timezone เป็นไทย
date_default_timezone_set('Asia/Bangkok');

try {
    // 1. เชื่อมต่อฐานข้อมูล
    $db = new DatabaseUsers();
    $pdo = $db->getPDO();

    // 2. รับค่าจาก DataTables
    $draw = intval($_GET['draw'] ?? 0);
    $start = intval($_GET['start'] ?? 0); // Offset
    $length = intval($_GET['length'] ?? 10); // Limit
    
    // การจัดเรียง
    $orderColumnIndex = $_GET['order'][0]['column'] ?? 1; // Default เรียงตามคอลัมน์ 1 (datetime)
    $orderColumnName = $_GET['columns'][$orderColumnIndex]['data'] ?? 'datetime';
    $orderDir = $_GET['order'][0]['dir'] ?? 'DESC';

    // แปลงชื่อคอลัมน์จาก JS (data:) เป็น DB (table field)
    $columnMap = [
        'datetime' => 'access_time',
        'userId' => 'user_id',
        'role' => 'role',
        'action' => 'action_type', // เราจะเรียงตาม action_type ก่อนแปลง
        'status' => 'status_code'  // เราจะเรียงตาม status_code ก่อนแปลง
    ];
    $dbOrderColumn = $columnMap[$orderColumnName] ?? 'access_time';


    // 3. รับค่า Filter
    $userIdFilter = trim($_GET['user_id'] ?? '');
    $roleFilter = trim($_GET['role'] ?? '');
    $actionFilter = trim($_GET['action'] ?? '');
    $statusFilter = trim($_GET['status'] ?? '');
    $dateFrom = trim($_GET['date_from'] ?? '');
    $dateTo = trim($_GET['date_to'] ?? '');

    // 4. สร้าง Query
    $baseQuery = "FROM app_logs";
    $whereClause = " WHERE 1=1";
    $params = []; // สำหรับ PDO prepared statements

    if ($userIdFilter !== '') {
        $whereClause .= " AND user_id LIKE :user_id";
        $params[':user_id'] = "%$userIdFilter%";
    }
    if ($roleFilter !== '') {
        $whereClause .= " AND role LIKE :role";
        $params[':role'] = "%$roleFilter%";
    }
    if ($actionFilter !== '') {
        if ($actionFilter === 'login') {
            $whereClause .= " AND action_type LIKE 'login%'";
        } else { // 'logout'
            $whereClause .= " AND action_type = :action_type";
            $params[':action_type'] = 'logout';
        }
    }
    if ($statusFilter !== '') {
        if ($statusFilter === 'success') {
            $whereClause .= " AND status_code = 200";
        } else { // 'fail'
            $whereClause .= " AND status_code != 200";
        }
    }
    if ($dateFrom !== '') {
        $whereClause .= " AND access_time >= :date_from";
        $params[':date_from'] = $dateFrom . ' 00:00:00';
    }
    if ($dateTo !== '') {
        $whereClause .= " AND access_time <= :date_to";
        $params[':date_to'] = $dateTo . ' 23:59:59';
    }

    // 5. ดึงข้อมูล
    
    // 5.1 นับจำนวนทั้งหมด (ไม่กรอง)
    $totalRecordsStmt = $pdo->query("SELECT COUNT(id) as total " . $baseQuery);
    $totalRecords = $totalRecordsStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 5.2 นับจำนวนที่กรองแล้ว
    $filteredRecordsStmt = $pdo->prepare("SELECT COUNT(id) as total " . $baseQuery . $whereClause);
    $filteredRecordsStmt->execute($params);
    $filteredRecords = $filteredRecordsStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 5.3 ดึงข้อมูลสำหรับหน้านี้ (พร้อมเรียงลำดับและแบ่งหน้า)
    $sql = "SELECT * " . $baseQuery . $whereClause . 
           " ORDER BY $dbOrderColumn $orderDir " .
           " LIMIT :limit OFFSET :offset";
           
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $length, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $start, PDO::PARAM_INT);
    // Bind ค่า filter
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. จัดรูปแบบข้อมูลสำหรับ DataTables
    $data = [];
    foreach ($logs as $log) {
        // ตกแต่งข้อมูล (Formatting)
        $action_type = $log['action_type'] ?? '';
        if (strpos($action_type, 'login') !== false) {
            $action = ($action_type === 'login_success')
                ? '<span class="badge badge-success">Login</span>'
                : '<span class="badge badge-warning">Login Attempt</span>';
        } else { // logout
             $action = '<span class="badge badge-danger">Logout</span>';
        }
        
        $status = ($log['status_code'] ?? 0) == 200
            ? '<span class="badge badge-light-green">Success</span>' // ใช้คลาส CSS ที่มีในระบบ AdminLTE
            : '<span class="badge badge-light-red">Fail</span>';

        $data[] = [
            // คอลัมน์ที่แสดงในตารางหลัก
            'datetime' => htmlspecialchars($log['access_time'] ?? ''),
            'userId' => htmlspecialchars($log['user_id'] ?? '-'),
            'role' => htmlspecialchars($log['role'] ?? '-'),
            'action' => $action, // ใช้ค่าที่ตกแต่งแล้ว
            'status' => $status, // ใช้ค่าที่ตกแต่งแล้ว
            
            // !! KEV: ส่งข้อมูลที่ซ่อนไว้ไปด้วย (สำหรับ Child Row)
            'message' => htmlspecialchars($log['message'] ?? ''),
            'ip' => htmlspecialchars($log['ip_address'] ?? '-'),
            'user_agent' => htmlspecialchars($log['user_agent'] ?? '-'),
            'url' => htmlspecialchars($log['url'] ?? '-')
        ];
    }

    // 7. ส่ง JSON กลับไป
    $output = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data,
    ];
    echo json_encode($output);

} catch (Exception $e) {
    // ส่งข้อผิดพลาดกลับไปในรูปแบบ JSON (สำคัญมากสำหรับ Debug)
    echo json_encode([
        "draw" => $draw ?? 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error processing request: " . $e->getMessage()
    ]);
}
?>