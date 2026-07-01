<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

// Check Permission
if (!isset($_SESSION['Officer_login'])) {
    echo "<h1>Permission Denied</h1>";
    exit;
}

require_once "../config/Database.php";
require_once "../class/Behavior.php";
require_once "../class/UserLogin.php";

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$behavior = new Behavior($db);
$user = new UserLogin($db);

// Load school global settings
$configPath = __DIR__ . '/../config.json';
$config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];
$global = $config['global'] ?? ['logoLink' => 'logo-phicha.png', 'nameTitle' => 'StdCare', 'nameschool' => 'โรงเรียนพิชัย'];

$tab = $_GET['tab'] ?? '';
$term = $_GET['term'] ?? '1';
$pee = $_GET['pee'] ?? '2567';

$students = [];
$reportName = '';
$subReportName = '';

if ($tab === 'deduct-group') {
    $group = $_GET['group'] ?? '';
    $type = $_GET['type'] ?? 'all';
    $level = $_GET['level'] ?? '';
    $class = $_GET['class'] ?? '';
    $major = $_GET['major'] ?? '';
    $room = $_GET['room'] ?? '';
    
    $groupNames = [
        '1' => 'ต่ำกว่า 50 คะแนน',
        '2' => '50 - 70 คะแนน',
        '3' => '71 - 99 คะแนน'
    ];
    $groupName = $groupNames[$group] ?? 'ทั้งหมด';
    
    $reportName = "รายงานสรุปผลการหักคะแนนความประพฤตินักเรียน";
    $subReportName = "เกณฑ์คะแนน: " . $groupName;
    
    $allStudents = [];
    
    if ($type === 'all') {
        for ($g = 1; $g <= 3; $g++) {
            $stdList = $behavior->getScoreBehaviorsGroup($g, $term, $pee);
            if ($stdList && is_array($stdList)) {
                $allStudents = array_merge($allStudents, $stdList);
            }
        }
        $subReportName = "เกณฑ์คะแนน: ทุกกลุ่มคะแนนความประพฤติ";
    } else {
        $stdList = $behavior->getScoreBehaviorsGroup($group, $term, $pee);
        if ($stdList) {
            if ($type === 'level') {
                if ($level === 'lower') {
                    $allStudents = array_filter($stdList, fn($s) => intval($s['Stu_major']) >= 1 && intval($s['Stu_major']) <= 3);
                    $subReportName .= " | ระดับมัธยมศึกษาตอนต้น";
                } else if ($level === 'upper') {
                    $allStudents = array_filter($stdList, fn($s) => intval($s['Stu_major']) >= 4 && intval($s['Stu_major']) <= 6);
                    $subReportName .= " | ระดับมัธยมศึกษาตอนปลาย";
                }
            } else if ($type === 'class') {
                if ($class) {
                    $allStudents = array_filter($stdList, fn($s) => intval($s['Stu_major']) === intval($class));
                    $subReportName .= " | ชั้นมัธยมศึกษาปีที่ " . $class;
                }
            } else if ($type === 'room') {
                if ($major && $room) {
                    $allStudents = array_filter($stdList, fn($s) => intval($s['Stu_major']) === intval($major) && intval($s['Stu_room']) === intval($room));
                    $subReportName .= " | ชั้นมัธยมศึกษาปีที่ {$major}/{$room}";
                }
            }
        }
    }
    
    // Sort
    usort($allStudents, function($a, $b) {
        if ($a['Stu_major'] != $b['Stu_major']) return $a['Stu_major'] - $b['Stu_major'];
        if ($a['Stu_room'] != $b['Stu_room']) return $a['Stu_room'] - $b['Stu_room'];
        return $a['Stu_no'] - $b['Stu_no'];
    });
    
    // final map to match the properties we need
    $students = array_values(array_map(function($s) {
        return [
            'Stu_id' => $s['Stu_id'],
            'Stu_no' => $s['Stu_no'],
            'FullName' => ($s['Stu_pre'] ?? '') . ($s['Stu_name'] ?? '') . ' ' . ($s['Stu_sur'] ?? ''),
            'ClassRoom' => 'ม.' . ($s['Stu_major'] ?? '') . '/' . ($s['Stu_room'] ?? ''),
            'behavior_count' => (int)$s['behavior_count']
        ];
    }, $allStudents));
    
} else if ($tab === 'deduct-room') {
    $class = $_GET['class'] ?? '';
    $room = $_GET['room'] ?? '';
    
    $reportName = "รายงานประวัติการหักคะแนนความประพฤตินักเรียนรายห้อง";
    $subReportName = "ห้องเรียน ชั้นมัธยมศึกษาปีที่ {$class}/{$room}";
    
    $result = $behavior->getScoreBehaviorsClass($class, $room, $term, $pee);
    $students = $result !== false ? $result : [];
} else {
    echo "<h1>Invalid Tab</h1>";
    exit;
}

