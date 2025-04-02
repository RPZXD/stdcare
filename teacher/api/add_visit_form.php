<?php
require_once "../../config/Database.php";
require_once "../../class/StudentVisit.php";

$db = (new Database("phichaia_student"))->getConnection();
$visitHome = new StudentVisit($db);

$term = $_GET['term'] ?? null;
$pee = $_GET['pee'] ?? null;
$stuId = $_GET['stuId'] ?? null;

if (!$term || !$pee || !$stuId) {
    echo '<p class="text-danger">ข้อมูลไม่ครบถ้วน</p>';
    exit;
}

$data = $visitHome->getStudentById($stuId); // Fetch student data

if ($data) {
    error_log("Student data fetched successfully: " . print_r($data, true)); // Debug log
    include 'add_visit_form_template.php'; // Include the form template
} else {
    error_log("Student data not found for StuId: $stuId");
    echo '<p class="text-danger">ไม่พบข้อมูลนักเรียน</p>';
}
?>
