<?php
/**
 * Director Layout
 * MVC Pattern - Main layout template for director pages
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check login
if (!isset($_SESSION['Director_login'])) {
    header('Location: ../login.php');
    exit;
}

// Ensure director data is in session
if (!isset($_SESSION['director_data']) || empty($_SESSION['director_data']['Teach_name'])) {
    require_once __DIR__ . '/../../config/Database.php';
    require_once __DIR__ . '/../../class/UserLogin.php';
    $connectDB = new \Database("phichaia_student");
    $db = $connectDB->getConnection();
    $userLogin = new \UserLogin($db);
    $_SESSION['director_data'] = $userLogin->userData($_SESSION['Director_login']);
}
$userData = $_SESSION['director_data'];

// Set variables for base_app
$role = 'director';
$themeColor = 'indigo'; // Director uses indigo theme

include __DIR__ . '/base_app.php';
?>
