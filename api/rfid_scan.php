<?php
require_once('../config/Database.php');
require_once('../class/UserLogin.php'); // Assuming this class gets term/year

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

    // --- Fetch all time settings from the database ---
    $settings_stmt = $conn->query("SELECT setting_key, setting_value FROM time_settings");
    $settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $arrival_late_time = $settings['arrival_late_time'] ?? '08:00:00';
    $leave_early_time = $settings['leave_early_time'] ?? '15:50:00';
    $scan_crossover_time = $settings['scan_crossover_time'] ?? '12:00:00';

    $user = new UserLogin($conn);
    $term = $user->getTerm();
    $year = $user->getPee();

    $stmt = $conn->prepare("SELECT sr.stu_id, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture
        FROM student_rfid sr
        INNER JOIN student s ON sr.stu_id = s.Stu_id
        WHERE sr.rfid_code = :rfid AND s.Stu_status = '1'");
    $stmt->execute([':rfid' => $rfid]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $now_datetime = date('Y-m-d H:i:s');
        $now_time = date('H:i:s');

        // --- Automatically determine scan type based on crossover time ---
        $scan_type = ($now_time < $scan_crossover_time) ? 'arrival' : 'leave';

        $stu_id = $student['stu_id'];
        
        // --- Debounce Logic: Prevent duplicate scans ---
        $debounce_minutes = 3;
        $check_stmt = $conn->prepare(
            "SELECT id FROM attendance_log
             WHERE student_id = :stu_id
               AND scan_type = :scan_type
               AND scan_timestamp >= NOW() - INTERVAL :minutes MINUTE
             LIMIT 1"
        );
        $check_stmt->execute([
            ':stu_id' => $stu_id,
            ':scan_type' => $scan_type,
            ':minutes' => $debounce_minutes
        ]);

        $fullname = $student['Stu_name'] . ' ' . $student['Stu_sur'];
        $class = $student['Stu_major'] . '/' . $student['Stu_room'];
        $photo = !empty($student['Stu_picture']) ? 'https://std.phichai.ac.th/photo/'.$student['Stu_picture'] : 'https://std.phichai.ac.th/dist/img/logo-phicha.png';

        if ($check_stmt->rowCount() > 0) {
            echo json_encode([
                'student_id' => $stu_id,
                'fullname' => $fullname,
                'class' => $class,
                'photo' => $photo,
                'time' => $now_time,
                'status' => 'สแกนซ้ำ',
                'is_duplicate' => true
            ]);
            exit;
        }

        // --- If not a duplicate, insert the record ---
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

        // Calculate status using settings from the database
        $statusText = '';
        if ($scan_type === 'arrival') {
            $statusText = ($now_time > $arrival_late_time) ? 'มาสาย' : 'มาเรียน';
        } else {
            $statusText = ($now_time < $leave_early_time) ? 'กลับก่อน' : 'กลับปกติ';
        }

        echo json_encode([
            'student_id' => $stu_id,
            'fullname' => $fullname,
            'class' => $class,
            'photo' => $photo,
            'time' => $now_time,
            'status' => $statusText,
            'is_duplicate' => false
        ]);

    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Student not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>