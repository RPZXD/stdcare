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
    public function batchUpdateStudentNumbers(array $data, array $header)
    {
        // (ส่วนนี้จากไฟล์ที่คุณส่งมาถูกต้องแล้ว)
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];
        $sql = "UPDATE student SET Stu_number = :number WHERE Stu_id = :id AND Stu_status = '1'";
        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $row) {
            $stu_id = trim($row['Stu_id'] ?? '');
            $stu_number = trim($row['Stu_number'] ?? '');

            if (empty($stu_id) || empty($stu_number)) continue;

            try {
                $stmt->execute([':number' => $stu_number, ':id' => $stu_id]);
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
         // (ส่วนนี้จากไฟล์ที่คุณส่งมาถูกต้องแล้ว)
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        // (โค้ดส่วนนี้ยาวมาก - จะใช้โค้ดเดิมจากไฟล์ที่คุณอัปโหลดมา)
        // ... (ตรรกะการอัปเดตข้อมูลนักเรียนทั้งหมด) ...
        // (ตรวจสอบให้แน่ใจว่าทุกคำสั่ง SQL ใช้ $this->pdo)

        //ตัวอย่าง (หากคุณคัดลอกมาไม่ครบ):
        $sql = "UPDATE student SET Stu_pre = :pre, Stu_name = :name, Stu_sur = :sur, Stu_major = :major, Stu_room = :room, Stu_number = :number
                WHERE Stu_id = :id AND Stu_status = '1'";
        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $row) {
             try {
                $stu_id = trim($row['Stu_id']);
                // (ตรวจสอบข้อมูล $row อื่นๆ)
                
                $params = [
                    ':id' => $stu_id,
                    ':pre' => trim($row['Stu_pre']),
                    ':name' => trim($row['Stu_name']),
                    ':sur' => trim($row['Stu_sur']),
                    ':major' => trim($row['Stu_major']),
                    ':room' => trim($row['Stu_room']),
                    ':number' => trim($row['Stu_number']),
                ];
                $stmt->execute($params);
                $report['success']++;
             } catch (\PDOException $e) {
                 $report['failed']++;
                 $report['errors'][] = "Error at $stu_id: " . $e->getMessage();
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
}