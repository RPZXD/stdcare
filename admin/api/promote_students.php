<?php
header('Content-Type: application/json');
include_once("../../config/Database.php");
include_once("../../class/Student.php");

try {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $student = new Student($db);

    // เลื่อนชั้นปี
    $result = $student->promoteAllStudents();

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => $result['message'] ?? 'เลื่อนชั้นปีสำเร็จ'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'เกิดข้อผิดพลาด'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
exit;

// --- ตัวอย่างใน class Student.php ---
// public function promoteAllStudents() {
//     try {
//         // เลื่อนชั้นปี (Stu_room + 1) สำหรับ Stu_room 1,2,4,5 และ Stu_status = 1 (ปกติ)
//         // ถ้า Stu_room = 3 หรือ 6 ให้เปลี่ยน Stu_status = 2 (จบ)
//         $this->db->beginTransaction();
//         $this->db->query("UPDATE student SET Stu_room = Stu_room + 1 WHERE Stu_room IN (1,2,4,5) AND Stu_status = 1");
//         $this->db->query("UPDATE student SET Stu_status = 2 WHERE Stu_room IN (3,6) AND Stu_status = 1");
//         $this->db->commit();
//         return ['success' => true, 'message' => 'เลื่อนชั้นปีสำเร็จ'];
//     } catch (Exception $e) {
//         $this->db->rollBack();
//         return ['success' => false, 'message' => $e->getMessage()];
//     }
// }
