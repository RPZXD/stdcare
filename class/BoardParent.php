<?php
class BoardParent {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getBoardParentByClassAndRoom($class, $room, $pee) {
        try {
            $query = "
                SELECT * 
                    FROM tb_parnet 
                    WHERE parn_lev = :class
                        AND parn_room = :room
                        AND parn_pee = :pee 
                    ORDER BY parn_pos ASC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':class', $class, PDO::PARAM_STR);
            $stmt->bindParam(':room', $room, PDO::PARAM_STR);
            $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch data: " . $e->getMessage());
        }
    }

    public function insertBoardParent($stu_id, $name, $address, $tel, $pos, $photo, $major, $room, $teacherid, $term, $pee) {
        try {
            $query = "
                INSERT INTO tb_parnet (Stu_id, parn_name, parn_addr, parn_tel, parn_pos, parn_photo, parn_lev, parn_room , tidadd, parn_term, parn_pee)
                VALUES (:stu_id, :name, :address, :tel, :pos, :photo, :major, :room, :teacherid, :term, :pee)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':stu_id', $stu_id, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
            $stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
            $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
            $stmt->bindParam(':major', $major, PDO::PARAM_STR);
            $stmt->bindParam(':room', $room, PDO::PARAM_STR);
            $stmt->bindParam(':teacherid', $teacherid, PDO::PARAM_STR);
            $stmt->bindParam(':term', $term, PDO::PARAM_STR);
            $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Failed to insert data: " . $e->getMessage());
        }
    }

    public function updateBoardParent($stu_id, $name, $address, $tel, $pos, $photo, $pee) {
        try {
            $query = "
                UPDATE tb_parnet
                SET parn_name = :name,
                    parn_addr = :address,
                    parn_tel = :tel,
                    parn_pos = :pos,
                    parn_photo = COALESCE(:photo, parn_photo)
                WHERE Stu_id = :stu_id AND parn_pee = :pee
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
            $stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
            $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
            $stmt->bindParam(':stu_id', $stu_id, PDO::PARAM_INT);
            $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Failed to update data: " . $e->getMessage());
        }
    }

    public function deleteBoardParentById($studentId) {
        try {
            $query = "DELETE FROM tb_parnet WHERE Stu_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $studentId, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount() > 0; // Return true if rows were affected
        } catch (PDOException $e) {
            throw new Exception("Failed to delete data: " . $e->getMessage());
        }
    }
}
?>