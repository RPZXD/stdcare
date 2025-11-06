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
    // (KEV: เพิ่ม Teach_class, Teach_room และ Teach_photo เพื่อให้ View แสดงผลได้ถูกต้อง)
    $sql = "SELECT Teach_id, Teach_name, Teach_major, Teach_class, Teach_room, Teach_photo, Teach_status, role_std FROM teacher 
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
        // Use NULL for empty class/room and do NOT set Teach_photo here (DB stores filename only)
        $sql = "INSERT INTO teacher 
                    (Teach_id, Teach_name, Teach_major, Teach_class, Teach_room, Teach_status, role_std, Teach_password) 
                VALUES 
                    (:Teach_id, :Teach_name, :Teach_major, :Teach_class, :Teach_room, :Teach_status, :role_std, :Teach_password)";

        // Normalize optional values: use NULL for empty
        $data['Teach_class'] = (isset($data['Teach_class']) && $data['Teach_class'] !== '') ? $data['Teach_class'] : null;
        $data['Teach_room'] = (isset($data['Teach_room']) && $data['Teach_room'] !== '') ? $data['Teach_room'] : null;

        $stmt = $this->db->query($sql, [
            ':Teach_id' => $data['Teach_id'],
            ':Teach_name' => $data['Teach_name'],
            ':Teach_major' => $data['Teach_major'],
            ':Teach_class' => $data['Teach_class'],
            ':Teach_room' => $data['Teach_room'],
            ':Teach_status' => $data['Teach_status'],
            ':role_std' => $data['role_std'],
            ':Teach_password' => $data['Teach_password']
        ]);
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
                    Teach_class = :Teach_class,
                    Teach_room = :Teach_room,
                    Teach_status = :Teach_status, 
                    role_std = :role_std 
                WHERE Teach_id = :Teach_id_old";

        // Normalize optional values: use NULL for empty
        $classVal = isset($data_from_controller['Teach_class']) && $data_from_controller['Teach_class'] !== '' ? $data_from_controller['Teach_class'] : null;
        $roomVal = isset($data_from_controller['Teach_room']) && $data_from_controller['Teach_room'] !== '' ? $data_from_controller['Teach_room'] : null;

        $params = [
            ':Teach_id_new'  => $data_from_controller['Teach_id_new'],
            ':Teach_name'    => $data_from_controller['Teach_name'],
            ':Teach_major'   => $data_from_controller['Teach_major'],
            ':Teach_class'   => $classVal,
            ':Teach_room'    => $roomVal,
            ':Teach_status'  => $data_from_controller['Teach_status'],
            ':role_std'      => $data_from_controller['role_std'],
            ':Teach_id_old'  => $id_old // (ใช้ $id_old ที่รับเข้ามา)
        ];

        $stmt = $this->db->query($sql, $params);
        return $stmt !== false; // คืนผลการรัน
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
        // รีเซ็ตรหัสผ่านเป็น Teach_id (ตั้งค่า Teach_password = Teach_id)
        $sql = "UPDATE teacher SET Teach_password = Teach_id WHERE Teach_id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt !== false;
    }

    /**
     * ดึงรายชื่อครูที่ปรึกษาจากระดับชั้นและห้อง
     * (สำหรับใช้ในฟอร์มเช็คชื่อ หรือหน้าแสดงข้อมูลห้องเรียน)
     *
     * @param int $class_level  (เช่น 1, 2, 3... สำหรับ ม.1, ม.2, ม.3)
     * @param int $room_number  (เช่น 1, 2, 3... สำหรับ ห้อง 1, 2, 3)
     * @return array รายชื่อครูที่ยังปฏิบัติงาน (status = 1)
     */
    public function getByClassAndRoom($class_level, $room_number)
    {
        // เราจะดึงเฉพาะครูที่ Teach_status = '1' (ปกติ)
        $sql = "SELECT Teach_id, Teach_name 
                FROM teacher 
                WHERE Teach_class = :class_level 
                  AND Teach_room = :room_number
                  AND Teach_status = '1'
                ORDER BY Teach_name"; // จัดเรียงตามชื่อ
        
        $params = [
            'class_level' => $class_level,
            'room_number' => $room_number
        ];
        
        // ใช้ fetchAll() เพราะห้องหนึ่งอาจมีครูที่ปรึกษา 2 คน
        return $this->db->query($sql, $params)->fetchAll();
    }
    
}
?>