<?php
require_once("../../config/Database.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$class = isset($_GET['class']) ? $_GET['class'] : '';
$room = isset($_GET['room']) ? $_GET['room'] : '';

if ($class !== '' && $room !== '') {
    $stmt = $db->prepare("SELECT Stu_id, Stu_no, Stu_pre, Stu_name, Stu_sur FROM student WHERE Stu_status=1 AND Stu_major=? AND Stu_room=? ORDER BY Stu_no ASC");
    $stmt->execute([$class, $room]);
    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result);
} else {
    echo json_encode([]);
}
