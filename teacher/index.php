<?php
/**
 * StdCare System - Teacher Dashboard Router
 * MVC Structure
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Bangkok');

// Load Database
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';

// Load Controller
require_once __DIR__ . '/../controllers/TeacherDashboardController.php';

use App\Controllers\TeacherDashboardController;

// Database Connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Controller
$controller = new TeacherDashboardController($db);

// Route to controller
$controller->index();
