<?php
/**
 * StdCare System - Main Router
 * Refactored to MVC Structure
 */

// Load Configurations & Database
require_once __DIR__ . '/classes/DatabaseUsers.php';
require_once __DIR__ . '/class/Utils.php';
require_once __DIR__ . '/class/UserLogin.php';

// Load Models
require_once __DIR__ . '/models/Home.php';

// Load Controllers
require_once __DIR__ . '/controllers/HomeController.php';

use App\Controllers\HomeController;
use App\DatabaseUsers;

// Database Connection
$database = new DatabaseUsers(); 
$db = $database->getPDO();

// Initialize Controller
$controller = new HomeController($db);

// Simple Routing
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch($action) {
    case 'index':
    default:
        $controller->index();
        break;
}
