<?php
require_once "../../config/Database.php";


$database = new Database("phichaia_student");
$db = $database->getConnection();

function sendLineNotifyMessage($accessToken, $message) {
    $url = 'https://notify-api.line.me/api/notify';
    
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $accessToken
    );
    
    $data = array(
        'message' => $message
    );
    
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    );
    
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    
    $response = curl_exec($curl);
    $error = curl_error($curl);
    
    curl_close($curl);
    
    if ($error) {
    } else {
    }
}


function levellinegroup($major)
{
    switch ($major) {
        case "1":
          $results = 'Pg1GJ0OqUin2WpvoNrcZIZHKge9OSpcPBaharTZWY1K';
        //   $results = 'cWbJ8cFrosOwnos7lAmYjZxyChjE92pwVXJEOQz847w';
          break;
        case "2":
          $results = 'NtEvhFrd2RJj2KziqHDSszBVCETAIgYZlEPwCzItUbg';
        //   $results = 'NtEvhFrd2RJj2KziqHDSszBVCETAIgYZlEPwCzItUbg';
          break;
        case "3":
          $results = '1N2DdrTBLkP1fi1ieIIqmWKxSeolcPb7is2Keby7Mb7';
        //   $results = 'cWbJ8cFrosOwnos7lAmYjZxyChjE92pwVXJEOQz847w';
          break;
        case "4":
          $results = 'UAiAbFto3DFZIZA1yccYHsAWZFEOb5AtoJrbwdW6iE2';
        //   $results = 'cWbJ8cFrosOwnos7lAmYjZxyChjE92pwVXJEOQz847w';
          break;
        case "5":
          $results = 'BM7i0flDx9GhLPK256IgXV2GemIOz30ZZ0lmVB0l1I2';
        //   $results = 'cWbJ8cFrosOwnos7lAmYjZxyChjE92pwVXJEOQz847w';
          break;
        case "6":
          $results = 'eJKEM1Xat1LVPfzwQOklpKBuAQtdCo0N4HKH9nSIc9c';
        //   $results = 'cWbJ8cFrosOwnos7lAmYjZxyChjE92pwVXJEOQz847w';
          break;
        default:
          $results = '';
      }
    return $results;
}

