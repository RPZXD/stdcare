<?php
require_once __DIR__ . '/../classes/DatabaseUsers.php';
$db = new App\DatabaseUsers();
$pdo = $db->getPDO();

$sql = "CREATE TABLE IF NOT EXISTS student_gps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Stu_id VARCHAR(50) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    accuracy DECIMAL(10, 2),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (Stu_id)
)";

try {
    $pdo->exec($sql);
    echo "Table student_gps created successfully.";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage();
}
