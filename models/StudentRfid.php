<?php
namespace App\Models;

require_once __DIR__ . '/../classes/DatabaseUsers.php';

class StudentRfid
{
    private $db;

    public function __construct()
    {
        $this->db = new \App\DatabaseUsers();
    }

    public function getAll()
    {
        $sql = "SELECT r.id, r.stu_id, r.rfid_code, r.registered_at, 
                       s.Stu_no as stu_no, s.Stu_name as stu_name, s.Stu_sur as stu_sur, s.Stu_major as stu_major, s.Stu_room as stu_room, s.Stu_picture as stu_photo
                FROM student_rfid r
                LEFT JOIN student s ON r.stu_id = s.Stu_id
                WHERE s.Stu_status = '1'
                ORDER BY s.Stu_no ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM student_rfid WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->fetch();
    }

    public function getByRfid($rfid_code)
    {
        $sql = "SELECT * FROM student_rfid WHERE rfid_code = :rfid_code";
        $stmt = $this->db->query($sql, ['rfid_code' => $rfid_code]);
        return $stmt->fetch();
    }

    public function getByStudent($stu_id)
    {
        $sql = "SELECT * FROM student_rfid WHERE stu_id = :stu_id";
        $stmt = $this->db->query($sql, ['stu_id' => $stu_id]);
        $result = $stmt->fetch();
        return $result ?: null; // คืน null แทน false
    }

    public function register($stu_id, $rfid_code)
    {
        // ตรวจสอบซ้ำ
        if ($this->getByRfid($rfid_code)) {
            return ['success' => false, 'error' => 'RFID นี้ถูกใช้แล้ว'];
        }
        if ($this->getByStudent($stu_id)) {
            return ['success' => false, 'error' => 'นักเรียนนี้ลงทะเบียน RFID แล้ว'];
        }
        $sql = "INSERT INTO student_rfid (stu_id, rfid_code, registered_at) VALUES (:stu_id, :rfid_code, NOW())";
        $this->db->query($sql, ['stu_id' => $stu_id, 'rfid_code' => $rfid_code]);
        return ['success' => true];
    }

    public function update($id, $rfid_code)
    {
        // ตรวจสอบซ้ำ
        $exist = $this->getByRfid($rfid_code);
        if ($exist && $exist['id'] != $id) {
            return ['success' => false, 'error' => 'RFID นี้ถูกใช้แล้ว'];
        }
        $sql = "UPDATE student_rfid SET rfid_code = :rfid_code WHERE id = :id";
        $this->db->query($sql, ['rfid_code' => $rfid_code, 'id' => $id]);
        return ['success' => true];
    }

    public function delete($id)
    {
        $sql = "DELETE FROM student_rfid WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
