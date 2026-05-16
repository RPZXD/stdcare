<?php
require_once __DIR__ . '/../classes/DatabaseUsers.php';
$db = new App\DatabaseUsers();
$pdo = $db->getPDO();

function tableExists($pdo, $table) {
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
    } catch (Exception $e) {
        return false;
    }
    return $result !== false;
}

$tables = ['student', 'visithome', 'student_gps'];
foreach ($tables as $table) {
    if (tableExists($pdo, $table)) {
        echo "Table $table exists.\n";
        $stmt = $pdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch()) {
            echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Table $table does not exist.\n";
    }
}
