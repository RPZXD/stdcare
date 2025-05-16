<?php

class Student {
    private $conn;
    private $table_student = "student";
    private $table_behavior = "behavior";

    // คุณสมบัติของนักเรียน
    public $Stu_id;
    public $Stu_pre;
    public $Stu_name;
    public $Stu_sur;
    public $Stu_no;
    public $Stu_password;
    public $Stu_sex;
    public $Stu_major;
    public $Stu_room;
    public $Stu_nick;
    public $Stu_birth;
    public $Stu_religion;
    public $Stu_blood;
    public $Stu_addr;
    public $Stu_phone;
    public $OldStu_id;
    public $StuId;
    public $StuNo;
    public $StuPass;
    public $StuSex;
    public $PreStu;
    public $NameStu;
    public $SurStu;
    public $StuClass;
    public $StuRoom;
    public $NickName;
    public $Birth;
    public $Religion;
    public $Blood;
    public $Addr;
    public $Phone;
    public $Status;

    // Add all missing properties to avoid deprecated warnings
    public $Stu_status;
    public $Par_phone;
    public $Stu_citizenid;
    public $Father_name;
    public $Father_occu;
    public $Father_income;
    public $Mother_name;
    public $Mother_occu;
    public $Mother_income;
    public $Par_name;
    public $Par_relate;
    public $Par_occu;
    public $Par_income;
    public $Par_addr;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ฟังก์ชันสำหรับค้นหานักเรียน
    public function searchStudents($keysearch) {
        $query = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur 
                  FROM " . $this->table_student . " 
                  WHERE (Stu_name LIKE :keysearch 
                  OR Stu_sur LIKE :keysearch 
                  OR Stu_id LIKE :keysearch)
                  AND Stu_status = 1
                  LIMIT 10"; // จำกัดจำนวนผลลัพธ์เพื่อประสิทธิภาพ

        // เตรียมคำสั่ง SQL
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keysearch}%";
        $stmt->bindParam(':keysearch', $searchTerm);

        // รันคำสั่ง SQL
        $stmt->execute();

