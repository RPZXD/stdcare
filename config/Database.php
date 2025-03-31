<?php 

class Database {
    private $host = "localhost:3306";
    private $username = "root";
    private $password = "";
        
    // private $username = "phichaia_rpz";
    // private $password = "r9u06D#e9";

    private $conn;

    public function __construct(private string $db) {}

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $exception) {
            die("Connection Error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}



?>

