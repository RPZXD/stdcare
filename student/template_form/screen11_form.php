<?php
/**
 * Unified Screen11 Form
 * Modes: add, edit, view
 * Modern UI with Tailwind CSS - Stepper form
 */
session_start();
if (!isset($_SESSION['Student_login'])) {
    echo '<div class="text-red-500 text-center py-4">ไม่ได้รับอนุญาต</div>';
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/Screeningdata.php');

$student_id = $_GET['stuId'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';
$mode = $_GET['mode'] ?? 'add';

$db = new Database("phichaia_student");
$conn = $db->getConnection();
$screening = new ScreeningData($conn);

// Get existing data for edit/view mode
$existingData = [];
if ($mode !== 'add') {
    $existingData = $screening->getScreeningDataByStudentId($student_id, $pee);
    if (!$existingData && $mode === 'view') {
        echo '<div class="text-center py-8"><div class="text-amber-500 font-bold">ยังไม่มีข้อมูลการคัดกรอง</div></div>';
        exit;
    }
}

$isReadonly = ($mode === 'view');

// Subjects for special ability
$subjects = [
    'คณิตศาสตร์', 'ภาษาไทย', 'ภาษาต่างประเทศ', 'วิทยาศาสตร์',
    'ศิลปะ', 'การงานอาชีพและเทคโนโลยี', 'สุขศึกษา และพลศึกษา', 'สังคมศึกษา ศาสนา และวัฒนธรรม'
];

$modeColors = [
    'add' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/20', 'border' => 'border-emerald-400 dark:border-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400'],
    'edit' => ['bg' => 'bg-amber-50 dark:bg-amber-950/20', 'border' => 'border-amber-400 dark:border-amber-500', 'text' => 'text-amber-600 dark:text-amber-400'],
    'view' => ['bg' => 'bg-blue-50 dark:bg-blue-950/20', 'border' => 'border-blue-400 dark:border-blue-500', 'text' => 'text-blue-600 dark:text-blue-400'],
];
$mc = $modeColors[$mode];

// Helper
function checked($data, $field, $value) {
    return (isset($data[$field]) && $data[$field] === $value) ? 'checked' : '';
}
function checkboxChecked($data, $field, $value) {
    if (!isset($data[$field])) return '';
    $arr = is_array($data[$field]) ? $data[$field] : [];
    return in_array($value, $arr) ? 'checked' : '';
}
?>

<!-- Info Banner -->
<div class="<?= $mc['bg'] ?> border-l-4 <?= $mc['border'] ?> rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 <?= $mc['bg'] ?> rounded-lg flex items-center justify-center">
            <i class="fas fa-clipboard-list <?= $mc['text'] ?>"></i>
        </div>
        <div>
            <h5 class="font-bold <?= $mc['text'] ?>">
                <?php if ($mode === 'view'): ?>ดูข้อมูลแบบคัดกรอง 11 ด้าน<?php elseif ($mode === 'edit'): ?>แก้ไขแบบคัดกรอง 11 ด้าน<?php else: ?>ทำแบบคัดกรอง 11 ด้าน<?php endif; ?>
            </h5>
            <p class="text-sm text-slate-500 dark:text-slate-400">กรุณาเลือกคำตอบที่ตรงกับตัวคุณ</p>
        </div>
    </div>
</div>

<!-- Progress -->
<?php if (!$isReadonly): ?>
<div class="mb-4 bg-white dark:bg-slate-800 rounded-xl p-3 shadow-sm border border-slate-100 dark:border-slate-700">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-bold text-slate-600 dark:text-slate-400">ความคืบหน้า</span>
        <span class="text-sm font-bold text-indigo-500" id="stepText">ข้อ 1/11</span>
    </div>
    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
        <div id="progressBar" class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all" style="width: 9.09%"></div>
    </div>
</div>
<?php endif; ?>

<form id="screen11Form">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
    <input type="hidden" name="special_ability_detail" id="special_ability_detail">
    
    <!-- Step Container -->
    <div id="stepsContainer" class="<?= $isReadonly ? 'space-y-8' : '' ?>">
        
        <!-- Step 1: ความสามารถพิเศษ -->
        <?php $isHaveSpecial = ($existingData['special_ability'] ?? '') === 'มี'; ?>
        <div class="step <?= (!$isReadonly && 0 !== 0) ? 'hidden' : '' ?>" data-step="1">
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800 mb-4">
                <h4 class="font-bold text-amber-700 dark:text-amber-400 flex items-center gap-2">
                    <span class="text-xl">🌟</span> 1. ด้านความสามารถพิเศษ
                </h4>
            </div>
            <div class="flex flex-wrap gap-3 mb-4">
                <label class="flex-1 flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-transparent dark:border-slate-700/50 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 has-[:checked]:bg-emerald-100 dark:has-[:checked]:bg-emerald-950/30 has-[:checked]:border-emerald-400 dark:has-[:checked]:border-emerald-500">
                    <input type="radio" name="special_ability" value="ไม่มี" <?= checked($existingData, 'special_ability', 'ไม่มี') ?> <?= $isReadonly ? 'disabled' : 'required' ?> class="w-4 h-4 text-emerald-500 focus:ring-emerald-400">
                    <span class="font-medium">ไม่มี</span>
                </label>
                <label class="flex-1 flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-transparent dark:border-slate-700/50 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 has-[:checked]:bg-emerald-100 dark:has-[:checked]:bg-emerald-950/30 has-[:checked]:border-emerald-400 dark:has-[:checked]:border-emerald-500">
                    <input type="radio" name="special_ability" value="มี" <?= checked($existingData, 'special_ability', 'มี') ?> <?= $isReadonly ? 'disabled' : '' ?> class="w-4 h-4 text-emerald-500 focus:ring-emerald-400">
                    <span class="font-medium">มี</span>
                </label>
            </div>
            <div id="specialAbilityFields" class="<?= $isHaveSpecial ? '' : 'hidden' ?> space-y-3">
                <?php 
                $abilityDetail = $existingData['special_ability_detail'] ?? [];
                foreach ($subjects as $i => $subject): 
                    $fieldName = "special_$i";
                    $hasDetail = !empty($abilityDetail[$fieldName]);
                    $details = $hasDetail ? $abilityDetail[$fieldName] : ['', ''];
                ?>
                <div class="bg-white dark:bg-slate-800 rounded-lg p-3 border border-slate-200 dark:border-slate-700">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="subject-checkbox w-5 h-5 text-indigo-500" data-subject="<?= $i ?>" <?= $hasDetail ? 'checked' : '' ?> <?= $isReadonly ? 'disabled' : '' ?>>
                        <span class="font-medium text-slate-700 dark:text-slate-300"><?= $subject ?></span>
                    </label>
                    <div class="subject-inputs <?= $hasDetail ? '' : 'hidden' ?> mt-2 space-y-2 pl-7" data-subject="<?= $i ?>">
                        <input type="text" name="special_<?= $i ?>[]" value="<?= htmlspecialchars($details[0] ?? '') ?>" class="w-full px-3 py-2 text-sm rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none" placeholder="รายละเอียด 1" <?= $isReadonly ? 'readonly' : '' ?>>
                        <input type="text" name="special_<?= $i ?>[]" value="<?= htmlspecialchars($details[1] ?? '') ?>" class="w-full px-3 py-2 text-sm rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none" placeholder="รายละเอียด 2" <?= $isReadonly ? 'readonly' : '' ?>>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
        // Generic steps 2-9, 11 (status with risk/problem)
        $genericSteps = [
            2 => ['title' => 'ด้านการเรียน', 'icon' => '📚', 'color' => 'blue', 'field' => 'study'],
            3 => ['title' => 'ด้านสุขภาพ', 'icon' => '🏥', 'color' => 'emerald', 'field' => 'health'],
            4 => ['title' => 'ด้านเศรษฐกิจ', 'icon' => '💰', 'color' => 'yellow', 'field' => 'economic'],
            5 => ['title' => 'ด้านสวัสดิภาพและความปลอดภัย', 'icon' => '🛡️', 'color' => 'indigo', 'field' => 'welfare'],
            6 => ['title' => 'ด้านพฤติกรรมการใช้สารเสพติด', 'icon' => '🚭', 'color' => 'red', 'field' => 'drug'],
            7 => ['title' => 'ด้านพฤติกรรมการใช้ความรุนแรง', 'icon' => '⚠️', 'color' => 'orange', 'field' => 'violence'],
            8 => ['title' => 'ด้านพฤติกรรมทางเพศ', 'icon' => '💕', 'color' => 'pink', 'field' => 'sex'],
            9 => ['title' => 'ด้านการติดเกม', 'icon' => '🎮', 'color' => 'purple', 'field' => 'game'],
            11 => ['title' => 'ด้านการใช้สื่ออิเล็กทรอนิกส์', 'icon' => '📱', 'color' => 'cyan', 'field' => 'it'],
        ];

        // Risk/Problem options for each step
        $riskOptions = [
            'study' => [
                'risk' => ['ผลการเรียนเฉลี่ย 1.00-2.00', 'ติด 0, ร, มส, มผ 1-2 วิชา/1 ภาคเรียน', 'ไม่เข้าเรียน 1-2 ครั้ง/รายวิชา', 'มาเรียนสาย 3 ครั้งต่อสัปดาห์'],
                'problem' => ['ผลการเรียนต่ำกว่า 1.00', 'ติด 0, ร, มส, มผ มากกว่า 2 วิชา', 'ขาดเรียนบ่อยมากกว่า 1 วัน/สัปดาห์', 'สมาธิสั้น']
            ],
            'health' => [
                'risk' => ['ร่างกายไม่แข็งแรง', 'มีโรคประจำตัวหรือเจ็บป่วยบ่อย', 'มีปัญหาด้านสายตา (สวมแว่น)'],
                'problem' => ['มีภาวะทุพโภชนาการ', 'มีความพิการทางร่างกาย', 'ป่วยเป็นโรคร้ายแรง/เรื้อรัง']
            ],
            'economic' => [
                'risk' => ['รายได้ครอบครัว 5,000-10,000 บาท/เดือน', 'บิดาหรือมารดาตกงาน (1 คน)'],
                'problem' => ['รายได้ครอบครัวต่ำกว่า 5,000 บาท/เดือน', 'บิดาและมารดาตกงาน', 'ครอบครัวมีภาระหนี้สินมาก']
            ],
            'welfare' => [
                'risk' => ['พ่อแม่แยกทางกัน', 'ที่พักอาศัยในชุมชนแออัด', 'อยู่หอพัก', 'มีบุคคลเจ็บป่วยโรคร้ายแรง'],
                'problem' => ['ไม่มีผู้ดูแล', 'ใช้ความรุนแรงในครอบครัว', 'ถูกทารุณ/ทำร้าย', 'ถูกล่วงละเมิดทางเพศ']
            ],
            'drug' => [
                'risk' => ['คบเพื่อนในกลุ่มใช้สารเสพติด', 'สมาชิกในครอบครัวข้องเกี่ยวกับยาเสพติด', 'เคยลองสูบบุหรี่/กัญชา'],
                'problem' => ['ใช้หรือเสพเองมากกว่า 2 ครั้ง', 'มีประวัติเกี่ยวข้องกับสารเสพติด', 'เป็นผู้ติดสารเสพติด']
            ],
            'violence' => [
                'risk' => ['ไม่ปฏิบัติตามกฎจราจร', 'พาหนะและสภาพการเดินทางไม่ปลอดภัย', 'มีประวัติทะเลาะวิวาท'],
                'problem' => ['ทะเลาะวิวาทบ่อยๆ', 'ทำร้ายร่างกายผู้อื่น']
            ],
            'sex' => [
                'risk' => ['จับคู่ชัดเจนและแยกกลุ่มอยู่ด้วยกัน', 'ทำงานพิเศษที่ล่อแหลม', 'ใช้สื่อสารเป็นเวลานาน'],
                'problem' => ['ขาดเรียนไปกับคู่ของตนเสมอ', 'อยู่ด้วยกัน', 'ตั้งครรภ์']
            ],
            'game' => [
                'risk' => ['เล่นเกมเกินวันละ 1 ชั่วโมง', 'เก็บตัว แยกตัวจากกลุ่มเพื่อน', 'ใช้จ่ายเงินผิดปกติ'],
                'problem' => ['ใช้เวลาเล่นเกมเกิน 2 ชั่วโมง', 'หงุดหงิด ฉุนเฉียว', 'หมกมุ่นในการเล่นเกม']
            ],
            'it' => [
                'risk' => ['ใช้โทรศัพท์ในระหว่างเรียนโดยไม่จำเป็น', 'ใช้โซเชียลเกินวันละ 1 ชั่วโมง'],
                'problem' => ['ใช้โทรศัพท์ในเรียน 2-3 ครั้ง/วัน', 'ใช้โซเชียลเกินวันละ 2 ชั่วโมง']
            ]
        ];

        foreach ($genericSteps as $stepNum => $step):
            $field = $step['field'];
            $fieldStatus = $field . '_status';
            $fieldRisk = $field . '_risk';
            $fieldProblem = $field . '_problem';
            $statusVal = $existingData[$fieldStatus] ?? '';
        ?>
        <!-- Step <?= $stepNum ?> -->
        <div class="step <?= (!$isReadonly) ? 'hidden' : '' ?>" data-step="<?= $stepNum ?>">
            <div class="bg-<?= $step['color'] ?>-50 dark:bg-<?= $step['color'] ?>-900/20 rounded-xl p-4 border border-<?= $step['color'] ?>-200 dark:border-<?= $step['color'] ?>-800 mb-4">
                <h4 class="font-bold text-<?= $step['color'] ?>-700 dark:text-<?= $step['color'] ?>-400 flex items-center gap-2">
                    <span class="text-xl"><?= $step['icon'] ?></span> <?= $stepNum ?>. <?= $step['title'] ?>
                </h4>
            </div>
            <div class="flex flex-wrap gap-2 mb-4">
                <label class="flex-1 flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all border-transparent dark:border-slate-700/50 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 has-[:checked]:bg-emerald-100 dark:has-[:checked]:bg-emerald-950/30 has-[:checked]:border-emerald-400 dark:has-[:checked]:border-emerald-500">
                    <input type="radio" name="<?= $fieldStatus ?>" value="ปกติ" <?= checked($existingData, $fieldStatus, 'ปกติ') ?> <?= $isReadonly ? 'disabled' : 'required' ?> class="w-4 h-4 text-emerald-500 focus:ring-emerald-400">
                    <span class="font-medium text-emerald-700 dark:text-emerald-400">ปกติ</span>
                </label>
                <label class="flex-1 flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all border-transparent dark:border-slate-700/50 hover:bg-amber-50 dark:hover:bg-amber-950/20 has-[:checked]:bg-amber-100 dark:has-[:checked]:bg-amber-950/30 has-[:checked]:border-amber-400 dark:has-[:checked]:border-amber-500">
                    <input type="radio" name="<?= $fieldStatus ?>" value="เสี่ยง" <?= checked($existingData, $fieldStatus, 'เสี่ยง') ?> <?= $isReadonly ? 'disabled' : '' ?> class="w-4 h-4 text-amber-500 focus:ring-amber-400">
                    <span class="font-medium text-amber-700 dark:text-amber-400">เสี่ยง</span>
                </label>
                <label class="flex-1 flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all border-transparent dark:border-slate-700/50 hover:bg-red-50 dark:hover:bg-red-950/20 has-[:checked]:bg-red-100 dark:has-[:checked]:bg-red-950/30 has-[:checked]:border-red-400 dark:has-[:checked]:border-red-500">
                    <input type="radio" name="<?= $fieldStatus ?>" value="มีปัญหา" <?= checked($existingData, $fieldStatus, 'มีปัญหา') ?> <?= $isReadonly ? 'disabled' : '' ?> class="w-4 h-4 text-red-500 focus:ring-red-400">
                    <span class="font-medium text-red-700 dark:text-red-400">มีปัญหา</span>
                </label>
            </div>
            
            <!-- Risk fields -->
            <div id="<?= $field ?>RiskFields" class="<?= $statusVal === 'เสี่ยง' ? '' : 'hidden' ?> space-y-2 mb-4 p-3 bg-amber-50 dark:bg-amber-950/20 rounded-xl border border-amber-200 dark:border-amber-800/50">
                <p class="text-sm font-bold text-amber-700 dark:text-amber-400 mb-2">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</p>
                <?php foreach ($riskOptions[$field]['risk'] ?? [] as $opt): ?>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="<?= $fieldRisk ?>[]" value="<?= $opt ?>" class="w-4 h-4 text-amber-500 rounded focus:ring-amber-400" <?= checkboxChecked($existingData, $fieldRisk, $opt) ?> <?= $isReadonly ? 'disabled' : '' ?>>
                    <span><?= $opt ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            
            <!-- Problem fields -->
            <div id="<?= $field ?>ProblemFields" class="<?= $statusVal === 'มีปัญหา' ? '' : 'hidden' ?> space-y-2 mb-4 p-3 bg-red-50 dark:bg-red-950/20 rounded-xl border border-red-200 dark:border-red-800/50">
                <p class="text-sm font-bold text-red-700 dark:text-red-400 mb-2">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</p>
                <?php foreach ($riskOptions[$field]['problem'] ?? [] as $opt): ?>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="<?= $fieldProblem ?>[]" value="<?= $opt ?>" class="w-4 h-4 text-red-500 rounded focus:ring-red-400" <?= checkboxChecked($existingData, $fieldProblem, $opt) ?> <?= $isReadonly ? 'disabled' : '' ?>>
                    <span><?= $opt ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Step 10: ความต้องการพิเศษ -->
        <?php $isHaveSpecialNeed = ($existingData['special_need_status'] ?? '') === 'มี'; ?>
        <div class="step <?= (!$isReadonly) ? 'hidden' : '' ?>" data-step="10">
            <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-4 border border-teal-200 dark:border-teal-800 mb-4">
                <h4 class="font-bold text-teal-700 dark:text-teal-400 flex items-center gap-2">
                    <span class="text-xl">♿</span> 10. นักเรียนที่มีความต้องการพิเศษ
                </h4>
            </div>
            <div class="flex flex-wrap gap-3 mb-4">
                <label class="flex-1 flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-transparent dark:border-slate-700/50 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 has-[:checked]:bg-emerald-100 dark:has-[:checked]:bg-emerald-950/30 has-[:checked]:border-emerald-400 dark:has-[:checked]:border-emerald-500">
                    <input type="radio" name="special_need_status" value="ไม่มี" <?= checked($existingData, 'special_need_status', 'ไม่มี') ?> <?= $isReadonly ? 'disabled' : 'required' ?> class="w-4 h-4 text-emerald-500 focus:ring-emerald-400">
                    <span class="font-medium">ไม่มี</span>
                </label>
                <label class="flex-1 flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-transparent dark:border-slate-700/50 hover:bg-teal-50 dark:hover:bg-teal-950/20 has-[:checked]:bg-teal-100 dark:has-[:checked]:bg-teal-950/30 has-[:checked]:border-teal-400 dark:has-[:checked]:border-teal-500">
                    <input type="radio" name="special_need_status" value="มี" <?= checked($existingData, 'special_need_status', 'มี') ?> <?= $isReadonly ? 'disabled' : '' ?> class="w-4 h-4 text-teal-500 focus:ring-teal-400">
                    <span class="font-medium">มี</span>
                </label>
            </div>
            <div id="specialNeedFields" class="<?= $isHaveSpecialNeed ? '' : 'hidden' ?> space-y-2 p-3 bg-teal-50 dark:bg-teal-950/20 rounded-xl border border-teal-200 dark:border-teal-800/50">
                <p class="text-sm font-bold text-teal-700 dark:text-teal-400 mb-2">เลือกประเภท:</p>
                <?php 
                $specialNeedTypes = [
                    'มีความบกพร่องทางการเห็น', 'มีความบกพร่องทางการได้ยิน', 'มีความบกพร่องทางสติปัญญา',
                    'มีความบกพร่องทางร่างกายและสุขภาพ', 'มีความบกพร่องทางการเรียนรู้', 'มีความบกพร่องทางพฤติกรรมหรืออารมณ์',
                    'มีความบกพร่องทางการพูดและภาษา', 'ออทิสติก', 'มีสมาธิสั้น', 'พิการซ้ำซ้อน'
                ];
                foreach ($specialNeedTypes as $type): ?>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="special_need_type" value="<?= $type ?>" <?= checked($existingData, 'special_need_type', $type) ?> <?= $isReadonly ? 'disabled' : '' ?>>
                    <span><?= $type ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Navigation -->
    <?php if (!$isReadonly): ?>
    <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
        <button type="button" id="prevStep" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold transition-all disabled:opacity-50" disabled>
            <i class="fas fa-arrow-left mr-2"></i>ย้อนกลับ
        </button>
        <button type="button" id="nextStep" class="px-6 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl font-bold transition-all">
            ถัดไป<i class="fas fa-arrow-right ml-2"></i>
        </button>
    </div>
    <?php endif; ?>
</form>

<script>
const steps = document.querySelectorAll('.step');
let currentStep = 0;
const totalSteps = steps.length;
const prevBtn = document.getElementById('prevStep');
const nextBtn = document.getElementById('nextStep');

function showStep(idx) {
    // If view mode, don't hide anything
    if (<?= json_encode($isReadonly) ?>) {
        document.querySelectorAll('.step').forEach(s => s.classList.remove('hidden'));
        return;
    }
    
    steps.forEach((step, i) => step.classList.toggle('hidden', i !== idx));
    if (document.getElementById('stepText')) {
        document.getElementById('stepText').textContent = `ข้อ ${idx + 1}/11`;
    }
    if (document.getElementById('progressBar')) {
        document.getElementById('progressBar').style.width = ((idx + 1) / 11 * 100) + '%';
    }
    if (prevBtn) prevBtn.disabled = idx === 0;
    if (nextBtn) nextBtn.classList.toggle('hidden', idx === steps.length - 1);
}

if (prevBtn) {
    prevBtn.onclick = () => {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    };
}

if (nextBtn) {
    nextBtn.onclick = () => {
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    };
}

showStep(currentStep);

// Toggle special ability fields
$(document).on('change', 'input[name="special_ability"]', function() {
    const isHave = $(this).val() === 'มี';
    $('#specialAbilityFields').toggleClass('hidden', !isHave);
});

// Toggle subject inputs
$(document).on('change', '.subject-checkbox', function() {
    const subject = $(this).data('subject');
    $('.subject-inputs[data-subject="' + subject + '"]').toggleClass('hidden', !$(this).is(':checked'));
});

// Toggle risk/problem fields for each step
<?php foreach ($genericSteps as $step): $f = $step['field']; ?>
$(document).on('change', 'input[name="<?= $f ?>_status"]', function() {
    const val = $(this).val();
    $('#<?= $f ?>RiskFields').toggleClass('hidden', val !== 'เสี่ยง');
    $('#<?= $f ?>ProblemFields').toggleClass('hidden', val !== 'มีปัญหา');
});
<?php endforeach; ?>

// Toggle special need fields
$(document).on('change', 'input[name="special_need_status"]', function() {
    const isHave = $(this).val() === 'มี';
    $('#specialNeedFields').toggleClass('hidden', !isHave);
});

// Trigger change events once to set initial visibility for edit/view modes
setTimeout(() => {
    $('input[type="radio"]:checked, input[type="checkbox"]:checked').trigger('change');
}, 100);
</script>
