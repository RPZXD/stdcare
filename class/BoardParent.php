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

}
?>