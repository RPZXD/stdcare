<?php
/**
 * Teacher GPS Visit Home Page - MVC Entry Point
 * Displays a map with all student locations for the teacher's class
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Fetch student GPS data for the class
$sql = "SELECT s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_no, s.Stu_addr,
               s.Stu_nick, s.Stu_phone, s.Par_phone, s.Stu_picture,
               g.latitude, g.longitude, g.updated_at
        FROM student s
        JOIN student_gps g ON s.Stu_id = g.Stu_id
        WHERE s.Stu_major = ? AND s.Stu_room = ?
        ORDER BY CAST(s.Stu_no AS UNSIGNED)";
$stmt = $db->prepare($sql);
$stmt->execute([$class, $room]);
$studentGpsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to extract village name from address
function getVillageGroup($addr) {
    if (empty($addr)) {
        return "ไม่ระบุหมู่บ้าน/ที่อยู่";
    }
    // Add space before common prefixes if missing (e.g. ต.ในเมืองอ.พิชัย -> ต.ในเมือง อ.พิชัย)
    $addrClean = preg_replace('/([ก-๙]+)(อ\.|จ\.|อำเภอ|จังหวัด)/u', '$1 $2', $addr);
    $addrClean = preg_replace('/\s+/', ' ', $addrClean);
    
    // Find moo (หมู่ที่ / หมู่ / ม. / ม)
    $moo = '';
    if (preg_match('/(?:หมู่ที่|หมู่|ม\s*\.\s*|ม\s+)\s*(\d+)/u', $addrClean, $matches)) {
        $moo = $matches[1];
    }
    
    // Find district (ตำบล / ต. / ต)
    $subdistrict = '';
    if (preg_match('/(?:ตำบล|ต\s*\.\s*|ต\s+)\s*([\x{0e00}-\x{0e7f}]+)/u', $addrClean, $matches)) {
        $subdistrict = trim($matches[1]);
    }
    
    if (!$moo && !$subdistrict) {
        return "ไม่ระบุหมู่บ้าน/ที่อยู่";
    }
    
    $result = '';
    if ($moo) {
        $result .= "หมู่ " . $moo;
    }
    if ($subdistrict) {
        $result .= ($result ? " " : "") . "ต." . $subdistrict;
    }
    return $result;
}

// Helper function to extract subdistrict name from address
function getSubdistrictGroup($addr) {
    if (empty($addr)) {
        return "ไม่ระบุตำบล";
    }
    // Add space before common prefixes if missing (e.g. ต.ในเมืองอ.พิชัย -> ต.ในเมือง อ.พิชัย)
    $addrClean = preg_replace('/([ก-๙]+)(อ\.|จ\.|อำเภอ|จังหวัด)/u', '$1 $2', $addr);
    $addrClean = preg_replace('/\s+/', ' ', $addrClean);
    
    // Find district (ตำบล / ต. / ต)
    if (preg_match('/(?:ตำบล|ต\s*\.\s*|ต\s+)\s*([\x{0e00}-\x{0e7f}]+)/u', $addrClean, $matches)) {
        return "ต." . trim($matches[1]);
    }
    return "ไม่ระบุตำบล";
}

// Add village and subdistrict keys to each student
foreach ($studentGpsList as &$std) {
    $std['village'] = getVillageGroup($std['Stu_addr']);
    $std['subdistrict'] = getSubdistrictGroup($std['Stu_addr']);
}
unset($std);


// Main page configuration
$pageTitle = "แผนที่บ้านนักเรียน";
$activePage = "visithome";

// Include view
include __DIR__ . '/../views/teacher/gps_visithome.php';
