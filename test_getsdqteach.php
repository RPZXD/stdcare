<?php
require_once "config/Database.php";
require_once "class/SDQ.php";
try {
    $db = (new Database("phichaia_student"))->getConnection();
    $sdq = new SDQ($db);
    $data = $sdq->getSDQTeachData('26326', '2567', '1');
    print_r($data);
} catch (Exception $e) {
    echo "Err: " . $e->getMessage();
}
