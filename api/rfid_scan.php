<?php
require_once('../config/Database.php');
require_once('../class/UserLogin.php'); 

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');

$rfid = isset($_POST['rfid']) ? trim($_POST['rfid']) : '';
$device_id = isset($_POST['device_id']) ? intval($_POST['device_id']) : 1;

if (empty($rfid)) {
    http_response_code(400);
    echo json_encode(['error' => 'RFID not provided']);
    exit;
}

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    // --- 1. ดึงการตั้งค่าเวลาทั้งหมด (ตามข้อกำหนดใหม่) ---
    $settings_stmt = $conn->query("SELECT setting_key, setting_value FROM time_settings");
    $settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // ใช้เวลาตามที่คุณกำหนด
    $arrival_late_time = $settings['arrival_late_time'] ?? '08:00:00';
    $arrival_absent_time = $settings['arrival_absent_time'] ?? '10:00:00'; // <-- (ใหม่) เวลาตัดขาดเรียน
    $leave_early_time = $settings['leave_early_time'] ?? '15:40:00'; // <-- (ใหม่) เวลาออกปกติ
    $scan_crossover_time = $settings['scan_crossover_time'] ?? '12:00:00'; // เวลาตัดเช้า/บ่าย

    $user = new UserLogin($conn);
    $term = $user->getTerm();
    $year = $user->getPee(); // (หรือ getYear() ตามคลาสของคุณ)

    // --- 2. ค้นหานักเรียน ---
    $stmt = $conn->prepare(
        "SELECT s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture, s.Stu_status
         FROM student_rfid r
         INNER JOIN student s ON r.stu_id = s.Stu_id
         WHERE r.rfid_code = :rfid AND s.Stu_status = '1'"
    );
    $stmt->execute([':rfid' => $rfid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Student not found or inactive for this RFID']);
        exit;
    }

    $stu_id = $row['Stu_id'];
    $fullname = $row['Stu_pre'] . $row['Stu_name'] . ' ' . $row['Stu_sur'];
    $class = $row['Stu_major'] . '/' . $row['Stu_room'];
    $photo = !empty($row['Stu_picture']) ? 'https://std.phichai.ac.th/photo/' . $row['Stu_picture'] : 'assets/images/profile.png';

    // --- 3. ตรรกะการสแกน (เข้า/ออก) ---
    $now_datetime = date('Y-m-d H:i:s');
    $now_time = date('H:i:s');
    $today = date('Y-m-d');

    $scan_type = ($now_time < $scan_crossover_time) ? 'arrival' : 'leave';

    // --- 4. ตรวจสอบสแกนซ้ำ (ใน Log) ---
    $check_stmt = $conn->prepare(
        "SELECT 1 FROM attendance_log 
         WHERE student_id = :stu_id AND scan_type = :scan_type AND DATE(scan_timestamp) = :today 
         LIMIT 1"
    );
    $check_stmt->execute([':stu_id' => $stu_id, ':scan_type' => $scan_type, ':today' => $today]);

    if ($check_stmt->fetch()) {
        echo json_encode([
            'student_id' => $stu_id, 'fullname' => $fullname, 'class' => $class, 'photo' => $photo,
            'time' => $now_time, 'status' => 'สแกนซ้ำ',
            'is_duplicate' => true
        ]);
        exit;
    }

    // --- 5. บันทึก Log (เหมือนเดิม) ---
    $insert_stmt = $conn->prepare(
        "INSERT INTO attendance_log (student_id, scan_timestamp, scan_type, device_id, term, year)
         VALUES (:stu_id, :scan_time, :scan_type, :device_id, :term, :year)"
    );
    $insert_stmt->execute([
        ':stu_id' => $stu_id,
        ':scan_time' => $now_datetime,
        ':scan_type' => $scan_type,
        ':device_id' => $device_id,
        ':term' => $term,
        ':year' => $year
    ]);

    // --- 6. (ปรับปรุง) คำนวณสถานะตามตรรกะใหม่ ---
    $statusText = '';
    $statusCode = 0; // 0=ไม่ระบุ, 1=มาเรียน, 2=ขาด, 3=สาย, 4=ลาป่วย, 5=ลากิจ

    if ($scan_type === 'arrival') {
        if ($now_time > $arrival_absent_time) { // สแกนหลัง 10:00
            $statusText = 'ขาดเรียน'; // (มาสายมากจนระบบตัดขาด)
            $statusCode = 2; 
        } elseif ($now_time > $arrival_late_time) { // สแกนหลัง 08:00
            $statusText = 'มาสาย';
            $statusCode = 3;
        } else { // สแกนก่อน 08:00
            $statusText = 'มาเรียน';
            $statusCode = 1;
        }
    } else { // 'leave'
        $statusText = ($now_time < $leave_early_time) ? 'กลับก่อน' : 'กลับปกติ';
        $statusCode = 0; // เราจะไม่บันทึกสถานะ "กลับ" ลงตารางสรุป
    }


    // --- 7. (ใหม่) บันทึก/อัปเดต ตารางสรุป 'student_attendance' ---
    // (เฉพาะการสแกนเข้าที่มีผลต่อสถานะ มา/สาย/ขาด)
    if ($statusCode > 0 && $stu_id) { 
        try {
            // 7.1 ตรวจสอบสถานะเดิม (ถ้ามี)
            $check_sql = "SELECT attendance_status FROM student_attendance 
                          WHERE student_id = :stu_id AND attendance_date = :today";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->execute([':stu_id' => $stu_id, ':today' => $today]);
            $existing_status = $check_stmt->fetchColumn();

            // สถานะที่ครูบันทึก (ลาป่วย=4, ลากิจ=5, กิจกรรม=6) 
            // จะไม่ถูกเขียนทับโดย RFID
            $manual_statuses = [4, 5, 6]; 

            if ($existing_status === false) {
                // 7.2 ถ้ายังไม่มีข้อมูล -> INSERT
                $insert_att_sql = "INSERT INTO student_attendance 
                                   (student_id, attendance_date, attendance_status, attendance_time, checked_by, term, year, device_id)
                                   VALUES 
                                   (:stu_id, :today, :status, :time, 'RFID', :term, :year, :device_id)";
                $insert_att_stmt = $conn->prepare($insert_att_sql);
                $insert_att_stmt->execute([
                    ':stu_id' => $stu_id,
                    ':today' => $today,
                    ':status' => $statusCode,
                    ':time' => $now_time,
                    ':term' => $term,
                    ':year' => $year,
                    ':device_id' => $device_id
                ]);
            } elseif (!in_array($existing_status, $manual_statuses)) {
                // 7.3 มีข้อมูล และไม่ใช่สถานะที่ครูบันทึก (เช่น เป็น 'ขาดเรียน' (2) จาก Cron Job)
                // -> UPDATE
                $update_att_sql = "UPDATE student_attendance 
                                   SET attendance_status = :status, attendance_time = :time, checked_by = 'RFID', device_id = :device_id
                                   WHERE student_id = :stu_id AND attendance_date = :today";
                $update_att_stmt = $conn->prepare($update_att_sql);
                $update_att_stmt->execute([
                    ':status' => $statusCode,
                    ':time' => $now_time,
                    ':stu_id' => $stu_id,
                    ':today' => $today,
                    ':device_id' => $device_id
                ]);
            }
            // ถ้า $existing_status อยู่ใน $manual_statuses (เช่น ลาป่วย) -> ไม่ต้องทำอะไร

        } catch (PDOException $e) {
            // บันทึก Error ไว้ แต่ไม่ต้องหยุดการทำงาน
            error_log("Failed to update student_attendance: " . $e->getMessage());
        }
    }
    // --- สิ้นสุดส่วนที่เพิ่มใหม่ ---


    // --- 8. ส่งผลลัพธ์กลับ ---
    echo json_encode([
        'student_id' => $stu_id,
        'fullname' => $fullname,
        'class' => $class,
        'photo' => $photo,
        'time' => $now_time,
        'status' => $statusText, // สถานะที่คำนวณใหม่
        'is_duplicate' => false
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>