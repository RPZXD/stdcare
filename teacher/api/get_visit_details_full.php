<?php
/**
 * API: Get Visit Details Full (HTML Response)
 * Refactored with Tailwind CSS for Modern Premium UI
 */
session_start();
header('Content-Type: text/html; charset=utf-8');

require_once "../../config/Database.php";
require_once "../../class/UserLogin.php";
require_once "../../class/Utils.php";

// 1. Check Authentication
if (!isset($_SESSION['Teacher_login'])) {
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

    // 5. Questions Definition
    $questions = [
        1 => ["label" => "บ้านที่อยู่อาศัย", "options" => ["บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น"]],
        2 => ["label" => "ระยะทาง", "options" => ["1-5 กิโลเมตร", "6-10 กิโลเมตร", "11-15 กิโลเมตร", "16-20 กิโลเมตร", "20 กม. ขึ้นไป"]],
        3 => ["label" => "การเดินทาง", "options" => ["เดิน", "รถจักรยาน", "รถจักรยานยนต์", "รถยนต์ส่วนตัว", "รถรับส่ง/สาย", "อื่นๆ"]],
        4 => ["label" => "สภาพแวดล้อม", "options" => ["ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง"]],
        5 => ["label" => "อาชีพผู้ปกครอง", "options" => ["เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ"]],
        6 => ["label" => "ที่ทำงานผู้ปกครอง", "options" => ["ในอำเภอเดียวกัน", "ในจังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ"]],
        7 => ["label" => "สถานภาพบิดามารดา", "options" => ["อยู่ด้วยกัน", "หย่าร้าง", "บิดาถึงแก่กรรม", "มารดาถึงแก่กรรม", "ถึงแก่กรรมทั้งคู่"]],
        8 => ["label" => "การอบรมเลี้ยงดู", "options" => ["เข้มงวด", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ"]],
        9 => ["label" => "โรคประจำตัว", "options" => ["ไม่มี", "มี"]],
        10 => ["label" => "ความสัมพันธ์", "options" => ["อบอุ่น", "เฉยๆ", "ห่างเหิน"]],
        11 => ["label" => "หน้าที่ในบ้าน", "options" => ["มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี"]],
        12 => ["label" => "สนิทกับใครที่สุด", "options" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"]],
        13 => ["label" => "รายได้/ใช้จ่าย", "options" => ["เพียงพอ", "ไม่พอในบางครั้ง", "ขัดสน"]],
        14 => ["label" => "ลักษณะเพื่อนเล่น", "options" => ["รุ่นเดียวกัน", "รุ่นน้อง", "รุ่นพี่", "ทุกรุ่น"]],
        15 => ["label" => "การศึกษาต่อ", "options" => ["ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ"]],
        16 => ["label" => "ที่ปรึกษาปัญหา", "options" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"]],
        17 => ["label" => "ความรู้สึกครูมาเยี่ยม", "options" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"]],
        18 => ["label" => "ทัศนคติต่อโรงเรียน", "options" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"]],
    ];

    function getAnswer($idx, $val, $questions) {
        if (!$val || !isset($questions[$idx]['options'][$val-1])) return '-';
        return $questions[$idx]['options'][$val-1];
    }
?>

<div class="space-y-10">
    <!-- Student Quick Profile -->
    <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-900/50 rounded-[2.5rem] p-8 border border-white dark:border-slate-800 shadow-xl overflow-hidden relative">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-500/5 rounded-full blur-3xl"></div>
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
            <div class="relative group">
                <?php 
                    $stuImg = !empty($studentData['Stu_picture']) ? "../img/student/" . $studentData['Stu_picture'] : "../dist/img/default-avatar.svg";
                ?>
                <img src="<?= $stuImg ?>" onerror="this.src='../dist/img/default-avatar.svg';" 
                     class="w-32 h-32 rounded-[2.5rem] object-cover border-4 border-white shadow-2xl group-hover:scale-105 transition-transform duration-500">
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-orange-600 text-white rounded-2xl flex items-center justify-center font-black shadow-lg italic">
                    <?= $studentData['Stu_no'] ?>
                </div>
            </div>
            <div class="flex-1 text-center md:text-left space-y-2">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <h2 class="text-3xl font-black text-slate-800 dark:text-white italic">
                        <?= $studentData['Stu_pre'].$studentData['Stu_name'].' '.$studentData['Stu_sur'] ?>
                    </h2>
                    <span class="px-3 py-1 bg-white/50 dark:bg-slate-800/50 rounded-xl text-xs font-black text-slate-400 border border-slate-200 dark:border-slate-700 italic">
                        ID: <?= $studentData['Stu_id'] ?>
                    </span>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-bold italic">
                    ชั้นมัธยมศึกษาปีที่ <?= $studentData['Stu_major'] ?>/<?= $studentData['Stu_room'] ?>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-500 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-xs"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-300 truncate max-w-[250px]" title="<?= $studentData['Stu_addr'] ?>">
                            <?= $studentData['Stu_addr'] ?: 'ไม่ระบุที่อยู่' ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 flex items-center justify-center">
                            <i class="fas fa-phone-alt text-xs"></i>
                        </div>
                        <span class="text-sm font-black text-slate-600 dark:text-slate-300">
                            <?= $studentData['Par_phone'] ?: ($studentData['Stu_phone'] ?: '-') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visit Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php for($round = 1; $round <= 2; $round++): 
            $data = ($round == 1) ? $round1Data : $round2Data;
        ?>
            <div class="space-y-6">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-10 h-10 <?= $round == 1 ? 'bg-indigo-600' : 'bg-emerald-600' ?> text-white rounded-2xl flex items-center justify-center shadow-lg font-black italic">
                        <?= $round ?>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white italic">การเยี่ยมบ้านภาคเรียนที่ <?= $round ?></h3>
                </div>

                <?php if(!$data): ?>
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] p-10 border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center text-slate-300">
                        <i class="fas fa-clock-rotate-left text-4xl mb-4 opacity-50"></i>
                        <p class="font-black italic uppercase tracking-widest text-xs">อยู่ระหว่างรอดำเนินการ</p>
                    </div>
                <?php else: ?>
                    <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-800 shadow-xl space-y-6">
                        <!-- Questions & Answers -->
                        <div class="grid grid-cols-1 gap-4">
                            <?php for($i = 1; $i <= 18; $i++): 
                                $ans = getAnswer($i, $data['vh'.$i] ?? null, $questions);
                                $label = $questions[$i]['label'];
                            ?>
                                <div class="flex items-start justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 group hover:border-orange-200 transition-colors">
                                    <div class="flex-1">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic"><?= $label ?></p>
                                        <p class="text-sm font-black text-slate-700 dark:text-slate-200"><?= $ans ?></p>
                                    </div>
                                    <div class="w-6 h-6 rounded-lg bg-white dark:bg-slate-900 flex items-center justify-center text-[8px] font-black text-slate-300">
                                        <?= $i ?>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <!-- Problems Section -->
                        <?php if(!empty($data['vh20'])): ?>
                            <div class="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-2xl border border-orange-100 dark:border-orange-900/30">
                                <h4 class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest italic mb-3 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i> ปัญหาและความต้องการ
                                </h4>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 leading-relaxed italic">
                                    <?= nl2br(htmlspecialchars($data['vh20'])) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- Images Gallery -->
                        <?php 
                            $images = [];
                            for($k=1;$k<=5;$k++) if(!empty($data['picture'.$k])) $images[] = $data['picture'.$k];
                            
                            if(!empty($images)):
                        ?>
                            <div class="space-y-3">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic px-1">ภาพประกอบการเยี่ยมบ้าน</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    <?php foreach($images as $img): 
                                        $imgPath = "../teacher/uploads/visithome" . ($pee - 543) . "/" . $img;
                                    ?>
                                        <a href="<?= $imgPath ?>" target="_blank" class="relative group aspect-square overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                                            <img src="<?= $imgPath ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <i class="fas fa-expand text-white"></i>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>

<?php
} catch (Exception $e) {
    echo '<div class="p-8 bg-rose-50 dark:bg-rose-900/20 rounded-3xl border border-rose-100 dark:border-rose-900/30 text-center">';
    echo '  <i class="fas fa-bug text-3xl text-rose-500 mb-4 block"></i>';
    echo '  <p class="font-black italic text-rose-600">เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
?>
