<?php
/**
 * Student Data Controller
 * MVC Pattern - Handles teacher student data management logic
 */

namespace App\Controllers;

require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Teacher.php';
require_once __DIR__ . '/../class/Utils.php';

class StudentDataController
{
    private $db;
    private $user;
    private $teacher;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new \UserLogin($db);
        $this->teacher = new \Teacher($db);
    }

    public function index()
    {
        // Check login
        if (!isset($_SESSION['Teacher_login'])) {
            header('Location: ../login.php');
            exit;
        }

        $userid = $_SESSION['Teacher_login'];
        $userData = $this->user->userData($userid);
        
        // Save to session for navbar/sidebar
        $_SESSION['teacher_data'] = $userData;
        
        $term = $this->user->getTerm();
        $pee = $this->user->getPee();
        
        $class = $userData['Teach_class'];
        $room = $userData['Teach_room'];
        $teacher_id = $userData['Teach_id'];
        $teacher_name = $userData['Teach_name'];
        
        // Prepare data for view
        $data = [
            'pageTitle' => 'ข้อมูลนักเรียน',
            'userData' => $userData,
            'class' => $class,
            'room' => $room,
            'term' => $term,
            'pee' => $pee,
            'teacher_id' => $teacher_id,
            'teacher_name' => $teacher_name
        ];
        
        extract($data);
        
        include __DIR__ . '/../views/teacher/data_student.php';
    }
}
