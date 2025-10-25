<?php
namespace App\Models;

require_once __DIR__ . '/../classes/DatabaseUsers.php';

class Student
{
    private $db;

    public function __construct()
    {
        $this->db = new \App\DatabaseUsers();
    }

    public function getAll()
    {
        $sql = "SELECT 
            Stu_id, Stu_no, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_status 
            FROM student 
            WHERE Stu_status = '1'
            ORDER BY Stu_major, Stu_room, Stu_no, Stu_name";
        return $this->db->query($sql)->fetchAll();
    }

    // --- ADDED: เมธอดสำหรับดึงข้อมูลฟิลเตอร์ (ระดับชั้น/ห้อง) ---
    public function getMajorAndRoomFilters()
    {
        $sqlMajors = "SELECT DISTINCT Stu_major FROM student WHERE Stu_status = '1' AND Stu_major IS NOT NULL ORDER BY Stu_major";
        $majors = $this->db->query($sqlMajors)->fetchAll(\PDO::FETCH_COLUMN, 0);
        
        $sqlRooms = "SELECT DISTINCT Stu_room FROM student WHERE Stu_status = '1' AND Stu_room IS NOT NULL ORDER BY Stu_room";
        $rooms = $this->db->query($sqlRooms)->fetchAll(\PDO::FETCH_COLUMN, 0);

        return ['majors' => $majors, 'rooms' => $rooms];
    }

    // --- ADDED: เมธอดสำหรับ DataTables Server-Side Processing ---
    public function getStudentsForDatatable($params)
    {
        $start = intval($params['start'] ?? 0);
        $length = intval($params['length'] ?? 10);
        $searchValue = $params['search']['value'] ?? '';
        $orderColumnIndex = $params['order'][0]['column'] ?? 0;
        $orderDir = $params['order'][0]['dir'] ?? 'asc';
        
        $filterMajor = $params['filter_major'] ?? null;
        $filterRoom = $params['filter_room'] ?? null;

        $columns = ['s.Stu_id', 's.Stu_name', 's.Stu_room'];
        $orderColumn = $columns[$orderColumnIndex] ?? 's.Stu_id';

        $baseSql = "FROM student s LEFT JOIN student_rfid r ON s.Stu_id = r.stu_id WHERE s.Stu_status = '1'";
        $bindings = [];
        $where = [];

        if (!empty($filterMajor)) {
            $where[] = "s.Stu_major = :filter_major";
            $bindings['filter_major'] = $filterMajor;
        }
        if (!empty($filterRoom)) {
            $where[] = "s.Stu_room = :filter_room";
            $bindings['filter_room'] = $filterRoom;
        }

        if (!empty($searchValue)) {
            $where[] = "(s.Stu_id LIKE :search_val OR s.Stu_name LIKE :search_val OR s.Stu_sur LIKE :search_val)";
            $bindings['search_val'] = '%' . $searchValue . '%';
        }
        
        if (count($where) > 0) {
            $baseSql .= " AND " . implode(' AND ', $where);
        }

        $sqlTotal = "SELECT COUNT(Stu_id) as total FROM student WHERE Stu_status = '1'";
        $recordsTotal = $this->db->query($sqlTotal)->fetch()['total'];

        $sqlFiltered = "SELECT COUNT(s.Stu_id) as total_filtered " . $baseSql;
        $recordsFiltered = $this->db->query($sqlFiltered, $bindings)->fetch()['total_filtered'];

        $sqlData = "SELECT s.Stu_id, s.Stu_no, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture, r.id as rfid_id ";
        $sqlData .= $baseSql;
        $sqlData .= " ORDER BY $orderColumn $orderDir ";
        $sqlData .= " LIMIT $start, $length";

        $data = $this->db->query($sqlData, $bindings)->fetchAll();

        return [
            "draw"            => intval($params['draw'] ?? 0),
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => $data
        ];
    }

    public function getById($id)
    {
        $sql = "SELECT Stu_id, Stu_no,Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_status FROM student WHERE Stu_id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->fetch();
    }
    
    // ... (เมธอด create, update, delete, resetPassword, getStudentsByRooms, getStudentsByClassAndRooms 
    //      ยังคงเหมือนเดิม ไม่ต้องแก้ไข) ...


