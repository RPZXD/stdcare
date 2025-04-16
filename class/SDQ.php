<?php
class SDQ {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getSDQByClassAndRoom($class, $room, $pee) {
        $query = "
            SELECT
                CONCAT(st.Stu_pre, st.Stu_name, '  ', st.Stu_sur) AS full_name,
                st.Stu_id,
                st.Stu_no,
                st.Stu_picture,

                CASE
                    WHEN ss.Stu_id IS NOT NULL AND ss.Pee = :pee THEN 1
                    ELSE 0
                END AS self_ishave,

                CASE
                    WHEN sp.Stu_id IS NOT NULL AND sp.Pee = :pee THEN 1
                    ELSE 0
                END AS par_ishave,

                CASE
                    WHEN stc.Stu_id IS NOT NULL AND stc.Pee = :pee THEN 1
                    ELSE 0
                END AS teach_ishave

            FROM
                student AS st

            LEFT JOIN sdq_self AS ss ON ss.Stu_id = st.Stu_id AND ss.Pee = :pee
            LEFT JOIN sdq_par AS sp ON sp.Stu_id = st.Stu_id AND sp.Pee = :pee
            LEFT JOIN sdq_teach AS stc ON stc.Stu_id = st.Stu_id AND stc.Pee = :pee

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
}
?>
