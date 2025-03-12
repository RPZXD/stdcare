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

    public function getStudyStatusCountClassRoom2($class, $room, $date) {
        $query = "SELECT st.Study_status, 
                    CASE 
                        WHEN st.Study_status = 1 THEN 'มาเรียน'
                        WHEN st.Study_status = 2 THEN 'ขาดเรียน'
                        WHEN st.Study_status = 3 THEN 'มาสาย'
                        WHEN st.Study_status = 4 THEN 'ลาป่วย'
                        WHEN st.Study_status = 5 THEN 'ลากิจ'
                        WHEN st.Study_status = 6 THEN 'เข้าร่วมกิจกรรม'
                        ELSE 'ไม่ระบุ'
                    END AS status_name,
                    COUNT(*) AS count_total
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

    public function getCountClassRoom($class, $room) {
        $query = "SELECT COUNT(*) AS total_count 
                  FROM {$this->table_student}
                  WHERE Stu_major = :class AND Stu_room = :room
                  AND Stu_status = 1
                  ";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':class', $class, PDO::PARAM_INT);
        $statement->bindParam(':room', $room, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $result['total_count'] ?? 0;
    
        return $count === 0 ? "0" : $count;
    }

    public function getStatusCountClassRoom($class, $room, $status, $date) {
        // Ensure $status is an array
        if (!is_array($status)) {
            $status = [$status]; 
        }
        
        // Convert status array into a comma-separated string for SQL
        $placeholders = implode(',', array_fill(0, count($status), '?'));
    
        $query = "SELECT 
                        COUNT(*) AS total_count 
                  FROM study AS st 
                  INNER JOIN student AS s ON st.Stu_id = s.Stu_id 
                  WHERE s.Stu_major = ? 
                  AND s.Stu_room = ?
                  AND st.Study_status IN ($placeholders)
                  AND st.Study_date = ?";
    
        $statement = $this->conn->prepare($query);
    
        // Bind values
        $params = array_merge([$class, $room], $status, [$date]);
        $statement->execute($params);
    
        // Fetch result
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $result['total_count'] ?? 0;
    
        return $count === 0 ? "0" : $count;
    }
    
}
?>
