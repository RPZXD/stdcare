<?php
require_once __DIR__ . '/../classes/DatabaseUsers.php';
$db = new App\DatabaseUsers();
$pdo = $db->getPDO();

$output = "";
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
        $output .= "Table $table exists.\n";
        $stmt = $pdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch()) {
            $output .= "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        $output .= "Table $table does not exist.\n";
    }
}
file_put_contents(__DIR__ . '/gps_results.txt', $output);
echo "Done";
