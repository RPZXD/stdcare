<?php
namespace App\Models;

require_once __DIR__ . '/../classes/DatabaseUsers.php';

class StudentRfid
{
    private $db;
    private $pdo;

    /**
     * @param \App\DatabaseUsers $db การเชื่อมต่อฐานข้อมูล
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->pdo = $db->getPDO(); 
    }

    // ... (เมธอด getAll() และ getRfidsForDatatable() เหมือนเดิม) ...
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
    public function getRfidsForDatatable($params)
    {
        // ... (โค้ด SSP เดิม) ...
        $start = intval($params['start'] ?? 0);
        $length = intval($params['length'] ?? 10);
        $searchValue = $params['search']['value'] ?? '';
        $orderColumnIndex = $params['order'][0]['column'] ?? 0;
        $orderDir = $params['order'][0]['dir'] ?? 'ASC';
        $columns = $params['columns'] ?? [];
        
        $orderColumnName = $columns[$orderColumnIndex]['data'] ?? 'stu_no';
        $columnMap = [
            'stu_no' => 's.Stu_no', 'stu_id' => 'r.stu_id', 'stu_name' => 's.Stu_name',
            'stu_major' => 's.Stu_major', 'rfid_code' => 'r.rfid_code', 'registered_at' => 'r.registered_at'
        ];
        $dbOrderColumn = $columnMap[$orderColumnName] ?? 's.Stu_no';

        $baseQuery = "FROM student_rfid r LEFT JOIN student s ON r.stu_id = s.Stu_id";
        $whereClause = " WHERE s.Stu_status = '1'";
        $queryParams = [];

        if (!empty($searchValue)) {
            $whereClause .= " AND (r.stu_id LIKE :search OR s.Stu_name LIKE :search OR s.Stu_sur LIKE :search OR r.rfid_code LIKE :search)";
            $queryParams[':search'] = "%$searchValue%";
        }
        $totalRecordsStmt = $this->pdo->query("SELECT COUNT(r.id) as total $baseQuery WHERE s.Stu_status = '1'");
        $totalRecords = $totalRecordsStmt->fetch()['total'];
        $filteredRecordsStmt = $this->pdo->prepare("SELECT COUNT(r.id) as total $baseQuery $whereClause");
        $filteredRecordsStmt->execute($queryParams);
        $filteredRecords = $filteredRecordsStmt->fetch()['total'];
        $sql = "SELECT r.id, r.stu_id, r.rfid_code, r.registered_at, 
                       s.Stu_no as stu_no, s.Stu_name as stu_name, s.Stu_sur as stu_sur, s.Stu_major as stu_major, s.Stu_room as stu_room, s.Stu_picture as stu_photo
                $baseQuery $whereClause ORDER BY $dbOrderColumn $orderDir LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $length, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $start, \PDO::PARAM_INT);
        foreach ($queryParams as $key => $val) { $stmt->bindValue($key, $val); }
        $stmt->execute();
        $data = $stmt->fetchAll();
        return [
            'draw' => intval($params['draw'] ?? 0), 'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords, 'data' => $data
        ];
    }


    public function getByRfid($rfid_code)
    {
        $sql = "SELECT * FROM student_rfid WHERE rfid_code = :rfid_code";
        $stmt = $this->db->query($sql, ['rfid_code' => $rfid_code]);
        $result = $stmt->fetch();
        return $result ?: null; 
    }

    public function getByStudent($stu_id)
    {
        $sql = "SELECT * FROM student_rfid WHERE stu_id = :stu_id";
        $stmt = $this->db->query($sql, ['stu_id' => $stu_id]);
        $result = $stmt->fetch();
        return $result ?: null; 
    }

    // (แก้ไข) เปลี่ยน register ให้คืนค่า array มาตรฐาน
    public function register($stu_id, $rfid_code)
    {
        if ($this->getByRfid($rfid_code)) {
            return ['status' => 'error', 'message' => 'RFID นี้ถูกใช้แล้ว'];
        }
        if ($this->getByStudent($stu_id)) {
            return ['status' => 'error', 'message' => 'นักเรียนนี้ลงทะเบียน RFID แล้ว'];
        }
        $sql = "INSERT INTO student_rfid (stu_id, rfid_code, registered_at) VALUES (:stu_id, :rfid_code, NOW())";
        $this->db->query($sql, ['stu_id' => $stu_id, 'rfid_code' => $rfid_code]);
        return ['status' => 'success'];
    }

    public function update($id, $rfid_code)
    {
        $exist = $this->getByRfid($rfid_code);
        if ($exist && $exist['id'] != $id) {
            return ['success' => false, 'error' => 'RFID นี้ถูกใช้แล้ว'];
        }
        $sql = "UPDATE student_rfid SET rfid_code = :rfid_code, registered_at = NOW() WHERE id = :id";
        $this->db->query($sql, ['rfid_code' => $rfid_code, 'id' => $id]);
        return ['success' => true];
    }

    //
    // !! KEV: เพิ่มเมธอดนี้ !! (สำหรับอัปเดตจาก CSV)
    //
    public function updateByStudentId($stu_id, $rfid_code)
    {
        // 1. ตรวจสอบว่า RFID ใหม่นี้ ถูกคนอื่นใช้อยู่หรือไม่
        $existingRfid = $this->getByRfid($rfid_code);
        if ($existingRfid && $existingRfid['stu_id'] != $stu_id) {
            return ['status' => 'error', 'message' => 'RFID นี้ถูกใช้โดยคนอื่นแล้ว'];
        }

        // 2. ตรวจสอบว่านักเรียนคนนี้มีบัตรเดิมหรือไม่
        $studentRecord = $this->getByStudent($stu_id);
        if (!$studentRecord) {
             // (ไม่ควรเกิดขึ้น ถ้า Student.php เรียกถูกต้อง)
            return ['status' => 'error', 'message' => 'ไม่พบข้อมูลนักเรียนนี้ในตาราง RFID'];
        }

        // 3. อัปเดตข้อมูล
        $sql = "UPDATE student_rfid SET rfid_code = :rfid_code, registered_at = NOW() WHERE stu_id = :stu_id";
        $this->db->query($sql, ['rfid_code' => $rfid_code, 'stu_id' => $stu_id]);
        return ['status' => 'updated'];
    }

    public function delete($id)
    {
        $sql = "DELETE FROM student_rfid WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
?>