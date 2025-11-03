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
     * ดึงประวัติการสแกนวันนี้ทั้งหมด (แสดงทั้งเข้าและออก)
     */
    public function getTodayAttendanceLog(array $timeSettings)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT
                    l.student_id,
                    l.scan_type,
                    DATE_FORMAT(l.scan_timestamp, '%H:%i:%s') AS time,
                    s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room,
                    CASE
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) <= :arrival_late THEN '<span class=\"inline-block px-2 py-1 bg-green-200 rounded text-green-700\">✅ มาเรียน</span>'
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_late AND TIME(l.scan_timestamp) <= :arrival_absent THEN '<span class=\"inline-block px-2 py-1 bg-yellow-200 rounded text-yellow-700\">⚠️ มาสาย</span>'
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_absent THEN '<span class=\"inline-block px-2 py-1 bg-red-200 rounded text-red-700\">❌ ขาดเรียน</span>'
                        WHEN l.scan_type = 'leave' AND TIME(l.scan_timestamp) < :leave_early THEN '<span class=\"inline-block px-2 py-1 bg-blue-200 rounded text-blue-700\">🏃 กลับก่อน</span>'
                        WHEN l.scan_type = 'leave' THEN '<span class=\"inline-block px-2 py-1 bg-purple-200 rounded text-purple-700\">🏠 กลับปกติ</span>'
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

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'student_id' => $row['student_id'],
                'fullname'   => $row['Stu_pre'].$row['Stu_name'] . '  ' . $row['Stu_sur'],
                'class'      => $row['Stu_major'] . '/' . $row['Stu_room'],
                'time'       => $row['time'],
                'scan_type'  => $row['scan_type'] === 'arrival' ? '🔵 เข้า' : '🔴 ออก',
                'status'     => $row['status'],
            ];
        }
        return $data;
    }

    /**
     * ดึงข้อมูลการสแกนล่าสุด
     */
    public function getLastScan(array $timeSettings)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT
                    l.student_id,
                    l.scan_type,
                    DATE_FORMAT(l.scan_timestamp, '%H:%i:%s') AS time,
                    s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_picture,
                    CASE
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) <= :arrival_late THEN 'มาเรียน'
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_late AND TIME(l.scan_timestamp) <= :arrival_absent THEN 'มาสาย'
                        WHEN l.scan_type = 'arrival' AND TIME(l.scan_timestamp) > :arrival_absent THEN 'ขาดเรียน'
                        WHEN l.scan_type = 'leave' AND TIME(l.scan_timestamp) < :leave_early THEN 'กลับก่อนเวลา'
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
                'fullname'   => $row['Stu_pre'] . $row['Stu_name'] . ' ' . $row['Stu_sur'],
                'class'      => $row['Stu_major'] . '/' . $row['Stu_room'],
                'photo'      => !empty($row['Stu_picture']) ? 'https://std.phichai.ac.th/photo/' . $row['Stu_picture'] : 'assets/images/profile.png',
                'time'       => $row['time'],
                'scan_type'  => $row['scan_type'] === 'arrival' ? 'เข้า' : 'ออก',
                'status'     => $row['status'],
            ];
        }
        return null;
    }

    /**
     * ประมวลผลการสแกน RFID (รองรับทั้งเข้าและออก)
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
            throw new \Exception('ไม่พบบัตร RFID นี้ หรือนักเรียนไม่มีสถานะใช้งาน', 404);
        }

        $stu_id = $row['Stu_id'];
        $fullname = $row['Stu_pre'] . $row['Stu_name'] . ' ' . $row['Stu_sur'];
        $class = $row['Stu_major'] . '/' . $row['Stu_room'];
        $photo = !empty($row['Stu_picture']) ? 'https://std.phichai.ac.th/photo/' . $row['Stu_picture'] : 'assets/images/profile.png';

        // 2. กำหนด scan_type (เข้า/ออก) ตามเวลา
        $now_datetime = date('Y-m-d H:i:s');
        $now_time = date('H:i:s');
        $today = date('Y-m-d');

        // ดึงเวลา Crossover ที่ตั้งค่าไว้ (หรือใช้ 12:00:00 ถ้าไม่มี)
        $crossover_time = $timeSettings['scan_crossover_time'] ?? '12:00:00';
        
        // Logic ใหม่:
        // ถ้าสแกนก่อน $crossover_time (เช่น ก่อนเที่ยง) = 'arrival' (เข้า)
        // ถ้าสแกนหลัง $crossover_time (เช่น หลังเที่ยง)  = 'leave' (ออก)
        $scan_type = ($now_time < $crossover_time) ? 'arrival' : 'leave';

        // 3. ตรวจสอบสแกนซ้ำ (ต่อ scan_type)
        $check_stmt = $this->db->prepare(
            "SELECT 1 FROM attendance_log 
             WHERE student_id = :stu_id AND scan_type = :scan_type AND DATE(scan_timestamp) = :today 
             LIMIT 1"
        );
        $check_stmt->execute([':stu_id' => $stu_id, ':scan_type' => $scan_type, ':today' => $today]);

        if ($check_stmt->fetch()) {
            return [
                'student_id' => $stu_id, 
                'fullname' => $fullname, 
                'class' => $class, 
                'photo' => $photo,
                'time' => $now_time, 
                'scan_type' => $scan_type,
                'status' => 'สแกนซ้ำ',
                'is_duplicate' => true
            ];
        }

        // 4. บันทึก Log ลง attendance_log
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

        // 5. ประมวลผลตาม scan_type
        if ($scan_type === 'arrival') {
            // การสแกนเข้า -> อัปเดต student_attendance
            $statusText = $this->processArrival($stu_id, $today, $now_time, $timeSettings, $term, $year, $device_id);
        } else {
            // การสแกนออก -> บันทึกลง student_leave_log
            $statusText = $this->processLeave($stu_id, $today, $now_time, $timeSettings, $term, $year, $device_id);
        }

        // 6. ส่งผลลัพธ์กลับ
        return [
            'student_id' => $stu_id,
            'fullname' => $fullname,
            'class' => $class,
            'photo' => $photo,
            'time' => $now_time,
            'scan_type' => $scan_type,
            'status' => $statusText,
            'is_duplicate' => false
        ];
    }

    /**
     * ประมวลผลการสแกนเข้า (arrival)
     */
    private function processArrival($stu_id, $date, $time, $timeSettings, $term, $year, $device_id)
    {
        $arrival_late = $timeSettings['arrival_late_time'] ?? '08:00:00';
        $arrival_absent = $timeSettings['arrival_absent_time'] ?? '10:00:00';

        // คำนวณสถานะ
        if ($time > $arrival_absent) {
            $statusText = 'ขาดเรียน';
            $statusCode = 2;
        } elseif ($time > $arrival_late) {
            $statusText = 'มาสาย';
            $statusCode = 3;
        } else {
            $statusText = 'มาเรียน';
            $statusCode = 1;
        }

        // บันทึก/อัปเดต student_attendance
        try {
            $check_sql = "SELECT attendance_status FROM student_attendance 
                          WHERE student_id = :stu_id AND attendance_date = :date";
            $check_stmt = $this->db->prepare($check_sql);
            $check_stmt->execute([':stu_id' => $stu_id, ':date' => $date]);
            $existing_status = $check_stmt->fetchColumn();

            $manual_statuses = [4, 5, 6]; // ลา, ลาป่วย, กิจ

            if ($existing_status === false) {
                // ยังไม่มีข้อมูล -> INSERT
                $insert_sql = "INSERT INTO student_attendance 
                               (student_id, attendance_date, attendance_time, attendance_status, checked_by, term, year, device_id)
                               VALUES 
                               (:stu_id, :date, :time, :status, 'RFID', :term, :year, :device_id)";
                $stmt = $this->db->prepare($insert_sql);
                $stmt->execute([
                    ':stu_id' => $stu_id, ':date' => $date, ':time' => $time, ':status' => $statusCode,
                    ':term' => $term, ':year' => $year, ':device_id' => $device_id
                ]);
            } elseif (!in_array($existing_status, $manual_statuses)) {
                // มีข้อมูลแล้ว แต่ไม่ใช่การลา -> UPDATE
                $update_sql = "UPDATE student_attendance 
                               SET attendance_status = :status, attendance_time = :time, checked_by = 'RFID', device_id = :device_id
                               WHERE student_id = :stu_id AND attendance_date = :date";
                $stmt = $this->db->prepare($update_sql);
                $stmt->execute([
                    ':status' => $statusCode, ':time' => $time, ':stu_id' => $stu_id,
                    ':date' => $date, ':device_id' => $device_id
                ]);
            }
        } catch (\PDOException $e) {
            error_log("Failed to update student_attendance: " . $e->getMessage());
        }

        return $statusText;
    }

    /**
     * ประมวลผลการสแกนออก (leave)
     */
    private function processLeave($stu_id, $date, $time, $timeSettings, $term, $year, $device_id)
    {
        $leave_early = $timeSettings['leave_early_time'] ?? '15:40:00';

        // คำนวณสถานะ
        if ($time < $leave_early) {
            $statusText = 'กลับก่อนเวลา';
            $leave_status = 'early';
        } else {
            $statusText = 'กลับปกติ';
            $leave_status = 'normal';
        }

        // บันทึกลง student_leave_log
        try {
            $insert_sql = "INSERT INTO student_leave_log 
                           (student_id, leave_date, leave_time, leave_status, device_id, term, year)
                           VALUES 
                           (:stu_id, :date, :time, :status, :device_id, :term, :year)
                           ON DUPLICATE KEY UPDATE
                           leave_time = :time, leave_status = :status";
            $stmt = $this->db->prepare($insert_sql);
            $stmt->execute([
                ':stu_id' => $stu_id, ':date' => $date, ':time' => $time, ':status' => $leave_status,
                ':device_id' => $device_id, ':term' => $term, ':year' => $year
            ]);
        } catch (\PDOException $e) {
            error_log("Failed to insert student_leave_log: " . $e->getMessage());
        }

        return $statusText;
    }

    /**
     * ดึงรายชื่อนักเรียนที่ยังไม่กลับบ้าน
     */
    public function getStudentsNotLeftToday()
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT 
                    s.Stu_id,
                    s.Stu_pre,
                    s.Stu_name,
                    s.Stu_sur,
                    s.Stu_major,
                    s.Stu_room,
                    sa.attendance_time AS arrival_time
                FROM student s
                INNER JOIN student_attendance sa ON s.Stu_id = sa.student_id
                LEFT JOIN student_leave_log sl ON s.Stu_id = sl.student_id AND sl.leave_date = :today
                WHERE sa.attendance_date = :today
                  AND sa.attendance_status IN (1,3) -- มาเรียนหรือมาสาย
                  AND sl.id IS NULL -- ยังไม่ได้สแกนออก
                  AND s.Stu_status = '1'
                ORDER BY s.Stu_major, s.Stu_room, s.Stu_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':today' => $today]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}