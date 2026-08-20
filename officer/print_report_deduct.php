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
$logoFile = !empty($global['logoLink']) ? $global['logoLink'] : 'logo-phicha.png';
$logoPath = '../dist/img/' . $logoFile;

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
        '1' => 'คะแนนต่ำกว่า 50 คะแนน (กลุ่มปรับปรุงเร่งด่วน)',
        '2' => 'คะแนน 50 - 70 คะแนน (กลุ่มเฝ้าระวัง)',
        '3' => 'คะแนน 71 - 99 คะแนน (กลุ่มว่ากล่าวตักเตือน)'
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
            'Stu_id' => $s['Stu_id'] ?? '',
            'Stu_no' => $s['Stu_no'] ?? '',
            'FullName' => trim(($s['Stu_pre'] ?? '') . ($s['Stu_name'] ?? '') . ' ' . ($s['Stu_sur'] ?? '')),
            'ClassRoom' => 'ม.' . ($s['Stu_major'] ?? '') . '/' . ($s['Stu_room'] ?? ''),
            'behavior_count' => (int)($s['behavior_count'] ?? 0)
        ];
    }, $allStudents));
    
} else if ($tab === 'deduct-room') {
    $class = $_GET['class'] ?? '';
    $room = $_GET['room'] ?? '';
    
    $reportName = "รายงานประวัติการหักคะแนนความประพฤตินักเรียนรายห้อง";
    $subReportName = "ห้องเรียน ชั้นมัธยมศึกษาปีที่ {$class}/{$room}";
    
    $result = $behavior->getScoreBehaviorsClass($class, $room, $term, $pee);
    $rawStudents = $result !== false ? $result : [];
    
    $students = array_values(array_map(function($s) {
        return [
            'Stu_id' => $s['Stu_id'] ?? '',
            'Stu_no' => $s['Stu_no'] ?? '',
            'FullName' => trim(($s['Stu_pre'] ?? '') . ($s['Stu_name'] ?? '') . ' ' . ($s['Stu_sur'] ?? '')),
            'ClassRoom' => 'ม.' . ($s['Stu_major'] ?? '') . '/' . ($s['Stu_room'] ?? ''),
            'behavior_count' => (int)($s['behavior_count'] ?? 0)
        ];
    }, $rawStudents));
} else {
    echo "<h1>Invalid Tab</h1>";
    exit;
}

$userData = $user->userData($_SESSION['Officer_login']);
$reporterName = $userData['Teach_name'] ?? 'เจ้าหน้าที่งานวินัยและกิจการนักเรียน';

// Calculation for summary stats
$totalStudents = count($students);
$deductedStudentsCount = 0;
$totalDeductedScore = 0;
$totalRemainingScore = 0;

