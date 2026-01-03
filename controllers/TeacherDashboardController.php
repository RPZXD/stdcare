<?php
namespace App\Controllers;

require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Utils.php';

class TeacherDashboardController
{
    private $db;
    private $user;
    private $student;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new \UserLogin($db);
        $this->student = new \Student($db);
    }

    public function index()
    {
        // Check login
        if (!isset($_SESSION['Teacher_login'])) {
            echo '<script>Swal.fire({
                icon: "error",
                title: "คุณยังไม่ได้เข้าสู่ระบบ",
                confirmButtonText: "ตกลง"
            }).then(() => { window.location.href = "../login.php"; });</script>';
            return;
        }

        $userid = $_SESSION['Teacher_login'];
        $userData = $this->user->userData($userid);
        
        // Save to session for navbar and other components
        $_SESSION['teacher_data'] = $userData;
        
        $term = $this->user->getTerm();
        $pee = $this->user->getPee();
        
        $teacher_id = $userData['Teach_id'];
        $teacher_name = $userData['Teach_name'];
        $class = $userData['Teach_class'];
        $room = $userData['Teach_room'];
        
        $currentDate = date("Y-m-d");
        $currentDateDisplay = \Utils::convertToThaiDatePlus(date("Y-m-d"));
        
        // Get student counts
        $countStdCome = $this->student->getStatusCountClassRoom($class, $room, [1, 3, 6], $currentDate);
        $countStdAbsent = $this->student->getStatusCountClassRoom($class, $room, [2, 4, 5], $currentDate);
        $countAll = $this->student->getCountClassRoom($class, $room);
        
        // Prepare data for view
        $data = [
            'title' => 'หน้าหลักครูที่ปรึกษา',
            'userData' => $userData,
            'teacher_name' => $teacher_name,
            'class' => $class,
            'room' => $room,
            'term' => $term,
            'pee' => $pee,
            'currentDate' => $currentDate,
            'currentDateDisplay' => $currentDateDisplay,
            'countAll' => $countAll,
            'countStdCome' => $countStdCome,
            'countStdAbsent' => $countStdAbsent
        ];
        
        extract($data);
        
        include __DIR__ . '/../views/teacher/index.php';
    }
}
