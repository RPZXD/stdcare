<?php
$token = "8503085481:AAGU1Qh4_rm0J5XSt0MS4d5zf42WFuA0Emg";
$update = json_decode(file_get_contents("php://input"), true);

// ป้องกัน error กรณี update ไม่มี message (เช่น callback)
$message = $update["message"] ?? null;
if (!$message) exit;

$chat_id = $message["chat"]["id"] ?? null;
$text = trim($message["text"] ?? '');

if (!$chat_id) exit; // กัน error

require_once __DIR__ . '/classes/DatabaseUsers.php';

try {
    $db = new \App\DatabaseUsers();
} catch (Exception $e) {
    error_log('DB connection error: ' . $e->getMessage());
    exit;
}

if ($text === "/start") {
    sendMessage($chat_id,
        "สวัสดีค่ะ 🙏\nกรุณาส่ง *รหัสนักเรียน* หรือ *เลขประจำตัวนักเรียน* เพื่อยืนยันตัวตน",
    );
    exit;
}

// ============================
// ส่วนยืนยันรหัสนักเรียน
// ============================
$student_id = $text;

// ตรวจสอบในฐานข้อมูล
$student = $db->getStudentByUsername($student_id);

if ($student) {

    // รองรับหลายชื่อ field
    $stu_id = $student['Stu_id'] 
                ?? $student['student_id']
                ?? $student['studentID']
                ?? $student_id;

    $stu_name = $student['Stu_name'] 
                ?? $student['fullname']
                ?? $student['name']
                ?? "ไม่ทราบชื่อ";

    // บันทึก / อัปเดตข้อมูลผู้ปกครอง
    $db->query(
        "INSERT INTO parents (telegram_id, student_id, verified)
         VALUES (:tg, :stu, 1)
         ON DUPLICATE KEY UPDATE verified = 1",
        ['tg' => $chat_id, 'stu' => $stu_id]
    );

    sendMessage($chat_id, "ยืนยันสำเร็จ 🎉\nคุณคือผู้ปกครองของ: *{$stu_name}*");

} else {
    sendMessage($chat_id, "❌ ไม่พบรหัสนักเรียนนี้ กรุณาลองใหม่");
}


// ============================
// ฟังก์ชันส่งข้อความกลับ Telegram
// ============================
function sendMessage($chat_id, $text)
{
    global $token;
    $url = "https://api.telegram.org/bot$token/sendMessage";
    
    // ใช้ curl จะเสถียรกว่า file_get_contents
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'Markdown'
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    file_get_contents($url, false, stream_context_create($options));
}
?>
