<?php
require_once "../../config/Database.php";
require_once "../../class/Utils.php";

$response = ['success' => false, 'message' => ''];

try {
    // Initialize database connection
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    // Validate input
    $class = $_POST['class'] ?? null;
    $room = $_POST['room'] ?? null;
    $term = $_POST['term'] ?? null;
    $pee = $_POST['pee'] ?? null;

    if (!$class || !$room || !$term || !$pee) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
    }

    // Handle file uploads
    $uploadedFiles = $_FILES['uploadImage'] ?? null;
    if (!$uploadedFiles || !is_array($uploadedFiles['name'])) {
        throw new Exception('ไม่มีไฟล์ที่อัปโหลด');
    }

    $uploadDir = '../uploads/picmeeting' . $term . $pee . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Prepare file names for up to 4 images
    $fileNames = array_fill(0, 4, null);
    foreach ($uploadedFiles['name'] as $key => $name) {
        if ($key < 4 && $uploadedFiles['error'][$key] === UPLOAD_ERR_OK) {
            $tmpName = $uploadedFiles['tmp_name'][$key];
            $fileName = "{$class}{$room}{$term}{$pee}_pic" . ($key + 1) . '.' . pathinfo($name, PATHINFO_EXTENSION);
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $filePath)) {
                $fileNames[$key] = $fileName; // Store only the file name
            } else {
                throw new Exception('ไม่สามารถอัปโหลดไฟล์: ' . $name);
            }
        }
    }

    // Insert or update the database record
    $stmt = $db->prepare("
        INSERT INTO tb_picmeeting (
            Stu_major, Stu_room, term, pee,
            picture1, picture2, picture3, picture4
        ) VALUES (
            :class, :room, :term, :pee,
            :picture1, :picture2, :picture3, :picture4
        )
        ON DUPLICATE KEY UPDATE
            picture1 = VALUES(picture1),
            picture2 = VALUES(picture2),
            picture3 = VALUES(picture3),
            picture4 = VALUES(picture4)
    ");
    $stmt->execute([
        ':class' => $class,
        ':room' => $room,
        ':term' => $term,
        ':pee' => $pee,
        ':picture1' => $fileNames[0],
        ':picture2' => $fileNames[1],
        ':picture3' => $fileNames[2],
        ':picture4' => $fileNames[3]
    ]);

    $response['success'] = true;
    $response['message'] = 'อัปโหลดรูปภาพสำเร็จ';
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
