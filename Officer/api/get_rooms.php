<?php
require_once("../../config/Database.php");
header('Content-Type: application/json');

$class = $_GET['class'] ?? null;
if (!$class) {
    echo json_encode(["success" => false, "error" => "Missing class"]);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

try {
    $stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_major = :class AND Stu_status = 1 ORDER BY Stu_room ASC");
    $stmt->bindParam(':class', $class);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        "success" => true,
        "rooms" => $rooms
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
