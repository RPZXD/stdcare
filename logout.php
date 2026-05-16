<?php
/**
 * StdCare System - Logout Controller Entry
 * MVC Pattern Refactored
 */
date_default_timezone_set('Asia/Bangkok');

require_once(__DIR__ . "/controllers/LoginController.php");
require_once(__DIR__ . "/controllers/DatabaseLogger.php");
require_once(__DIR__ . "/classes/DatabaseUsers.php");

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
$database = new App\DatabaseUsers();
$db = $database->getPDO();

// Initialize Logger and Controller
$logger = new DatabaseLogger($db);
$loginController = new LoginController($logger);

// Handle Logout Logic
$logoutSuccess = false;
if (isset($_SESSION['user']) || isset($_SESSION['Admin_login']) || isset($_SESSION['Teacher_login']) || isset($_SESSION['Student_login']) || isset($_SESSION['Officer_login'])) {
    $result = $loginController->logout();
    
    // Fallback: Manually clear role-specific sessions if not handled by controller
    unset($_SESSION['Admin_login']);
    unset($_SESSION['Teacher_login']);
    unset($_SESSION['Student_login']);
    unset($_SESSION['Officer_login']);
    unset($_SESSION['admin_data']);
    unset($_SESSION['teacher_data']);
    unset($_SESSION['student_data']);
    unset($_SESSION['officer_data']);
    
    $logoutSuccess = true; // Assume success if we manually cleared or controller succeeded
} else {
    // Log attempt without session
    $logger->log([
        "user_id" => null,
        "role" => null,
        "ip_address" => $_SERVER['REMOTE_ADDR'],
        "user_agent" => $_SERVER['HTTP_USER_AGENT'],
        "access_time" => date("c"),
        "url" => $_SERVER['REQUEST_URI'],
        "method" => $_SERVER['REQUEST_METHOD'],
        "status_code" => 200, // Still 200 because we show the logout page anyway
        "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
        "action_type" => "logout_attempt",
        "session_id" => session_id(),
        "message" => "Logout attempted without an active session"
    ]);
    $logoutSuccess = true; // Still show success UI to the user
}

// Load the view
include(__DIR__ . "/views/auth/logout.php");