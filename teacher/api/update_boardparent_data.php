<?php
require_once "../../config/Database.php";
require_once "../../class/BoardParent.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $BoardParent = new BoardParent($db);

    $stu_id = $_POST['edit_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $tel = $_POST['tel'];
    $pos = $_POST['pos'];
    $pee = $_POST['pee'];

    $photo = null;
    if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
        $photo = basename($_FILES['image1']['name']);
        $uploadDir = "../uploads/photopar/";
        move_uploaded_file($_FILES['image1']['tmp_name'], $uploadDir . $photo);
    } else {
        // Fetch the existing photo if no new photo is uploaded
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
