<?php
include_once("../../config/Database.php");
include_once("../../class/Behavior.php");

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$behavior = new Behavior($db);
function behaviorscoretype($type)
{
    switch ($type) {
        case "หนีเรียนหรือออกนอกสถานศึกษา":
          $results = 10;
          break;
        case "เล่นการพนัน":
          $results = 20;
          break;
        case "มาโรงเรียนสาย":
          $results = 5;
          break;
        case "แต่งกาย/ทรงผมผิดระเบียบ":
          $results = 5;
          break;
        case "พกพาอาวุธหรือวัตถุระเบิด":
          $results = 20;
          break;
        case "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์":
          $results = 20;
          break;
        case "สูบบุหรี่":
          $results = 30;
          break;
        case "เสพยาเสพติด":
          $results = 30;
          break;
        case "ลักทรัพย์ กรรโชกทรัพย์":
          $results = 30;
          break;
        case "ก่อเหตุทะเลาะวิวาท":
          $results = 20;
          break;
        case "แสดงพฤติกรรมทางชู้สาว":
          $results = 20;
          break;
        case "จอดรถในที่ห้ามจอด":
          $results = 10;
          break;
        case "แสดงพฤติกรรมก้าวร้าว":
          $results = 10;
          break;
        case "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ":
          $results = 5;
          break;

        default:
          $results = '';
      }
    return $results;
}
// Get POST data
$id = $_POST['id']; // ID of the behavior entry to update
$stu_id = $_POST['StuId']; // Student ID
$currentDate = $_POST['BehaviorDate'];
$type = $_POST['BehaviorType']; // Type of behavior
$detail = $_POST['BehaviorName']; // Description of behavior
$score = behaviorscoretype($type); // Default behavior score
// Check if a behavior entry already exists for the same student and date
$dateTime = new DateTime($currentDate);
$dateTime->modify('+543 years');

$newDate = $dateTime->format('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $select_stmt = $db->prepare("SELECT behavior_date, behavior_type FROM behavior WHERE stu_id = :stu_id AND behavior_date = :udate AND behavior_type = :utype");
        $select_stmt->execute(array(
            ':stu_id' => $stu_id,
            ':udate' => $newDate,
            ':utype' => $type
        ));
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
    
        // ตรวจสอบว่ามีข้อมูลใน $row หรือไม่
            // If no existing behavior, proceed to insert new behavior
            $behavior->id = $id;
            $behavior->stu_id = $stu_id;
            $behavior->behavior_date = $newDate;
            $behavior->behavior_type = $type;
            $behavior->behavior_name = $detail;
            $behavior->behavior_score = $score;
    
            // Call the create function from the Behavior class to insert data
            if ($behavior->update()) {
                echo json_encode(['success' => true, 'message' => 'อัพเดทข้อมูลเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล']);
            }
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล: ' . $e->getMessage()]);
    }
    

} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>
