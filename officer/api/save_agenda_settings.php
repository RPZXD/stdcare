<?php
/**
 * API: Save Meeting Agenda Settings (Officer)
 * Saves dynamic agenda configurations scoped per term and year.
 */
session_start();
require_once "../../config/Database.php";

header('Content-Type: application/json');

// Check permission
if (!isset($_SESSION['Officer_login'])) {
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ในการดำเนินการนี้']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    $term = $_POST['term'] ?? null;
    $pee = $_POST['pee'] ?? null;

    if (!$term || !$pee) {
        throw new Exception('กรุณาระบุภาคเรียนและปีการศึกษา');
    }

    // 1. Construct the agenda configuration JSON
    $agendas = [];
    for ($i = 1; $i <= 5; $i++) {
        $subs = [];
        if (isset($_POST['agenda'][$i]) && is_array($_POST['agenda'][$i])) {
            foreach ($_POST['agenda'][$i] as $subVal) {
                $trimmed = trim($subVal);
                if ($trimmed !== '') {
                    $subs[] = $trimmed;
                }
            }
        }
        
        $agendas[$i] = [
            'title' => trim($_POST['agenda_titles'][$i] ?? ('ระเบียบวาระที่ ' . $i)),
            'subs' => $subs
        ];
    }

    $agendaConfig = [
        'term' => $term,
        'pee' => $pee,
        'meeting_date' => trim($_POST['meeting_date'] ?? ''),
        'show_committee_election' => isset($_POST['show_committee_election']),
        'show_committee_page' => isset($_POST['show_committee_page']),
        'agendas' => $agendas
    ];

    $meetingAgendaJson = json_encode($agendaConfig, JSON_UNESCAPED_UNICODE);

    $db->beginTransaction();

    // Save dynamic JSON setting scoped by term and year
    $settingKey = "agenda_settings_{$term}_{$pee}";
    $stmtJson = $db->prepare("
        INSERT INTO time_settings (setting_key, setting_value) 
        VALUES (:key, :value) 
        ON DUPLICATE KEY UPDATE setting_value = :value
    ");
    $stmtJson->execute([':key' => $settingKey, ':value' => $meetingAgendaJson]);

    // Also update legacy keys for the current configuration to maintain backward compatibility for old scripts
    $legacyMapping = [
        'agenda1_1_title' => $agendas[1]['subs'][0] ?? '',
        'agenda1_2_title' => $agendas[1]['subs'][1] ?? '',
        'agenda1_3_title' => $agendas[1]['subs'][2] ?? '',
        'agenda1_4_title' => $agendas[1]['subs'][3] ?? '',
        'agenda2_title' => $agendas[2]['subs'][0] ?? $agendas[2]['title'],
        'agenda3_title' => $agendas[3]['subs'][0] ?? $agendas[3]['title'],
        'agenda4_1_title' => $agendas[4]['subs'][0] ?? '',
        'agenda4_2_title' => $agendas[4]['subs'][1] ?? '',
        'agenda5_1_title' => $agendas[5]['subs'][0] ?? '',
        'agenda5_2_title' => $agendas[5]['subs'][1] ?? '',
        'agenda5_other_title' => $agendas[5]['subs'][2] ?? ''
    ];

    $stmtLegacy = $db->prepare("
        INSERT INTO time_settings (setting_key, setting_value) 
        VALUES (:key, :value) 
        ON DUPLICATE KEY UPDATE setting_value = :value
    ");

    foreach ($legacyMapping as $key => $val) {
        $stmtLegacy->execute([':key' => $key, ':value' => $val]);
    }

    $db->commit();

    echo json_encode(['success' => true, 'message' => 'บันทึกการตั้งค่าวาระการประชุมสำเร็จ']);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
