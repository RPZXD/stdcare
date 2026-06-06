<?php
/**
 * API: Save Parent Meeting Minutes & Metadata
 * Saves dynamic agenda data as JSON and updates legacy columns in tb_picmeeting.
 */
require_once "../../config/Database.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    $class = $_POST['class'] ?? null;
    $room = $_POST['room'] ?? null;
    $term = $_POST['term'] ?? null;
    $pee = $_POST['pee'] ?? null;

    if (!$class || !$room || !$term || !$pee) {
        throw new Exception('ข้อมูลห้องเรียน ภาคเรียน หรือปีการศึกษาไม่ครบถ้วน');
    }

    // Check if record exists
    $check = $db->prepare("SELECT id FROM tb_picmeeting WHERE Stu_major = :class AND Stu_room = :room AND term = :term AND pee = :pee LIMIT 1");
    $check->execute([':class' => $class, ':room' => $room, ':term' => $term, ':pee' => $pee]);
    $exists = $check->fetchColumn() > 0;

    if ($exists) {
        // UPDATE: Build dynamic update fields
        $updateFields = [];
        $params = [
            ':class' => $class,
            ':room' => $room,
            ':term' => $term,
            ':pee' => $pee
        ];

        if (isset($_POST['meeting_date'])) {
            $updateFields[] = "meeting_date = :meeting_date";
            $params[':meeting_date'] = $_POST['meeting_date'];
        }
        if (isset($_POST['closing_time'])) {
            $updateFields[] = "closing_time = :closing_time";
            $params[':closing_time'] = $_POST['closing_time'];
        }
        if (isset($_POST['agenda_data'])) {
            $agenda_data_raw = $_POST['agenda_data'];
            $agenda_data_json = json_encode($agenda_data_raw, JSON_UNESCAPED_UNICODE);
            
            $updateFields[] = "agenda_data = :agenda_data";
            $params[':agenda_data'] = $agenda_data_json;

            // Map to legacy individual variables
            $legacyMapping = [
                'agenda1_1' => $agenda_data_raw[1][0] ?? null,
                'agenda1_2' => $agenda_data_raw[1][1] ?? null,
                'agenda1_3' => $agenda_data_raw[1][2] ?? null,
                'agenda1_4' => $agenda_data_raw[1][3] ?? null,
                'agenda2' => $agenda_data_raw[2][0] ?? null,
                'agenda3' => $agenda_data_raw[3][0] ?? null,
                'agenda4_1' => $agenda_data_raw[4][0] ?? null,
                'agenda4_2' => $agenda_data_raw[4][1] ?? null,
                'agenda5_1' => $agenda_data_raw[5][0] ?? null,
                'agenda5_2' => $agenda_data_raw[5][1] ?? null,
                'agenda5_other' => $agenda_data_raw[5][2] ?? null
            ];

            foreach ($legacyMapping as $col => $val) {
                $updateFields[] = "$col = :$col";
                $params[":$col"] = $val;
            }
        }

        if (empty($updateFields)) {
            echo json_encode(['success' => true, 'message' => 'ไม่มีข้อมูลสำหรับอัปเดต']);
            exit;
        }

        $sql = "UPDATE tb_picmeeting SET " . implode(", ", $updateFields) . " WHERE Stu_major = :class AND Stu_room = :room AND term = :term AND pee = :pee";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

    } else {
        // INSERT: insert all provided fields or their defaults
        $fields = ['Stu_major', 'Stu_room', 'term', 'pee'];
        $placeholders = [':class', ':room', ':term', ':pee'];
        $params = [
            ':class' => $class,
            ':room' => $room,
            ':term' => $term,
            ':pee' => $pee
        ];

        $fields[] = 'meeting_date';
        $placeholders[] = ':meeting_date';
        $params[':meeting_date'] = $_POST['meeting_date'] ?? null;

        $fields[] = 'closing_time';
        $placeholders[] = ':closing_time';
        $params[':closing_time'] = $_POST['closing_time'] ?? null;

        $agenda_data_raw = $_POST['agenda_data'] ?? [];
        $agenda_data_json = json_encode($agenda_data_raw, JSON_UNESCAPED_UNICODE);
        
        $fields[] = 'agenda_data';
        $placeholders[] = ':agenda_data';
        $params[':agenda_data'] = $agenda_data_json;

        $legacyMapping = [
            'agenda1_1' => $agenda_data_raw[1][0] ?? null,
            'agenda1_2' => $agenda_data_raw[1][1] ?? null,
            'agenda1_3' => $agenda_data_raw[1][2] ?? null,
            'agenda1_4' => $agenda_data_raw[1][3] ?? null,
            'agenda2' => $agenda_data_raw[2][0] ?? null,
            'agenda3' => $agenda_data_raw[3][0] ?? null,
            'agenda4_1' => $agenda_data_raw[4][0] ?? null,
            'agenda4_2' => $agenda_data_raw[4][1] ?? null,
            'agenda5_1' => $agenda_data_raw[5][0] ?? null,
            'agenda5_2' => $agenda_data_raw[5][1] ?? null,
            'agenda5_other' => $agenda_data_raw[5][2] ?? null
        ];

        foreach ($legacyMapping as $col => $val) {
            $fields[] = $col;
            $placeholders[] = ":$col";
            $params[":$col"] = $val;
        }

        $sql = "INSERT INTO tb_picmeeting (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลการประชุมสำเร็จ']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
