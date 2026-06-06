<?php
/**
 * Print Parent Meeting Booklet Report (เล่มรายงานการประชุมผู้ปกครอง)
 * Dynamically aggregates minutes, activity photos, and parent network roster.
 * Supports contenteditable for on-screen editing before printing.
 */
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee (Allow override via GET parameters)
$currentTerm = $user->getTerm();
$currentPee = $user->getPee();

$term = isset($_GET['term']) ? trim($_GET['term']) : $currentTerm;
$pee = isset($_GET['pee']) ? trim($_GET['pee']) : $currentPee;

// Check login
if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    header("Location: ../login.php");
    exit;
}

$class = $userData['Teach_class'];
$room = $userData['Teach_room'];
$teacher_name = $userData['Teach_name'] ?? '';

// Fetch room teachers
$roomTeachers = $teacher->getTeachersByClassAndRoom($class, $room);

// Fetch Current Agenda Settings from JSON scoped by round
$agendaConfig = null;
try {
    $settingKey = "agenda_settings_{$term}_{$pee}";
    $stmtJson = $db->prepare("SELECT setting_value FROM time_settings WHERE setting_key = :key LIMIT 1");
    $stmtJson->execute([':key' => $settingKey]);
    $agendaJsonStr = $stmtJson->fetchColumn();
    if ($agendaJsonStr) {
        $agendaConfig = json_decode($agendaJsonStr, true);
    }
} catch (Exception $e) {
    $agendaConfig = null;
}

// Fallback default agendas structure if not set
$defaultAgendas = [
    1 => [
        'title' => 'ระเบียบวาระที่ 1 เรื่องที่ประธานแจ้งให้ทราบ',
        'subs' => [
            '1.1 ขอบคุณผู้ปกครองนักเรียนทุกคนที่ให้ความร่วมมือในการเข้าร่วมประชุมและการจัดการศึกษาและสนับสนุนการดำเนินงานของโรงเรียน',
            '1.2 กฎระเบียบ/ข้อตกลงของโรงเรียน',
            '1.3 การมาเรียน/การขาดเรียน/การลา/การมาสาย เกณฑ์การตัดคะแนนนักเรียนที่มีพฤติกรรมผิดระเบียบของโรงเรียน',
            '1.4 การดำเนินกิจกรรมต่าง ๆ ของโรงเรียน'
        ]
    ],
    2 => [
        'title' => 'ระเบียบวาระที่ 2 เรื่องรับรองรายงานการประชุม',
        'subs' => [
            'เรื่องรับรองรายงานการประชุม'
        ]
    ],
    3 => [
        'title' => 'ระเบียบวาระที่ 3 เรื่องสืบเนื่องจากการประชุมครั้งที่แล้ว',
        'subs' => [
            'เรื่องสืบเนื่องจากการประชุมครั้งที่แล้ว'
        ]
    ],
    4 => [
        'title' => 'ระเบียบวาระที่ 4 เรื่องเสนอเพื่อพิจารณา',
        'subs' => [
            '4.1 การคัดเลือกคณะกรรมการเครือข่ายผู้ปกครองระดับชั้นมัธยมศึกษาปีที่...',
            '4.2 แนวทางแก้ไขปัญหานักเรียนที่มีพฤติกรรมไม่เหมาะสม และกระทำผิดกฎระเบียบของโรงเรียน'
        ]
    ],
    5 => [
        'title' => 'ระเบียบวาระที่ 5 เรื่องอื่น ๆ',
        'subs' => [
            '5.1 เรื่องอื่น ๆ (ข้อที่ 1)',
            '5.2 เรื่องอื่น ๆ (ข้อที่ 2)',
            'บันทึกเพิ่มเติมวาระอื่น ๆ'
        ]
    ]
];

