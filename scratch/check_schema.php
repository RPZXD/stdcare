<?php
require_once __DIR__ . '/classes/DatabaseUsers.php';
$db = new App\DatabaseUsers();
$pdo = $db->getPDO();
$stmt = $pdo->query("DESCRIBE student");
echo "Table: student\n";
while ($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
echo "\n";

$stmt = $pdo->query("DESCRIBE visithome");
echo "Table: visithome\n";
while ($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
