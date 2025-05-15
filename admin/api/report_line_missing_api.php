<?php
require_once("../../class/Attendance.php");
require_once("../../class/UserLogin.php");
require_once("../../config/Database.php");
require_once('../../class/AttendanceSummary.php');

header('Content-Type: application/json; charset=utf-8');

// --- Config ---
$channel_access_token = '3K7fh1bhbCn0uPjgNoGQpN3jNgpwpSoMA0QaE6m4dOMJkly+SeGyDyS73+EV6wSVuLoB6M/+FwdbxRWlY6ZGuQymNTYSrFzA5xQ7AhwlwOufu+et60PnAnYK2vpyvUyy3ye0yBe7cTu+PoiFDxsmmgdB04t89/1O/w1cDnyilFU=';

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
    return $map[$class] ?? '';
}

// --- แปลงวันที่ ---
function convertToBuddhistYear($date) {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        list($year, $month, $day) = explode('-', $date);
        if ($year < 2500) $year += 543;
        return $year . '-' . $month . '-' . $day;
    }
    return $date;
}

$date = $_REQUEST['date'] ?? date('Y-m-d');
$dateC = convertToBuddhistYear($date);

// เช็คถ้าเป็นวันเสาร์หรืออาทิตย์ ไม่ต้องส่งข้อความ
$dayOfWeek = date('N', strtotime($date)); // 6=Saturday, 7=Sunday
if ($dayOfWeek == 6 || $dayOfWeek == 7) {
    echo json_encode([
        'status' => 'skip',
        'message' => 'ไม่ส่งข้อความในวันเสาร์-อาทิตย์'
    ]);
    exit;
}

$dbObj = new Database("phichaia_student");
$db = $dbObj->getConnection();
$attendance = new Attendance($db);
$user = new UserLogin($db);
$term = $user->getTerm();
$pee = $user->getPee();

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

    $missing_rooms = [];
    foreach ($rooms as $room) {
        // ดึงรายชื่อ Stu_id ของห้องนี้
        $stmtStu = $db->prepare("SELECT Stu_id FROM student WHERE Stu_major = :class AND Stu_room = :room AND Stu_status = 1");
        $stmtStu->execute([
            ':class' => $classKey,
            ':room' => $room
        ]);
        $stu_ids = $stmtStu->fetchAll(PDO::FETCH_COLUMN);

        if (empty($stu_ids)) continue;

        // ตรวจสอบว่ามีใครในห้องนี้บันทึกการมาเรียนในวันนั้นหรือยัง
        $placeholders = implode(',', array_fill(0, count($stu_ids), '?'));
        $sql = "SELECT COUNT(*) FROM student_attendance WHERE attendance_date = ? AND student_id IN ($placeholders)";
        $params = array_merge([$date], $stu_ids);
        $stmt2 = $db->prepare($sql);
        $stmt2->execute($params);
        $count = $stmt2->fetchColumn();

        // ถ้าไม่มีเลย แสดงว่ายังไม่ได้รายงาน
        if ($count == 0) {
            $missing_rooms[] = $room;
        }
    }

    if (count($missing_rooms) > 0) {
        // สร้าง flex message สำหรับ class นี้ (ตกแต่ง/เพิ่มอิโมจิ)
        $flex = [
            "type" => "bubble",
            "size" => "mega",
            "header" => [
                "type" => "box",
                "layout" => "vertical",
                "backgroundColor" => "#ffeaea",
                "contents" => [
                    [
                        "type" => "text",
                        "text" => "🚨 ยังไม่ได้รายงานการมาเรียน",
                        "weight" => "bold",
                        "size" => "lg",
                        "color" => "#d32f2f",
                        "align" => "center",
                        "margin" => "md"
                    ],
                    [
                        "type" => "text",
                        "text" => "ระดับชั้น ม.$classKey",
                        "size" => "md",
                        "color" => "#333333",
                        "align" => "center",
                        "margin" => "sm"
                    ]
                ]
            ],
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "spacing" => "md",
                "contents" => array_merge(
                    [
                        [
                            "type" => "text",
                            "text" => "📝 ห้องที่ยังไม่ได้รายงาน:",
                            "weight" => "bold",
                            "size" => "md",
                            "color" => "#d32f2f",
                            "margin" => "none"
                        ]
                    ],
                    array_map(function($room) {
                        return [
                            "type" => "box",
                            "layout" => "horizontal",
                            "margin" => "sm",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => "🚫 ห้อง $room",
                                    "size" => "md",
                                    "color" => "#b71c1c",
                                    "weight" => "bold",
                                    "align" => "start"
                                ]
                            ]
                        ];
                    }, $missing_rooms)
                )
            ],
            "footer" => [
                "type" => "box",
                "layout" => "vertical",
                "backgroundColor" => "#f5f5f5",
                "contents" => [
                    [
                        "type" => "separator",
                        "margin" => "md"
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "margin" => "md",
                        "contents" => [
                            [
                                "type" => "text",
                                "text" => "📅 วันที่ " . date('d/m/Y', strtotime($date)),
                                "size" => "sm",
                                "color" => "#888888",
                                "align" => "start"
                            ]
                        ]
                    ],
                    [
                        "type" => "text",
                        "text" => "กรุณาตรวจสอบและรายงานให้ครบถ้วน 🙏",
                        "size" => "xs",
                        "color" => "#d32f2f",
                        "align" => "center",
                        "margin" => "md"
                    ]
                ]
            ]
        ];

        // ส่ง Flex Message ไปยังกลุ่ม LINE (Messaging API)
        $line_response = send_line_flex($channel_access_token, $groupId, $flex);

        $results[] = [
            'class' => $classKey,
            'groupId' => $groupId,
            'missing_rooms' => $missing_rooms,
            'line_flex_response' => $line_response,
            'flex_example' => $flex
        ];
    }
}

// --- ตอบกลับ ---
echo json_encode([
    'status' => 'ok',
    'results' => $results
]);

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
                "altText" => "แจ้งเตือนห้องที่ยังไม่ได้รายงานการมาเรียน",
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
