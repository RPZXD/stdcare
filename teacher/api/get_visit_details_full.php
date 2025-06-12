<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

require_once "../../config/Database.php";
require_once "../../class/UserLogin.php";
require_once "../../class/Utils.php";

// Check authentication
if (!isset($_SESSION['Teacher_login'])) {
    echo '<div class="alert alert-danger">ไม่ได้รับอนุญาต</div>';
    exit;
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);

// Get parameters
$student_id = $_GET['student_id'] ?? '';
$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$pee = $user->getPee();

if (empty($student_id)) {
    echo '<div class="alert alert-danger">ไม่พบข้อมูลนักเรียน</div>';
    exit;
}

try {
    // Get student data
    $studentSql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, 
                          Stu_addr, Stu_phone, Par_phone 
                   FROM student 
                   WHERE Stu_id = :student_id";
    $studentStmt = $db->prepare($studentSql);
    $studentStmt->bindParam(':student_id', $student_id);
    $studentStmt->execute();
    $studentData = $studentStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$studentData) {
        echo '<div class="alert alert-danger">ไม่พบข้อมูลนักเรียน</div>';
        exit;
    }
    
    // Get visit data for both rounds
    $round1Data = null;
    $round2Data = null;
    
    // Round 1 (Term = 1)
    $visitSql = "SELECT * FROM visithome WHERE Stu_id = :student_id AND Term = '1' AND Pee = :pee";
    $visitStmt = $db->prepare($visitSql);
    $visitStmt->bindParam(':student_id', $student_id);
    $visitStmt->bindParam(':pee', $pee);
    $visitStmt->execute();
    $round1Data = $visitStmt->fetch(PDO::FETCH_ASSOC);
    
    // Round 2 (Term = 2)
    $visitSql = "SELECT * FROM visithome WHERE Stu_id = :student_id AND Term = '2' AND Pee = :pee";
    $visitStmt = $db->prepare($visitSql);
    $visitStmt->bindParam(':student_id', $student_id);
    $visitStmt->bindParam(':pee', $pee);
    $visitStmt->execute();
    $round2Data = $visitStmt->fetch(PDO::FETCH_ASSOC);
    
    // Questions array
    $questions = [
        "1. บ้านที่อยู่อาศัย" => ["บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น"],
        "2. ระยะทางระหว่างบ้านกับโรงเรียน" => ["1-5 กิโลเมตร", "6-10 กิโลเมตร", "11-15 กิโลเมตร", "16-20 กิโลเมตร", "20 กิโลเมตรขึ้นไป"],
        "3. การเดินทางไปโรงเรียนของนักเรียน" => ["เดิน", "รถจักรยาน", "รถจักรยานยนต์", "รถยนต์ส่วนตัว", "รถรับส่งรถโดยสาร", "อื่นๆ"],
        "4. สภาพแวดล้อมของบ้าน" => ["ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง"],
        "5. อาชีพของผู้ปกครอง" => ["เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ"],
        "6. สถานที่ทำงานของบิดามารดา" => ["ในอำเภอเดียวกัน", "ในจังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ"],
        "7. สถานภาพของบิดามารดา" => ["บิดามารดาอยู่ด้วยกัน", "บิดามารดาหย่าร้างกัน", "บิดาถึงแก่กรรม", "มารดาถึงแก่กรรม", "บิดาและมารดาถึงแก่กรรม"],
        "8. วิธีการที่ผู้ปกครองอบรมเลี้ยงดูนักเรียน" => ["เข้มงวดกวดขัน", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ"],
        "9. โรคประจำตัวของนักเรียน" => ["ไม่มี", "มี"],
        "10. ความสัมพันธ์ของสมาชิกในครอบครัว" => ["อบอุ่น", "เฉยๆ", "ห่างเหิน"],
        "11. หน้าที่รับผิดชอบภายในบ้าน" => ["มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี"],
        "12. สมาชิกในครอบครัวนักเรียนสนิทสนมกับใครมากที่สุด" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"],
        "13. รายได้กับการใช้จ่ายในครอบครัว" => ["เพียงพอ", "ไม่เพียงพอในบางครั้ง", "ขัดสน"],
        "14. ลักษณะเพื่อนเล่นที่บ้านของนักเรียนโดยปกติเป็น" => ["เพื่อนรุ่นเดียวกัน", "เพื่อนรุ่นน้อง", "เพื่อนรุ่นพี่", "เพื่อนทุกรุ่น"],
        "15. ความต้องการของผู้ปกครอง เมื่อนักเรียนจบชั้นสูงสุดของโรงเรียน" => ["ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ"],
        "16. เมื่อนักเรียนมีปัญหา นักเรียนจะปรึกษาใคร" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"],
        "17. ความรู้สึกของผู้ปกครองที่มีต่อครูที่มาเยี่ยมบ้าน" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"],
        "18. ทัศนคติ/ความรู้สึกของผู้ปกครองที่มีต่อโรงเรียน" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"],
    ];
    
    function getAnswerText($questionIndex, $answerValue, $questions) {
        $questionKey = array_keys($questions)[$questionIndex - 1];
        $options = $questions[$questionKey];
        
        if ($answerValue && isset($options[$answerValue - 1])) {
            return $options[$answerValue - 1];
        }
        return '-';
    }
    
    function renderVisitData($data, $roundNumber, $questions, $pee) {
        if (!$data) {
            return '<div class="alert alert-warning">ยังไม่มีข้อมูลการเยี่ยมบ้านรอบที่ ' . $roundNumber . '</div>';
        }
        
        $html = '<div class="card">';
        $html .= '<div class="card-header bg-primary text-white">';
        $html .= '<h5 class="mb-0"><i class="fas fa-home mr-2"></i>ข้อมูลการเยี่ยมบ้านรอบที่ ' . $roundNumber . '</h5>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        
        // Questions and Answers
        $html .= '<div class="row">';
        for ($i = 1; $i <= 18; $i++) {
            $questionKey = array_keys($questions)[$i - 1];
            $question = substr($questionKey, 2); // Remove number from question
            $answer = getAnswerText($i, $data['vh' . $i] ?? null, $questions);
            
            $html .= '<div class="col-md-6 mb-3">';
            $html .= '<div class="border rounded p-3 h-100">';
            $html .= '<strong class="text-primary">' . $question . '</strong><br>';
            $html .= '<span class="text-muted">' . htmlspecialchars($answer) . '</span>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        // Problems/Obstacles
        if (!empty($data['vh20'])) {
            $html .= '<div class="mt-4">';
            $html .= '<div class="card bg-light">';
            $html .= '<div class="card-header">';
            $html .= '<h6 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>ปัญหา/อุปสรรค และความต้องการความช่วยเหลือ</h6>';
            $html .= '</div>';
            $html .= '<div class="card-body">';
            $html .= '<p>' . nl2br(htmlspecialchars($data['vh20'])) . '</p>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        // Images
        $html .= '<div class="mt-4">';
        $html .= '<h6><i class="fas fa-images mr-2"></i>รูปภาพการเยี่ยมบ้าน</h6>';
        $html .= '<div class="row">';
        
        $imageLabels = [
            'picture1' => 'ภาพตัวบ้านนักเรียน',
            'picture2' => 'ภาพภายในบ้านนักเรียน', 
            'picture3' => 'ภาพขณะครูเยี่ยมบ้าน',
            'picture4' => 'ภาพเพิ่มเติม 1',
            'picture5' => 'ภาพเพิ่มเติม 2'
        ];
        
        for ($i = 1; $i <= 5; $i++) {
            $pictureField = 'picture' . $i;
            if (!empty($data[$pictureField])) {
                $imagePath = "../teacher/uploads/visithome" . ($pee - 543) . "/" . $data[$pictureField];
                $html .= '<div class="col-md-4 mb-3">';
                $html .= '<div class="card">';
                $html .= '<img src="' . $imagePath . '" class="card-img-top" style="height: 200px; object-fit: cover;" alt="' . $imageLabels[$pictureField] . '">';
                $html .= '<div class="card-body p-2">';
                $html .= '<p class="card-text text-center small">' . $imageLabels[$pictureField] . '</p>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    // Generate output
    ?>
    <div class="container-fluid">
        <!-- Student Info -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-user-graduate mr-2"></i>ข้อมูลนักเรียน</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>เลขประจำตัว:</strong> <?= htmlspecialchars($studentData['Stu_id']) ?></p>
                        <p><strong>ชื่อ-นามสกุล:</strong> <?= htmlspecialchars($studentData['Stu_pre'] . $studentData['Stu_name'] . ' ' . $studentData['Stu_sur']) ?></p>
                        <p><strong>ชั้น:</strong> <?= htmlspecialchars($studentData['Stu_major'] . '/' . $studentData['Stu_room']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>ที่อยู่:</strong> <?= htmlspecialchars($studentData['Stu_addr']) ?></p>
                        <p><strong>เบอร์โทรศัพท์:</strong> <?= htmlspecialchars($studentData['Stu_phone'] ?: '-') ?></p>
                        <p><strong>เบอร์ผู้ปกครอง:</strong> <?= htmlspecialchars($studentData['Par_phone'] ?: '-') ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Visit Data -->
        <div class="row">
            <div class="col-12 mb-4">
                <?= renderVisitData($round1Data, 1, $questions, $pee) ?>
            </div>
            <div class="col-12 mb-4">
                <?= renderVisitData($round2Data, 2, $questions, $pee) ?>
            </div>
        </div>
    </div>
    
    <?php
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
