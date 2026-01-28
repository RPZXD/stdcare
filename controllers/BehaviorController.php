<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
header('Content-Type: application/json');

// (1) เรียกใช้คลาสหลัก
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/DatabaseLogger.php';
require_once __DIR__ . '/../models/BehaviorModel.php';
require_once __DIR__ . '/../class/UserLogin.php';

use App\DatabaseUsers;
use App\Models\BehaviorModel;

try {
    // (2) สร้างการเชื่อมต่อ, Logger, และ Model
    $db = new DatabaseUsers();
    $pdo = $db->getPDO();
    $logger = new DatabaseLogger($pdo);
    $model = new BehaviorModel($db);
    $userLogin = new UserLogin($pdo);

    // (3) ดึงข้อมูล Admin/User/Officer สำหรับ Log
    if (!isset($_SESSION['Admin_login']) && !isset($_SESSION['Teacher_login']) && !isset($_SESSION['Officer_login'])) {
        throw new Exception('ไม่ได้รับอนุญาต', 403);
    }
    // Determine acting user id and role. Prefer Admin -> Teacher -> Officer, fallback to 'system'
    $admin_id = $_SESSION['Admin_login'] ?? $_SESSION['Teacher_login'] ?? $_SESSION['Officer_login'] ?? 'system';
    // prevent undefined index notice and infer role when not explicitly set in session
    $admin_role = $_SESSION['role'] ?? (
        isset($_SESSION['Teacher_login']) ? 'Teacher' : (
            isset($_SESSION['Officer_login']) ? 'Officer' : (
                isset($_SESSION['Admin_login']) ? 'Admin' : 'System'
            )
        )
    );
    // teach_id should prefer Teacher_login, then Officer, then Admin
    $teach_id = $_SESSION['Teacher_login'] ?? $_SESSION['Officer_login'] ?? $_SESSION['Admin_login'] ?? $admin_id;

    // (4) ดึง เทอม/ปี ปัจจุบัน (จาก Server-side)
    $term = $userLogin->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
    $pee = $userLogin->getPee() ?: (date('Y') + 543);

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        case 'list':
            // Check if server-side processing is requested
            if (isset($_GET['draw'])) {
                $draw = intval($_GET['draw']);
                $start = intval($_GET['start'] ?? 0);
                $length = intval($_GET['length'] ?? 10);
                $search = $_GET['search']['value'] ?? '';
                $orderIdx = intval($_GET['order'][0]['column'] ?? 0);
                $orderDir = $_GET['order'][0]['dir'] ?? 'DESC';

                $result = $model->getBehaviorsServerSide($term, $pee, $start, $length, $search, $orderIdx, $orderDir);
                $totalRecords = $model->countAllBehaviors($term, $pee);

                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => intval($totalRecords),
                    "recordsFiltered" => intval($result['filtered']),
                    "data" => $result['data']
                ]);
            } else {
                // Fallback for any legacy code
                $data = $model->getAllBehaviors($term, $pee);
                echo json_encode($data ?: []);
            }
            break;

        case 'get':
            $id = $_GET['id'] ?? '';
            $data = $model->getBehaviorById($id);
            echo json_encode($data ?: []);
            break;

        // (เพิ่ม) Action สำหรับค้นหานักเรียน
        case 'search_student':
            $id = $_GET['id'] ?? '';
            $data = $model->getStudentPreview($id);
            echo json_encode($data ?: null);
            break;

        // (เพิ่ม) Action สำหรับค้นหาแบบ live-search (หลายผลลัพธ์)
        case 'search_students':
            $q = trim($_GET['q'] ?? '');
            $limit = intval($_GET['limit'] ?? 12);
            if ($q === '') {
                echo json_encode([]);
                break;
            }
            try {
                $rows = $model->searchStudents($q, $limit);
                echo json_encode($rows ?: []);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;

        // (เพิ่ม) คืนค่าสรุปคะแนนพฤติกรรมตามชั้น/ห้อง
        case 'class_list':
            $classReq = $_GET['class'] ?? '';
            $roomReq = $_GET['room'] ?? '';
            try {
                if ($classReq === '' || $roomReq === '') {
                    echo json_encode(['success' => false, 'message' => 'class or room missing', 'data' => []]);
                    break;
                }
                $rows = $model->getBehaviorSummaryByClass($classReq, $roomReq, $term, $pee);
                echo json_encode(['success' => true, 'data' => $rows]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
            }
            break;

        case 'create':
            $stu_id = $_POST['addStu_id'] ?? '';
            try {
                // compute score for logging (model will also compute and persist)
                $computedScore = $model->getScoreForType($_POST['addBehavior_type'] ?? '');
                if ($computedScore === null) {
                    $computedScore = isset($_POST['addBehavior_score']) ? intval($_POST['addBehavior_score']) : 0;
                }

                $success = $model->createBehavior($_POST, $teach_id, $term, $pee);
                if (!$success)
                    throw new Exception('Model returned false');

                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'behavior_create_success',
                    'status_code' => 200,
                    'message' => "User created behavior for Stu_id: $stu_id. Score: " . $computedScore
                ]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'behavior_create_fail',
                    'status_code' => 500,
                    'message' => "Failed to create behavior for Stu_id: $stu_id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'update':
            $id = $_POST['editId'] ?? '';
            $stu_id = $_POST['editStu_id'] ?? '';
            try {
                // compute score for logging (model will compute and persist)
                $computedScore = $model->getScoreForType($_POST['editBehavior_type'] ?? '');
                if ($computedScore === null) {
                    $computedScore = isset($_POST['editBehavior_score']) ? intval($_POST['editBehavior_score']) : 'N/A';
                }

                $model->updateBehavior($id, $_POST, $teach_id, $term, $pee);
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'behavior_update_success',
                    'status_code' => 200,
                    'message' => "User updated behavior ID: $id for Stu_id: $stu_id. Score: " . $computedScore
                ]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'behavior_update_fail',
                    'status_code' => 500,
                    'message' => "Failed to update behavior ID: $id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'delete':
            // Accept both form-encoded POST and raw JSON bodies (the frontend sends JSON)
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                $raw = file_get_contents('php://input');
                if ($raw) {
                    $json = json_decode($raw, true);
                    if (is_array($json)) {
                        $id = $json['id'] ?? $json['deleteId'] ?? '';
                    }
                }
            }

            try {
                if (empty($id))
                    throw new Exception('ID is empty');
                $success = $model->deleteBehavior($id);
                if (!$success)
                    throw new Exception('Model returned false');

                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'behavior_delete_success',
                    'status_code' => 200,
                    'message' => "User deleted behavior ID: $id"
                ]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'behavior_delete_fail',
                    'status_code' => 500,
                    'message' => "Failed to delete behavior ID: $id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        // ดึงรายละเอียดการถูกหักคะแนนของนักเรียนคนหนึ่ง
        case 'student_details':
            $stu_id = $_GET['stu_id'] ?? '';
            try {
                if ($stu_id === '') {
                    echo json_encode(['success' => false, 'message' => 'Student ID is required', 'data' => []]);
                    break;
                }
                $rows = $model->getStudentBehaviorDetails($stu_id, $term, $pee);
                echo json_encode(['success' => true, 'data' => $rows]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
            }
            break;

        // ดึงข้อมูลพฤติกรรมทั้งหมดของครูคนหนึ่ง
        case 'teacher_behaviors':
            $teacher_id = $_GET['teacher_id'] ?? '';
            try {
                if ($teacher_id === '') {
                    echo json_encode(['success' => false, 'message' => 'Teacher ID is required', 'data' => []]);
                    break;
                }
                $rows = $model->getBehaviorsByTeacherId($teacher_id, $term, $pee);
                echo json_encode(['success' => true, 'data' => $rows]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
            }
            break;

        // ดึงคะแนนเพิ่มจากกิจกรรมจิตอาสา (1 ชั่วโมง = 1 คะแนน)
        case 'volunteer_bonus':
            $stu_id = $_GET['stu_id'] ?? '';
            $classReq = $_GET['class'] ?? '';
            $roomReq = $_GET['room'] ?? '';
            try {
                // Connect to eventstd database
                require_once __DIR__ . '/../classes/DatabaseEventstd.php';
                $eventDb = new \App\DatabaseEventstd(); // This connects to phichaia_eventstd
                $eventPdo = $eventDb->getPDO();

                if (!empty($stu_id)) {
                    // Get bonus for single student
                    $sql = "SELECT COALESCE(SUM(a.hours), 0) AS bonus_hours
                            FROM student_activity_logs sal
                            INNER JOIN activities a ON sal.activity_id = a.id
                            WHERE sal.student_id = :stu_id 
                              AND a.category = 'จิตอาสา'
                              AND a.term = :term 
                              AND a.pee = :pee";
                    $stmt = $eventPdo->prepare($sql);
                    $stmt->execute(['stu_id' => $stu_id, 'term' => $term, 'pee' => $pee]);
                    $result = $stmt->fetch();
                    $bonusPoints = (int) ($result['bonus_hours'] ?? 0);
                    echo json_encode(['success' => true, 'bonus_points' => $bonusPoints]);
                } elseif (!empty($classReq) && !empty($roomReq)) {
                    // Get bonus for all students in class/room
                    // First get student IDs from main db
                    $studentsSql = "SELECT Stu_id FROM student WHERE Stu_status = '1' AND Stu_major = :class AND Stu_room = :room";
                    $studentsStmt = $pdo->prepare($studentsSql);
                    $studentsStmt->execute(['class' => $classReq, 'room' => $roomReq]);
                    $students = $studentsStmt->fetchAll(\PDO::FETCH_COLUMN);

                    $bonusData = [];
                    if (!empty($students)) {
                        $placeholders = implode(',', array_fill(0, count($students), '?'));
                        $sql = "SELECT sal.student_id, COALESCE(SUM(a.hours), 0) AS bonus_hours
                                FROM student_activity_logs sal
                                INNER JOIN activities a ON sal.activity_id = a.id
                                WHERE sal.student_id IN ($placeholders)
                                  AND a.category = 'จิตอาสา'
                                  AND a.term = ?
                                  AND a.pee = ?
                                GROUP BY sal.student_id";
                        $stmt = $eventPdo->prepare($sql);
                        $params = array_merge($students, [$term, $pee]);
                        $stmt->execute($params);
                        $rows = $stmt->fetchAll();
                        foreach ($rows as $row) {
                            $bonusData[$row['student_id']] = (int) $row['bonus_hours'];
                        }
                    }
                    echo json_encode(['success' => true, 'data' => $bonusData]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
                }
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        // ดึงรายละเอียดคะแนนจิตอาสารายบุคคล
        case 'volunteer_bonus_details':
            $stu_id = $_GET['stu_id'] ?? '';
            try {
                require_once __DIR__ . '/../classes/DatabaseEventstd.php';
                $eventDb = new \App\DatabaseEventstd();
                $eventPdo = $eventDb->getPDO();

                if (empty($stu_id)) {
                    echo json_encode(['success' => false, 'message' => 'Missing stu_id']);
                    break;
                }

                $sql = "SELECT sal.id, a.title AS activity_name, a.event_date AS activity_date, a.hours
                        FROM student_activity_logs sal
                        INNER JOIN activities a ON sal.activity_id = a.id
                        WHERE sal.student_id = :stu_id 
                          AND a.category = 'จิตอาสา'
                          AND a.term = :term 
                          AND a.pee = :pee
                        ORDER BY a.event_date DESC";
                $stmt = $eventPdo->prepare($sql);
                $stmt->execute(['stu_id' => $stu_id, 'term' => $term, 'pee' => $pee]);
                $rows = $stmt->fetchAll();

                echo json_encode(['success' => true, 'data' => $rows]);
            } catch (\Throwable $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Detail error: ' . $e->getMessage()]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (\Throwable $e) {
    $code = is_numeric($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(['error' => 'General Controller error: ' . $e->getMessage()]);
}
?>