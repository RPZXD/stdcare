<?php
require_once "../../config/Database.php";
require_once "../../class/Student.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Student class
$student = new Student($db);

// Get parameters from request
$class = isset($_GET['class']) ? $_GET['class'] : '';
$room = isset($_GET['room']) ? $_GET['room'] : '';

$response = array('success' => false, 'data' => array());

if (!empty($class) && !empty($room)) {
    $query = "SELECT Stu_id, Stu_no, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_phone, Stu_picture, Par_phone, Stu_citizenid, Stu_nick, Par_name, Stu_sex
              FROM student
              WHERE Stu_major = :class AND Stu_room = :room AND Stu_status = 1
              ORDER BY Stu_no ASC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':class', $class);
    $stmt->bindParam(':room', $room);
    $stmt->execute();

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($students) {
        $response['success'] = true;
        $response['data'] = $students;
    }
}

echo json_encode($response);
?>
