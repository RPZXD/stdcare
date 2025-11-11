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
        $username_param = null, // --- CHANGED ---
        $password_param = null  // --- CHANGED ---
    ) {
        
        // --- ADDED: Auto-detect environment ---
        // ตรวจสอบว่าเรากำลังรันบน localhost (XAMPP) หรือไม่
        $is_local = in_array(
            $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost', 
            ['localhost', '127.0.0.1']
        ) || php_sapi_name() === 'cli'; // Also consider CLI as local

        if ($is_local) {
            // --- ใช้สำหรับ Localhost (XAMPP) ---
            $username = 'root';
            $password = '';
        } else {
            // --- ใช้สำหรับ Web Hosting (Production) ---
            $username = 'phichaia_stdcare';
            $password = '48dv_m64N';
        }
        
        // ถ้ามีการส่งค่า username/password มาใน constructor (ซึ่งปกติเราไม่ส่ง) ให้ใช้ค่านั้น
        // แต่ถ้าไม่ส่งมา (เป็น null) ให้ใช้ค่าที่เราเพิ่งตั้งค่าด้านบน
        $username = $username_param ?? $username;
        $password = $password_param ?? $password;
        // --- END: Auto-detect environment ---

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // ถ้ายัง Access Denied ให้ตรวจสอบ $username / $password ด้านบน
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