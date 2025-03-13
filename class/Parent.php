<?php
class StudentParent {
    private $conn;
    private $table_name = "student";

    // Properties for student and parent information
    public $Stu_id;
    public $Father_name;
    public $Father_occu;
    public $Father_income;
    public $Mother_name;
    public $Mother_occu;
    public $Mother_income;
    public $Par_name;
    public $Par_relate;
    public $Par_occu;
    public $Par_income;
    public $Par_addr;
    public $Par_phone;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all parent details
    public function fetchAllParent() {
        $query = "SELECT Stu_id, Stu_no, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, 
                 Father_name, Father_occu, Mother_name, Mother_occu, 
                 Par_name, Par_relate, Par_occu, Par_phone 
                FROM  {$this->table_name}
                WHERE Stu_status = 1
                ORDER BY Stu_major ASC, Stu_room ASC, Stu_no ASC
                LIMIT 2500";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParentById($stu_id) {
        try {
            $query = "SELECT Stu_id, Stu_no, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, 
                         Father_name, Father_occu, Father_income, Mother_name, Mother_occu, Mother_income, 
                         Par_name, Par_relate, Par_occu, Par_income, Par_addr, Par_phone 
            FROM {$this->table_name} 
            WHERE Stu_id = :stu_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":stu_id", $stu_id);
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

    // Update parent information
    public function updateParentInfo() {
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                      Father_name = :Father_name, 
                      Father_occu = :Father_occu, 
                      Father_income = :Father_income, 
                      Mother_name = :Mother_name, 
                      Mother_occu = :Mother_occu, 
                      Mother_income = :Mother_income, 
                      Par_name = :Par_name, 
                      Par_relate = :Par_relate, 
                      Par_occu = :Par_occu, 
                      Par_income = :Par_income, 
                      Par_addr = :Par_addr, 
                      Par_phone = :Par_phone 
                  WHERE Stu_id = :Stu_id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':Father_name', $this->Father_name);
        $stmt->bindParam(':Father_occu', $this->Father_occu);
        $stmt->bindParam(':Father_income', $this->Father_income);
        $stmt->bindParam(':Mother_name', $this->Mother_name);
        $stmt->bindParam(':Mother_occu', $this->Mother_occu);
        $stmt->bindParam(':Mother_income', $this->Mother_income);
        $stmt->bindParam(':Par_name', $this->Par_name);
        $stmt->bindParam(':Par_relate', $this->Par_relate);
        $stmt->bindParam(':Par_occu', $this->Par_occu);
        $stmt->bindParam(':Par_income', $this->Par_income);
        $stmt->bindParam(':Par_addr', $this->Par_addr);
        $stmt->bindParam(':Par_phone', $this->Par_phone);
        $stmt->bindParam(':Stu_id', $this->Stu_id);

        // Execute the query
        return $stmt->execute();
    }

    public function fetchFilteredParents($class = '', $room = '') {
        // Base query with default filters
        $query = "SELECT Stu_id, Stu_no, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, 
                         Father_name, Father_occu, Father_income, Mother_name, Mother_occu, Mother_income, 
                         Par_name, Par_relate, Par_occu, Par_income, Par_addr, Par_phone
                  FROM {$this->table_name} 
                  WHERE Stu_status = 1";
        
        // Add class filter if provided
        if (!empty($class)) {
            $query .= " AND Stu_major = :class";
        }
        
        // Add room filter if provided
        if (!empty($room)) {
            $query .= " AND Stu_room = :room";
        }
        
        // Add ordering
        $query .= " ORDER BY Stu_no ASC";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters if filters are provided
        if (!empty($class)) {
            $stmt->bindParam(':class', $class);
        }
        
        if (!empty($room)) {
            $stmt->bindParam(':room', $room);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
}
?>
