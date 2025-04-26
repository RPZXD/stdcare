<?php
class Wroom {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    // ดึงข้อมูลนักเรียนและตำแหน่ง
    public function getWroomStudents($major, $room, $pee) {
        $query = "SELECT wr.*, st.Stu_no, st.Stu_id, st.Stu_pre, st.Stu_name, st.Stu_sur , st.Stu_picture
                  FROM tb_wroom as wr
                  INNER JOIN student as st ON wr.Stu_id = st.Stu_id
                  WHERE wr.wmajor = :major AND wr.wroom = :room AND wr.wpee = :pee AND st.Stu_status = 1
                  ORDER BY st.Stu_no ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':major' => $major,
            ':room' => $room,
            ':pee' => $pee
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) === 0) {
            $query = "SELECT st.Stu_no, st.Stu_id, st.Stu_pre, st.Stu_name, st.Stu_sur , st.Stu_picture
                      FROM student as st
                      WHERE st.Stu_major = :major AND st.Stu_room = :room AND st.Stu_status = 1
                      ORDER BY st.Stu_no ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':major' => $major,
                ':room' => $room
            ]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // เพิ่ม wposit = "" ให้ทุกคน
            foreach ($results as &$row) {
                $row['wposit'] = "";
            }
        }
        return $results;
    }

    // ดึง maxim
    public function getMaxim($major, $room, $pee) {
        $stmt = $this->db->prepare("SELECT wkatipot FROM tb_wroom2 WHERE wmajor = :major AND wroom = :room AND wpee = :pee");
        $stmt->execute([
            ':major' => $major,
            ':room' => $room,
            ':pee' => $pee
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['wkatipot'] : '';
    }

    // บันทึกตำแหน่งและ maxim
    public function saveWroom($major, $room, $pee, $term, $positions, $stdids, $maxim) {
        // ลบข้อมูลเดิม
        $this->db->beginTransaction();
        try {
            $this->db->prepare("DELETE FROM tb_wroom WHERE wmajor = :major AND wroom = :room AND wpee = :pee")
                ->execute([':major' => $major, ':room' => $room, ':pee' => $pee]);
            // เพิ่มข้อมูลใหม่
            $insert = $this->db->prepare("INSERT INTO tb_wroom (wmajor, wroom, wpee, wterm, Stu_id, wposit) VALUES (:major, :room, :pee, :term, :stuid, :wposit)");
            foreach ($stdids as $i => $stuid) {
                $insert->execute([
                    ':major' => $major,
                    ':room' => $room,
                    ':pee' => $pee,
                    ':term' => $term,
                    ':stuid' => $stuid,
                    ':wposit' => $positions[$i] ?? ""
                ]);
            }
            // maxim
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM tb_wroom2 WHERE wmajor = :major AND wroom = :room AND wpee = :pee");
            $stmt->execute([':major' => $major, ':room' => $room, ':pee' => $pee]);
            if ($stmt->fetchColumn() > 0) {
                $this->db->prepare("UPDATE tb_wroom2 SET wkatipot = :maxim WHERE wmajor = :major AND wroom = :room AND wpee = :pee")
                    ->execute([':maxim' => $maxim, ':major' => $major, ':room' => $room, ':pee' => $pee]);
            } else {
                $this->db->prepare("INSERT INTO tb_wroom2 (wmajor, wroom, wpee, wkatipot) VALUES (:major, :room, :pee, :maxim)")
                    ->execute([':major' => $major, ':room' => $room, ':pee' => $pee, ':maxim' => $maxim]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
