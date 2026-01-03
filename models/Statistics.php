<?php
namespace App\Models;

use PDO;

class Statistics
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get student counts by level
     */
    public function getStudentCounts()
    {
        $counts = [
            'total' => 0,
            'junior' => 0,
            'senior' => 0
        ];

        // Total
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1");
        $stmt->execute();
        $counts['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Junior (ม.1-3)
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (1,2,3)");
        $stmt->execute();
        $counts['junior'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Senior (ม.4-6)
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (4,5,6)");
        $stmt->execute();
        $counts['senior'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return $counts;
    }

    /**
     * Get attendance statistics for today, week, month
     */
    public function getAttendanceStats($date)
    {
        $currentDate = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($currentDate)));
        $weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($currentDate)));
        $monthStart = date('Y-m-01', strtotime($currentDate));
        $monthEnd = date('Y-m-t', strtotime($currentDate));

        $stats = [
            'today' => $this->getAttendanceForPeriod($date, $date),
            'week' => $this->getAttendanceForPeriod($weekStart, $weekEnd),
            'month' => $this->getAttendanceForPeriod($monthStart, $monthEnd)
        ];

        return $stats;
    }

    /**
     * Get attendance for a period
     */
    private function getAttendanceForPeriod($startDate, $endDate)
    {
        $default = ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'sick' => 0, 'business' => 0, 'activity' => 0];

        if ($startDate === $endDate) {
            $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN attendance_status = 4 THEN 1 ELSE 0 END) as sick,
                SUM(CASE WHEN attendance_status = 5 THEN 1 ELSE 0 END) as business,
                SUM(CASE WHEN attendance_status = 6 THEN 1 ELSE 0 END) as activity
            FROM student_attendance WHERE attendance_date = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$startDate]);
        } else {
            $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN attendance_status = 4 THEN 1 ELSE 0 END) as sick,
                SUM(CASE WHEN attendance_status = 5 THEN 1 ELSE 0 END) as business,
                SUM(CASE WHEN attendance_status = 6 THEN 1 ELSE 0 END) as activity
            FROM student_attendance WHERE attendance_date BETWEEN ? AND ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$startDate, $endDate]);
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: $default;
    }

    /**
     * Get detailed status statistics by level
     */
    public function getDetailedStatsByLevel($date, $level)
    {
        $majors = $level === 'junior' ? '1,2,3' : '4,5,6';
        
        $sql = "SELECT 
            CASE 
                WHEN sa.attendance_status = 1 THEN 'มาเรียน'
                WHEN sa.attendance_status = 2 THEN 'ขาดเรียน'
                WHEN sa.attendance_status = 3 THEN 'มาสาย'
                WHEN sa.attendance_status = 4 THEN 'ลาป่วย'
                WHEN sa.attendance_status = 5 THEN 'ลากิจ'
                WHEN sa.attendance_status = 6 THEN 'กิจกรรม'
                ELSE 'ไม่ระบุ'
            END AS status_name,
            sa.attendance_status,
            COUNT(*) as total
        FROM student_attendance sa
        INNER JOIN student s ON sa.student_id = s.Stu_id
        WHERE s.Stu_status=1 AND s.Stu_major IN ($majors) AND sa.attendance_date = ?
        GROUP BY sa.attendance_status
        ORDER BY sa.attendance_status";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
