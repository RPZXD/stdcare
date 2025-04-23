<?php
header('Content-Type: application/json');
require_once "../../config/Database.php";

// สร้างการเชื่อมต่อฐานข้อมูล
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// รับค่าพารามิเตอร์จากคำขอ (เช่น class, room, term, pee)
$class = isset($_GET['class']) ? $_GET['class'] : null;
$room = isset($_GET['room']) ? $_GET['room'] : null;
$term = isset($_GET['term']) ? $_GET['term'] : null;
$pee = isset($_GET['pee']) ? $_GET['pee'] : null;

try {
    // Query ดึงข้อมูลรูปภาพตามเงื่อนไข
    $query = "SELECT picture1, picture2, picture3, picture4 
              FROM tb_picmeeting 
              WHERE Stu_major = :class AND Stu_room = :room AND term = :term AND pee = :pee 
              LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':class', $class);
    $stmt->bindParam(':room', $room);
    $stmt->bindParam(':term', $term);
    $stmt->bindParam(':pee', $pee);
    $stmt->execute();

    $pictures = [];
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // เพิ่มรูปภาพที่ไม่ใช่ NULL ลงในอาร์เรย์
        foreach (['picture1', 'picture2', 'picture3', 'picture4'] as $pictureColumn) {
            if (!empty($row[$pictureColumn])) {
                $pictures[] = [
                    'url' => 'https://std.phichai.ac.th/teacher/uploads/picmeeting'.$pee.'/'.$row[$pictureColumn],
                    'alt' => "$pictureColumn"
                ];
            }
        }
    }

    // ส่งข้อมูลกลับในรูปแบบ JSON
    echo json_encode([
        'success' => true,
        'data' => $pictures
    ]);
} catch (Exception $e) {
    // ส่งข้อความ error หากเกิดข้อผิดพลาด
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลรูปภาพ: ' . $e->getMessage()
    ]);
}
?>