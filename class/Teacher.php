<?php

class Teacher {
    private $conn;
    private $table_name = "teacher";

    public $Teach_id_old;
    public $Teach_id;
    public $Teach_name;
    public $Teach_password;
    public $Teach_major;
    public $Teach_class;
    public $Teach_room;
    public $Teach_status;
    public $role_std;
    public $Teach_phone;
    public $Teach_email;
    public $Teach_Position;
    public $Teach_Academic;
    public $Teach_HiDegree;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function sanitize() {
        foreach (get_object_vars($this) as $key => $value) {
            if (is_string($value)) {
                $this->$key = htmlspecialchars(strip_tags($value));
            }
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET Teach_id=:Teach_id, Teach_name=:Teach_name, Teach_password=:Teach_password, 
                      Teach_major=:Teach_major, Teach_class=:Teach_class, Teach_room=:Teach_room, 
                      Teach_status=:Teach_status, role_std=:role_std";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        // Bind values
        $stmt->bindParam(':Teach_id', $this->Teach_id);
        $stmt->bindParam(':Teach_name', $this->Teach_name);
        $stmt->bindParam(':Teach_password', $this->Teach_password);
        $stmt->bindParam(':Teach_major', $this->Teach_major);
        $stmt->bindParam(':Teach_class', $this->Teach_class);
        $stmt->bindParam(':Teach_room', $this->Teach_room);
        $stmt->bindParam(':Teach_status', $this->Teach_status);
        $stmt->bindParam(':role_std', $this->role_std);

        return $stmt->execute();
    }

    public function update() {
        // กำหนด password เป็นรหัสครู (Teach_id) ทุกครั้งที่แก้ไข
        $this->Teach_password = $this->Teach_id;

        $query = "UPDATE {$this->table_name}
                  SET Teach_id = :Teach_id, 
                      Teach_password = :Teach_password, 
                      Teach_name = :Teach_name, 
                      Teach_major = :Teach_major, 
                      Teach_class = :Teach_class, 
                      Teach_room = :Teach_room, 
                      Teach_status = :Teach_status, 
                      role_std = :role_std 
                  WHERE Teach_id = :Teach_id_old";
    
        $stmt = $this->conn->prepare($query);
    
        $this->sanitize();

        // Bind parameters
        $stmt->bindParam(':Teach_id', $this->Teach_id);
        $stmt->bindParam(':Teach_password', $this->Teach_password);
        $stmt->bindParam(':Teach_name', $this->Teach_name);
        $stmt->bindParam(':Teach_major', $this->Teach_major);
        $stmt->bindParam(':Teach_class', $this->Teach_class);
        $stmt->bindParam(':Teach_room', $this->Teach_room);
        $stmt->bindParam(':Teach_status', $this->Teach_status);
        $stmt->bindParam(':role_std', $this->role_std);
        $stmt->bindParam(':Teach_id_old', $this->Teach_id_old);
    
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE Teach_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->Teach_id);

        return $stmt->execute();
    }

    private function fetchResults($stmt) {
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $stmt->rowCount() > 0 ? $results : false;
    }

    public function userDepartment($department) {
        try {
            $query = "SELECT Teach_id, Teach_name 
                      FROM {$this->table_name} 
                      WHERE Teach_major = :department
                      AND Teach_status = 1
                      ORDER BY Teach_id ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":department", $department);
            return $this->fetchResults($stmt);
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }

