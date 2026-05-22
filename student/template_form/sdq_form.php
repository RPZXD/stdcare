<?php
/**
 * Unified SDQ Form
 * Modes: add, edit, view
 * Types: self, parent
 * Modern UI with Tailwind CSS
 */
include_once("../../config/Database.php");
include_once("../../class/SDQ.php");

$stuId = $_GET['stuId'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';
$type = $_GET['type'] ?? 'self'; // self or parent
$mode = $_GET['mode'] ?? 'add'; // add, edit, view

$db = (new Database("phichaia_student"))->getConnection();
$sdq = new SDQ($db);

// Get existing data for edit/view mode
$existingData = [];
if ($mode !== 'add') {
    if ($type === 'self') {
        $data = $sdq->getSDQSelfData($stuId, $pee, $term);
    } else {
        $data = $sdq->getSDQParData($stuId, $pee, $term);
    }
    if (!empty($data['answers'])) {
        $existingData = $data['answers'];
    }
}

$isReadonly = ($mode === 'view');

// Questions
$questions = [
    1 => ['text' => 'พยายามทำตัวดีกับคนอื่น ใส่ใจความรู้สึกคนอื่น', 'cat' => 'prosocial', 'color' => 'emerald'],
    2 => ['text' => 'ไม่อยู่นิ่ง นั่งนานๆ ไม่ได้', 'cat' => 'hyperactivity', 'color' => 'amber'],
    3 => ['text' => 'ปวดศีรษะ ปวดท้อง หรือไม่สบายบ่อยๆ', 'cat' => 'emotional', 'color' => 'red'],
    4 => ['text' => 'เต็มใจแบ่งปันสิ่งของให้คนอื่น', 'cat' => 'prosocial', 'color' => 'emerald'],
    5 => ['text' => 'โกรธแรง และมักอารมณ์เสีย', 'cat' => 'conduct', 'color' => 'orange'],
    6 => ['text' => 'ชอบอยู่กับตัวเอง ชอบเล่นคนเดียว', 'cat' => 'peer', 'color' => 'sky'],
    7 => ['text' => 'มักทำตามที่คนอื่นบอก', 'cat' => 'prosocial', 'color' => 'emerald'],
    8 => ['text' => 'ขี้กังวล', 'cat' => 'emotional', 'color' => 'red'],
    9 => ['text' => 'ใครๆ ก็พึ่งได้ถ้าเขาเสียใจ หรือไม่สบายใจ', 'cat' => 'prosocial', 'color' => 'emerald'],
    10 => ['text' => 'อยู่ไม่สุข วุ่นวาย', 'cat' => 'hyperactivity', 'color' => 'amber'],
    11 => ['text' => 'มีเพื่อนสนิท', 'cat' => 'peer', 'color' => 'sky'],
    12 => ['text' => 'มีเรื่องทะเลาะวิวาทบ่อย', 'cat' => 'conduct', 'color' => 'orange'],
    13 => ['text' => 'ไม่มีความสุข ท้อแท้ร้องไห้บ่อยๆ', 'cat' => 'emotional', 'color' => 'red'],
    14 => ['text' => 'เพื่อนๆ ส่วนมากชอบ', 'cat' => 'peer', 'color' => 'sky'],
    15 => ['text' => 'วอกแวกง่าย รู้สึกว่าไม่มีสมาธิ', 'cat' => 'hyperactivity', 'color' => 'amber'],
    16 => ['text' => 'กังวลเวลาอยู่ในสถานการณ์ที่ไม่คุ้นเคย', 'cat' => 'emotional', 'color' => 'red'],
    17 => ['text' => 'ใจดีกับเด็กที่เล็กกว่า', 'cat' => 'prosocial', 'color' => 'emerald'],
    18 => ['text' => 'มีคนว่าโกหก หรือขี้โกงบ่อยๆ', 'cat' => 'conduct', 'color' => 'orange'],
    19 => ['text' => 'เด็กๆ คนอื่นล้อเลียนหรือรังแก', 'cat' => 'peer', 'color' => 'sky'],
    20 => ['text' => 'มักจะอาสาช่วยเหลือคนอื่น', 'cat' => 'prosocial', 'color' => 'emerald'],
    21 => ['text' => 'คิดก่อนทำ', 'cat' => 'hyperactivity', 'color' => 'amber'],
    22 => ['text' => 'เอาของคนอื่นในบ้าน โรงเรียน หรือที่อื่น', 'cat' => 'conduct', 'color' => 'orange'],
    23 => ['text' => 'เข้ากับผู้ใหญ่ได้ดีกว่าเด็กวัยเดียวกัน', 'cat' => 'peer', 'color' => 'sky'],
    24 => ['text' => 'ขี้กลัว รู้สึกหวาดกลัวได้ง่าย', 'cat' => 'emotional', 'color' => 'red'],
    25 => ['text' => 'ทำงานได้จนเสร็จ ความตั้งใจในการทำงานดี', 'cat' => 'prosocial', 'color' => 'emerald'],
];

$choices = [0 => 'ไม่จริง', 1 => 'จริงบางส่วน', 2 => 'จริงแน่นอน'];
$choiceColors = [0 => 'slate', 1 => 'amber', 2 => 'emerald'];

$typeLabels = ['self' => 'นักเรียนประเมินตนเอง', 'parent' => 'ผู้ปกครองประเมิน'];
$modeColors = [
    'add' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/20', 'border' => 'border-emerald-400 dark:border-emerald-800/50', 'text' => 'text-emerald-600 dark:text-emerald-400'],
    'edit' => ['bg' => 'bg-amber-50 dark:bg-amber-950/20', 'border' => 'border-amber-400 dark:border-amber-800/50', 'text' => 'text-amber-600 dark:text-amber-400'],
    'view' => ['bg' => 'bg-blue-50 dark:bg-blue-950/20', 'border' => 'border-blue-400 dark:border-blue-800/50', 'text' => 'text-blue-600 dark:text-blue-400'],
];
$mc = $modeColors[$mode];

// Border color mapping for Tailwind
$borderClasses = [
    'emerald' => 'border-emerald-500',
    'amber' => 'border-amber-500',
    'red' => 'border-red-500',
    'orange' => 'border-orange-500',
    'sky' => 'border-sky-500',
];

// Badge gradient mapping for Tailwind
$badgeClasses = [
    'emerald' => 'bg-gradient-to-br from-emerald-500 to-emerald-600',
    'amber' => 'bg-gradient-to-br from-amber-500 to-amber-600',
    'red' => 'bg-gradient-to-br from-red-500 to-red-600',
    'orange' => 'bg-gradient-to-br from-orange-500 to-orange-600',
    'sky' => 'bg-gradient-to-br from-sky-500 to-sky-600',
];
?>

<!-- Info Banner -->
<div class="<?= $mc['bg'] ?> border-l-4 <?= $mc['border'] ?> rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 <?= $mc['bg'] ?> rounded-lg flex items-center justify-center">
            <i class="fas <?= $type === 'self' ? 'fa-user-check' : 'fa-user-friends' ?> <?= $mc['text'] ?>"></i>
        </div>
        <div>
            <h5 class="font-bold <?= $mc['text'] ?>"><?= $typeLabels[$type] ?></h5>
            <p class="text-sm text-slate-500 dark:text-slate-400">กรุณาเลือกคำตอบที่ตรงกับ<?= $type === 'self' ? 'ตัวเอง' : 'นักเรียน' ?>ในช่วง 6 เดือนที่ผ่านมา</p>
        </div>
    </div>
</div>

<form id="sdqForm">
    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
    <input type="hidden" name="stuId" value="<?= htmlspecialchars($stuId) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
    
    <div class="space-y-3">
        <?php foreach ($questions as $num => $q): 
            $currentValue = $existingData["q$num"] ?? null;
            $color = $q['color'];
            $borderClass = $borderClasses[$color] ?? 'border-slate-500';
            $badgeClass = $badgeClasses[$color] ?? 'bg-indigo-500';
        ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-l-4 <?= $borderClass ?>">
            <div class="flex items-start gap-3 mb-3">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0 <?= $badgeClass ?>">
                    <?= $num ?>
                </span>
                <p class="font-medium text-slate-700 dark:text-slate-300 text-sm md:text-base pt-1">
                    <?= htmlspecialchars($q['text']) ?>
                </p>
            </div>
            
            <!-- Choices - Stack on mobile -->
            <div class="flex flex-col md:flex-row gap-2 md:gap-3 ml-0 md:ml-11">
                <?php foreach ($choices as $value => $label): 
                    $radioId = "q{$num}_{$value}";
                    $isChecked = ($currentValue !== null && (int)$currentValue === $value);
                    $cColor = $choiceColors[$value];
                ?>
                <label for="<?= $radioId ?>" class="flex items-center gap-2 p-2.5 rounded-lg cursor-pointer transition-all border-2
                    <?php if ($isReadonly): ?>
                        <?= $isChecked ? "bg-{$cColor}-100 dark:bg-{$cColor}-950/30 border-{$cColor}-400 dark:border-{$cColor}-500/50" : 'bg-slate-50 dark:bg-slate-700/30 border-transparent dark:border-transparent' ?>
                    <?php else: ?>
                        hover:bg-slate-50 dark:hover:bg-slate-700/50 border-transparent dark:border-transparent has-[:checked]:bg-purple-100 has-[:checked]:border-purple-400 dark:has-[:checked]:bg-purple-900/30 dark:has-[:checked]:border-purple-500/50
                    <?php endif; ?>">
                    <input type="radio" 
                           id="<?= $radioId ?>" 
                           name="q<?= $num ?>" 
                           value="<?= $value ?>"
                           <?= $isChecked ? 'checked' : '' ?>
                           <?= $isReadonly ? 'disabled' : 'required' ?>
                           class="w-4 h-4 text-purple-500 focus:ring-purple-400">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">
                        <?php if ($value === 0): ?>❌<?php elseif ($value === 1): ?>😐<?php else: ?>✅<?php endif; ?>
                        <?= $label ?>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Memo -->
    <div class="mt-4 bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-100 dark:border-slate-700">
        <h5 class="font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
            <i class="fas fa-comment-dots text-purple-500"></i>
            <?= $type === 'self' ? 'เธอ' : 'ท่าน' ?>มีอย่างอื่นที่จะบอกเพิ่มเติมหรือไม่?
        </h5>
        <textarea 
            name="memo" 
            rows="3" 
            <?= $isReadonly ? 'readonly' : '' ?>
            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm text-slate-700 dark:text-white focus:border-purple-400 focus:ring-4 focus:ring-purple-400/20 transition-all <?= $isReadonly ? 'cursor-not-allowed' : '' ?>"
            placeholder="<?= $isReadonly ? '' : 'เขียนข้อความเพิ่มเติมที่นี่...' ?>"><?= htmlspecialchars($existingData['memo'] ?? '') ?></textarea>
    </div>
</form>
