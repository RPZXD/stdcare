<?php
include_once("../../config/Database.php");
include_once("../../class/Behavior.php");

header('Content-Type: application/json');

$group = isset($_GET['group']) ? intval($_GET['group']) : 0;
$term = isset($_GET['term']) ? intval($_GET['term']) : 1;
$pee = isset($_GET['pee']) ? intval($_GET['pee']) : 2567;

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$behavior = new Behavior($db);

$data = $behavior->getScoreBehaviorsGroup($group, $term, $pee);

if ($data && is_array($data)) {
    echo json_encode([
        "success" => true,
        "students" => $data
    ]);
} else {
    echo json_encode([
        "success" => false,
        "students" => []
    ]);
}
?>
