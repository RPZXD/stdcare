<?php
$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['student_name'] ?? '';
$student_no = $_GET['student_no'] ?? '';
$student_class = $_GET['student_class'] ?? '';
$student_room = $_GET['student_room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';
?>

<style>
.step-card { transition: all 0.3s ease; }
.step-card:hover { transform: translateY(-2px); }
.radio-option { transition: all 0.2s ease; }
.radio-option:hover { background: rgba(99, 102, 241, 0.1); }
.radio-option input:checked + span { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
.checkbox-option { transition: all 0.2s ease; }
.checkbox-option:hover { background: rgba(99, 102, 241, 0.05); }
.checkbox-option input:checked + span { background: rgba(99, 102, 241, 0.15); border-color: #6366f1; }
.stepper-btn { transition: all 0.3s ease; }
.stepper-btn:hover:not(:disabled) { transform: scale(1.05); }
.stepper-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.progress-step { transition: all 0.3s ease; }
.progress-step.active { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
.progress-step.completed { background: #10b981; color: white; }
</style>

<form id="screenForm" method="POST" class="space-y-4">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
    <input type="hidden" name="special_ability_detail" id="special_ability_detail">

    <!-- Student Info Card -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-4 text-white shadow-lg">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <span class="text-2xl">🎓</span>
            </div>
            <div>
                <h2 class="font-bold text-lg"><?= htmlspecialchars($student_name) ?></h2>
                <p class="text-sm opacity-90">เลขที่ <?= htmlspecialchars($student_no) ?> | ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?> | ปี <?= htmlspecialchars($pee) ?></p>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white rounded-xl p-3 shadow border">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-bold text-slate-600">ความคืบหน้า</span>
            <span id="stepIndicator" class="text-sm font-bold text-indigo-600">ข้อ 1/11</span>
        </div>
        <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
            <div id="progressBarFill" class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-300" style="width: 9%"></div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex items-center justify-between gap-3">
        <button type="button" id="prevStep" class="stepper-btn flex-1 px-4 py-3 bg-slate-200 text-slate-600 font-bold rounded-xl flex items-center justify-center gap-2" disabled>
            <i class="fas fa-chevron-left"></i> <span class="hidden sm:inline">ย้อนกลับ</span>
        </button>
        <button type="button" id="nextStep" class="stepper-btn flex-1 px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold rounded-xl flex items-center justify-center gap-2">
            <span class="hidden sm:inline">ถัดไป</span> <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <!-- Steps Container -->
    <div id="stepsContainer" class="space-y-4">
        <?php
        // Step configurations
        $steps = [
            1 => ['title' => '1. ด้านความสามารถพิเศษ', 'icon' => '⭐', 'color' => 'amber'],
            2 => ['title' => '2. ด้านการเรียน', 'icon' => '📚', 'color' => 'blue'],
            3 => ['title' => '3. ด้านสุขภาพ', 'icon' => '❤️', 'color' => 'rose'],
            4 => ['title' => '4. ด้านเศรษฐกิจ', 'icon' => '💰', 'color' => 'emerald'],
            5 => ['title' => '5. ด้านสวัสดิภาพและความปลอดภัย', 'icon' => '🛡️', 'color' => 'cyan'],
            6 => ['title' => '6. ด้านพฤติกรรมการใช้สารเสพติด', 'icon' => '🚫', 'color' => 'red'],
            7 => ['title' => '7. ด้านพฤติกรรมการใช้ความรุนแรง', 'icon' => '⚡', 'color' => 'orange'],
            8 => ['title' => '8. ด้านพฤติกรรมทางเพศ', 'icon' => '👫', 'color' => 'pink'],
            9 => ['title' => '9. ด้านการติดเกม', 'icon' => '🎮', 'color' => 'violet'],
            10 => ['title' => '10. นักเรียนที่มีความต้องการพิเศษ', 'icon' => '🌟', 'color' => 'indigo'],
            11 => ['title' => '11. ด้านการใช้เครื่องมือสื่อสาร', 'icon' => '📱', 'color' => 'teal'],
        ];

        $subjects = ['คณิตศาสตร์', 'ภาษาไทย', 'ภาษาต่างประเทศ', 'วิทยาศาสตร์', 'ศิลปะ', 'การงานอาชีพและเทคโนโลยี', 'สุขศึกษา และพลศึกษา', 'สังคมศึกษา ศาสนา และวัฒนธรรม'];
        ?>

        <!-- Step 1: ความสามารถพิเศษ -->
        <div class="step step-card bg-white rounded-2xl p-4 shadow-lg border" data-step="1">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow">
                    <span class="text-xl">⭐</span>
                </div>
                <h3 class="font-bold text-slate-800">1. ด้านความสามารถพิเศษ</h3>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-3">
                <label class="radio-option flex items-center gap-2 p-3 rounded-xl border cursor-pointer">
                    <input type="radio" name="special_ability" value="ไม่มี" required class="hidden">
                    <span class="flex-1 text-center py-1 rounded-lg font-semibold text-sm">❌ ไม่มี</span>
                </label>
                <label class="radio-option flex items-center gap-2 p-3 rounded-xl border cursor-pointer">
                    <input type="radio" name="special_ability" value="มี">
                    <span class="flex-1 text-center py-1 rounded-lg font-semibold text-sm">✅ มี</span>
                </label>
            </div>
            <div id="specialAbilityFields" class="hidden space-y-2 mt-3 p-3 bg-slate-50 rounded-xl">
                <?php foreach ($subjects as $i => $subject): ?>
                <div class="bg-white rounded-lg p-3 border">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="subject-checkbox w-5 h-5 rounded" data-subject="<?= $i ?>">
                        <span class="font-semibold text-sm"><?= $subject ?></span>
                    </label>
                    <div class="subject-inputs hidden mt-2 space-y-2" data-subject="<?= $i ?>">
                        <input type="text" name="special_<?= $i ?>[]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="รายละเอียด 1">
                        <input type="text" name="special_<?= $i ?>[]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="รายละเอียด 2">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Step 2-11: Standard 3-option steps -->
        <?php
        $stepConfigs = [
            2 => ['name' => 'study_status', 'title' => '2. ด้านการเรียน', 'icon' => '📚', 'risk' => ['ผลการเรียนเฉลี่ย 1.00-2.00', 'ติด 0, ร, มส, มผ 1-2 วิชา', 'ไม่เข้าเรียน 1-2 ครั้ง/รายวิชา', 'มาเรียนสาย 3 ครั้งต่อสัปดาห์', 'ไม่ตั้งใจเรียนขณะครูสอน', 'ไม่มีอุปกรณ์การเรียนครบ', 'อ่านสะกดคำไม่ได้', 'ไม่รู้ความหมายของคำ', 'จับใจความสำคัญไม่ได้', 'เขียนตัวอักษรไม่ได้', 'เขียนไม่ได้ใจความ', 'คำนวณ บวก ลบ คูณ หาร ไม่ได้', 'ไม่ส่งงาน 1-2 วิชา', 'ไม่ผ่านคุณลักษณะอันพึงประสงค์ 1 ข้อ'], 'problem' => ['ผลการเรียนต่ำกว่า 1.00', 'ติด 0, ร, มส, มผ มากกว่า 2 วิชา', 'มาเรียนสายมากกว่า 3 ครั้งต่อสัปดาห์', 'ไม่เข้าเรียนหลายครั้งโดยไม่มีเหตุ', 'สมาธิสั้น', 'ขาดเรียนบ่อยมากกว่า 1 วัน/สัปดาห์', 'ไม่ส่งงานมากกว่า 2 วิชา', 'ไม่ผ่านคุณลักษณะอันพึงประสงค์ 2+ ข้อ']],
            3 => ['name' => 'health_status', 'title' => '3. ด้านสุขภาพ', 'icon' => '❤️', 'risk' => ['ร่างกายไม่แข็งแรง', 'มีโรคประจำตัวหรือเจ็บป่วยบ่อย', 'มีปัญหาด้านสายตา (สวมแว่น)'], 'problem' => ['มีภาวะทุพโภชนาการ', 'มีความพิการทางร่างกาย', 'ป่วยเป็นโรคร้ายแรง/เรื้อรัง', 'มีปัญหาด้านสายตา (ไม่สวมแว่น)', 'มีความบกพร่องทางการได้ยิน', 'สมรรถภาพทางร่างกายต่ำ']],
            4 => ['name' => 'economic_status', 'title' => '4. ด้านเศรษฐกิจ', 'icon' => '💰', 'risk' => ['รายได้ครอบครัว 5,000-10,000 บาท/เดือน', 'บิดาหรือมารดาตกงาน 1 คน'], 'problem' => ['รายได้ครอบครัวต่ำกว่า 5,000 บาท/เดือน', 'บิดาและมารดาตกงาน(ทั้ง 2 คน)', 'ครอบครัวมีภาระหนี้สินจำนวนมาก', 'รายได้ไม่เพียงพอต่อการใช้จ่าย']],
            5 => ['name' => 'welfare_status', 'title' => '5. ด้านสวัสดิภาพและความปลอดภัย', 'icon' => '🛡️', 'risk' => ['พ่อแม่แยกทางกัน หรือแต่งงานใหม่', 'ที่พักใกล้แหล่งมั่วสุม', 'อยู่หอพัก', 'มีบุคคลเจ็บป่วยด้วยโรคร้ายแรง', 'บุคคลในครอบครัวติดสารเสพติด', 'บุคคลในครอบครัวเล่นการพนัน', 'มีความขัดแย้งในครอบครัว'], 'problem' => ['ไม่มีผู้ดูแล', 'มีการใช้ความรุนแรงในครอบครัว', 'ถูกทารุณ/ทำร้าย', 'ถูกล่วงละเมิดทางเพศ', 'สูบบุหรี่/กัญชา/ของมึนเมา', 'เล่นการพนัน']],
            6 => ['name' => 'drug_status', 'title' => '6. ด้านพฤติกรรมการใช้สารเสพติด', 'icon' => '🚫', 'risk' => ['คบเพื่อนในกลุ่มใช้สารเสพติด', 'สมาชิกในครอบครัวข้องเกี่ยวกับยาเสพติด', 'เคยลองสูบบุหรี่/กัญชา', 'อยู่ในสภาพแวดล้อมที่ใช้สารเสพติด'], 'problem' => ['ใช้หรือเสพเองมากกว่า 2 ครั้ง', 'มีประวัติเกี่ยวข้องกับสารเสพติด', 'เป็นผู้ติดบุหรี่ สุรา หรือสารเสพติด']],
            7 => ['name' => 'violence_status', 'title' => '7. ด้านพฤติกรรมการใช้ความรุนแรง', 'icon' => '⚡', 'risk' => ['ไม่ปฏิบัติตามกฎจราจร', 'พาหนะและสภาพการเดินทางไม่ปลอดภัย', 'มีประวัติทะเลาะวิวาท', 'ก้าวร้าว เกเร'], 'problem' => ['ไม่ปฏิบัติตามกฎจราจรบ่อยๆ', 'ทะเลาะวิวาทบ่อยๆ', 'ทำร้ายร่างกายผู้อื่น']],
            8 => ['name' => 'sex_status', 'title' => '8. ด้านพฤติกรรมทางเพศ', 'icon' => '👫', 'risk' => ['ประพฤติตนเหมือนเพศตรงข้าม', 'ทำงานพิเศษที่ล่อแหลม', 'จับคู่ชัดเจน', 'อยู่ในกลุ่มขายบริการ', 'ใช้เครื่องมือสื่อสารนานและบ่อย'], 'problem' => ['ประพฤติตนเหมือนเพศตรงข้าม', 'ขาดเรียนไปกับคู่เสมอๆ', 'อยู่ด้วยกัน', 'ตั้งครรภ์', 'ขายบริการทางเพศ', 'มีการมั่วสุมทางเพศ']],
            9 => ['name' => 'game_status', 'title' => '9. ด้านการติดเกม', 'icon' => '🎮', 'risk' => ['เล่นเกมเกินวันละ 1 ชั่วโมง', 'ขาดจินตนาการและความคิดสร้างสรรค์', 'เก็บตัว แยกตัวจากกลุ่มเพื่อน', 'ใช้จ่ายเงินผิดปกติ', 'อยู่ในกลุ่มเพื่อนเล่นเกม', 'ร้านเกมอยู่ใกล้บ้านหรือโรงเรียน'], 'problem' => ['ใช้เวลาเล่นเกมเกิน 2 ชั่วโมง', 'หงุดหงิด ฉุนเฉียว อารมณ์รุนแรง', 'บุคลิกภาพผิดไปจากเดิม', 'ขาดความรับผิดชอบ', 'หมกมุ่น จริงจังในการเล่นเกม', 'ใช้เงินสิ้นเปลือง โกหก ลักขโมย']],
            11 => ['name' => 'it_status', 'title' => '11. ด้านการใช้เครื่องมือสื่อสาร', 'icon' => '📱', 'risk' => ['ใช้โทรศัพท์ระหว่างเรียนโดยไม่จำเป็น', 'ใช้ Social Media เกินวันละ 1 ชั่วโมง'], 'problem' => ['ใช้โทรศัพท์ระหว่างเรียน 2-3 ครั้ง/วัน', 'ใช้ Social Media เกินวันละ 2 ชั่วโมง']],
        ];
        
        foreach ($stepConfigs as $stepNum => $config):
            $hidden = $stepNum > 1 ? 'hidden' : '';
        ?>
        <div class="step step-card bg-white rounded-2xl p-4 shadow-lg border <?= $hidden ?>" data-step="<?= $stepNum ?>">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center shadow">
                    <span class="text-xl"><?= $config['icon'] ?></span>
                </div>
                <h3 class="font-bold text-slate-800 text-sm md:text-base"><?= $config['title'] ?></h3>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-3">
                <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                    <input type="radio" name="<?= $config['name'] ?>" value="ปกติ" required class="hidden">
                    <span class="flex-1 text-center py-1 rounded-lg font-semibold text-xs md:text-sm">✅ ปกติ</span>
                </label>
                <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                    <input type="radio" name="<?= $config['name'] ?>" value="เสี่ยง" class="hidden">
                    <span class="flex-1 text-center py-1 rounded-lg font-semibold text-xs md:text-sm">⚠️ เสี่ยง</span>
                </label>
                <label class="radio-option flex items-center p-2 rounded-xl border cursor-pointer">
                    <input type="radio" name="<?= $config['name'] ?>" value="มีปัญหา" class="hidden">
                    <span class="flex-1 text-center py-1 rounded-lg font-semibold text-xs md:text-sm">❌ มีปัญหา</span>
                </label>
            </div>
            <div id="<?= $config['name'] ?>RiskFields" class="hidden p-3 bg-amber-50 rounded-xl space-y-1">
                <p class="text-xs font-bold text-amber-700 mb-2">⚠️ เลือกข้อที่เกี่ยวข้อง:</p>
                <?php foreach ($config['risk'] as $item): ?>
                <label class="checkbox-option flex items-center gap-2 p-2 rounded-lg border bg-white cursor-pointer">
                    <input type="checkbox" name="<?= str_replace('_status', '', $config['name']) ?>_risk[]" value="<?= $item ?>" class="w-4 h-4 rounded">
                    <span class="text-xs md:text-sm"><?= $item ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <div id="<?= $config['name'] ?>ProblemFields" class="hidden p-3 bg-rose-50 rounded-xl space-y-1">
                <p class="text-xs font-bold text-rose-700 mb-2">❌ เลือกข้อที่เกี่ยวข้อง:</p>
                <?php foreach ($config['problem'] as $item): ?>
                <label class="checkbox-option flex items-center gap-2 p-2 rounded-lg border bg-white cursor-pointer">
                    <input type="checkbox" name="<?= str_replace('_status', '', $config['name']) ?>_problem[]" value="<?= $item ?>" class="w-4 h-4 rounded">
                    <span class="text-xs md:text-sm"><?= $item ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Step 10: Special Needs -->
        <div class="step step-card bg-white rounded-2xl p-4 shadow-lg border hidden" data-step="10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-violet-500 rounded-xl flex items-center justify-center shadow">
                    <span class="text-xl">🌟</span>
                </div>
                <h3 class="font-bold text-slate-800">10. นักเรียนที่มีความต้องการพิเศษ</h3>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-3">
                <label class="radio-option flex items-center p-3 rounded-xl border cursor-pointer">
                    <input type="radio" name="special_need_status" value="ไม่มี" required class="hidden">
                    <span class="flex-1 text-center py-1 rounded-lg font-semibold text-sm">❌ ไม่มี</span>
                </label>
                <label class="radio-option flex items-center p-3 rounded-xl border cursor-pointer">
                    <input type="radio" name="special_need_status" value="มี" class="hidden">
                    <span class="flex-1 text-center py-1 rounded-lg font-semibold text-sm">✅ มี</span>
                </label>
            </div>
            <div id="specialNeedFields" class="hidden p-3 bg-indigo-50 rounded-xl space-y-1">
                <p class="text-xs font-bold text-indigo-700 mb-2">🌟 เลือกประเภท:</p>
                <?php 
                $specialNeeds = ['มีความบกพร่องทางการเห็น', 'มีความบกพร่องทางการได้ยิน', 'มีความบกพร่องทางสติปัญญา', 'มีความบกพร่องทางร่างกายและสุขภาพ', 'มีความบกพร่องทางการเรียนรู้', 'มีความบกพร่องทางพฤติกรรมหรืออารมณ์', 'มีความบกพร่องทางการพูดและภาษา', 'ออทิสติก', 'มีสมาธิสั้น', 'พิการซ้ำซ้อน'];
                foreach ($specialNeeds as $item): ?>
                <label class="checkbox-option flex items-center gap-2 p-2 rounded-lg border bg-white cursor-pointer">
                    <input type="radio" name="special_need_type" value="<?= $item ?>" class="w-4 h-4">
                    <span class="text-xs md:text-sm"><?= $item ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" id="saveScreen" class="hidden w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all">
        <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
    </button>
</form>

<script>
(function() {
    const screenFormSteps = document.querySelectorAll('#screenForm .step');
    let screenCurrentStep = 0;
    const screenPrevBtn = document.getElementById('prevStep');
    const screenNextBtn = document.getElementById('nextStep');
    const screenStepIndicator = document.getElementById('stepIndicator');
    const screenProgressBar = document.getElementById('progressBarFill');
    const screenSaveBtn = document.getElementById('saveScreen');

    function showScreenStep(idx) {
        screenFormSteps.forEach((step, i) => step.classList.toggle('hidden', i !== idx));
        screenStepIndicator.textContent = `ข้อ ${idx+1}/11`;
        screenProgressBar.style.width = `${((idx+1)/11)*100}%`;
        screenPrevBtn.disabled = idx === 0;
        screenNextBtn.classList.toggle('hidden', idx === screenFormSteps.length-1);
        screenSaveBtn.classList.toggle('hidden', idx !== screenFormSteps.length-1);
    }

    screenPrevBtn.onclick = () => { if(screenCurrentStep>0){ screenCurrentStep--; showScreenStep(screenCurrentStep); } };
    screenNextBtn.onclick = () => {
        const currentStepDiv = screenFormSteps[screenCurrentStep];
        const radios = currentStepDiv.querySelectorAll('input[type="radio"][required]');
        let checked = radios.length === 0 || Array.from(radios).some(r => document.querySelector(`input[name="${r.name}"]:checked`));
        if (!checked) {
            Swal.fire({icon: 'warning', title: 'กรุณาเลือกตัวเลือก', confirmButtonColor: '#6366f1'});
            return;
        }
        if(screenCurrentStep<screenFormSteps.length-1){ screenCurrentStep++; showScreenStep(screenCurrentStep); }
    };

    showScreenStep(screenCurrentStep);

    // Toggle fields for all steps
    document.querySelectorAll('#screenForm input[name="special_ability"]').forEach(el => {
        el.addEventListener('change', e => document.getElementById('specialAbilityFields').classList.toggle('hidden', e.target.value !== 'มี'));
    });

    document.querySelectorAll('#screenForm .subject-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const inputs = document.querySelector(`#screenForm .subject-inputs[data-subject="${this.dataset.subject}"]`);
            inputs.classList.toggle('hidden', !this.checked);
            if (!this.checked) inputs.querySelectorAll('input').forEach(i => i.value = '');
        });
    });

    ['study_status', 'health_status', 'economic_status', 'welfare_status', 'drug_status', 'violence_status', 'sex_status', 'game_status', 'it_status'].forEach(name => {
        document.querySelectorAll(`#screenForm input[name="${name}"]`).forEach(el => {
            el.addEventListener('change', e => {
                document.getElementById(`${name}RiskFields`)?.classList.toggle('hidden', e.target.value !== 'เสี่ยง');
                document.getElementById(`${name}ProblemFields`)?.classList.toggle('hidden', e.target.value !== 'มีปัญหา');
            });
        });
    });

    document.querySelectorAll('#screenForm input[name="special_need_status"]').forEach(el => {
        el.addEventListener('change', e => document.getElementById('specialNeedFields').classList.toggle('hidden', e.target.value !== 'มี'));
    });

    window.collectScreenSpecialAbilityDetail = function() {
        const result = {};
        document.querySelectorAll('#screenForm .subject-checkbox:checked').forEach(cb => {
            const inputs = document.querySelectorAll(`#screenForm .subject-inputs[data-subject="${cb.dataset.subject}"] input`);
            const details = Array.from(inputs).map(i => i.value.trim()).filter(v => v);
            if (details.length) result['special_' + cb.dataset.subject] = details;
        });
        return result;
    };

    document.getElementById('screenForm').addEventListener('submit', function(e) {
        const detail = window.collectScreenSpecialAbilityDetail();
        document.getElementById('special_ability_detail').value = Object.keys(detail).length ? JSON.stringify(detail) : '';
    });
})();
</script>
