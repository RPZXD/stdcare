<?php
namespace App\Models;

class BehaviorModel
{
    private $db;
    private $pdo;

    /**
     * @param \App\DatabaseUsers $db การเชื่อมต่อฐานข้อมูล
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->pdo = $db->getPDO();
    }

    /**
     * คืนค่าสรุปคะแนนพฤติกรรมของนักเรียนในชั้น/ห้อง (รวมคะแนน)
     * ใช้สำหรับหน้าแสดงผลของครูที่ปรึกษา
     */
    public function getBehaviorSummaryByClass($class, $room, $term, $pee)
    {
        $sql = "SELECT s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_picture, s.Stu_no,
                       COALESCE(SUM(CASE WHEN b.behavior_type NOT IN ('ความดี', 'จิตอาสาช่วยเหลือครู', 'ช่วยเหลือเพื่อน', 'เก็บของได้ส่งคืน', 'บำเพ็ญประโยชน์') THEN b.behavior_score ELSE 0 END), 0) AS total_behavior_score,
                       COALESCE(SUM(CASE WHEN b.behavior_type IN ('ความดี', 'จิตอาสาช่วยเหลือครู', 'ช่วยเหลือเพื่อน', 'เก็บของได้ส่งคืน', 'บำเพ็ญประโยชน์') THEN b.behavior_score ELSE 0 END), 0) AS behavior_bonus,
                       GROUP_CONCAT(DISTINCT CONCAT(t.Teach_name) SEPARATOR ', ') AS teacher_names
                FROM student s
                LEFT JOIN behavior b ON s.Stu_id = b.stu_id AND b.behavior_term = :term AND b.behavior_pee = :pee
                LEFT JOIN teacher t ON b.teach_id = t.Teach_id
                WHERE s.Stu_status = '1' AND s.Stu_major = :class AND s.Stu_room = :room
                GROUP BY s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_picture, s.Stu_no
                ORDER BY s.Stu_no ASC";

        $params = [
            ':class' => $class,
            ':room' => $room,
            ':term' => $term,
            ':pee' => $pee
        ];

        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * ดึงข้อมูลพฤติกรรมทั้งหมดในเทอมปัจจุบัน
     */
    public function getAllBehaviors($term, $pee)
    {
        // (เพิ่ม JOIN ตาราง student)
        $sql = "SELECT b.*, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_no
                FROM behavior b
                JOIN student s ON b.stu_id = s.Stu_id
                WHERE b.behavior_term = :term AND b.behavior_pee = :pee
                ORDER BY b.behavior_date DESC, s.Stu_major, s.Stu_room, s.Stu_no";
        return $this->db->query($sql, ['term' => $term, 'pee' => $pee])->fetchAll();
    }

    /**
     * ดึงข้อมูลพฤติกรรมแบบ Server-side สำหรับ DataTables
     */
    public function getBehaviorsServerSide($term, $pee, $start = 0, $length = 10, $search = '', $orderIdx = 0, $orderDir = 'DESC')
    {
        $allowedColumns = ['behavior_date', 'Stu_name', 'behavior_type', 'behavior_name', 'behavior_score'];
        $orderCol = $allowedColumns[$orderIdx] ?? 'behavior_date';

        // Base Query
        $sql = "SELECT b.*, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_no
                FROM behavior b
                JOIN student s ON b.stu_id = s.Stu_id
                WHERE b.behavior_term = :term AND b.behavior_pee = :pee";

        $params = [':term' => $term, ':pee' => $pee];

        // Search
        if (!empty($search)) {
            $search = trim($search);
            $words = preg_split('/\s+/', $search);
            $searchConditions = [];
            foreach ($words as $idx => $word) {
                if ($word === '') continue;
                $paramKey = ":s{$idx}";
                $searchConditions[] = "(
                    s.Stu_id LIKE {$paramKey} OR 
                    b.stu_id LIKE {$paramKey} OR 
                    s.Stu_name LIKE {$paramKey} OR 
                    s.Stu_sur LIKE {$paramKey} OR 
                    CONCAT(s.Stu_name, ' ', s.Stu_sur) LIKE {$paramKey} OR 
                    CONCAT(s.Stu_pre, s.Stu_name, ' ', s.Stu_sur) LIKE {$paramKey} OR 
                    CONCAT(s.Stu_pre, ' ', s.Stu_name, ' ', s.Stu_sur) LIKE {$paramKey} OR 
                    CONCAT('ม.', s.Stu_major, '/', s.Stu_room) LIKE {$paramKey} OR 
                    CONCAT(s.Stu_major, '/', s.Stu_room) LIKE {$paramKey} OR 
                    b.behavior_name LIKE {$paramKey} OR 
                    b.behavior_type LIKE {$paramKey} OR 
                    b.behavior_date LIKE {$paramKey}
                )";
                $params[$paramKey] = "%{$word}%";
            }
            if (!empty($searchConditions)) {
                $sql .= " AND " . implode(' AND ', $searchConditions);
            }
        }

        // Final counts for filtered records
        $countSql = "SELECT COUNT(*) FROM (" . $sql . ") as filtered";
        $filteredCount = $this->db->query($countSql, $params)->fetchColumn();

        // Order and Limit
        $sql .= " ORDER BY " . $orderCol . " " . $orderDir;
        $sql .= " LIMIT " . intval($length) . " OFFSET " . intval($start);

        $data = $this->db->query($sql, $params)->fetchAll();

        return [
            'data' => $data,
            'filtered' => $filteredCount
        ];
    }

