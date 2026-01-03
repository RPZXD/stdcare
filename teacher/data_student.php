<?php
/**
 * StdCare System - Student Data Router
 * MVC Structure
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Bangkok');

// Load Database
require_once __DIR__ . '/../config/Database.php';

// Load Controller
require_once __DIR__ . '/../controllers/StudentDataController.php';

use App\Controllers\StudentDataController;

// Database Connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Controller
$controller = new StudentDataController($db);

// Route to controller
$controller->index();