if (!$agendaConfig || !isset($agendaConfig['agendas'])) {
    $isTerm1 = ($term == '1' || $term == 1);
    $agendaConfig = [
        'show_committee_election' => $isTerm1,
        'show_committee_page' => $isTerm1,
        'agendas' => $defaultAgendas
    ];
} else {
    if (!isset($agendaConfig['show_committee_election'])) {
        $agendaConfig['show_committee_election'] = ($term == '1' || $term == 1);
    }
    if (!isset($agendaConfig['show_committee_page'])) {
        $agendaConfig['show_committee_page'] = ($term == '1' || $term == 1);
    }
}

// Map dynamic configs to the legacy array format for safety
$agendaTitles = [];
$agendaTitles['agenda1_1_title'] = $agendaConfig['agendas'][1]['subs'][0] ?? '';
$agendaTitles['agenda1_2_title'] = $agendaConfig['agendas'][1]['subs'][1] ?? '';
$agendaTitles['agenda1_3_title'] = $agendaConfig['agendas'][1]['subs'][2] ?? '';
$agendaTitles['agenda1_4_title'] = $agendaConfig['agendas'][1]['subs'][3] ?? '';
$agendaTitles['agenda2_title'] = $agendaConfig['agendas'][2]['subs'][0] ?? $agendaConfig['agendas'][2]['title'] ?? '';
$agendaTitles['agenda3_title'] = $agendaConfig['agendas'][3]['subs'][0] ?? $agendaConfig['agendas'][3]['title'] ?? '';
$agendaTitles['agenda4_1_title'] = $agendaConfig['agendas'][4]['subs'][0] ?? '';
$agendaTitles['agenda4_2_title'] = $agendaConfig['agendas'][4]['subs'][1] ?? '';
$agendaTitles['agenda5_1_title'] = $agendaConfig['agendas'][5]['subs'][0] ?? '';
$agendaTitles['agenda5_2_title'] = $agendaConfig['agendas'][5]['subs'][1] ?? '';
$agendaTitles['agenda5_other_title'] = $agendaConfig['agendas'][5]['subs'][2] ?? '';

// Fetch parent board network members (grouped by position)
$chairmen = [];
$members = [];
$secretaries = [];

try {
    $stmt = $db->prepare("SELECT * FROM tb_parnet WHERE parn_lev = :class AND parn_room = :room AND parn_pee = :pee ORDER BY parn_pos ASC");
    $stmt->execute([':class' => $class, ':room' => $room, ':pee' => $pee]);
    $boardData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($boardData as $row) {
        if ($row['parn_pos'] == 1) $chairmen[] = $row;
        if ($row['parn_pos'] == 2) $members[] = $row;
        if ($row['parn_pos'] == 3) $secretaries[] = $row;
    }
} catch (Exception $e) {
    // Silent fail
}

$chairmanNames = !empty($chairmen) ? implode(', ', array_filter(array_column($chairmen, 'parn_name'))) : '';
$memberNames = !empty($members) ? implode(', ', array_filter(array_column($members, 'parn_name'))) : '';
$secretaryNames = !empty($secretaries) ? implode(', ', array_filter(array_column($secretaries, 'parn_name'))) : '';

$chairmanNamesStr = !empty($chairmanNames) ? $chairmanNames : '......................................................................';
$memberNamesStr = !empty($memberNames) ? $memberNames : '......................................................................';
$secretaryNamesStr = !empty($secretaryNames) ? $secretaryNames : '......................................................................';

// Generate default Thai date parts
$thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
$thaiMonths = [
    '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน',
    '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม',
    '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
];

$dayOfWeekNum = date('w');
$dayOfWeekName = $thaiDays[$dayOfWeekNum];
$dayNum = date('j');
$monthNum = date('m');
$monthName = $thaiMonths[$monthNum];
$buddhistYear = date('Y') + 543;

