<?php
header('Content-Type: application/json');
require_once("../../config/Database.php");
require_once("../../class/Behavior.php");
require_once("../../class/StudentVisit.php");
require_once("../../class/Poor.php");
require_once("../../class/SDQ.php");
require_once("../../class/EQ.php");
require_once("../../class/Screeningdata.php");
require_once("../../class/Homeroom.php");

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$term = $_GET['term'] ?? '';
$pee = $_GET['pee'] ?? '';

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$result = [
    "behavior_count" => "-",
    "visit_count" => "-",
    "poor_count" => "-",
    "sdq_count" => "-",
    "eq_count" => "-",
    "screen_count" => "-",
    "homeroom_count" => "-"
];

// Behavior (นับจำนวนเหตุการณ์ในเดือนนี้)
try {
    $behavior = new Behavior($db);
    $month = date('m');
    $year = date('Y');
    $stmt = $db->prepare("SELECT COUNT(*) FROM behavior WHERE behavior_term = :term AND behavior_pee = :pee AND stu_id IN (SELECT Stu_id FROM student WHERE Stu_major = :class AND Stu_room = :room AND Stu_status = 1) AND MONTH(behavior_date) = :month AND YEAR(behavior_date) = :year");
    $stmt->execute([
        ':term' => $term,
        ':pee' => $pee,
        ':class' => $class,
        ':room' => $room,
        ':month' => $month,
        ':year' => $year
    ]);
    $result["behavior_count"] = (int)$stmt->fetchColumn();
} catch (\Throwable $e) {}

// Visit Home (เยี่ยมบ้าน) - แยก 2 เทอม
try {
    $visit = new StudentVisit($db);
    $result["visit_count_t1"] = (int)$visit->getTotalVisitCount($class, $room, 1, $pee);
    $result["visit_count_t2"] = (int)$visit->getTotalVisitCount($class, $room, 2, $pee);
} catch (\Throwable $e) {}

// Poor (นักเรียนยากจน)
try {
    $poor = new Poor($db);
    $stmt = $db->prepare("SELECT COUNT(*) FROM tb_poor WHERE Stu_id IN (SELECT Stu_id FROM student WHERE Stu_major = :class AND Stu_room = :room AND Stu_status = 1)");
    $stmt->execute([':class' => $class, ':room' => $room]);
    $result["poor_count"] = (int)$stmt->fetchColumn();
} catch (\Throwable $e) {}

// SDQ (นับคนที่ประเมินแล้ว) - แยก 2 เทอม
try {
    $sdq = new SDQ($db);
    $data1 = $sdq->getSDQByClassAndRoom($class, $room, $pee, 1);
    $data2 = $sdq->getSDQByClassAndRoom($class, $room, $pee, 2);
    $count1 = 0; $count2 = 0;
    foreach ($data1 as $row) {
        if (($row['self_ishave'] ?? 0) || ($row['par_ishave'] ?? 0) || ($row['teach_ishave'] ?? 0)) $count1++;
    }
    foreach ($data2 as $row) {
        if (($row['self_ishave'] ?? 0) || ($row['par_ishave'] ?? 0) || ($row['teach_ishave'] ?? 0)) $count2++;
    }
    $result["sdq_count_t1"] = $count1;
    $result["sdq_count_t2"] = $count2;
} catch (\Throwable $e) {}

// EQ (นับคนที่ประเมินแล้ว) - แยก 2 เทอม
try {
    $eq = new EQ($db);
    $data1 = $eq->getEQByClassAndRoom($class, $room, $pee, 1);
    $data2 = $eq->getEQByClassAndRoom($class, $room, $pee, 2);
    $count1 = 0; $count2 = 0;
    foreach ($data1 as $row) {
        if ($row['eq_ishave'] ?? 0) $count1++;
    }
    foreach ($data2 as $row) {
        if ($row['eq_ishave'] ?? 0) $count2++;
    }
    $result["eq_count_t1"] = $count1;
    $result["eq_count_t2"] = $count2;
} catch (\Throwable $e) {}

// Screening (นับคนที่คัดกรอง 11 ด้านแล้ว) - แยก 2 เทอม
try {
    $screen = new ScreeningData($db);
    // สมมติว่ามี field term ใน student_screening ถ้าไม่มีให้ใช้ pee อย่างเดียว
    $data1 = $screen->getScreenByClassAndRoom($class, $room, $pee);
    $data2 = $screen->getScreenByClassAndRoom($class, $room, $pee);
    $count1 = 0; $count2 = 0;
    foreach ($data1 as $row) {
        if ($row['screen_ishave'] ?? 0 && ($row['term'] ?? 1) == 1) $count1++;
    }
    foreach ($data2 as $row) {
        if ($row['screen_ishave'] ?? 0 && ($row['term'] ?? 2) == 2) $count2++;
    }
    $result["screen_count_t1"] = $count1;
    $result["screen_count_t2"] = $count2;
} catch (\Throwable $e) {}

// Homeroom (นับกิจกรรมโฮมรูม)
try {
    $homeroom = new Homeroom($db);
    $data = $homeroom->fetchHomerooms($class, $room, $term, $pee);
    $result["homeroom_count"] = is_array($data) ? count($data) : 0;
} catch (\Throwable $e) {}

echo json_encode($result);
