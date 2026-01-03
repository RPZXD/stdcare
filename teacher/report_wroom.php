<?php 
/**
 * Report Wroom - MVC Controller
 * Displays committee members list
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";
require_once "../class/Wroom.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Check login
if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    header("Location: ../login.php");
    exit;
}

// Extract teacher information
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Get teachers for this room
$roomTeachers = $teacher->getTeachersByClassAndRoom($class, $room);

// Get wroom data
$wroomObj = new Wroom($db);
$wroom = $wroomObj->getWroomStudents($class, $room, $pee);
$maxim = $wroomObj->getMaxim($class, $room, $pee);

// Position config
$positions = [
    "advisors" => ["emoji" => "👨‍🏫", "label" => "ครูที่ปรึกษา", "limit" => null, "color" => "indigo"],
    "1" => ["emoji" => "👤", "label" => "หัวหน้าห้อง", "limit" => 1, "color" => "rose"],
    "2" => ["emoji" => "📘", "label" => "รองฯ ฝ่ายการเรียน", "limit" => 1, "color" => "blue"],
    "3" => ["emoji" => "🛠️", "label" => "รองฯ ฝ่ายการงาน", "limit" => 1, "color" => "orange"],
    "4" => ["emoji" => "🎉", "label" => "รองฯ ฝ่ายกิจกรรม", "limit" => 1, "color" => "purple"],
    "5" => ["emoji" => "🚨", "label" => "รองฯ ฝ่ายสารวัตร", "limit" => 1, "color" => "red"],
    "10" => ["emoji" => "📝", "label" => "เลขานุการ", "limit" => 1, "color" => "teal"],
    "11" => ["emoji" => "🗂️", "label" => "ผู้ช่วยเลขานุการ", "limit" => 1, "color" => "cyan"],
    "6" => ["emoji" => "📚", "label" => "แกนนำ ฝ่ายการเรียน", "limit" => 4, "color" => "sky"],
    "7" => ["emoji" => "🔧", "label" => "แกนนำ ฝ่ายการงาน", "limit" => 4, "color" => "amber"],
    "8" => ["emoji" => "🎭", "label" => "แกนนำ ฝ่ายกิจกรรม", "limit" => 4, "color" => "violet"],
    "9" => ["emoji" => "🛡️", "label" => "แกนนำ ฝ่ายสารวัตร", "limit" => 4, "color" => "pink"],
];

// Group students by position
$grouped = [];
foreach ($wroom as $stu) {
    $pos = $stu['wposit'];
    if (!isset($grouped[$pos])) $grouped[$pos] = [];
    $grouped[$pos][] = $stu;
}

// Set page title
$title = 'รายชื่อคณะกรรมการห้องเรียนสีขาว';

// Load the view
include __DIR__ . '/../views/teacher/report_wroom.php';
?>