// Fetch meeting details (Minutes & Date & Photos)
$meetingData = null;
$photos = [];
try {
    $stmt = $db->prepare("SELECT * FROM tb_picmeeting WHERE Stu_major = :class AND Stu_room = :room AND term = :term AND pee = :pee LIMIT 1");
    $stmt->execute([':class' => $class, ':room' => $room, ':term' => $term, ':pee' => $pee]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $meetingData = $row;
        for ($i = 1; $i <= 4; $i++) {
            $col = 'picture' . $i;
            if (!empty($row[$col])) {
                $photos[$i] = "../teacher/uploads/picmeeting" . $term . $pee . "/" . $row[$col];
            }
        }
    }
} catch (Exception $e) {
    // Silent fail
}

// Fallback logic for date
$meetingDateStr = !empty($meetingData['meeting_date']) 
    ? $meetingData['meeting_date'] 
    : "วันที่ " . $dayNum . " " . $monthName . " พ.ศ. " . $buddhistYear;

// Date for photo page
$meetingDatePhotoStr = !empty($meetingData['meeting_date']) 
    ? $meetingData['meeting_date'] 
    : "วัน" . $dayOfWeekName . " ที่ " . $dayNum . " เดือน " . $monthName . " พ.ศ. " . $buddhistYear;

// Fallback logic for closing time
$closingTimeStr = !empty($meetingData['closing_time']) ? $meetingData['closing_time'] : "....................";

// Parse dynamic agenda_data from db row
$dynamicAgendaData = [];
if (!empty($meetingData['agenda_data'])) {
    $dynamicAgendaData = json_decode($meetingData['agenda_data'], true);
}

// Fallback to legacy fields if agenda_data is empty
if (empty($dynamicAgendaData)) {
    $dynamicAgendaData = [
        1 => [
            $meetingData['agenda1_1'] ?? null,
            $meetingData['agenda1_2'] ?? null,
            $meetingData['agenda1_3'] ?? null,
            $meetingData['agenda1_4'] ?? null
        ],
        2 => [
            $meetingData['agenda2'] ?? null
        ],
        3 => [
            $meetingData['agenda3'] ?? null
        ],
        4 => [
            $meetingData['agenda4_1'] ?? null,
            $meetingData['agenda4_2'] ?? null
        ],
        5 => [
            $meetingData['agenda5_1'] ?? null,
            $meetingData['agenda5_2'] ?? null,
            $meetingData['agenda5_other'] ?? null
        ]
    ];
}

// Format dynamic notes with dotted line fallbacks
function formatAgendaNotes($notes, $defaultLinesCount = 2) {
    $dotsLine = "..........................................................................................................................................................................";
    if (!empty($notes)) {
        return $notes;
    }
    
    $res = "บันทึกกิจกรรมการประชุม" . $dotsLine;
    for ($i = 1; $i < $defaultLinesCount; $i++) {
        $res .= "\n" . $dotsLine;
    }
    return $res;
}

// Helper functions for Page 4 dynamic parent committee table
function renderCommitteeRow($idx, $row, $positionName) {
    $name = !empty($row['parn_name']) ? $row['parn_name'] : '.....................................................................................';
    $addr = !empty($row['parn_addr']) ? $row['parn_addr'] : '................................................................................................................';
    $tel = !empty($row['parn_tel']) ? $row['parn_tel'] : '.....................................................................................';
    
    $photoPath = "../dist/img/user-placeholder.png";
    if (!empty($row['parn_photo']) && file_exists("uploads/photopar/" . $row['parn_photo'])) {
        $photoPath = "uploads/photopar/" . $row['parn_photo'];
    }
    ?>
    <tr>
        <td class="center" style="font-weight: bold; font-size: 15px;"><?= $idx; ?>.</td>
        <td style="line-height: 2;">
            ชื่อ-สกุล: <span style="font-weight: bold;" contenteditable="true"><?= htmlspecialchars($name); ?></span><br>
            ที่อยู่: <span contenteditable="true"><?= htmlspecialchars($addr); ?></span><br>
            เบอร์โทรศัพท์: <span contenteditable="true"><?= htmlspecialchars($tel); ?></span>
        </td>
        <td style="vertical-align: middle; text-align: center;">
            <img src="<?= htmlspecialchars($photoPath); ?>" style="width: 90px; height: 120px; object-fit: cover; border-radius: 8px; border: 1.5px solid #000; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        </td>
        <td class="center" style="font-weight: bold; font-size: 15px; vertical-align: middle;"><?= $positionName; ?></td>
    </tr>
    <?php
}