        return $stmt;
    }

    public function create() {
        // SQL query to insert a new record
        $query = "INSERT INTO " . $this->table_student . " 
                  SET 
                      Stu_id = :StuId,
                      Stu_no = :StuNo,
                      Stu_password = :StuPass,
                      Stu_sex = :StuSex,
                      Stu_pre = :PreStu,
                      Stu_name = :NameStu,
                      Stu_sur = :SurStu,
                      Stu_major = :StuClass,
                      Stu_room = :StuRoom,
                      Stu_nick = :NickName,
                      Stu_birth = :Birth,
                      Stu_religion = :Religion,
                      Stu_blood = :Blood,
                      Stu_addr = :Addr,
                      Stu_phone = :Phone,
                      Stu_status = 1";

        // Prepare the query
        $stmt = $this->conn->prepare($query);

        // Bind values to the query
        $stmt->bindParam(":StuId", $this->StuId);
        $stmt->bindParam(":StuNo", $this->StuNo);
        $stmt->bindParam(":StuPass", $this->StuPass);
        $stmt->bindParam(":StuSex", $this->StuSex);
        $stmt->bindParam(":PreStu", $this->PreStu);
        $stmt->bindParam(":NameStu", $this->NameStu);
        $stmt->bindParam(":SurStu", $this->SurStu);
        $stmt->bindParam(":StuClass", $this->StuClass);
        $stmt->bindParam(":StuRoom", $this->StuRoom);
        $stmt->bindParam(":NickName", $this->NickName);
        $stmt->bindParam(":Birth", $this->Birth);
        $stmt->bindParam(":Religion", $this->Religion);
        $stmt->bindParam(":Blood", $this->Blood);
        $stmt->bindParam(":Addr", $this->Addr);
        $stmt->bindParam(":Phone", $this->Phone);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    public function fetchFilteredStudents($class = '', $room = '', $status = '') {
        $query = "SELECT Stu_id, Stu_no, Stu_password, Stu_sex, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, 
                     Stu_nick, Stu_birth, Stu_religion, Stu_blood, Stu_addr, Stu_phone, 
                     Stu_status
                  FROM {$this->table_student} 
                  WHERE 1=1"; // Base query
        
        if (!empty($class)) {
            $query .= " AND Stu_major = :class";
        }
        if (!empty($room)) {
            $query .= " AND Stu_room = :room";
        }
        if (!empty($status)) {
            $query .= " AND Stu_status = :status"; // Add status filter
        }
        
        $query .= " ORDER BY Stu_no ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($class)) {
            $stmt->bindParam(':class', $class);
        }
        if (!empty($room)) {
            $stmt->bindParam(':room', $room);
        }
        if (!empty($status)) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentById($stu_id) {
        try {
            $query = "SELECT * 
            FROM {$this->table_student} 
            WHERE Stu_id = :stu_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":stu_id", $stu_id);
            $stmt->execute();
            
            // Fetch all matching records
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return results if found, otherwise return false
            return $stmt->rowCount() > 0 ? $results : false;
        } catch (PDOException $e) {
            // Log error or handle accordingly
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }

    // ดึงข้อมูลนักเรียนจาก Stu_id (คืนค่าแถวเดียวแบบ associative array)
    public function getStudentDataByStuId($stu_id) {
        $query = "SELECT * FROM {$this->table_student} WHERE Stu_id = :stu_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stu_id', $stu_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStudentInfo() {
        $sql = "UPDATE {$this->table_student}
                SET Stu_id = :stuId,
                    Stu_no = :stuNo,
                    Stu_password = :stuPass,
                    Stu_sex = :stuSex,
                    Stu_pre = :preStu,
                    Stu_name = :nameStu,
                    Stu_sur = :surStu,
                    Stu_major = :stuClass,
                    Stu_room = :stuRoom,
                    Stu_status = :status
                WHERE Stu_id = :oldStuId";

        $stmt = $this->conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':stuId', $this->Stu_id);
        $stmt->bindParam(':stuNo', $this->Stu_no);
        $stmt->bindParam(':stuPass', $this->Stu_password);
        $stmt->bindParam(':stuSex', $this->Stu_sex);
        $stmt->bindParam(':preStu', $this->Stu_pre);
        $stmt->bindParam(':nameStu', $this->Stu_name);
        $stmt->bindParam(':surStu', $this->Stu_sur);
        $stmt->bindParam(':stuClass', $this->Stu_major);
        $stmt->bindParam(':stuRoom', $this->Stu_room);
        $stmt->bindParam(':status', $this->Stu_status); // <-- เพิ่มบรรทัดนี้
        $stmt->bindParam(':oldStuId', $this->OldStu_id);

        return $stmt->execute();
    }

    public function getStudyStatusCount($class, $date) {
        $query = "SELECT st.Study_status, COUNT(*) AS count 
                  FROM {$this->table_study} AS st 
                  INNER JOIN {$this->table_student} AS s ON st.Stu_id = s.Stu_id 
                  WHERE s.Stu_major = :class AND st.Study_date = :date 
                  GROUP BY st.Study_status";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':date', $date);
        $statement->bindParam(':class', $class, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudyStatusCountClassRoom($class, $room, $date) {
        $query = "SELECT st.Study_status, COUNT(*) AS count 
                  FROM {$this->table_study} AS st 
                  INNER JOIN {$this->table_student} AS s ON st.Stu_id = s.Stu_id 
                  WHERE s.Stu_major = :class AND s.Stu_room = :room AND st.Study_date = :date 
                  GROUP BY st.Study_status";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':date', $date);
        $statement->bindParam(':class', $class, PDO::PARAM_INT);
        $statement->bindParam(':room', $room, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudyStatusCountClassRoom2($class, $room, $date) {
        $query = "SELECT st.Study_status, 
                    CASE 
                        WHEN st.Study_status = 1 THEN 'มาเรียน'
                        WHEN st.Study_status = 2 THEN 'ขาดเรียน'
                        WHEN st.Study_status = 3 THEN 'มาสาย'
                        WHEN st.Study_status = 4 THEN 'ลาป่วย'
                        WHEN st.Study_status = 5 THEN 'ลากิจ'
                        WHEN st.Study_status = 6 THEN 'เข้าร่วมกิจกรรม'
                        ELSE 'ไม่ระบุ'
                    END AS status_name,
                    COUNT(*) AS count_total
                  FROM {$this->table_study} AS st 
                  INNER JOIN {$this->table_student} AS s ON st.Stu_id = s.Stu_id 
                  WHERE s.Stu_major = :class AND s.Stu_room = :room AND st.Study_date = :date 
                  GROUP BY st.Study_status";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':date', $date);
        $statement->bindParam(':class', $class, PDO::PARAM_INT);
        $statement->bindParam(':room', $room, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCountClassRoom($class, $room) {
        $query = "SELECT COUNT(*) AS total_count 
                  FROM {$this->table_student}
                  WHERE Stu_major = :class AND Stu_room = :room
                  AND Stu_status = 1
                  ";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':class', $class, PDO::PARAM_INT);
        $statement->bindParam(':room', $room, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $result['total_count'] ?? 0;
    
        return $count === 0 ? "0" : $count;
    }

    public function getStatusCountClassRoom($class, $room, $status, $date) {
        // Ensure $status is an array
        if (!is_array($status)) {
            $status = [$status]; 
        }
        
        // Convert status array into a comma-separated string for SQL
        $placeholders = implode(',', array_fill(0, count($status), '?'));
    
        $query = "SELECT 
                        COUNT(*) AS total_count 
                  FROM student_attendance AS st 
                  INNER JOIN student AS s ON st.student_id = s.Stu_id 
                  WHERE s.Stu_major = ? 
                  AND s.Stu_room = ?
                  AND st.attendance_status IN ($placeholders)
                  AND st.attendance_date = ?";
    
        $statement = $this->conn->prepare($query);
    
        // Bind values
        $params = array_merge([$class, $room], $status, [$date]);
        $statement->execute($params);
    
        // Fetch result
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $result['total_count'] ?? 0;
    
        return $count === 0 ? "0" : $count;
    }

    public function getStudentInfoByRfid($rfid) {
        $query = "SELECT `Stu_id`, `Stu_rfid`, `Stu_no`, `Stu_password`, `Stu_sex`, `Stu_pre`, `Stu_name`, `Stu_sur`, `Stu_major`, `Stu_room`, `Stu_nick`, `Stu_birth`, `Stu_religion`, `Stu_blood`, `Stu_addr`, `Stu_phone`, `Father_name`, `Father_occu`, `Father_income`, `Mother_name`, `Mother_occu`, `Mother_income`, `Par_name`, `Par_relate`, `Par_occu`, `Par_income`, `Par_addr`, `Par_phone`, `Risk_group`, `Stu_picture`, `Stu_status`, `vehicle` 
                  FROM {$this->table_student} 
                  WHERE Stu_rfid = :rfid";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':rfid', $rfid);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getRealTimeStudentInfo($device = '') {
        $query = "SELECT sa.id, sa.Stu_id, sa.Study_date, sa.Study_status, sa.Study_term, sa.Study_pee, sa.device, sa.create_at,
                         s.Stu_rfid, s.Stu_no, s.Stu_password, s.Stu_sex, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, 
                         s.Stu_nick, s.Stu_birth, s.Stu_religion, s.Stu_blood, s.Stu_addr, s.Stu_phone, s.Father_name, s.Father_occu, 
                         s.Father_income, s.Mother_name, s.Mother_occu, s.Mother_income, s.Par_name, s.Par_relate, s.Par_occu, 
                         s.Par_income, s.Par_addr, s.Par_phone, s.Risk_group, s.Stu_picture, s.Stu_status, s.vehicle
                  FROM student_attendance AS sa
                  INNER JOIN student AS s ON sa.Stu_id = s.Stu_id";
        if ($device) {
            $query .= " WHERE sa.device = :device";
        }
        $query .= " ORDER BY sa.create_at DESC LIMIT 1";
        $statement = $this->conn->prepare($query);
        if ($device) {
            $statement->bindParam(':device', $device);
        }
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getDeviceNames() {
        $query = "SELECT DISTINCT device FROM student_attendance";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodayAttendanceRecords() {
        $query = "SELECT sa.id, sa.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, sa.create_at,
                         CONCAT(s.Stu_pre, s.Stu_name, '  ', s.Stu_sur) AS full_name
                  FROM student_attendance AS sa
                  INNER JOIN student AS s ON sa.Stu_id = s.Stu_id
                  WHERE DATE(sa.create_at) = CURDATE()
                  ORDER BY sa.create_at DESC";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function promoteAllStudents() {
        try {
            $this->conn->beginTransaction();
    
            // 1. จบการศึกษานักเรียนที่อยู่ชั้น ม.3 หรือ ม.6 ก่อน (ก่อนเลื่อน)
            $this->conn->query("UPDATE student SET Stu_status = 2 WHERE Stu_major IN (3,6) AND Stu_status = 1");
    
            // 2. เลื่อนชั้นปีให้นักเรียนที่เหลือ
            $this->conn->query("UPDATE student SET Stu_major = Stu_major + 1 WHERE Stu_major IN (1,2,4,5) AND Stu_status = 1");
    
            $this->conn->commit();
            return ['success' => true, 'message' => 'เลื่อนชั้นปีสำเร็จ'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ฟังก์ชันสำหรับนักเรียนแก้ไขข้อมูลตนเอง
    public function updateStudentInfoByStudent($stu_id, $data) {
        $sql = "UPDATE {$this->table_student} SET
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
            Par_phone = :Par_phone
        WHERE Stu_id = :Stu_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':Stu_id', $stu_id);
        $stmt->bindValue(':Stu_sex', $data['Stu_sex']);
        $stmt->bindValue(':Stu_pre', $data['Stu_pre']);
        $stmt->bindValue(':Stu_name', $data['Stu_name']);
        $stmt->bindValue(':Stu_sur', $data['Stu_sur']);
        $stmt->bindValue(':Stu_major', $data['Stu_major']);
        $stmt->bindValue(':Stu_room', $data['Stu_room']);
        $stmt->bindValue(':Stu_nick', $data['Stu_nick']);
        $stmt->bindValue(':Stu_birth', $data['Stu_birth']);
        $stmt->bindValue(':Stu_religion', $data['Stu_religion']);
        $stmt->bindValue(':Stu_blood', $data['Stu_blood']);
        $stmt->bindValue(':Stu_addr', $data['Stu_addr']);
        $stmt->bindValue(':Stu_phone', $data['Stu_phone']);
        $stmt->bindValue(':Father_name', $data['Father_name']);
        $stmt->bindValue(':Father_occu', $data['Father_occu']);
        $stmt->bindValue(':Father_income', $data['Father_income']);
        $stmt->bindValue(':Mother_name', $data['Mother_name']);
        $stmt->bindValue(':Mother_occu', $data['Mother_occu']);
        $stmt->bindValue(':Mother_income', $data['Mother_income']);
        $stmt->bindValue(':Par_name', $data['Par_name']);
        $stmt->bindValue(':Par_relate', $data['Par_relate']);
        $stmt->bindValue(':Par_occu', $data['Par_occu']);
        $stmt->bindValue(':Par_income', $data['Par_income']);
        $stmt->bindValue(':Par_addr', $data['Par_addr']);
        $stmt->bindValue(':Par_phone', $data['Par_phone']);
        return $stmt->execute();
    }

    // คืนค่าเบอร์โทรศัพท์ผู้ปกครองจาก Stu_id
    public function getParentTel($stu_id) {
        $query = "SELECT Par_phone FROM {$this->table_student} WHERE Stu_id = :stu_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stu_id', $stu_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && !empty($row['Par_phone']) ? $row['Par_phone'] : null;
    }

}

?>
