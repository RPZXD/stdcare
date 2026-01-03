<?php
/**
 * API: Fetch Deduct Group Data (JSON)
 * Refactored to return raw data for modern UI rendering
 */
header('Content-Type: application/json');
include_once("../../config/Database.php");
include_once("../../class/Behavior.php");

$group = $_GET['group'] ?? '';
$type = $_GET['type'] ?? 'all';
$term = $_GET['term'] ?? '1';
$pee = $_GET['pee'] ?? '2567';
$level = $_GET['level'] ?? '';
$class = $_GET['class'] ?? '';
$major = $_GET['major'] ?? '';
$room = $_GET['room'] ?? '';

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$behavior = new Behavior($db);

$allStudents = [];

if ($type === 'all') {
    for ($g = 1; $g <= 3; $g++) {
        $students = $behavior->getScoreBehaviorsGroup($g, $term, $pee);
        if ($students && is_array($students)) {
            $allStudents = array_merge($allStudents, $students);
        }
    }
} else {
    $students = $behavior->getScoreBehaviorsGroup($group, $term, $pee);
    if ($students) {
        if ($type === 'level') {
            if ($level === 'lower') {
                $allStudents = array_filter($students, fn($s) => intval($s['Stu_major']) >= 1 && intval($s['Stu_major']) <= 3);
            } else if ($level === 'upper') {
                $allStudents = array_filter($students, fn($s) => intval($s['Stu_major']) >= 4 && intval($s['Stu_major']) <= 6);
            } else {
                $allStudents = $students;
            }
        } else if ($type === 'class') {
            if ($class) {
                $allStudents = array_filter($students, fn($s) => intval($s['Stu_major']) === intval($class));
            } else {
                $allStudents = $students;
            }
        } else if ($type === 'room') {
            if ($major && $room) {
                $allStudents = array_filter($students, fn($s) => intval($s['Stu_major']) === intval($major) && intval($s['Stu_room']) === intval($room));
            } elseif ($major) {
                $allStudents = array_filter($students, fn($s) => intval($s['Stu_major']) === intval($major));
            } else {
                $allStudents = $students;
            }
        }
    }
}

// Global Sort by Class/Room/No
usort($allStudents, function($a, $b) {
    if ($a['Stu_major'] != $b['Stu_major']) return $a['Stu_major'] - $b['Stu_major'];
    if ($a['Stu_room'] != $b['Stu_room']) return $a['Stu_room'] - $b['Stu_room'];
    return $a['Stu_no'] - $b['Stu_no'];
});

// Final mapping to ensure clean response
$data = array_map(function($s) {
    return [
        'Stu_id' => $s['Stu_id'],
        'Stu_no' => $s['Stu_no'],
        'FullName' => ($s['Stu_pre'] ?? '') . ($s['Stu_name'] ?? '') . ' ' . ($s['Stu_sur'] ?? ''),
        'ClassRoom' => 'ม.' . ($s['Stu_major'] ?? '') . '/' . ($s['Stu_room'] ?? ''),
        'major' => $s['Stu_major'],
        'room' => $s['Stu_room'],
        'behavior_count' => (int)$s['behavior_count']
    ];
}, array_values($allStudents));

echo json_encode([
    'success' => true,
    'data' => $data
]);
