<?php
require_once("../../config/Database.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$class = isset($_GET['class']) ? $_GET['class'] : '';

if ($class !== '') {
    $stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_status=1 AND Stu_major=? ORDER BY Stu_room ASC");
    $stmt->execute([$class]);
    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result);
} else {
    echo json_encode([]);
}
