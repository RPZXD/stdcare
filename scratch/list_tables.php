<?php
require_once __DIR__ . '/../config/Database.php';
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

echo "Tables in database:\n";
$stmt = $db->query("SHOW TABLES");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo $row[0] . "\n";
}
?>
