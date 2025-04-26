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
        $query = "SELECT Teach_id, password FROM {$this->table_teacher} WHERE Teach_id = :user LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $this->user);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $password = $this->password;
            $storedPassword = $row['password'];

            if (empty($storedPassword)) {
                // Redirect to password change page if password field is empty
                $_SESSION['user'] = $row['Teach_id'];
                
                header("Location: change_password.php");
                exit();
            }

            if (password_verify($password, $storedPassword)) {
                $_SESSION['user'] = $row['Teach_id'];
                return $_SESSION['user']; // Return user ID
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
    

    // เปลี่ยน getUserRoleStudent ให้คืน Stu_status
    public function getUserRoleStudent() {
        $query = "SELECT Stu_status FROM {$this->table_student} WHERE Stu_id = :user LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $this->user);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['Stu_status'];
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
        unset($_SESSION['Admin_login']);
        unset($_SESSION['Officer_login']);
        unset($_SESSION['Director_login']);
        unset($_SESSION['Group_leader_login']);
        unset($_SESSION['Student_login']);
        session_write_close(); // Ensure session data is saved and session is properly closed
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


    public function studentNotExists() {
        $query = "SELECT Stu_id FROM {$this->table_student} WHERE Stu_id = :user LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $this->user);
        $stmt->execute();
        return $stmt->rowCount() == 0;
    }

    public function verifyStudentPassword() {
        $query = "SELECT Stu_id, Stu_password FROM {$this->table_student} WHERE Stu_id = :user LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $this->user);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $password = $this->password;
            $storedPassword = $row['Stu_password'];

            if (empty($storedPassword)) {
                // Redirect to password change page if password field is empty
                $_SESSION['user'] = $row['Stu_id'];
                header("Location: change_password.php");
                exit();
            }

            // For students, assume password is stored as plain text or hashed (adjust as needed)
            if ($password === $storedPassword || password_verify($password, $storedPassword)) {
                $_SESSION['user'] = $row['Stu_id'];
                return $_SESSION['user'];
            } else {
                return false;
            }
        } else {
            echo "ไม่พบนักเรียน"; // Student not found
            return false;
        }
    }
}
?>
