<?php
require_once "../../config/Database.php";
require_once "../../class/StudentVisit.php";

$db = (new Database("phichaia_student"))->getConnection();
$visitHome = new StudentVisit($db);

$term = $_GET['term'];
$pee = $_GET['pee'];
$stuId = $_GET['stuId'];

$data = $visitHome->getVisitData($stuId, $term, $pee);

if ($data) {
    include 'edit_visit_form.php'; // Include the form template
} else {
    echo '<p class="text-danger">ไม่พบข้อมูล</p>';
}
?>