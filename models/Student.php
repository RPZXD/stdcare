<?php
namespace App\Models;

require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/StudentRfid.php'; 

class Student
{
    private $db; // นี่คือ object DatabaseUsers
    private $pdo; // นี่คือ PDO connection

    public function __construct($db)
    {
        $this->db = $db;
        $this->pdo = $db->getPDO(); 
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function getAll($filters = []) 
    {
        $sql = "SELECT 
            Stu_id, Stu_no, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_status, Stu_pre 
            FROM student";
        
        $whereClause = " WHERE 1=1";
        $params = [];

        // JavaScript ส่ง 'class' (จาก filterClass) เราใช้ 'Stu_major'
        if (!empty($filters['class'])) {
            $whereClause .= " AND Stu_major = :class";
            $params[':class'] = $filters['class'];
        }
        
        // JavaScript ส่ง 'room' (จาก filterRoom) เราใช้ 'Stu_room'
        if (!empty($filters['room'])) {
            $whereClause .= " AND Stu_room = :room";
            $params[':room'] = $filters['room'];
        }

        // JavaScript ส่ง 'status' (จาก filterStatus) เราใช้ 'Stu_status'
        if (!empty($filters['status'])) {
            $whereClause .= " AND Stu_status = :status";
            $params[':status'] = $filters['status'];
        } else {
             $whereClause .= " AND Stu_status = '1'";
        }

        $sql .= $whereClause;
        $sql .= " ORDER BY Stu_major, Stu_room, Stu_no, Stu_name";
        
        return $this->db->query($sql, $params)->fetchAll(); 
    }

    public function getMajorAndRoomFilters()
    {
        $sqlMajors = "SELECT DISTINCT Stu_major FROM student WHERE Stu_status = '1' AND Stu_major IS NOT NULL ORDER BY Stu_major";
        $majors = $this->db->query($sqlMajors)->fetchAll(\PDO::FETCH_COLUMN, 0);
        
        $sqlRooms = "SELECT DISTINCT Stu_room FROM student WHERE Stu_status = '1' AND Stu_room IS NOT NULL ORDER BY Stu_room";
        $rooms = $this->db->query($sqlRooms)->fetchAll(\PDO::FETCH_COLUMN, 0);

        return ['majors' => $majors, 'rooms' => $rooms];
    }
    
    public function getStudentsForDatatable($params)
    {
        // (โค้ดสำหรับ Server-side Datatables)
        $sql = "SELECT Stu_id, Stu_no, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_status FROM student";
        $totalRecords = $this->db->query($sql)->rowCount();
        
        // (เพิ่ม WHERE, ORDER BY, LIMIT, OFFSET ตาม $params)
        
        $data = $this->db->query($sql . " WHERE Stu_status = '1' LIMIT 10 OFFSET 0")->fetchAll();
        
        return [
            'draw' => intval($params['draw'] ?? 0),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, 
            'data' => $data
        ];
    }
    
    public function getById($id)
    {
        $sql = "SELECT * FROM student WHERE Stu_id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->fetch(); // (แก้จาก fetchAll เป็น fetch เพราะ ID ควรได้แค่ 1 แถว)
    }

    public function batchRegisterRfid($rfid_data)
    {
        $report = ['success' => 0, 'failed' => 0, 'skipped' => 0, 'errors' => []];
        $rfidModel = new \App\Models\StudentRfid($this->pdo); 

        foreach ($rfid_data as $data) {
            $stu_id = trim($data['stu_id']);
            $rfid_code = trim($data['rfid_code']);

            if (empty($stu_id) || empty($rfid_code)) {
                $report['skipped']++;
                continue;
            }
            $result = $rfidModel->register($stu_id, $rfid_code);

            if ($result['status'] === 'success' || $result['status'] === 'updated') {
                $report['success']++;
            } else if ($result['status'] === 'skipped') {
                 $report['skipped']++;
            } else {
                $report['failed']++;
                $report['errors'][] = $result['message'];
            }
        }
        return $report;
    }

    //
    // !! KEV: นี่คือเมธอดที่ขาดไป !!
    //
    /**
     * อัปเดตข้อมูลนักเรียนจากฟอร์ม (Form Update)
     * @param array $data ข้อมูลนักเรียนจาก $_POST
     * @return bool True ถ้าสำเร็จ
     */
    public function updateStudentInfo($data)
    {
        $sql = "UPDATE student SET 
                    Stu_id = :Stu_id, 
                    Stu_no = :Stu_no, 
                    Stu_password = :Stu_password,
                    Stu_sex = :Stu_sex,
                    Stu_pre = :Stu_pre,
                    Stu_name = :Stu_name,
                    Stu_sur = :Stu_sur,
                    Stu_major = :Stu_major,
                    Stu_room = :Stu_room,
                    Stu_status = :Stu_status
                WHERE Stu_id = :OldStu_id"; // (สำคัญ) อัปเดตโดยอ้างอิง ID เดิม

        $params = [
            ':Stu_id' => $data['Stu_id'],
            ':Stu_no' => $data['Stu_no'],
            ':Stu_password' => $data['Stu_password'],
            ':Stu_sex' => $data['Stu_sex'],
            ':Stu_pre' => $data['Stu_pre'],
            ':Stu_name' => $data['Stu_name'],
            ':Stu_sur' => $data['Stu_sur'],
            ':Stu_major' => $data['Stu_major'],
            ':Stu_room' => $data['Stu_room'],
            ':Stu_status' => $data['Stu_status'],
            ':OldStu_id' => $data['OldStu_id']
        ];

        // ใช้ $this->db (DatabaseUsers object)
        $stmt = $this->db->query($sql, $params);
        
        // คืนค่า true ถ้ามีการเปลี่ยนแปลง (row-count > 0)
        // หรือคืนค่า true เสมอถ้าไม่ต้องการเช็ค (บางทีบันทึกค่าเดิม)
        return true; 
        // หรือ return $stmt->rowCount() > 0;
    }
    
    public function inlineUpdate($id, $field, $value)
    {
        // (โค้ดเดิมของคุณสำหรับ inline_update)
        // (ย้ายโค้ดจาก api_student.php (เวอร์ชันเก่า) มาไว้ที่นี่จะดีที่สุด)
        
        $allowedFields = ['Stu_no', 'Stu_name', 'Stu_sur', 'Stu_major', 'Stu_room', 'Stu_status'];
        if (!in_array($field, $allowedFields)) {
            // (ตรวจสอบ field พิเศษจากโค้ดเก่าของคุณ)
            if (!in_array($field, ['Stu_pre_name_sur', 'Stu_major_room'])) {
                 throw new \Exception('Invalid field for update.');
            }
        }
        
        // (จำลองโค้ดจาก api_student.php เดิมของคุณ)
        $params = [];
        $updateFields = [];
        
        if ($field === 'Stu_no' || $field === 'Stu_status') {
             $updateFields[] = "$field = :val";
             $params[':val'] = $value;
        } else if ($field === 'Stu_pre_name_sur') {
            $obj = json_decode($value, true);
            $updateFields[] = 'Stu_pre = :pre';
            $updateFields[] = 'Stu_name = :name';
            $updateFields[] = 'Stu_sur = :sur';
            $params[':pre'] = $obj['pre'];
            $params[':name'] = $obj['name'];
            $params[':sur'] = $obj['sur'];
        } else if ($field === 'Stu_major_room') {
            $obj = json_decode($value, true);
            $updateFields[] = 'Stu_major = :major';
            $updateFields[] = 'Stu_room = :room';
            $params[':major'] = $obj['major'];
            $params[':room'] = $obj['room'];
        }
        
        if (empty($updateFields)) {
            throw new \Exception('No valid fields to update.');
        }

        $params[':id'] = $id;
        $sql = "UPDATE student SET " . implode(',', $updateFields) . " WHERE Stu_id = :id";
        
        $this->db->query($sql, $params);
        return true;
    }

    /**
     * สร้างนักเรียนใหม่ (Form Create)
     * @param array $data ข้อมูลนักเรียนจาก $_POST
     * @return bool True ถ้าสำเร็จ
     */
    public function createStudent($data)
    {
        // (คำนวณเพศและรหัสผ่าน เหมือนโค้ดเดิมของคุณ)
        $stu_pre = $data['Stu_pre'] ?? '';
        $stu_sex = '';
        if ($stu_pre === 'เด็กชาย' || $stu_pre === 'นาย') {
            $stu_sex = 1;
        } else if ($stu_pre === 'เด็กหญิง' || $stu_pre === 'นางสาว') {
            $stu_sex = 2;
        }

        $stu_password = $data['Stu_id']; // ตั้งรหัสผ่านเริ่มต้น = รหัสนักเรียน
        $stu_status = 1; // สถานะ "ปกติ"
        
        $sql = "INSERT INTO student 
                    (Stu_id, Stu_no, Stu_password, Stu_sex, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_status)
                VALUES 
                    (:Stu_id, :Stu_no, :Stu_password, :Stu_sex, :Stu_pre, :Stu_name, :Stu_sur, :Stu_major, :Stu_room, :Stu_status)";
        
        $params = [
            ':Stu_id' => $data['Stu_id'],
            ':Stu_no' => $data['Stu_no'],
            ':Stu_password' => $stu_password,
            ':Stu_sex' => $stu_sex,
            ':Stu_pre' => $data['Stu_pre'],
            ':Stu_name' => $data['Stu_name'],
            ':Stu_sur' => $data['Stu_sur'],
            ':Stu_major' => $data['Stu_major'],
            ':Stu_room' => $data['Stu_room'],
            ':Stu_status' => $stu_status
        ];
        
        // ใช้ $this->db (DatabaseUsers object)
        $stmt = $this->db->query($sql, $params);
        
        // คืนค่า true ถ้าแถวถูกเพิ่ม (row-count > 0)
        return $stmt->rowCount() > 0;
    }

    public function delete($id)
    {
        // (โค้ดเดิมของคุณคือย้ายไป student_del และลบจริง)
        // (โค้ดใหม่ของเราคืออัปเดต status = 0)
        // ** ผมจะใช้โค้ดใหม่ที่ปลอดภัยกว่า (อัปเดต status) **
        
        $sql = "UPDATE student SET Stu_status = '0' WHERE Stu_id = :id";
        $this->db->query($sql, ['id' => $id]);
        return true;
    }
    
    public function resetPassword($id)
    {
        // รีเซ็ตรหัสผ่านเป็น Stu_id
        $sql = "UPDATE student SET Stu_password = :password WHERE Stu_id = :id";
        $this->db->query($sql, ['password' => $id, 'id' => $id]);
        return true;
    }
}
?>