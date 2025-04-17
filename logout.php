<?php 

    include_once("config/Database.php");
    include_once("class/UserLogin.php");
    include_once("class/Logger.php"); // Include Logger class
    require_once("header.php");
    require_once("script.php");

    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $studentDb = new Database("phichaia_student");
    $studentConn = $studentDb->getConnection();

    $user = new UserLogin($studentConn);
    $logger = new Logger("logs/logout.json"); // Initialize logger

    if (isset($_SESSION['user'])) {
        $logger->log([
            "user_id" => $_SESSION['user'],
            "ip_address" => $_SERVER['REMOTE_ADDR'],
            "user_agent" => $_SERVER['HTTP_USER_AGENT'],
            "access_time" => date("c"),
            "url" => $_SERVER['REQUEST_URI'],
            "method" => $_SERVER['REQUEST_METHOD'],
            "status_code" => 200,
            "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
            "action_type" => "logout",
            "session_id" => session_id(),
            "message" => "Logout successful"
        ]);
        session_destroy(); // End session
    } else {
        $logger->log([
            "user_id" => null,
            "ip_address" => $_SERVER['REMOTE_ADDR'],
            "user_agent" => $_SERVER['HTTP_USER_AGENT'],
            "access_time" => date("c"),
            "url" => $_SERVER['REQUEST_URI'],
            "method" => $_SERVER['REQUEST_METHOD'],
            "status_code" => 400,
            "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
            "action_type" => "logout_attempt",
            "session_id" => session_id(),
            "message" => "Logout attempted without an active session"
        ]);
    }

    $user->logOut();
?>