<?php
require_once("../../config/Database.php");
require_once("../../class/Behavior.php");
header('Content-Type: application/json');

$class = $_GET['class'] ?? null;
$room = $_GET['room'] ?? null;
$term = $_GET['term'] ?? null;
$pee = $_GET['pee'] ?? null;

if (!$class || !$room || !$term || !$pee) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$behavior = new Behavior($db);

$result = $behavior->getScoreBehaviorsClass($class, $room, $term, $pee);

echo json_encode([
    "success" => $result !== false,
    "students" => $result !== false ? $result : []
]);
