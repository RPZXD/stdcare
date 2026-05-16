<?php
require_once "c:/xampp/htdocs/stdcare/config/Database.php";
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$q = $db->query("DESCRIBE teacher");
while($r = $q->fetch(PDO::FETCH_ASSOC)) {
    echo $r['Field'] . " - " . $r['Type'] . "\n";
}
?>
