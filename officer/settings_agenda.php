<?php
/**
 * Controller: Meeting Agenda Management (Officer)
 * MVC Pattern - Handles authentication and prepares data for the meeting agenda view
 */
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Utils.php';

// (1) Check Permission
if (!isset($_SESSION['Officer_login'])) {
    header("Location: ../login.php");
    exit;
}

// (2) Initialize DB & Objects
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);

// (3) Fetch Core Context
$userid = $_SESSION['Officer_login'];
$userData = $user->userData($userid);
// (3) Fetch Core Context (Allow override via GET parameters for configuration rounds)
$currentTerm = $user->getTerm();
$currentPee = $user->getPee();

$term = isset($_GET['term']) ? trim($_GET['term']) : $currentTerm;
$pee = isset($_GET['pee']) ? trim($_GET['pee']) : $currentPee;

// (4) Fetch Current Dynamic Agenda Settings for this specific term/year
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

// Fetch all configured keys like agenda_settings_% to build round selector list
$configuredRounds = [];
try {
    $stmtRounds = $db->query("SELECT setting_key FROM time_settings WHERE setting_key LIKE 'agenda_settings_%'");
    $roundKeys = $stmtRounds->fetchAll(PDO::FETCH_COLUMN);
    foreach ($roundKeys as $key) {
        $parts = explode('_', $key);
        if (count($parts) >= 4) {
            $rTerm = $parts[2];
            $rPee = $parts[3];
            $configuredRounds[] = [
                'term' => $rTerm,
                'pee' => $rPee,
                'key' => "{$rTerm}_{$rPee}"
            ];
        }
    }
} catch (Exception $e) {
    // Silent fail
}

// Ensure the current active round and the requested round are in the list
$hasCurrentActive = false;
$hasRequested = false;
foreach ($configuredRounds as $r) {
    if ($r['term'] == $currentTerm && $r['pee'] == $currentPee) {
        $hasCurrentActive = true;
    }
    if ($r['term'] == $term && $r['pee'] == $pee) {
        $hasRequested = true;
    }
}
if (!$hasCurrentActive) {
    $configuredRounds[] = ['term' => $currentTerm, 'pee' => $currentPee, 'key' => "{$currentTerm}_{$currentPee}"];
}
if (!$hasRequested && ($term != $currentTerm || $pee != $currentPee)) {
    $configuredRounds[] = ['term' => $term, 'pee' => $pee, 'key' => "{$term}_{$pee}"];
}

// Sort rounds by year (desc) and term (desc)
usort($configuredRounds, function($a, $b) {
    if ($a['pee'] != $b['pee']) {
        return (int)$b['pee'] - (int)$a['pee'];
    }
    return (int)$b['term'] - (int)$a['term'];
});

// Fallback default agendas structure
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
    // Ensure settings exist if JSON was saved previously without them
    if (!isset($agendaConfig['show_committee_election'])) {
        $agendaConfig['show_committee_election'] = ($term == '1' || $term == 1);
    }
    if (!isset($agendaConfig['show_committee_page'])) {
        $agendaConfig['show_committee_page'] = ($term == '1' || $term == 1);
    }
}


// (5) Prepare Data for View
$pageTitle = 'ตั้งค่าวาระการประชุม';
$activePage = 'meeting_agenda';

// (6) Render View
include __DIR__ . '/../views/officer/settings_agenda.php';
?>
