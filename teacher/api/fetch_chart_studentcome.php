<?php
include_once("../../config/Database.php");
include_once("../../class/Student.php");

$database = new Database("phichaia_student");
$db = $database->getConnection();

$student = new Student($db);

$class = $_GET['class'];
$room = $_GET['room'];
$date = $_GET['date'];


$data = $student->getStudyStatusCountClassRoom2($class, $room, $date);

echo json_encode($data);
?>

