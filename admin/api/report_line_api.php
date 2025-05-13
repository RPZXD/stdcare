<?php
require_once("../../class/Attendance.php");
require_once("../../class/UserLogin.php");

header('Content-Type: application/json; charset=utf-8');

// --- Config ---
$line_token = 'U9e0d2e5050696fef1168a9fcb9ca5a3f'; // LINE Notify Token
$channel_access_token = '3K7fh1bhbCn0uPjgNoGQpN3jNgpwpSoMA0QaE6m4dOMJkly+SeGyDyS73+EV6wSVuLoB6M/+FwdbxRWlY6ZGuQymNTYSrFzA5xQ7AhwlwOufu+et60PnAnYK2vpyvUyy3ye0yBe7cTu+PoiFDxsmmgdB04t89/1O/w1cDnyilFU='; // ใส่ Channel Access Token ของ Messaging API

// --- ฟังก์ชันสำหรับแมป class ไปยัง groupId ---
function getGroupIdByClass($class) {
    $map = [
        '1' => 'C0cf2923dbaf5ca2ff308a336bcaf1642', // invite
        '2' => 'C905068e97d63ba1ecc46091121735650', // updated groupId
        '3' => 'Cf28bd4fca19fb6d0d1b1b0f1116912a4',
        '4' => 'C7d75a57ea9078dd70076d7aee38f6e8a', // invite
        '5' => 'Cccc2671904450ba9977acd4992e99898', // invite
        '6' => 'Ce05c66cecc5b60c51921d722c2825825', // invite
    ];
    // $map = [
    //     '1' => 'U9e0d2e5050696fef1168a9fcb9ca5a3f', // invite
    //     '2' => 'U9e0d2e5050696fef1168a9fcb9ca5a3f', // updated groupId
    //     '3' => 'U9e0d2e5050696fef1168a9fcb9ca5a3f',
    //     '4' => 'U9e0d2e5050696fef1168a9fcb9ca5a3f', // invite
    //     '5' => 'U9e0d2e5050696fef1168a9fcb9ca5a3f', // invite
    //     '6' => 'U9e0d2e5050696fef1168a9fcb9ca5a3f', // invite
    // ];
    return $map[$class] ?? '';
}

$date = $_REQUEST['date'] ?? date('Y-m-d');

// --- เตรียมข้อมูล ---
require_once("../../config/Database.php");
$dbObj = new Database("phichaia_student");
$db = $dbObj->getConnection();
$attendance = new Attendance($db);

$user = new UserLogin($db);
$term = $user->getTerm();
$pee = $user->getPee();

// --- เพิ่ม: include class AttendanceSummary ---
require_once('../../class/AttendanceSummary.php');

// --- แปลงวันที่ ---
function convertToBuddhistYear($date) {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        list($year, $month, $day) = explode('-', $date);
        if ($year < 2500) $year += 543;
        return $year . '-' . $month . '-' . $day;
    }
    return $date;
}
function thaiDateShort($date) {
    $months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
        5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
        9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
        $year = (int)$m[1];
        $month = (int)$m[2];
        $day = (int)$m[3];
        if ($year < 2500) $year += 543;
        return $day . ' ' . $months[$month] . ' ' . $year;
    }
    return $date;
}
$dateC = convertToBuddhistYear($date);

// --- ส่ง flex ของทุก class ทุก room ---
$results = [];
$classMap = [
    '1' => getGroupIdByClass('1'),
    '2' => getGroupIdByClass('2'),
    '3' => getGroupIdByClass('3'),
    '4' => getGroupIdByClass('4'),
    '5' => getGroupIdByClass('5'),
    '6' => getGroupIdByClass('6'),
];
foreach ($classMap as $classKey => $groupId) {
    // ดึงห้องของแต่ละ class
    $stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_major = :class AND Stu_status = 1 ORDER BY Stu_room ASC");
    $stmt->execute([':class' => $classKey]);
    $rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($rooms as $room) {
        $students_all = $attendance->getStudentsWithAttendance($dateC, $classKey, $room, $term, $pee);

        // --- ใช้คลาส AttendanceSummary ---
        $summary = new AttendanceSummary($students_all, $classKey, $room, $date, $term, $pee);
        $text_message = $summary->getTextSummary();
        $flex = $summary->getFlexMessage();

        // --- ส่ง Flex Message ไปยังกลุ่ม LINE (Messaging API) ---
        $results[] = [
            'class' => $classKey,
            'room' => $room,
            'groupId' => $groupId,
            'line_flex_response' => send_line_flex($channel_access_token, $groupId, $flex),
            'flex_example' => $flex
        ];
    }
}

// --- ตอบกลับ ---
echo json_encode([
    'status' => 'ok',
    'results' => $results
]);

// --- ฟังก์ชันส่งข้อความไป LINE Notify ---
function send_line_notify($token, $message) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['message' => $message]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// --- ส่ง Flex Message ไปยังกลุ่ม LINE (Messaging API) ---
function send_line_flex($channel_access_token, $groupId, $flex) {
    $url = "https://api.line.me/v2/bot/message/push";
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer {$channel_access_token}"
    ];
    $body = [
        "to" => $groupId,
        "messages" => [
            [
                "type" => "flex",
                "altText" => "สรุปการมาเรียน",
                "contents" => $flex
            ]
        ]
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
