<?php
/**
 * Admin Layout
 * MVC Pattern - Main layout template for admin pages
 * Premium Design with Tailwind CSS & Responsive Components
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check login
if (!isset($_SESSION['Admin_login'])) {
    header('Location: ../login.php');
    exit;
}

$config = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
$global = $config['global'] ?? ['nameschool' => 'โรงเรียน'];

// Ensure admin data is in session
if (!isset($_SESSION['admin_data']) || empty($_SESSION['admin_data']['Teach_name'])) {
    require_once __DIR__ . '/../../config/Database.php';
    require_once __DIR__ . '/../../class/UserLogin.php';
    $connectDB = new \Database("phichaia_student");
    $db = $connectDB->getConnection();
    $userLogin = new \UserLogin($db);
    $_SESSION['admin_data'] = $userLogin->userData($_SESSION['Admin_login']);
}
$userData = $_SESSION['admin_data'];

// Set variables for base_app
$role = 'admin';
$themeColor = 'rose'; // Admin uses rose theme

include __DIR__ . '/base_app.php';
?>

