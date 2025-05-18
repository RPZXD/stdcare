<?php
namespace App;

use PDO;
use PDOException;

class DatabaseUsers
{
    private $pdo;

    public function __construct(
        $host = 'localhost',
        $dbname = 'phichaia_student',
        // $username = 'root',
        // $password = ''
        $username = 'phichaia_stdcare',
        $password = '48dv_m64N'
    ) {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดนี้
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception('Database query error: ' . $e->getMessage());
        }
    }

    public function getTeacherByUsername($username)
    {
        $sql = "SELECT * FROM teacher WHERE (Teach_id = :username OR Teach_name = :username) AND Teach_status = '1'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    // เพิ่มเมธอดนี้สำหรับนักเรียน
    public function getStudentByUsername($username)
    {
        $sql = "SELECT * FROM student WHERE (Stu_id = :username OR Stu_name = :username) AND Stu_status = '1'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
