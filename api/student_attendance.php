<?php
require_once '../config/Database.php';
require_once '../config/Setting.php';
require_once '../class/UserLogin.php';
require_once '../class/Student.php';
require_once '../class/Utils.php';

header('Content-Type: application/json');

try {
    // Initialize database connection
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    // Initialize UserLogin class
    $user = new UserLogin($db);

    // Fetch term and pee
    $term = $user->getTerm();
    $pee = $user->getPee();

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the raw POST data
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (!isset($input['Stu_id']) || empty($input['Stu_id'])) {
            echo json_encode(['error' => 'Student ID is required']);
            exit;
        }

        $stu_id = intval($input['Stu_id']); // Ensure it's an integer
        $study_date = date('Y-m-d');
        $study_status = '1';
        $study_term = $term;
        $study_pee = $pee;
        $device_id = $input['device_id'] ?? null;

        // Prepare the SQL query
        $query = "INSERT INTO student_attendance (Stu_id, Study_date, Study_status, Study_term, Study_pee, device)
                  VALUES (:stu_id, :study_date, :study_status, :study_term, :study_pee, :device)
                  ON DUPLICATE KEY UPDATE Study_status = :study_status";
        
        $statement = $db->prepare($query);
        $statement->bindParam(':stu_id', $stu_id, PDO::PARAM_INT);
        $statement->bindParam(':study_date', $study_date, PDO::PARAM_STR);
        $statement->bindParam(':study_status', $study_status, PDO::PARAM_STR);
        $statement->bindParam(':study_term', $study_term, PDO::PARAM_STR);
        $statement->bindParam(':study_pee', $study_pee, PDO::PARAM_STR);
        $statement->bindParam(':device', $device_id, PDO::PARAM_STR);

        // Execute the query
        if ($statement->execute()) {
            echo json_encode(['message' => 'Attendance recorded successfully']);
        } else {
            echo json_encode(['error' => 'Failed to record attendance']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request method']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
