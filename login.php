<?php
/**
 * StdCare System - Login Router
 * MVC Structure
 */

ob_start();
date_default_timezone_set('Asia/Bangkok');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load dependencies
require_once __DIR__ . '/controllers/LoginController.php';
require_once __DIR__ . '/controllers/DatabaseLogger.php';
require_once __DIR__ . '/classes/DatabaseUsers.php';

use App\DatabaseUsers;

// Redirect if already logged in
function redirectUser() {
    $roles = [
        'Teacher_login' => 'teacher/index.php',
        'Director_login' => 'director/index.php',
        'Group_leader_login' => 'groupleader/index.php',
        'Officer_login' => 'officer/index.php',
        'Admin_login' => 'admin/index.php',
        'Student_login' => 'student/index.php'
    ];

    foreach ($roles as $sessionKey => $redirectPath) {
        if (isset($_SESSION[$sessionKey])) {
            header("Location: $redirectPath");
            exit();
        }
    }
}

redirectUser();

// Initialize database and controllers
$database = new DatabaseUsers();
$db = $database->getPDO();
$logger = new DatabaseLogger($db);
$loginController = new LoginController($logger);

// Process login form
$error = '';
$success = false;
$redirect = 'index.php';
$successMessage = '';

if (isset($_POST['signin'])) {
    $username = filter_input(INPUT_POST, 'txt_username_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'txt_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $remember = isset($_POST['remember_me']);
    
    $allowed_roles = ['Admin', 'Teacher', 'Officer', 'Director', 'Parent', 'Student'];
    $role = filter_input(INPUT_POST, 'txt_role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if (!in_array($role, $allowed_roles)) {
        $role = 'Teacher';
    }

    $result = $loginController->login($username, $password, $role);
    
    if ($result['success']) {
        // Handle remember me
        if ($remember) {
            setcookie('stdcare_username', $username, time() + (86400 * 30), "/");
            setcookie('stdcare_role', $role, time() + (86400 * 30), "/");
        } else {
            setcookie('stdcare_username', '', time() - 3600, "/");
            setcookie('stdcare_role', '', time() - 3600, "/");
        }
        
        $success = true;
        $redirect = $result['redirect'];
        $successMessage = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// Prepare data for view
$title = 'เข้าสู่ระบบ';

// Include view
include __DIR__ . '/views/auth/login.php';

ob_end_flush();