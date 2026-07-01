<?php
$dsn = 'mysql:host=localhost:3306;dbname=phichaia_student;charset=utf8';
$pdo = new PDO($dsn, 'root', '');

// Find classrooms with poor students
$stmt = $pdo->query('
    SELECT s.Stu_major, s.Stu_room, COUNT(*) as count
    FROM student s
    INNER JOIN tb_poor p ON s.Stu_id = p.Stu_id
    GROUP BY s.Stu_major, s.Stu_room
    ORDER BY count DESC
');
echo "Classrooms with poor students:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
