<?php

class HomeroomModel {
    private $conn;
    private $table_homeroom = "tb_homeroom";
    private $table_type = "tb_typehomeroom";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function fetchHomerooms($class, $room, $term, $pee) {
        $query = "SELECT h.h_id, h.th_id, t.th_name, h.h_topic, h.h_detail, h.h_result, h.h_date, h.h_major, h.h_room, h.h_term, h.h_pee, h.h_pic1, h.h_pic2
                  FROM " . $this->table_homeroom . " h
                  INNER JOIN " . $this->table_type . " t ON h.th_id = t.th_id
                  WHERE h.h_major = :class AND h.h_room = :room AND h.h_term = :term AND h.h_pee = :pee
                  ORDER BY h.h_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':room', $room);
        $stmt->bindParam(':term', $term);
        $stmt->bindParam(':pee', $pee);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchHomeroomById($id) {
        $query = "SELECT h.h_id, h.th_id, t.th_name, h.h_topic, h.h_detail, h.h_result, h.h_date, h.h_major, h.h_room, h.h_term, h.h_pee, h.h_pic1, h.h_pic2
                  FROM " . $this->table_homeroom . " h
                  INNER JOIN " . $this->table_type . " t ON h.th_id = t.th_id
                  WHERE h.h_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchHomeroomTypes() {
        $query = "SELECT th_id, th_name FROM " . $this->table_type . " ORDER BY th_name ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteHomeroom($id) {
        $query = "DELETE FROM " . $this->table_homeroom . " WHERE h_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function updateHomeroom($id, $type, $title, $detail, $result, $image1, $image2) {
        $query = "UPDATE " . $this->table_homeroom . " 
                  SET th_id = :type, h_topic = :title, h_detail = :detail, h_result = :result, h_pic1 = :image1, h_pic2 = :image2 
                  WHERE h_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':detail', $detail);
        $stmt->bindParam(':result', $result);
        $stmt->bindParam(':image1', $image1);
        $stmt->bindParam(':image2', $image2);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function insertHomeroom($type, $title, $detail, $result, $date, $major, $room, $term, $pee, $image1 = null, $image2 = null) {
        $query = "INSERT INTO " . $this->table_homeroom . " (th_id, h_topic, h_detail, h_result, h_date, h_major, h_room, h_term, h_pee, h_pic1, h_pic2)
                  VALUES (:type, :title, :detail, :result, :date, :major, :room, :term, :pee, :image1, :image2)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':detail', $detail);
        $stmt->bindParam(':result', $result);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':major', $major);
        $stmt->bindParam(':room', $room);
        $stmt->bindParam(':term', $term);
        $stmt->bindParam(':pee', $pee);
        $stmt->bindParam(':image1', $image1);
        $stmt->bindParam(':image2', $image2);

        return $stmt->execute();
    }
}

?>
