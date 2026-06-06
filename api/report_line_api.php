<?php
require_once(dirname(__DIR__) . "/config/Database.php");
require_once(dirname(__DIR__) . "/class/Attendance.php");
require_once(dirname(__DIR__) . "/class/UserLogin.php");
require_once(dirname(__DIR__) . "/class/AttendanceSummary.php");

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Bangkok');

try {
    $dbObj = new Database("phichaia_student");
    $db = $dbObj->getConnection();
    
    // 1. ดึงการตั้งค่าที่จำเป็นจากฐานข้อมูล
    $stmtSettings = $db->query("SELECT setting_key, setting_value FROM time_settings");
    $timeSettings = $stmtSettings->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $line_token = $timeSettings['line_notify_token'] ?? ''; // LINE Notify Token (if needed)
    $channel_access_token = $timeSettings['line_channel_access_token'] ?? ''; // LINE Messaging API Channel Token
    $line_report_time = $timeSettings['line_report_time'] ?? '08:30:00';
    $term_start_date = $timeSettings['term_start_date'] ?? null;
    $term_end_date = $timeSettings['term_end_date'] ?? null;
    
    $date = $_REQUEST['date'] ?? date('Y-m-d');
    $today = date('Y-m-d');
    $currentTime = date('H:i:s');
    
    // ฟังก์ชันช่วยดึง Group ID ตามระดับชั้น
    function getGroupIdByClass($class, $timeSettings) {
        return $timeSettings['line_group_id_' . $class] ?? '';
    }

    // 2. เช็ควันเสาร์-อาทิตย์
    $dayOfWeek = date('N', strtotime($date)); // 6=Saturday, 7=Sunday
    if ($dayOfWeek >= 6) {
        echo json_encode([
            'status' => 'skip',
            'message' => 'ไม่ส่งข้อความในวันเสาร์-อาทิตย์'
        ]);
        exit;
    }

    // 3. เช็คระยะเวลาเปิด-ปิดภาคเรียน
    if ($term_start_date && $term_end_date) {
        if ($date < $term_start_date || $date > $term_end_date) {
            echo json_encode([
                'status' => 'skip',
                'message' => 'อยู่นอกระยะเวลาเปิด-ปิดภาคเรียน ไม่ส่งข้อความสรุป'
            ]);
            exit;
        }
    }

    // 4. เช็ควันหยุดพิเศษจากฐานข้อมูล
    $stmtHoliday = $db->prepare("SELECT description FROM school_holidays WHERE holiday_date = :date");
    $stmtHoliday->execute([':date' => $date]);
    $holiday = $stmtHoliday->fetchColumn();

    if ($holiday) {
        echo json_encode([
            'status' => 'skip',
            'message' => 'วันนี้เป็นวันหยุดพิเศษ: ' . $holiday . ' (ไม่ส่งข้อความสรุป)'
        ]);
        exit;
    }

    // 5. เช็คเวลาปัจจุบันกับเวลาส่งรายงาน (ถ้าเป็นวันที่ปัจจุบัน)
    if ($date === $today && $currentTime < $line_report_time) {
        echo json_encode([
            'status' => 'skip',
            'message' => 'ยังไม่ถึงเวลาส่งข้อความสรุป (รอให้ถึงเวลา ' . $line_report_time . ')'
        ]);
        exit;
    }

    $attendance = new Attendance($db);
    $user = new UserLogin($db);
    $term = $user->getTerm();
    $pee = $user->getPee();

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาดในการเริ่มต้นฐานข้อมูล/การตั้งค่า: ' . $e->getMessage()
    ]);
    exit;
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

// --- ส่ง flex สรุปของทุกระดับชั้น ---
$results = [];
$classes = ['1', '2', '3', '4', '5', '6'];

