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
            * 
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
    
    // In a method like getById($id)
        public function getById($id) 
        {
            $sql = "SELECT * FROM student WHERE Stu_id = :id";
            // Change to use your query() method and pass params as the 2nd argument
            $stmt = $this->db->query($sql, ['id' => $id]); // <-- THIS IS THE FIX
            return $stmt->fetch();
        }

   /**
     * ลงทะเบียน หรือ อัปเดต RFID จาก CSV (แบบ Batch)
     * @param array $rfid_data [['stu_id' => '...', 'rfid_code' => '...'], ...]
     * @return array รายงานผล
     */
    public function batchRegisterOrUpdateRfid($rfid_data)
    {
        // (เพิ่ม 'updated' เข้าไปใน report)
        $report = ['success' => 0, 'updated' => 0, 'failed' => 0, 'skipped' => 0, 'errors' => []];
        
        // (ส่ง $this->db (DatabaseUsers object) เข้าไป)
        $rfidModel = new \App\Models\StudentRfid($this->db); 

        foreach ($rfid_data as $data) {
            $stu_id = trim($data['stu_id']);
            $rfid_code = trim($data['rfid_code'] ?? ''); // (รองรับ rfid_code ว่าง)

            // ถ้าไม่มี rfid_code หรือ stu_id ให้ข้าม
            if (empty($stu_id) || empty($rfid_code)) {
                $report['skipped']++;
                continue;
            }

            // (ตรรกะใหม่) ตรวจสอบว่ามีนักเรียนนี้ในตาราง rfid หรือยัง
            $existingRfidRecord = $rfidModel->getByStudent($stu_id);

            if ($existingRfidRecord) {
                // (ถ้ามี) -> ให้อัปเดต
                $result = $rfidModel->updateByStudentId($stu_id, $rfid_code);
                if ($result['status'] === 'updated') {
                    $report['updated']++;
                } else {
                    $report['failed']++;
                    $report['errors'][] = "Stu_id $stu_id: " . ($result['message'] ?? 'Update failed');
                }
            } else {
                // (ถ้าไม่มี) -> ให้ลงทะเบียนใหม่
                $result = $rfidModel->register($stu_id, $rfid_code);
                 if ($result['status'] === 'success') {
                    $report['success']++;
                } else {
                    $report['failed']++;
                    $report['errors'][] = "Stu_id $stu_id: " . ($result['message'] ?? 'Register failed');
                }
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
    
    /**
     * ดึงรายชื่อนักเรียนจากระดับชั้นและห้อง (สำหรับหน้าพิมพ์)
     *
     * @param int $class_level  (เช่น 1, 2, 3...)
     * @param int $room_number  (เช่น 1, 2, 3...)
     * @return array รายชื่อนักเรียนที่ยังเรียนอยู่ (status = 1)
     */
    public function getByClassAndRoom($class_level, $room_number)
    {
        // เลือกฟิลด์ที่จำเป็นสำหรับหน้าพิมพ์
        // และรวมชื่อ-สกุล (CONCAT)
        $sql = "SELECT 
                    Stu_no, 
                    Stu_id, 
                    Stu_pre,
                    CONCAT(Stu_pre, Stu_name, ' ', Stu_sur) AS Stu_name 
                FROM student 
                WHERE Stu_major = :class_level 
                  AND Stu_room = :room_number
                  AND Stu_status = '1'
                ORDER BY Stu_no ASC"; // (สำคัญมาก: ต้องเรียงตามเลขที่)
        
        $params = [
            'class_level' => $class_level,
            'room_number' => $room_number
        ];
        
        // ใช้ fetchAll() เพื่อดึงนักเรียนทั้งหมดในห้อง
        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * (เพิ่มใหม่) ดึงข้อมูลนักเรียนที่ยังไม่มี RFID (สำหรับ DataTables SSP ใน rfid.php)
     * @param array $params (ค่าที่ส่งมาจาก DataTables: draw, start, length, search, major, room)
     * @return array
     */
    public function getStudentsWithoutRfid($params)
    {
        $draw = intval($params['draw'] ?? 0);
        $start = intval($params['start'] ?? 0);
        $length = intval($params['length'] ?? 10);
        $searchValue = $params['search']['value'] ?? '';
        
        // (ฟิลเตอร์ที่ส่งมาจาก rfid.php)
        $major = $params['major'] ?? '';
        $room = $params['room'] ?? '';

        $baseQuery = "FROM student s LEFT JOIN student_rfid r ON s.Stu_id = r.stu_id";
        // (เงื่อนไขหลัก: สถานะ 1 (ปกติ) และยังไม่มีในตาราง rfid)
        $baseWhere = " WHERE s.Stu_status = '1' AND r.id IS NULL"; 
        
        $filterWhere = "";
        $queryParams = [];

        if (!empty($major)) {
            $filterWhere .= " AND s.Stu_major = :major";
            $queryParams[':major'] = $major;
        }
        if (!empty($room)) {
            $filterWhere .= " AND s.Stu_room = :room";
            $queryParams[':room'] = $room;
        }
        if (!empty($searchValue)) {
            $filterWhere .= " AND (s.Stu_id LIKE :search OR s.Stu_name LIKE :search OR s.Stu_sur LIKE :search)";
            $queryParams[':search'] = "%$searchValue%";
        }

        // (นับจำนวนทั้งหมดที่ "ยังไม่มีบัตร" ก่อนการกรองใดๆ)
        $totalRecordsStmt = $this->pdo->query("SELECT COUNT(s.Stu_id) as total $baseQuery $baseWhere");
        $totalRecords = $totalRecordsStmt->fetch()['total'];

        // (นับจำนวนหลังการกรอง (ชั้น/ห้อง/ค้นหา))
        $filteredRecordsStmt = $this->pdo->prepare("SELECT COUNT(s.Stu_id) as total $baseQuery $baseWhere $filterWhere");
        $filteredRecordsStmt->execute($queryParams);
        $filteredRecords = $filteredRecordsStmt->fetch()['total'];

        // (ดึงข้อมูลสำหรับแสดงผล)
        $sql = "SELECT s.Stu_id, s.Stu_no, s.Stu_name, s.Stu_sur
                $baseQuery $baseWhere $filterWhere
                ORDER BY s.Stu_no ASC, s.Stu_id ASC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $length, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $start, \PDO::PARAM_INT);
        foreach ($queryParams as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $data = $stmt->fetchAll();

        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    /**
     * (ใหม่) อัปโหลดหรือลบรูปโปรไฟล์นักเรียน
     * @param string $stu_id รหัสนักเรียน
     * @param bool $delete_image ถ้า true จะลบรูปภาพปัจจุบัน
     * @return array ['success' => bool, 'message' => string]
     */
    public function uploadProfileImage($stu_id, $delete_image = false)
    {
        try {
            // ตรวจสอบว่ามีนักเรียนนี้อยู่หรือไม่
            $student = $this->getById($stu_id);
            if (!$student) {
                return ['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียน'];
            }

            if ($delete_image) {
                // ลบรูปภาพ - ตั้งค่า Stu_picture เป็น NULL หรือ empty string
                $sql = "UPDATE student SET Stu_picture = NULL WHERE Stu_id = :stu_id";
                $this->db->query($sql, ['stu_id' => $stu_id]);
                return ['success' => true, 'message' => 'ลบรูปโปรไฟล์เรียบร้อยแล้ว'];
            }

            // ตรวจสอบไฟล์ที่อัปโหลด
            if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'message' => 'ไม่พบไฟล์รูปภาพที่อัปโหลด'];
            }

            $file = $_FILES['profile_image'];
            
            // ตรวจสอบขนาดไฟล์ (2MB)
            if ($file['size'] > 2 * 1024 * 1024) {
                return ['success' => false, 'message' => 'ไฟล์รูปภาพมีขนาดใหญ่เกิน 2MB'];
            }

            // ตรวจสอบประเภทไฟล์
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง กรุณาอัปโหลดไฟล์ JPG, PNG หรือ GIF เท่านั้น'];
            }

            // สร้างชื่อไฟล์ใหม่ (ใช้รหัสนักเรียน + timestamp + นามสกุลเดิม)
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = $stu_id . '_' . time() . '.' . $fileExtension;

            // โฟลเดอร์สำหรับเก็บรูปภาพ (ในโปรเจ็คนี้)
            $uploadDir = __DIR__ . '/../../photo/';
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadPath = $uploadDir . $newFileName;

            // ย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // อัปเดตฐานข้อมูล
                $sql = "UPDATE student SET Stu_picture = :picture WHERE Stu_id = :stu_id";
                $this->db->query($sql, [
                    'picture' => $newFileName,
                    'stu_id' => $stu_id
                ]);

                return ['success' => true, 'message' => 'อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว'];
            } else {
                return ['success' => false, 'message' => 'ไม่สามารถบันทึกไฟล์รูปภาพได้'];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }
    
    public function updatePhoto($stu_id, $filename) {
        try {
            $query = "UPDATE student SET Stu_picture = :filename WHERE Stu_id = :stu_id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':filename', $filename);
            $stmt->bindParam(':stu_id', $stu_id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error updating photo: " . $e->getMessage());
            return false;
        }
    }
    
}
?>