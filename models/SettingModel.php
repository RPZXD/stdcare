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

        foreach ($data as $rowIndex => $row) {
            $stu_id = trim($row['Stu_id'] ?? '');
            $stu_no_new = trim($row['Stu_no_new'] ?? '');

            if (empty($stu_id) || $stu_id === 'ตัวอย่าง') {
                continue; // ข้ามแถวตัวอย่าง
            }

            if (empty($stu_no_new)) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": Stu_no_new ว่างเปล่า (Stu_id: $stu_id)";
                continue;
            }

            // ตรวจสอบว่า Stu_no_new เป็นตัวเลขที่ถูกต้อง
            if (!is_numeric($stu_no_new) || $stu_no_new <= 0) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": Stu_no_new ต้องเป็นตัวเลขที่มากกว่า 0 (Stu_id: $stu_id, ค่า: '$stu_no_new')";
                continue;
            }

            // ตรวจสอบข้อมูลนักเรียนและเลขที่เดิม
            $check_sql = "SELECT Stu_id, Stu_no FROM student WHERE Stu_id = :id AND Stu_status = '1'";
            $check_stmt = $this->pdo->prepare($check_sql);
            $check_stmt->execute([':id' => $stu_id]);
            $student = $check_stmt->fetch();

            if (!$student) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": ไม่พบนักเรียน Stu_id: $stu_id ในระบบ";
                continue;
            }

            try {
                $stmt->execute([':number' => $stu_no_new, ':id' => $stu_id]);
                if ($stmt->rowCount() > 0) {
                    $report['success']++;
                } else {
                    // ถ้าเลขที่เดิมและใหม่เหมือนกัน ไม่นับเป็น error
                    if ($student['Stu_no'] == $stu_no_new) {
                        $report['success']++; // นับเป็น success เพราะข้อมูลถูกต้องอยู่แล้ว
                    } else {
                        $report['failed']++;
                        $report['errors'][] = "แถว " . ($rowIndex + 2) . ": ไม่สามารถอัปเดต Stu_id: $stu_id (อาจมีปัญหาด้านสิทธิ์หรือข้อมูล)";
                    }
                }
            } catch (\PDOException $e) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": Error at Stu_id $stu_id: " . $e->getMessage();
            }
        }
        return $report;
    }

    /**
     * อัปเดตเลขที่นักเรียน แบบรายห้อง (จาก CSV)
     */
    public function batchUpdateStudentNumbersByRoom(array $data, $class, $room)
    {
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];
        $sql = "UPDATE student SET Stu_no = :number WHERE Stu_id = :id AND Stu_status = '1' AND Stu_major = :class AND Stu_room = :room";
        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $rowIndex => $row) {
            $stu_id = trim($row['Stu_id'] ?? '');
            $stu_no_new = trim($row['Stu_no_new'] ?? '');
            $stu_no_old = trim($row['Stu_no_old'] ?? '');
            $stu_major_csv = trim($row['Stu_major'] ?? '');
            $stu_room_csv = trim($row['Stu_room'] ?? '');

            // ตรวจสอบข้อมูลพื้นฐาน
            if (empty($stu_id) || $stu_id === 'ตัวอย่าง') {
                continue; // ข้ามแถวตัวอย่าง
            }

            if (empty($stu_no_new)) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": Stu_no_new ว่างเปล่า (Stu_id: $stu_id)";
                continue;
            }

            // ตรวจสอบว่า Stu_no_new เป็นตัวเลขที่ถูกต้อง
            if (!is_numeric($stu_no_new) || $stu_no_new <= 0) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": Stu_no_new ต้องเป็นตัวเลขที่มากกว่า 0 (Stu_id: $stu_id, ค่า: '$stu_no_new')";
                continue;
            }

            // ตรวจสอบว่าข้อมูลอยู่ในชั้นและห้องที่ถูกต้อง
            $check_sql = "SELECT Stu_id, Stu_major, Stu_room, Stu_no FROM student WHERE Stu_id = :id AND Stu_status = '1'";
            $check_stmt = $this->pdo->prepare($check_sql);
            $check_stmt->execute([':id' => $stu_id]);
            $student = $check_stmt->fetch();

            if (!$student) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": ไม่พบนักเรียน Stu_id: $stu_id ในระบบ";
                continue;
            }

            // ตรวจสอบว่าชั้นและห้องตรงกับที่เลือกหรือไม่
            if ($student['Stu_major'] != $class || $student['Stu_room'] != $room) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": นักเรียน $stu_id อยู่ในชั้น {$student['Stu_major']} ห้อง {$student['Stu_room']} ไม่ตรงกับที่เลือก (ชั้น $class ห้อง $room)";
                continue;
            }

            // ตรวจสอบว่าเลขที่ใหม่ซ้ำหรือไม่ (ในห้องเดียวกัน)
            $duplicate_check_sql = "SELECT Stu_id FROM student WHERE Stu_no = :new_no AND Stu_major = :class AND Stu_room = :room AND Stu_status = '1' AND Stu_id != :id";
            $duplicate_stmt = $this->pdo->prepare($duplicate_check_sql);
            $duplicate_stmt->execute([
                ':new_no' => $stu_no_new,
                ':class' => $class,
                ':room' => $room,
                ':id' => $stu_id
            ]);

            if ($duplicate_stmt->fetch()) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": เลขที่ $stu_no_new ซ้ำในชั้น $class ห้อง $room (Stu_id: $stu_id)";
                continue;
            }

            try {
                $stmt->execute([
                    ':number' => $stu_no_new,
                    ':id' => $stu_id,
                    ':class' => $class,
                    ':room' => $room
                ]);

                if ($stmt->rowCount() > 0) {
                    $report['success']++;
                } else {
                    // ถ้าเลขที่เดิมและใหม่เหมือนกัน ไม่นับเป็น error
                    if ($student['Stu_no'] == $stu_no_new) {
                        $report['success']++; // นับเป็น success เพราะข้อมูลถูกต้องอยู่แล้ว
                    } else {
                        $report['failed']++;
                        $report['errors'][] = "แถว " . ($rowIndex + 2) . ": ไม่สามารถอัปเดต Stu_id: $stu_id (อาจมีปัญหาด้านสิทธิ์หรือข้อมูล)";
                    }
                }
            } catch (\PDOException $e) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": Error at Stu_id $stu_id: " . $e->getMessage();
            }
        }
        return $report;
    }

    /**
     * เพิ่มนักเรียนใหม่แบบ batch จาก CSV
     */
    public function batchInsertStudentData(array $data)
    {
        $report = ['success' => 0, 'failed' => 0, 'errors' => []];

        $sql = "INSERT INTO student
                    (Stu_id, Stu_no, Stu_password, Stu_sex, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_status,
                     Stu_nick, Stu_birth, Stu_religion, Stu_blood, Stu_addr, Stu_phone,
                     Father_name, Father_occu, Father_income, Mother_name, Mother_occu, Mother_income,
                     Par_name, Par_relate, Par_occu, Par_income, Par_addr, Par_phone,
                     Risk_group, vehicle, Stu_citizenid)
                VALUES
                    (:Stu_id, :Stu_no, :Stu_password, :Stu_sex, :Stu_pre, :Stu_name, :Stu_sur, :Stu_major, :Stu_room, :Stu_status,
                     :Stu_nick, :Stu_birth, :Stu_religion, :Stu_blood, :Stu_addr, :Stu_phone,
                     :Father_name, :Father_occu, :Father_income, :Mother_name, :Mother_occu, :Mother_income,
                     :Par_name, :Par_relate, :Par_occu, :Par_income, :Par_addr, :Par_phone,
                     :Risk_group, :vehicle, :Stu_citizenid)";

        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $rowIndex => $row) {
            $stu_id = trim($row['Stu_id'] ?? '');

            // ข้ามแถวตัวอย่าง
            if (empty($stu_id) || $stu_id === 'ตัวอย่าง' || $stu_id === '12345') {
                continue;
            }

            // ตรวจสอบข้อมูลพื้นฐาน
            if (empty($stu_id)) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": Stu_id ว่างเปล่า";
                continue;
            }

            // ตรวจสอบว่านักเรียนมีอยู่แล้วหรือไม่
            $check_sql = "SELECT Stu_id FROM student WHERE Stu_id = :id";
            $check_stmt = $this->pdo->prepare($check_sql);
            $check_stmt->execute([':id' => $stu_id]);
            if ($check_stmt->fetch()) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": นักเรียน Stu_id: $stu_id มีอยู่แล้วในระบบ";
                continue;
            }

            // คำนวณเพศจากคำนำหน้า
            $stu_pre = trim($row['Stu_pre'] ?? '');
            $stu_sex = '';
            if ($stu_pre === 'เด็กชาย' || $stu_pre === 'นาย') {
                $stu_sex = 1;
            } elseif ($stu_pre === 'เด็กหญิง' || $stu_pre === 'นางสาว' || $stu_pre === 'นาง') {
                $stu_sex = 2;
            }

            // เตรียมข้อมูล
            $stu_password = trim($row['Stu_password'] ?? $stu_id); // รหัสผ่านเริ่มต้น = รหัสนักเรียน
            $stu_status = trim($row['Stu_status'] ?? 1); // สถานะ "ปกติ"

            $params = [
                ':Stu_id' => $stu_id,
                ':Stu_no' => trim($row['Stu_no'] ?? null),
                ':Stu_password' => $stu_password,
                ':Stu_sex' => $stu_sex,
                ':Stu_pre' => $stu_pre,
                ':Stu_name' => trim($row['Stu_name'] ?? null),
                ':Stu_sur' => trim($row['Stu_sur'] ?? null),
                ':Stu_major' => trim($row['Stu_major'] ?? null),
                ':Stu_room' => trim($row['Stu_room'] ?? null),
                ':Stu_status' => $stu_status,
                ':Stu_nick' => trim($row['Stu_nick'] ?? null),
                ':Stu_birth' => trim($row['Stu_birth'] ?? null),
                ':Stu_religion' => trim($row['Stu_religion'] ?? null),
                ':Stu_blood' => trim($row['Stu_blood'] ?? null),
                ':Stu_addr' => trim($row['Stu_addr'] ?? null),
                ':Stu_phone' => trim($row['Stu_phone'] ?? null),
                ':Father_name' => trim($row['Father_name'] ?? null),
                ':Father_occu' => trim($row['Father_occu'] ?? null),
                ':Father_income' => trim($row['Father_income'] ?? null),
                ':Mother_name' => trim($row['Mother_name'] ?? null),
                ':Mother_occu' => trim($row['Mother_occu'] ?? null),
                ':Mother_income' => trim($row['Mother_income'] ?? null),
                ':Par_name' => trim($row['Par_name'] ?? null),
                ':Par_relate' => trim($row['Par_relate'] ?? null),
                ':Par_occu' => trim($row['Par_occu'] ?? null),
                ':Par_income' => trim($row['Par_income'] ?? null),
                ':Par_addr' => trim($row['Par_addr'] ?? null),
                ':Par_phone' => trim($row['Par_phone'] ?? null),
                ':Risk_group' => trim($row['Risk_group'] ?? null),
                ':vehicle' => trim($row['vehicle'] ?? null),
                ':Stu_citizenid' => trim($row['Stu_citizenid'] ?? null)
            ];

            try {
                $stmt->execute($params);
                if ($stmt->rowCount() > 0) {
                    $report['success']++;
                } else {
                    $report['failed']++;
                    $report['errors'][] = "แถว " . ($rowIndex + 2) . ": ไม่สามารถเพิ่มนักเรียน Stu_id: $stu_id";
                }
            } catch (\PDOException $e) {
                $report['failed']++;
                $report['errors'][] = "แถว " . ($rowIndex + 2) . ": Error at Stu_id $stu_id: " . $e->getMessage();
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
     * ดึงข้อมูลนักเรียนสำหรับอัปเดตเลขที่ แบบรายห้อง (CSV Template)
     */
    public function getStudentsForNumberUpdateByRoom($class, $room)
    {
        $sql = "SELECT Stu_id, Stu_major, Stu_room, Stu_no, 
                CONCAT(Stu_pre, Stu_name, ' ', Stu_sur) as fullname
                FROM student 
                WHERE Stu_status = '1' 
                AND Stu_major = :class 
                AND Stu_room = :room
                ORDER BY Stu_no ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':class' => $class, ':room' => $room]);
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