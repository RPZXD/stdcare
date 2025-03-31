<?php 

    include_once("config/Database.php");
    include_once("class/UserLogin.php");
    require_once("header.php");
    require_once("script.php");

    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $studentDb = new Database("phichaia_student");
    $studentConn = $studentDb->getConnection();

    $user = new UserLogin($studentConn);
    $user->logOut();
?>