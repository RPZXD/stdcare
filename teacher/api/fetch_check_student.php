<?php
require_once "../../config/Database.php";
require_once "../../class/Student.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Student class
$student = new Student($db);

// Get parameters from request
$date = isset($_GET['date']) ? $_GET['date'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';
$room = isset($_GET['room']) ? $_GET['room'] : '';

// Function to convert date from YYYY-MM-DD to YYYY-MM-DD in Thai Buddhist calendar
function convertDateToThaiFormat($date) {
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    if ($dateTime) {
        $dateTime->modify('+543 years');
        return $dateTime->format('Y-m-d');
    }
    return null;
}

$response = array('success' => false, 'data' => array());

if (!empty($date) && !empty($class) && !empty($room)) {
    $convertedDate = convertDateToThaiFormat($date);
    if ($convertedDate) {
        $query = "SELECT s.Stu_id, s.Stu_no, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, st.Study_status
                  FROM student s
                  LEFT JOIN study st ON s.Stu_id = st.Stu_id AND st.Study_date = :date
                  WHERE s.Stu_major = :class AND s.Stu_room = :room AND s.Stu_status = 1
                  ORDER BY s.Stu_no ASC";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':date', $convertedDate);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':room', $room);
        $stmt->execute();

        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($students) {
            $response['success'] = true;
            $response['data'] = $students;
        }
    }
}

echo json_encode($response);
?>
