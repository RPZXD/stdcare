<?php
require_once('../config/Database.php');
require_once('../class/Student.php');

$database = new Database("phichaia_student");
$db = $database->getConnection();
$student = new Student($db);

$level = isset($_GET['level']) ? $_GET['level'] : '1-3';
$levels = explode('-', $level);
$classes = range($levels[0], $levels[1]);
// $date = date("Y-m-d");
$date = '2567-02-28';

$labels = [];
$data = [
    'มาเรียน' => [],
    'ขาดเรียน' => [],
    'ลาป่วย' => [],
    'ลากิจ' => [],
    'มาสาย' => [],
    'เข้าร่วมกิจกรรม' => []
];

foreach ($classes as $class) {
    for ($room = 1; $room <= ($class <= 3 ? 12 : 7); $room++) {
        $labels[] = "ม.$class/$room";
        $statusCounts = $student->getStudyStatusCount($class, $date);
        $statusMap = array_column($statusCounts, 'count', 'Study_status');
        $data['มาเรียน'][] = $statusMap['มาเรียน'] ?? 0;
        $data['ขาดเรียน'][] = $statusMap['ขาดเรียน'] ?? 0;
        $data['ลาป่วย'][] = $statusMap['ลาป่วย'] ?? 0;
        $data['ลากิจ'][] = $statusMap['ลากิจ'] ?? 0;
        $data['มาสาย'][] = $statusMap['มาสาย'] ?? 0;
        $data['เข้าร่วมกิจกรรม'][] = $statusMap['เข้าร่วมกิจกรรม'] ?? 0;
    }
}

$response = [
    'labels' => $labels,
    'datasets' => [
        ['label' => 'มาเรียน', 'backgroundColor' => '#4caf50', 'data' => $data['มาเรียน']],
        ['label' => 'ขาดเรียน', 'backgroundColor' => '#f44336', 'data' => $data['ขาดเรียน']],
        ['label' => 'ลาป่วย', 'backgroundColor' => '#ffeb3b', 'data' => $data['ลาป่วย']],
        ['label' => 'ลากิจ', 'backgroundColor' => '#ff9800', 'data' => $data['ลากิจ']],
        ['label' => 'มาสาย', 'backgroundColor' => '#9c27b0', 'data' => $data['มาสาย']],
        ['label' => 'เข้าร่วมกิจกรรม', 'backgroundColor' => '#2196f3', 'data' => $data['เข้าร่วมกิจกรรม']]
    ]
];

echo json_encode($response);
?>
