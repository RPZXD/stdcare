<?php
require_once("../../config/Database.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$stmt = $db->query("SELECT DISTINCT Stu_major FROM student WHERE Stu_status=1 ORDER BY Stu_major ASC");
$result = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $result[] = $row;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
