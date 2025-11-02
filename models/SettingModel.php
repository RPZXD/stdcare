<?php
namespace App\Models;

class SettingModel {
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
     * อัปเดตปีการศึกษาและเทอม
     */
    public function updateTermPee($year, $term) {
        $sql = "UPDATE termpee SET pee = ?, term = ? WHERE id = 1";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$year, $term]);
    }

    /**
     * เลื่อนชั้นนักเรียนทั้งหมด
     */
    public function promoteStudents() {
        // (อ้างอิงจากตรรกะใน promote_students.php ของคุณ)
        try {
            $this->pdo->beginTransaction();
            
            // 1. จบการศึกษา ม.3 และ ม.6 (เปลี่ยนสถานะเป็น 2)
            $this->pdo->exec("UPDATE student SET Stu_status = '2' WHERE Stu_major IN (3, 6) AND Stu_status = '1'");
            
            // 2. เลื่อนชั้น ม.4 -> ม.5, ม.5 -> ม.6
            $this->pdo->exec("UPDATE student SET Stu_major = Stu_major + 1 WHERE Stu_major IN (4, 5) AND Stu_status = '1'");
            
            // 3. เลื่อนชั้น ม.1 -> ม.2, ม.2 -> ม.3
            $this->pdo->exec("UPDATE student SET Stu_major = Stu_major + 1 WHERE Stu_major IN (1, 2) AND Stu_status = '1'");

            // 4. ตั้งค่าเลขที่ใหม่เป็น 0 (หรือ null) สำหรับคนที่เลื่อนชั้น
            $this->pdo->exec("UPDATE student SET Stu_no = 0 WHERE Stu_status = '1' AND Stu_major IN (2, 3, 5, 6)");

            $this->pdo->commit();
            return ['success' => true, 'message' => 'เลื่อนชั้นปีนักเรียนสำเร็จ'];
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'การเลื่อนชั้นล้มเหลว: ' . $e->getMessage()];
        }
    }

    /**
     * ดึงข้อมูลนักเรียนสำหรับไฟล์ CSV "อัปเดตเลขที่"
     */
    public function getStudentsForNumberUpdate() {
        $sql = "SELECT Stu_id, Stu_major, Stu_room, Stu_no, CONCAT(Stu_pre, Stu_name, ' ',Stu_sur) AS fullname
                FROM student
                WHERE Stu_status = 1
                ORDER BY Stu_major, Stu_room, Stu_no, Stu_id";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * ดึงข้อมูลนักเรียนทั้งหมด (ตามชั้น/ห้อง) สำหรับไฟล์ CSV "อัปเดตทั้งหมด"
     */
    public function getStudentsForFullUpdate($class, $room) {
        $sql = "SELECT * FROM student WHERE 1=1"; // (ดึงทุก field)
        $params = [];
        if (!empty($class)) {
            $sql .= " AND Stu_major = :class";
            $params[':class'] = $class;
        }
        if (!empty($room)) {
            $sql .= " AND Stu_room = :room";
            $params[':room'] = $room;
        }
        $sql .= " ORDER BY Stu_major, Stu_room, Stu_no";
        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * อัปเดตข้อมูลนักเรียน (ทั้งหมด) จาก CSV
     */
    public function batchUpdateStudentData($csv_data, $all_headers) {
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        // (สร้าง SQL SET clause แบบไดนามิกจาก header)
        $set = [];
        foreach ($all_headers as $field) {
            if ($field !== 'Stu_id') { // ห้ามอัปเดต Stu_id
                $set[] = "$field = :$field";
            }
        }
        $sql = "UPDATE student SET " . implode(', ', $set) . " WHERE Stu_id = :Stu_id";
        $stmt = $this->pdo->prepare($sql);

        foreach ($csv_data as $row) {
            try {
                if (empty($row['Stu_id'])) continue;
                
                $params = [];
                foreach ($all_headers as $field) {
                    // (แปลงค่าว่างเป็น null)
                    $params[$field] = (isset($row[$field]) && $row[$field] !== '') ? $row[$field] : null;
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
    
    /**
     * อัปเดต "เลขที่" นักเรียนจาก CSV
     */
    public function batchUpdateStudentNumbers($csv_data) {
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];
        $sql = "UPDATE student SET Stu_no = :Stu_no WHERE Stu_id = :Stu_id";
        $stmt = $this->pdo->prepare($sql);

        foreach ($csv_data as $row) {
             try {
                $stu_id = $row['Stu_id'] ?? '';
                $stu_no = $row['Stu_no_new'] ?? ''; // (ใช้ 'Stu_no_new' จากเทมเพลต)

                if (empty($stu_id) || $stu_no === '') continue; // (อนุญาตเลขที่ 0)

                $stmt->execute([':Stu_no' => $stu_no, ':Stu_id' => $stu_id]);
                $report['success']++;
             } catch (\Exception $e) {
                $report['failed']++;
                $report['errors'][] = "Stu_id $stu_id: " . $e->getMessage();
             }
        }
        return $report;
    }
}
?>