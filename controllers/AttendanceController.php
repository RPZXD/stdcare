<?php
// (ปรับ Path ตามโครงสร้างของคุณ)
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
require_once(__DIR__ . "/../models/SettingModel.php");
require_once(__DIR__ . "/../models/AttendanceModel.php");
require_once(__DIR__ . "/../class/UserLogin.php"); 
require_once(__DIR__ . "/../class/Poor.php");
require_once(__DIR__ . "/../config/Database.php");

use App\DatabaseUsers;
use App\Models\SettingModel;
use App\Models\AttendanceModel;

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');

try {
    // 1. Setup Database and Models
    $connectDB = new DatabaseUsers();
    $db = $connectDB->getPDO();
    
    $settingsModel = new SettingModel($db);
    $attendanceModel = new AttendanceModel($db);
    // Prepare user/term/year for actions that need them
    $user = new UserLogin($db);
    $term = $user->getTerm();
    $year = $user->getPee();
    
    // ดึงการตั้งค่าเวลา (ใช้สำหรับทุก action)
    $timeSettings = $settingsModel->getAllTimeSettings();

    // 2. Routing (ดูว่า Frontend ขอ action อะไร)
    $action = $_GET['action'] ?? '';

    switch ($action) {
        
        // Action: ดึงประวัติทั้งหมดสำหรับตาราง
        case 'get_log':
            $data = $attendanceModel->getTodayAttendanceLog($timeSettings);
            echo json_encode(['data' => $data]);
            break;

        // Action: บันทึกการเช็คชื่อแบบกลุ่ม (จากหน้า teacher) -> ใช้ POST และคืนค่า JSON
        case 'save_bulk':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
                exit;
            }

            // Use the school database connection for attendance class
            require_once(__DIR__ . "/../class/Attendance.php");
            require_once(__DIR__ . "/../class/Behavior.php");
            require_once(__DIR__ . "/../class/Utils.php");

            try {
                $schoolDbObj = new \Database("phichaia_student");
                $schoolDb = $schoolDbObj->getConnection();
                $attendanceClass = new Attendance($schoolDb);
                $behaviorClass = new Behavior($schoolDb);

                // Read POST payload
                $date = $_POST['date'] ?? date('Y-m-d');
                $stu_ids = $_POST['Stu_id'] ?? [];
                $statuses = $_POST['attendance_status'] ?? [];
                $reasons = $_POST['reason'] ?? [];
                $behavior_names = $_POST['behavior_name'] ?? [];
                $behavior_scores = $_POST['behavior_score'] ?? [];
                $teach_ids = $_POST['teach_id'] ?? [];
                $term_post = $_POST['term'] ?? $term;
                $year_post = $_POST['pee'] ?? $year;

                // determine checked_by as enum-like indicator (not user id)
                // store 'teacher' when a teacher is logged in, otherwise fallback to 'system'
                if (session_status() == PHP_SESSION_NONE) session_start();
                $checked_by = 'system';
                if (!empty($_SESSION['Teacher_login'])) {
                    $checked_by = 'teacher';
                }

                // Save bulk attendance
                $savedCount = $attendanceClass->saveAttendanceBulk($stu_ids, $statuses, $reasons, $date, $term_post, $year_post, $checked_by);

                // Optionally create behavior records for late students (status == '3')
                foreach ($stu_ids as $stu_id) {
                    $status = $statuses[$stu_id] ?? '1';
                    if ($status == '3') {
                        $behavior_type = 'มาโรงเรียนสาย';
                        $behavior_name = $behavior_names[$stu_id] ?? 'มาโรงเรียนสาย';
                        $behavior_score = !empty($behavior_scores[$stu_id]) && $behavior_scores[$stu_id] != 5 ? $behavior_scores[$stu_id] : 5;
                        $teach_id = $teach_ids[$stu_id] ?? $checked_by;

                        // avoid duplicate for the same date
                        $stmt = $schoolDb->prepare("SELECT id FROM behavior WHERE stu_id = :stu_id AND behavior_date = :date AND behavior_type = :behavior_type LIMIT 1");
                        $stmt->execute([':stu_id' => $stu_id, ':date' => $date, ':behavior_type' => $behavior_type]);
                        if (!$stmt->fetch()) {
                            $behaviorClass->stu_id = $stu_id;
                            $behaviorClass->behavior_date = $date;
                            $behaviorClass->behavior_type = $behavior_type;
                            $behaviorClass->behavior_name = $behavior_name;
                            $behaviorClass->behavior_score = $behavior_score;
                            $behaviorClass->teach_id = $teach_id;
                            $behaviorClass->term = $term_post;
                            $behaviorClass->pee = $year_post;
                            $behaviorClass->create();
                        }
                    }
                }

                // Build per-student result snapshot by querying student_attendance for each student
                $perResults = [];
                $stmtFetch = $schoolDb->prepare("SELECT attendance_status, reason, checked_by, attendance_time, attendance_date FROM student_attendance WHERE student_id = :stu_id AND attendance_date = :date LIMIT 1");
                foreach ($stu_ids as $stu_id) {
                    $stmtFetch->execute([':stu_id' => $stu_id, ':date' => $date]);
                    $row = $stmtFetch->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $perResults[$stu_id] = [
                            'attendance_status' => $row['attendance_status'],
                            'reason' => $row['reason'],
                            'checked_by' => $row['checked_by'],
                            'attendance_time' => $row['attendance_time'],
                            'attendance_date' => $row['attendance_date']
                        ];
                    } else {
                        $perResults[$stu_id] = null;
                    }
                }

                echo json_encode(['success' => true, 'saved' => $savedCount, 'results' => $perResults]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;

        // Action: ดึงการสแกนล่าสุดสำหรับแสดงผล
        case 'get_last_scan':
            $data = $attendanceModel->getLastScan($timeSettings);
            echo json_encode($data);
            break;

        // Action: ประมวลผลการสแกนบัตร
        case 'scan':
            $rfid = $_POST['rfid'] ?? '';
            $device_id = intval($_POST['device_id'] ?? 1);

            if (empty($rfid)) {
                http_response_code(400);
                echo json_encode(['error' => 'ไม่พบรหัส RFID']);
                exit;
            }

            // (ดึง $term, $year จาก UserLogin class)
            $user = new UserLogin($db);
            $term = $user->getTerm();
            $year = $user->getPee();
            
            $result = $attendanceModel->processRfidScan($rfid, $device_id, $timeSettings, $term, $year);
            echo json_encode($result);
            break;

        // Action: เจ้าหน้าที่บันทึกการเช็คชื่อด้วยตนเอง (กรณีนักเรียนลืมบัตร)
        case 'manual_scan':
            // รับค่า student_id และ scan_type (arrival|leave) และ device_id (optional)
            $student_id = $_POST['student_id'] ?? '';
            $scan_type = $_POST['scan_type'] ?? '';
            $device_id = intval($_POST['device_id'] ?? 0);

            if (empty($student_id) || !in_array($scan_type, ['arrival', 'leave'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing student_id or invalid scan_type']);
                exit;
            }

            // Determine current staff id from session if available
            if (session_status() == PHP_SESSION_NONE) session_start();
            $staff_id = null;
            $possible_keys = ['Teacher_login', 'Officer_login', 'Director_login', 'Admin_login', 'Group_leader_login'];
            foreach ($possible_keys as $k) {
                if (!empty($_SESSION[$k])) { $staff_id = $_SESSION[$k]; break; }
            }

            $result = $attendanceModel->processManualScan($student_id, $scan_type, $device_id, $staff_id, $timeSettings, $term, $year);
            echo json_encode($result);
            break;

        // Action: ดึงจำนวนวันที่ลืมบัตรสำหรับนักเรียน (term/year ปัจจุบัน)
        case 'get_forgot_count':
            $student_id = $_GET['student_id'] ?? '';
            if (empty($student_id)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing student_id']);
                exit;
            }
            try {
                $count_stmt = $db->prepare("SELECT COUNT(DISTINCT forgot_date) FROM forgot_card WHERE student_id = :stu_id AND term = :term AND year = :year");
                $count_stmt->execute([':stu_id' => $student_id, ':term' => $term, ':year' => $year]);
                $count = (int) $count_stmt->fetchColumn();
                echo json_encode(['count' => $count]);
            } catch (\PDOException $e) {
                // fallback: count without term/year
                try {
                    $count_stmt = $db->prepare("SELECT COUNT(DISTINCT forgot_date) FROM forgot_card WHERE student_id = :stu_id");
                    $count_stmt->execute([':stu_id' => $student_id]);
                    $count = (int) $count_stmt->fetchColumn();
                    echo json_encode(['count' => $count]);
                } catch (\Exception $ex) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to fetch forgot count']);
                }
            }
            break;

        // Action: ดึงประวัติ forgot_card (สำหรับ DataTables)
        case 'get_forgot_history':
            // optional filter by student_id
            $filter_student = $_GET['student_id'] ?? null;
            try {
                $sql = "SELECT f.id, f.student_id, f.forgot_date, f.staff_id, f.term, f.year, f.note, f.created_at,
                               s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room
                        FROM forgot_card f
                        LEFT JOIN student s ON f.student_id = s.Stu_id";
                $params = [];
                if ($filter_student) {
                    $sql .= " WHERE f.student_id = :student_id";
                    $params[':student_id'] = $filter_student;
                } else {
                    // default to current term/year
                    $sql .= " WHERE f.term = :term AND f.year = :year";
                    $params[':term'] = $term;
                    $params[':year'] = $year;
                }
                $sql .= " ORDER BY f.forgot_date DESC, f.created_at DESC LIMIT 0, 2000";

                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // normalize rows for DataTables
                $data = [];
                foreach ($rows as $r) {
                    $data[] = [
                        'id' => $r['id'],
                        'student_id' => $r['student_id'],
                        'fullname' => ($r['Stu_pre'] ?? '') . ($r['Stu_name'] ?? '') . ' ' . ($r['Stu_sur'] ?? ''),
                        'class' => isset($r['Stu_major']) ? 'ม.' . $r['Stu_major'] . '/' . $r['Stu_room'] : '',
                        'forgot_date' => $r['forgot_date'],
                        'staff_id' => $r['staff_id'],
                        'note' => $r['note'],
                        'created_at' => $r['created_at']
                    ];
                }
                echo json_encode(['data' => $data]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['data' => [], 'error' => $e->getMessage()]);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }

} catch (\Exception $e) {
    // 3. Error Handling
    $code = $e->getCode() == 404 ? 404 : 500;
    http_response_code($code);
    echo json_encode(['error' => $e->getMessage()]);
}
?>