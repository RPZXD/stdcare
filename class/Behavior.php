<?php
class Behavior {
    private $conn;
    private $table = 'behavior';
    private $table_data = 'student';
    private $table_teacher = 'teacher';

    public $id;
    public $stu_id;
    public $behavior_date;
    public $behavior_type;
    public $behavior_name;
    public $behavior_score;
    public $teach_id;
    public $term;
    public $pee;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Insert behavior data into the database
    public function create() {
        // Standardize behavior_type for late
        if ($this->behavior_type === 'late' || $this->behavior_type === 'มาสาย') {
            $this->behavior_type = 'มาโรงเรียนสาย';
        }

        // Check for duplicate before insert
        $checkQuery = "SELECT id FROM " . $this->table . " WHERE stu_id = :stu_id AND behavior_date = :behavior_date AND behavior_type = :behavior_type LIMIT 1";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':stu_id', $this->stu_id);
        $checkStmt->bindParam(':behavior_date', $this->behavior_date);
        $checkStmt->bindParam(':behavior_type', $this->behavior_type);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            // Duplicate found, do not insert
            return false;
        }

        $query = "INSERT INTO " . $this->table . " 
                  (stu_id, behavior_date, behavior_type, behavior_name, behavior_score, teach_id, behavior_term, behavior_pee)
                  VALUES (:stu_id, :behavior_date, :behavior_type, :behavior_name, :behavior_score, :teach_id, :term, :pee)";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize input data (convert null/array to string to avoid fatal error)
        $this->stu_id = is_scalar($this->stu_id) ? htmlspecialchars(strip_tags($this->stu_id)) : '';
        $this->behavior_date = is_scalar($this->behavior_date) ? htmlspecialchars(strip_tags($this->behavior_date)) : '';
        $this->behavior_type = is_scalar($this->behavior_type) ? htmlspecialchars(strip_tags($this->behavior_type)) : '';
        $this->behavior_name = is_scalar($this->behavior_name) ? htmlspecialchars(strip_tags($this->behavior_name)) : '';
        $this->behavior_score = is_scalar($this->behavior_score) ? htmlspecialchars(strip_tags($this->behavior_score)) : '';
        $this->teach_id = is_scalar($this->teach_id) ? htmlspecialchars(strip_tags($this->teach_id)) : '';
        $this->term = is_scalar($this->term) ? htmlspecialchars(strip_tags($this->term)) : '';
        $this->pee = is_scalar($this->pee) ? htmlspecialchars(strip_tags($this->pee)) : '';

        // Bind data
        $stmt->bindParam(':stu_id', $this->stu_id);
        $stmt->bindParam(':behavior_date', $this->behavior_date);
        $stmt->bindParam(':behavior_type', $this->behavior_type);
        $stmt->bindParam(':behavior_name', $this->behavior_name);
        $stmt->bindParam(':behavior_score', $this->behavior_score);
        $stmt->bindParam(':teach_id', $this->teach_id);
        $stmt->bindParam(':term', $this->term);
        $stmt->bindParam(':pee', $this->pee);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function update() {
        $query = "UPDATE {$this->table} 
                  SET stu_id = :stu_id, 
                      behavior_date = :behavior_date, 
                      behavior_type = :behavior_type, 
                      behavior_name = :behavior_name, 
                      behavior_score = :behavior_score 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':stu_id', $this->stu_id);
        $stmt->bindParam(':behavior_date', $this->behavior_date);
        $stmt->bindParam(':behavior_type', $this->behavior_type);
        $stmt->bindParam(':behavior_name', $this->behavior_name);
        $stmt->bindParam(':behavior_score', $this->behavior_score);
        $stmt->bindParam(':id', $this->id);

        // Execute the query
        return $stmt->execute();
    }

    // Function to get behavior scores based on class and room
    public function getScoreBehaviorsClassTA($class, $room, $term, $pee)
    {
        try {
            // SQL query
            $query = "
                SELECT 
                    s.Stu_id, 
                    s.Stu_no, 
                    s.Stu_pre, 
                    s.Stu_name, 
                    s.Stu_sur, 
                    s.Stu_major, 
                    s.Stu_room, 
                    COALESCE(SUM(b.behavior_score), 0) AS total_behavior_score, 
                    GROUP_CONCAT(DISTINCT t.Teach_name) AS teacher_names
                FROM 
                    student AS s
                LEFT JOIN 
                    behavior AS b 
                    ON s.stu_id = b.stu_id 
                    AND b.behavior_term = :term 
                    AND b.behavior_pee = :pee
                LEFT JOIN 
                    teacher AS t 
                    ON b.teach_id = t.Teach_id
                WHERE 
                    s.Stu_major = :class 
                    AND s.Stu_room = :room 
                    AND s.Stu_status = 1
                GROUP BY 
                    s.Stu_id, s.Stu_no, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room
                ORDER BY 
                    s.Stu_no ASC
            ";

            // Prepare the statement
            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':class', $class, PDO::PARAM_INT);
            $stmt->bindParam(':room', $room, PDO::PARAM_INT);
            $stmt->bindParam(':term', $term, PDO::PARAM_INT);
            $stmt->bindParam(':pee', $pee, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch results
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "success" => true,
                "data" => $result
            ];
        } catch (Exception $e) {
            // Handle errors
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }
    public function getBehaviorTeacherID($id, $term, $pee)
    {
        try {
            // SQL query
            $query = "
                SELECT 
                    a.*, b.Stu_pre, b.Stu_name, b.Stu_sur, b.Stu_major, b.Stu_room 
                FROM 
                    behavior AS a
                INNER JOIN 
                    student AS b
                ON 
                    a.stu_id = b.Stu_id
                WHERE 
                    teach_id = :teacherid 
                    AND behavior_term = :term 
                    AND behavior_pee = :pee 
                ORDER BY 
                    a.behavior_date DESC
            ";

            // Prepare the statement
            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':teacherid', $id, PDO::PARAM_INT);
            $stmt->bindParam(':term', $term, PDO::PARAM_INT);
            $stmt->bindParam(':pee', $pee, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch results
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "success" => true,
                "data" => $result
            ];
        } catch (Exception $e) {
            // Handle errors
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }


    // Method to fetch all behaviors
    public function getAllBehaviors($term, $pee) {
        try {
            $query = "
                SELECT 
                    t1.id, 
                    t2.Stu_id, 
                    t2.Stu_pre, 
                    t2.Stu_name, 
                    t2.Stu_sur, 
                    t2.Stu_major, 
                    t2.Stu_room, 
                    t1.behavior_date, 
                    t1.behavior_type, 
                    t1.behavior_name, 
                    t1.behavior_score,
                    t3.Teach_name AS teacher_behavior
                FROM {$this->table} AS t1
                INNER JOIN {$this->table_data} AS t2 
                    ON t1.stu_id = t2.Stu_id
                INNER JOIN {$this->table_teacher} AS t3
                    ON t1.teach_id = t3.Teach_id
                WHERE t2.Stu_status = 1 AND t1.behavior_term = :term AND t1.behavior_pee = :pee
                ORDER BY t1.behavior_date DESC, 
                         t2.Stu_major ASC, 
                         t2.Stu_room ASC
                LIMIT 1000
                ;
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':term', $term);
            $stmt->bindParam(':pee', $pee);
            $stmt->execute();
            
            // Fetch all matching records
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Return results if found, otherwise return false
            return $stmt->rowCount() > 0 ? $results : false;
        } catch (PDOException $e) {
            // Log error or handle accordingly
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    // Method to fetch all behaviors
    public function getScoreBehaviorsClass($class, $room, $term, $pee) {
        try {
            $query = "
                SELECT 
                    t1.Stu_id, 
                    t1.Stu_pre, 
                    t1.Stu_no, 
                    t1.Stu_name, 
                    t1.Stu_sur, 
                    t1.Stu_major, 
                    t1.Stu_room, 
                    COALESCE(SUM(t2.behavior_score), 0) AS behavior_count
                FROM {$this->table_data} AS t1
                LEFT JOIN {$this->table} AS t2 
                    ON t1.Stu_id = t2.stu_id 
                    AND t2.behavior_term = :term 
                    AND t2.behavior_pee = :pee
                WHERE t1.Stu_major = :class 
                AND t1.Stu_room = :room 
                AND t1.Stu_status = 1
                GROUP BY 
                    t1.Stu_id, 
                    t1.Stu_pre, 
                    t1.Stu_no,
                    t1.Stu_name, 
                    t1.Stu_sur, 
                    t1.Stu_major, 
                    t1.Stu_room
                ORDER BY 
                    t1.Stu_no ASC 
                ;
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class', $class);
            $stmt->bindParam(':room', $room);
            $stmt->bindParam(':term', $term);
            $stmt->bindParam(':pee', $pee);
            $stmt->execute();
            
            // Fetch all matching records
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Return results if found, otherwise return false
            return $stmt->rowCount() > 0 ? $results : false;
        } catch (PDOException $e) {
            // Log error or handle accordingly
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }

    // Method to fetch all behaviors
    public function getScoreBehaviorsGroup($GroupValue, $term, $pee) {
        // กำหนดเงื่อนไขตาม GroupValue
        switch ($GroupValue) {
            // ปรับให้ตรงกับตัวเลือกใน UI:
            // 1 = คะแนนพฤติกรรมต่ำกว่า 50
            // 2 = คะแนนพฤติกรรมระหว่าง 50 - 70
            // 3 = คะแนนพฤติกรรมระหว่าง 71 - 99
            case 1:
                $keysearch = "BETWEEN 1 AND 49";
                break;
            case 2:
                $keysearch = "BETWEEN 50 AND 70";
                break;
            case 3:
                $keysearch = "BETWEEN 71 AND 99";
                break;
            default:
                $keysearch = "";
                break;
        }
    
        try {
            $query = "
                SELECT 
                    t1.Stu_id, 
                    t1.Stu_pre, 
                    t1.Stu_no, 
                    t1.Stu_name, 
                    t1.Stu_sur, 
                    t1.Stu_major, 
                    t1.Stu_room, 
                    COALESCE(SUM(t2.behavior_score), 0) AS behavior_count
                FROM {$this->table_data} AS t1
                LEFT JOIN {$this->table} AS t2 
                    ON t1.Stu_id = t2.stu_id 
                    AND t2.behavior_term = :term 
                    AND t2.behavior_pee = :pee
                WHERE t1.Stu_status = 1
                GROUP BY 
                    t1.Stu_id, 
                    t1.Stu_pre, 
                    t1.Stu_no,
                    t1.Stu_name, 
                    t1.Stu_sur, 
                    t1.Stu_major, 
                    t1.Stu_room
                HAVING behavior_count " . $keysearch . "
                ORDER BY 
                    t1.Stu_major ASC,
                    t1.Stu_room ASC,
                    t1.Stu_no ASC
            ";
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':term', $term);
            $stmt->bindParam(':pee', $pee);
            $stmt->execute();
            
            // Fetch all matching records
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Return results if found, otherwise return false
            return $stmt->rowCount() > 0 ? $results : false;
        } catch (PDOException $e) {
            // Log error or handle accordingly
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    
    // Method to fetch all behaviors
    public function getBehaviorsByStudentId($stu_id, $term, $pee) {
        try {
            $query = "
                SELECT 
                    t1.id, 
                    t2.Stu_id, 
                    t2.Stu_pre, 
                    t2.Stu_name, 
                    t2.Stu_sur, 
                    t2.Stu_major, 
                    t2.Stu_room, 
                    t1.behavior_date, 
                    t1.behavior_type, 
                    t1.behavior_name, 
                    t1.behavior_score,
                    t3.Teach_name AS teacher_behavior
                FROM {$this->table} AS t1
                INNER JOIN {$this->table_data} AS t2 
                    ON t1.stu_id = t2.Stu_id
                INNER JOIN {$this->table_teacher} AS t3
                    ON t1.teach_id = t3.Teach_id
                WHERE  t2.Stu_id = :stdid AND t2.Stu_status = 1 AND t1.behavior_term = :term AND t1.behavior_pee = :pee
                ORDER BY t1.behavior_date DESC, 
                         t2.Stu_major ASC, 
                         t2.Stu_room ASC
                ;
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':stdid', $stu_id);
            $stmt->bindParam(':term', $term);
            $stmt->bindParam(':pee', $pee);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            return $stmt->rowCount() > 0 ? $results : false;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }        
    }
    

    // Method to fetch behavior by ID
    public function getBehaviorById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteBehavior($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteAllBehavior() {
        // SQL query to delete a record based on the ID
        $query = "DELETE FROM {$this->table} WHERE stu_id = :stu_id AND behavior_term = :term AND behavior_pee = :pee";

        // Prepare the query
        $stmt = $this->conn->prepare($query);

        // Bind the ID parameter
        $stmt->bindParam(':stu_id', $this->stu_id);
        $stmt->bindParam(':term', $this->term);
        $stmt->bindParam(':pee', $this->pee);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Method to add a new behavior
    public function addBehavior() {
        $sql = "INSERT INTO {$this->table} (Stu_id, behavior_date, behavior_type, behavior_name, behavior_score, teach_id, behavior_term, behavior_pee)
                VALUES (:stu_id, :behavior_date, :behavior_type, :behavior_name, :behavior_score, :teachid, :term, :pee)";
        $stmt = $this->conn->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':stu_id', $this->stu_id);
        $stmt->bindParam(':behavior_date', $this->behavior_date);
        $stmt->bindParam(':behavior_type', $this->behavior_type);
        $stmt->bindParam(':behavior_name', $this->behavior_name);
        $stmt->bindParam(':behavior_score', $this->behavior_score);
        $stmt->bindParam(':teachid', $this->teach_id);
        $stmt->bindParam(':term', $this->term);
        $stmt->bindParam(':pee', $this->pee);
        
        // Execute the statement
        return $stmt->execute();
    }
    
    

    // Method to update an existing behavior
    public function updateBehavior($id, $student_id, $behavior_type, $behavior_name, $behavior_score, $behavior_date) {
        try {
            $query = "
                UPDATE {$this->table}
                SET stu_id = :student_id,
                    behavior_type = :behavior_type,
                    behavior_name = :behavior_name,
                    behavior_score = :behavior_score,
                    behavior_date = :behavior_date
                WHERE id = :id;
            ";
    
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':behavior_type', $behavior_type);
            $stmt->bindParam(':behavior_name', $behavior_name);
            $stmt->bindParam(':behavior_score', $behavior_score);
            $stmt->bindParam(':behavior_date', $behavior_date);
    
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
}
?>
