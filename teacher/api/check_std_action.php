<?php
include_once("../../config/Database.php");
include_once("../../class/Attendance.php");
include_once("../../class/Behavior.php");
include_once("../../class/Utils.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$attendance = new Attendance($db);

// เพิ่ม Behavior instance สำหรับหักคะแนน
$behavior = new Behavior($db);

$date = $_POST['date'] ?? date('Y-m-d');
$stu_ids = $_POST['Stu_id'] ?? [];
$statuses = $_POST['attendance_status'] ?? [];
$reasons = $_POST['reason'] ?? [];
$behavior_types = $_POST['behavior_type'] ?? [];
$behavior_names = $_POST['behavior_name'] ?? [];
$behavior_scores = $_POST['behavior_score'] ?? [];
$teach_ids = $_POST['teach_id'] ?? [];
$term = $_POST['term'] ?? (date('n') >= 5 && date('n') <= 10 ? 1 : 2);
$year = $_POST['pee'] ?? date('Y');
$checked_by = 'system';

// เรียกใช้ฟังก์ชันในคลาส Attendance
$result = $attendance->saveAttendanceBulk($stu_ids, $statuses, $reasons, $date, $term, $year, $checked_by);

// หักคะแนน (หรือเพิ่มคะแนน) กรณีมีข้อมูล behavior จากฟอร์มและยังไม่มีการบันทึกในวันนั้น
foreach ($stu_ids as $stu_id) {
    if (($statuses[$stu_id] ?? '1') == '3') {
        // Always use 'มาโรงเรียนสาย' for late
        $behavior_type = 'มาโรงเรียนสาย';
        $behavior_name = $behavior_names[$stu_id] ?? 'มาโรงเรียนสาย';
        $behavior_score = !empty($behavior_scores[$stu_id]) && $behavior_scores[$stu_id] != 5 ? $behavior_scores[$stu_id] : 5;
        $teach_id = $teach_ids[$stu_id] ?? ($_SESSION['Teacher_login'] ?? null);
        $this_term = $term;
        $this_pee = $year;

        $stmt = $db->prepare("SELECT id FROM behavior WHERE stu_id = :stu_id AND behavior_date = :date AND behavior_type = :behavior_type LIMIT 1");
        $stmt->execute([':stu_id' => $stu_id, ':date' => $date, ':behavior_type' => $behavior_type]);
        if (!$stmt->fetch()) {
            $behavior->stu_id = $stu_id;
            $behavior->behavior_date = $date;
            $behavior->behavior_type = $behavior_type;
            $behavior->behavior_name = $behavior_name;
            $behavior->behavior_score = $behavior_score;
            $behavior->teach_id = $teach_id;
            $behavior->term = $this_term;
            $behavior->pee = $this_pee;
            $behavior->create();
        }
    }
}

// แสดง SweetAlert2 หลังบันทึกสำเร็จ
$sw2 = new SweetAlert2(
    "บันทึกการเช็คชื่อสำเร็จ",
    "success",
    "../check_student.php?tab=check&date=" . urlencode($date)
);
$sw2->renderAlert();
exit;
