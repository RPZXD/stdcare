<?php
require_once("../../config/Database.php");

$db = (new Database("phichaia_student"))->getConnection();
$class = $_GET['class'] ?? '';

if ($class) {
    $stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_major = :class AND Stu_status = 1 ORDER BY Stu_room ASC");
    $stmt->bindParam(':class', $class);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($rooms);
} else {
    echo json_encode([]);
}
?>
