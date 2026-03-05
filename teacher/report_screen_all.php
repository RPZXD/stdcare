<?php
/**
 * Screen 11 Report All - Controller
 * MVC Pattern
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Fetch all teachers in this room for signatures
$roomTeachers = $teacher->getTeachersByClassAndRoom($class, $room);

// 11 screening fields definition
$screenFields = [
    ['label' => '1. ความสามารถพิเศษ', 'key' => 'special_ability', 'choices' => ['ไม่มี', 'มี']],
    ['label' => '2. ด้านการเรียน', 'key' => 'study_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '3. ด้านสุขภาพ', 'key' => 'health_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '4. ด้านเศรษฐกิจ', 'key' => 'economic_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '5. ด้านสวัสดิภาพและความปลอดภัย', 'key' => 'welfare_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '6. ด้านพฤติกรรมการใช้สารเสพติด', 'key' => 'drug_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '7. ด้านพฤติกรรมการใช้ความรุนแรง', 'key' => 'violence_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '8. ด้านพฤติกรรมทางเพศ', 'key' => 'sex_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '9. ด้านการติดเกม', 'key' => 'game_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
    ['label' => '10. นักเรียนที่มีความต้องการพิเศษ', 'key' => 'special_need_status', 'choices' => ['ไม่มี', 'มี']],
    ['label' => '11. ด้านการใช้เครื่องมือสื่อสารอิเล็กทรอนิกส์', 'key' => 'it_status', 'choices' => ['ปกติ', 'เสี่ยง', 'มีปัญหา']],
];

// Fetch students in this classroom
$students = [];
$stmt = $db->prepare("SELECT Stu_id, Stu_sex FROM student WHERE Stu_major = :class AND Stu_room = :room AND Stu_status = 1");
$stmt->bindParam(':class', $class);
$stmt->bindParam(':room', $room);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $students[$row['Stu_id']] = $row['Stu_sex'];
}
$total_students = count($students);

// Fetch latest screening data per student
$screenData = [];
if ($total_students > 0) {
    $ids = array_keys($students);
    $in = str_repeat('?,', count($ids) - 1) . '?';
    $sql = "SELECT * FROM student_screening WHERE student_id IN ($in) AND pee = ? AND created_at IN (
        SELECT MAX(created_at) FROM student_screening WHERE student_id IN ($in) AND pee = ? GROUP BY student_id
    )";
    $params = array_merge($ids, [$pee], $ids, [$pee]);
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $screenData[$row['student_id']] = $row;
    }
}

// Build summary
$summary = [];
foreach ($screenFields as $field) {
    $summary[$field['key']] = [];
    foreach ($field['choices'] as $choice) {
        $summary[$field['key']][$choice] = ['male' => 0, 'female' => 0, 'total' => 0];
    }
}

foreach ($students as $stu_id => $sex) {
    $data = $screenData[$stu_id] ?? [];
    foreach ($screenFields as $field) {
        $val = $data[$field['key']] ?? null;
        foreach ($field['choices'] as $choice) {
            if ($val === $choice) {
                $gender = ($sex == 'ช' || $sex == 'ชาย' || $sex == '1') ? 'male' : 'female';
                $summary[$field['key']][$choice][$gender]++;
                $summary[$field['key']][$choice]['total']++;
            }
        }
    }
}

// Calculate percentages
foreach ($screenFields as $field) {
    foreach ($field['choices'] as $choice) {
        $summary[$field['key']][$choice]['percent'] = $total_students > 0
            ? round(($summary[$field['key']][$choice]['total'] / $total_students) * 100, 2)
            : 0;
    }
}

$screened_count = count($screenData);
$pageTitle = 'สรุปสถิติคัดกรอง 11 ด้าน';

// Include the view
include __DIR__ . '/../views/teacher/report_screen_all.php';