foreach ($students as $stu) {
    $count = (int)$stu['behavior_count'];
    if ($count > 0) {
        $deductedStudentsCount++;
    }
    $totalDeductedScore += $count;
    $totalRemainingScore += (100 - $count);
}
$avgRemainingScore = $totalStudents > 0 ? round($totalRemainingScore / $totalStudents, 2) : 100;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์<?= htmlspecialchars($reportName); ?> - <?= htmlspecialchars($global['nameTitle']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Prompt:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --font-size: 11.5px;
            --cell-padding: 3px 5px;
            --th-bg: #f1f5f9;
            --table-border: #334155;
            --primary-color: #1e3a8a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            color: #0f172a;
            background-color: #f1f5f9;
            font-size: var(--font-size);
            line-height: 1.25;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Screen Control Toolbar */
        .control-bar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #cbd5e1;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            gap: 12px;
            flex-wrap: wrap;
        }

        .control-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            font-family: 'Prompt', 'Sarabun', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 500;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }

        .btn:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
            border-color: #1d4ed8;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            color: #ffffff;
        }

        .btn-active {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #3b82f6;
            font-weight: 600;
        }

        .btn-secondary {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .form-select {
            font-family: 'Sarabun', sans-serif;
            padding: 5px 8px;
            font-size: 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: white;
            color: #334155;
        }

        /* Paper Layout for Screen Preview */
        .paper-container {
            display: flex;
            justify-content: center;
            padding: 16px 10px 30px;
        }

        .paper {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 10mm 10mm 8mm 10mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .paper-content {
            flex: 1;
        }

        /* Document Header */
        .doc-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #0f172a;
        }

        .doc-header .logo-container {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .doc-header .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .doc-header .title-container {
            flex-grow: 1;
            text-align: center;
        }

        .doc-header h1 {
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 2px 0;
            color: #0f172a;
        }

        .doc-header h2 {
            font-size: 13.5px;
            font-weight: 700;
            margin: 0 0 2px 0;
            color: #1e3a8a;
        }

        .doc-header .sub-title {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin: 1px 0;
        }

        .doc-header .meta-info {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 2px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        /* Stats Summary Box */
        .stats-bar {
            display: flex;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 4px 10px;
            margin-bottom: 8px;
            font-size: 11px;
            line-height: 1.3;
        }

        .stats-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stats-item strong {
            color: #0f172a;
        }

        /* Tables */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: var(--font-size);
            margin-bottom: 8px;
        }

        table.report-table thead {
            display: table-header-group;
        }

        table.report-table tfoot {
            display: table-footer-group;
        }

        table.report-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table.report-table th, 
        table.report-table td {
            border: 1px solid #475569;
            padding: var(--cell-padding);
            vertical-align: middle;
        }

        table.report-table th {
            background-color: var(--th-bg) !important;
            font-weight: 700;
            text-align: center;
            color: #0f172a;
            font-size: 11px;
            white-space: nowrap;
        }

        table.report-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }

        .score-deduct {
            color: #b91c1c;
            font-weight: 700;
        }

        .score-remaining {
            font-weight: 700;
        }

        .score-danger {
            color: #b91c1c;
        }

        .score-warning {
            color: #d97706;
        }

        .score-good {
            color: #16a34a;
        }

        .badge-status {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
        }

        .badge-normal {
            background: #dcfce7;
            color: #166534;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Signatures Container */
        .signature-container {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            gap: 15px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
            font-size: 11px;
            line-height: 1.35;
        }

        .signature-box p {
            margin: 2px 0;
        }

        .signature-box .sign-space {
            height: 25px;
        }

        .signature-box .sign-name {
            font-weight: 500;
        }

        .signature-box .sign-title {
            font-size: 10.5px;
            color: #475569;
        }

        /* Document Footer */
        .doc-footer {
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #64748b;
            page-break-inside: avoid;
        }

        /* Print Media Styles Optimized for 1 Page */
        @media print {
            @page {
                size: A4 portrait;
                margin: 7mm 8mm 6mm 8mm;
            }

            html, body {
                background: #ffffff !important;
                color: #000000;
                font-size: var(--font-size);
                height: 100%;
                overflow: hidden;
            }

            .no-print {
                display: none !important;
            }

            .paper-container {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            .paper {
                width: 100% !important;
                min-height: auto !important;
                max-height: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                page-break-inside: avoid;
                page-break-after: avoid;
            }

            .doc-header {
                border-bottom: 1.5px solid #000000;
            }

            .doc-header h2 {
                color: #000000 !important;
            }

            table.report-table {
                font-size: var(--font-size);
                margin-bottom: 6px;
            }

            table.report-table th, 
            table.report-table td {
                border: 1px solid #000000 !important;
                padding: var(--cell-padding) !important;
            }

            table.report-table th {
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table.report-table tbody tr:nth-child(even) {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .score-deduct {
                color: #000000 !important;
            }

            .stats-bar {
                border: 1px solid #000000 !important;
                background-color: #f8fafc !important;
                padding: 3px 8px !important;
                margin-bottom: 6px !important;
            }

            .badge-status {
                border: 1px solid #666;
                background: transparent !important;
                color: #000 !important;
            }

            .signature-box .sign-title {
                color: #000000;
            }

            .doc-footer {
                border-top: 1px solid #000000;
                color: #333333;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Toolbar (No Print) -->
    <div class="control-bar no-print">
        <div class="control-group">
            <button class="btn btn-primary" onclick="window.print();">
                <i class="fas fa-print"></i> พิมพ์รายงาน
            </button>
            <button id="btnFitPage" class="btn btn-active" onclick="toggleFitSinglePage()">
                <i class="fas fa-compress-alt"></i> โหมดพอดี 1 หน้า: <span id="fitPageStatus">เปิด</span>
            </button>
            <button class="btn" onclick="window.close();">
                <i class="fas fa-times"></i> ปิดหน้าต่าง
            </button>
        </div>
        <div class="control-group">
            <label style="font-size: 12px; font-weight: 500; display: flex; align-items: center; gap: 5px;">
                <i class="fas fa-font"></i> ขนาดข้อความ:
                <select id="densitySelect" class="form-select" onchange="changeDensity(this.value)">
                    <option value="compact" selected>กะทัดรัด (พอดี 1 หน้า 30-45 คน)</option>
                    <option value="ultra-compact">กะทัดรัดพิเศษ (มากกว่า 45 คน)</option>
                    <option value="normal">ขนาดปกติ</option>
                    <option value="large">ขนาดใหญ่</option>
                </select>
            </label>
            <button class="btn btn-secondary" id="btnToggleSign" onclick="toggleSignatures()">
                <i class="fas fa-signature"></i> <span id="signBtnText">แสดงช่องลงนาม</span>
            </button>
        </div>
    </div>

    <!-- Paper Sheet Container -->
    <div class="paper-container">
        <div class="paper" id="printableArea">
            
            <div class="paper-content">
                <!-- Document Header -->
                <div class="doc-header">
                    <?php if (file_exists($logoPath)): ?>
                    <div class="logo-container">
                        <img src="<?= htmlspecialchars($logoPath); ?>" alt="School Logo">
                    </div>
                    <?php endif; ?>
                    <div class="title-container">
                        <h1><?= htmlspecialchars($global['nameschool']); ?></h1>
                        <h2><?= htmlspecialchars($reportName); ?></h2>
                        <div class="sub-title"><?= htmlspecialchars($subReportName); ?></div>
                        <div class="meta-info">
                            <span><strong>ภาคเรียนที่:</strong> <?= htmlspecialchars($term); ?>/<?= htmlspecialchars($pee); ?></span>
                            <span><strong>ข้อมูล ณ วันที่:</strong> <?= date('d/m/Y H:i'); ?> น.</span>
                            <span><strong>ผู้จัดพิมพ์:</strong> <?= htmlspecialchars($reporterName); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats Bar -->
                <div class="stats-bar">
                    <div class="stats-item">
                        <span>จำนวนนักเรียน:</span>
                        <strong><?= number_format($totalStudents); ?> คน</strong>
                    </div>
                    <div class="stats-item">
                        <span>ถูกหักคะแนน:</span>
                        <strong class="score-deduct"><?= number_format($deductedStudentsCount); ?> คน</strong>
                    </div>
                    <div class="stats-item">
                        <span>รวมคะแนนที่ถูกหัก:</span>
                        <strong class="score-deduct"><?= number_format($totalDeductedScore); ?> คะแนน</strong>
                    </div>
                    <div class="stats-item">
                        <span>คะแนนคงเหลือเฉลี่ย:</span>
                        <strong><?= $avgRemainingScore; ?> คะแนน</strong>
                    </div>
                </div>

                <!-- Table Section -->
                <?php if ($tab === 'deduct-group'): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 38px;">ลำดับ</th>
                            <th style="width: 80px;">รหัสนักเรียน</th>
                            <th style="width: 44px;">เลขที่</th>
                            <th style="text-align: left; padding-left: 6px;">ชื่อ - สกุล</th>
                            <th style="width: 70px;">ระดับชั้น</th>
                            <th style="width: 78px;">คะแนนที่หัก</th>
                            <th style="width: 85px;">คะแนนคงเหลือ</th>
                            <th style="width: 90px;">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (empty($students)): 
                            echo '<tr><td colspan="8" class="text-center" style="padding: 16px;">ไม่พบข้อมูลนักเรียนตามเงื่อนไขที่ระบุ</td></tr>';
                        else:
                            foreach ($students as $idx => $stu): 
                                $count = (int)$stu['behavior_count'];
                                $score = 100 - $count;
                                $scoreClass = $score < 50 ? 'score-danger' : ($score < 71 ? 'score-warning' : 'score-good');
                                $statusText = $score < 50 ? 'ปรับปรุงเร่งด่วน' : ($score < 71 ? 'เฝ้าระวัง' : ($score < 100 ? 'ตักเตือน' : 'ปกติ'));
                        ?>
                            <tr>
                                <td class="text-center"><?= $idx + 1 ?></td>
                                <td class="text-center font-mono"><?= htmlspecialchars($stu['Stu_id']) ?></td>
                                <td class="text-center font-mono"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                                <td class="text-left" style="padding-left: 6px;"><?= htmlspecialchars($stu['FullName']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($stu['ClassRoom']) ?></td>
                                <td class="text-center score-deduct"><?= $count > 0 ? '-' . $count : '0' ?></td>
                                <td class="text-center font-bold <?= $scoreClass ?>"><?= $score ?></td>
                                <td class="text-center" style="font-size: 10px;"><?= $statusText ?></td>
                            </tr>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </tbody>
                    <?php if (!empty($students)): ?>
                    <tfoot>
                        <tr style="font-weight: 700; background-color: #f1f5f9;">
                            <td colspan="5" class="text-center">รวมทั้งสิ้น (<?= number_format($totalStudents); ?> รายการ)</td>
                            <td class="text-center score-deduct"><?= $totalDeductedScore > 0 ? '-' . number_format($totalDeductedScore) : '0' ?></td>
                            <td class="text-center">เฉลี่ย <?= $avgRemainingScore ?></td>
                            <td class="text-center">-</td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
                
                <?php elseif ($tab === 'deduct-room'): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">เลขที่</th>
                            <th style="width: 80px;">รหัสนักเรียน</th>
                            <th style="text-align: left; padding-left: 6px;">ชื่อ - สกุล</th>
                            <th style="width: 70px;">ระดับชั้น</th>
                            <th style="width: 78px;">คะแนนที่หัก</th>
                            <th style="width: 85px;">คะแนนคงเหลือ</th>
                            <th style="width: 80px;">สถานะ</th>
                            <th style="width: 85px;">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (empty($students)): 
                            echo '<tr><td colspan="8" class="text-center" style="padding: 16px;">ไม่พบข้อมูลนักเรียนตามเงื่อนไขที่ระบุ</td></tr>';
                        else:
                            foreach ($students as $stu): 
                                $count = (int)$stu['behavior_count'];
                                $score = 100 - $count;
                                $scoreClass = $score < 50 ? 'score-danger' : ($score < 71 ? 'score-warning' : 'score-good');
                                $statusBadge = $count === 0 ? '<span class="badge-status badge-normal">ปกติ</span>' : '<span class="badge-status badge-danger">ถูกตัดคะแนน</span>';
                        ?>
                            <tr>
                                <td class="text-center font-mono"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                                <td class="text-center font-mono"><?= htmlspecialchars($stu['Stu_id']) ?></td>
                                <td class="text-left" style="padding-left: 6px;"><?= htmlspecialchars($stu['FullName']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($stu['ClassRoom']) ?></td>
                                <td class="text-center score-deduct"><?= $count > 0 ? '-' . $count : '0' ?></td>
                                <td class="text-center font-bold <?= $scoreClass ?>"><?= $score ?></td>
                                <td class="text-center"><?= $statusBadge ?></td>
                                <td class="text-center" style="color: #64748b;">-</td>
                            </tr>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </tbody>
                    <?php if (!empty($students)): ?>
                    <tfoot>
                        <tr style="font-weight: 700; background-color: #f1f5f9;">
                            <td colspan="4" class="text-center">รวมทั้งสิ้น (<?= number_format($totalStudents); ?> คน / ถูกหักคะแนน <?= number_format($deductedStudentsCount); ?> คน)</td>
                            <td class="text-center score-deduct"><?= $totalDeductedScore > 0 ? '-' . number_format($totalDeductedScore) : '0' ?></td>
                            <td class="text-center">เฉลี่ย <?= $avgRemainingScore ?></td>
                            <td colspan="2" class="text-center">-</td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
                <?php endif; ?>

                <!-- Signatures Section (Hidden by default) -->
                <div class="signature-container" id="signatureBlock" style="display: none;">
                    <div class="signature-box">
                        <p>ลงชื่อ..............................................................</p>
                        <p class="sign-name">( <?= htmlspecialchars($reporterName); ?> )</p>
                        <p class="sign-title">เจ้าหน้าที่งานส่งเสริมวินัยและคุณธรรมจริยธรรม</p>
                        <p class="sign-title">วันที่ ......... เดือน ......................... พ.ศ. ...........</p>
                    </div>
                    <div class="signature-box">
                        <p>ลงชื่อ..............................................................</p>
                        <p class="sign-name">( ............................................................ )</p>
                        <p class="sign-title">หัวหน้ากลุ่มบริหารงานกิจการนักเรียน</p>
                        <p class="sign-title">วันที่ ......... เดือน ......................... พ.ศ. ...........</p>
                    </div>
                </div>
            </div>

            <!-- Document Footer Meta -->
            <div class="doc-footer">
                <div>ระบบบริหารจัดการงานกิจการนักเรียน <?= htmlspecialchars($global['nameTitle']); ?> - <?= htmlspecialchars($global['nameschool']); ?></div>
                <div>พิมพ์เมื่อ: <?= date('d/m/Y H:i:s'); ?> น.</div>
            </div>

        </div>
    </div>

    <script>
        let isFitSinglePage = true;

        function changeDensity(density) {
            const root = document.documentElement;
            if (density === 'ultra-compact') {
                root.style.setProperty('--font-size', '10px');
                root.style.setProperty('--cell-padding', '1.5px 3px');
            } else if (density === 'compact') {
                root.style.setProperty('--font-size', '11.5px');
                root.style.setProperty('--cell-padding', '2.5px 4.5px');
            } else if (density === 'normal') {
                root.style.setProperty('--font-size', '13px');
                root.style.setProperty('--cell-padding', '4px 6px');
            } else if (density === 'large') {
                root.style.setProperty('--font-size', '14px');
                root.style.setProperty('--cell-padding', '6px 8px');
            }
        }

        function toggleFitSinglePage() {
            isFitSinglePage = !isFitSinglePage;
            const btn = document.getElementById('btnFitPage');
            const status = document.getElementById('fitPageStatus');
            const densitySelect = document.getElementById('densitySelect');
            
            if (isFitSinglePage) {
                btn.classList.add('btn-active');
                status.innerText = 'เปิด';
                densitySelect.value = 'compact';
                changeDensity('compact');
            } else {
                btn.classList.remove('btn-active');
                status.innerText = 'ปิด';
                densitySelect.value = 'normal';
                changeDensity('normal');
            }
        }

        function toggleSignatures() {
            const block = document.getElementById('signatureBlock');
            const btnText = document.getElementById('signBtnText');
            if (block) {
                const isHidden = (block.style.display === 'none' || getComputedStyle(block).display === 'none');
                block.style.display = isHidden ? 'flex' : 'none';
                if (btnText) {
                    btnText.innerText = isHidden ? 'ซ่อนช่องลงนาม' : 'แสดงช่องลงนาม';
                }
            }
        }

        // Auto determine optimal density based on student count
        window.onload = function() {
            const totalCount = <?= (int)$totalStudents; ?>;
            const densitySelect = document.getElementById('densitySelect');
            
            if (totalCount > 40) {
                densitySelect.value = 'ultra-compact';
                changeDensity('ultra-compact');
            } else if (totalCount > 25) {
                densitySelect.value = 'compact';
                changeDensity('compact');
            } else {
                // If only a few students, compact or normal
                densitySelect.value = 'compact';
                changeDensity('compact');
            }

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(function() {
                    window.print();
                }, 300);
            }
        };
    </script>
</body>
</html>


