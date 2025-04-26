<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['Admin_login'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้รับอนุญาต']);
    exit;
}

if (!isset($_POST['academic_year']) || !isset($_POST['term'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$academic_year = trim($_POST['academic_year']);
$term = trim($_POST['term']);

if (!is_numeric($academic_year) || !in_array($term, ['1', '2'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit;
}

require_once("../../config/Database.php");
require_once("../../config/Setting.php");

$db = (new Database("phichaia_student"))->getConnection();

try {
    $stmt = $db->prepare("UPDATE termpee SET pee = ?, term = ? WHERE id = 1");
    $stmt->execute([$academic_year, $term]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
}
