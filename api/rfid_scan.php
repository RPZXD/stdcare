<?php
require_once('../config/Database.php');
require_once('../class/Utils.php');
require_once('../class/UserLogin.php');

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

    // 2. บันทึกลง student_attendance (ถ้ายังไม่มีในวันนี้)
    // แปลงวันที่เป็น พ.ศ.
    $today_gregorian = date('Y-m-d');
    $today_parts = explode('-', $today_gregorian);
    $today_buddhist = ($today_parts[0] + 543) . '-' . $today_parts[1] . '-' . $today_parts[2];

    $now = date('H:i:s');

    // กำหนด attendance_status ตามช่วงเวลา
    if ($now < '08:00:00') {
        $attendance_status = '1'; // มาเรียน
    }  else {
        $attendance_status = '3'; // สาย
    }

    // ตรวจสอบว่ามี record วันนี้หรือยัง
    $stmt = $conn->prepare("SELECT id FROM student_attendance WHERE student_id = :stu_id AND attendance_date = :date AND device_id = :device_id");
    $stmt->execute([':stu_id' => $stu_id, ':date' => $today_buddhist, ':device_id' => $device_id]);
    if ($stmt->fetch()) {
        // อัปเดตเวลาและสถานะ
        $stmt2 = $conn->prepare("UPDATE student_attendance SET attendance_status = :status, attendance_time = :time, checked_by = 'rfid', term = :term, year = :year, device_id = :device_id WHERE student_id = :stu_id AND attendance_date = :date AND device_id = :device_id");
        $stmt2->execute([
            ':status' => $attendance_status,
            ':time' => $now,
            ':term' => $term,
            ':year' => $year,
            ':stu_id' => $stu_id,
            ':date' => $today_buddhist,
            ':device_id' => $device_id
        ]);
    } else {
        // เพิ่มใหม่
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

    // 3. คืนข้อมูลนักเรียนล่าสุด
    echo json_encode([
        'student_id' => $stu_id,
        'fullname' => $fullname,
        'class' => $class,
        'photo' => $photo,
        'time' => $now,
        'status' => ($attendance_status == '1' ? 'มาเรียน' : ($attendance_status == '3' ? 'สาย' : 'ขาดเรียน'))
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
