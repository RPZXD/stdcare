<?php
/**
 * Unified SDQ Form Template
 * Supports: self (นักเรียน), teach (ครู), par (ผู้ปกครอง)
 * Modes: add (บันทึกใหม่), edit (แก้ไข)
 */
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../class/SDQ.php';

// Get parameters
$type = $_GET['type'] ?? 'self'; // self | teach | par
$mode = $_GET['mode'] ?? 'add';  // add | edit
$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['student_name'] ?? '';
$student_no = $_GET['student_no'] ?? '';
$student_class = $_GET['student_class'] ?? '';
$student_room = $_GET['student_room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

// Type configurations
$typeConfig = [
    'self' => [
        'title' => 'ฉบับนักเรียนประเมินตนเอง',
        'icon' => 'fa-user',
        'color' => 'from-blue-500 to-indigo-600',
        'instruction' => 'กรุณาเลือกคำตอบที่ตรงกับตัวเธอในช่วง 6 เดือนที่ผ่านมา',
        'getMethod' => 'getSDQSelfData'
    ],
    'teach' => [
        'title' => 'ฉบับครูเป็นผู้ประเมิน',
        'icon' => 'fa-chalkboard-teacher',
        'color' => 'from-amber-500 to-orange-600',
        'instruction' => 'กรุณาเลือกคำตอบที่ตรงกับพฤติกรรมของนักเรียนในช่วง 6 เดือนที่ผ่านมา',
        'getMethod' => 'getSDQTeachData'
    ],
    'par' => [
        'title' => 'ฉบับผู้ปกครองเป็นผู้ประเมิน',
        'icon' => 'fa-user-friends',
        'color' => 'from-purple-500 to-pink-600',
        'instruction' => 'กรุณาเลือกคำตอบที่ตรงกับพฤติกรรมของบุตรหลานในช่วง 6 เดือนที่ผ่านมา',
        'getMethod' => 'getSDQParData'
    ]
];

$config = $typeConfig[$type] ?? $typeConfig['self'];

// If edit mode, get existing data
$answers = [];
$memo = '';
if ($mode === 'edit') {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $sdq = new SDQ($db);
    
    $method = $config['getMethod'];
    $existingData = $sdq->$method($student_id, $pee, $term);
    $answers = $existingData['answers'] ?? [];
    $memo = $existingData['memo'] ?? '';
}

// SDQ Questions (25 items)
$questions = [
    ['q1', 'พยายามจะทำตัวดีกับคนอื่น ใส่ใจความรู้สึกคนอื่น', 'จุดแข็ง', 'emerald'],
    ['q2', 'ไม่อยู่นิ่ง นั่งนานๆ ไม่ได้', 'สมาธิ', 'amber'],
    ['q3', 'ปวดศรีษะ ปวดท้อง หรือไม่สบายบ่อยๆ', 'อารมณ์', 'blue'],
    ['q4', 'เต็มใจแบ่งปันสิ่งของให้คนอื่น (ของกิน เกม ปากกา เป็นต้น)', 'จุดแข็ง', 'emerald'],
    ['q5', 'โกรธแรง และมักอารมณ์เสีย', 'เกเร', 'rose'],
    ['q6', 'ชอบอยู่กับตัวเอง ชอบเล่นคนเดียวอยู่ตามลำพัง', 'เพื่อน', 'purple'],
    ['q7', 'มักทำตามที่คนอื่นบอก', 'จุดแข็ง', 'emerald'],
    ['q8', 'ขี้กังวล', 'อารมณ์', 'blue'],
    ['q9', 'ใครๆ ก็พึ่งได้ถ้าเขาเสียใจ อารมณ์ไม่ดีหรือไม่สบายใจ', 'จุดแข็ง', 'emerald'],
    ['q10', 'อยู่ไม่สุข วุ่นวาย', 'สมาธิ', 'amber'],
    ['q11', 'มีเพื่อนสนิท', 'เพื่อน', 'purple'],
    ['q12', 'มีเรื่องทะเลาะวิวาทบ่อย ทำให้คนอื่นอย่างที่ต้องการได้', 'เกเร', 'rose'],
    ['q13', 'ไม่มีความสุข ท้อแท้ร้องไห้บ่อยๆ', 'อารมณ์', 'blue'],
    ['q14', 'เพื่อนๆ ส่วนมากชอบ', 'เพื่อน', 'purple'],
    ['q15', 'วอกแวกง่าย รู้สึกว่าไม่มีสมาธิ', 'สมาธิ', 'amber'],
    ['q16', 'กังวลเวลาอยู่ในสถานการณ์ที่ไม่คุ้นเคยและเสียความเชื่อมั่นในตนเองง่าย', 'อารมณ์', 'blue'],
    ['q17', 'ใจดีกับเด็กที่เล็กกว่า', 'จุดแข็ง', 'emerald'],
    ['q18', 'มีคนว่าโกหก หรือขี้โกงบ่อยๆ', 'เกเร', 'rose'],
    ['q19', 'เด็กๆ คนอื่นล้อเลียนหรือรังแก', 'เพื่อน', 'purple'],
    ['q20', 'มักจะอาสาช่วยเหลือคนอื่น (พ่อ แม่ ครู เด็กคนอื่น)', 'จุดแข็ง', 'emerald'],
    ['q21', 'คิดก่อนทำ', 'สมาธิ', 'amber'],
    ['q22', 'เอาของคนอื่นในบ้าน ที่โรงเรียนหรือที่อื่น', 'เกเร', 'rose'],
    ['q23', 'เข้ากับผู้ใหญ่ได้ดีกว่าเด็กวัยเดียวกัน', 'เพื่อน', 'purple'],
    ['q24', 'ขี้กลัว รู้สึกหวาดกลัวได้ง่าย', 'อารมณ์', 'blue'],
    ['q25', 'ทำงานได้จนเสร็จ ความตั้งใจในการทำงานดี', 'จุดแข็ง', 'emerald'],
];

// Form ID based on mode
$formId = $mode === 'edit' ? 'sdqEditForm' : 'sdqForm';
?>

<form id="<?= $formId ?>" class="space-y-6">
    <!-- Hidden Fields -->
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

    <!-- Student Info Card -->
    <div class="bg-gradient-to-r <?= $config['color'] ?> rounded-2xl p-5 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                <i class="fas <?= $config['icon'] ?> text-2xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold">แบบประเมิน SDQ <?= $config['title'] ?></h2>
                <p class="text-white/80 text-sm">
                    <?= htmlspecialchars($student_name) ?> | เลขที่ <?= htmlspecialchars($student_no) ?> | ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?>
                </p>
                <p class="text-white/60 text-xs mt-1">ภาคเรียนที่ <?= htmlspecialchars($term) ?> ปีการศึกษา <?= htmlspecialchars($pee) ?></p>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <p class="text-blue-700 dark:text-blue-300 text-sm">
                <strong>คำชี้แจง:</strong> <?= $config['instruction'] ?>
            </p>
        </div>
    </div>

    <!-- Questions List -->
    <div class="space-y-3">
        <?php foreach ($questions as $index => [$id, $text, $category, $color]): ?>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 hover:shadow-md transition-shadow">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <!-- Question Number & Text -->
                    <div class="flex-1">
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 bg-<?= $color ?>-100 dark:bg-<?= $color ?>-900/30 text-<?= $color ?>-600 rounded-lg flex items-center justify-center font-bold text-sm">
                                <?= $index + 1 ?>
                            </span>
                            <div>
                                <p class="text-slate-700 dark:text-slate-300 font-medium"><?= htmlspecialchars($text) ?></p>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-<?= $color ?>-100 dark:bg-<?= $color ?>-900/30 text-<?= $color ?>-600 text-[10px] font-bold rounded-full uppercase tracking-wider">
                                    <?= $category ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Answer Options -->
                    <div class="flex flex-wrap gap-2 lg:flex-shrink-0">
                        <label class="cursor-pointer">
                            <input type="radio" name="<?= $id ?>" value="0" <?= isset($answers[$id]) && $answers[$id] == '0' ? 'checked' : '' ?> required class="peer hidden">
                            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:text-red-600 transition-all">
                                <span class="text-lg">❌</span>
                                <span class="hidden sm:inline">ไม่จริง</span>
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="<?= $id ?>" value="1" <?= isset($answers[$id]) && $answers[$id] == '1' ? 'checked' : '' ?> class="peer hidden">
                            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 peer-checked:text-amber-600 transition-all">
                                <span class="text-lg">😐</span>
                                <span class="hidden sm:inline">บางส่วน</span>
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="<?= $id ?>" value="2" <?= isset($answers[$id]) && $answers[$id] == '2' ? 'checked' : '' ?> class="peer hidden">
                            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 peer-checked:text-emerald-600 transition-all">
                                <span class="text-lg">✅</span>
                                <span class="hidden sm:inline">จริงแน่นอน</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Additional Comments -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
        <label class="block mb-3">
            <span class="text-slate-700 dark:text-slate-300 font-bold flex items-center gap-2">
                <i class="fas fa-comment-dots text-slate-400"></i>
                ความคิดเห็นเพิ่มเติม
            </span>
            <span class="text-slate-400 text-xs">(ถ้ามี)</span>
        </label>
        <textarea name="memo" rows="3" 
            class="w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
            placeholder="กรุณาเขียนข้อความเพิ่มเติมที่นี่..."><?= htmlspecialchars($memo) ?></textarea>
    </div>
</form>