foreach ($classes as $classKey) {
    $groupId = getGroupIdByClass($classKey, $timeSettings);
    if (empty($groupId) || empty($channel_access_token)) {
        $results[] = [
            'class' => $classKey,
            'status' => 'skip',
            'message' => 'ไม่ได้ตั้งค่า Group ID หรือ Channel Access Token'
        ];
        continue;
    }

    // ดึงข้อมูลนักเรียนทั้งหมดในระดับชั้น
    $students_all = $attendance->getStudentsWithAttendance($date, $classKey, null, $term, $pee);
    
    if (empty($students_all) || !is_array($students_all)) {
        $results[] = [
            'class' => $classKey,
            'groupId' => $groupId,
            'status' => 'no_students',
            'message' => 'ไม่มีนักเรียนในระดับชั้นนี้'
        ];
        continue;
    }

    // Group students by room
    $students_by_room = [];
    foreach ($students_all as $s) {
        $room = $s['Stu_room'];
        if (!isset($students_by_room[$room])) {
            $students_by_room[$room] = [];
        }
        $students_by_room[$room][] = $s;
    }
    ksort($students_by_room);

    $bubbles = [];
    foreach ($students_by_room as $roomKey => $room_students) {
        // ตรวจสอบว่าห้องนี้มีข้อมูลการเช็คชื่อหรือไม่
        $roomHasAttendance = false;
        foreach ($room_students as $srow) {
            if (!empty($srow['attendance_id']) || (isset($srow['attendance_status']) && $srow['attendance_status'] !== null && $srow['attendance_status'] !== '')) {
                $roomHasAttendance = true;
                break;
            }
        }

        if ($roomHasAttendance) {
            $summary = new AttendanceSummary($room_students, $classKey, $roomKey, $date, $term, $pee);
            $bubbles[] = $summary->getFlexMessage();
        } else {
            // Build a grey no-data flex bubble for this room
            $bubbles[] = [
                "type" => "bubble",
                "size" => "mega",
                "header" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "backgroundColor" => "#9ca3af",
                    "contents" => [[
                        "type" => "text",
                        "text" => "📭 ยังไม่มีข้อมูล",
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
                            "text" => "ชั้น ม." . $classKey . "/" . $roomKey,
                            "weight" => "bold",
                            "size" => "lg",
                            "color" => "#374151",
                            "align" => "center"
                        ],
                        [
                            "type" => "text",
                            "text" => thaiDateShort($date),
                            "size" => "sm",
                            "color" => "#6b7280",
                            "align" => "center"
                        ],
                        [
                            "type" => "separator",
                            "margin" => "md"
                        ],
                        [
                            "type" => "text",
                            "text" => "ยังไม่มีการบันทึกการมาเรียนสำหรับห้องนี้",
                            "size" => "sm",
                            "color" => "#6b7280",
                            "align" => "center",
                            "margin" => "lg",
                            "wrap" => true
                        ]
                    ]
                ],
                "footer" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "contents" => [[
                        "type" => "text",
                        "text" => "🔄 STD Care by PhichaiSchool",
                        "size" => "xs",
                        "color" => "#9ca3af",
                        "align" => "center"
                    ]]
                ]
            ];
        }
    }

    // LINE Flex Carousel message supports up to 10 bubbles
    $bubbleChunks = array_chunk($bubbles, 10);
    $sendResponses = [];
    foreach ($bubbleChunks as $chunkIndex => $chunkBubbles) {
        $carouselFlex = [
            "type" => "carousel",
            "contents" => $chunkBubbles
        ];

        // --- ส่ง Flex Message Carousel ไปยังกลุ่ม LINE (Messaging API) ---
        $sendRes = send_line_flex($channel_access_token, $groupId, $carouselFlex);
        $sendResponses[] = [
            'chunk' => $chunkIndex + 1,
            'response' => $sendRes,
            'flex' => $carouselFlex
        ];
    }

    $results[] = [
        'class' => $classKey,
        'groupId' => $groupId,
        'status' => 'ok',
        'line_flex_responses' => $sendResponses
    ];
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
