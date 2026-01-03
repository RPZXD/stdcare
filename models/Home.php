<?php
namespace App\Models;

use PDO;

class Home
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAttendanceStats($date, $term, $year)
    {
        $stats = [
            'today' => ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0],
            'week' => ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0],
            'month' => ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0]
        ];

        $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
        $monthStart = date('Y-m-01', strtotime($date));
        $monthEnd = date('Y-m-t', strtotime($date));

        // Today stats
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT student_id) as total,
                SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late
            FROM student_attendance 
            WHERE attendance_date = :date AND term = :term AND year = :pee
        ");
        $stmt->execute([':date' => $date, ':term' => $term, ':pee' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['total']) $stats['today'] = $row;

        // Week stats
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late
            FROM student_attendance 
            WHERE attendance_date BETWEEN :start AND :end AND term = :term AND year = :pee
        ");
        $stmt->execute([':start' => $weekStart, ':end' => $weekEnd, ':term' => $term, ':pee' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['total']) $stats['week'] = $row;

        // Month stats
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as late
            FROM student_attendance 
            WHERE attendance_date BETWEEN :start AND :end AND term = :term AND year = :pee
        ");
        $stmt->execute([':start' => $monthStart, ':end' => $monthEnd, ':term' => $term, ':pee' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['total']) $stats['month'] = $row;

        return $stats;
    }

    public function getStudentCounts()
    {
        $counts = [
            'total' => 0,
            'junior' => 0,
            'senior' => 0
        ];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1");
        $stmt->execute();
        $counts['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (1,2,3)");
        $stmt->execute();
        $counts['junior'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM student WHERE Stu_status=1 AND Stu_major IN (4,5,6)");
        $stmt->execute();
        $counts['senior'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $counts;
    }

    public function getClassAttendance($date, $term, $year)
    {
        $stmt = $this->db->prepare("
            SELECT s.Stu_major, s.Stu_room, a.attendance_status
            FROM student s
            LEFT JOIN student_attendance a
                ON s.Stu_id = a.student_id
                AND a.attendance_date = :date
                AND a.term = :term
                AND a.year = :pee
            WHERE s.Stu_status=1
            ORDER BY s.Stu_major, s.Stu_room
        ");
        $stmt->execute([
            ':date' => $date,
            ':term' => $term,
            ':pee' => $year
        ]);
        $all_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $class_map = [];
        $status_count = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];
        $total = 0;
        foreach ($all_attendance as $row) {
            $major = $row['Stu_major'];
            $room = $row['Stu_room'];
            $status = $row['attendance_status'];
            $key = $major . '-' . $room;
            if (!isset($class_map[$key])) {
                $class_map[$key] = [
                    'Stu_major' => $major,
                    'Stu_room' => $room,
                    'count' => 0,
                    'status' => ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0]
                ];
            }
            $class_map[$key]['count']++;
            if ($status && isset($class_map[$key]['status'][$status])) {
                $class_map[$key]['status'][$status]++;
                $status_count[$status]++;
            }
            $total++;
        }

        return [
            'classes' => array_values($class_map),
            'status_count' => $status_count,
            'total' => $total
        ];
    }
}
