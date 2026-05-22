<?php
/**
 * Unified EQ Form
 * Modes: add, edit, view
 * Modern UI with Tailwind CSS
 */
session_start();
if (!isset($_SESSION['Student_login'])) {
    echo '<div class="text-red-500 text-center py-4">ไม่ได้รับอนุญาต</div>';
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/EQ.php');

$student_id = $_GET['stuId'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';
$mode = $_GET['mode'] ?? 'add'; // add, edit, view

$db = new Database("phichaia_student");
$conn = $db->getConnection();
$eq = new EQ($conn);

// Get existing data for edit/view mode
$existingData = [];
if ($mode !== 'add') {
    $existingData = $eq->getEQData($student_id, $pee, $term);
    if (!$existingData && $mode === 'view') {
        echo '<div class="text-center py-8"><div class="text-amber-500 font-bold">ยังไม่มีข้อมูลการประเมิน EQ</div></div>';
        exit;
    }
}

$isReadonly = ($mode === 'view');

// EQ Categories with colors
$categories = [
    'รู้จักและเข้าใจตนเอง' => ['icon' => '🧠', 'color' => 'rose'],
    'การควบคุมอารมณ์' => ['icon' => '😌', 'color' => 'amber'],
    'การมีแรงจูงใจ' => ['icon' => '🎯', 'color' => 'emerald'],
    'การเห็นอกเห็นใจผู้อื่น' => ['icon' => '💕', 'color' => 'pink'],
    'ทักษะทางสังคม' => ['icon' => '🤝', 'color' => 'sky'],
];

// Questions with category
$questions = [
    1 => ['text' => 'ฉันเข้าใจความรู้สึกของตัวเองเวลาที่โกรธ เสียใจ หรือดีใจ', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    2 => ['text' => 'ฉันสามารถบอกความรู้สึกของตัวเองได้เมื่อมีอารมณ์ต่าง ๆ', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    3 => ['text' => 'ฉันเห็นข้อดีและข้อเสียของตนเองได้ชัดเจน', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    4 => ['text' => 'ฉันเชื่อมั่นในความสามารถของตนเอง', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    5 => ['text' => 'ฉันยอมรับในสิ่งที่ตนเองเป็นได้', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    6 => ['text' => 'ฉันสามารถควบคุมอารมณ์ของตนเองเมื่อไม่พอใจ', 'cat' => 'การควบคุมอารมณ์'],
    7 => ['text' => 'ฉันใจเย็นเมื่อมีปัญหาเกิดขึ้น', 'cat' => 'การควบคุมอารมณ์'],
    8 => ['text' => 'ฉันอดทนต่อสิ่งเร้าที่ไม่พึงประสงค์ได้ดี', 'cat' => 'การควบคุมอารมณ์'],
    9 => ['text' => 'ฉันไม่แสดงอารมณ์รุนแรงเมื่อมีความขัดแย้ง', 'cat' => 'การควบคุมอารมณ์'],
    10 => ['text' => 'ฉันสามารถให้อภัยผู้อื่นได้เมื่อเขาทำผิด', 'cat' => 'การควบคุมอารมณ์'],
    11 => ['text' => 'ฉันมีความตั้งใจในการเรียน', 'cat' => 'การมีแรงจูงใจ'],
    12 => ['text' => 'ฉันพยายามทำสิ่งต่าง ๆ ให้สำเร็จแม้จะยาก', 'cat' => 'การมีแรงจูงใจ'],
    13 => ['text' => 'ฉันไม่ยอมแพ้ง่าย ๆ เมื่อเจออุปสรรค', 'cat' => 'การมีแรงจูงใจ'],
    14 => ['text' => 'ฉันมีเป้าหมายในชีวิต', 'cat' => 'การมีแรงจูงใจ'],
    15 => ['text' => 'ฉันรู้สึกภูมิใจเมื่อทำสิ่งดี ๆ ได้สำเร็จ', 'cat' => 'การมีแรงจูงใจ'],
    16 => ['text' => 'ฉันเข้าใจความรู้สึกของผู้อื่นได้เมื่อเขาเศร้า', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    17 => ['text' => 'ฉันสามารถแสดงความเห็นใจเมื่อเพื่อนมีปัญหา', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    18 => ['text' => 'ฉันช่วยเหลือผู้อื่นโดยไม่หวังผลตอบแทน', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    19 => ['text' => 'ฉันเสียใจเมื่อเห็นผู้อื่นเดือดร้อน', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    20 => ['text' => 'ฉันยินดีช่วยเหลือเพื่อนที่อ่อนแอกว่า', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    21 => ['text' => 'ฉันพูดจาสุภาพกับผู้อื่น', 'cat' => 'ทักษะทางสังคม'],
    22 => ['text' => 'ฉันมีน้ำใจและแบ่งปันสิ่งของกับเพื่อน', 'cat' => 'ทักษะทางสังคม'],
    23 => ['text' => 'ฉันทำงานร่วมกับผู้อื่นได้ดี', 'cat' => 'ทักษะทางสังคม'],
    24 => ['text' => 'ฉันเคารพกฎระเบียบของกลุ่มหรือโรงเรียน', 'cat' => 'ทักษะทางสังคม'],
    25 => ['text' => 'ฉันยอมรับความคิดเห็นของผู้อื่นแม้จะแตกต่าง', 'cat' => 'ทักษะทางสังคม'],
    26 => ['text' => 'ฉันกล้าแสดงออกอย่างเหมาะสม', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    27 => ['text' => 'ฉันสามารถรับฟังความคิดเห็นของผู้อื่นได้ดี', 'cat' => 'ทักษะทางสังคม'],
    28 => ['text' => 'ฉันวางแผนและจัดการเวลาได้เหมาะสม', 'cat' => 'การมีแรงจูงใจ'],
    29 => ['text' => 'ฉันสามารถปรับตัวเข้ากับผู้อื่นได้ง่าย', 'cat' => 'ทักษะทางสังคม'],
    30 => ['text' => 'ฉันไม่เก็บความเครียดไว้นาน', 'cat' => 'การควบคุมอารมณ์'],
    31 => ['text' => 'ฉันเปิดใจยอมรับการเปลี่ยนแปลง', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    32 => ['text' => 'ฉันขอโทษเมื่อทำผิด', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    33 => ['text' => 'ฉันขอบคุณเมื่อได้รับความช่วยเหลือ', 'cat' => 'ทักษะทางสังคม'],
    34 => ['text' => 'ฉันสามารถพูดคุยกับผู้ใหญ่ได้อย่างมั่นใจ', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    35 => ['text' => 'ฉันกล้าตัดสินใจในเรื่องที่เหมาะสม', 'cat' => 'การมีแรงจูงใจ'],
    36 => ['text' => 'ฉันสามารถขอความช่วยเหลือจากผู้อื่นได้เมื่อจำเป็น', 'cat' => 'ทักษะทางสังคม'],
    37 => ['text' => 'ฉันยอมรับผลจากการกระทำของตนเอง', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    38 => ['text' => 'ฉันเรียนรู้จากความผิดพลาดของตนเอง', 'cat' => 'การควบคุมอารมณ์'],
    39 => ['text' => 'ฉันมีกำลังใจที่จะทำสิ่งดี ๆ ต่อไป', 'cat' => 'การมีแรงจูงใจ'],
    40 => ['text' => 'ฉันใช้คำพูดที่ให้กำลังใจผู้อื่น', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    41 => ['text' => 'ฉันรู้สึกดีกับตัวเองเมื่อทำสิ่งดี ๆ ให้ผู้อื่น', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    42 => ['text' => 'ฉันรู้จักเลือกคบเพื่อนที่เหมาะสม', 'cat' => 'ทักษะทางสังคม'],
    43 => ['text' => 'ฉันอดทนต่อคำวิจารณ์ได้ดี', 'cat' => 'การควบคุมอารมณ์'],
    44 => ['text' => 'ฉันไม่ตัดสินผู้อื่นจากภายนอก', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    45 => ['text' => 'ฉันรับฟังปัญหาของเพื่อนได้อย่างตั้งใจ', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    46 => ['text' => 'ฉันสามารถอธิบายความต้องการของตนเองได้ชัดเจน', 'cat' => 'รู้จักและเข้าใจตนเอง'],
    47 => ['text' => 'ฉันไม่โทษผู้อื่นเมื่อเกิดปัญหา', 'cat' => 'การควบคุมอารมณ์'],
    48 => ['text' => 'ฉันจัดการความเครียดได้ด้วยกิจกรรมที่ชอบ', 'cat' => 'การควบคุมอารมณ์'],
    49 => ['text' => 'ฉันมีวิธีจัดการกับความไม่พอใจได้ดี', 'cat' => 'การควบคุมอารมณ์'],
    50 => ['text' => 'ฉันสามารถยอมรับความแตกต่างของผู้อื่นได้', 'cat' => 'การเห็นอกเห็นใจผู้อื่น'],
    51 => ['text' => 'ฉันกล้าปฏิเสธเมื่อถูกชักชวนให้ทำสิ่งไม่ดี', 'cat' => 'ทักษะทางสังคม'],
    52 => ['text' => 'ฉันสามารถปรับอารมณ์ให้กลับมาสงบได้เร็ว', 'cat' => 'การควบคุมอารมณ์'],
];

$choices = [
    0 => ['label' => 'ไม่จริง', 'icon' => '❌'],
    1 => ['label' => 'จริงบางครั้ง', 'icon' => '😐'],
    2 => ['label' => 'ค่อนข้างจริง', 'icon' => '🙂'],
    3 => ['label' => 'จริงมาก', 'icon' => '✅'],
];

$modeColors = [
    'add' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/20', 'border' => 'border-emerald-400 dark:border-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400'],
    'edit' => ['bg' => 'bg-amber-50 dark:bg-amber-950/20', 'border' => 'border-amber-400 dark:border-amber-500', 'text' => 'text-amber-600 dark:text-amber-400'],
    'view' => ['bg' => 'bg-blue-50 dark:bg-blue-950/20', 'border' => 'border-blue-400 dark:border-blue-500', 'text' => 'text-blue-600 dark:text-blue-400'],
];
$mc = $modeColors[$mode];
?>

<!-- Info Banner -->
<div class="<?= $mc['bg'] ?> border-l-4 <?= $mc['border'] ?> rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 <?= $mc['bg'] ?> rounded-lg flex items-center justify-center">
            <i class="fas fa-heart <?= $mc['text'] ?>"></i>
        </div>
        <div>
            <h5 class="font-bold <?= $mc['text'] ?>">
                <?php if ($mode === 'view'): ?>ดูข้อมูลการประเมิน EQ<?php elseif ($mode === 'edit'): ?>แก้ไขข้อมูลการประเมิน EQ<?php else: ?>ทำแบบประเมิน EQ<?php endif; ?>
            </h5>
            <p class="text-sm text-slate-500 dark:text-slate-400">กรุณาเลือกคำตอบที่ตรงกับตัวคุณในช่วง 6 เดือนที่ผ่านมา</p>
        </div>
    </div>
</div>

<!-- Progress indicator -->
<?php if (!$isReadonly): ?>
<div class="mb-4 bg-white dark:bg-slate-800 rounded-xl p-3 shadow-sm border border-slate-100 dark:border-slate-700">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-bold text-slate-600 dark:text-slate-400">ความคืบหน้า</span>
        <span class="text-sm font-bold text-pink-500" id="progressText">0/52</span>
    </div>
    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
        <div id="progressBar" class="bg-gradient-to-r from-rose-500 to-pink-500 h-2 rounded-full transition-all" style="width: 0%"></div>
    </div>
</div>
<?php endif; ?>

<form id="eqForm">
    <input type="hidden" name="stuId" value="<?= htmlspecialchars($student_id) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
    
    <div class="space-y-3">
        <?php foreach ($questions as $num => $q): 
            $currentValue = $existingData["EQ$num"] ?? null;
            $catInfo = $categories[$q['cat']] ?? ['icon' => '📝', 'color' => 'slate'];
            $color = $catInfo['color'];
        ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-start gap-3 mb-3">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0" 
                      style="background: linear-gradient(135deg, <?= $color === 'rose' ? '#f43f5e' : ($color === 'amber' ? '#f59e0b' : ($color === 'emerald' ? '#10b981' : ($color === 'pink' ? '#ec4899' : '#0ea5e9'))) ?>, <?= $color === 'rose' ? '#e11d48' : ($color === 'amber' ? '#d97706' : ($color === 'emerald' ? '#059669' : ($color === 'pink' ? '#db2777' : '#0284c7'))) ?>);">
                    <?= $num ?>
                </span>
                <div class="flex-1">
                    <p class="font-medium text-slate-700 dark:text-slate-300 text-sm md:text-base">
                        <?= htmlspecialchars($q['text']) ?>
                    </p>
                    <span class="inline-flex items-center gap-1 text-xs text-slate-400 mt-1">
                        <?= $catInfo['icon'] ?> <?= $q['cat'] ?>
                    </span>
                </div>
            </div>
            
            <!-- Choices - Grid on mobile -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-3">
                <?php foreach ($choices as $value => $choice): 
                    $radioId = "eq{$num}_{$value}";
                    $isChecked = ($currentValue !== null && (int)$currentValue === $value);
                ?>
                <label for="<?= $radioId ?>" class="flex flex-col items-center gap-1 p-2.5 rounded-lg cursor-pointer transition-all border-2 text-center
                    <?php if ($isReadonly): ?>
                        <?= $isChecked ? 'bg-pink-100 dark:bg-pink-900/30 border-pink-400 dark:border-pink-500' : 'bg-slate-50 dark:bg-slate-700/50 border-transparent dark:border-slate-700/30 opacity-50' ?>
                    <?php else: ?>
                        hover:bg-pink-50 dark:hover:bg-pink-900/20 border-transparent dark:border-slate-700/30 has-[:checked]:bg-pink-100 has-[:checked]:border-pink-400 dark:has-[:checked]:bg-pink-900/30 dark:has-[:checked]:border-pink-500
                    <?php endif; ?>">
                    <input type="radio" 
                           id="<?= $radioId ?>" 
                           name="eq<?= $num ?>" 
                           value="<?= $value ?>"
                           <?= $isChecked ? 'checked' : '' ?>
                           <?= $isReadonly ? 'disabled' : 'required' ?>
                           class="w-4 h-4 text-pink-500 focus:ring-pink-400 question-radio">
                    <span class="text-lg"><?= $choice['icon'] ?></span>
                    <span class="text-[10px] md:text-xs font-medium text-slate-600 dark:text-slate-300 leading-tight"><?= $choice['label'] ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</form>

<?php if (!$isReadonly): ?>
<script>
// Progress tracking
document.querySelectorAll('.question-radio').forEach(radio => {
    radio.addEventListener('change', updateProgress);
});

function updateProgress() {
    let answered = 0;
    for (let i = 1; i <= 52; i++) {
        if (document.querySelector('input[name="eq' + i + '"]:checked')) {
            answered++;
        }
    }
    const percent = Math.round((answered / 52) * 100);
    document.getElementById('progressBar').style.width = percent + '%';
    document.getElementById('progressText').textContent = answered + '/52';
}

// Initial progress
updateProgress();
</script>
<?php endif; ?>
