<?php
class SDQ {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getSDQByClassAndRoom($class, $room, $pee, $term) {
        $query = "
            SELECT
                CONCAT(st.Stu_pre, st.Stu_name, '  ', st.Stu_sur) AS full_name,
                st.Stu_id,
                st.Stu_no,
                st.Stu_picture,

                CASE
                    WHEN ss.Stu_id IS NOT NULL AND ss.Pee = :pee AND ss.Term = :term THEN 1
                    ELSE 0
                END AS self_ishave,

                CASE
                    WHEN sp.Stu_id IS NOT NULL AND sp.Pee = :pee AND sp.Term = :term THEN 1
                    ELSE 0
                END AS par_ishave,

                CASE
                    WHEN stc.Stu_id IS NOT NULL AND stc.Pee = :pee AND stc.Term = :term THEN 1
                    ELSE 0
                END AS teach_ishave

            FROM
                student AS st

            LEFT JOIN sdq_self AS ss ON ss.Stu_id = st.Stu_id AND ss.Pee = :pee AND ss.term = :term
            LEFT JOIN sdq_par AS sp ON sp.Stu_id = st.Stu_id AND sp.Pee = :pee AND sp.term = :term
            LEFT JOIN sdq_teach AS stc ON stc.Stu_id = st.Stu_id AND stc.Pee = :pee AND stc.term = :term

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

    public function saveSDQSelf($student_id, $answers, $memo, $pee, $term) {
        $this->db->beginTransaction();

        try {
            // Delete existing data for the student
            $deleteQuery = "DELETE FROM sdq_self WHERE Stu_id = :student_id AND Pee = :pee AND Term = :term";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->execute([
                ':student_id' => $student_id,
                ':pee' => $pee,
                ':term' => $term
            ]);

            // Prepare insert query
            $insertQuery = "
                INSERT INTO sdq_self (
                    Stu_id, Sdq1, Sdq2, Sdq3, Sdq4, Sdq5, Sdq6, Sdq7, Sdq8, Sdq9, Sdq10,
                    Sdq11, Sdq12, Sdq13, Sdq14, Sdq15, Sdq16, Sdq17, Sdq18, Sdq19, Sdq20,
                    Sdq21, Sdq22, Sdq23, Sdq24, Sdq25, Memo, Term, Pee
                ) VALUES (
                    :student_id, :Sdq1, :Sdq2, :Sdq3, :Sdq4, :Sdq5, :Sdq6, :Sdq7, :Sdq8, :Sdq9, :Sdq10,
                    :Sdq11, :Sdq12, :Sdq13, :Sdq14, :Sdq15, :Sdq16, :Sdq17, :Sdq18, :Sdq19, :Sdq20,
                    :Sdq21, :Sdq22, :Sdq23, :Sdq24, :Sdq25, :memo, :term, :pee
                )
            ";
            $insertStmt = $this->db->prepare($insertQuery);

            // Map answers to query parameters
            $params = [
                ':student_id' => $student_id,
                ':memo' => $memo,
                ':term' => $term,
                ':pee' => $pee
            ];
            for ($i = 1; $i <= 25; $i++) {
                $params[":Sdq$i"] = $answers["q$i"] ?? null;
            }

            // Execute insert query
            $insertStmt->execute($params);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
?>
