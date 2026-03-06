<?php
require_once "config/Database.php";
try {
    $db = (new Database("phichaia_student"))->getConnection();

    $stmt = $db->query("DESCRIBE sdq_teach");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo $r['Field'] . ", ";
    }
} catch (Exception $e) {
    echo "Err: " . $e->getMessage();
}
