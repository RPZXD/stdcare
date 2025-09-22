<?php
require_once('../config/Database.php');
require_once('../class/Utils.php');
require_once('../class/UserLogin.php');
require_once('../models/ScanTimeSettings.php');

header('Content-Type: application/json');

date_default_timezone_set('Asia/Bangkok');

// รับค่า rfid และ device_id จาก POST
$rfid = isset($_POST['rfid']) ? trim($_POST['rfid']) : '';
$device_id = isset($_POST['device_id']) ? intval($_POST['device_id']) : 1;

if ($rfid === '') {
    echo json_encode(['error' => 'RFID not provided']);
    exit;
}

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();
    // Initialize UserLogin class
    $user = new UserLogin($conn);

    // Fetch terms and pee
    $term = $user->getTerm();
    $year = $user->getPee();
    // 1. หา stu_id จาก student_rfid
    $stmt = $conn->prepare("SELECT sr.stu_id, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture
        FROM student_rfid sr
        INNER JOIN student s ON sr.stu_id = s.Stu_id
        WHERE sr.rfid_code = :rfid
        LIMIT 1");
    $stmt->execute([':rfid' => $rfid]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['error' => 'RFID not found']);
        exit;
    }

    $stu_id = $student['stu_id'];
    $fullname = $student['Stu_name'] . ' ' . $student['Stu_sur'];
    $class = $student['Stu_major'] . '/' . $student['Stu_room'];
    $photo = !empty($student['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.$student['Stu_picture'] : 'https://std.phichai.ac.th/dist/img/logo-phicha.png';

    // Initialize ScanTimeSettings
    $scanSettingsModel = new \App\Models\ScanTimeSettings($conn);
    $scanSettings = $scanSettingsModel->getSettings();

    // using settings (fallbacks handled by model)
    $arrival_cutoff = isset($scanSettings['arrival_cutoff']) ? $scanSettings['arrival_cutoff'] : '08:00:00';
    $arrival_absent_after = isset($scanSettings['arrival_absent_after']) ? $scanSettings['arrival_absent_after'] : '12:00:00';
    $leave_cutoff = isset($scanSettings['leave_cutoff']) ? $scanSettings['leave_cutoff'] : '15:00:00';

    $now = date('H:i:s');

    // กำหนด attendance_status ตามช่วงเวลา และ device_id
    // device_id 1 = เข้าห้องเช้า (arrive), device_id 2 = ออก/กลับ (leave)
    if ($device_id == 1) {
        if ($now <= $arrival_cutoff) {
            $attendance_status = '1'; // มาเรียน (on-time)
        } elseif ($now > $arrival_cutoff && $now <= $arrival_absent_after) {
            $attendance_status = '3'; // สาย
        } else {
            $attendance_status = '2'; // ขาด (arrived after absent threshold)
        }
    } else {
        // สำหรับการกลับ (device_id !=1)
        // ถ้ากลับก่อนเวลา leave_cutoff ถือว่าออกก่อนกำหนด (we'll use status '4' => กลับก่อน)
        if ($now >= $leave_cutoff) {
            $attendance_status = '5'; // กลับปกติ
        } else {
            $attendance_status = '4'; // กลับก่อนเวลา
        }
    }

    // ตรวจสอบว่ามี record วันนี้จาก device และ student หรือไม่
    $stmt = $conn->prepare("SELECT id, attendance_status, checked_by FROM student_attendance WHERE student_id = :stu_id AND attendance_date = :date");
    $stmt->execute([':stu_id' => $stu_id, ':date' => $today_buddhist]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // ถ้าเป็นการสแกนเข้าช่วงเช้า และยังไม่มีสถานะมาเรียน ให้เพิ่ม/อัพเดต
        if ($device_id == 1) {
            // update arrival info
            $stmt2 = $conn->prepare("UPDATE student_attendance SET attendance_status = :status, attendance_time = :time, checked_by = 'rfid', term = :term, year = :year WHERE id = :id");
            $stmt2->execute([
                ':status' => $attendance_status,
                ':time' => $now,
                ':term' => $term,
                ':year' => $year,
                ':id' => $existing['id']
            ]);
        } else {
            // device_id !=1 treat as leave/return scan: we insert a separate leave record
            $stmt2 = $conn->prepare("INSERT INTO student_attendance (student_id, attendance_date, attendance_time, attendance_status, checked_by, term, year, device_id) VALUES (:stu_id, :date, :time, :status, 'rfid', :term, :year, :device_id)");
            $stmt2->execute([
                ':stu_id' => $stu_id,
                ':date' => $today_buddhist,
                ':time' => $now,
                ':status' => $attendance_status,
                ':term' => $term,
                ':year' => $year,
                ':device_id' => $device_id
            ]);
        }
    } else {
        // no existing arrival record; for device_id==1 we create, for leave we also create but mark accordingly
        $stmt2 = $conn->prepare("INSERT INTO student_attendance (student_id, attendance_date, attendance_time, attendance_status, checked_by, term, year, device_id) VALUES (:stu_id, :date, :time, :status, 'rfid', :term, :year, :device_id)");
        $stmt2->execute([
            ':stu_id' => $stu_id,
            ':date' => $today_buddhist,
            ':time' => $now,
            ':status' => $attendance_status,
            ':term' => $term,
            ':year' => $year,
            ':device_id' => $device_id
        ]);
    }

    // Map status codes to text
    $statusTextMap = [
        '1' => 'มาเรียน',
        '2' => 'ขาดเรียน',
        '3' => 'มาสาย',
        '4' => 'กลับก่อน',
        '5' => 'กลับปกติ'
    ];

    // 3. คืนข้อมูลนักเรียนล่าสุด
    echo json_encode([
        'student_id' => $stu_id,
        'fullname' => $fullname,
        'class' => $class,
        'photo' => $photo,
        'time' => $now,
        'status' => ($statusTextMap[$attendance_status] ?? 'ไม่ระบุ')
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
