<?php 

    class Database_User {
        private $host = "localhost:3306";
        private $db = "phichaia_student";
        private $username = "root";
        private $password = "";
        public $conn;

        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO("mysql:host=". $this->host . ";dbname=" . $this->db, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->exec("set names utf8");
            } catch(PDOException $exception) {
                echo "Connection Error: " . $exception->getMessage();
            }

            return $this->conn;
        }
    }


?>