<?php
namespace App\Models;

class ParentModel {
    private $db;
    private $pdo;

    /**
     * @param \App\DatabaseUsers $db การเชื่อมต่อฐานข้อมูล
     */
    public function __construct($db) {
        $this->db = $db;
        $this->pdo = $db->getPDO();
    }

    /**
     * ดึงข้อมูลผู้ปกครอง (จากตาราง student) แบบกรองข้อมูล
     */
    public function getParentDataFiltered($class, $room) {
        $sql = "SELECT s.Stu_id, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, 
                       s.Father_name, s.Mother_name, s.Par_name, s.Par_phone 
                FROM student s 
                WHERE s.Stu_status = '1'"; // ดึงเฉพาะนักเรียนที่ยังใช้งาน
        
        $params = [];
        if (!empty($class)) {
            $sql .= " AND s.Stu_major = :class";
            $params[':class'] = $class;
        }
        if (!empty($room)) {
            $sql .= " AND s.Stu_room = :room";
            $params[':room'] = $room;
        }
        $sql .= " ORDER BY s.Stu_major, s.Stu_room, s.Stu_no";
        
        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * ดึงข้อมูลนักเรียน (รวมผู้ปกครอง) ด้วย ID
     */
    public function getParentById($stu_id) {
        $sql = "SELECT * FROM student WHERE Stu_id = :id";
        return $this->db->query($sql, ['id' => $stu_id])->fetch();
    }

    /**
     * อัปเดตข้อมูลผู้ปกครอง (จากฟอร์ม Modal)
     */
    public function updateParentInfo($data) {
        $sql = "UPDATE student SET
                    Father_name = :Father_name, 
                    Father_occu = :Father_occu, 
                    Father_income = :Father_income,
                    Mother_name = :Mother_name, 
                    Mother_occu = :Mother_occu, 
                    Mother_income = :Mother_income,
                    Par_name = :Par_name, 
                    Par_relate = :Par_relate, 
                    Par_occu = :Par_occu,
                    Par_income = :Par_income, 
                    Par_addr = :Par_addr, 
                    Par_phone = :Par_phone
                WHERE Stu_id = :Stu_id";

        $params = [
            ':Father_name' => $data['editFather_name'] ?? null,
            ':Father_occu' => $data['editFather_occu'] ?? null,
            ':Father_income' => empty($data['editFather_income']) ? null : $data['editFather_income'],
            ':Mother_name' => $data['editMother_name'] ?? null,
            ':Mother_occu' => $data['editMother_occu'] ?? null,
            ':Mother_income' => empty($data['editMother_income']) ? null : $data['editMother_income'],
            ':Par_name' => $data['editPar_name'] ?? null,
            ':Par_relate' => $data['editPar_relate'] ?? null,
            ':Par_occu' => $data['editPar_occu'] ?? null,
            ':Par_income' => empty($data['editPar_income']) ? null : $data['editPar_income'],
            ':Par_addr' => $data['editPar_addr'] ?? null,
            ':Par_phone' => $data['editPar_phone'] ?? null,
            ':Stu_id' => $data['editStu_id'] ?? ''
        ];
        
        $this->db->query($sql, $params);
        return true;
    }

    /**
     * อัปเดตข้อมูลผู้ปกครองหลายรายการจาก CSV
     */
    public function batchUpdateParentsCSV($data) {
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        // (คอลัมน์ที่อนุญาตให้อัปเดต)
        $sql = "UPDATE student SET 
                    Father_name = :Father_name, 
                    Father_occu = :Father_occu,
                    Mother_name = :Mother_name, 
                    Mother_occu = :Mother_occu,
                    Par_name = :Par_name, 
                    Par_relate = :Par_relate,
                    Par_addr = :Par_addr, 
                    Par_phone = :Par_phone
                WHERE Stu_id = :Stu_id";
        
        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $row) {
            try {
                $params = [
                    ':Father_name' => $row['Father_name'] ?? null,
                    ':Father_occu' => $row['Father_occu'] ?? null,
                    ':Mother_name' => $row['Mother_name'] ?? null,
                    ':Mother_occu' => $row['Mother_occu'] ?? null,
                    ':Par_name' => $row['Par_name'] ?? null,
                    ':Par_relate' => $row['Par_relate'] ?? null,
                    ':Par_addr' => $row['Par_addr'] ?? null,
                    ':Par_phone' => $row['Par_phone'] ?? null,
                    ':Stu_id' => $row['Stu_id'] ?? ''
                ];
                
                if (empty($params[':Stu_id'])) {
                    $report['failed']++;
                    $report['errors'][] = "ข้อมูลว่าง (Empty Stu_id)";
                    continue;
                }
                
                $stmt->execute($params);
                $report['success']++;
            } catch (\Exception $e) {
                $report['failed']++;
                $report['errors'][] = "Stu_id {$row['Stu_id']}: " . $e->getMessage();
            }
        }
        return $report;
    }
}
?>