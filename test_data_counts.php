<?php
require_once "config/Database.php";
try {
    $db = (new Database("phichaia_student"))->getConnection();

    $stmt1 = $db->query("SELECT COUNT(*) FROM sdq_self WHERE Term = 1");
    echo "Term 1 self count: " . $stmt1->fetchColumn() . "\n";

    $stmt2 = $db->query("SELECT COUNT(*) FROM sdq_teach WHERE Term = 1");
    echo "Term 1 teach count: " . $stmt2->fetchColumn() . "\n";

    $stmt3 = $db->query("SELECT COUNT(*) FROM sdq_par WHERE Term = 1");
    echo "Term 1 par count: " . $stmt3->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Err: " . $e->getMessage();
}
