<?php
// (ไฟล์นี้ควรอยู่ใน officer/print_roster.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

// --- (ส่วนที่ 1: เรียกใช้คลาส) ---
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../models/Student.php'; 

// --- (ส่วนที่ 2: ตรวจสอบการ Login) ---
if (!isset($_SESSION['Officer_login'])) {
    echo "กรุณาเข้าสู่ระบบก่อน";
    exit;
}

// --- (ส่วนที่ 3: รับค่า Level และ Room จาก URL) ---
$level = $_GET['level'] ?? 0;
$room = $_GET['room'] ?? 0;
$year = $_GET['year'] ?? 2568; 

if (empty($level) || empty($room)) {
    echo "กรุณาระบุชั้นและห้อง";
    exit;
}

// --- (ส่วนที่ 4: ดึงข้อมูลจากฐานข้อมูล) ---
use App\DatabaseUsers;
use App\Models\Teacher;
use App\Models\Student; 

$db = new DatabaseUsers();
$teacherModel = new Teacher($db);
$studentModel = new Student($db); 

// 4.1 ดึงครูที่ปรึกษา
$advisors_data = $teacherModel->getByClassAndRoom($level, $room);

// 4.2 ดึงนักเรียน (จาก Student Model)
$students_data = $studentModel->getByClassAndRoom($level, $room); 

// 4.3 (เพิ่ม) ประมวลผลนับจำนวน ชาย/หญิง
$total_students = count($students_data);
$male_count = 0;
$male_prefixes = ['เด็กชาย', 'นาย']; // (คำนำหน้าผู้ชาย)

foreach ($students_data as $student) {
    // (เราดึง Stu_pre มาจาก Model แล้ว)
    if (in_array($student['Stu_pre'], $male_prefixes)) {
        $male_count++;
    }
}
$female_count = $total_students - $male_count;


