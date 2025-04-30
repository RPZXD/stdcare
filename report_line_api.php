<?php
require_once("class/Attendance.php");
require_once("class/UserLogin.php");

header('Content-Type: application/json; charset=utf-8');

// --- Config ---
$line_token = 'U9e0d2e5050696fef1168a9fcb9ca5a3f'; // LINE Notify Token
$channel_access_token = '3K7fh1bhbCn0uPjgNoGQpN3jNgpwpSoMA0QaE6m4dOMJkly+SeGyDyS73+EV6wSVuLoB6M/+FwdbxRWlY6ZGuQymNTYSrFzA5xQ7AhwlwOufu+et60PnAnYK2vpyvUyy3ye0yBe7cTu+PoiFDxsmmgdB04t89/1O/w1cDnyilFU='; // ใส่ Channel Access Token ของ Messaging API

// --- ฟังก์ชันสำหรับแมป class ไปยัง groupId ---
function getGroupIdByClass($class) {
    $map = [
        '1' => 'C4be823f58bf3d70d10f4024e01da302d',
        '2' => 'C4be823f58bf3d70d10f4024e01da302d',
        '3' => 'C4be823f58bf3d70d10f4024e01da302d',
        '4' => 'C4be823f58bf3d70d10f4024e01da302d',
        '5' => 'C4be823f58bf3d70d10f4024e01da302d',
        '6' => 'C4be823f58bf3d70d10f4024e01da302d',
    ];
    return $map[$class] ?? 'C4be823f58bf3d70d10f4024e01da302d';
}

// --- รับค่า ---
$class = $_REQUEST['class'] ?? null;
$date = $_REQUEST['date'] ?? date('Y-m-d');

// --- ตรวจสอบค่า ---
if (!$class) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing class']);
    exit;
}

// --- เตรียมข้อมูล ---
require_once("config/Database.php");
$dbObj = new Database("phichaia_student");
$db = $dbObj->getConnection();
$attendance = new Attendance($db);

$user = new UserLogin($db);
$term = $user->getTerm();
$pee = $user->getPee();

// --- กำหนด groupId ตาม class ---
$groupId = getGroupIdByClass($class);

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

// --- ดึงรายชื่อห้องทั้งหมดใน class นี้ ---
$stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_major = :class AND Stu_status = 1 ORDER BY Stu_room ASC");
$stmt->execute([':class' => $class]);
$rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

