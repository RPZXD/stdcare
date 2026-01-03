<?php
/**
 * EQ Assessment Edit Form Template
 * Modern UI with Tailwind CSS
 */
require_once "../../config/Database.php";
require_once "../../class/EQ.php";

$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['student_name'] ?? '';
$student_no = $_GET['student_no'] ?? '';
$student_class = $_GET['student_class'] ?? '';
$student_room = $_GET['student_room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

// Initialize database and EQ class
$db = (new Database("phichaia_student"))->getConnection();
$eq = new EQ($db);

// Fetch EQ data
$eqData = $eq->getEQData($student_id, $pee, $term);

// Map EQ data keys to match form input names
$mappedEqData = [];
if ($eqData) {
    foreach ($eqData as $key => $value) {
        $mappedKey = strtolower(str_replace('EQ', 'q', $key)); // Convert "EQ1" to "q1"
        $mappedEqData[$mappedKey] = $value;
    }
}

// Questions List (Same as form_eq.php)
$questions = [
    ['q1', 'เข้าใจความรู้สึกของตัวเองเวลาที่โกรธ เสียใจ หรือดีใจ', 'รู้จักและเข้าใจตนเอง'],
    ['q2', 'สามารถบอกความรู้สึกของตัวเองได้เมื่อมีอารมณ์ต่าง ๆ', 'รู้จักและเข้าใจตนเอง'],
    ['q3', 'เห็นข้อดีและข้อเสียของตนเองได้ชัดเจน', 'รู้จักและเข้าใจตนเอง'],
    ['q4', 'เชื่อมั่นในความสามารถของตนเอง', 'รู้จักและเข้าใจตนเอง'],
    ['q5', 'ยอมรับในสิ่งที่ตนเองเป็นได้', 'รู้จักและเข้าใจตนเอง'],
    ['q6', 'สามารถควบคุมอารมณ์ของตนเองเมื่อไม่พอใจ', 'การควบคุมอารมณ์'],
    ['q7', 'ใจเย็นเมื่อมีปัญหาเกิดขึ้น', 'การควบคุมอารมณ์'],
    ['q8', 'อดทนต่อสิ่งเร้าที่ไม่พึงประสงค์ได้ดี', 'การควบคุมอารมณ์'],
    ['q9', 'ไม่แสดงอารมณ์รุนแรงเมื่อมีความขัดแย้ง', 'การควบคุมอารมณ์'],
    ['q10', 'สามารถให้อภัยผู้อื่นได้เมื่อเขาทำผิด', 'การควบคุมอารมณ์'],
    ['q11', 'มีความตั้งใจในการเรียน', 'การมีแรงจูงใจ'],
    ['q12', 'พยายามทำสิ่งต่าง ๆ ให้สำเร็จแม้จะยาก', 'การมีแรงจูงใจ'],
    ['q13', 'ไม่ยอมแพ้ง่าย ๆ เมื่อเจออุปสรรค', 'การมีแรงจูงใจ'],
    ['q14', 'มีเป้าหมายในชีวิต', 'การมีแรงจูงใจ'],
    ['q15', 'รู้สึกภูมิใจเมื่อทำสิ่งดี ๆ ได้สำเร็จ', 'การมีแรงจูงใจ'],
    ['q16', 'เข้าใจความรู้สึกของผู้อื่นได้เมื่อเขาเศร้า', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q17', 'สามารถแสดงความเห็นใจเมื่อเพื่อนมีปัญหา', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q18', 'ช่วยเหลือผู้อื่นโดยไม่หวังผลตอบแทน', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q19', 'เสียใจเมื่อเห็นผู้อื่นเดือดร้อน', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q20', 'ยินดีช่วยเหลือเพื่อนที่อ่อนแอกกว่า', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q21', 'พูดจาสุภาพกับผู้อื่น', 'ทักษะทางสังคม'],
    ['q22', 'มีน้ำใจและแบ่งปันสิ่งของกับเพื่อน', 'ทักษะทางสังคม'],
    ['q23', 'ทำงานร่วมกับผู้อื่นได้ดี', 'ทักษะทางสังคม'],
    ['q24', 'เคารพกฎระเบียบของกลุ่มหรือโรงเรียน', 'ทักษะทางสังคม'],
    ['q25', 'ยอมรับความคิดเห็นของผู้อื่นแม้จะแตกต่าง', 'ทักษะทางสังคม'],
    ['q26', 'กล้าแสดงออกอย่างเหมาะสม', 'รู้จักและเข้าใจตนเอง'],
    ['q27', 'สามารถรับฟังความคิดเห็นของผู้อื่นได้ดี', 'ทักษะทางสังคม'],
    ['q28', 'วางแผนและจัดการเวลาได้เหมาะสม', 'การมีแรงจูงใจ'],
    ['q29', 'สามารถปรับตัวเข้ากับผู้อื่นได้ง่าย', 'ทักษะทางสังคม'],
    ['q30', 'ไม่เก็บความเครียดไว้นาน', 'การควบคุมอารมณ์'],
    ['q31', 'เปิดใจยอมรับการเปลี่ยนแปลง', 'รู้จักและเข้าใจตนเอง'],
    ['q32', 'ขอโทษเมื่อทำผิด', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q33', 'ขอบคุณเมื่อได้รับความช่วยเหลือ', 'ทักษะทางสังคม'],
    ['q34', 'สามารถพูดคุยกับผู้ใหญ่ได้อย่างมั่นใจ', 'รู้จักและเข้าใจตนเอง'],
    ['q35', 'กล้าตัดสินใจในเรื่องที่เหมาะสม', 'การมีแรงจูงใจ'],
    ['q36', 'สามารถขอความช่วยเหลือจากผู้อื่นได้เมื่อจำเป็น', 'ทักษะทางสังคม'],
    ['q37', 'ยอมรับผลจากการกระทำของตนเอง', 'รู้จักและเข้าใจตนเอง'],
    ['q38', 'เรียนรู้จากความผิดพลาดของตนเอง', 'การควบคุมอารมณ์'],
    ['q39', 'มีกำลังใจที่จะทำสิ่งดี ๆ ต่อไป', 'การมีแรงจูงใจ'],
    ['q40', 'ใช้คำพูดที่ให้กำลังใจผู้อื่น', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q41', 'รู้สึกดีกับตัวเองเมื่อทำสิ่งดี ๆ ให้ผู้อื่น', 'รู้จักและเข้าใจตนเอง'],
    ['q42', 'รู้จักเลือกคบเพื่อนที่เหมาะสม', 'ทักษะทางสังคม'],
    ['q43', 'อดทนต่อคำวิจารณ์ได้ดี', 'การควบคุมอารมณ์'],
    ['q44', 'ไม่ตัดสินผู้อื่นจากภายนอก', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q45', 'รับฟังปัญหาของเพื่อนได้อย่างตั้งใจ', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q46', 'สามารถอธิบายความต้องการของตนเองได้ชัดเจน', 'รู้จักและเข้าใจตนเอง'],
    ['q47', 'ไม่โทษผู้อื่นเมื่อเกิดปัญหา', 'การควบคุมอารมณ์'],
    ['q48', 'จัดการความเครียดได้ด้วยกิจกรรมที่ชอบ', 'การควบคุมอารมณ์'],
    ['q49', 'มีวิธีจัดการกับความไม่พอใจได้ดี', 'การควบคุมอารมณ์'],
    ['q50', 'สามารถยอมรับความแตกต่างของผู้อื่นได้', 'การเห็นอกเห็นใจผู้อื่น'],
    ['q51', 'กล้าปฏิเสธเมื่อถูกชักชวนให้ทำสิ่งไม่ดี', 'ทักษะทางสังคม'],
    ['q52', 'สามารถปรับอารมณ์ให้กลับมาสงบได้เร็ว', 'การควบคุมอารมณ์'],
];

$choices = [
    '0' => ['label' => 'ไม่จริง', 'icon' => 'fa-times', 'color' => 'text-rose-500', 'bg' => 'peer-checked:bg-rose-50 peer-checked:border-rose-200'],
    '1' => ['label' => 'จริงบางครั้ง', 'icon' => 'fa-meh', 'color' => 'text-amber-500', 'bg' => 'peer-checked:bg-amber-50 peer-checked:border-amber-200'],
    '2' => ['label' => 'ค่อนข้างจริง', 'icon' => 'fa-smile', 'color' => 'text-blue-500', 'bg' => 'peer-checked:bg-blue-50 peer-checked:border-blue-200'],
    '3' => ['label' => 'จริงมาก', 'icon' => 'fa-check-double', 'color' => 'text-emerald-500', 'bg' => 'peer-checked:bg-emerald-50 peer-checked:border-emerald-200'],
];
?>

<div class="space-y-6">
    <!-- Student Profile Header (Amber for Edit Mode) -->
    <div class="relative overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 rounded-3xl p-6 text-white shadow-lg">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black">แก้ไขข้อมูล EQ: <?= htmlspecialchars($student_name) ?></h3>
                    <div class="flex flex-wrap gap-2 mt-1 opacity-90">
                        <span class="px-2 py-0.5 bg-white/20 rounded-lg text-xs font-bold">เลขประจำตัว: <?= htmlspecialchars($student_id) ?></span>
                        <span class="px-2 py-0.5 bg-white/20 rounded-lg text-xs font-bold">เลขที่: <?= htmlspecialchars($student_no) ?></span>
                        <span class="px-2 py-0.5 bg-white/20 rounded-lg text-xs font-bold">ชั้น: ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?></span>
                    </div>
                </div>
            </div>
            <div class="bg-black/10 backdrop-blur rounded-2xl p-3 text-center border border-white/10">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">ปีการศึกษา / ภาคเรียน</p>
                <p class="text-lg font-black"><?= htmlspecialchars($pee) ?> / <?= htmlspecialchars($term) ?></p>
            </div>
        </div>
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-black/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Info Box -->
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 rounded-2xl p-4 flex gap-4 items-start">
        <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <h4 class="font-bold text-amber-900 dark:text-amber-200">โหมดแก้ไขข้อมูล</h4>
            <p class="text-sm text-amber-800 dark:text-amber-300">คุณกำลังทำการแก้ไขข้อมูลที่ได้บันทึกไว้ก่อนหน้านี้ กรุณาตรวจสอบและปรับปรุงข้อมูลให้ถูกต้อง</p>
        </div>
    </div>

    <form id="eqEditForm" class="space-y-4">
        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
        <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
        <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">

        <div class="grid grid-cols-1 gap-4">
            <?php foreach ($questions as $index => [$id, $text, $category]): ?>
            <div class="glass-card rounded-2xl p-5 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-lg text-xs font-black group-hover:bg-amber-500 group-hover:text-white transition-colors">
                            <?= $index + 1 ?>
                        </span>
                    </div>
                    <div class="flex-grow">
                        <div class="mb-4">
                            <h4 class="text-slate-800 dark:text-white font-bold text-base leading-relaxed"><?= htmlspecialchars($text) ?></h4>
                            <span class="inline-block mt-1 px-2 py-0.5 bg-slate-50 dark:bg-slate-900 text-slate-400 text-[10px] font-bold uppercase tracking-wider rounded border border-slate-100 dark:border-slate-800">
                                <i class="fas fa-tag mr-1 text-[8px]"></i> ด้าน<?= $category ?>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                            <?php foreach ($choices as $value => $info): 
                                $checked = (isset($mappedEqData[$id]) && $mappedEqData[$id] == $value) ? 'checked' : '';
                            ?>
                            <div class="relative">
                                <input type="radio" name="<?= $id ?>" id="<?= $id . '_' . $value ?>" value="<?= $value ?>" <?= $checked ?> required class="peer absolute opacity-0 invisible">
                                <label for="<?= $id . '_' . $value ?>" 
                                    class="flex flex-col items-center justify-center p-3 rounded-xl border-2 border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 cursor-pointer transition-all hover:border-slate-200 dark:hover:border-slate-700 <?= $info['bg'] ?> peer-checked:scale-[0.98]">
                                    <i class="fas <?= $info['icon'] ?> mb-1 text-sm <?= $info['color'] ?>"></i>
                                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 peer-checked:text-slate-800 dark:peer-checked:text-white"><?= $info['label'] ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </form>
</div>