// 4.4 ประมวลผล "แผนการเรียน"
function getPlanName($level, $room) {
    if ($level == 1) { // ม.ต้น
        if ($room == 1) return 'Enrichment Science Classroom (ESC)';
        if ($room == 2) return 'Enrichment Math Classroom (EMC)';
        if ($room == 2) return 'วิทยาศาสตร์ คณิตศาสตร์  และเทคโนโลยี (Coding)';
        if ($room == 4) return 'วิทยาศาสตร์พลังสิบ';
        if ($room == 5) return 'ภาษาอังกฤษ';
        if ($room == 6) return 'ภาษาจีน';
        if ($room == 7) return 'ภาษาไทย';
        if ($room == 8) return 'สังคมศึกษา';
        if ($room == 9) return 'อุตสาหกรรม - พาณิชยกรรม';
        if ($room == 10) return 'เกษตรกรรม - คหกรรม';
        if ($room == 11) return 'ศิลปะ - ดนตรี';
        if ($room == 12) return 'กีฬา';
        return '';
    }
    if ($level >= 2 && $level <= 3) { // ม.ต้น
        if ($room == 1) return 'Enrichment Science Classroom (ESC)';
        if ($room == 2) return 'Enrichment Math Classroom (EMC)';
        if ($room == 3) return 'วิทยาศาสตร์ คณิตศาสตร์  และเทคโนโลยี (Coding)';
        if ($room == 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
        if ($room == 5) return 'ภาษาอังกฤษ';
        if ($room == 6) return 'ภาษาจีน';
        if ($room == 7) return 'ภาษาไทย';
        if ($room == 8) return 'สังคมศึกษา';
        if ($room == 9) return 'อุตสาหกรรม - พาณิชยกรรม';
        if ($room == 10) return 'เกษตรกรรม - คหกรรม';
        if ($room == 11) return 'ศิลปะ - ดนตรี';
        if ($room == 12) return 'กีฬา';
        return '';
    }
    if ($level == 4) { // ม.ปลาย
        if ($room == 1) return 'Enrichment Science Classroom (ESC)';
        if ($room == 2) return 'วิทยาศาสตร์ คณิตศาสตร์  และเทคโนโลยี (Coding)';
        if ($room == 3) return 'วิทยาศาสตร์พลังสิบ';
        if ($room == 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
        if ($room == 5) return 'สังคมศาสตร์และภาษาไทย';
        if ($room == 6) return 'ภาษาศาสตร์';
        if ($room == 7) return 'บริหารอุตสาหกรรม';
        return '';
    }
    if ($level >= 5 && $level <= 6) { // ม.ปลาย
        if ($room == 1) return 'Enrichment Science Classroom (ESC)';
        if ($room == 2) return 'วิทยาศาสตร์ คณิตศาสตร์  และเทคโนโลยี (Coding)';
        if ($room == 3) return 'วิทยาศาสตร์ คณิตศาสตร์  และเทคโนโลยี (Coding)';
        if ($room == 4) return 'วิทยาศาสตร์ คณิตศาสตร์';
        if ($room == 5) return 'แผนการเรียนศิลปศาสตร์ - สังคมศาสตร์';
        if ($room == 6) return 'ภาษาศาสตร์';
        if ($room == 7) return 'บริหารอุตสาหกรรม';
        return '';
    }
    return 'ไม่พบแผนการเรียน';
}

$plan_name = getPlanName($level, $room);

// 4.5 จัดรูปแบบชื่อครู
$advisors_text = "";
if (count($advisors_data) > 0) {
    $advisor_names = array_map(function($a) { return $a['Teach_name']; }, $advisors_data);
    foreach($advisor_names as $index => $name) {
         $advisors_text .= ($index + 1) . ". " . htmlspecialchars($name) . "  ";
    }
} else {
    $advisors_text = " (ไม่พบข้อมูลครูที่ปรึกษา) ";
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายชื่อนักเรียน ม.<?php echo "$level/$room"; ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        /* 2. กำหนด @page สำหรับ A4 แนวตั้ง และขอบกระดาษ */
        @page {
            size: A4 portrait;
            margin: 15mm 10mm 15mm 10mm; /* บน, ขวา, ล่าง, ซ้าย */
        }

        /* 3. กำหนดฟอนต์และขนาดพื้นฐาน */
        body { 
            font-family: 'Sarabun', 'Arial', sans-serif; 
            margin: 0; /* ลบ margin ของ body (ใช้ @page margin แทน) */
            font-size: 10pt; /* ขนาดฟอนต์พื้นฐานสำหรับเนื้อหา */
            line-height: .9;
        }

        /* 4. จัดการส่วนหัวกระดาษ */
        .header-info { 
            text-align: center; 
            line-height: 1.2;
            margin-bottom: 10px;
        }
        .header-info h2 { 
            margin: 0; 
            font-size: 10pt; /* (ขนาดสำหรับหัวเรื่อง) */
            font-weight: 700;
        }
        .header-info p { 
            margin: 0; 
            font-size: 12pt; /* (ขนาดสำหรับข้อมูลรอง) */
        }
        .advisors { 
            text-align: left; 
            margin-top: 5px;
        }

        /* 5. จัดการตาราง */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 11pt; /* ขนาดฟอนต์ในตาราง */
        }
        th, td { 
            border: 1px solid #000; 
            padding: 4px 6px; /* (ลด padding ลง) */
            text-align: left;
            vertical-align: top;
        }
        th { 
            background-color: #f2f2f2; 
            text-align: center;
            font-weight: 700;
            padding: 5px;
        }
        
        td {
            /* (ตัดคำในช่องชื่อ-สกุล ถ้าจำเป็น) */
            word-break: break-word;
        }

        /* 6. กำหนดความกว้างคอลัมน์ */
        
        /* เลขที่ (คอลัมน์ที่ 1) */
        table th:nth-child(1),
        table td:nth-child(1) {
            width: 5%;
            text-align: center;
        }
        
        /* เลขประจำตัว (คอลัมน์ที่ 2) */
        table th:nth-child(2),
        table td:nth-child(2) {
            width: 10%;
            text-align: center;
        }
        
        /* ชื่อ-สกุล (คอลัมน์ที่ 3) */
        table th:nth-child(3) {
            width: auto; /* (ให้คอลัมน์นี้ยืดหยุ่น) */
            text-align: left;
        }

        /* (คอลัมน์ว่าง 10 ช่อง) */
        th.empty-col, td.empty-col {
            width: 3%; /* (3.5% * 10 = 35% ของตาราง) */
            min-width: 25px; /* (กำหนดความกว้างขั้นต่ำ) */
        }
        
        /* 7. ซ่อนสิ่งที่ไม่ต้องการพิมพ์ (ถ้ามี) */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();"> 
    
    <div class="header-info">
        <p>รายชื่อนักเรียนระดับชั้นมัธยมศึกษาปีที่ <?php echo htmlspecialchars("$level/$room"); ?> โรงเรียนพิชัย ปีการศึกษา <?php echo htmlspecialchars($year); ?></p>
        <p>แผนการเรียน: <?php echo htmlspecialchars($plan_name); ?></p>
        <p>ครูที่ปรึกษา: <?php echo trim($advisors_text); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align: center;">เลขที่</th>
                <th style="text-align: center;">เลขประจำตัว</th>
                <th style="text-align: center;">ชื่อ-สกุล</th>
                <th class="empty-col"></th><th class="empty-col"></th>
                <th class="empty-col"></th><th class="empty-col"></th>
                <th class="empty-col"></th><th class="empty-col"></th>
                <th class="empty-col"></th><th class="empty-col"></th>
                <th class="empty-col"></th><th class="empty-col"></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (count($students_data) > 0) {
                foreach ($students_data as $student) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($student['Stu_no']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['Stu_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['Stu_name']) . "</td>";
                    // 10 ช่องว่าง
                    echo "<td class='empty-col'></td><td class='empty-col'></td>";
                    echo "<td class='empty-col'></td><td class='empty-col'></td>";
                    echo "<td class='empty-col'></td><td class='empty-col'></td>";
                    echo "<td class='empty-col'></td><td class='empty-col'></td>";
                    echo "<td class='empty-col'></td><td class='empty-col'></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='13' style='text-align: center;'>ไม่พบข้อมูลนักเรียน</td></tr>";
            }
            ?>
        </tbody>
         <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">
                    สรุปจำนวนนักเรียน
                </td>
                <td colspan="10" style="text-align: left; padding-left: 10px;">
                     รวม <?php echo $total_students; ?> คน 
                     ชาย <?php echo $male_count; ?> คน 
                     หญิง <?php echo $female_count; ?> คน
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>