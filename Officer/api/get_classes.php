<?php
require_once("../../config/Database.php");
header('Content-Type: application/json');

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

try {
    $stmt = $db->prepare("SELECT DISTINCT Stu_major FROM student WHERE Stu_status = 1 ORDER BY Stu_major ASC");
    $stmt->execute();
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        "success" => true,
        "classes" => $classes
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
