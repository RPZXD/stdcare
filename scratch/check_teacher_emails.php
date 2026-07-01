<?php
require_once __DIR__ . '/../config/Database.php';
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

echo "Checking existing teacher emails...\n";
$stmt = $db->query("SELECT Teach_id, Teach_name, Teach_email FROM teacher WHERE Teach_email IS NOT NULL AND Teach_email != '' LIMIT 10");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['Teach_id'] . " | Name: " . $row['Teach_name'] . " | Email: " . $row['Teach_email'] . "\n";
}

$stmt = $db->query("SELECT COUNT(*) as count FROM teacher WHERE Teach_email IS NOT NULL AND Teach_email != ''");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total teachers with emails: " . $row['count'] . "\n";
?>
