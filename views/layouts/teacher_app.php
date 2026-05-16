<?php
/**
 * Teacher Layout
 * MVC Pattern - Main layout template for teacher pages
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check login - allow if Teacher_login exists
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

// Ensure teacher_data is in session (fallback if not already set)
if (!isset($_SESSION['teacher_data']) || empty($_SESSION['teacher_data']['Teach_name'])) {
    require_once __DIR__ . '/../../config/Database.php';
    require_once __DIR__ . '/../../class/UserLogin.php';
    $connectDB = new \Database("phichaia_student");
    $db = $connectDB->getConnection();
    $userLogin = new \UserLogin($db);
    $_SESSION['teacher_data'] = $userLogin->userData($_SESSION['Teacher_login']);
}
$userData = $_SESSION['teacher_data'];

// Set variables for base_app
$role = 'teacher';
$themeColor = 'blue'; // Teacher uses blue theme

include __DIR__ . '/base_app.php';
?>
