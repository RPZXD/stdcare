<?php
/**
 * StdCare System - Announce Router
 * MVC Structure
 */

// Load Controller
require_once __DIR__ . '/controllers/AnnounceController.php';

use App\Controllers\AnnounceController;

// Initialize Controller
$controller = new AnnounceController();

// Route to controller
$controller->index();