    public function userTeacher() {
        try {
            $query = "SELECT * 
                      FROM {$this->table_name}
                      WHERE Teach_status = 1 
                      ORDER BY Teach_id ASC";
            $stmt = $this->conn->prepare($query);
            return $this->fetchResults($stmt);
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }

    public function getTeacherById($teacher_id) {
        try {
            $query = "SELECT * 
                      FROM {$this->table_name} 
                      WHERE Teach_id = :teacher_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":teacher_id", $teacher_id);
            return $this->fetchResults($stmt);
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }

    public function getDepartment() {
        try {
            $query = "SELECT DISTINCT Teach_major FROM {$this->table_name}";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }

    public function updateTeacher($teach_id, $teach_name, $teach_sex, $teach_birth, $teach_addr, $teach_major, $teach_phone, $teach_class, $teach_room, $teach_photo) {
        $query = "UPDATE {$this->table_name} 
                  SET Teach_name = :teach_name, 
                      Teach_sex = :teach_sex, 
                      Teach_birth = :teach_birth, 
                      Teach_addr = :teach_addr, 
                      Teach_major = :teach_major, 
                      Teach_phone = :teach_phone, 
                      Teach_class = :teach_class, 
                      Teach_room = :teach_room, 
                      Teach_photo = :teach_photo 
                  WHERE Teach_id = :teach_id";
    
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':teach_id', $teach_id);
        $stmt->bindParam(':teach_name', $teach_name);
        $stmt->bindParam(':teach_sex', $teach_sex);
        $stmt->bindParam(':teach_birth', $teach_birth);
        $stmt->bindParam(':teach_addr', $teach_addr);
        $stmt->bindParam(':teach_major', $teach_major);
        $stmt->bindParam(':teach_phone', $teach_phone);
        $stmt->bindParam(':teach_class', $teach_class);
        $stmt->bindParam(':teach_room', $teach_room);
        $stmt->bindParam(':teach_photo', $teach_photo);
    
        return $stmt->execute();
    }

    public function getTeachersByClassAndRoom($class, $room) {
        $query = "SELECT Teach_name,Teach_photo FROM {$this->table_name} WHERE Teach_class = :class AND Teach_room = :room AND Teach_status = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':room', $room);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_person($Teach_id, $Teach_name, $Teach_major, $Teach_phone, $Teach_email, $Teach_Position, $Teach_Academic, $Teach_HiDegree) {
        $query = "UPDATE {$this->table_name}
                  SET Teach_name = :Teach_name, 
                      Teach_major = :Teach_major, 
                      Teach_phone = :Teach_phone, 
                      Teach_email = :Teach_email, 
                      Teach_Position = :Teach_Position, 
                      Teach_Academic = :Teach_Academic, 
                      Teach_HiDegree = :Teach_HiDegree 
                  WHERE Teach_id = :Teach_id";
    
        $stmt = $this->conn->prepare($query);
    
        // Bind parameters
        $stmt->bindParam(':Teach_id', $Teach_id);
        $stmt->bindParam(':Teach_name', $Teach_name);
        $stmt->bindParam(':Teach_major', $Teach_major);
        $stmt->bindParam(':Teach_phone', $Teach_phone);
        $stmt->bindParam(':Teach_email', $Teach_email);
        $stmt->bindParam(':Teach_Position', $Teach_Position);
        $stmt->bindParam(':Teach_Academic', $Teach_Academic);
        $stmt->bindParam(':Teach_HiDegree', $Teach_HiDegree);
    
        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error executing update_person query: " . print_r($stmt->errorInfo(), true));
            return false;
        }
    }

    public function getAllMajors() {
        $query = "SELECT DISTINCT Teach_major FROM {$this->table_name}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTeachersByClassAndRoomDuo($class, $room) {
        try {
            $query = "SELECT Teach_name FROM {$this->table_name} 
                      WHERE Teach_class = :class AND Teach_room = :room AND Teach_status = 1";
    
            // Prepare the statement
            $stmt = $this->conn->prepare($query);
    
            // Bind the parameters
            $stmt->bindParam(':class', $class);
            $stmt->bindParam(':room', $room);
    
            // Execute the query
            $stmt->execute();
    
            // Fetch the results
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Return the results
            return $results;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }

    // เพิ่มฟังก์ชัน resetPasswordToId
    public function resetPasswordToId() {
        $query = "UPDATE {$this->table_name} SET password = '' WHERE Teach_id = :Teach_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':Teach_id', $this->Teach_id);
        return $stmt->execute();
    }
}
?>