    /**
     * นับจำนวนรายการพฤติกรรมทั้งหมดในเทอมปัจจุบัน
     */
    public function countAllBehaviors($term, $pee)
    {
        $sql = "SELECT COUNT(*) FROM behavior WHERE behavior_term = :term AND behavior_pee = :pee";
        return $this->db->query($sql, ['term' => $term, 'pee' => $pee])->fetchColumn();
    }

    /**
     * ดึงข้อมูลพฤติกรรม 1 รายการด้วย ID พร้อมข้อมูลนักเรียน
     */
    public function getBehaviorById($id)
    {
        $sql = "SELECT b.*, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture
                FROM behavior b
                JOIN student s ON b.stu_id = s.Stu_id
                WHERE b.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    /**
     * (เพิ่ม) ดึงข้อมูลนักเรียนสำหรับแสดง Preview
     */
    public function getStudentPreview($stu_id)
    {
        $sql = "SELECT Stu_id, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_picture 
                FROM student 
                WHERE Stu_id = :id AND Stu_status = '1'";
        return $this->db->query($sql, ['id' => $stu_id])->fetch();
    }

    /**
     * ค้นหานักเรียนแบบ autocomplete / fuzzy search (รหัสประจำตัว, ชื่อ, นามสกุล)
     * คืนค่าเป็น array ของนักเรียน (จำกัดผลลัพธ์)
     */
    public function searchStudents($q, $limit = 10)
    {
        $q = trim((string) $q);
        if ($q === '') {
            return [];
        }

        // แยกคำค้นหาด้วยช่องว่างเพื่อรองรับการค้นหา ชื่อ นามสกุล พร้อมกัน
        $words = preg_split('/\s+/', $q);
        $conditions = [];
        $params = [];

        foreach ($words as $idx => $word) {
            if ($word === '') continue;
            $paramKey = ":w{$idx}";
            $conditions[] = "(
                Stu_id LIKE {$paramKey} OR 
                Stu_name LIKE {$paramKey} OR 
                Stu_sur LIKE {$paramKey} OR 
                CONCAT(Stu_name, ' ', Stu_sur) LIKE {$paramKey} OR 
                CONCAT(Stu_pre, Stu_name, ' ', Stu_sur) LIKE {$paramKey} OR
                CONCAT(Stu_pre, ' ', Stu_name, ' ', Stu_sur) LIKE {$paramKey}
            )";
            $params[$paramKey] = '%' . $word . '%';
        }

        if (empty($conditions)) {
            return [];
        }

        $whereClause = implode(' AND ', $conditions);

        // จัดลำดับ: เลขประจำตัวตรงเป๊ะ หรือขึ้นต้นด้วยคำค้นหามาก่อน
        $params[':exact_q'] = $q;
        $params[':prefix_q'] = $q . '%';

        $sql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_picture
                FROM student
                WHERE Stu_status = '1'
                  AND ({$whereClause})
                ORDER BY 
                    (Stu_id = :exact_q) DESC,
                    (Stu_id LIKE :prefix_q) DESC,
                    (Stu_name LIKE :prefix_q) DESC,
                    Stu_major ASC, Stu_room ASC, Stu_no ASC, Stu_name ASC
                LIMIT " . intval($limit);

        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * สร้างรายการพฤติกรรมใหม่
     */
    public function createBehavior($data, $teach_id, $term, $pee)
    {
        $sql = "INSERT INTO behavior 
                    (stu_id, behavior_date, behavior_type, behavior_name, behavior_score, teach_id, behavior_term, behavior_pee)
                VALUES 
                    (:stu_id, :behavior_date, :behavior_type, :behavior_name, :behavior_score, :teach_id, :term, :pee)";
        // Determine score: prioritize user input score if provided, fallback to type mapping or 0
        $rawScore = $data['addBehavior_score'] ?? $data['behavior_score'] ?? null;
        if ($rawScore !== null && $rawScore !== '' && is_numeric($rawScore)) {
            $score = intval($rawScore);
        } else {
            $score = $this->getScoreForType($data['addBehavior_type'] ?? $data['behavior_type'] ?? '') ?? 0;
        }

        $params = [
            ':stu_id' => $data['addStu_id'] ?? $data['stu_id'] ?? '',
            ':behavior_date' => $data['addBehavior_date'] ?? $data['behavior_date'] ?? '',
            ':behavior_type' => $data['addBehavior_type'] ?? $data['behavior_type'] ?? '',
            ':behavior_name' => $data['addBehavior_name'] ?? $data['behavior_name'] ?? '',
            ':behavior_score' => $score,
            ':teach_id' => $teach_id,
            ':term' => $term,
            ':pee' => $pee
        ];

        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    /**
     * อัปเดตรายการพฤติกรรม
     */
    public function updateBehavior($id, $data, $teach_id, $term, $pee)
    {
        $sql = "UPDATE behavior SET
                    stu_id = :stu_id,
                    behavior_date = :behavior_date,
                    behavior_type = :behavior_type,
                    behavior_name = :behavior_name,
                    behavior_score = :behavior_score,
                    teach_id = :teach_id,
                    behavior_term = :term,
                    behavior_pee = :pee
                WHERE id = :id";

        // Determine score: prioritize user input score if provided, fallback to type mapping or 0
        $rawScore = $data['editBehavior_score'] ?? $data['behavior_score'] ?? null;
        if ($rawScore !== null && $rawScore !== '' && is_numeric($rawScore)) {
            $score = intval($rawScore);
        } else {
            $score = $this->getScoreForType($data['editBehavior_type'] ?? $data['behavior_type'] ?? '') ?? 0;
        }

        $params = [
            ':stu_id' => $data['editStu_id'],
            ':behavior_date' => $data['editBehavior_date'],
            ':behavior_type' => $data['editBehavior_type'],
            ':behavior_name' => $data['editBehavior_name'],
            ':behavior_score' => $score,
            ':teach_id' => $teach_id,
            ':term' => $term,
            ':pee' => $pee,
            ':id' => $id
        ];

        $this->db->query($sql, $params);
        return true;
    }

    /**
     * คืนค่าสกอร์ตามประเภทพฤติกรรม (null หากไม่แม็ป)
     * @param string $type
     * @return int|null
     */
    public function getScoreForType($type)
    {
        // Normalize input
        $t = trim((string) $type);
        switch ($t) {
            case "หนีเรียนหรือออกนอกสถานศึกษา":
                return 10;
            case "เล่นการพนัน":
                return 20;
            case "มาโรงเรียนสาย":
                return 5;
            case "ขาดเรียน":
                return 5;
            case "แต่งกาย/ทรงผมผิดระเบียบ":
                return 5;
            case "พกพาอาวุธหรือวัตถุระเบิด":
                return 20;
            case "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์":
                return 20;
            case "สูบบุหรี่":
                return 30;
            case "เสพยาเสพติด":
                return 30;
            case "ลักทรัพย์ กรรโชกทรัพย์":
                return 30;
            case "ก่อเหตุทะเลาะวิวาท":
                return 20;
            case "แสดงพฤติกรรมทางชู้สาว":
                return 20;
            case "จอดรถในที่ห้ามจอด":
                return 10;
            case "แสดงพฤติกรรมก้าวร้าว":
                return 10;
            case "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ":
                return 5;
            default:
                // return null to indicate no mapping; caller may fallback to posted score
                return null;
        }
    }

    /**
     * ลบรายการพฤติกรรม
     */
    public function deleteBehavior($id)
    {
        $sql = "DELETE FROM behavior WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * ดึงรายละเอียดการถูกหักคะแนนของนักเรียนคนหนึ่งในเทอมปัจจุบัน
     */
    public function getStudentBehaviorDetails($stu_id, $term, $pee)
    {
        $sql = "SELECT b.id, b.behavior_date, b.behavior_type, b.behavior_name, b.behavior_score,
                       t.Teach_name
                FROM behavior b
                LEFT JOIN teacher t ON b.teach_id = t.Teach_id
                WHERE b.stu_id = :stu_id AND b.behavior_term = :term AND b.behavior_pee = :pee
                ORDER BY b.behavior_date DESC, b.id DESC";

        $params = [
            ':stu_id' => $stu_id,
            ':term' => $term,
            ':pee' => $pee
        ];

        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * ดึงข้อมูลพฤติกรรมทั้งหมดของครูคนหนึ่งในเทอมปัจจุบัน
     */
    public function getBehaviorsByTeacherId($teacher_id, $term, $pee)
    {
        $sql = "SELECT b.*, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room
                FROM behavior b
                INNER JOIN student s ON b.stu_id = s.Stu_id
                WHERE b.teach_id = :teacher_id AND b.behavior_term = :term AND b.behavior_pee = :pee
                ORDER BY b.behavior_date DESC";

        $params = [
            ':teacher_id' => $teacher_id,
            ':term' => $term,
            ':pee' => $pee
        ];

        return $this->db->query($sql, $params)->fetchAll();
    }
}
?>