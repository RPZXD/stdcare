<?php
/**
 * API: Get Parent Info by Student ID
 * Returns father, mother, and guardian information
 */
header('Content-Type: application/json');
require_once "../../config/Database.php";
require_once "../../class/Student.php";

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $student = new Student($db);
    
    $stuId = $_GET['stu_id'] ?? '';
    
    if (empty($stuId)) {
        echo json_encode(['success' => false, 'message' => 'กรุณาระบุรหัสนักเรียน']);
        exit;
    }
    
    $data = $student->getStudentDataByStuId($stuId);
    
    if ($data) {
        // Build parent options array
        $parents = [];
        
        // Father
        if (!empty($data['Father_name'])) {
            $parents[] = [
                'type' => 'father',
                'label' => '👨 บิดา',
                'name' => $data['Father_name'],
                'address' => $data['Stu_addr'] ?? '',
                'phone' => $data['Par_phone'] ?? ''
            ];
        }
        
        // Mother
        if (!empty($data['Mother_name'])) {
            $parents[] = [
                'type' => 'mother',
                'label' => '👩 มารดา',
                'name' => $data['Mother_name'],
                'address' => $data['Stu_addr'] ?? '',
                'phone' => $data['Par_phone'] ?? ''
            ];
        }
        
        // Guardian
        if (!empty($data['Par_name'])) {
            $parents[] = [
                'type' => 'guardian',
                'label' => '👤 ผู้ปกครอง (' . ($data['Par_relate'] ?? 'อื่นๆ') . ')',
                'name' => $data['Par_name'],
                'address' => $data['Par_addr'] ?? $data['Stu_addr'] ?? '',
                'phone' => $data['Par_phone'] ?? ''
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'student_name' => $data['Stu_pre'] . $data['Stu_name'] . ' ' . $data['Stu_sur'],
                'student_address' => $data['Stu_addr'] ?? '',
                'parents' => $parents
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียน']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
