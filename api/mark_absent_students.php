<?php
// ตั้งค่า Path ให้ถูกต้อง
require_once(dirname(__DIR__) . '/config/Database.php');
require_once(dirname(__DIR__) . '/class/UserLogin.php'); 

date_default_timezone_set('Asia/Bangkok');

echo "Starting Absent Marker Cron Job at " . date('Y-m-d H:i:s') . "\n";

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    $user = new UserLogin($conn);
    $term = $user->getTerm();
    $year = $user->getPee();
    $today = date('Y-m-d');

    // SQL นี้จะเลือกนักเรียน (s) ที่ "กำลังเรียน" (Stu_status = '1')
    // ที่ยังไม่มีข้อมูล (a.id IS NULL) ในตาราง student_attendance ของวันนี้
    // และทำการ INSERT สถานะ "ขาดเรียน" (2) ให้
    
    $sql = "
        INSERT INTO student_attendance 
            (student_id, attendance_date, attendance_status, checked_by, term, year)
        SELECT 
            s.Stu_id, 
            :today, 
            '2', -- 2 = สถานะขาดเรียน
            'System', 
            :term, 
            :year
        FROM 
            student s
        LEFT JOIN 
            student_attendance a ON s.Stu_id = a.student_id AND a.attendance_date = :today
        WHERE 
            s.Stu_status = '1' 
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

} catch (Exception $e) {
    error_log("Cron Job Failed (mark_absent_students): " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Cron Job Finished.\n";
?>