<?php
// Include the database and the Behavior class
require_once "../../config/Database.php";
require_once "../../class/Behavior.php";

// Instantiate database object and Behavior class
$database = new Database("phichaia_student");
$db = $database->getConnection();
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
$stu_id = $_POST['stuid']; // Student ID
$currentDate = $_POST['date'];
$type = $_POST['type']; // Type of behavior
$detail = $_POST['detail']; // Description of behavior
$score = behaviorscoretype($type); // Default behavior score
$teacherid = $_POST['teacherid']; // Teacher ID (set as static here, you can adjust as needed)
$term = $_POST['term']; // Term (set as static here, you can adjust as needed)
$pee = $_POST['pee']; // Pee (set as static here, you can adjust as needed)

// Check if a behavior entry already exists for the same student and date
$dateTime = new DateTime($currentDate);
$dateTime->modify('+543 years');

$newDate = $dateTime->format('Y-m-d');

try {
    $select_stmt = $db->prepare("SELECT behavior_date, behavior_type FROM behavior WHERE stu_id = :stu_id AND behavior_date = :udate AND behavior_type = :utype");
    $select_stmt->execute(array(
        ':stu_id' => $stu_id,
        ':udate' => $newDate,
        ':utype' => $type
    ));
    $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่ามีข้อมูลใน $row หรือไม่
    if ($row && $row['behavior_date'] == $newDate && ($row['behavior_type'] == 'มาโรงเรียนสาย' || $row['behavior_type'] == 'แต่งกาย/ทรงผมผิดระเบียบ')) {
        echo json_encode(['warning' => true, 'message' => 'มีการหักคะแนนพฤติกรรมประเภท ' . $type . ' ของวันนี้ไปแล้ว (' . $newDate . ')']);

    } else {
        // If no existing behavior, proceed to insert new behavior
        $behavior->stu_id = $stu_id;
        $behavior->behavior_date = $newDate;
        $behavior->behavior_type = $type;
        $behavior->behavior_name = $detail;
        $behavior->behavior_score = $score;
        $behavior->teach_id = $teacherid;
        $behavior->term = $term;
        $behavior->pee = $pee;

        // Call the create function from the Behavior class to insert data
        if ($behavior->create()) {
            echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว']);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()]);
}

?>