$userData = $user->userData($_SESSION['Officer_login']);
$reporterName = $userData['Teach_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายงาน - StdCare</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,300;0,400;0,700;1,400&display=swap');
        
        body {
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            padding: 1.5cm;
            color: #000;
            background-color: #fff;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .header h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }
        
        .header h2 {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }
        
        .header p {
            font-size: 12px;
            margin: 3px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        
        th, td {
            border: 1px solid #000000;
            padding: 8px 6px;
            vertical-align: middle;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-left {
            text-align: left;
        }
        
        .w-12 {
            width: 48px;
        }
        
        .w-20 {
            width: 80px;
        }
        
        .w-24 {
            width: 96px;
        }
        
        .w-28 {
            width: 112px;
        }
        
        .signature-container {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .signature-box.full-width {
            width: 100%;
            margin-top: 15px;
        }
        
        .signature-box p {
            margin: 4px 0;
        }
        
        .footer-meta {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 40px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1><?php echo htmlspecialchars($global['nameschool']); ?></h1>
        <h2><?php echo htmlspecialchars($reportName); ?></h2>
        <p><strong><?php echo htmlspecialchars($subReportName); ?></strong></p>
        <p>ภาคเรียนที่ <?php echo htmlspecialchars($term); ?> ปีการศึกษา <?php echo htmlspecialchars($pee); ?></p>
        <p>ผู้พิมพ์รายงาน: <?php echo htmlspecialchars($reporterName); ?> | ข้อมูล ณ วันที่ <?php echo date('d/m/Y H:i'); ?> น.</p>
    </div>

    <?php if ($tab === 'deduct-group'): ?>
    <table>
        <thead>
            <tr>
                <th class="w-5">ที่</th>
                <th>ชื่อ - นามสกุล</th>
                <th class="w-10">รหัสนักเรียน</th>
                <th class="w-10">ชั้น/ห้อง</th>
                <th class="w-5">เลขที่</th>
                <th class="w-15">คะแนนที่หัก</th>
                <th class="w-25">คะแนนคงเหลือ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (empty($students)): 
                echo '<tr><td colspan="7" class="text-center">ไม่พบข้อมูลตามเงื่อนไข</td></tr>';
            else:
                foreach ($students as $idx => $stu): 
                    $count = (int)$stu['behavior_count'];
                    $score = 100 - $count;
            ?>
                <tr>
                    <td class="text-center"><?= $idx + 1 ?></td>
                    <td class="text-left"><?= htmlspecialchars($stu['FullName']) ?></td>
                    <td class="text-center font-mono"><?= htmlspecialchars($stu['Stu_id']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($stu['ClassRoom']) ?></td>
                    <td class="text-center font-mono"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                    <td class="text-center font-bold text-rose-600"><?= $count ?></td>
                    <td class="text-center font-bold"><?= $score ?></td>
                </tr>
            <?php 
                endforeach;
            endif; 
            ?>
        </tbody>
    </table>
    
    <?php elseif ($tab === 'deduct-room'): ?>
    <table>
        <thead>
            <tr>
                <th class="w-12">เลขที่</th>
                <th>ชื่อ - นามสกุล</th>
                <th class="w-28">รหัสนักเรียน</th>
                <th class="w-20">ชั้น/ห้อง</th>
                <th class="w-20">คะแนนที่หัก</th>
                <th class="w-24">คะแนนคงเหลือ</th>
                <th class="w-28">หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (empty($students)): 
                echo '<tr><td colspan="7" class="text-center">ไม่พบข้อมูลตามเงื่อนไข</td></tr>';
            else:
                foreach ($students as $stu): 
                    $count = (int)$stu['behavior_count'];
                    $score = 100 - $count;
                    $fullName = ($stu['Stu_pre'] ?? '') . ($stu['Stu_name'] ?? '') . ' ' . ($stu['Stu_sur'] ?? '');
                    $classRoom = 'ม.' . ($stu['Stu_major'] ?? '') . '/' . ($stu['Stu_room'] ?? '');
            ?>
                <tr>
                    <td class="text-center font-mono"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                    <td class="text-left"><?= htmlspecialchars($fullName) ?></td>
                    <td class="text-center font-mono"><?= htmlspecialchars($stu['Stu_id']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($classRoom) ?></td>
                    <td class="text-center font-bold text-rose-600"><?= $count ?></td>
                    <td class="text-center font-bold"><?= $score ?></td>
                    <td class="text-center">-</td>
                </tr>
            <?php 
                endforeach;
            endif; 
            ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="footer-meta">
        พิมพ์และออกรายงานโดยระบบ StdCare <?php echo htmlspecialchars($global['nameschool']); ?> ณ วันที่ <?php echo date('d/m/Y H:i'); ?> น.
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
