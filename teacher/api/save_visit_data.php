<?php
require_once "../../config/Database.php";
require_once "../../class/StudentVisit.php";

$db = (new Database("phichaia_student"))->getConnection();
$visitHome = new StudentVisit($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        'picture1' => null,
        'picture2' => null,
        'picture3' => null,
        'picture4' => null,
        'picture5' => null,
    ];

    // Validate required fields
    for ($i = 1; $i <= 18; $i++) {
        if (empty($data["vh$i"])) {
            echo json_encode(['success' => false, 'message' => "หัวข้อที่ $i ไม่สามารถเว้นว่างได้"]);
            exit;
        }
    }

    for ($i = 1; $i <= 3; $i++) {
        $fileKey = "image$i";
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => "ไฟล์ที่ $i ไม่สาเว้นรถเว้นว่างได้"]);
            exit;
        }
    }

    // Handle file uploads
    $uploadDir = "../uploads/visithome/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
    }

    for ($i = 1; $i <= 5; $i++) {
        $fileKey = "image$i";
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $fileName = uniqid() . "_" . basename($_FILES[$fileKey]['name']);
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $filePath)) {
                $data["picture$i"] = $fileName;
            } else {
                echo json_encode(['success' => false, 'message' => "ไม่สามารถอัปโหลดไฟล์ $fileKey ได้"]);
                exit;
            }
        }
    }

    // Save visit data
    if ($visitHome->saveVisitData($data)) {
        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
