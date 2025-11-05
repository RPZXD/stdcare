<?php

header('Content-Type: application/json');
include_once("../../config/Database.php");



// สร้าง connection ด้วย Database class
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Check if class, room, and date parameters are set
if(isset($_GET['start_date']) && isset($_GET['end_date']) ) {
    // Assign values from GET parameters
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    try {
        // Prepare SQL statement
        $query = "SELECT 
                        s.Stu_id, 
                        s.Stu_no, 
                        s.Stu_pre, 
                        s.Stu_name, 
                        s.Stu_sur, 
                        s.Stu_major, 
                        s.Stu_room, 
                        COUNT(st.attendance_date) AS count_late, 
                        s.Par_phone
                    FROM 
                        student AS s
                    INNER JOIN 
                        student_attendance AS st ON s.Stu_id = st.student_id
                    WHERE 
                        (DATE(st.attendance_date) BETWEEN :startdate AND :enddate) 
                        AND st.attendance_status = 3 
                        AND s.Stu_status = 1
                    GROUP BY 
                        s.Stu_id, 
                        s.Stu_no, 
                        s.Stu_pre, 
                        s.Stu_name, 
                        s.Stu_sur, 
                        s.Stu_major, 
                        s.Stu_room, 
                        s.Par_phone
                    HAVING 
                        count_late >= 3
                    ORDER BY 
                        s.Stu_major, 
                        s.Stu_room, 
                        s.Stu_id ASC
                    LIMIT 0, 2000;
                    ";

        $statement = $db->prepare($query);

        // Bind parameters
        $statement->bindParam(':startdate', $start_date);
        $statement->bindParam(':enddate', $end_date);

        // Execute query
        $statement->execute();

        // Fetch data
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Check if data is fetched
        if($result) {
            // Modify fetched data
            foreach($result as &$row) {
                // Concatenate Stu_pre, Stu_name, and Stu_sur into full_name
                $row['name'] = ($row['Stu_pre'] ?? '') . ($row['Stu_name'] ?? '') . '&nbsp;&nbsp;' . ($row['Stu_sur'] ?? '');
                $row['classroom'] = 'ม.' . ($row['Stu_major'] ?? '')  . '/' . ($row['Stu_room'] ?? '');
                // Ensure optional fields exist to avoid notices
                $row['count_late'] = isset($row['count_late']) ? (int)$row['count_late'] : 0;
                $row['Study_date'] = isset($row['Study_date']) ? $row['Study_date'] : null;
                $row['parent_tel'] = isset($row['Par_phone']) ? $row['Par_phone'] : null;

                // Remove raw columns we no longer need
                unset($row['Stu_pre'], $row['Stu_name'], $row['Stu_sur'], $row['Stu_major'], $row['Stu_room'], $row['Par_phone']);
            }

            // Echo JSON response
            echo json_encode($result);
        } else {
            echo json_encode(array('message' => 'No records found'));
        }
    } catch(PDOException $e) {
        echo json_encode(array('error' => 'Failed to execute query: ' . $e->getMessage()));
    }
} else {
    // If class, room, or date parameters are not set
    echo json_encode(array('error' => 'Class, room, and date parameters are required'));
}
?>
