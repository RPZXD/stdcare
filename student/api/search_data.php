<?php
header('Content-Type: application/json');
require_once("../../config/Database.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([]);
    exit;
}

$type = isset($_POST['type']) ? $_POST['type'] : '';
$search = isset($_POST['search']) ? trim($_POST['search']) : '';

if ($search === '' || !in_array($type, ['student', 'teacher'])) {
    echo json_encode([]);
    exit;
}

$db = (new Database("phichaia_student"))->getConnection();

if ($type === 'student') {
    $sql = "SELECT * FROM student 
            WHERE (Stu_id LIKE :search 
               OR Stu_name LIKE :search 
               OR Stu_sur LIKE :search)
            AND Stu_status = 1  
            LIMIT 20";
    $stmt = $db->prepare($sql);
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    $stmt->execute();

    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'Stu_id'      => $row['Stu_id'],
            'Stu_pre'     => $row['Stu_pre'],
            'Stu_name'    => $row['Stu_name'],
            'Stu_no'      => $row['Stu_no'],
            'Stu_nick'    => $row['Stu_nick'],
            'Stu_phone'   => $row['Stu_phone'],
            'Par_phone'   => $row['Par_phone'],
            'Stu_picture' => $row['Stu_picture'],
        ];
    }
    echo json_encode($results);
    exit;
}

if ($type === 'teacher') {
    $sql = "SELECT * FROM teacher 
            WHERE (Teach_id LIKE :search 
               OR Teach_name LIKE :search )
            AND Teach_status = 1  
            LIMIT 20";
    $stmt = $db->prepare($sql);
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    $stmt->execute();

    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'Teach_id'    => $row['Teach_id'],
            'Teach_name'  => $row['Teach_name'],
            'Teach_photo' => isset($row['Teach_photo']) ? $row['Teach_photo'] : '',
            'Teach_major' => isset($row['Teach_major']) ? $row['Teach_major'] : '',
            'Teach_sex'   => isset($row['Teach_sex']) ? $row['Teach_sex'] : '',
            'Teach_birth' => isset($row['Teach_birth']) ? $row['Teach_birth'] : '',
            'Teach_addr'  => isset($row['Teach_addr']) ? $row['Teach_addr'] : '',
            'Teach_phone' => isset($row['Teach_phone']) ? $row['Teach_phone'] : '',
            'Teach_class' => isset($row['Teach_class']) ? $row['Teach_class'] : '',
            'Teach_room'  => isset($row['Teach_room']) ? $row['Teach_room'] : '',
        ];
    }
    echo json_encode($results);
    exit;
}
