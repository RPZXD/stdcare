<?php
/**
 * API: Get Visit Details Full (HTML Response)
 * Clean Minimal Report UI & Optimized A4 Perfect Print
 * (No Borders, Large Profile Image, No Signatures)
 */
session_start();
header('Content-Type: text/html; charset=utf-8');

require_once "../../config/Database.php";
require_once "../../class/UserLogin.php";
require_once "../../class/Utils.php";

// 1. Check Authentication
if (!isset($_SESSION['Teacher_login']) && !isset($_SESSION['Officer_login'])) {
    echo '<div class="p-8 text-center text-rose-500 font-black italic">! ไม่ได้รับอนุญาตให้เข้าถึงข้อมูล</div>';
    exit;
}

// 2. Initialize Database Connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);

$student_id = $_GET['student_id'] ?? '';
$pee = $user->getPee();

if (empty($student_id)) {
    echo '<div class="p-8 text-center text-amber-500 font-black italic">! ไม่พบเลขประจำตัวนักเรียน</div>';
    exit;
}

try {
    // 3. Fetch Student Data
    $studentSql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, 
                          Stu_addr, Stu_phone, Par_phone, Stu_no, Stu_picture
                   FROM student 
                   WHERE Stu_id = :student_id";
    $studentStmt = $db->prepare($studentSql);
    $studentStmt->bindParam(':student_id', $student_id);
    $studentStmt->execute();
    $studentData = $studentStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$studentData) {
        echo '<div class="p-8 text-center text-rose-500 font-black italic">! ไม่พบข้อมูลนักเรียนในระบบ</div>';
        exit;
    }
    
    // Save search history in session
    if (!isset($_SESSION['visithome_search_history'])) {
        $_SESSION['visithome_search_history'] = [];
    }
    // Remove duplicate entry if it exists to push it to the front
    $_SESSION['visithome_search_history'] = array_values(array_filter(
        $_SESSION['visithome_search_history'],
        function($item) use ($student_id) {
            return $item['Stu_id'] != $student_id;
        }
    ));
    // Add student details to the search history session
    array_unshift($_SESSION['visithome_search_history'], [
        'Stu_id' => $studentData['Stu_id'],
        'Stu_pre' => $studentData['Stu_pre'],
        'Stu_name' => $studentData['Stu_name'],
        'Stu_sur' => $studentData['Stu_sur'],
        'Stu_major' => $studentData['Stu_major'],
        'Stu_room' => $studentData['Stu_room']
    ]);
    // Limit suggestions to 3 items
    $_SESSION['visithome_search_history'] = array_slice($_SESSION['visithome_search_history'], 0, 3);
    
    // Fetch room advisors
    $advisors = [];
    if (!empty($studentData['Stu_major']) && !empty($studentData['Stu_room'])) {
        $advisorSql = "SELECT Teach_name FROM teacher WHERE Teach_class = :class AND Teach_room = :room AND Teach_status = 1";
        $advisorStmt = $db->prepare($advisorSql);
        $advisorStmt->execute(['class' => $studentData['Stu_major'], 'room' => $studentData['Stu_room']]);
        $advisors = $advisorStmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // 4. Fetch Visit Data
    $round1Data = null;
    $round2Data = null;
    
    $visitSql = "SELECT * FROM visithome WHERE Stu_id = :student_id AND Term = :term AND Pee = :pee";
    
    $stmt1 = $db->prepare($visitSql);
    $stmt1->execute(['student_id' => $student_id, 'term' => '1', 'pee' => $pee]);
    $round1Data = $stmt1->fetch(PDO::FETCH_ASSOC);
    
    $stmt2 = $db->prepare($visitSql);
    $stmt2->execute(['student_id' => $student_id, 'term' => '2', 'pee' => $pee]);
    $round2Data = $stmt2->fetch(PDO::FETCH_ASSOC);

    $hasRound1 = !empty($round1Data);
    $hasRound2 = !empty($round2Data);
    $activeRoundsCount = ($hasRound1 ? 1 : 0) + ($hasRound2 ? 1 : 0);

    // 5. Questions Definition
    $questions = [
        1 => ["label" => "บ้านที่อยู่อาศัย", "options" => ["บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น"]],
        2 => ["label" => "ระยะทางไป รร.", "options" => ["1-5 กิโลเมตร", "6-10 กิโลเมตร", "11-15 กิโลเมตร", "16-20 กิโลเมตร", "20 กม. ขึ้นไป"]],
        3 => ["label" => "การเดินทาง", "options" => ["เดิน", "รถจักรยาน", "รถจักรยานยนต์", "รถยนต์ส่วนตัว", "รถรับส่ง/สาย", "อื่นๆ"]],
        4 => ["label" => "สภาพแวดล้อม", "options" => ["ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง"]],
        5 => ["label" => "อาชีพผู้ปกครอง", "options" => ["เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ"]],
        6 => ["label" => "ที่ทำงานผู้ปกครอง", "options" => ["ในอำเภอเดียวกัน", "ในจังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ"]],
        7 => ["label" => "สถานภาพบิดามารดา", "options" => ["อยู่ด้วยกัน", "หย่าร้าง", "บิดาถึงแก่กรรม", "มารดาถึงแก่กรรม", "ถึงแก่กรรมทั้งคู่"]],
        8 => ["label" => "การอบรมเลี้ยงดู", "options" => ["เข้มงวด", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ"]],
        9 => ["label" => "โรคประจำตัว", "options" => ["ไม่มี", "มี"]],
        10 => ["label" => "ความสัมพันธ์ในบ้าน", "options" => ["อบอุ่น", "เฉยๆ", "ห่างเหิน"]],
        11 => ["label" => "หน้าที่ในบ้าน", "options" => ["มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี"]],
        12 => ["label" => "สนิทกับใครที่สุด", "options" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"]],
        13 => ["label" => "รายได้/ใช้จ่าย", "options" => ["เพียงพอ", "ไม่พอในบางครั้ง", "ขัดสน"]],
        14 => ["label" => "ลักษณะเพื่อนเล่น", "options" => ["รุ่นเดียวกัน", "รุ่นน้อง", "รุ่นพี่", "ทุกรุ่น"]],
        15 => ["label" => "การศึกษาต่อ", "options" => ["ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ"]],
        16 => ["label" => "ที่ปรึกษาปัญหา", "options" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"]],
        17 => ["label" => "รู้สึกเมื่อครูมาเยี่ยม", "options" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"]],
        18 => ["label" => "ทัศนคติต่อโรงเรียน", "options" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"]],
    ];

    function getAnswer($idx, $val, $questions) {
        if (!$val || !isset($questions[$idx]['options'][$val-1])) return '-';
        return $questions[$idx]['options'][$val-1];
    }
?>

<?php
    $configPath = __DIR__ . '/../../config.json';
    $config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];
    $global = $config['global'] ?? ['nameschool' => 'โรงเรียนพิชัย'];
?>

<style>
    /* Screen Styles */
    @media screen {
        .print-only {
            display: none !important;
        }
    }
    
    /* Print Styles */
    @media print {
        .no-print {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body, html {
            background-color: #fff !important;
            color: #000 !important;
            font-family: 'TH Sarabun New', 'TH Sarabun PSK', 'Sarabun', sans-serif !important;
            font-size: 15px !important;
            line-height: 1.35 !important;
        }
        /* Override parent print view styles for print-only elements */
        #reportContent img.garuda-logo {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            margin: 0 auto 12px auto !important;
            display: block !important;
        }
        #reportContent img.print-avatar {
            border: 1px solid #000 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            display: block !important;
        }
    }
</style>

<!-- Screen-Only Container (Retains user's beautiful modern UI/UX) -->
<div class="no-print space-y-8 font-sans text-slate-800 dark:text-slate-100">
    <!-- Header หัวรายงานเอกสาร (แสดงบนหน้าจอและหน้าพิมพ์แบบเรียบง่าย) -->
    <div class="border-b border-slate-200 dark:border-slate-700 pb-4">

        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1"><?= htmlspecialchars($global['nameschool']) ?></p>
    </div>

    <!-- Student Quick Profile (รูปซ้ายใหญ่ / ข้อมูลขวา / ไม่มีกรอบ) -->
    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8 py-2">
        <div class="flex-shrink-0">
            <?php 
                $stuImg = !empty($studentData['Stu_picture']) ? "../photo/" . $studentData['Stu_picture'] : "../dist/img/default-avatar.svg";
            ?>
            <img src="<?= $stuImg ?>" onerror="this.src='../dist/img/default-avatar.svg';" 
                 class="w-36 h-44 rounded-2xl object-cover shadow-md border border-slate-100 dark:border-slate-800 print:w-32 print:h-40 print:shadow-none">
        </div>
        <div class="flex-1 space-y-3 text-center sm:text-left pt-2">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                    <?= $studentData['Stu_pre'].$studentData['Stu_name'].' '.$studentData['Stu_sur'] ?>
                </h2>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
                    เลขประจำตัวนักเรียน: <?= $studentData['Stu_id'] ?> &bull; เลขที่: <?= $studentData['Stu_no'] ?>
                </p>
            </div>
            
            <div class="text-base font-semibold text-slate-700 dark:text-slate-300">
                ชั้นมัธยมศึกษาปีที่ <?= $studentData['Stu_major'] ?>/<?= $studentData['Stu_room'] ?>
            </div>

            <div class="pt-2 space-y-1.5 text-sm text-slate-600 dark:text-slate-400 border-t border-dashed border-slate-200 dark:border-slate-700">
                <div><strong>ที่อยู่:</strong> <?= $studentData['Stu_addr'] ?: 'ไม่ระบุที่อยู่' ?></div>
                <div><strong>เบอร์โทรศัพท์ติดต่อ:</strong> <?= $studentData['Par_phone'] ?: ($studentData['Stu_phone'] ?: '-') ?></div>
            </div>
        </div>
    </div>

    <!-- Visit Details Grid (แบบข้อความล้วน / ไม่มีกรอบกล่อง) -->
    <div class="grid grid-cols-1 <?= $activeRoundsCount > 1 ? 'lg:grid-cols-2' : '' ?> gap-10 pt-4 print-layout-grid">
        <?php for($round = 1; $round <= 2; $round++): 
            $data = ($round == 1) ? $round1Data : $round2Data;
            if(!$data) continue; 
        ?>
            <div class="space-y-4">
                <div class="border-b-2 border-slate-800 dark:border-slate-200 pb-1.5">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        ข้อมูลการเยี่ยมบ้าน ภาคเรียนที่ <?= $round ?>
                    </h3>
                </div>

                <!-- รายการคำถามคำตอบในรูปแบบ Text รายงาน -->
                <div class="grid grid-cols-1 <?= $activeRoundsCount == 1 ? 'sm:grid-cols-2' : '' ?> gap-x-8 gap-y-2.5 text-sm print-sub-grid">
                    <?php for($i = 1; $i <= 18; $i++): 
                        $ans = getAnswer($i, $data['vh'.$i] ?? null, $questions);
                        $label = $questions[$i]['label'];
                    ?>
                        <div class="flex items-baseline justify-between border-b border-dotted border-slate-200 dark:border-slate-700 pb-1">
                            <span class="text-slate-500 dark:text-slate-400 pr-4"><?= $i ?>. <?= $label ?></span>
                            <span class="font-bold text-slate-900 dark:text-white text-right"><?= $ans ?></span>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- ปัญหาและความต้องการ -->
                <?php if(!empty($data['vh20'])): ?>
                    <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                            [ปัญหาและความต้องการที่พบ]
                        </h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed pl-4 border-l-2 border-slate-300 dark:border-slate-600">
                            <?= nl2br(htmlspecialchars($data['vh20'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- ภาพประกอบ (ซ่อนตอนพิมพ์อัตโนมัติเพื่อคุมระยะหน้ากระดาษ) -->
                <?php 
                    $images = [];
                    for($k=1;$k<=5;$k++) if(!empty($data['picture'.$k])) $images[] = $data['picture'.$k];
                    if(!empty($images)):
                ?>
                    <div class="space-y-2 pt-4 no-print">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">ภาพประกอบการเยี่ยมบ้าน</span>
                        <div class="grid grid-cols-3 gap-2">
                            <?php foreach($images as $img): 
                                $imgPath = "../teacher/uploads/visithome" . ($pee - 543) . "/" . $img;
                            ?>
                                <div class="aspect-square overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-800">
                                    <img src="<?= $imgPath ?>" class="w-full h-full object-cover">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>

<!-- Print-Only Container (Formal Thai Government Document Style) -->
<div class="print-only" style="display: none; font-family: 'TH Sarabun New', 'TH Sarabun PSK', 'Sarabun', sans-serif; color: #000; background-color: #fff;">
    
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="font-size: 20px; font-weight: bold; margin: 0 0 4px 0; line-height: 1.4;">แบบรายงานการเยี่ยมบ้านนักเรียนรายบุคคล</h2>
        <h3 style="font-size: 16px; font-weight: bold; margin: 0 0 4px 0; line-height: 1.4;">โรงเรียน<?= htmlspecialchars($global['nameschool']) ?></h3>
        <p style="font-size: 15px; margin: 0; line-height: 1.4;">ภาคเรียนที่ 1 และภาคเรียนที่ 2 ปีการศึกษา <?= ($pee) ?></p>
    </div>

    <!-- Student Profile Information Table -->
    <?php
    $phones = [];
    if (!empty($studentData['Par_phone'])) {
        $phones[] = $studentData['Par_phone'] . ' (ผู้ปกครอง)';
    }
    if (!empty($studentData['Stu_phone'])) {
        $phones[] = $studentData['Stu_phone'] . ' (นักเรียน)';
    }
    $phoneStr = !empty($phones) ? implode(' / ', $phones) : '-';
    ?>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 15px; line-height: 1.6;">
        <tr>
            <td style="width: 105px; padding: 4px 0; vertical-align: top;" rowspan="3">
                <img src="<?= $stuImg ?>" onerror="this.src='../dist/img/default-avatar.svg';" class="print-avatar" style="width: 90px; height: 112px; object-fit: cover; border: 1px solid #000; display: block;">
            </td>
            <td style="padding: 4px 8px; font-weight: bold; border-bottom: 1px dotted #000;">
                ชื่อ-นามสกุล: <span style="font-weight: normal;"><?= htmlspecialchars($studentData['Stu_pre'].$studentData['Stu_name'].' '.$studentData['Stu_sur']) ?></span>
            </td>
            <td style="padding: 4px 8px; font-weight: bold; border-bottom: 1px dotted #000; width: 170px;">
                เลขประจำตัว: <span style="font-weight: normal;"><?= htmlspecialchars($studentData['Stu_id']) ?></span>
            </td>
            <td style="padding: 4px 8px; font-weight: bold; border-bottom: 1px dotted #000; width: 100px;">
                เลขที่: <span style="font-weight: normal;"><?= htmlspecialchars($studentData['Stu_no']) ?></span>
            </td>
        </tr>
        <tr>
            <td style="padding: 4px 8px; font-weight: bold; border-bottom: 1px dotted #000;">
                ชั้นมัธยมศึกษาปีที่: <span style="font-weight: normal;"><?= htmlspecialchars($studentData['Stu_major']) ?> / <?= htmlspecialchars($studentData['Stu_room']) ?></span>
            </td>
            <td style="padding: 4px 8px; font-weight: bold; border-bottom: 1px dotted #000;" colspan="2">
                เบอร์โทรศัพท์: <span style="font-weight: normal;"><?= htmlspecialchars($phoneStr) ?></span>
            </td>
        </tr>
        <tr>
            <td style="padding: 4px 8px; font-weight: bold; border-bottom: 1px dotted #000;" colspan="3">
                ที่อยู่: <span style="font-weight: normal;"><?= htmlspecialchars($studentData['Stu_addr'] ?: 'ไม่ระบุที่อยู่') ?></span>
            </td>
        </tr>
    </table>

    <!-- Comparison Table (Semester 1 vs Semester 2) -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px; line-height: 1.3;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 35px; font-weight: bold;">ที่</th>
                <th style="border: 1px solid #000; padding: 6px 8px; text-align: left; font-weight: bold;">รายการข้อมูลการเยี่ยมบ้าน</th>
                <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 190px; font-weight: bold;">ผลการเยี่ยมบ้าน ภาคเรียนที่ 1</th>
                <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 190px; font-weight: bold;">ผลการเยี่ยมบ้าน ภาคเรียนที่ 2</th>
            </tr>
        </thead>
        <tbody>
            <?php for($i = 1; $i <= 18; $i++): 
                $ans1 = getAnswer($i, $round1Data['vh'.$i] ?? null, $questions);
                $ans2 = getAnswer($i, $round2Data['vh'.$i] ?? null, $questions);
                $label = $questions[$i]['label'];
            ?>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px 4px; text-align: center;"><?= $i ?></td>
                    <td style="border: 1px solid #000; padding: 5px 8px;"><?= htmlspecialchars($label) ?></td>
                    <td style="border: 1px solid #000; padding: 5px 4px; text-align: center; font-weight: <?= ($ans1 != '-') ? 'bold' : 'normal' ?>;"><?= htmlspecialchars($ans1) ?></td>
                    <td style="border: 1px solid #000; padding: 5px 4px; text-align: center; font-weight: <?= ($ans2 != '-') ? 'bold' : 'normal' ?>;"><?= htmlspecialchars($ans2) ?></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- Problems and Needs Section (Print-Only) -->
    <?php if(!empty($round1Data['vh20']) || !empty($round2Data['vh20'])): ?>
        <div style="margin-bottom: 25px; border: 1px solid #000; padding: 12px; border-radius: 4px; page-break-inside: avoid; font-size: 14px;">
            <h4 style="font-size: 15px; font-weight: bold; margin: 0 0 8px 0; border-bottom: 1px solid #000; padding-bottom: 4px;">ปัญหาและความต้องการที่พบ / ข้อเสนอแนะเพิ่มเติม</h4>
            <?php if(!empty($round1Data['vh20'])): ?>
                <div style="margin-bottom: 6px;">
                    <strong>ภาคเรียนที่ 1:</strong> <?= nl2br(htmlspecialchars($round1Data['vh20'])) ?>
                </div>
            <?php endif; ?>
            <?php if(!empty($round2Data['vh20'])): ?>
                <div style="margin-bottom: 6px;">
                    <strong>ภาคเรียนที่ 2:</strong> <?= nl2br(htmlspecialchars($round2Data['vh20'])) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
	
	<!-- เพิ่มโค้ดส่วนนี้ต่อจากข้อมูลปัญหาและความต้องการ (ภายในคลาส print-only) -->
    <?php 
        // รวบรวมรูปภาพจากทั้ง 2 ภาคเรียน
        $printImages = [];
        
        // รูปภาคเรียนที่ 1
        if ($hasRound1) {
            for($k=1; $k<=5; $k++) {
                if(!empty($round1Data['picture'.$k])) {
                    $printImages[] = [
                        'path' => "../teacher/uploads/visithome" . ($pee - 543) . "/" . $round1Data['picture'.$k],
                        'label' => 'เทอม 1'
                    ];
                }
            }
        }
        
        // รูปภาคเรียนที่ 2
        if ($hasRound2) {
            for($k=1; $k<=5; $k++) {
                if(!empty($round2Data['picture'.$k])) {
                    $printImages[] = [
                        'path' => "../teacher/uploads/visithome" . ($pee - 543) . "/" . $round2Data['picture'.$k],
                        'label' => 'เทอม 2'
                    ];
                }
            }
        }

        if(!empty($printImages)): 
    ?>
        <div style="margin-top: 15px; page-break-inside: avoid;">
            <h4 style="font-size: 15px; font-weight: bold; margin: 0 0 10px 0; border-bottom: 1px solid #000; padding-bottom: 4px;">
                ภาพประกอบการเยี่ยมบ้านนักเรียน
            </h4>
            
            <!-- ใช้ CSS Table Layout เพื่อความเสถียรในการสั่งพิมพ์ (เลียนแบบ Grid 3 คอลัมน์) -->
            <table style="width: 100%; border-collapse: separate; border-spacing: 8px; margin: -8px;">
                <tr>
                    <?php 
                    $count = 0;
                    foreach($printImages as $imgItem): 
                        // ถ้าครบ 3 รูปให้ขึ้นแถวใหม่
                        if ($count > 0 && $count % 3 == 0) {
                            echo '</tr><tr>';
                        }
                        $count++;
                    ?>
                        <td style="width: 33.33%; text-align: center; vertical-align: top; border: 1px solid #ddd; padding: 4px; background: #fafafa;">
                            <img src="<?= $imgItem['path'] ?>" style="width: 100%; height: 200px; object-fit: cover; display: block;" onerror="this.style.display='none';">
                            <div style="font-size: 12px; margin-top: 4px; color: #555;">(ภาพถ่าย<?= $imgItem['label'] ?>)</div>
                        </td>
                    <?php endforeach; ?>
                    
                    <!-- เติมคอลัมน์ว่างให้เต็มแถว (ถ้ามีรูปไม่ครบจำนวนทวีคูณของ 3) -->
                    <?php while ($count % 3 != 0): $count++; ?>
                        <td style="width: 33.33%;"></td>
                    <?php endwhile; ?>
                </tr>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php
} catch (Exception $e) {
    echo '<div class="p-4 text-center text-rose-600 font-bold border border-rose-200 rounded-xl">';
    echo 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>