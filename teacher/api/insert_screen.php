<?php
header('Content-Type: application/json');
require_once '../../config/Database.php';
require_once '../../class/Screeningdata.php';

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $screening = new ScreeningData($db);

    // รับค่าจาก POST
    function postArr($key) {
        return isset($_POST[$key]) ? $_POST[$key] : [];
    }
    function postArrJson($key) {
        $val = postArr($key);
        return !empty($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : null;
    }

    // รายละเอียดความสามารถพิเศษ
    $special_ability_detail = postArrJson('special_ability_detail');
    // หรือถ้ารับเป็น array ของ array (special_0[], special_1[] ...) ให้รวมเป็น array แล้ว encode
    $special_ability_detail_arr = [];
    foreach ($_POST as $k => $v) {
        if (preg_match('/^special_\d+$/', $k) && is_array($v)) {
            $special_ability_detail_arr[$k] = array_filter($v, fn($x) => trim($x) !== '');
        }
    }
    if (!empty($special_ability_detail_arr)) {
        $special_ability_detail = json_encode($special_ability_detail_arr, JSON_UNESCAPED_UNICODE);
    }

    $data = [
        'student_id' => $_POST['student_id'] ?? '',
        'pee' => $_POST['pee'] ?? '',
        'special_ability' => $_POST['special_ability'] ?? '',
        'special_ability_detail' => $special_ability_detail,
        'study_status' => $_POST['study_status'] ?? '',
        'study_risk' => postArrJson('study_risk'),
        'study_problem' => postArrJson('study_problem'),
        'health_status' => $_POST['health_status'] ?? '',
        'health_risk' => postArrJson('health_risk'),
        'health_problem' => postArrJson('health_problem'),
        'economic_status' => $_POST['economic_status'] ?? '',
        'economic_risk' => postArrJson('economic_risk'),
        'economic_problem' => postArrJson('economic_problem'),
        'welfare_status' => $_POST['welfare_status'] ?? '',
        'welfare_risk' => postArrJson('welfare_risk'),
        'welfare_problem' => postArrJson('welfare_problem'),
        'drug_status' => $_POST['drug_status'] ?? '',
        'drug_risk' => postArrJson('drug_risk'),
        'drug_problem' => postArrJson('drug_problem'),
        'violence_status' => $_POST['violence_status'] ?? '',
        'violence_risk' => postArrJson('violence_risk'),
        'violence_problem' => postArrJson('violence_problem'),
        'sex_status' => $_POST['sex_status'] ?? '',
        'sex_risk' => postArrJson('sex_risk'),
        'sex_problem' => postArrJson('sex_problem'),
        'game_status' => $_POST['game_status'] ?? '',
        'game_risk' => postArrJson('game_risk'),
        'game_problem' => postArrJson('game_problem'),
        'special_need_status' => $_POST['special_need_status'] ?? '',
        'special_need_type' => $_POST['special_need_type'] ?? '',
        'it_status' => $_POST['it_status'] ?? '',
        'it_risk' => postArrJson('it_risk'),
        'it_problem' => postArrJson('it_problem')
    ];

    $result = $screening->insertScreening($data);
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
