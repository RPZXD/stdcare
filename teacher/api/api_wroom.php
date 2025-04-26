<?php
header('Content-Type: application/json');
require_once "../../config/Database.php";
require_once "../../class/Wroom.php";

$major = $_GET['major'] ?? '';
$room = $_GET['room'] ?? '';
$pee = $_GET['pee'] ?? '';

$db = (new Database("phichaia_student"))->getConnection();
$wroom = new Wroom($db);

$students = $wroom->getWroomStudents($major, $room, $pee);
echo json_encode($students);
