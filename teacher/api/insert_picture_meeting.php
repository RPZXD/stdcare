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

    // Check if record exists
    $checkStmt = $db->prepare("
        SELECT COUNT(*) FROM tb_picmeeting
        WHERE Stu_major = :class AND Stu_room = :room AND term = :term AND pee = :pee
    ");
    $checkStmt->execute([
        ':class' => $class,
        ':room' => $room,
        ':term' => $term,
        ':pee' => $pee
    ]);
    $exists = $checkStmt->fetchColumn() > 0;

    if ($exists) {
        // Update existing record
        $stmt = $db->prepare("
            UPDATE tb_picmeeting SET
                picture1 = :picture1,
                picture2 = :picture2,
                picture3 = :picture3,
                picture4 = :picture4
            WHERE Stu_major = :class AND Stu_room = :room AND term = :term AND pee = :pee
        ");
    } else {
        // Insert new record
        $stmt = $db->prepare("
            INSERT INTO tb_picmeeting (
                Stu_major, Stu_room, term, pee,
                picture1, picture2, picture3, picture4
            ) VALUES (
                :class, :room, :term, :pee,
                :picture1, :picture2, :picture3, :picture4
            )
        ");
    }
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