// --- ส่ง flex ของทุก room ---
$results = [];
foreach ($rooms as $room) {
    $students_all = $attendance->getStudentsWithAttendance($dateC, $class, $room, $term, $pee);

    $status_labels = [
        '1' => ['label' => 'มาเรียน', 'emoji' => '✅'],
        '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌'],
        '3' => ['label' => 'มาสาย', 'emoji' => '🕒'],
        '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒'],
        '5' => ['label' => 'ลากิจ', 'emoji' => '📝'],
        '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉'],
    ];
    $status_count = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];
    $status_names = ['1'=>[],'2'=>[],'3'=>[],'4'=>[],'5'=>[],'6'=>[]];

    foreach ($students_all as $s) {
        $st = $s['attendance_status'] ?? null;
        if ($st && isset($status_count[$st])) {
            $status_count[$st]++;
            if ($st !== '1') {
                $status_names[$st][] = $s['Stu_pre'].$s['Stu_name'].' '.$s['Stu_sur'].' ('.$s['Stu_no'].')';
            }
        }
    }
    $total = count($students_all);

    // --- สร้างข้อความสรุป ---
    $lines = [];
    $lines[] = "สรุปการมาเรียน ม.$class/$room วันที่ ".thaiDateShort($date);
    foreach ($status_labels as $key => $info) {
        $percent = $total ? round($status_count[$key]*100/$total,1) : 0;
        $lines[] = "{$info['emoji']} {$info['label']}: {$status_count[$key]} คน ($percent%)";
        if ($key !== '1' && !empty($status_names[$key])) {
            $lines[] = " - ".implode(", ", $status_names[$key]);
        }
    }
    $text_message = implode("\n", $lines);

    // --- สร้าง Flex Message (ตกแต่งใหม่) ---
    $flex = [
        "type" => "bubble",
        "size" => "mega",
        "header" => [
            "type" => "box",
            "layout" => "vertical",
            "backgroundColor" => "#1B8F3A",
            "contents" => [[
                "type" => "text",
                "text" => "สรุปการมาเรียน",
                "weight" => "bold",
                "size" => "xl",
                "color" => "#ffffff",
                "align" => "center"
            ]]
        ],
        "body" => [
            "type" => "box",
            "layout" => "vertical",
            "spacing" => "md",
            "contents" => [
                [
                    "type" => "text",
                    "text" => "ชั้น ม.$class/$room",
                    "weight" => "bold",
                    "size" => "lg",
                    "color" => "#1B8F3A",
                    "align" => "center"
                ],
                [
                    "type" => "text",
                    "text" => thaiDateShort($date),
                    "size" => "sm",
                    "color" => "#888888",
                    "align" => "center"
                ],
                [
                    "type" => "separator",
                    "margin" => "md"
                ],
                [
                    "type" => "box",
                    "layout" => "vertical",
                    "spacing" => "sm",
                    "margin" => "md",
                    "contents" => array_map(function($key) use ($status_labels, $status_count, $total) {
                        $info = $status_labels[$key];
                        $percent = $total ? round($status_count[$key]*100/$total,1) : 0;
                        $colorMap = [
                            '1' => "#22c55e", // green
                            '2' => "#ef4444", // red
                            '3' => "#eab308", // yellow
                            '4' => "#3b82f6", // blue
                            '5' => "#a21caf", // purple
                            '6' => "#ec4899", // pink
                        ];
                        return [
                            "type" => "box",
                            "layout" => "horizontal",
                            "backgroundColor" => $key === '1' ? "#f0fdf4" : "#f9fafb",
                            "cornerRadius" => "md",
                            "paddingAll" => "md",
                            "contents" => [
                                [
                                    "type" => "text",
                                    "text" => $info['emoji'],
                                    "size" => "xl",
                                    "flex" => 1,
                                    "align" => "center"
                                ],
                                [
                                    "type" => "text",
                                    "text" => $info['label'],
                                    "size" => "md",
                                    "weight" => "bold",
                                    "color" => $colorMap[$key],
                                    "flex" => 3
                                ],
                                [
                                    "type" => "text",
                                    "text" => "{$status_count[$key]} คน",
                                    "size" => "md",
                                    "align" => "end",
                                    "color" => "#333333",
                                    "flex" => 2
                                ],
                                [
                                    "type" => "text",
                                    "text" => "($percent%)",
                                    "size" => "xs",
                                    "align" => "end",
                                    "color" => "#888888",
                                    "flex" => 2
                                ]
                            ]
                        ];
                    }, array_keys($status_labels))
                ],
                [
                    "type" => "separator",
                    "margin" => "md"
                ],
                [
                    "type" => "text",
                    "text" => "รายชื่อที่ไม่ได้มาเรียนปกติ",
                    "weight" => "bold",
                    "size" => "md",
                    "color" => "#ef4444",
                    "margin" => "md"
                ],
                [
                    "type" => "box",
                    "layout" => "vertical",
                    "spacing" => "xs",
                    "margin" => "sm",
                    "contents" => array_reduce(array_keys($status_labels), function($carry, $key) use ($status_labels, $status_names) {
                        if ($key === '1') return $carry;
                        if (!empty($status_names[$key])) {
                            $carry[] = [
                                "type" => "text",
                                "text" => $status_labels[$key]['emoji']." ".$status_labels[$key]['label'].": ".implode(", ", $status_names[$key]),
                                "size" => "sm",
                                "color" => "#333333",
                                "wrap" => true
                            ];
                        }
                        return $carry;
                    }, [])
                ]
            ]
        ],
        "footer" => [
            "type" => "box",
            "layout" => "vertical",
            "contents" => [[
                "type" => "text",
                "text" => "STD Care",
                "size" => "xs",
                "color" => "#aaaaaa",
                "align" => "center"
            ]]
        ]
    ];

    // --- ส่ง Flex Message ไปยังกลุ่ม LINE (Messaging API) ---
    $results[] = [
        'room' => $room,
        'line_flex_response' => send_line_flex($channel_access_token, $groupId, $flex),
        'flex_example' => $flex
    ];
}

// --- ส่งข้อความไป LINE Notify (ข้อความธรรมดาเท่านั้น) เฉพาะ class/room ที่รับมาจาก request (optional) ---
if (isset($_REQUEST['room'])) {
    $room = $_REQUEST['room'];
    $students_all = $attendance->getStudentsWithAttendance($dateC, $class, $room, $term, $pee);
    $status_labels = [
        '1' => ['label' => 'มาเรียน', 'emoji' => '✅'],
        '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌'],
        '3' => ['label' => 'มาสาย', 'emoji' => '🕒'],
        '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒'],
        '5' => ['label' => 'ลากิจ', 'emoji' => '📝'],
        '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉'],
    ];
    $status_count = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];
    $status_names = ['1'=>[],'2'=>[],'3'=>[],'4'=>[],'5'=>[],'6'=>[]];

    foreach ($students_all as $s) {
        $st = $s['attendance_status'] ?? null;
        if ($st && isset($status_count[$st])) {
            $status_count[$st]++;
            if ($st !== '1') {
                $status_names[$st][] = $s['Stu_pre'].$s['Stu_name'].' '.$s['Stu_sur'].' ('.$s['Stu_no'].')';
            }
        }
    }
    $total = count($students_all);

    // --- สร้างข้อความสรุป ---
    $lines = [];
    $lines[] = "สรุปการมาเรียน ม.$class/$room วันที่ ".thaiDateShort($date);
    foreach ($status_labels as $key => $info) {
        $percent = $total ? round($status_count[$key]*100/$total,1) : 0;
        $lines[] = "{$info['emoji']} {$info['label']}: {$status_count[$key]} คน ($percent%)";
        if ($key !== '1' && !empty($status_names[$key])) {
            $lines[] = " - ".implode(", ", $status_names[$key]);
        }
    }
    $text_message = implode("\n", $lines);

    // --- ส่งข้อความไป LINE Notify (ข้อความธรรมดาเท่านั้น) ---
    $notify_response = send_line_notify($line_token, $text_message);
} else {
    $notify_response = null;
}

// --- ตอบกลับ ---
echo json_encode([
    'status' => 'ok',
    'class' => $class,
    'groupId' => $groupId,
    'results' => $results,
    'line_notify_response' => $notify_response
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
