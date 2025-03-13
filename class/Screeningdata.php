<?php

class ScreeningData {
    private $db;
    private $stu_major;
    private $stu_room;
    private $pee;

    public function __construct($db, $stu_major, $stu_room, $pee) {
        $this->db = $db;
        $this->stu_major = $stu_major;
        $this->stu_room = $stu_room;
        $this->pee = $pee;
    }

    public function getDistinctDanIds() {
        $query = "SELECT DISTINCT dan_id FROM tb_screenstu ORDER BY dan_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScreeningData($dan_id, $screen_status, $stu_sex = null) {
        $query = "SELECT COUNT(*) FROM tb_screenstu sc
                  INNER JOIN student s ON sc.stu_id = s.stu_id
                  WHERE sc.screen_status = :screen_status 
                  AND sc.dan_id = :dan_id
                  AND s.stu_major = :stu_major 
                  AND s.stu_room = :stu_room 
                  AND sc.pee = :pee 
                  AND s.Stu_status = 1";
        
        if ($stu_sex !== null) {
            $query .= " AND s.stu_sex = :stu_sex";
        }
        $query .= " GROUP BY s.stu_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':screen_status', $screen_status);
        $stmt->bindParam(':dan_id', $dan_id);
        $stmt->bindParam(':stu_major', $this->stu_major);
        $stmt->bindParam(':stu_room', $this->stu_room);
        $stmt->bindParam(':pee', $this->pee);

        if ($stu_sex !== null) {
            $stmt->bindParam(':stu_sex', $stu_sex);
        }

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getTotalRecords() {
        $query = "SELECT COUNT(*) FROM tb_screenstu sc
                  INNER JOIN student s ON sc.stu_id = s.stu_id
                  WHERE s.stu_major = :stu_major 
                  AND s.stu_room = :stu_room 
                  AND s.Stu_status = 1
                  GROUP BY s.stu_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':stu_major', $this->stu_major);
        $stmt->bindParam(':stu_room', $this->stu_room);

        $stmt->execute();
        return $stmt->rowCount();
    }
}
