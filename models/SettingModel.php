<?php
namespace App\Models;

use PDO; // <-- ตรวจสอบว่ามี use PDO;

class SettingModel 
{
    // เราต้องการแค่ตัวแปรเดียวสำหรับ PDO
    private $pdo; 

    /**
     * @param PDO $db (แก้ไข) รับ PDO object เข้ามาโดยตรง
     */
    public function __construct(PDO $db) 
    {
        // (แก้ไข) กำหนดค่า $db (ที่เป็น PDO) ให้กับ $this->pdo โดยตรง
        $this->pdo = $db;
        
        // (ลบ) บรรทัดที่มีปัญหา $this->pdo = $db->getPDO(); ออกไป
    }

    /**
     * อัปเดตปีการศึกษาและเทอม
     */
    public function updateTermPee($year, $term) {
        $sql = "UPDATE termpee SET pee = ?, term = ? WHERE id = 1";
        // ใช้ $this->pdo ในการ query
        $stmt = $this->pdo->prepare($sql); 
        return $stmt->execute([$year, $term]);
    }

    /**
     * เลื่อนชั้นนักเรียนทั้งหมด
     */
    public function promoteStudents() {
        try {
            $this->pdo->beginTransaction();
            
            // 1. จบการศึกษา ม.3 และ ม.6 (เปลี่ยนสถานะเป็น 2)
            $this->pdo->exec("UPDATE student SET Stu_status = '2' WHERE Stu_major IN (3, 6) AND Stu_status = '1'");
            
            // 2. เลื่อนชั้น ม.4 -> ม.5, ม.5 -> ม.6
            $this->pdo->exec("UPDATE student SET Stu_major = Stu_major + 1 WHERE Stu_major IN (4, 5) AND Stu_status = '1'");
            
            // 3. เลื่อนชั้น ม.1 -> ม.2, ม.2 -> ม.3
            $this->pdo->exec("UPDATE student SET Stu_major = Stu_major + 1 WHERE Stu_major IN (1, 2) AND Stu_status = '1'");
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'เลื่อนชั้นนักเรียนสำเร็จ'];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * อัปเดตเลขที่นักเรียน (จาก CSV)
     */
    public function batchUpdateStudentNumbers(array $data)
    {
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];
        $sql = "UPDATE student SET Stu_no = :number WHERE Stu_id = :id AND Stu_status = '1'";
        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $row) {
            $stu_id = trim($row['Stu_id'] ?? '');
            $stu_no_new = trim($row['Stu_no_new'] ?? '');

            if (empty($stu_id) || empty($stu_no_new)) {
                $report['failed']++;
                $report['errors'][] = "ข้อมูลไม่ครบ: Stu_id=$stu_id";
                continue;
            }

            try {
                $stmt->execute([':number' => $stu_no_new, ':id' => $stu_id]);
                if ($stmt->rowCount() > 0) {
                    $report['success']++;
                } else {
                    $report['failed']++;
                    $report['errors'][] = "ไม่พบ Stu_id: $stu_id";
                }
            } catch (\PDOException $e) {
                $report['failed']++;
                $report['errors'][] = "Error at $stu_id: " . $e->getMessage();
            }
        }
        return $report;
    }

    /**
     * อัปเดตข้อมูลนักเรียนทั้งหมด (จาก CSV)
     */
    public function batchUpdateStudentData(array $data, array $header)
    {
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        // Build dynamic UPDATE query based on CSV headers
        $allowedFields = [
            'Stu_id', 'Stu_citizenid', 'Stu_no', 'Stu_password', 'Stu_sex', 
            'Stu_pre', 'Stu_name', 'Stu_sur', 'Stu_nick', 'Stu_birth', 
            'Stu_religion', 'Stu_blood', 'Stu_phone', 'Stu_addr', 'vehicle',
            'Stu_major', 'Stu_room', 'Stu_status', 'Risk_group',
            'Father_name', 'Father_occu', 'Father_income',
            'Mother_name', 'Mother_occu', 'Mother_income',
            'Par_name', 'Par_relate', 'Par_occu', 'Par_income', 'Par_phone', 'Par_addr'
        ];

        foreach ($data as $row) {
            try {
                $stu_id = trim($row['Stu_id'] ?? '');
                
                if (empty($stu_id)) {
                    $report['failed']++;
                    $report['errors'][] = "ข้อมูลไม่มี Stu_id";
                    continue;
                }

                // Build SET clause dynamically
                $setFields = [];
                $params = [':id' => $stu_id];
                
                foreach ($header as $field) {
                    if ($field === 'Stu_id') continue; // Skip primary key
                    if (in_array($field, $allowedFields) && isset($row[$field])) {
                        $setFields[] = "$field = :$field";
                        $params[":$field"] = trim($row[$field]);
                    }
                }

                if (empty($setFields)) {
                    $report['failed']++;
                    $report['errors'][] = "ไม่มีฟิลด์ที่จะอัปเดตสำหรับ Stu_id: $stu_id";
                    continue;
                }

                $sql = "UPDATE student SET " . implode(', ', $setFields) . " WHERE Stu_id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                
                if ($stmt->rowCount() > 0) {
                    $report['success']++;
                } else {
                    $report['failed']++;
                    $report['errors'][] = "ไม่พบหรือไม่มีการเปลี่ยนแปลง Stu_id: $stu_id";
                }
                
            } catch (\PDOException $e) {
                $report['failed']++;
                $report['errors'][] = "Error at " . ($stu_id ?? 'unknown') . ": " . $e->getMessage();
            }
        }
        
        return $report;
    }

    /**
     * ดึงการตั้งค่าเวลาทั้งหมด
     */
    public function getAllTimeSettings()
    {
        try {
            // (แก้ไข) ใช้ $this->pdo
            $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM time_settings");
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * อัปเดตการตั้งค่าเวลา
     */
    public function updateTimeSettings(array $settings)
    {
        $sql = "INSERT INTO time_settings (setting_key, setting_value) 
                VALUES (:key, :value) 
                ON DUPLICATE KEY UPDATE setting_value = :value";
        
        // (แก้ไข) ใช้ $this->pdo
        $stmt = $this->pdo->prepare($sql); 

        foreach ($settings as $key => $value) {
            $allowed_keys = [
                'arrival_late_time', 
                'arrival_absent_time', 
                'leave_early_time', 
                'scan_crossover_time'
            ];
            
            if (in_array($key, $allowed_keys) && !empty($value)) {
                $stmt->execute([':key' => $key, ':value' => $value]);
            }
        }
        return true;
    }

    /**
     * ดึงข้อมูลนักเรียนสำหรับอัปเดตเลขที่ (CSV Template)
     */
    public function getStudentsForNumberUpdate()
    {
        $sql = "SELECT Stu_id, Stu_major, Stu_room, Stu_no, 
                CONCAT(Stu_pre, Stu_name, ' ', Stu_sur) as fullname
                FROM student 
                WHERE Stu_status = '1'
                ORDER BY Stu_major, Stu_room, Stu_no";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ดึงข้อมูลนักเรียนทั้งหมดสำหรับอัปเดต (CSV Template)
     */
    public function getStudentsForFullUpdate($class = '', $room = '')
    {
        $sql = "SELECT * FROM student WHERE Stu_status = '1'";
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
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}   