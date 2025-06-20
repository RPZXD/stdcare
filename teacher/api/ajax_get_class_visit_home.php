<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once "../../config/Database.php";
require_once "../../class/UserLogin.php";
require_once "../../class/Utils.php";

// Check authentication
if (!isset($_SESSION['Teacher_login'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);

// Get parameters
$class = isset($_GET['class']) ? intval($_GET['class']) : 0;
$room = isset($_GET['room']) ? intval($_GET['room']) : 0;
$term = $user->getTerm();
$pee = $user->getPee();

if ($class === 0 || $room === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Fetch students in the class
    $sql = "SELECT s.Stu_id, s.Stu_no, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_phone, s.Par_phone
            FROM student s 
            WHERE s.Stu_major = :class AND s.Stu_room = :room 
            AND s.Stu_status = '1'
            ORDER BY s.Stu_no ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':class', $class, PDO::PARAM_INT);
    $stmt->bindParam(':room', $room, PDO::PARAM_INT);
    $stmt->execute();
    
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize counters
    $summary = [
        'total' => count($students),
        'visited' => 0,
        'pending' => 0,
        'overdue' => 0,
        'round1_completed' => 0,
        'round2_completed' => 0
    ];
    
    // Process each student's visit data
    $studentsWithVisits = [];
    
    foreach ($students as $student) {
        // Get visit home records for Round 1 (Term = 1)
        $round1Sql = "SELECT vh.visit_id, vh.Stu_id, 
                            vh.vh1, vh.vh2, vh.vh3, vh.vh4, vh.vh5, vh.vh6, 
                            vh.vh7, vh.vh8, vh.vh9, vh.vh10, vh.vh11, vh.vh12,
                            vh.vh13, vh.vh14, vh.vh15, vh.vh16, vh.vh17, vh.vh18,
                            vh.picture1, vh.picture2, vh.picture3, vh.picture4, vh.picture5,
                            vh.vh20, vh.Term, vh.Pee
                     FROM visithome vh 
                     WHERE vh.Stu_id = :stu_id 
                     AND vh.Term = '1'
                     AND vh.Pee = :pee";
        
        $round1Stmt = $db->prepare($round1Sql);
        $round1Stmt->bindParam(':stu_id', $student['Stu_id']);
        $round1Stmt->bindParam(':pee', $pee);
        $round1Stmt->execute();
        
        $round1Data = $round1Stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get visit home records for Round 2 (Term = 2)
        $round2Sql = "SELECT vh.visit_id, vh.Stu_id, 
                            vh.vh1, vh.vh2, vh.vh3, vh.vh4, vh.vh5, vh.vh6, 
                            vh.vh7, vh.vh8, vh.vh9, vh.vh10, vh.vh11, vh.vh12,
                            vh.vh13, vh.vh14, vh.vh15, vh.vh16, vh.vh17, vh.vh18,
                            vh.picture1, vh.picture2, vh.picture3, vh.picture4, vh.picture5,
                            vh.vh20, vh.Term, vh.Pee
                     FROM visithome vh 
                     WHERE vh.Stu_id = :stu_id 
                     AND vh.Term = '2'
                     AND vh.Pee = :pee";
        
        $round2Stmt = $db->prepare($round2Sql);
        $round2Stmt->bindParam(':stu_id', $student['Stu_id']);
        $round2Stmt->bindParam(':pee', $pee);
        $round2Stmt->execute();
        
        $round2Data = $round2Stmt->fetch(PDO::FETCH_ASSOC);
        
        // Process visit data to determine rounds completion
        $round1_visit = null;
        $round2_visit = null;
        
        // Check Round 1 completion - เช็คจาก picture3 เพราะครูเป็นคนเพิ่มรูปภาพเมื่อเยี่ยมเสร็จ
        if ($round1Data && !empty($round1Data['picture3'])) {
            // Count filled fields for completion rate
            $all_fields = ['vh1', 'vh2', 'vh3', 'vh4', 'vh5', 'vh6', 'vh7', 'vh8', 'vh9', 
                          'vh10', 'vh11', 'vh12', 'vh13', 'vh14', 'vh15', 'vh16', 'vh17', 'vh18'];
            $round1_completed = 0;
            foreach ($all_fields as $field) {
                if (!empty($round1Data[$field])) {
                    $round1_completed++;
                }
            }
            
            $round1_visit = [
                'visit_date' => date('Y-m-d'), // Use current date as placeholder
                'completed_fields' => $round1_completed,
                'total_fields' => count($all_fields),
                'completion_rate' => round(($round1_completed / count($all_fields)) * 100, 2),
                'term' => '1',
                'visit_data' => $round1Data,
                'picture3' => $round1Data['picture3'] // เก็บรูปภาพที่ยืนยันการเยี่ยม
            ];
            $summary['round1_completed']++;
        }
        
        // Check Round 2 completion - เช็คจาก picture3 เพราะครูเป็นคนเพิ่มรูปภาพเมื่อเยี่ยมเสร็จ
        if ($round2Data && !empty($round2Data['picture3'])) {
            // Count filled fields for completion rate
            $all_fields = ['vh1', 'vh2', 'vh3', 'vh4', 'vh5', 'vh6', 'vh7', 'vh8', 'vh9', 
                          'vh10', 'vh11', 'vh12', 'vh13', 'vh14', 'vh15', 'vh16', 'vh17', 'vh18'];
            $round2_completed = 0;
            foreach ($all_fields as $field) {
                if (!empty($round2Data[$field])) {
                    $round2_completed++;
                }
            }
            
            $round2_visit = [
                'visit_date' => date('Y-m-d'), // Use current date as placeholder
                'completed_fields' => $round2_completed,
                'total_fields' => count($all_fields),
                'completion_rate' => round(($round2_completed / count($all_fields)) * 100, 2),
                'term' => '2',
                'visit_data' => $round2Data,
                'picture3' => $round2Data['picture3'] // เก็บรูปภาพที่ยืนยันการเยี่ยม
            ];
            $summary['round2_completed']++;
        }
        
        // Determine overall status
        $hasRound1 = $round1_visit !== null;
        $hasRound2 = $round2_visit !== null;
        
        if ($hasRound1) {
            $summary['visited']++;
            $status = 'completed';
        } elseif ($hasRound1) {
            $summary['pending']++;
            $status = 'partial';
        } else {
            // Check if overdue (assuming visits should be completed within academic year)
            $currentDate = new DateTime();
            $academicStartDate = new DateTime($pee-543 . '-06-31'); // Assume academic year starts May 16
            $monthsPassed = $currentDate->diff($academicStartDate)->m + ($currentDate->diff($academicStartDate)->y * 12);
            
            if ($monthsPassed > 6) { // Consider overdue after 6 months
                $summary['overdue']++;
                $status = 'overdue';
            } else {
                $summary['pending']++;
                $status = 'pending';
            }
        }
        
        // Add student with visit data
        $studentsWithVisits[] = [
            'Stu_id' => $student['Stu_id'],
            'Stu_no' => $student['Stu_no'],
            'Stu_pre' => $student['Stu_pre'],
            'Stu_name' => $student['Stu_name'],
            'Stu_sur' => $student['Stu_sur'],
            'Stu_phone' => $student['Stu_phone'],
            'Par_phone' => $student['Par_phone'],
            'round1_visit' => $round1_visit,
            'round2_visit' => $round2_visit,
            'status' => $status,
            'round1_data' => $round1Data, // Include round 1 data
            'round2_data' => $round2Data  // Include round 2 data
        ];
    }
    
    // Return response
    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'students' => $studentsWithVisits,
        'class_info' => [
            'class' => $class,
            'room' => $room,
            'term' => $term,
            'pee' => $pee
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
