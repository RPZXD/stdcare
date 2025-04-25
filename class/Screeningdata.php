<?php
class ScreeningData {
    private $db;
    private $stu_major;
    private $stu_room;
    private $pee;
    private $term;

    public function __construct($db, $stu_major = null, $stu_room = null, $pee = null, $term = null) {
        $this->db = $db;
        $this->stu_major = $stu_major;
        $this->stu_room = $stu_room;
        $this->pee = $pee;
        $this->term = $term;
    }

    public function getScreenByClassAndRoom($class, $room, $pee) {
        $query = "
            SELECT
                CONCAT(st.Stu_pre, st.Stu_name, '  ', st.Stu_sur) AS full_name,
                st.Stu_id,
                st.Stu_no,
                st.Stu_picture,

                CASE
                    WHEN ss.student_id IS NOT NULL AND ss.pee = :pee THEN 1
                    ELSE 0
                END AS screen_ishave

            FROM
                student AS st

            LEFT JOIN student_screening AS ss ON ss.student_id = st.Stu_id AND ss.pee = :pee

            WHERE
                st.Stu_major = :class
                AND st.Stu_room = :room
                AND st.Stu_status = 1

            ORDER BY
                st.Stu_no ASC;
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':room', $room);
        $stmt->bindParam(':pee', $pee);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertScreening($data) {
        // ตรวจสอบข้อมูลที่จำเป็น
        if (
            empty($data['student_id']) ||
            empty($data['pee']) ||
            empty($data['special_ability'])
        ) {
            return ['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน'];
        }

        $sql = "INSERT INTO student_screening (
            student_id, pee, created_at,
            special_ability, special_ability_detail,
            study_status, study_risk, study_problem,
            health_status, health_risk, health_problem,
            economic_status, economic_risk, economic_problem,
            welfare_status, welfare_risk, welfare_problem,
            drug_status, drug_risk, drug_problem,
            violence_status, violence_risk, violence_problem,
            sex_status, sex_risk, sex_problem,
            game_status, game_risk, game_problem,
            special_need_status, special_need_type,
            it_status, it_risk, it_problem
        ) VALUES (
            :student_id, :pee, NOW(),
            :special_ability, :special_ability_detail,
            :study_status, :study_risk, :study_problem,
            :health_status, :health_risk, :health_problem,
            :economic_status, :economic_risk, :economic_problem,
            :welfare_status, :welfare_risk, :welfare_problem,
            :drug_status, :drug_risk, :drug_problem,
            :violence_status, :violence_risk, :violence_problem,
            :sex_status, :sex_risk, :sex_problem,
            :game_status, :game_risk, :game_problem,
            :special_need_status, :special_need_type,
            :it_status, :it_risk, :it_problem
        )";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $data['student_id']);
        $stmt->bindParam(':pee', $data['pee']);
        $stmt->bindParam(':special_ability', $data['special_ability']);
        $stmt->bindParam(':special_ability_detail', $data['special_ability_detail']);
        $stmt->bindParam(':study_status', $data['study_status']);
        $stmt->bindParam(':study_risk', $data['study_risk']);
        $stmt->bindParam(':study_problem', $data['study_problem']);
        $stmt->bindParam(':health_status', $data['health_status']);
        $stmt->bindParam(':health_risk', $data['health_risk']);
        $stmt->bindParam(':health_problem', $data['health_problem']);
        $stmt->bindParam(':economic_status', $data['economic_status']);
        $stmt->bindParam(':economic_risk', $data['economic_risk']);
        $stmt->bindParam(':economic_problem', $data['economic_problem']);
        $stmt->bindParam(':welfare_status', $data['welfare_status']);
        $stmt->bindParam(':welfare_risk', $data['welfare_risk']);
        $stmt->bindParam(':welfare_problem', $data['welfare_problem']);
        $stmt->bindParam(':drug_status', $data['drug_status']);
        $stmt->bindParam(':drug_risk', $data['drug_risk']);
        $stmt->bindParam(':drug_problem', $data['drug_problem']);
        $stmt->bindParam(':violence_status', $data['violence_status']);
        $stmt->bindParam(':violence_risk', $data['violence_risk']);
        $stmt->bindParam(':violence_problem', $data['violence_problem']);
        $stmt->bindParam(':sex_status', $data['sex_status']);
        $stmt->bindParam(':sex_risk', $data['sex_risk']);
        $stmt->bindParam(':sex_problem', $data['sex_problem']);
        $stmt->bindParam(':game_status', $data['game_status']);
        $stmt->bindParam(':game_risk', $data['game_risk']);
        $stmt->bindParam(':game_problem', $data['game_problem']);
        $stmt->bindParam(':special_need_status', $data['special_need_status']);
        $stmt->bindParam(':special_need_type', $data['special_need_type']);
        $stmt->bindParam(':it_status', $data['it_status']);
        $stmt->bindParam(':it_risk', $data['it_risk']);
        $stmt->bindParam(':it_problem', $data['it_problem']);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ'];
        } else {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'];
        }
    }

    public function getScreeningDataByStudentId($student_id, $pee = null) {
        $sql = "SELECT * FROM student_screening WHERE student_id = :student_id";
        if ($pee !== null) {
            $sql .= " AND pee = :pee";
        }
        $sql .= " ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        if ($pee !== null) {
            $stmt->bindParam(':pee', $pee);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return [];

        // แปลงข้อมูลที่เป็น JSON หรือ array กลับเป็น array
        $arrayFields = [
            'special_ability_detail', 'study_risk', 'study_problem', 'health_risk', 'health_problem',
            'economic_risk', 'economic_problem', 'welfare_risk', 'welfare_problem',
            'drug_risk', 'drug_problem', 'violence_risk', 'violence_problem',
            'sex_risk', 'sex_problem', 'game_risk', 'game_problem',
            'it_risk', 'it_problem'
        ];
        foreach ($arrayFields as $field) {
            if (isset($row[$field])) {
                $val = $row[$field];
                if (is_string($val) && ($val[0] === '[' || $val[0] === '{')) {
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $row[$field] = $decoded;
                    }
                } elseif (strpos($val, ',') !== false) {
                    $row[$field] = array_map('trim', explode(',', $val));
                }
            }
        }
        return $row;
    }

    public function updateScreening($data, $fields) {
        if (
            empty($data['student_id']) ||
            empty($data['pee']) ||
            empty($data['special_ability'])
        ) {
            return ['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน'];
        }

        // ตรวจสอบว่ามี record เดิมหรือไม่
        $sqlCheck = "SELECT id FROM student_screening WHERE student_id = :student_id AND pee = :pee";
        $sqlCheck .= " ORDER BY created_at DESC LIMIT 1";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bindParam(':student_id', $data['student_id']);
        $stmtCheck->bindParam(':pee', $data['pee']);
        $stmtCheck->execute();
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        // ระบุเฉพาะฟิลด์ที่มีจริงในฐานข้อมูล student_screening (เพิ่มฟิลด์ detail ให้ครบ)
        $dbFields = [
            'student_id', 'pee',
            'special_ability', 'special_ability_detail',
            'study_status', 'study_risk', 'study_problem',
            'health_status', 'health_risk', 'health_problem',
            'economic_status', 'economic_risk', 'economic_problem',
            'welfare_status', 'welfare_risk', 'welfare_problem',
            'drug_status', 'drug_risk', 'drug_problem',
            'violence_status', 'violence_risk', 'violence_problem',
            'sex_status', 'sex_risk', 'sex_problem',
            'game_status', 'game_risk', 'game_problem',
            'special_need_status', 'special_need_type',
            'it_status', 'it_risk', 'it_problem'
        ];

        // กรอง $fields ให้เหลือเฉพาะฟิลด์ที่มีจริง
        $fields = array_values(array_intersect($fields, $dbFields));

        // เตรียมข้อมูลสำหรับ update
        $set = [];
        foreach ($fields as $field) {
            if ($field === 'student_id' || $field === 'pee') continue;
            $set[] = "$field = :$field";
        }
        $setStr = implode(', ', $set);

        if ($row) {
            // UPDATE
            $sql = "UPDATE student_screening SET $setStr WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            foreach ($fields as $field) {
                if ($field === 'student_id' || $field === 'pee') continue;
                $stmt->bindValue(":$field", $data[$field] ?? null);
            }
            $stmt->bindValue(':id', $row['id']);
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'แก้ไขข้อมูลสำเร็จ'];
            } else {
                return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล'];
            }
        } else {
            // INSERT (กรณีไม่มีข้อมูลเดิม)
            $insertFields = $fields;
            $insertFields[] = 'created_at';
            $insertPlaceholders = array_map(fn($f) => ":$f", $insertFields);
            $insertPlaceholders[count($insertPlaceholders)-1] = 'NOW()';
            $sql = "INSERT INTO student_screening (" . implode(',', $insertFields) . ") VALUES (" . implode(',', $insertPlaceholders) . ")";
            $stmt = $this->db->prepare($sql);
            foreach ($fields as $field) {
                $stmt->bindValue(":$field", $data[$field] ?? null);
            }
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'บันทึกข้อมูลใหม่สำเร็จ'];
            } else {
                return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'];
            }
        }
    }
}
