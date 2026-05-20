<?php
// ตั้งค่า Path ให้ถูกต้อง
require_once(dirname(__DIR__) . '/config/Database.php');
require_once(dirname(__DIR__) . '/class/UserLogin.php'); 

date_default_timezone_set('Asia/Bangkok');

echo "Starting Absent Marker Script at " . date('Y-m-d H:i:s') . "\n";

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    // ดึงเวลาปัจจุบัน
    $currentTime = date('H:i:s');
    $today = date('Y-m-d');
    
    // ดึงการตั้งค่าที่จำเป็นจากฐานข้อมูล
    $stmtSettings = $conn->prepare("SELECT setting_key, setting_value FROM time_settings WHERE setting_key IN ('arrival_absent_time', 'term_start_date', 'term_end_date', 'exclude_absent_grades')");
    $stmtSettings->execute();
    $settings = $stmtSettings->fetchAll(PDO::FETCH_KEY_PAIR);

    $term_start_date = $settings['term_start_date'] ?? null;
    $term_end_date = $settings['term_end_date'] ?? null;
    $absentTimeSetting = $settings['arrival_absent_time'] ?? null;
    $exclude_absent_grades = $settings['exclude_absent_grades'] ?? '';

    // เช็คระยะเวลาเปิด-ปิดภาคเรียน
    if ($term_start_date && $term_end_date) {
        if ($today < $term_start_date || $today > $term_end_date) {
            echo "วันนี้อยู่นอกระยะเวลาเปิด-ปิดภาคเรียน ไม่มีการเช็คขาดเรียน\n";
            exit;
        }
    }

    // เช็ควันเสาร์-อาทิตย์
    $dayOfWeek = date('N'); // 1 (จันทร์) - 7 (อาทิตย์)
    if ($dayOfWeek >= 6) {
        echo "วันนี้เป็นวันหยุดเสาร์-อาทิตย์ ไม่มีการเช็คขาดเรียน\n";
        exit;
    }

    // เช็ควันหยุดพิเศษจากฐานข้อมูล
    $stmtHoliday = $conn->prepare("SELECT description FROM school_holidays WHERE holiday_date = :today");
    $stmtHoliday->execute([':today' => $today]);
    $holiday = $stmtHoliday->fetchColumn();

    if ($holiday) {
        echo "วันนี้เป็นวันหยุดพิเศษ: " . $holiday . " (ไม่มีการเช็คขาดเรียน)\n";
        exit;
    }

    if (!$absentTimeSetting) {
        $absentTimeSetting = '9:00:00'; // Default ถ้าระบบไม่มีการตั้งค่า
    }

    echo "Current Time: " . $currentTime . "\n";
    echo "Absent Cut-off Time: " . $absentTimeSetting . "\n";

    // เช็คว่าเลยเวลาตัดขาดเรียนหรือยัง
    if ($currentTime < $absentTimeSetting) {
        echo "ยังไม่ถึงเวลาตัดขาดเรียน (รอให้ถึงเวลา " . $absentTimeSetting . ")\n";
        exit;
    }

    $user = new UserLogin($conn);
    $term = $user->getTerm();
    $year = $user->getPee();

    // ============================================================
    // ส่วนที่ 1: ตัดขาดเรียนนักเรียนที่ยังไม่ได้สแกน/เช็คชื่อ
    // ============================================================
    
    // จัดการเงื่อนไขยกเว้นระดับชั้น
    $excludeCondition = "";
    if (!empty(trim($exclude_absent_grades))) {
        $grades = array_map('intval', explode(',', $exclude_absent_grades));
        $grades = array_filter($grades, function($val) { return $val > 0; });
        if (!empty($grades)) {
            $gradesStr = implode(',', $grades);
            $excludeCondition = "AND s.Stu_major NOT IN ($gradesStr)";
            echo "Excluding grades: $gradesStr\n";
        }
    }

    $sql = "
        INSERT INTO student_attendance 
            (student_id, attendance_date, attendance_status, checked_by, term, year, reason)
        SELECT 
            s.Stu_id, 
            :today, 
            '2', -- 2 = สถานะขาดเรียน
            'System', 
            :term, 
            :year,
            'ระบบตัดขาดเรียนอัตโนมัติ'
        FROM 
            student s
        LEFT JOIN 
            student_attendance a ON s.Stu_id = a.student_id AND a.attendance_date = :today
        WHERE 
            s.Stu_status = '1' 
            $excludeCondition
            AND a.id IS NULL;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':today' => $today,
        ':term' => $term,
        ':year' => $year
    ]);

    $count = $stmt->rowCount();
    echo "Marked $count students as absent (Status 2) for $today.\n";

    // ============================================================
    // ส่วนที่ 2: หักคะแนนพฤติกรรมนักเรียนมาสาย (วันละ 1 ครั้ง)
    // ============================================================
    echo "\n--- Processing Late Student Behavior Deduction ---\n";

    // ดึงนักเรียนที่สถานะ "สาย" (3) ของวันนี้ ที่ยังไม่มีบันทึกหักคะแนนในวันนี้
    $sqlLate = "
        SELECT sa.student_id
        FROM student_attendance sa
        LEFT JOIN behavior b 
            ON sa.student_id = b.stu_id 
            AND b.behavior_date = :today_b
            AND b.behavior_type = 'มาโรงเรียนสาย'
        WHERE sa.attendance_date = :today_a
            AND sa.attendance_status = '3'
            AND b.id IS NULL
    ";

    $stmtLate = $conn->prepare($sqlLate);
    $stmtLate->execute([
        ':today_a' => $today,
        ':today_b' => $today
    ]);
    $lateStudents = $stmtLate->fetchAll(PDO::FETCH_COLUMN);

    if (empty($lateStudents)) {
        echo "ไม่มีนักเรียนมาสายที่ต้องหักคะแนนวันนี้\n";
    } else {
        $insertBehavior = $conn->prepare("
            INSERT INTO behavior 
                (stu_id, behavior_date, behavior_type, behavior_name, behavior_score, teach_id, behavior_term, behavior_pee)
            VALUES 
                (:stu_id, :behavior_date, :behavior_type, :behavior_name, :behavior_score, :teach_id, :term, :pee)
        ");

        $behaviorCount = 0;
        foreach ($lateStudents as $stuId) {
            try {
                $insertBehavior->execute([
                    ':stu_id'         => $stuId,
                    ':behavior_date'  => $today,
                    ':behavior_type'  => 'มาโรงเรียนสาย',
                    ':behavior_name'  => 'มาโรงเรียนสาย',
                    ':behavior_score' => 5,
                    ':teach_id'       => 'System',
                    ':term'           => $term,
                    ':pee'            => $year
                ]);
                $behaviorCount++;
            } catch (PDOException $e) {
                echo "Error deducting score for $stuId: " . $e->getMessage() . "\n";
            }
        }

        echo "หักคะแนนนักเรียนมาสายสำเร็จ: $behaviorCount คน (หักคนละ 5 คะแนน)\n";
    }

} catch (Exception $e) {
    error_log("Cron Job Failed (mark_absent_students): " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nScript Finished.\n";
?>