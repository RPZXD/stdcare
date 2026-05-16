<?php
require_once __DIR__ . '/classes/DatabaseUsers.php';
$db = new App\DatabaseUsers();
$pdo = $db->getPDO();
$stmt = $pdo->query("SHOW TABLES");
while ($row = $stmt->fetch()) {
    echo $row[0] . "\n";
}
