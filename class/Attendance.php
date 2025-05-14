<?php

class Attendance {
    private $conn;
    private $table_student = "student";
    private $table_attendance = "student_attendance";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * ดึงข้อมูลนักเรียนพร้อมสถานะการเช็คชื่อ (LEFT JOIN)
     * @param string $date วันที่ต้องการเช็คชื่อ (Y-m-d)
     * @param int|null $class เลขชั้น (Stu_major) ถ้าไม่ระบุจะดึงทุกชั้น
     * @param int|null $room เลขห้อง (Stu_room) ถ้าไม่ระบุจะดึงทุกห้อง
     * @param string|null $term ภาคเรียนที่ (optional)
     * @param string|null $pee ปีการศึกษา (optional)
     * @return array
     */
    public function getStudentsWithAttendance($date, $class = null, $room = null, $term = null, $pee = null) {
        $query = "SELECT 
                    s.Stu_id, s.Stu_no, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_status,
                    a.id AS attendance_id, a.attendance_date, a.attendance_status, a.term, a.year, a.checked_by, a.device_id, a.reason, a.attendance_time
                  FROM {$this->table_student} s
                  LEFT JOIN {$this->table_attendance} a
                    ON s.Stu_id = a.student_id AND a.attendance_date = :date";
        $params = [':date' => $date];

        $where = " WHERE s.Stu_status = 1";
        if (!is_null($class)) {
            $where .= " AND s.Stu_major = :class";
            $params[':class'] = $class;
        }
        if (!is_null($room)) {
            $where .= " AND s.Stu_room = :room";
            $params[':room'] = $room;
        }
        $query .= $where;

        $query .= " ORDER BY s.Stu_no ASC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // เพิ่ม term และ pee ให้กับแต่ละแถว (สำหรับใช้ในฟอร์ม)
        if ($term !== null && $pee !== null) {
            foreach ($result as &$row) {
                $row['term'] = $term;
                $row['year'] = $pee;
            }
        }

        return $result;
    }

    /**
     * Save or update attendance for multiple students (bulk)
     * @param array $stu_ids
     * @param array $statuses
     * @param array $reasons
     * @param string $date
     * @param string $term
     * @param string $year
     * @param string $checked_by
     * @return int จำนวนที่สำเร็จ
     */
    public function saveAttendanceBulk($stu_ids, $statuses, $reasons, $date, $term, $year, $checked_by = 'system') {
        $success = 0;
        foreach ($stu_ids as $stu_id) {
            $status = $statuses[$stu_id] ?? '1';
            $reason = $reasons[$stu_id] ?? null;

            // ตรวจสอบว่ามี record อยู่แล้วหรือยัง
            $stmt = $this->conn->prepare("SELECT id FROM {$this->table_attendance} WHERE student_id = :stu_id AND attendance_date = :date");
            $stmt->execute([':stu_id' => $stu_id, ':date' => $date]);
            if ($stmt->fetch()) {
                // อัปเดต
                $stmt2 = $this->conn->prepare("UPDATE {$this->table_attendance} SET attendance_status = :status, reason = :reason, checked_by = :checked_by, term = :term, year = :year WHERE student_id = :stu_id AND attendance_date = :date");
                $result = $stmt2->execute([
                    ':status' => $status,
                    ':reason' => $reason,
                    ':checked_by' => $checked_by,
                    ':term' => $term,
                    ':year' => $year,
                    ':stu_id' => $stu_id,
                    ':date' => $date
                ]);
            } else {
                // เพิ่มใหม่
                $stmt2 = $this->conn->prepare("INSERT INTO {$this->table_attendance} (student_id, attendance_date, attendance_status, reason, checked_by, term, year) VALUES (:stu_id, :date, :status, :reason, :checked_by, :term, :year)");
                $result = $stmt2->execute([
                    ':stu_id' => $stu_id,
                    ':date' => $date,
                    ':status' => $status,
                    ':reason' => $reason,
                    ':checked_by' => $checked_by,
                    ':term' => $term,
                    ':year' => $year
                ]);
            }
            if ($result) $success++;
        }
        return $success;
    }
}