function convertThaiDate($dateString) {
    // แปลงวันที่เป็น timestamp
    $timestamp = strtotime($dateString);
    
    // ตรวจสอบว่าแปลงวันที่สำเร็จหรือไม่
    if (!$timestamp) {
        return "Invalid date";
    }

    // กำหนดชื่อเดือนภาษาไทย
    $thaiMonths = [
        "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
        "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
    ];

    // ดึงค่าวัน, เดือน, ปี พ.ศ.
    $day = date("j", $timestamp);
    $month = date("n", $timestamp) - 1; // -1 เพื่อใช้ index ของ array
    $year = date("Y", $timestamp);

    return "{$day} {$thaiMonths[$month]} พ.ศ. {$year}";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_date = $_POST['attendance_date'];
    // Convert attendance_date to Buddhist calendar year (พ.ศ.)
    $date = new DateTime($attendance_date);
    $date->modify('+543 years');
    $attendance_date = $date->format('Y-m-d');

    $class = $_POST['class'];
    $room = $_POST['room'];
    $term = $_POST['term'];
    $pee = $_POST['pee'];
    $teacher_id = $_POST['teacher_id'];
    $teacher_name = $_POST['teacher_name'];
    $checks = $_POST['check'];

    $statusCount = [
        1 => 0, // มาเรียน
        2 => 0, // ขาดเรียน
        3 => 0, // มาสาย
        4 => 0, // ลาป่วย
        5 => 0, // ลากิจ
        6 => 0  // เข้าร่วมกิจกรรม
    ];

    try {
        $db->beginTransaction();

        // Prepare a bulk insert or update statement for the study table
        $stmt = $db->prepare("
            INSERT INTO study (Stu_id, Study_date, Study_status, Study_term, Study_pee) 
            VALUES (:stu_id, :study_date, :study_status, :study_term, :study_pee)
            ON DUPLICATE KEY UPDATE Study_status = VALUES(Study_status)
        ");

        // Prepare the insert statement for the behavior table
        $behaviorStmt = $db->prepare("
            INSERT INTO behavior (Stu_id, Behavior_date, Behavior_type, Behavior_name, Behavior_score, Teach_id, Behavior_term, Behavior_pee)
            VALUES (:stu_id, :behavior_date, :behavior_type, :behavior_name, :behavior_score, :teach_id, :behavior_term, :behavior_pee)
            ON DUPLICATE KEY UPDATE Behavior_score = VALUES(Behavior_score)
        ");

        // Prepare the insert statement for the ckstudy table with ON DUPLICATE KEY UPDATE
        $ckstudyStmt = $db->prepare("
            INSERT INTO ckstudy (ck_date, Stu_major, Stu_room, ck_term, ck_pee)
            VALUES (:ck_date, :stu_major, :stu_room, :ck_term, :ck_pee)
            ON DUPLICATE KEY UPDATE ck_date = VALUES(ck_date)
        ");

        // Loop through checks and prepare values for bulk operation
        foreach ($checks as $index => $check) {
            $stu_id = $_POST['stu_id'][$index];
            $status = $check;

            // Increment the status count
            if (isset($statusCount[$status])) {
                $statusCount[$status]++;
            }

            // Bind parameters for study table
            $stmt->bindParam(':stu_id', $stu_id);
            $stmt->bindParam(':study_date', $attendance_date);
            $stmt->bindParam(':study_status', $status);
            $stmt->bindParam(':study_term', $term);
            $stmt->bindParam(':study_pee', $pee);

            // Execute the study insert/update query
            $stmt->execute();

            // If study_status is 3 (Late to school) or 2 (Absent), insert into behavior table
            if ($status == 3 || $status == 2) {
                $behaviorStmt->execute([
                    ':stu_id' => $stu_id,
                    ':behavior_date' => $attendance_date,
                    ':behavior_type' => ($status == 3) ? 'มาโรงเรียนสาย' : 'ขาดเรียน',
                    ':behavior_name' => ($status == 3) ? 'มาโรงเรียนสาย' : 'ขาดเรียน',
                    ':behavior_score' => 5,
                    ':teach_id' => $teacher_id,
                    ':behavior_term' => $term,
                    ':behavior_pee' => $pee
                ]);
            }

            // Bind parameters for ckstudy table
            $ckstudyStmt->bindParam(':ck_date', $attendance_date);
            $ckstudyStmt->bindParam(':stu_major', $class);
            $ckstudyStmt->bindParam(':stu_room', $room);
            $ckstudyStmt->bindParam(':ck_term', $term);
            $ckstudyStmt->bindParam(':ck_pee', $pee);

            // Execute the ckstudy insert query
            $ckstudyStmt->execute();
        }

        $db->commit();

        $message = "📊 รายงานการมาเรียน ชั้น ม.". $class . "/" . $room ."\n";
        $message .= "📅 บันทึกของวันที่ : ". convertThaiDate($attendance_date) ."\n";
        foreach ($statusCount as $status => $count) {
            switch ($status) {
                case 1: $statusText = "✅ มาเรียน"; break;
                case 2: $statusText = "❌ ขาดเรียน"; break;
                case 3: $statusText = "⏰ มาสาย"; break;
                case 4: $statusText = "🤒 ลาป่วย"; break;
                case 5: $statusText = "🏖️ ลากิจ"; break;
                case 6: $statusText = "🎉 เข้าร่วมกิจกรรม"; break;
            }
            $message .= "$statusText: $count คน\n";
        }
        $message .= "👨‍🏫 ผู้บันทึก : ". $teacher_name ."\n";

        // Send the LINE notification
        // $accessToken = levellinegroup($class);
        $accessToken = '';
        sendLineNotifyMessage($accessToken, $message);
        
        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว']);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
