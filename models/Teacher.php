<?php
namespace App\Models;

require_once __DIR__ . '/../classes/DatabaseUsers.php';

class Teacher
{
    private $db;  // DatabaseUsers object
    private $pdo; // PDO object

    /**
     * @param \App\DatabaseUsers $db การเชื่อมต่อฐานข้อมูล
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->pdo = $db->getPDO(); // ดึง PDO connection เก็บไว้
    }
    
    /**
     * สำหรับส่งต่อ PDO ไปให้ Logger
     * @return \PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    public function getAll()
    {
        $sql = "SELECT Teach_id, Teach_name, Teach_major, Teach_status, role_std FROM teacher 
                WHERE Teach_status = '1' 
                ORDER BY Teach_major, Teach_name";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM teacher WHERE Teach_id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * !! KEV: แก้ไขเมธอดนี้ (create) !!
     */
    public function create($data)
    {
        // (เพิ่มรหัสผ่าน = Teach_id)
        $data['Teach_password'] = $data['Teach_id']; // $data ตอนนี้มี 6 items
        
        // (SQL มี 6 placeholders)
        $sql = "INSERT INTO teacher 
                    (Teach_id, Teach_name, Teach_major, Teach_status, role_std, Teach_password) 
                VALUES 
                    (:Teach_id, :Teach_name, :Teach_major, :Teach_status, :role_std, :Teach_password)";
        
        // (6 items ตรงกับ 6 placeholders)
        $stmt = $this->db->query($sql, $data);
        return $stmt->rowCount() > 0;
    }

    /**
     * !! KEV: แก้ไขเมธอดนี้ (update) !!
     */
    public function update($id_old, $data_from_controller)
    {
        // $id_old คือ 'editTeach_id_old'
        // $data_from_controller คือ array 6 ตัวจาก Controller
        
        // (SQL มี 6 placeholders)
        $sql = "UPDATE teacher SET 
                    Teach_id = :Teach_id_new,
                    Teach_name = :Teach_name, 
                    Teach_major = :Teach_major, 
                    Teach_status = :Teach_status, 
                    role_std = :role_std 
                WHERE Teach_id = :Teach_id_old";
                
        // (สร้าง array params ที่มี 6 items เป๊ะๆ)
        $params = [
            ':Teach_id_new'  => $data_from_controller['Teach_id_new'],
            ':Teach_name'    => $data_from_controller['Teach_name'],
            ':Teach_major'   => $data_from_controller['Teach_major'],
            ':Teach_status'  => $data_from_controller['Teach_status'],
            ':role_std'      => $data_from_controller['role_std'],
            ':Teach_id_old'  => $id_old // (ใช้ $id_old ที่รับเข้ามา)
        ];

        $stmt = $this->db->query($sql, $params);
        return true; // (คืนค่า true เสมอ)
    }

    public function delete($id)
    {
        // (เปลี่ยนเป็นการอัปเดตสถานะ (Soft Delete) จะปลอดภัยกว่า)
        $sql = "UPDATE teacher SET Teach_status = '0' WHERE Teach_id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * !! KEV: แก้ไขเมธอดนี้ !!
     */
    public function resetPassword($id)
    {
        // รีเซ็ตรหัสผ่านเป็น Teach_id
        $sql = "UPDATE teacher SET Teach_password = :password WHERE Teach_id = :id";
        $stmt = $this->db->query($sql, ['password' => $id, 'id' => $id]);
        
        // (เปลี่ยนจาก rowCount() > 0)
        return true; // <--- แก้ไขจุดนี้
        // คืนค่า true เสมอ ถ้าคำสั่ง UPDATE รันผ่าน (ไม่มี Error)
    }
}
?>