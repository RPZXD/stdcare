<?php 

    include_once("config/Database.php");
    include_once("class/UserLogin.php");
    require_once("header.php");

    $connectDB = new Database_User();
    $db = $connectDB->getConnection();

    $user = new UserLogin($db);
    $user->logOut();



?>