<?php
require_once("../../config/Database.php");
$db = (new Database("phichaia_student"))->getConnection();

$stmt = $db->query("SELECT DISTINCT Stu_major FROM student WHERE Stu_status=1 ORDER BY Stu_major ASC");
$result = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $result[] = $row;
}
header('Content-Type: application/json');
echo json_encode($result);