function renderCommitteeRowFallback($idx, $positionName) {
    ?>
    <tr>
        <td class="center" style="font-weight: bold; font-size: 15px;"><?= $idx; ?>.</td>
        <td style="line-height: 2;">
            ชื่อ-สกุล: <span style="font-weight: bold;" contenteditable="true">.....................................................................................</span><br>
            ที่อยู่: <span contenteditable="true">................................................................................................................</span><br>
            เบอร์โทรศัพท์: <span contenteditable="true">.....................................................................................</span>
        </td>
        <td style="vertical-align: middle; text-align: center;">
            <img src="../dist/img/user-placeholder.png" style="width: 90px; height: 120px; object-fit: cover; border-radius: 8px; border: 1.5px dashed #aaa; opacity: 0.5;">
        </td>
        <td class="center" style="font-weight: bold; font-size: 15px; vertical-align: middle;"><?= $positionName; ?></td>
    </tr>
    <?php
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เล่มรายงานการประชุมผู้ปกครอง ม.<?= $class; ?>/<?= $room; ?> ภาคเรียนที่ <?= $term; ?>/<?= $pee; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            color: #000;
        }

        /* Float Control Panel style */
        .control-panel {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            z-index: 9999;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .control-panel h4 {
            margin-top: 0;
            color: #1e293b;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .control-panel p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
            transition: all 0.2s;
        }

        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -1px rgba(16, 185, 129, 0.3);
        }

        .btn-close {
            display: block;
            width: 100%;
            margin-top: 8px;
            padding: 8px;
            background: #e2e8f0;
            color: #475569;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
            text-align: center;
            transition: background 0.2s;
        }

        .btn-close:hover {
            background: #cbd5e1;
        }

        /* A4 Page Layout */
        .page {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm 20mm 20mm 20mm;
            box-sizing: border-box;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            position: relative;
            font-size: 15px;
            line-height: 1.6;
        }

        /* Page Breaks */
        .page-break {
            page-break-after: always;
            break-after: page;
        }

        /* Thai School Document Header style */
        .doc-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .doc-header img {
            width: 65px;
            height: 65px;
            margin-bottom: 10px;
        }

        .doc-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .doc-subtitle {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .doc-divider {
            border: 0;
            border-top: 1.5px solid #000;
            margin-bottom: 20px;
        }

        /* Agenda styles */
        .agenda-title {
            font-weight: bold;
            font-size: 16px;
            margin-top: 15px;
            margin-bottom: 6px;
        }

        .agenda-sub {
            padding-left: 20px;
            margin-bottom: 10px;
        }

        .agenda-sub p {
            margin: 4px 0;
            font-weight: 500;
        }

        /* Dotted note fields for writing or typing */
        .dotted-notes {
            min-height: 48px;
            line-height: 1.8;
            outline: none;
            word-break: break-word;
            white-space: pre-wrap;
            position: relative;
        }

        /* Signature layouts */
        .signatures-grid {
            display: grid;
            grid-cols: 2;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .signature-block {
            text-align: center;
            font-size: 14px;
            line-height: 2;
        }

        /* Grid for Page 3 Photos */
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .photo-frame {
            border: 1px solid #000;
            aspect-ratio: 4/3;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            object-cover: cover;
            object-fit: cover;
        }

        .photo-frame .placeholder-text {
            color: #777;
            font-style: italic;
            font-size: 13px;
        }

        /* Page 4 Table style */
        .committee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .committee-table th, .committee-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: top;
        }

        .committee-table th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
        }

        .committee-table td.center {
            text-align: center;
        }

        /* In-browser editable indicator style */
        [contenteditable="true"] {
            cursor: text;
            transition: background 0.2s;
        }
        
        [contenteditable="true"]:hover {
            background: #f8fafc;
        }

        [contenteditable="true"]:focus {
            background: #f0fdf4;
            box-shadow: 0 0 0 1px #10b981;
        }

        /* Print styles overrides */
        @media print {
            body {
                background-color: #fff !important;
                color: #000 !important;
            }
            .no-print {
                display: none !important;
            }
            .page {
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                width: 210mm !important;
                height: 297mm !important;
                padding: 15mm 15mm 15mm 15mm !important;
                box-sizing: border-box !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            [contenteditable="true"] {
                background: transparent !important;
                outline: none !important;
                border-bottom: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Control Panel -->
    <div class="control-panel no-print">
        <h4><i class="fas fa-print text-emerald-500"></i> สั่งพิมพ์เล่มรายงาน</h4>
        <p>คุณสามารถคลิกในช่องข้อความต่าง ๆ เพื่อแก้ไขรายละเอียด ดำเนินการ และกดสั่งพิมพ์เพื่อดาวน์โหลดเอกสาร</p>
        <button class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> พิมพ์เอกสาร</button>
        <button class="btn-close" onclick="window.close()"><i class="fa fa-times"></i> ปิดหน้านี้</button>
    </div>

    <!-- ================= PAGE 1 ================= -->
    <div class="page page-break">
        <div class="doc-header">
            <img src="../dist/img/logo-phicha.png" alt="Phichai Logo">
            <div class="doc-title">บันทึกการประชุมตามระเบียบวาระการประชุมผู้ปกครองนักเรียนครั้งที่ <span contenteditable="true"><?= $term; ?>/<?= $pee; ?></span></div>
            <div class="doc-subtitle">ชั้นมัธยมศึกษาปีที่ <span contenteditable="true"><?= $class; ?>/<?= $room; ?></span></div>
            <div class="doc-subtitle"><span contenteditable="true"><?= $meetingDateStr; ?></span></div>
        </div>

        <hr class="doc-divider">

        <div class="agenda-title"><?= htmlspecialchars($agendaConfig['agendas'][1]['title']); ?></div>
        <?php foreach ($agendaConfig['agendas'][1]['subs'] as $idx => $subTitle): 
            $val = $dynamicAgendaData[1][$idx] ?? '';
            $notesFormatted = formatAgendaNotes($val, 3);
        ?>
            <div class="agenda-sub">
                <p><?= htmlspecialchars($subTitle); ?></p>
                <div class="dotted-notes" contenteditable="true"><?= htmlspecialchars($notesFormatted); ?></div>
            </div>
        <?php endforeach; ?>

        <div class="agenda-title" style="margin-top: 30px;"><?= htmlspecialchars($agendaConfig['agendas'][2]['title']); ?></div>
        <?php 
            $val2 = $dynamicAgendaData[2][0] ?? '';
            $notes2Formatted = formatAgendaNotes($val2, 2);
        ?>
        <div class="dotted-notes" style="padding-left: 20px;" contenteditable="true"><?= htmlspecialchars($notes2Formatted); ?></div>
    </div>

    <!-- ================= PAGE 2 ================= -->
    <div class="page page-break">
        <div class="agenda-title"><?= htmlspecialchars($agendaConfig['agendas'][3]['title']); ?></div>
        <?php 
            $val3 = $dynamicAgendaData[3][0] ?? '';
            $notes3Formatted = formatAgendaNotes($val3, 2);
        ?>
        <div class="dotted-notes" style="padding-left: 20px; margin-bottom: 30px;" contenteditable="true"><?= htmlspecialchars($notes3Formatted); ?></div>

        <div class="agenda-title"><?= htmlspecialchars($agendaConfig['agendas'][4]['title']); ?></div>
        <?php foreach ($agendaConfig['agendas'][4]['subs'] as $idx => $subTitle): 
            $isCommitteeSub = ($idx === 0 || mb_strpos($subTitle, 'คณะกรรมการเครือข่าย') !== false || mb_strpos($subTitle, 'คัดเลือกคณะกรรมการ') !== false);
            
            if ($isCommitteeSub && !($agendaConfig['show_committee_election'] ?? true)) {
                // Skip rendering committee sub if disabled
                continue;
            }
            
            $val = $dynamicAgendaData[4][$idx] ?? '';
            $notesFormatted = formatAgendaNotes($val, 2);
        ?>
            <div class="agenda-sub" style="margin-top: 15px;">
                <p><?= htmlspecialchars($subTitle); ?> <?php if ($isCommitteeSub) { echo 'ระดับชั้นมัธยมศึกษาปีที่ <span contenteditable="true">' . $class . '/' . $room . '</span>'; } ?></p>
                
                <?php if ($isCommitteeSub): ?>
                    <div style="padding-left: 20px; line-height: 2;">
                        ประกอบด้วย &nbsp;&nbsp;&nbsp; ประธาน &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="border-bottom: 1px dotted #000; min-width: 300px; display: inline-block;" contenteditable="true"><?= htmlspecialchars($chairmanNamesStr); ?></span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; กรรมการ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="border-bottom: 1px dotted #000; min-width: 300px; display: inline-block;" contenteditable="true"><?= htmlspecialchars($memberNamesStr); ?></span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; เลขานุการ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="border-bottom: 1px dotted #000; min-width: 300px; display: inline-block;" contenteditable="true"><?= htmlspecialchars($secretaryNamesStr); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="dotted-notes mt-2" contenteditable="true"><?= htmlspecialchars($notesFormatted); ?></div>
            </div>
        <?php endforeach; ?>

        <div class="agenda-title" style="margin-top: 30px;"><?= htmlspecialchars($agendaConfig['agendas'][5]['title']); ?></div>
        <div class="agenda-sub">
            <?php foreach ($agendaConfig['agendas'][5]['subs'] as $idx => $subTitle): 
                $val = $dynamicAgendaData[5][$idx] ?? '';
                $notesFormatted = formatAgendaNotes($val, 2);
                $prefix = '5.' . ($idx + 1);
            ?>
                <div style="margin-top: 10px;">
                    <p style="font-weight: bold; margin-bottom: 4px;"><?= $prefix ?> <?= htmlspecialchars($subTitle); ?></p>
                    <div class="dotted-notes" contenteditable="true"><?= htmlspecialchars($notesFormatted); ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 40px; font-weight: bold;">
            ปิดประชุม เวลา <span contenteditable="true"><?= htmlspecialchars($closingTimeStr); ?></span> น.
        </div>

        <div class="signatures-grid" style="margin-top: 80px;">
            <?php foreach ($roomTeachers as $t): ?>
            <div class="signature-block">
                <p>ลงชื่อ............................................................ผู้บันทึกการประชุม/ครูที่ปรึกษา</p>
                <p>( <span contenteditable="true"><?= htmlspecialchars($t['Teach_name']); ?></span> )</p>
            </div>
            <?php endforeach; ?>
            <?php if (empty($roomTeachers)): ?>
            <div class="signature-block">
                <p>ลงชื่อ............................................................ผู้บันทึกการประชุม/ครูที่ปรึกษา</p>
                <p>( <span contenteditable="true"><?= htmlspecialchars($teacher_name); ?></span> )</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ================= PAGE 3 ================= -->
    <div class="page page-break">
        <div class="doc-header">
            <img src="../dist/img/logo-phicha.png" alt="Phichai Logo">
            <div class="doc-title">ภาพกิจกรรมการประชุมผู้ปกครอง ระดับชั้นมัธยมศึกษาปีที่ <span contenteditable="true"><?= $class; ?>/<?= $room; ?></span></div>
            <div class="doc-subtitle">ภาคเรียนที่ <span contenteditable="true"><?= $term; ?></span> ปีการศึกษา <span contenteditable="true"><?= $pee; ?></span></div>
            <div class="doc-subtitle"><span contenteditable="true"><?= $meetingDatePhotoStr; ?></span></div>
            <div class="doc-subtitle">โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์</div>
        </div>

        <!-- Photo layout grid -->
        <div class="photo-grid">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="photo-frame">
                <?php if (isset($photos[$i])): ?>
                <img src="<?= htmlspecialchars($photos[$i]); ?>" alt="รูปภาพการประชุม <?= $i; ?>">
                <?php else: ?>
                <div class="placeholder-text">ภาพกิจกรรมการประชุมผู้ปกครอง รูปที่ <?= $i; ?></div>
                <?php endif; ?>
            </div>
            <?php endfor; ?>
        </div>

        <div class="signatures-grid" style="margin-top: 100px;">
            <?php foreach ($roomTeachers as $t): ?>
            <div class="signature-block">
                <p>ลงชื่อ............................................................ครูที่ปรึกษา</p>
                <p>( <span contenteditable="true"><?= htmlspecialchars($t['Teach_name']); ?></span> )</p>
            </div>
            <?php endforeach; ?>
            <?php if (empty($roomTeachers)): ?>
            <div class="signature-block">
                <p>ลงชื่อ............................................................ครูที่ปรึกษา</p>
                <p>( <span contenteditable="true"><?= htmlspecialchars($teacher_name); ?></span> )</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ================= PAGE 4 ================= -->
    <?php if ($agendaConfig['show_committee_page'] ?? true): ?>
        <div class="page">
            <div class="doc-header" style="margin-bottom: 40px;">
                <div class="doc-title" style="font-size: 20px;">รายชื่อคณะกรรมการเครือข่ายผู้ปกครองในชั้นเรียน</div>
                <div class="doc-subtitle" style="font-size: 18px;">ชั้นมัธยมศึกษาปีที่ <span contenteditable="true"><?= $class; ?>/<?= $room; ?></span></div>
                <div class="doc-subtitle" style="font-size: 16px;">ปีการศึกษา <span contenteditable="true"><?= $pee; ?></span></div>
            </div>

            <!-- Roster table matching design -->
            <table class="committee-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">ลำดับที่</th>
                        <th style="width: 50%;">ชื่อ - นามสกุล/ที่อยู่/เบอร์โทรศัพท์ (ตัวบรรจง)</th>
                        <th style="width: 25%;">รูปถ่าย</th>
                        <th style="width: 15%;">ตำแหน่ง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $index = 1;
                    // Render Chairmen
                    if (!empty($chairmen)) {
                        foreach ($chairmen as $c) {
                            renderCommitteeRow($index++, $c, 'ประธาน');
                        }
                    } else {
                        renderCommitteeRowFallback($index++, 'ประธาน');
                    }

                    // Render Members
                    if (!empty($members)) {
                        foreach ($members as $m) {
                            renderCommitteeRow($index++, $m, 'กรรมการ');
                        }
                    } else {
                        renderCommitteeRowFallback($index++, 'กรรมการ');
                    }

                    // Render Secretaries
                    if (!empty($secretaries)) {
                        foreach ($secretaries as $s) {
                            renderCommitteeRow($index++, $s, 'เลขานุการ');
                        }
                    } else {
                        renderCommitteeRowFallback($index++, 'เลขานุการ');
                    }
                    ?>
                </tbody>
            </table>

            <div class="signatures-grid" style="margin-top: 80px;">
                <?php foreach ($roomTeachers as $t): ?>
                <div class="signature-block">
                    <p>ลงชื่อ............................................................ครูที่ปรึกษา</p>
                    <p>( <span contenteditable="true"><?= htmlspecialchars($t['Teach_name']); ?></span> )</p>
                </div>
                <?php endforeach; ?>
                <?php if (empty($roomTeachers)): ?>
                <div class="signature-block">
                    <p>ลงชื่อ............................................................ครูที่ปรึกษา</p>
                    <p>( <span contenteditable="true"><?= htmlspecialchars($teacher_name); ?></span> )</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>
