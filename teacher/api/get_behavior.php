<?php

include_once("../../config/Database.php");
include_once("../../class/Behavior.php");

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    $behavior = new Behavior($db);

    $result = $behavior->getBehaviorById($id);

    if ($result) {
        echo json_encode([
            'id' => $result['id'],
            'stu_id' => $result['stu_id'],
            'behavior_date' => $result['behavior_date'],
            'behavior_type' => $result['behavior_type'],
            'behavior_name' => $result['behavior_name']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Car not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
