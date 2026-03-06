<?php
require_once "config/Database.php";
try {
    $db = (new Database("phichaia_student"))->getConnection();

    // Find a student who HAS teach data in term 1, but maybe no self data
    $stmt = $db->query("SELECT Stu_id, Pee FROM sdq_teach WHERE Term = 1 LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "Found Student: " . $row['Stu_id'] . " Pee: " . $row['Pee'] . "\n";
    } else {
        echo "No students with teach data in term 1.\n";
    }
} catch (Exception $e) {
    echo "Err: " . $e->getMessage();
}
