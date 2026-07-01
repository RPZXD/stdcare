<?php
require_once "config/Database.php";
require_once "class/UserLogin.php";

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);

$userData = $user->userData('152');
print_r($userData);
