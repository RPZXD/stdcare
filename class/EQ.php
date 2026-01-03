<?php
class EQ {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getEQByClassAndRoom($class, $room, $pee, $term) {
        $query = "
            SELECT
                CONCAT(st.Stu_pre, st.Stu_name, '  ', st.Stu_sur) AS full_name,
                st.Stu_id,
                st.Stu_no,
                st.Stu_picture,

                CASE
                    WHEN ss.Stu_id IS NOT NULL AND ss.Pee = :pee AND ss.Term = :term THEN 1
                    ELSE 0
                END AS eq_ishave

            FROM
                student AS st

            LEFT JOIN eq AS ss ON ss.Stu_id = st.Stu_id AND ss.Pee = :pee AND ss.Term = :term

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
        $stmt->bindParam(':term', $term);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getEQData($student_id, $pee, $term) {
        $query = "
            SELECT
                EQ1, EQ2, EQ3, EQ4, EQ5, EQ6, EQ7, EQ8, EQ9, EQ10,
                EQ11, EQ12, EQ13, EQ14, EQ15, EQ16, EQ17, EQ18, EQ19, EQ20,
                EQ21, EQ22, EQ23, EQ24, EQ25, EQ26, EQ27, EQ28, EQ29, EQ30,
                EQ31, EQ32, EQ33, EQ34, EQ35, EQ36, EQ37, EQ38, EQ39, EQ40,
                EQ41, EQ42, EQ43, EQ44, EQ45, EQ46, EQ47, EQ48, EQ49, EQ50,
                EQ51, EQ52
            FROM
                eq
            WHERE
                Stu_id = :student_id AND Pee = :pee AND Term = :term
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':student_id' => $student_id,
            ':pee' => $pee,
            ':term' => $term
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveEQData($student_id, $answers, $pee, $term) {
        $this->db->beginTransaction();

        try {
            // Delete existing data for the student
            $deleteQuery = "DELETE FROM eq WHERE Stu_id = :student_id AND Pee = :pee AND Term = :term";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->execute([
                ':student_id' => $student_id,
                ':pee' => $pee,
                ':term' => $term
            ]);

            // Prepare insert query
            $insertQuery = "
                INSERT INTO eq (
                    Stu_id, EQ1, EQ2, EQ3, EQ4, EQ5, EQ6, EQ7, EQ8, EQ9, EQ10,
                    EQ11, EQ12, EQ13, EQ14, EQ15, EQ16, EQ17, EQ18, EQ19, EQ20,
                    EQ21, EQ22, EQ23, EQ24, EQ25, EQ26, EQ27, EQ28, EQ29, EQ30,
                    EQ31, EQ32, EQ33, EQ34, EQ35, EQ36, EQ37, EQ38, EQ39, EQ40,
                    EQ41, EQ42, EQ43, EQ44, EQ45, EQ46, EQ47, EQ48, EQ49, EQ50,
                    EQ51, EQ52, Term, Pee
                ) VALUES (
                    :student_id, :EQ1, :EQ2, :EQ3, :EQ4, :EQ5, :EQ6, :EQ7, :EQ8, :EQ9, :EQ10,
                    :EQ11, :EQ12, :EQ13, :EQ14, :EQ15, :EQ16, :EQ17, :EQ18, :EQ19, :EQ20,
                    :EQ21, :EQ22, :EQ23, :EQ24, :EQ25, :EQ26, :EQ27, :EQ28, :EQ29, :EQ30,
                    :EQ31, :EQ32, :EQ33, :EQ34, :EQ35, :EQ36, :EQ37, :EQ38, :EQ39, :EQ40,
                    :EQ41, :EQ42, :EQ43, :EQ44, :EQ45, :EQ46, :EQ47, :EQ48, :EQ49, :EQ50,
                    :EQ51, :EQ52, :term, :pee
                )
            ";
            $insertStmt = $this->db->prepare($insertQuery);

            // Map answers to query parameters (default '0' if not set)
            $params = [
                ':student_id' => $student_id,
                ':term' => $term,
                ':pee' => $pee
            ];
            for ($i = 1; $i <= 52; $i++) {
                $params[":EQ$i"] = isset($answers["eq$i"]) ? $answers["eq$i"] : (isset($answers["q$i"]) ? $answers["q$i"] : '0');
            }

            // Execute insert query
            $insertStmt->execute($params);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateEQData($student_id, $answers, $pee, $term) {
        $set = [];
        $params = [
            ':student_id' => $student_id,
            ':pee' => $pee,
            ':term' => $term
        ];
        for ($i = 1; $i <= 52; $i++) {
            $set[] = "EQ$i = :EQ$i";
            $params[":EQ$i"] = $answers["q$i"] ?? null;
        }
        $setStr = implode(', ', $set);

        $query = "UPDATE eq SET $setStr WHERE Stu_id = :student_id AND Pee = :pee AND Term = :term";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
    }

    // สรุปผล EQ รายห้อง/รายชั้น
    public function getEQClassRoomSummary($class, $room, $pee, $term) {
        $params = [];
        $where = "s.Stu_major = :class AND s.Stu_status = 1";
        $params[':class'] = $class;
        if ($room !== '' && $room !== null) {
            $where .= " AND s.Stu_room = :room";
            $params[':room'] = $room;
        }

        // นับจำนวนนักเรียนทั้งหมดในห้อง/ชั้น
        $sqlTotal = "SELECT COUNT(*) AS total FROM student s WHERE $where";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->execute($params);
        $total = (int)($stmtTotal->fetchColumn());

        // นับจำนวนนักเรียนที่ส่ง EQ
        $sqlHave = "SELECT COUNT(DISTINCT s.Stu_id) AS have
            FROM student s
            INNER JOIN eq eq ON eq.Stu_id = s.Stu_id AND eq.Pee = :pee AND eq.Term = :term
            WHERE $where";
        $paramsHave = $params + [':pee' => $pee, ':term' => $term];
        $stmtHave = $this->db->prepare($sqlHave);
        $stmtHave->execute($paramsHave);
        $have = (int)($stmtHave->fetchColumn());

        // นับแต่ละกลุ่ม (ดีมาก, ดี, ปานกลาง, ต้องปรับปรุง) จากคะแนนรวม
        $verygood = $good = $mid = $low = 0;
        $sqlScore = "SELECT 
                (" . implode('+', array_map(fn($i) => "COALESCE(eq.EQ$i,0)", range(1,52))) . ") AS total_score
            FROM student s
            INNER JOIN eq eq ON eq.Stu_id = s.Stu_id AND eq.Pee = :pee AND eq.Term = :term
            WHERE $where";
        $stmtScore = $this->db->prepare($sqlScore);
        $stmtScore->execute($paramsHave);
        while ($row = $stmtScore->fetch(PDO::FETCH_ASSOC)) {
            $score = (int)$row['total_score'];
            if ($score >= 170) $verygood++;
            elseif ($score >= 140) $good++;
            elseif ($score >= 100) $mid++;
            else $low++;
        }

        return [
            'total' => $total,
            'have' => $have,
            'verygood' => $verygood,
            'good' => $good,
            'mid' => $mid,
            'low' => $low
        ];
    }

}
?>
