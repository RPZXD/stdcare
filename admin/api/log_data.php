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
    $orderColumnIndex = $_GET['order'][0]['column'] ?? 1; 
    $orderColumnName = $_GET['columns'][$orderColumnIndex]['data'] ?? 'datetime';
    $orderDir = $_GET['order'][0]['dir'] ?? 'DESC';

    // แปลงชื่อคอลัมน์จาก JS (data:) เป็น DB (table field)
    $columnMap = [
        'datetime' => 'access_time',
        'userId' => 'user_id',
        'role' => 'role',
        'action' => 'action_type', 
        'status' => 'status_code'  
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
    $params = []; 

    if ($userIdFilter !== '') {
        $whereClause .= " AND user_id LIKE :user_id";
        $params[':user_id'] = "%$userIdFilter%";
    }
    if ($roleFilter !== '') {
        $whereClause .= " AND role LIKE :role";
        $params[':role'] = "%$roleFilter%";
    }
    
    // !! KEV: แก้ไขการกรอง action
    if ($actionFilter !== '') {
        if ($actionFilter === 'login') {
            $whereClause .= " AND action_type LIKE 'login%'";
        } elseif ($actionFilter === 'logout') {
            $whereClause .= " AND action_type = 'logout'";
        } elseif ($actionFilter === 'student_admin') {
             $whereClause .= " AND action_type LIKE 'student_%'";
        }
        // (คุณสามารถเพิ่มเงื่อนไขอื่นๆ ได้ถ้าต้องการ)
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

    // 5.3 ดึงข้อมูลสำหรับหน้านี้
    $sql = "SELECT * " . $baseQuery . $whereClause . 
           " ORDER BY $dbOrderColumn $orderDir " .
           " LIMIT :limit OFFSET :offset";
           
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $length, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $start, PDO::PARAM_INT);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. จัดรูปแบบข้อมูลสำหรับ DataTables
    $data = [];
    foreach ($logs as $log) {
        
        //
        // !! KEV: นี่คือส่วนที่แก้ไข !!
        //
        $action_type = $log['action_type'] ?? '';
        $action = ''; // (ค่าเริ่มต้น)

        // ใช้ switch case จะอ่านง่ายกว่า
        switch ($action_type) {
            case 'login_success':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200"><i class="fas fa-sign-in-alt mr-1"></i>Login Success</span>';
                break;
            case 'login_fail':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200"><i class="fas fa-exclamation-triangle mr-1"></i>Login Failed</span>';
                break;
            case 'logout':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200"><i class="fas fa-sign-out-alt mr-1"></i>Logout</span>';
                break;
            
            // --- Log ของ Student ---
            case 'student_create_success':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200"><i class="fas fa-user-plus mr-1"></i>Student Created</span>';
                break;
            case 'student_update_success':
            case 'student_inline_update_success':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800 border border-cyan-200"><i class="fas fa-edit mr-1"></i>Student Updated</span>';
                break;
            case 'student_delete_success':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200"><i class="fas fa-trash mr-1"></i>Student Deleted</span>';
                break;
            case 'student_resetpwd_success':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200"><i class="fas fa-key mr-1"></i>Password Reset</span>';
                break;
            case 'student_rfid_upload_success':
                 $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200"><i class="fas fa-id-card mr-1"></i>RFID Upload</span>';
                 break;

            // --- Log ที่ล้มเหลว (แสดงเป็นสีส้ม) ---
            case 'student_create_fail':
            case 'student_update_fail':
            case 'student_inline_update_fail':
            case 'student_delete_fail':
            case 'student_resetpwd_fail':
            case 'student_rfid_upload_fail':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200"><i class="fas fa-times-circle mr-1"></i>Student Action Failed</span>';
                break;

            // --- Log ที่ไม่มีการเปลี่ยนแปลง ---
             case 'student_update_noop':
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200"><i class="fas fa-minus-circle mr-1"></i>No Changes</span>';
                break;
                
            default:
                // ถ้าเจอ action_type ที่ไม่รู้จัก ให้แสดงชื่อดิบๆ
                $action = '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200"><i class="fas fa-question-circle mr-1"></i>' . htmlspecialchars($action_type) . '</span>';
        }
        //
        // !! KEV: สิ้นสุดการแก้ไข !!
        //
        
        $status = ($log['status_code'] ?? 0) == 200
            ? '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200"><i class="fas fa-check-circle mr-1"></i>Success</span>'
            : '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200"><i class="fas fa-times-circle mr-1"></i>Failed</span>';

        $data[] = [
            // คอลัมน์ที่แสดงในตารางหลัก
            'datetime' => htmlspecialchars($log['access_time'] ?? ''),
            'userId' => htmlspecialchars($log['user_id'] ?? '-'),
            'role' => htmlspecialchars($log['role'] ?? '-'),
            'action' => $action, // ใช้ค่าที่ตกแต่งแล้ว
            'status' => $status, // ใช้ค่าที่ตกแต่งแล้ว
            
            // ข้อมูลที่ซ่อนไว้สำหรับ Child Row
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
    // ส่งข้อผิดพลาดกลับไปในรูปแบบ JSON
    echo json_encode([
        "draw" => $draw ?? 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error processing request: " . $e->getMessage()
    ]);
}
?>