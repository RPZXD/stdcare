<?php
namespace App\Models;

use PDO;

class AttendanceModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * ดึงประวัติการสแกนวันนี้ทั้งหมด (แทน get_attendance.php)
     */
    public function getTodayAttendanceLog(array $timeSettings)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT
                    l.student_id,
                    DATE_FORMAT(l.scan_timestamp, '%H:%i:%s') AS time,
                    s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room,
                    CASE
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) <= :arrival_late THEN '<span class=\\\"inline-block px-2 py-1 bg-green-200 rounded text-green-700\\\">มาเรียน</span>'
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_late AND TIME(l.scan_timestamp) <= :arrival_absent THEN '<span class=\\\"inline-block px-2 py-1 bg-yellow-200 rounded text-yellow-700\\\">มาสาย</span>'
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_absent THEN '<span class=\\\"inline-block px-2 py-1 bg-red-200 rounded text-red-700\\\">ขาดเรียน</span>'
                        WHEN l.scan_type = 'leave' AND TIME(l.scan_timestamp) < :leave_early THEN '<span class=\\\"inline-block px-2 py-1 bg-blue-200 rounded text-blue-700\\\">กลับก่อน</span>'
                        WHEN l.scan_type = 'leave' THEN '<span class=\\\"inline-block px-2 py-1 bg-purple-200 rounded text-purple-700\\\">กลับปกติ</span>'
                        ELSE 'ไม่ระบุ'
                    END as status
                FROM attendance_log l
                INNER JOIN student s ON l.student_id = s.Stu_id
                WHERE DATE(l.scan_timestamp) = :today
                ORDER BY l.scan_timestamp DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':today' => $today,
            ':arrival_late' => $timeSettings['arrival_late_time'] ?? '08:00:00',
            ':arrival_absent' => $timeSettings['arrival_absent_time'] ?? '10:00:00',
            ':leave_early' => $timeSettings['leave_early_time'] ?? '15:40:00',
        ]);
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // จัดรูปแบบข้อมูลสำหรับ DataTables
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'student_id' => $row['student_id'],
                'fullname'   => $row['Stu_pre'].$row['Stu_name'] . '  ' . $row['Stu_sur'],
                'class'      => $row['Stu_major'] . '/' . $row['Stu_room'],
                'time'       => $row['time'],
                'status'     => $row['status'],
            ];
        }
        return $data;
    }

    /**
     * ดึงข้อมูลการสแกนล่าสุด (แทน last_scan.php)
     */
    public function getLastScan(array $timeSettings)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT
                    l.student_id,
                    DATE_FORMAT(l.scan_timestamp, '%H:%i:%s') AS time,
                    s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture,
                    CASE
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) <= :arrival_late THEN 'มาเรียน'
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_late AND TIME(l.scan_timestamp) <= :arrival_absent THEN 'มาสาย'
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_absent THEN 'ขาดเรียน'
                        WHEN l.scan_type = 'leave' AND TIME(l.scan_timestamp) < :leave_early THEN 'กลับก่อน'
                        WHEN l.scan_type = 'leave' THEN 'กลับปกติ'
                        ELSE 'ไม่ระบุ'
                    END as status
                FROM attendance_log l
                INNER JOIN student s ON l.student_id = s.Stu_id
                WHERE DATE(l.scan_timestamp) = :today
                ORDER BY l.scan_timestamp DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':today' => $today,
            ':arrival_late' => $timeSettings['arrival_late_time'] ?? '08:00:00',
            ':arrival_absent' => $timeSettings['arrival_absent_time'] ?? '10:00:00',
            ':leave_early' => $timeSettings['leave_early_time'] ?? '15:40:00',
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                'student_id' => $row['student_id'],
                'fullname'   => $row['Stu_name'] . ' ' . $row['Stu_sur'],
                'class'      => $row['Stu_major'] . '/' . $row['Stu_room'],
                'photo'      => !empty($row['Stu_picture']) ? 'https://std.phichai.ac.th/photo/' . $row['Stu_picture'] : 'assets/images/profile.png',
                'time'       => $row['time'],
                'status'     => $row['status'],
            ];
        }
        return null;
    }

    /**
     * ประมวลผลการสแกน RFID (แทน rfid_scan.php)
     */
    public function processRfidScan($rfid, $device_id, array $timeSettings, $term, $year)
    {
        // 1. ค้นหานักเรียน
        $stmt = $this->db->prepare(
            "SELECT s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture, s.Stu_status
             FROM student_rfid r
             INNER JOIN student s ON r.stu_id = s.Stu_id
             WHERE r.rfid_code = :rfid AND s.Stu_status = '1'"
        );
        $stmt->execute([':rfid' => $rfid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // (แก้ไขข้อความตรงนี้)
            throw new \Exception('ไม่พบบัตร RFID นี้ หรือนักเรียนไม่มีสถานะใช้งาน', 404);
        }

        $stu_id = $row['Stu_id'];
        $fullname = $row['Stu_pre'] . $row['Stu_name'] . ' ' . $row['Stu_sur'];
        $class = $row['Stu_major'] . '/' . $row['Stu_room'];
        $photo = !empty($row['Stu_picture']) ? 'https://std.phichai.ac.th/photo/' . $row['Stu_picture'] : 'assets/images/profile.png';

        // 2. ตรรกะการสแกน (เข้า/ออก)
        $now_datetime = date('Y-m-d H:i:s');
        $now_time = date('H:i:s');
        $today = date('Y-m-d');
        
        $scan_crossover_time = $timeSettings['scan_crossover_time'] ?? '12:00:00';
        $scan_type = ($now_time < $scan_crossover_time) ? 'arrival' : 'leave';

        // 3. ตรวจสอบสแกนซ้ำ
        $check_stmt = $this->db->prepare(
            "SELECT 1 FROM attendance_log 
             WHERE student_id = :stu_id AND scan_type = :scan_type AND DATE(scan_timestamp) = :today 
             LIMIT 1"
        );
        $check_stmt->execute([':stu_id' => $stu_id, ':scan_type' => $scan_type, ':today' => $today]);

        if ($check_stmt->fetch()) {
            return [
                'student_id' => $stu_id, 'fullname' => $fullname, 'class' => $class, 'photo' => $photo,
                'time' => $now_time, 'status' => 'สแกนซ้ำ',
                'is_duplicate' => true
            ];
        }

        // 4. บันทึก Log
        $insert_stmt = $this->db->prepare(
            "INSERT INTO attendance_log (student_id, scan_timestamp, scan_type, device_id, term, year)
             VALUES (:stu_id, :scan_time, :scan_type, :device_id, :term, :year)"
        );
        $insert_stmt->execute([
            ':stu_id' => $stu_id,
            ':scan_time' => $now_datetime,
            ':scan_type' => $scan_type,
            ':device_id' => $device_id,
            ':term' => $term,
            ':year' => $year
        ]);

        // 5. คำนวณสถานะ
        $arrival_late_time = $timeSettings['arrival_late_time'] ?? '08:00:00';
        $arrival_absent_time = $timeSettings['arrival_absent_time'] ?? '10:00:00';
        $leave_early_time = $timeSettings['leave_early_time'] ?? '15:40:00';
        
        $statusText = '';
        $statusCode = 0; 

        if ($scan_type === 'arrival') {
            if ($now_time > $arrival_absent_time) {
                $statusText = 'ขาดเรียน'; $statusCode = 2;
            } elseif ($now_time > $arrival_late_time) {
                $statusText = 'มาสาย'; $statusCode = 3;
            } else {
                $statusText = 'มาเรียน'; $statusCode = 1;
            }
        } else { // 'leave'
            $statusText = ($now_time < $leave_early_time) ? 'กลับก่อน' : 'กลับปกติ';
            $statusCode = 0; 
        }

        // 6. บันทึก/อัปเดต ตารางสรุป 'student_attendance'
        if ($statusCode > 0 && $stu_id) { 
            try {
                $check_sql = "SELECT attendance_status FROM student_attendance 
                              WHERE student_id = :stu_id AND attendance_date = :today";
                $check_stmt = $this->db->prepare($check_sql);
                $check_stmt->execute([':stu_id' => $stu_id, ':today' => $today]);
                $existing_status = $check_stmt->fetchColumn();

                $manual_statuses = [4, 5, 6]; 

                if ($existing_status === false) {
                    $insert_att_sql = "INSERT INTO student_attendance 
                                       (student_id, attendance_date, attendance_status, attendance_time, checked_by, term, year, device_id)
                                       VALUES 
                                       (:stu_id, :today, :status, :time, 'RFID', :term, :year, :device_id)";
                    $insert_att_stmt = $this->db->prepare($insert_att_sql);
                    $insert_att_stmt->execute([
                        ':stu_id' => $stu_id, ':today' => $today, ':status' => $statusCode,
                        ':time' => $now_time, ':term' => $term, ':year' => $year, ':device_id' => $device_id
                    ]);
                } elseif (!in_array($existing_status, $manual_statuses)) {
                    $update_att_sql = "UPDATE student_attendance 
                                       SET attendance_status = :status, attendance_time = :time, checked_by = 'RFID', device_id = :device_id
                                       WHERE student_id = :stu_id AND attendance_date = :today";
                    $update_att_stmt = $this->db->prepare($update_att_sql);
                    $update_att_stmt->execute([
                        ':status' => $statusCode, ':time' => $now_time, ':stu_id' => $stu_id,
                        ':today' => $today, ':device_id' => $device_id
                    ]);
                }
            } catch (\PDOException $e) {
                error_log("Failed to update student_attendance: " . $e->getMessage());
            }
        }

        // 7. ส่งผลลัพธ์กลับ
        return [
            'student_id' => $stu_id,
            'fullname' => $fullname,
            'class' => $class,
            'photo' => $photo,
            'time' => $now_time,
            'status' => $statusText,
            'is_duplicate' => false
        ];
    }
}