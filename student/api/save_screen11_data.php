<?php
/**
 * API: Save Screen11 Data
 */
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Student_login'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/Screeningdata.php');

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $screening = new ScreeningData($db);

    $student_id = $_POST['student_id'] ?? '';
    $pee = $_POST['pee'] ?? '';
    
    // Prepare fields
    $data = $_POST;
    
    // Convert array fields to JSON strings
    $arrayFields = [
        'study_risk', 'study_problem', 'health_risk', 'health_problem',
        'economic_risk', 'economic_problem', 'welfare_risk', 'welfare_problem',
        'drug_risk', 'drug_problem', 'violence_risk', 'violence_problem',
        'sex_risk', 'sex_problem', 'game_risk', 'game_problem',
        'it_risk', 'it_problem'
    ];

    foreach ($arrayFields as $field) {
        if (isset($data[$field]) && is_array($data[$field])) {
            $data[$field] = json_encode($data[$field], JSON_UNESCAPED_UNICODE);
        } else {
            $data[$field] = null;
        }
    }

    // Special Ability handling: Convert special_0 to special_7 into a JSON object
    $specialDetails = [];
    for ($i = 0; $i < 8; $i++) {
        $fieldName = "special_$i";
        if (isset($data[$fieldName]) && is_array($data[$fieldName])) {
            $specialDetails[$fieldName] = array_filter($data[$fieldName], fn($v) => !empty(trim($v)));
        }
    }
    $data['special_ability_detail'] = json_encode($specialDetails, JSON_UNESCAPED_UNICODE);

    // Save
    $result = $screening->insertScreening($data);
    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
