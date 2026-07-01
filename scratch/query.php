<?php
$dsn = 'mysql:host=localhost:3306;dbname=phichaia_student;charset=utf8';
$pdo = new PDO($dsn, 'root', '');

// Find a teacher with class and room
$stmt = $pdo->query('SELECT Teach_id, Teach_name, Teach_class, Teach_room FROM teacher WHERE Teach_class IS NOT NULL AND Teach_class != "" AND Teach_class != "0" LIMIT 5');
echo "Classroom Teachers:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

// Find some poor students
$stmt = $pdo->query('SELECT * FROM tb_poor LIMIT 5');
echo "\nPoor Students:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
