<?php
namespace App\Models;

class BehaviorModel {
    private $db;
    private $pdo;

    /**
     * @param \App\DatabaseUsers $db การเชื่อมต่อฐานข้อมูล
     */
    public function __construct($db) {
        $this->db = $db;
        $this->pdo = $db->getPDO();
    }

    public function getPDO() {
        return $this->pdo;
    }

    /**
     * ดึงข้อมูลพฤติกรรมทั้งหมดในเทอมปัจจุบัน
     */
    public function getAllBehaviors($term, $pee) {
        // (เพิ่ม JOIN ตาราง student)
        $sql = "SELECT b.*, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_no
                FROM behavior b
                JOIN student s ON b.stu_id = s.Stu_id
                WHERE b.behavior_term = :term AND b.behavior_pee = :pee
                ORDER BY b.behavior_date DESC, s.Stu_major, s.Stu_room, s.Stu_no";
        return $this->db->query($sql, ['term' => $term, 'pee' => $pee])->fetchAll();
    }

    /**
     * ดึงข้อมูลพฤติกรรม 1 รายการด้วย ID
     */
    public function getBehaviorById($id) {
        $sql = "SELECT * FROM behavior WHERE id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }
    
    /**
     * (เพิ่ม) ดึงข้อมูลนักเรียนสำหรับแสดง Preview
     */
    public function getStudentPreview($stu_id) {
        $sql = "SELECT Stu_id, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_picture 
                FROM student 
                WHERE Stu_id = :id AND Stu_status = '1'";
        return $this->db->query($sql, ['id' => $stu_id])->fetch();
    }

    /**
     * สร้างรายการพฤติกรรมใหม่
     */
    public function createBehavior($data, $teach_id, $term, $pee) {
        $sql = "INSERT INTO behavior 
                    (stu_id, behavior_date, behavior_type, behavior_name, behavior_score, teach_id, behavior_term, behavior_pee)
                VALUES 
                    (:stu_id, :behavior_date, :behavior_type, :behavior_name, :behavior_score, :teach_id, :term, :pee)";
        
        $params = [
            ':stu_id' => $data['addStu_id'],
            ':behavior_date' => $data['addBehavior_date'],
            ':behavior_type' => $data['addBehavior_type'],
            ':behavior_name' => $data['addBehavior_name'],
            ':behavior_score' => $data['addBehavior_score'],
            ':teach_id' => $teach_id,
            ':term' => $term,
            ':pee' => $pee
        ];
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    /**
     * อัปเดตรายการพฤติกรรม
     */
    public function updateBehavior($id, $data, $teach_id, $term, $pee) {
         $sql = "UPDATE behavior SET
                    stu_id = :stu_id,
                    behavior_date = :behavior_date,
                    behavior_type = :behavior_type,
                    behavior_name = :behavior_name,
                    behavior_score = :behavior_score,
                    teach_id = :teach_id,
                    behavior_term = :term,
                    behavior_pee = :pee
                WHERE id = :id";
        
        $params = [
            ':stu_id' => $data['editStu_id'],
            ':behavior_date' => $data['editBehavior_date'],
            ':behavior_type' => $data['editBehavior_type'],
            ':behavior_name' => $data['editBehavior_name'],
            ':behavior_score' => $data['editBehavior_score'],
            ':teach_id' => $teach_id,
            ':term' => $term,
            ':pee' => $pee,
            ':id' => $id
        ];
        
        $this->db->query($sql, $params);
        return true; 
    }

    /**
     * ลบรายการพฤติกรรม
     */
    public function deleteBehavior($id) {
        $sql = "DELETE FROM behavior WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
?>