<?php
/**
 * API: Update Parent Info for Student
 */
header('Content-Type: application/json');
require_once "../../config/Database.php";
require_once "../../class/Parent.php";

try {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    $stuId = $_POST['stu_id'] ?? '';
    $parName = $_POST['par_name'] ?? '';
    $parPhone = $_POST['par_phone'] ?? '';
    $parRelate = $_POST['par_relation'] ?? $_POST['par_relate'] ?? '';

    if (empty($stuId)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสนักเรียน']);
        exit;
    }

    // Fetch existing parent data first so we preserve other fields
    $parent = new StudentParent($db);
    $existing = $parent->getParentById($stuId);
    
    if ($existing && count($existing) > 0) {
        $data = $existing[0];
        $parent->Stu_id = $stuId;
        $parent->Father_name = $data['Father_name'] ?? '';
        $parent->Father_occu = $data['Father_occu'] ?? '';
        $parent->Father_income = $data['Father_income'] ?? '';
        $parent->Mother_name = $data['Mother_name'] ?? '';
        $parent->Mother_occu = $data['Mother_occu'] ?? '';
        $parent->Mother_income = $data['Mother_income'] ?? '';
        $parent->Par_name = $parName;
        $parent->Par_relate = $parRelate;
        $parent->Par_occu = $data['Par_occu'] ?? '';
        $parent->Par_income = $data['Par_income'] ?? '';
        $parent->Par_addr = $data['Par_addr'] ?? '';
        $parent->Par_phone = $parPhone;

        if ($parent->updateParentInfo()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียน']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
