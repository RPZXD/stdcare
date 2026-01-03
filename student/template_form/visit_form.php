<?php
/**
 * Unified Visit Home Form
 * Modes: view, edit, add
 * Modern UI with Tailwind CSS
 */
include_once("../../config/Database.php");
include_once("../../class/StudentVisit.php");
include_once("../../class/UserLogin.php");

$stuId = $_GET['stuId'] ?? '';
$term = $_GET['term'] ?? '';
$pee = $_GET['pee'] ?? '';
$mode = $_GET['mode'] ?? 'view'; // view, edit, add

$db = (new Database("phichaia_student"))->getConnection();
$visit = new StudentVisit($db);

// Get student data
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $stuId);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo '<div class="text-center py-8"><div class="text-red-500 font-bold">ไม่พบข้อมูลนักเรียน</div></div>';
    exit;
}

// Get visit data for edit/view mode
$visitData = null;
if ($mode !== 'add') {
    $visitData = $visit->getVisitData($stuId, $term, $pee);
    if (!$visitData && $mode === 'view') {
        echo '<div class="text-center py-8"><div class="text-amber-500 font-bold">ไม่พบข้อมูลการเยี่ยมบ้าน</div></div>';
        exit;
    }
}

// Questions array
$questions = [
    1 => ["q" => "1. บ้านที่อยู่อาศัย", "a" => ["บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น"]],
    2 => ["q" => "2. ระยะทางระหว่างบ้านกับโรงเรียน", "a" => ["1-5 กม.", "6-10 กม.", "11-15 กม.", "16-20 กม.", "20 กม.ขึ้นไป"]],
    3 => ["q" => "3. การเดินทางไปโรงเรียน", "a" => ["เดิน", "รถจักรยาน", "รถจักรยานยนต์", "รถยนต์ส่วนตัว", "รถรับส่ง/โดยสาร", "อื่นๆ"]],
    4 => ["q" => "4. สภาพแวดล้อมของบ้าน", "a" => ["ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง"]],
    5 => ["q" => "5. อาชีพของผู้ปกครอง", "a" => ["เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ"]],
    6 => ["q" => "6. สถานที่ทำงานของบิดามารดา", "a" => ["ในอำเภอเดียวกัน", "ในจังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ"]],
    7 => ["q" => "7. สถานภาพของบิดามารดา", "a" => ["อยู่ด้วยกัน", "หย่าร้าง", "บิดาเสียชีวิต", "มารดาเสียชีวิต", "ทั้งสองเสียชีวิต"]],
    8 => ["q" => "8. วิธีการอบรมเลี้ยงดู", "a" => ["เข้มงวดกวดขัน", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ"]],
    9 => ["q" => "9. โรคประจำตัวของนักเรียน", "a" => ["ไม่มี", "มี"]],
    10 => ["q" => "10. ความสัมพันธ์ของสมาชิกในครอบครัว", "a" => ["อบอุ่น", "เฉยๆ", "ห่างเหิน"]],
    11 => ["q" => "11. หน้าที่รับผิดชอบภายในบ้าน", "a" => ["มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี"]],
    12 => ["q" => "12. สนิทสนมกับใครมากที่สุด", "a" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"]],
    13 => ["q" => "13. รายได้กับการใช้จ่ายในครอบครัว", "a" => ["เพียงพอ", "ไม่เพียงพอบางครั้ง", "ขัดสน"]],
    14 => ["q" => "14. ลักษณะเพื่อนเล่นที่บ้าน", "a" => ["รุ่นเดียวกัน", "รุ่นน้อง", "รุ่นพี่", "ทุกรุ่น"]],
    15 => ["q" => "15. ความต้องการเมื่อจบการศึกษา", "a" => ["ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ"]],
    16 => ["q" => "16. เมื่อมีปัญหาจะปรึกษาใคร", "a" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"]],
    17 => ["q" => "17. ความรู้สึกต่อครูที่มาเยี่ยมบ้าน", "a" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"]],
    18 => ["q" => "18. ทัศนคติต่อโรงเรียน", "a" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"]],
];

$isReadonly = ($mode === 'view');
$modeColors = [
    'view' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-400', 'icon' => 'fa-eye', 'text' => 'text-blue-600'],
    'edit' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-400', 'icon' => 'fa-edit', 'text' => 'text-amber-600'],
    'add' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-400', 'icon' => 'fa-plus-circle', 'text' => 'text-emerald-600'],
];
$mc = $modeColors[$mode] ?? $modeColors['view'];
?>

<!-- Student Info Card -->
<div class="<?= $mc['bg'] ?> border-l-4 <?= $mc['border'] ?> rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3 mb-3">
        <div class="w-10 h-10 <?= $mc['bg'] ?> rounded-lg flex items-center justify-center">
            <i class="fas <?= $mc['icon'] ?> <?= $mc['text'] ?>"></i>
        </div>
        <h5 class="font-bold <?= $mc['text'] ?>">
            <?php if ($mode === 'view'): ?>ข้อมูลการเยี่ยมบ้าน<?php elseif ($mode === 'edit'): ?>แก้ไขข้อมูลการเยี่ยมบ้าน<?php else: ?>บันทึกข้อมูลการเยี่ยมบ้าน<?php endif; ?>
        </h5>
    </div>
    
    <!-- Mobile: Stack, Desktop: Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
        <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800 rounded-lg">
            <span class="w-6 text-center">🆔</span>
            <span class="text-slate-600 dark:text-slate-300"><?= htmlspecialchars($student['Stu_id']) ?></span>
        </div>
        <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800 rounded-lg">
            <span class="w-6 text-center">👤</span>
            <span class="text-slate-600 dark:text-slate-300"><?= htmlspecialchars($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']) ?></span>
        </div>
        <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800 rounded-lg">
            <span class="w-6 text-center">🏫</span>
            <span class="text-slate-600 dark:text-slate-300">ม.<?= htmlspecialchars($student['Stu_major']) ?>/<?= htmlspecialchars($student['Stu_room']) ?></span>
        </div>
        <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800 rounded-lg">
            <span class="w-6 text-center">📞</span>
            <span class="text-slate-600 dark:text-slate-300"><?= htmlspecialchars($student['Stu_phone'] ?: '-') ?></span>
        </div>
    </div>
</div>

<!-- Form -->
<form id="addVisitForm" method="post">
    <input type="hidden" name="stuId" value="<?= htmlspecialchars($stuId) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    
    <div class="space-y-4">
        <?php foreach ($questions as $num => $q): 
            $currentValue = $visitData["vh$num"] ?? null;
        ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-100 dark:border-slate-700">
            <h5 class="font-bold text-slate-800 dark:text-white mb-3 text-sm md:text-base">
                <?= htmlspecialchars($q['q']) ?>
            </h5>
            
            <!-- Mobile: Stack vertically, Desktop: Flex wrap -->
            <div class="flex flex-col md:flex-row md:flex-wrap gap-2 md:gap-4">
                <?php foreach ($q['a'] as $idx => $ans): 
                    $value = $idx + 1;
                    $radioId = "vh{$num}_{$idx}";
                    $isChecked = ($currentValue == $value);
                ?>
                <label for="<?= $radioId ?>" class="flex items-center gap-2 p-2 rounded-lg cursor-pointer transition-all
                    <?php if ($isReadonly): ?>
                        <?= $isChecked ? 'bg-blue-100 dark:bg-blue-900/30 border-2 border-blue-400' : 'bg-slate-50 dark:bg-slate-700/50' ?>
                    <?php else: ?>
                        hover:bg-amber-50 dark:hover:bg-amber-900/20 border-2 border-transparent has-[:checked]:bg-amber-100 has-[:checked]:border-amber-400 dark:has-[:checked]:bg-amber-900/30
                    <?php endif; ?>">
                    <input type="radio" 
                           id="<?= $radioId ?>" 
                           name="vh<?= $num ?>" 
                           value="<?= $value ?>"
                           <?= $isChecked ? 'checked' : '' ?>
                           <?= $isReadonly ? 'disabled' : 'required' ?>
                           class="w-4 h-4 text-amber-500 focus:ring-amber-400 hidden md:block">
                    <span class="md:hidden w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0
                        <?= $isChecked ? 'bg-amber-500 border-amber-500' : 'border-slate-300 dark:border-slate-600' ?>">
                        <?php if ($isChecked): ?>
                        <i class="fas fa-check text-white text-xs"></i>
                        <?php endif; ?>
                    </span>
                    <span class="text-sm text-slate-700 dark:text-slate-300"><?= htmlspecialchars($ans) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Question 20: Textarea -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-100 dark:border-slate-700">
            <h5 class="font-bold text-slate-800 dark:text-white mb-3 text-sm md:text-base">
                19. ปัญหา/อุปสรรค และความต้องการความช่วยเหลือ
            </h5>
            <textarea 
                name="vh20" 
                id="vh20" 
                rows="4" 
                <?= $isReadonly ? 'readonly' : '' ?>
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm text-slate-700 dark:text-white focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 transition-all <?= $isReadonly ? 'cursor-not-allowed' : '' ?>"
                placeholder="<?= $isReadonly ? '' : 'กรอกรายละเอียด...' ?>"><?= htmlspecialchars($visitData['vh20'] ?? '') ?></textarea>
        </div>
    </div>
</form>

<?php if (!$isReadonly): ?>
<style>
/* Make radio labels work better on mobile */
label:has(input[type="radio"]:checked) {
    background-color: rgba(251, 191, 36, 0.2) !important;
    border-color: #f59e0b !important;
}
label:has(input[type="radio"]:checked) span.md\\:hidden {
    background-color: #f59e0b !important;
    border-color: #f59e0b !important;
}
</style>
<script>
// Handle mobile radio button visual feedback
document.querySelectorAll('#addVisitForm input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Remove checked state from siblings
        const name = this.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
            const label = r.closest('label');
            const circle = label.querySelector('span.md\\:hidden');
            if (r.checked) {
                circle.classList.add('bg-amber-500', 'border-amber-500');
                circle.classList.remove('border-slate-300', 'dark:border-slate-600');
                circle.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
            } else {
                circle.classList.remove('bg-amber-500', 'border-amber-500');
                circle.classList.add('border-slate-300', 'dark:border-slate-600');
                circle.innerHTML = '';
            }
        });
    });
});
</script>
<?php endif; ?>
