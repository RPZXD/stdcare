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

    // --- ADDED: เมธอดสำหรับ DataTables Server-Side Processing (ตาราง RFID) ---
    public function getRfidsForDatatable($params)
    {
        $start = intval($params['start'] ?? 0);
        $length = intval($params['length'] ?? 10);
        $searchValue = $params['search']['value'] ?? '';
        $orderColumnIndex = $params['order'][0]['column'] ?? 0;
        $orderDir = $params['order'][0]['dir'] ?? 'asc';

        // คอลัมน์ที่รองรับการเรียงลำดับ
        // 0=rfid_code, 1=stu_name, 2=stu_major, 3=registered_at
        $columns = ['r.rfid_code', 's.Stu_name', 's.Stu_major', 'r.registered_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 's.Stu_no'; // Default sort

        $baseSql = "FROM student_rfid r LEFT JOIN student s ON r.stu_id = s.Stu_id WHERE s.Stu_status = '1'";
        $bindings = [];

        // --- สร้างเงื่อนไข WHERE ---
        $where = [];
        if (!empty($searchValue)) {
            // ค้นหาจาก รหัส RFID, รหัสนักเรียน, ชื่อ, นามสกุล
            $where[] = "(r.rfid_code LIKE :search_val OR r.stu_id LIKE :search_val OR s.Stu_name LIKE :search_val OR s.Stu_sur LIKE :search_val)";
            $bindings['search_val'] = '%' . $searchValue . '%';
        }
        
        if (count($where) > 0) {
            $baseSql .= " AND " . implode(' AND ', $where);
        }

        // --- 1. นับจำนวนข้อมูลทั้งหมด (ไม่รวมฟิลเตอร์) ---
        $sqlTotal = "SELECT COUNT(r.id) as total FROM student_rfid r LEFT JOIN student s ON r.stu_id = s.Stu_id WHERE s.Stu_status = '1'";
        $recordsTotal = $this->db->query($sqlTotal)->fetch()['total'];

        // --- 2. นับจำนวนข้อมูลที่ผ่านการกรอง (มี WHERE) ---
        $sqlFiltered = "SELECT COUNT(r.id) as total_filtered " . $baseSql;
        $recordsFiltered = $this->db->query($sqlFiltered, $bindings)->fetch()['total_filtered'];

        // --- 3. ดึงข้อมูลจริง (มี WHERE, ORDER BY, LIMIT) ---
        $sqlData = "SELECT r.id, r.stu_id, r.rfid_code, r.registered_at, 
                       s.Stu_no as stu_no, s.Stu_name as stu_name, s.Stu_sur as stu_sur, 
                       s.Stu_major as stu_major, s.Stu_room as stu_room, s.Stu_picture as stu_photo ";
        $sqlData .= $baseSql;
        $sqlData .= " ORDER BY $orderColumn $orderDir, s.Stu_no ASC ";
        $sqlData .= " LIMIT $start, $length";

        $data = $this->db->query($sqlData, $bindings)->fetchAll();
        
        // เติมชื่อเต็ม (ถ้าต้องการ)
        foreach ($data as &$row) {
            $row['stu_name_full'] = trim(($row['stu_name'] ?? '') . ' ' . ($row['stu_sur'] ?? ''));
        }

        // --- 4. จัดรูปแบบผลลัพธ์ ---
        return [
            "draw"            => intval($params['draw'] ?? 0),
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => $data
        ];
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