    public function create($data)
    {
        $sql = "INSERT INTO student (
            Stu_id, Stu_no, Stu_password, Stu_sex, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_nick, Stu_birth, Stu_religion, Stu_blood, Stu_addr, Stu_phone, 
            Father_name, Father_occu, Father_income, Mother_name, Mother_occu, Mother_income, Par_name, Par_relate, Par_occu, Par_income, Par_addr, Par_phone, 
            Risk_group, Stu_picture, Stu_status, vehicle, Stu_citizenid
        ) VALUES (
            :Stu_id, :Stu_no, :Stu_password, :Stu_sex, :Stu_pre, :Stu_name, :Stu_sur, :Stu_major, :Stu_room, :Stu_nick, :Stu_birth, :Stu_religion, :Stu_blood, :Stu_addr, :Stu_phone, 
            :Father_name, :Father_occu, :Father_income, :Mother_name, :Mother_occu, :Mother_income, :Par_name, :Par_relate, :Par_occu, :Par_income, :Par_addr, :Par_phone, 
            :Risk_group, :Stu_picture, :Stu_status, :vehicle, :Stu_citizenid
        )";
        $this->db->query($sql, $data);
        return true;
    }

    public function update($id, $data)
    {
        $sql = "UPDATE student SET 
            Stu_no = :Stu_no,
            Stu_password = :Stu_password,
            Stu_sex = :Stu_sex,
            Stu_pre = :Stu_pre,
            Stu_name = :Stu_name,
            Stu_sur = :Stu_sur,
            Stu_major = :Stu_major,
            Stu_room = :Stu_room,
            Stu_nick = :Stu_nick,
            Stu_birth = :Stu_birth,
            Stu_religion = :Stu_religion,
            Stu_blood = :Stu_blood,
            Stu_addr = :Stu_addr,
            Stu_phone = :Stu_phone,
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
            Par_phone = :Par_phone,
            Risk_group = :Risk_group,
            Stu_picture = :Stu_picture,
            Stu_status = :Stu_status,
            vehicle = :vehicle,
            Stu_citizenid = :Stu_citizenid
        WHERE Stu_id = :Stu_id";
        $data['Stu_id'] = $id;
        $this->db->query($sql, $data);
        return true;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM student WHERE Stu_id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function resetPassword($id)
    {
        $student = $this->getById($id);
        if (!$student) return false;
        $newPassword = $student['Stu_id'];
        // ถ้าใช้ hash: $newPassword = password_hash($student['Stu_id'], PASSWORD_DEFAULT);
        $sql = "UPDATE student SET Stu_password = :password WHERE Stu_id = :id";
        $this->db->query($sql, ['password' => $newPassword, 'id' => $id]);
        return true;
    }

    /**
     * ดึงรายชื่อนักเรียนตามห้องเรียน (array ของชื่อห้อง)
     * @param array $rooms เช่น ['ห้อง 1', 'ห้อง 2']
     * @return array
     */
    public function getStudentsByRooms($rooms)
    {
        if (empty($rooms)) return [];
        // สมมติว่าชื่อห้องตรงกับฟิลด์ class_room ในตาราง student
        $in = str_repeat('?,', count($rooms) - 1) . '?';
        $sql = "SELECT Stu_id, CONCAT(Stu_pre,Stu_name, ' ', Stu_sur) AS fullname FROM student WHERE Stu_major IN ($in) ORDER BY Stu_room, Stu_id";
        $stmt = $this->db->query($sql, $rooms);
        return $stmt->fetchAll();
    }

    /**
     * ดึงรายชื่อนักเรียนตามระดับชั้นและห้องเรียน (array ของ ['class' => ..., 'room' => ...])
     * @param array $classRooms เช่น [['class' => '1', 'room' => '1'], ...]
     * @return array
     */
    public function getStudentsByClassAndRooms($classRooms)
    {
        if (empty($classRooms)) return [];
        $where = [];
        $params = [];
        foreach ($classRooms as $cr) {
            // ปรับชื่อฟิลด์ให้ตรงกับฐานข้อมูลจริง
            // สมมติใช้ Stu_level (หรือ Stu_major) แทน Stu_class และ Stu_room
            $where[] = '(Stu_major = ? AND Stu_room = ?)';
            $params[] = $cr['class'];
            $params[] = $cr['room'];
        }
        $sql = "SELECT Stu_id, Stu_major, Stu_room, CONCAT(Stu_pre,Stu_name, ' ', Stu_sur) AS fullname 
                FROM student 
                WHERE (" . implode(' OR ', $where) . ") AND Stu_status = '1'
                ORDER BY Stu_major, Stu_room, Stu_id";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
}
