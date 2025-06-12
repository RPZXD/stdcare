<?php
session_start();

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

// Get teacher data
$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);
$teacher_name = $userData['Teach_name'];

// Get parameters
$class = isset($_GET['class']) ? intval($_GET['class']) : 0;
$room = isset($_GET['room']) ? intval($_GET['room']) : 0;
$format = isset($_GET['format']) ? $_GET['format'] : 'table';

if ($class === 0 || $room === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Fetch student data
    $sql = "SELECT Stu_id, Stu_no, Stu_pre, Stu_name, Stu_sur, Stu_nick, 
                   Stu_phone, Par_phone, Stu_picture, Stu_addr
            FROM student 
            WHERE Stu_major = :class AND Stu_room = :room 
            AND Stu_status = '1'
            ORDER BY Stu_no ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':class', $class, PDO::PARAM_INT);
    $stmt->bindParam(':room', $room, PDO::PARAM_INT);
    $stmt->execute();
    
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get school year
    $pee = $user->getPee();
    $currentDate = Utils::convertToThaiDatePlus(date("Y-m-d"));
    
    // Generate HTML for printing
    $html = generatePrintHTML($students, $class, $room, $pee, $currentDate, $teacher_name);
    
    // Return HTML directly
    echo $html;
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

function generatePrintHTML($students, $class, $room, $pee, $currentDate, $teacher_name) {
    $totalStudents = count($students);
    
    $html = '<div class="print-header">';
    $html .= '<h1>โรงเรียนพิชัย</h1>';
    $html .= '<h2>รายชื่อนักเรียน ระดับชั้นมัธยมศึกษาปีที่ ' . $class . ' ห้อง ' . $room . '</h2>';
    $html .= '<p>ปีการศึกษา ' . $pee . ' | จำนวนนักเรียน: ' . $totalStudents . ' คน</p>';
    $html .= '</div>';
    
    if (empty($students)) {
        $html .= '<div style="text-align: center; margin-top: 50px;">';
        $html .= '<h3>ไม่พบข้อมูลนักเรียน</h3>';
        $html .= '</div>';
        return $html;
    }
    
    $html .= '<table class="print-table">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th class="col-no">ที่</th>';
    $html .= '<th class="col-id">รหัสนักเรียน</th>';
    $html .= '<th class="col-name">ชื่อ - นามสกุล</th>';
    $html .= '<th class="col-nick">ชื่อเล่น</th>';
    $html .= '<th class="col-phone">เบอร์โทร</th>';
    $html .= '<th class="col-parent">เบอร์ผู้ปกครอง</th>';
    $html .= '<th class="col-note">หมายเหตุ</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    foreach ($students as $index => $student) {
        $rowNum = $index + 1;
        $fullName = $student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur'];
        $nickname = $student['Stu_nick'] ?: '-';
        $studentPhone = $student['Stu_phone'] ?: '-';
        $parentPhone = $student['Par_phone'] ?: '-';
        
        $html .= '<tr>';
        $html .= '<td class="col-no">' . $rowNum . '</td>';
        $html .= '<td class="col-id">' . htmlspecialchars($student['Stu_id']) . '</td>';
        $html .= '<td class="col-name">' . htmlspecialchars($fullName) . '</td>';
        $html .= '<td class="col-nick">' . htmlspecialchars($nickname) . '</td>';
        $html .= '<td class="col-phone">' . htmlspecialchars($studentPhone) . '</td>';
        $html .= '<td class="col-parent">' . htmlspecialchars($parentPhone) . '</td>';
        $html .= '<td class="col-note"></td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    

    
    return $html;
}
?>
