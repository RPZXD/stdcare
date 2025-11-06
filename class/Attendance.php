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
        // Optimize bulk save: perform one select to detect existing rows,
        // then run a CASE-based UPDATE for existing rows and a multi-row INSERT for new rows.
        if (empty($stu_ids) || !is_array($stu_ids)) return 0;

        // Normalize arrays
        $stu_ids = array_values($stu_ids);
        $statuses = is_array($statuses) ? $statuses : [];
        $reasons = is_array($reasons) ? $reasons : [];

        try {
            $this->conn->beginTransaction();

            // 1) Find which students already have attendance for the date
            $placeholders = implode(',', array_fill(0, count($stu_ids), '?'));
            $selectSql = "SELECT student_id FROM {$this->table_attendance} WHERE attendance_date = ? AND student_id IN ($placeholders)";
            $stmt = $this->conn->prepare($selectSql);
            $params = array_merge([$date], $stu_ids);
            $stmt->execute($params);
            $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $existing = $existing ?: [];

            $toUpdate = array_intersect($stu_ids, $existing);
            $toInsert = array_diff($stu_ids, $existing);

            $now = date('H:i:s');

            // 2) Batch UPDATE existing rows using CASE WHEN to minimize roundtrips
            if (!empty($toUpdate)) {
                $casesStatus = '';
                $casesReason = '';
                $casesChecked = '';
                $inPlaceholders = [];
                $binds = [];
                $i = 0;
                foreach ($toUpdate as $stu) {
                    $i++;
                    $kSid = ':sid_u_' . $i;
                    $kStatus = ':status_u_' . $i;
                    $kReason = ':reason_u_' . $i;
                    $kChecked = ':checked_u_' . $i;
                    $casesStatus .= " WHEN " . $kSid . " THEN " . $kStatus;
                    $casesReason .= " WHEN " . $kSid . " THEN " . $kReason;
                    $casesChecked .= " WHEN " . $kSid . " THEN " . $kChecked;
                    $binds[$kSid] = $stu;
                    $binds[$kStatus] = $statuses[$stu] ?? '1';
                    $binds[$kReason] = $reasons[$stu] ?? null;
                    $binds[$kChecked] = $checked_by;
                    $inPlaceholders[] = $kSid;
                }

                if ($casesStatus) {
                    $updateSql = "UPDATE {$this->table_attendance} SET ";
                    $updateSql .= "attendance_status = CASE student_id " . $casesStatus . " END, ";
                    $updateSql .= "reason = CASE student_id " . $casesReason . " END, ";
                    $updateSql .= "checked_by = CASE student_id " . $casesChecked . " END, ";
                    $updateSql .= "term = :term_val, year = :year_val, attendance_time = :now_val ";
                    $updateSql .= "WHERE attendance_date = :date_val AND student_id IN (" . implode(',', $inPlaceholders) . ")";
                    $uStmt = $this->conn->prepare($updateSql);
                    // bind dynamic binds
                    foreach ($binds as $k => $v) {
                        $uStmt->bindValue($k, $v);
                    }
                    $uStmt->bindValue(':term_val', $term);
                    $uStmt->bindValue(':year_val', $year);
                    $uStmt->bindValue(':now_val', $now);
                    $uStmt->bindValue(':date_val', $date);
                    $uStmt->execute();
                }
            }

            // 3) Batch INSERT new rows
            $inserted = 0;
            if (!empty($toInsert)) {
                $rows = [];
                $binds = [];
                $i = 0;
                foreach ($toInsert as $stu) {
                    $i++;
                    $rows[] = "(:stu_{$i}, :date_{$i}, :time_{$i}, :status_{$i}, :checked_{$i}, :term_{$i}, :year_{$i}, :reason_{$i})";
                    $binds[":stu_{$i}"] = $stu;
                    $binds[":date_{$i}"] = $date;
                    $binds[":time_{$i}"] = date('H:i:s');
                    $binds[":status_{$i}"] = $statuses[$stu] ?? '1';
                    $binds[":checked_{$i}"] = $checked_by;
                    $binds[":term_{$i}"] = $term;
                    $binds[":year_{$i}"] = $year;
                    $binds[":reason_{$i}"] = $reasons[$stu] ?? null;
                }
                $insertSql = "INSERT INTO {$this->table_attendance} (student_id, attendance_date, attendance_time, attendance_status, checked_by, term, year, reason) VALUES " . implode(',', $rows);
                $insStmt = $this->conn->prepare($insertSql);
                foreach ($binds as $k => $v) {
                    $insStmt->bindValue($k, $v);
                }
                $insStmt->execute();
                $inserted = $insStmt->rowCount();
            }

            $this->conn->commit();

            // approximate saved count = updated + inserted
            $updated = isset($toUpdate) ? count($toUpdate) : 0;
            return $updated + (int)$inserted;
        } catch (\Exception $e) {
            try { $this->conn->rollBack(); } catch (\Exception $ex) {}
            // Fallback to safe per-row processing
            $success = 0;
            foreach ($stu_ids as $stu_id) {
                $status = $statuses[$stu_id] ?? '1';
                $reason = $reasons[$stu_id] ?? null;
                $stmt = $this->conn->prepare("SELECT id FROM {$this->table_attendance} WHERE student_id = :stu_id AND attendance_date = :date");
                $stmt->execute([':stu_id' => $stu_id, ':date' => $date]);
                if ($stmt->fetch()) {
                    $stmt2 = $this->conn->prepare("UPDATE {$this->table_attendance} SET attendance_status = :status, reason = :reason, checked_by = :checked_by, term = :term, year = :year WHERE student_id = :stu_id AND attendance_date = :date");
                    $result = $stmt2->execute([':status' => $status, ':reason' => $reason, ':checked_by' => $checked_by, ':term' => $term, ':year' => $year, ':stu_id' => $stu_id, ':date' => $date]);
                } else {
                    $stmt2 = $this->conn->prepare("INSERT INTO {$this->table_attendance} (student_id, attendance_date, attendance_time, attendance_status, checked_by, term, year, reason) VALUES (:stu_id, :date, :time, :status, :checked_by, :term, :year, :reason)");
                    $result = $stmt2->execute([':stu_id' => $stu_id, ':date' => $date, ':time' => date('H:i:s'), ':status' => $status, ':checked_by' => $checked_by, ':term' => $term, ':year' => $year, ':reason' => $reason]);
                }
                if ($result) $success++;
            }
            return $success;
        }
    }
}
