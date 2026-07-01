<?php
require_once __DIR__ . '/../config/Database.php';
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

echo "Table: teacher\n";
try {
    $stmt = $db->query("DESCRIBE teacher");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
