<?php
require_once '../../class/Screeningdata.php';
require_once '../../config/Database.php';

$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['student_name'] ?? '';
$student_no = $_GET['student_no'] ?? '';
$student_class = $_GET['student_class'] ?? '';
$student_room = $_GET['student_room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$screening = new ScreeningData($db);
$screenData = $screening->getScreeningDataByStudentId($student_id, $pee);

// Fetch classmates who already have screening data for copying
$classmatesRec = $screening->getScreenByClassAndRoom($student_class, $student_room, $pee);
$validClassmates = array_filter($classmatesRec, function($c) use ($student_id) {
    return $c['screen_ishave'] == 1 && $c['Stu_id'] != $student_id;
});

function checked($val, $target) {
    if (is_array($target)) return in_array($val, $target) ? 'checked' : '';
    return $val === $target ? 'checked' : '';
}

function get_special_detail($special_ability_detail, $i) {
    if (is_array($special_ability_detail)) {
        $key = 'special_' . $i;
        if (isset($special_ability_detail[$key])) return (array)$special_ability_detail[$key];
        if (isset($special_ability_detail[$i])) return (array)$special_ability_detail[$i];
    }
    return ['', ''];
}

$subjects = ['คณิตศาสตร์', 'ภาษาไทย', 'ภาษาต่างประเทศ', 'วิทยาศาสตร์', 'ศิลปะ', 'การงานอาชีพและเทคโนโลยี', 'สุขศึกษา และพลศึกษา', 'สังคมศึกษา ศาสนา และวัฒนธรรม'];
$special_ability_detail = $screenData['special_ability_detail'] ?? [];
?>

<style>
.section-card { transition: all 0.3s ease; }
.section-card:hover { box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
.radio-option { transition: all 0.2s ease; }
.radio-option:hover { background: rgba(99, 102, 241, 0.1); }
.radio-option input:checked + span { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
.checkbox-item { transition: all 0.2s ease; }
.checkbox-item:hover { background: rgba(99, 102, 241, 0.05); }
.checkbox-item input:checked + span { background: rgba(99, 102, 241, 0.15); border-color: #6366f1; }
/* Fix SweetAlert2 appearing behind modals */
.swal2-container { z-index: 99999 !important; }
</style>

<script>
    /**
     * Copy assessment data from another student in the same room
     */
    function copyFromClassmateScreenEdit(stuId) {
        Swal.fire({
            title: 'ต้องการคัดลอกข้อมูล?',
            text: "ระบบจะดึงข้อมูลการประเมินของเพื่อนมาใส่ในแบบฟอร์มนี้ ข้อมูลปัจจุบันจะถูกแทนที่",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#fbbf24',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'กำลังโหลด...', didOpen: () => Swal.showLoading(), zIndex: 100000 });
                
                $.ajax({
                    url: '../teacher/api/fetch_student_screen_answers.php',
                    method: 'GET',
                    data: { student_id: stuId, pee: '<?= $pee ?>' },
                    success: function(res) {
                        Swal.close();
                        if (res.status === 'success') {
                            const data = res.data;
                            const formId = '#screenEditForm';
                            
                            // 1. Special Ability
                            const specialAbilityRadio = document.querySelector(`${formId} input[name="special_ability"][value="${data.special_ability}"]`);
                            if (specialAbilityRadio) {
                                specialAbilityRadio.checked = true;
                                specialAbilityRadio.dispatchEvent(new Event('change'));
                            }
                            
                            // Special Ability Detail
                            if (data.special_ability === 'มี' && data.special_ability_detail) {
                                let details = data.special_ability_detail;
                                if (typeof details === 'string') details = JSON.parse(details);
                                
                                // Reset checkboxes and inputs first
                                document.querySelectorAll(`${formId} .subject-checkbox`).forEach(cb => {
                                    cb.checked = false;
                                    cb.dispatchEvent(new Event('change'));
                                });

                                Object.keys(details).forEach(key => {
                                    const subIdx = key.replace('special_', '');
                                    const cb = document.querySelector(`${formId} .subject-checkbox[data-subject="${subIdx}"]`);
                                    if (cb) {
                                        cb.checked = true;
                                        cb.dispatchEvent(new Event('change'));
                                        const values = details[key];
                                        const inputs = document.querySelectorAll(`${formId} .subject-inputs[data-subject="${subIdx}"] input`);
                                        values.forEach((v, i) => { if (inputs[i]) inputs[i].value = v; });
                                    }
                                });
                            }

                            // 2-9, 11: Standard statuses
                            const fields = ['study', 'health', 'economic', 'welfare', 'drug', 'violence', 'sex', 'game', 'it'];
                            fields.forEach(f => {
                                const statusName = f + '_status';
                                const statusVal = data[statusName];
                                if (statusVal) {
                                    const radio = document.querySelector(`${formId} input[name="${statusName}"][value="${statusVal}"]`);
                                    if (radio) {
                                        radio.checked = true;
                                        radio.dispatchEvent(new Event('change'));
                                        
                                        // Handle risk/problem checkboxes
                                        const riskName = f + '_risk';
                                        const probName = f + '_problem';
                                        
                                        // Reset checkboxes in these sections
                                        document.querySelectorAll(`${formId} input[name="${riskName}[]"], ${formId} input[name="${probName}[]"]`).forEach(chk => chk.checked = false);

                                        if (statusVal === 'เสี่ยง' && data[riskName]) {
                                            const vals = Array.isArray(data[riskName]) ? data[riskName] : (typeof data[riskName] === 'string' ? JSON.parse(data[riskName]) : []);
                                            vals.forEach(v => {
                                                const chk = document.querySelector(`${formId} input[name="${riskName}[]"][value="${v}"]`);
                                                if (chk) chk.checked = true;
                                            });
                                        } else if (statusVal === 'มีปัญหา' && data[probName]) {
                                            const vals = Array.isArray(data[probName]) ? data[probName] : (typeof data[probName] === 'string' ? JSON.parse(data[probName]) : []);
                                            vals.forEach(v => {
                                                const chk = document.querySelector(`${formId} input[name="${probName}[]"][value="${v}"]`);
                                                if (chk) chk.checked = true;
                                            });
                                        }
                                    }
                                }
                            });

                            // 10: Special Need
                            const snStatus = data.special_need_status;
                            if (snStatus) {
                                const radio = document.querySelector(`${formId} input[name="special_need_status"][value="${snStatus}"]`);
                                if (radio) {
                                    radio.checked = true;
                                    radio.dispatchEvent(new Event('change'));
                                    if (snStatus === 'มี' && data.special_need_type) {
                                        const typeRadio = document.querySelector(`${formId} input[name="special_need_type"][value="${data.special_need_type}"]`);
                                        if (typeRadio) typeRadio.checked = true;
                                    }
                                }
                            }
                            
                            Swal.fire({ icon: 'success', title: 'คัดลอกข้อมูลสำเร็จ', text: 'กรุณาตรวจสอบข้อมูลและกดบันทึก', timer: 2000, showConfirmButton: false });
                        } else {
                            Swal.fire('ข้อผิดพลาด', res.message, 'error');
                        }
                    },
                    error: function() { Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error'); }
                });
            }
        });
    }
</script>

<form id="screenEditForm" method="POST" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    <input type="hidden" name="special_ability_detail" id="special_ability_detail">

    <!-- Student Info -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl p-4 text-white shadow-lg sticky top-0 z-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">✏️</span>
                </div>
                <div>
                    <h2 class="font-bold text-lg"><?= htmlspecialchars($student_name) ?></h2>
                    <p class="text-sm opacity-90">เลขที่ <?= htmlspecialchars($student_no) ?> | ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?> | ปี <?= htmlspecialchars($pee) ?></p>
                </div>
            </div>

            <?php if (!empty($validClassmates)): ?>
            <div class="flex items-center gap-2">
                <select onchange="if(this.value) copyFromClassmateScreenEdit(this.value)" class="w-full md:w-48 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white border border-white/30 rounded-xl px-3 py-2 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-white/50 cursor-pointer transition">
                    <option value="" class="text-slate-800">-- คัดลอกข้อมูลจาก --</option>
                    <?php foreach ($validClassmates as $c): ?>
                        <option value="<?= $c['Stu_id'] ?>" class="text-slate-800">เลขที่ <?= $c['Stu_no'] ?>. <?= $c['full_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 1. ความสามารถพิเศษ -->
    <div class="section-card bg-white rounded-2xl p-4 shadow border">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center"><span>⭐</span></div>
            <h3 class="font-bold text-slate-800 text-sm">1. ด้านความสามารถพิเศษ</h3>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-3">
            <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                <input type="radio" name="special_ability" value="ไม่มี" <?= checked('ไม่มี', $screenData['special_ability'] ?? '') ?> class="hidden">
                <span class="flex-1 text-center py-1 rounded-lg font-semibold text-sm">❌ ไม่มี</span>
            </label>
            <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                <input type="radio" name="special_ability" value="มี" <?= checked('มี', $screenData['special_ability'] ?? '') ?> class="hidden">
                <span class="flex-1 text-center py-1 rounded-lg font-semibold text-sm">✅ มี</span>
            </label>
        </div>
        <div id="specialAbilityFields" class="<?= ($screenData['special_ability'] ?? '') === 'มี' ? '' : 'hidden' ?> space-y-2 p-3 bg-slate-50 rounded-xl">
            <?php foreach ($subjects as $i => $subject):
                $details = get_special_detail($special_ability_detail, $i);
                $isChecked = !empty(array_filter($details, fn($v) => trim($v) !== ''));
            ?>
            <div class="bg-white rounded-lg p-2 border">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="subject-checkbox w-4 h-4 rounded" data-subject="<?= $i ?>" <?= $isChecked ? 'checked' : '' ?>>
                    <span class="font-semibold text-xs"><?= $subject ?></span>
                </label>
                <div class="subject-inputs <?= $isChecked ? '' : 'hidden' ?> mt-2 space-y-1" data-subject="<?= $i ?>">
                    <input type="text" name="special_<?= $i ?>[]" class="w-full px-2 py-1 border rounded text-xs" placeholder="รายละเอียด 1" value="<?= htmlspecialchars($details[0] ?? '') ?>">
                    <input type="text" name="special_<?= $i ?>[]" class="w-full px-2 py-1 border rounded text-xs" placeholder="รายละเอียด 2" value="<?= htmlspecialchars($details[1] ?? '') ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    // Step configurations for edit form
    $editConfigs = [
        ['name' => 'study_status', 'title' => '2. ด้านการเรียน', 'icon' => '📚', 
         'risk' => ['ผลการเรียนเฉลี่ย 1.00-2.00', 'ติด 0, ร, มส, มผ 1-2 วิชา/1 ภาคเรียน', 'ไม่เข้าเรียน 1-2 ครั้ง/รายวิชา', 'มาเรียนสาย 3 ครั้งต่อสัปดาห์', 'ไม่ตั้งใจเรียนขณะครูสอน', 'ไม่มีอุปกรณ์การเรียนมาเรียน หรือ นำอุปกรณ์การเรียนมาไม่ครบ', 'อ่านสะกดคำไม่ได้', 'ไม่รู้ความหมายของคำ', 'จับใจความสำคัญไม่ได้', 'เขียนตัวอักษรไม่ได้', 'เขียนไม่ได้ใจความ', 'คำนวณ บวก ลบ คูณ หาร ไม่ได้', 'ไม่ส่งงาน 1-2 วิชา', 'ไม่ผ่านคุณลักษณะอันพึงประสงค์ 1 ข้อ'], 
         'problem' => ['ผลการประเมินการอ่านคิดวิเคราะห์ และเขียนสื่อความอยู่ในระดับ 1', 'ผลการเรียนต่ำกว่า 1.00', 'ติด 0, ร, มส, มผ มากกว่า 2 วิชา/1 ภาค เรียน', 'มาเรียนสายมากกว่า 3 ครั้งต่อสัปดาห์', 'ไม่เข้าเรียนหลายครั้งโดยไม่มีเหตุจำเป็น', 'สมาธิสั้น', 'ขาดเรียนบ่อยมากกว่า 1 วัน/สัปดาห์', 'ไม่ส่งงานมากกว่า 2 วิชา', 'ไม่ผ่านคุณลักษณะอันพึงประสงค์ตั้งแต่ 2 ข้อขึ้นไป', 'ผลการประเมินการอ่านคิดวิเคราะห์และเขียนสื่อความไม่ผ่าน']],
        ['name' => 'health_status', 'title' => '3. ด้านสุขภาพ', 'icon' => '❤️',
         'risk' => ['ร่างกายไม่แข็งแรง', 'มีโรคประจำตัวหรือเจ็บป่วยบ่อย', 'มีปัญหาด้านสายตา (สวมแว่น/คอนแท็คเลนส์)'],
         'problem' => ['มีภาวะทุพโภชนาการ', 'มีความพิการทางร่างกาย', 'ป่วยเป็นโรคร้ายแรง/เรื้อรัง', 'มีปัญหาด้านสายตา (ไม่สวมแว่น/คอนแท็คเลนส์)', 'มีความบกพร่องทางการได้ยิน', 'สมรรถภาพทางร่างกายต่ำ']],
        ['name' => 'economic_status', 'title' => '4. ด้านเศรษฐกิจ', 'icon' => '💰',
         'risk' => ['รายได้ครอบครัว 5,000-10,000 บาท ต่อเดือน', 'บิดาหรือมารดาตกงาน (1 คน) แต่รายได้มากกว่า 5,000 บาท'],
         'problem' => ['รายได้ครอบครัวต่ำกว่า 5,000 บาทต่อเดือน', 'บิดาและมารดาตกงาน(ทั้ง 2 คน)', 'ครอบครัวมีภาระหนี้สินจำนวนมาก', 'รายได้ไม่เพียงพอต่อการใช้จ่ายในชีวิตประจำวัน']],
        ['name' => 'welfare_status', 'title' => '5. ด้านสวัสดิภาพและความปลอดภัย', 'icon' => '🛡️',
         'risk' => ['พ่อแม่แยกทางกัน หรือแต่งงานใหม่', 'ที่พักอาศัยอยู่ในชุมชนแออัด หรือใกล้แหล่งมั่วสุม / สถานเริงรมย์', 'อยู่หอพัก', 'มีบุคคลเจ็บป่วยด้วยโรคร้ายแรง/เรื้อรัง', 'บุคคลในครอบครัวติดสารเสพติด', 'บุคคลในครอบครัวเล่นการพนัน', 'มีความขัดแย้ง / ทะเลาะกันในครอบครัว'],
         'problem' => ['ไม่มีผู้ดูแล', 'มีความขัดแย้งและมีการใช้ความรุนแรงในครอบครัว', 'ถูกทารุณ / ทำร้ายจากบุคคลในครอบครัว', 'ถูกล่วงละเมิดทางเพศ', 'สูบบุหรี่ / กัญชา / ของมึนเมา', 'เล่นการพนัน']],
        ['name' => 'drug_status', 'title' => '6. ด้านพฤติกรรมการใช้สารเสพติด', 'icon' => '🚫',
         'risk' => ['คบเพื่อนในกลุ่มใช้สารเสพติด เช่น บุหรี่ , สุรา', 'สมาชิกในครอบครัวข้องเกี่ยวกับยาเสพติด', 'เคยลองสูบบุหรี่ / กัญชา /ของมึนเมา', 'อยู่ในสภาพแวดล้อมที่ใช้สารเสพติด'],
         'problem' => ['ใช้หรือเสพเองมากกว่า 2 ครั้ง', 'มีประวัติเกี่ยวข้องกับสารเสพติด', 'เป็นผู้ติดบุหรี่ สุรา หรือสารเสพติดอื่นๆ']],
        ['name' => 'violence_status', 'title' => '7. ด้านพฤติกรรมการใช้ความรุนแรง', 'icon' => '⚡',
         'risk' => ['ไม่ปฏิบัติตามกฎจารจร', 'พาหนะและสภาพการเดินทางไม่ปลอดภัย', 'มีประวัติทะเลาะวิวาท', 'ก้าวร้าว เกเร'],
         'problem' => ['ไม่ปฏิบัติตามกฎจารจรบ่อยๆ หรือเป็นประจำ', 'ทะเลาะวิวาทบ่อยๆ', 'ทำร้ายร่างกายผู้อื่น']],
        ['name' => 'sex_status', 'title' => '8. ด้านพฤติกรรมทางเพศ', 'icon' => '👫',
         'risk' => ['อยู่ในกลุ่มประพฤติตนเหมือนเพศตรงข้าม', 'ทำงานพิเศษที่ล่อแหลมต่อการถูกล่วงละเมิดทางเพศ', 'จับคู่ชัดเจนและแยกกลุ่มอยู่ด้วยกันสองต่อสองบ่อยครั้ง', 'อยู่ในกลุ่มขายบริการ', 'ใช้เครื่องมือสื่อสารเป็นเวลานานและบ่อยครั้ง'],
         'problem' => ['ประพฤติตนเหมือนเพศตรงข้าม', 'ขาดเรียนไปกับคู่ของตนเสมอๆ', 'อยู่ด้วยกัน', 'ตั้งครรภ์', 'ขายบริการทางเพศ', 'มีการมั่วสุมทางเพศ', 'หมกมุ่นในการใช้เครื่องมือสื่อสารที่เกี่ยวข้องทางเพศ']],
        ['name' => 'game_status', 'title' => '9. ด้านการติดเกม', 'icon' => '🎮',
         'risk' => ['เล่นเกมเกินวันละ 1 ชั่วโมง', 'ขาดจินตนาการและความคิดสร้างสรรค์', 'เก็บตัว แยกตัวจากกลุ่มเพื่อน', 'ใช้จ่ายเงินผิดปกติ', 'อยู่ในกลุ่มเพื่อนเล่นเกม', 'ร้านเกมอยู่ใกล้บ้านหรือโรงเรียน'],
         'problem' => ['ใช้เวลาเล่นเกมเกิน 2 ชั่วโมง', 'หงุดหงิด ฉุนเฉียว อารมณ์รุนแรง', 'บุคลิกภาพผิดไปจากเดิม', 'ขาดความรับผิดชอบ', 'หมกมุ่น จริงจังในการเล่นเกม', 'ใช้เงินสิ้นเปลือง โกหก ลักขโมยเงินเพื่อเล่นเกม']],
        ['name' => 'it_status', 'title' => '11. ด้านการใช้เครื่องมือสื่อสาร', 'icon' => '📱',
         'risk' => ['เคยใช้โทรศัพท์มือถือในระหว่างการเรียนการสอนโดยไม่จำเป็น', 'เข้าใช้ MSN, Facebook ,Twitter หรือ chat เกินวันละ 1 ชั่วโมง'],
         'problem' => ['ใช้โทรศัพท์มือถือในระหว่างการเรียนการสอน 2-3 ครั้ง/วัน', 'เข้าใช้ MSN, Facebook, Twitter หรือ chat เกินวันละ 2 ชั่วโมง']],
    ];
    
    foreach ($editConfigs as $config):
        $name = $config['name'];
        $base = str_replace('_status', '', $name);
        $currentStatus = $screenData[$name] ?? '';
        $riskVal = $screenData[$base . '_risk'] ?? [];
        $problemVal = $screenData[$base . '_problem'] ?? [];
    ?>
    <div class="section-card bg-white rounded-2xl p-4 shadow border">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center"><span><?= $config['icon'] ?></span></div>
            <h3 class="font-bold text-slate-800 text-sm"><?= $config['title'] ?></h3>
        </div>
        <div class="grid grid-cols-3 gap-2 mb-3">
            <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                <input type="radio" name="<?= $name ?>" value="ปกติ" <?= checked('ปกติ', $currentStatus) ?> class="hidden">
                <span class="flex-1 text-center py-1 rounded-lg font-semibold text-xs">✅ ปกติ</span>
            </label>
            <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                <input type="radio" name="<?= $name ?>" value="เสี่ยง" <?= checked('เสี่ยง', $currentStatus) ?> class="hidden">
                <span class="flex-1 text-center py-1 rounded-lg font-semibold text-xs">⚠️ เสี่ยง</span>
            </label>
            <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                <input type="radio" name="<?= $name ?>" value="มีปัญหา" <?= checked('มีปัญหา', $currentStatus) ?> class="hidden">
                <span class="flex-1 text-center py-1 rounded-lg font-semibold text-xs">❌ มีปัญหา</span>
            </label>
        </div>
        <div id="<?= $name ?>RiskFields" class="<?= $currentStatus === 'เสี่ยง' ? '' : 'hidden' ?> p-3 bg-amber-50 rounded-xl space-y-1">
            <p class="text-xs font-bold text-amber-700 mb-2">⚠️ เลือกข้อที่เกี่ยวข้อง:</p>
            <?php foreach ($config['risk'] as $item): ?>
            <label class="checkbox-item flex items-center gap-2 p-2 rounded-lg border bg-white cursor-pointer">
                <input type="checkbox" name="<?= $base ?>_risk[]" value="<?= $item ?>" <?= checked($item, $riskVal) ?> class="w-4 h-4 rounded">
                <span class="text-xs"><?= $item ?></span>
            </label>
            <?php endforeach; ?>
        </div>
        <div id="<?= $name ?>ProblemFields" class="<?= $currentStatus === 'มีปัญหา' ? '' : 'hidden' ?> p-3 bg-rose-50 rounded-xl space-y-1">
            <p class="text-xs font-bold text-rose-700 mb-2">❌ เลือกข้อที่เกี่ยวข้อง:</p>
            <?php foreach ($config['problem'] as $item): ?>
            <label class="checkbox-item flex items-center gap-2 p-2 rounded-lg border bg-white cursor-pointer">
                <input type="checkbox" name="<?= $base ?>_problem[]" value="<?= $item ?>" <?= checked($item, $problemVal) ?> class="w-4 h-4 rounded">
                <span class="text-xs"><?= $item ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- 10. ความต้องการพิเศษ -->
    <div class="section-card bg-white rounded-2xl p-4 shadow border">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-violet-500 rounded-lg flex items-center justify-center"><span>🌟</span></div>
            <h3 class="font-bold text-slate-800 text-sm">10. นักเรียนที่มีความต้องการพิเศษ</h3>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-3">
            <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                <input type="radio" name="special_need_status" value="ไม่มี" <?= checked('ไม่มี', $screenData['special_need_status'] ?? '') ?> class="hidden">
                <span class="flex-1 text-center py-1 rounded-lg font-semibold text-sm">❌ ไม่มี</span>
            </label>
            <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                <input type="radio" name="special_need_status" value="มี" <?= checked('มี', $screenData['special_need_status'] ?? '') ?> class="hidden">
                <span class="flex-1 text-center py-1 rounded-lg font-semibold text-sm">✅ มี</span>
            </label>
        </div>
        <div id="specialNeedFields" class="<?= ($screenData['special_need_status'] ?? '') === 'มี' ? '' : 'hidden' ?> p-3 bg-indigo-50 rounded-xl space-y-1">
            <?php 
            $specialNeeds = ['มีความบกพร่องทางการเห็น', 'มีความบกพร่องทางการได้ยิน', 'มีความบกพร่องทางสติปัญญา', 'มีความบกพร่องทางร่างกายและสุขภาพ', 'มีความบกพร่องทางการเรียนรู้', 'มีความบกพร่องทางพฤติกรรมหรืออารมณ์', 'มีความบกพร่องทางการพูดและภาษา', 'ออทิสติก', 'มีสมาธิสั้น', 'พิการซ้ำซ้อน (มีความบกพร่องตั้งแต่ 2 ประเภทขึ้นไป)'];
            $specialNeedVal = $screenData['special_need_type'] ?? '';
            foreach ($specialNeeds as $item): ?>
            <label class="checkbox-item flex items-center gap-2 p-2 rounded-lg border bg-white cursor-pointer">
                <input type="radio" name="special_need_type" value="<?= $item ?>" <?= checked($item, $specialNeedVal) ?> class="w-4 h-4">
                <span class="text-xs"><?= $item ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>
</form>

<script>
(function() {
    // Toggle handlers for Edit Form
    document.querySelectorAll('#screenEditForm input[name="special_ability"]').forEach(el => {
        el.addEventListener('change', e => document.getElementById('specialAbilityFields').classList.toggle('hidden', e.target.value !== 'มี'));
    });

    document.querySelectorAll('#screenEditForm .subject-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const inputs = document.querySelector(`#screenEditForm .subject-inputs[data-subject="${this.dataset.subject}"]`);
            inputs.classList.toggle('hidden', !this.checked);
            if (!this.checked) inputs.querySelectorAll('input').forEach(i => i.value = '');
        });
    });

    ['study_status', 'health_status', 'economic_status', 'welfare_status', 'drug_status', 'violence_status', 'sex_status', 'game_status', 'it_status'].forEach(name => {
        document.querySelectorAll(`#screenEditForm input[name="${name}"]`).forEach(el => {
            el.addEventListener('change', e => {
                document.getElementById(`${name}RiskFields`)?.classList.toggle('hidden', e.target.value !== 'เสี่ยง');
                document.getElementById(`${name}ProblemFields`)?.classList.toggle('hidden', e.target.value !== 'มีปัญหา');
            });
        });
    });

    document.querySelectorAll('#screenEditForm input[name="special_need_status"]').forEach(el => {
        el.addEventListener('change', e => document.getElementById('specialNeedFields').classList.toggle('hidden', e.target.value !== 'มี'));
    });

    window.collectEditSpecialAbilityDetail = function() {
        const result = {};
        document.querySelectorAll('#screenEditForm .subject-checkbox:checked').forEach(cb => {
            const inputs = document.querySelectorAll(`#screenEditForm .subject-inputs[data-subject="${cb.dataset.subject}"] input`);
            const details = Array.from(inputs).map(i => i.value.trim()).filter(v => v);
            if (details.length) result['special_' + cb.dataset.subject] = details;
        });
        return result;
    };

    document.getElementById('screenEditForm').addEventListener('submit', function(e) {
        const detail = window.collectEditSpecialAbilityDetail();
        document.getElementById('special_ability_detail').value = Object.keys(detail).length ? JSON.stringify(detail) : '';
    });
})();
</script>
