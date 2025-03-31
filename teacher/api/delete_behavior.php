<?php
include_once("../../config/Database.php");
include_once("../../class/Behavior.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$behavior = new Behavior($db);

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    if ($behavior->deleteBehavior($data->id)) {
        echo json_encode(["message" => "Behavior deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to delete Behavior"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Invalid request"]);
}
?>