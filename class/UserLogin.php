<?php 

class UserLogin {
    private $conn;
    private $table_teacher = "teacher";
    private $table_student = "student";
    public $user;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setUsername($user) {
        $this->user = $user;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function userNotExists() {
        $query = "SELECT Teach_id FROM {$this->table_teacher} WHERE Teach_id = :user LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $this->user);
        $stmt->execute();

        return $stmt->rowCount() == 0; // true if user does not exist, false otherwise
    }

    public function verifyPassword() {
        $query = "SELECT Teach_id, Teach_password FROM {$this->table_teacher} WHERE Teach_id = :user LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $this->user);
        $stmt->execute();
    
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $password = $this->password;
            $Confirmpassword = $row['Teach_password'];
            
            if ($password == $Confirmpassword) {
                $_SESSION['user'] = $row['Teach_id'];
                return  $_SESSION['user']; // Return user ID
            } else {
                return false;
            }
        } else {
            echo "ไม่พบผู้ใช้"; // User not found
            return false;
        }
    }

    public function getUserRole() {
        $query = "SELECT role_general FROM {$this->table_teacher} WHERE Teach_id = :user LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $this->user);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['role_general'];
        }
        return null;
    }
    

    public function getUserRoleStudent() {
        $query = "SELECT role FROM {$this->table_student} WHERE Stu_id = :user LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $this->user);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['role'];
        }
        return null;
    }
    

    public function getTerm() {
        $sql = "SELECT term FROM termpee LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn(); // Fetch a single column value
    }
    
    public function getPee() {
        $sql = "SELECT pee FROM termpee LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn(); // Fetch a single column value
    }
    

    public function userData($userid) {
        $query = "SELECT * FROM {$this->table_teacher} WHERE Teach_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userid);
        $stmt->execute();

        return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function logOut() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['Teacher_login']);
        // session_destroy(); // Optional: Uncomment if you want to destroy the session completely
        include_once("Utils.php");
        $sw2 = new SweetAlert2(
            'คุณได้ออกจากระบบแล้ว',
            'success',
            'login.php' // Redirect URL
        );
        $sw2->renderAlert();
        exit;
    }

    public function getAllMajors() {
        $query = "SELECT DISTINCT Teach_major FROM {$this->table_teacher}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
