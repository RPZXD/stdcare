<?php
require_once "../../config/Database.php";
require_once "../../class/StudentVisit.php";

$db = (new Database("phichaia_student"))->getConnection();
$visitHome = new StudentVisit($db);

// Get POST data
$data = [
    'stuId' => $_POST['stuId'] ?? null,
    'term' => $_POST['term'] ?? null,
    'pee' => $_POST['pee'] ?? null,
    'vh1' => $_POST['vh1'] ?? null,
    'vh2' => $_POST['vh2'] ?? null,
    'vh3' => $_POST['vh3'] ?? null,
    'vh4' => $_POST['vh4'] ?? null,
    'vh5' => $_POST['vh5'] ?? null,
    'vh6' => $_POST['vh6'] ?? null,
    'vh7' => $_POST['vh7'] ?? null,
    'vh8' => $_POST['vh8'] ?? null,
    'vh9' => $_POST['vh9'] ?? null,
    'vh10' => $_POST['vh10'] ?? null,
    'vh11' => $_POST['vh11'] ?? null,
    'vh12' => $_POST['vh12'] ?? null,
    'vh13' => $_POST['vh13'] ?? null,
    'vh14' => $_POST['vh14'] ?? null,
    'vh15' => $_POST['vh15'] ?? null,
    'vh16' => $_POST['vh16'] ?? null,
    'vh17' => $_POST['vh17'] ?? null,
    'vh18' => $_POST['vh18'] ?? null,
    'vh20' => $_POST['vh20'] ?? null,
    'picture1' => $_FILES['image1']['name'] ?? null,
    'picture2' => $_FILES['image2']['name'] ?? null,
    'picture3' => $_FILES['image3']['name'] ?? null,
    'picture4' => $_FILES['image4']['name'] ?? null,
    'picture5' => $_FILES['image5']['name'] ?? null,
];

// Validate required fields
if (!$data['stuId'] || !$data['term'] || !$data['pee']) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// Handle file uploads
$uploadDir = "../uploads/visithome" . ($data['pee'] - 543) . "/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

foreach (['image1', 'image2', 'image3', 'image4', 'image5'] as $key) {
    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
        $filePath = $uploadDir . basename($_FILES[$key]['name']);
        move_uploaded_file($_FILES[$key]['tmp_name'], $filePath);
    }
}

// Save data using the StudentVisit class
$result = $visitHome->saveVisitData($data);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว']);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
}
?>
