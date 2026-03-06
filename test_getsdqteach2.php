<?php
require_once "config/Database.php";
try {
    $db = (new Database("phichaia_student"))->getConnection();

    $stmt = $db->query("SELECT * FROM sdq_teach WHERE Stu_id = '26326'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);
} catch (Exception $e) {
    echo "Err: " . $e->getMessage();
}
