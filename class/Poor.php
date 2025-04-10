<?php
class Poor {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getPoorByClassAndRoom($class, $room) {
        try {
            $query = "
                SELECT
                    s.Stu_pre,
                    s.Stu_name,
                    s.Stu_sur,
                    s.Stu_id,
                    s.Stu_picture,
                    pr.*
                FROM
                    student AS s
                INNER JOIN
                    tb_poor AS pr ON pr.Stu_id = s.Stu_id
                WHERE
                    s.Stu_major = :class
                    AND s.Stu_room = :room
                    AND s.Stu_status = 1
                ORDER BY
                    pr.poor_no ASC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':class', $class, PDO::PARAM_STR);
            $stmt->bindParam(':room', $room, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch data: " . $e->getMessage());
        }
    }

    public function getPoorById($studentId) {
        try {
            $query = "SELECT * FROM tb_poor WHERE Stu_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $studentId, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch data: " . $e->getMessage());
        }
    }

    public function insertPoorStudent($teacherId, $number, $studentId, $reason, $received, $detail) {
        try {
            $query = "INSERT INTO tb_poor (Stu_id, poor_no, poor_reason, poor_even, poor_schol, teacher_create, create_at) 
                      VALUES (:studentId, :number, :reason, :received, :detail, :teacherId, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':studentId', $studentId, PDO::PARAM_STR);
            $stmt->bindParam(':number', $number, PDO::PARAM_INT);
            $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
            $stmt->bindParam(':received', $received, PDO::PARAM_INT);
            $stmt->bindParam(':detail', $detail, PDO::PARAM_STR);
            $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Failed to insert data: " . $e->getMessage());
        }
    }

    public function updatePoorStudent($student, $number, $reason, $received, $detail) {
        try {
            $query = "UPDATE tb_poor 
                      SET poor_no = :poor_no, 
                          poor_reason = :poor_reason, 
                          poor_schol = :poor_schol, 
                          poor_even = :poor_even 
                      WHERE Stu_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':poor_no', $number, PDO::PARAM_INT);
            $stmt->bindParam(':poor_reason', $reason, PDO::PARAM_STR);
            $stmt->bindParam(':poor_schol', $detail, PDO::PARAM_STR);
            $stmt->bindParam(':poor_even', $received, PDO::PARAM_INT);
            $stmt->bindParam(':id', $student, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Failed to update data: " . $e->getMessage());
        }
    }

    public function deletePoorById($studentId) {
        try {
            $query = "DELETE FROM tb_poor WHERE Stu_id = :id";
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
