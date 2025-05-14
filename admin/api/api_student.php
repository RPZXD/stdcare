<?php
include_once("../../config/Database.php");
include_once("../../class/Student.php");

header('Content-Type: application/json; charset=utf-8');
$allowed_referers = [
    'http://localhost/stdcare/admin/',
    'https://std.phichai.ac.th/admin/'
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
    echo json_encode([
        'success' => false,
        'message' => 'Forbidden'
    ]);
    exit;
}


$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$student = new Student($db);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'filters':
        // ดึงค่าชั้นและห้องที่มีในระบบ
        $classes = [];
        $rooms = [];
        $sqlClass = "SELECT DISTINCT Stu_major FROM student WHERE Stu_major IS NOT NULL AND Stu_major != '' ORDER BY Stu_major ASC";
        $sqlRoom = "SELECT DISTINCT Stu_room FROM student WHERE Stu_room IS NOT NULL AND Stu_room != '' ORDER BY Stu_room ASC";
        $classes = array_map(function($row) { return $row['Stu_major']; }, $db->query($sqlClass)->fetchAll(PDO::FETCH_ASSOC));
        $rooms = array_map(function($row) { return $row['Stu_room']; }, $db->query($sqlRoom)->fetchAll(PDO::FETCH_ASSOC));
        echo json_encode(['classes' => $classes, 'rooms' => $rooms]);
        break;
    case 'list':
        $class = $_GET['class'] ?? '';
        $room = $_GET['room'] ?? '';
        $status = $_GET['status'] ?? ''; // New status filter
        $data = $student->fetchFilteredStudents($class, $room, $status);
        echo json_encode($data ?: []);
        break;
    case 'get':
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if ($id === '') {
            echo json_encode(['error' => true, 'message' => 'รหัสนักเรียนไม่ถูกต้อง']);
            break;
        }
        $data = $student->getStudentById($id);
        if (is_array($data) && isset($data[0]) && !empty($data[0]['Stu_id'])) {
            echo json_encode($data[0]);
        } else {
            echo json_encode(['error' => true, 'message' => 'ไม่พบข้อมูลนักเรียน หรือข้อมูลไม่สมบูรณ์']);
        }
        break;
    case 'create':
        // รับค่าจากฟอร์ม add
        $stu_pre = $_POST['addStu_pre'] ?? '';
        $stu_sex = '';
        if ($stu_pre === 'เด็กชาย' || $stu_pre === 'นาย') {
            $stu_sex = 1;
        } else if ($stu_pre === 'เด็กหญิง' || $stu_pre === 'นางสาว') {
            $stu_sex = 2;
        }
        // กำหนด property ให้ตรงกับฐานข้อมูล
        $student->Stu_id = $_POST['addStu_id'] ?? '';
        $student->Stu_no = $_POST['addStu_no'] ?? '';
        $student->Stu_password = $_POST['addStu_id'] ?? '';
        $student->Stu_sex = $stu_sex;
        $student->Stu_pre = $stu_pre;
        $student->Stu_name = $_POST['addStu_name'] ?? '';
        $student->Stu_sur = $_POST['addStu_sur'] ?? '';
        $student->Stu_major = $_POST['addStu_major'] ?? '';
        $student->Stu_room = $_POST['addStu_room'] ?? '';
        $student->Stu_nick = '';
        $student->Stu_birth = '';
        $student->Stu_religion = '';
        $student->Stu_blood = '';
        $student->Stu_addr = '';
        $student->Stu_phone = '';
        $student->Stu_status = 1;
        // ตรวจสอบว่ามี Stu_id ซ้ำหรือไม่
        $exists = $student->getStudentById($student->Stu_id);
        if ($exists && isset($exists[0])) {
            echo json_encode(['success' => false, 'message' => 'รหัสนักเรียนนี้มีอยู่ในระบบแล้ว']);
            break;
        }
        try {
            $stmt = $db->prepare("INSERT INTO student 
                (Stu_id, Stu_no, Stu_password, Stu_sex, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_nick, Stu_birth, Stu_religion, Stu_blood, Stu_addr, Stu_phone, Stu_status)
                VALUES (:Stu_id, :Stu_no, :Stu_password, :Stu_sex, :Stu_pre, :Stu_name, :Stu_sur, :Stu_major, :Stu_room, :Stu_nick, :Stu_birth, :Stu_religion, :Stu_blood, :Stu_addr, :Stu_phone, :Stu_status)
            ");
            $success = $stmt->execute([
                ':Stu_id' => $student->Stu_id,
                ':Stu_no' => $student->Stu_no,
                ':Stu_password' => $student->Stu_password,
                ':Stu_sex' => $student->Stu_sex,
                ':Stu_pre' => $student->Stu_pre,
                ':Stu_name' => $student->Stu_name,
                ':Stu_sur' => $student->Stu_sur,
                ':Stu_major' => $student->Stu_major,
                ':Stu_room' => $student->Stu_room,
                ':Stu_nick' => $student->Stu_nick !== '' ? $student->Stu_nick : null,
                ':Stu_birth' => $student->Stu_birth !== '' ? $student->Stu_birth : null,
                ':Stu_religion' => $student->Stu_religion !== '' ? $student->Stu_religion : null,
                ':Stu_blood' => $student->Stu_blood !== '' ? $student->Stu_blood : null,
                ':Stu_addr' => $student->Stu_addr !== '' ? $student->Stu_addr : null,
                ':Stu_phone' => $student->Stu_phone !== '' ? $student->Stu_phone : null,
                ':Stu_status' => $student->Stu_status
            ]);
            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถเพิ่มข้อมูลได้'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดขณะเพิ่มข้อมูล'
            ]);
        }
        break;
    case 'update':
        // รับค่าจากฟอร์ม edit
        $stu_pre = $_POST['editStu_pre'] ?? '';
        $stu_sex = '';
        if ($stu_pre === 'เด็กชาย' || $stu_pre === 'นาย') {
            $stu_sex = 1;
        } else if ($stu_pre === 'เด็กหญิง' || $stu_pre === 'นางสาว') {
            $stu_sex = 2;
        }
        // Use property names as expected by Student.php
        $student->Stu_id = $_POST['editStu_id'] ?? '';
        $student->Stu_no = $_POST['editStu_no'] ?? '';
        $student->Stu_password = $_POST['editStu_id'] ?? '';
        $student->Stu_sex = $stu_sex;
        $student->Stu_pre = $stu_pre;
        $student->Stu_name = $_POST['editStu_name'] ?? '';
        $student->Stu_sur = $_POST['editStu_sur'] ?? '';
        $student->Stu_major = $_POST['editStu_major'] ?? '';
        $student->Stu_room = $_POST['editStu_room'] ?? '';
        $student->Stu_status = $_POST['editStu_status'] ?? 1;
        $student->OldStu_id = $_POST['editStu_id_old'] ?? '';
        $success = $student->updateStudentInfo();
        echo json_encode(['success' => $success]);
        break;
    case 'delete':
        $stu_id = $_POST['id'] ?? '';
        if ($stu_id) {
            // ดึงข้อมูลเดิม
            $data = $student->getStudentById($stu_id);
            if ($data && isset($data[0])) {
                // 1. ย้ายข้อมูลไป student_del
                $row = $data[0];
                $columns = array_keys($row);
                $colList = implode(',', array_map(function($col) { return "`$col`"; }, $columns));
                $placeholders = implode(',', array_map(function($col) { return ":$col"; }, $columns));
                $sqlInsert = "INSERT INTO student_del ($colList) VALUES ($placeholders)";
                $stmtInsert = $db->prepare($sqlInsert);
                foreach ($row as $col => $val) {
                    $stmtInsert->bindValue(":$col", $val);
                }
                $inserted = $stmtInsert->execute();

                // 2. ลบข้อมูลจาก student ถ้า insert สำเร็จ
                if ($inserted) {
                    $sqlDelete = "DELETE FROM student WHERE Stu_id = :stu_id";
                    $stmtDelete = $db->prepare($sqlDelete);
                    $stmtDelete->bindParam(':stu_id', $stu_id);
                    $deleted = $stmtDelete->execute();
                    echo json_encode(['success' => $deleted]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'ไม่สามารถย้ายข้อมูลไป student_del ได้']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียน']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'รหัสนักเรียนไม่ถูกต้อง']);
        }
        break;
    case 'resetpwd':
        $stu_id = $_POST['id'] ?? '';
        if ($stu_id) {
            // รีเซ็ตรหัสผ่านเป็นรหัสนักเรียน
            $data = $student->getStudentById($stu_id);
            if ($data && isset($data[0])) {
                $student->Stu_id = $stu_id;
                $student->Stu_no = $data[0]['Stu_no'];
                $student->Stu_password = $stu_id;
                $student->Stu_sex = $data[0]['Stu_sex'];
                $student->Stu_pre = $data[0]['Stu_pre'];
                $student->Stu_name = $data[0]['Stu_name'];
                $student->Stu_sur = $data[0]['Stu_sur'];
                $student->Stu_major = $data[0]['Stu_major'];
                $student->Stu_room = $data[0]['Stu_room'];
                $student->Stu_nick = $data[0]['Stu_nick'];
                $student->Stu_birth = $data[0]['Stu_birth'];
                $student->Stu_religion = $data[0]['Stu_religion'];
                $student->Stu_blood = $data[0]['Stu_blood'];
                $student->Stu_addr = $data[0]['Stu_addr'];
                $student->Stu_phone = $data[0]['Stu_phone'];
                $student->Stu_status = $data[0]['Stu_status'];
                $student->OldStu_id = $stu_id;
                $success = $student->updateStudentInfo();
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียน']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'รหัสนักเรียนไม่ถูกต้อง']);
        }
        break;
    case 'inline_update':
        $stu_id = $_POST['id'] ?? '';
        $field = $_POST['field'] ?? '';
        $value = $_POST['value'] ?? '';
        if (!$stu_id || !$field) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            break;
        }
        $updateFields = [];
        $params = [];
        if ($field === 'Stu_no') {
            $updateFields[] = 'Stu_no = :val';
            $params[':val'] = $value;
        } else if ($field === 'Stu_name') {
            $obj = json_decode($value, true);
            $updateFields[] = 'Stu_name = :name';
            $updateFields[] = 'Stu_sur = :sur';
            $params[':name'] = $obj['name'];
            $params[':sur'] = $obj['sur'];
        } else if ($field === 'Stu_pre_name_sur') {
            $obj = json_decode($value, true);
            $updateFields[] = 'Stu_pre = :pre';
            $updateFields[] = 'Stu_name = :name';
            $updateFields[] = 'Stu_sur = :sur';
            $params[':pre'] = $obj['pre'];
            $params[':name'] = $obj['name'];
            $params[':sur'] = $obj['sur'];
        } else if ($field === 'Stu_major_room') {
            $obj = json_decode($value, true);
            $updateFields[] = 'Stu_major = :major';
            $updateFields[] = 'Stu_room = :room';
            $params[':major'] = $obj['major'];
            $params[':room'] = $obj['room'];
        } else if ($field === 'Stu_status') {
            $updateFields[] = 'Stu_status = :val';
            $params[':val'] = $value;
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่รองรับฟิลด์นี้']);
            break;
        }
        $params[':id'] = $stu_id;
        $sql = "UPDATE student SET " . implode(',', $updateFields) . " WHERE Stu_id = :id";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute($params);
        echo json_encode(['success' => $success]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
