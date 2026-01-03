<?php
/**
 * StdCare System - Statistics Router
 * MVC Structure
 */

// Load Database
require_once __DIR__ . '/classes/DatabaseUsers.php';

// Load Controller
require_once __DIR__ . '/controllers/StatisticsController.php';

use App\Controllers\StatisticsController;
use App\DatabaseUsers;

// Database Connection
$database = new DatabaseUsers();
$db = $database->getPDO();

// Initialize Controller
$controller = new StatisticsController($db);

// Route to controller
$controller->index();
