<?php
require_once "../../config/Database.php";
require_once "../../class/BoardParent.php";
require_once __DIR__ . "/helpers/upload_helper.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $BoardParent = new BoardParent($db);

    $stu_id = $_POST['edit_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $address = $_POST['address'] ?? null;
    $tel = $_POST['tel'] ?? null;
    $pos = $_POST['pos'] ?? null;
    $pee = $_POST['pee'] ?? null;

    if (!$stu_id || !$name || !$address || !$tel || !$pos) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        exit;
    }

    $photo = null;
    // Check file upload with proper error handling
    if (isset($_FILES['image1'])) {
        $error = $_FILES['image1']['error'];
        if ($error !== UPLOAD_ERR_NO_FILE) {
            if ($error !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => getUploadErrorMessage($error, 'รูปภาพ')]);
                exit;
            }
            if ($_FILES['image1']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'รูปภาพมีขนาดใหญ่เกินไป (สูงสุด 5MB)']);
                exit;
            }
            
            $photo = basename($_FILES['image1']['name']);
            $uploadDir = "../uploads/photopar/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (!move_uploaded_file($_FILES['image1']['tmp_name'], $uploadDir . $photo)) {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกรูปภาพได้ กรุณาลองใหม่']);
                exit;
            }
        }
    }
    
    // Fetch existing photo if no new photo is uploaded
    if (!$photo) {
        $query = "SELECT parn_photo FROM tb_parnet WHERE Stu_id = :stu_id AND parn_pee = :pee";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':stu_id', $stu_id, PDO::PARAM_INT);
        $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $photo = $result['parn_photo'] ?? null;
    }

    try {
        $query = "
            UPDATE tb_parnet
            SET parn_name = :name,
                parn_addr = :address,
                parn_tel = :tel,
                parn_pos = :pos
                " . ($photo ? ", parn_photo = :photo" : "") . "
            WHERE Stu_id = :stu_id AND parn_pee = :pee
        ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
        $stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
        if ($photo) {
            $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
        }
        $stmt->bindParam(':stu_id', $stu_id, PDO::PARAM_INT);
        $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'แก้ไขข้อมูลสำเร็จ']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถแก้ไขข้อมูลได้: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
}
?>
