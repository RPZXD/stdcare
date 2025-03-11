<?php

class Student {
    private $conn;
    private $table_student = "student";
    private $table_study = "study";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getStudyStatusCount($class, $date) {
        $query = "SELECT st.Study_status, COUNT(*) AS count 
                  FROM {$this->table_study} AS st 
                  INNER JOIN {$this->table_student} AS s ON st.Stu_id = s.Stu_id 
                  WHERE s.Stu_major = :class AND st.Study_date = :date 
                  GROUP BY st.Study_status";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':date', $date);
        $statement->bindParam(':class', $class, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudyStatusCountClassRoom($class, $room, $date) {
        $query = "SELECT st.Study_status, COUNT(*) AS count 
                  FROM {$this->table_study} AS st 
                  INNER JOIN {$this->table_student} AS s ON st.Stu_id = s.Stu_id 
                  WHERE s.Stu_major = :class AND s.Stu_room = :room AND st.Study_date = :date 
                  GROUP BY st.Study_status";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':date', $date);
        $statement->bindParam(':class', $class, PDO::PARAM_INT);
        $statement->bindParam(':room', $room, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
