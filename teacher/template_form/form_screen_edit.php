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

// Helper for radio checked
function checked($val, $target) {
    if (is_array($target)) return in_array($val, $target) ? 'checked' : '';
    return $val === $target ? 'checked' : '';
}

// Helper for special ability detail (แก้ไขให้รองรับ associative array)
function get_special_detail($special_ability_detail, $i) {
    if (is_array($special_ability_detail)) {
        // กรณี associative array เช่น ['special_0'=>[...], ...]
        $key = 'special_' . $i;
        if (isset($special_ability_detail[$key])) {
            return (array)$special_ability_detail[$key];
        }
        // กรณี array indexed (fallback)
        if (isset($special_ability_detail[$i])) {
            return (array)$special_ability_detail[$i];
        }
    }
    return ['', ''];
}
?>
<form id="screenEditForm" method="POST" class="space-y-6">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">

    <div class="bg-green-500 border rounded-lg shadow-sm p-4 mb-4 text-white">
        <h2 class="text-lg font-semibold">🎓 ข้อมูลนักเรียน</h2>
        <p>ชื่อ: <?= htmlspecialchars($student_name) ?> เลขที่: <?= htmlspecialchars($student_no) ?> ชั้น: ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?></p>
        <p>บันทึกข้อมูลของ ภาคเรียนที่ <?= htmlspecialchars($term) ?> ปีการศึกษา <?= htmlspecialchars($pee) ?></p>
    </div>

    <!-- 1. ความสามารถพิเศษ -->
    <div>
        <h3 class="font-bold mb-2">1. ด้านความสามารถพิเศษ</h3>
        <label class="mr-4"><input type="radio" name="special_ability" value="ไม่มี" <?= checked('ไม่มี', $screenData['special_ability'] ?? '') ?>> ไม่มี</label>
        <label><input type="radio" name="special_ability" value="มี" <?= checked('มี', $screenData['special_ability'] ?? '') ?>> มี</label>
        <div id="specialAbilityFields" class="<?= ($screenData['special_ability'] ?? '') === 'มี' ? '' : 'hidden' ?> mt-2 space-y-4">
            <?php
            $subjects = [
                'คณิตศาสตร์', 'ภาษาไทย', 'ภาษาต่างประเทศ', 'วิทยาศาสตร์',
                'ศิลปะ', 'การงานอาชีพและเทคโนโลยี', 'สุขศึกษา และพลศึกษา', 'สังคมศึกษา ศาสนา และวัฒนธรรม'
            ];
            $special_ability_detail = $screenData['special_ability_detail'] ?? [];
            foreach ($subjects as $i => $subject):
                $details = get_special_detail($special_ability_detail, $i);
                $checked = !empty(array_filter($details, fn($v) => trim($v) !== ''));
            ?>
            <div>
                <label>
                    <input type="checkbox" class="subject-checkbox" data-subject="<?= $i ?>" <?= $checked ? 'checked' : '' ?>>
                    <span class="font-semibold"><?= $subject ?></span>
                </label>
                <div class="flex flex-col gap-2 mt-1 subject-inputs <?= $checked ? '' : 'hidden' ?>" data-subject="<?= $i ?>">
                    <input type="text" name="special_<?= $i ?>[]" class="input input-bordered w-full" placeholder="รายละเอียด" value="<?= htmlspecialchars($details[0] ?? '') ?>">
                    <input type="text" name="special_<?= $i ?>[]" class="input input-bordered w-full" placeholder="รายละเอียด" value="<?= htmlspecialchars($details[1] ?? '') ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- เพิ่ม input hidden สำหรับรายละเอียดความสามารถพิเศษ -->
        <input type="hidden" name="special_ability_detail" id="special_ability_detail">
    </div>

    <!-- 2. ด้านการเรียน -->
    <div>
        <h3 class="font-bold mb-2">2. ด้านการเรียน</h3>
        <label class="mr-4"><input type="radio" name="study_status" value="ปกติ" <?= checked('ปกติ', $screenData['study_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="study_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['study_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="study_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['study_status'] ?? '') ?>> มีปัญหา</label>
        <div id="studyRiskFields" class="<?= ($screenData['study_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $study_risk = [
                'ผลการเรียนเฉลี่ย 1.00-2.00',
                'ติด 0, ร, มส, มผ 1-2 วิชา/1 ภาคเรียน',
                'ไม่เข้าเรียน 1-2 ครั้ง/รายวิชา',
                'มาเรียนสาย 3 ครั้งต่อสัปดาห์',
                'ไม่ตั้งใจเรียนขณะครูสอน',
                'ไม่มีอุปกรณ์การเรียนมาเรียน หรือ นำอุปกรณ์การเรียนมาไม่ครบ',
                'อ่านสะกดคำไม่ได้',
                'ไม่รู้ความหมายของคำ',
                'จับใจความสำคัญไม่ได้',
                'เขียนตัวอักษรไม่ได้',
                'เขียนไม่ได้ใจความ',
                'คำนวณ บวก ลบ คูณ หาร ไม่ได้',
                'ไม่ส่งงาน 1-2 วิชา',
                'ไม่ผ่านคุณลักษณะอันพึงประสงค์ 1 ข้อ'
            ];
            $study_risk_val = $screenData['study_risk'] ?? [];
            foreach ($study_risk as $item): ?>
            <label class="block"><input type="checkbox" name="study_risk[]" value="<?= $item ?>" <?= checked($item, $study_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="studyProblemFields" class="<?= ($screenData['study_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $study_problem = [
                'ผลการประเมินการอ่านคิดวิเคราะห์ และเขียนสื่อความอยู่ในระดับ 1',
                'ผลการเรียนต่ำกว่า 1.00',
                'ติด 0, ร, มส, มผ มากกว่า 2 วิชา/1 ภาค เรียน',
                'มาเรียนสายมากกว่า 3 ครั้งต่อสัปดาห์',
                'ไม่เข้าเรียนหลายครั้งโดยไม่มีเหตุจำเป็น',
                'สมาธิสั้น',
                'ขาดเรียนบ่อยมากกว่า 1 วัน/สัปดาห์',
                'ไม่ส่งงานมากกว่า 2 วิชา',
                'ไม่ผ่านคุณลักษณะอันพึงประสงค์ตั้งแต่ 2 ข้อขึ้นไป',
                'ผลการประเมินการอ่านคิดวิเคราะห์และเขียนสื่อความไม่ผ่าน'
            ];
            $study_problem_val = $screenData['study_problem'] ?? [];
            foreach ($study_problem as $item): ?>
            <label class="block"><input type="checkbox" name="study_problem[]" value="<?= $item ?>" <?= checked($item, $study_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 3. ด้านสุขภาพ -->
    <div>
        <h3 class="font-bold mb-2">3. ด้านสุขภาพ</h3>
        <label class="mr-4"><input type="radio" name="health_status" value="ปกติ" <?= checked('ปกติ', $screenData['health_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="health_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['health_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="health_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['health_status'] ?? '') ?>> มีปัญหา</label>
        <div id="healthRiskFields" class="<?= ($screenData['health_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $health_risk = [
                'ร่างกายไม่แข็งแรง',
                'มีโรคประจำตัวหรือเจ็บป่วยบ่อย',
                'มีปัญหาด้านสายตา (สวมแว่น/คอนแท็คเลนส์)'
            ];
            $health_risk_val = $screenData['health_risk'] ?? [];
            foreach ($health_risk as $item): ?>
            <label class="block"><input type="checkbox" name="health_risk[]" value="<?= $item ?>" <?= checked($item, $health_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="healthProblemFields" class="<?= ($screenData['health_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $health_problem = [
                'มีภาวะทุพโภชนาการ',
                'มีความพิการทางร่างกาย',
                'ป่วยเป็นโรคร้ายแรง/เรื้อรัง',
                'มีปัญหาด้านสายตา (ไม่สวมแว่น/คอนแท็คเลนส์)',
                'มีความบกพร่องทางการได้ยิน',
                'สมรรถภาพทางร่างกายต่ำ'
            ];
            $health_problem_val = $screenData['health_problem'] ?? [];
            foreach ($health_problem as $item): ?>
            <label class="block"><input type="checkbox" name="health_problem[]" value="<?= $item ?>" <?= checked($item, $health_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4. ด้านเศรษฐกิจ -->
    <div>
        <h3 class="font-bold mb-2">4. ด้านเศรษฐกิจ</h3>
        <label class="mr-4"><input type="radio" name="economic_status" value="ปกติ" <?= checked('ปกติ', $screenData['economic_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="economic_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['economic_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="economic_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['economic_status'] ?? '') ?>> มีปัญหา</label>
        <div id="economicRiskFields" class="<?= ($screenData['economic_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $economic_risk = [
                'รายได้ครอบครัว 5,000-10,000 บาท ต่อเดือน',
                'บิดาหรือมารดาตกงาน (1 คน) แต่รายได้มากกว่า 5,000 บาท'
            ];
            $economic_risk_val = $screenData['economic_risk'] ?? [];
            foreach ($economic_risk as $item): ?>
            <label class="block"><input type="checkbox" name="economic_risk[]" value="<?= $item ?>" <?= checked($item, $economic_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="economicProblemFields" class="<?= ($screenData['economic_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $economic_problem = [
                'รายได้ครอบครัวต่ำกว่า 5,000 บาทต่อเดือน',
                'บิดาและมารดาตกงาน(ทั้ง 2 คน)',
                'ครอบครัวมีภาระหนี้สินจำนวนมาก',
                'รายได้ไม่เพียงพอต่อการใช้จ่ายในชีวิตประจำวัน'
            ];
            $economic_problem_val = $screenData['economic_problem'] ?? [];
            foreach ($economic_problem as $item): ?>
            <label class="block"><input type="checkbox" name="economic_problem[]" value="<?= $item ?>" <?= checked($item, $economic_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 5. ด้านสวัสดิภาพและความปลอดภัย -->
    <div>
        <h3 class="font-bold mb-2">5. ด้านสวัสดิภาพและความปลอดภัย</h3>
        <label class="mr-4"><input type="radio" name="welfare_status" value="ปกติ" <?= checked('ปกติ', $screenData['welfare_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="welfare_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['welfare_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="welfare_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['welfare_status'] ?? '') ?>> มีปัญหา</label>
        <div id="welfareRiskFields" class="<?= ($screenData['welfare_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $welfare_risk = [
                'พ่อแม่แยกทางกัน หรือแต่งงานใหม่',
                'ที่พักอาศัยอยู่ในชุมชนแออัด หรือใกล้แหล่งมั่วสุม / สถานเริงรมย์',
                'อยู่หอพัก',
                'มีบุคคลเจ็บป่วยด้วยโรคร้ายแรง/เรื้อรัง',
                'บุคคลในครอบครัวติดสารเสพติด',
                'บุคคลในครอบครัวเล่นการพนัน',
                'มีความขัดแย้ง / ทะเลาะกันในครอบครัว'
            ];
            $welfare_risk_val = $screenData['welfare_risk'] ?? [];
            foreach ($welfare_risk as $item): ?>
            <label class="block"><input type="checkbox" name="welfare_risk[]" value="<?= $item ?>" <?= checked($item, $welfare_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="welfareProblemFields" class="<?= ($screenData['welfare_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $welfare_problem = [
                'ไม่มีผู้ดูแล',
                'มีความขัดแย้งและมีการใช้ความรุนแรงในครอบครัว',
                'ถูกทารุณ / ทำร้ายจากบุคคลในครอบครัว',
                'ถูกล่วงละเมิดทางเพศ',
                'สูบบุหรี่ / กัญชา / ของมึนเมา',
                'เล่นการพนัน'
            ];
            $welfare_problem_val = $screenData['welfare_problem'] ?? [];
            foreach ($welfare_problem as $item): ?>
            <label class="block"><input type="checkbox" name="welfare_problem[]" value="<?= $item ?>" <?= checked($item, $welfare_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 6. ด้านพฤติกรรมการใช้สารเสพติด -->
    <div>
        <h3 class="font-bold mb-2">6. ด้านพฤติกรรมการใช้สารเสพติด</h3>
        <label class="mr-4"><input type="radio" name="drug_status" value="ปกติ" <?= checked('ปกติ', $screenData['drug_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="drug_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['drug_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="drug_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['drug_status'] ?? '') ?>> มีปัญหา</label>
        <div id="drugRiskFields" class="<?= ($screenData['drug_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $drug_risk = [
                'คบเพื่อนในกลุ่มใช้สารเสพติด เช่น บุหรี่ , สุรา',
                'สมาชิกในครอบครัวข้องเกี่ยวกับยาเสพติด',
                'เคยลองสูบบุหรี่ / กัญชา /ของมึนเมา',
                'อยู่ในสภาพแวดล้อมที่ใช้สารเสพติด'
            ];
            $drug_risk_val = $screenData['drug_risk'] ?? [];
            foreach ($drug_risk as $item): ?>
            <label class="block"><input type="checkbox" name="drug_risk[]" value="<?= $item ?>" <?= checked($item, $drug_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="drugProblemFields" class="<?= ($screenData['drug_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $drug_problem = [
                'ใช้หรือเสพเองมากกว่า 2 ครั้ง',
                'มีประวัติเกี่ยวข้องกับสารเสพติด',
                'เป็นผู้ติดบุหรี่ สุรา หรือสารเสพติดอื่นๆ'
            ];
            $drug_problem_val = $screenData['drug_problem'] ?? [];
            foreach ($drug_problem as $item): ?>
            <label class="block"><input type="checkbox" name="drug_problem[]" value="<?= $item ?>" <?= checked($item, $drug_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 7. ด้านพฤติกรรมการใช้ความรุนแรง -->
    <div>
        <h3 class="font-bold mb-2">7. ด้านพฤติกรรมการใช้ความรุนแรง</h3>
        <label class="mr-4"><input type="radio" name="violence_status" value="ปกติ" <?= checked('ปกติ', $screenData['violence_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="violence_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['violence_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="violence_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['violence_status'] ?? '') ?>> มีปัญหา</label>
        <div id="violenceRiskFields" class="<?= ($screenData['violence_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $violence_risk = [
                'ไม่ปฏิบัติตามกฎจารจร',
                'พาหนะและสภาพการเดินทางไม่ปลอดภัย',
                'มีประวัติทะเลาะวิวาท',
                'ก้าวร้าว เกเร'
            ];
            $violence_risk_val = $screenData['violence_risk'] ?? [];
            foreach ($violence_risk as $item): ?>
            <label class="block"><input type="checkbox" name="violence_risk[]" value="<?= $item ?>" <?= checked($item, $violence_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="violenceProblemFields" class="<?= ($screenData['violence_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $violence_problem = [
                'ไม่ปฏิบัติตามกฎจารจรบ่อยๆ หรือเป็นประจำ',
                'ทะเลาะวิวาทบ่อยๆ',
                'ทำร้ายร่างกายผู้อื่น'
            ];
            $violence_problem_val = $screenData['violence_problem'] ?? [];
            foreach ($violence_problem as $item): ?>
            <label class="block"><input type="checkbox" name="violence_problem[]" value="<?= $item ?>" <?= checked($item, $violence_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 8. ด้านพฤติกรรมทางเพศ -->
    <div>
        <h3 class="font-bold mb-2">8. ด้านพฤติกรรมทางเพศ</h3>
        <label class="mr-4"><input type="radio" name="sex_status" value="ปกติ" <?= checked('ปกติ', $screenData['sex_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="sex_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['sex_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="sex_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['sex_status'] ?? '') ?>> มีปัญหา</label>
        <div id="sexRiskFields" class="<?= ($screenData['sex_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $sex_risk = [
                'อยู่ในกลุ่มประพฤติตนเหมือนเพศตรงข้าม',
                'ทำงานพิเศษที่ล่อแหลมต่อการถูกล่วงละเมิดทางเพศ',
                'จับคู่ชัดเจนและแยกกลุ่มอยู่ด้วยกันสองต่อสองบ่อยครั้ง',
                'อยู่ในกลุ่มขายบริการ',
                'ใช้เครื่องมือสื่อสารเป็นเวลานานและบ่อยครั้ง'
            ];
            $sex_risk_val = $screenData['sex_risk'] ?? [];
            foreach ($sex_risk as $item): ?>
            <label class="block"><input type="checkbox" name="sex_risk[]" value="<?= $item ?>" <?= checked($item, $sex_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="sexProblemFields" class="<?= ($screenData['sex_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $sex_problem = [
                'ประพฤติตนเหมือนเพศตรงข้าม',
                'ขาดเรียนไปกับคู่ของตนเสมอๆ',
                'อยู่ด้วยกัน',
                'ตั้งครรภ์',
                'ขายบริการทางเพศ',
                'มีการมั่วสุมทางเพศ',
                'หมกมุ่นในการใช้เครื่องมือสื่อสารที่เกี่ยวข้องทางเพศ'
            ];
            $sex_problem_val = $screenData['sex_problem'] ?? [];
            foreach ($sex_problem as $item): ?>
            <label class="block"><input type="checkbox" name="sex_problem[]" value="<?= $item ?>" <?= checked($item, $sex_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 9. ด้านการติดเกม -->
    <div>
        <h3 class="font-bold mb-2">9. ด้านการติดเกม</h3>
        <label class="mr-4"><input type="radio" name="game_status" value="ปกติ" <?= checked('ปกติ', $screenData['game_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="game_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['game_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="game_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['game_status'] ?? '') ?>> มีปัญหา</label>
        <div id="gameRiskFields" class="<?= ($screenData['game_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $game_risk = [
                'เล่นเกมเกินวันละ 1 ชั่วโมง',
                'ขาดจินตนาการและความคิดสร้างสรรค์',
                'เก็บตัว แยกตัวจากกลุ่มเพื่อน',
                'ใช้จ่ายเงินผิดปกติ',
                'อยู่ในกลุ่มเพื่อนเล่นเกม',
                'ร้านเกมอยู่ใกล้บ้านหรือโรงเรียน'
            ];
            $game_risk_val = $screenData['game_risk'] ?? [];
            foreach ($game_risk as $item): ?>
            <label class="block"><input type="checkbox" name="game_risk[]" value="<?= $item ?>" <?= checked($item, $game_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="gameProblemFields" class="<?= ($screenData['game_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $game_problem = [
                'ใช้เวลาเล่นเกมเกิน 2 ชั่วโมง',
                'หงุดหงิด ฉุนเฉียว อารมณ์รุนแรง',
                'บุคลิกภาพผิดไปจากเดิม',
                'ขาดความรับผิดชอบ',
                'หมกมุ่น จริงจังในการเล่นเกม',
                'ใช้เงินสิ้นเปลือง โกหก ลักขโมยเงินเพื่อเล่นเกม'
            ];
            $game_problem_val = $screenData['game_problem'] ?? [];
            foreach ($game_problem as $item): ?>
            <label class="block"><input type="checkbox" name="game_problem[]" value="<?= $item ?>" <?= checked($item, $game_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 10. นักเรียนที่มีความต้องการพิเศษ -->
    <div>
        <h3 class="font-bold mb-2">10. นักเรียนที่มีความต้องการพิเศษ</h3>
        <label class="mr-4"><input type="radio" name="special_need_status" value="ไม่มี" <?= checked('ไม่มี', $screenData['special_need_status'] ?? '') ?>> ไม่มี</label>
        <label><input type="radio" name="special_need_status" value="มี" <?= checked('มี', $screenData['special_need_status'] ?? '') ?>> มี</label>
        <div id="specialNeedFields" class="<?= ($screenData['special_need_status'] ?? '') === 'มี' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เลือกได้ 1 ข้อ):</div>
            <?php
            $special_need_type = [
                'มีความบกพร่องทางการเห็น',
                'มีความบกพร่องทางการได้ยิน',
                'มีความบกพร่องทางสติปัญญา',
                'มีความบกพร่องทางร่างกายและสุขภาพ',
                'มีความบกพร่องทางการเรียนรู้',
                'มีความบกพร่องทางพฤติกรรมหรืออารมณ์',
                'มีความบกพร่องทางการพูดและภาษา',
                'ออทิสติก',
                'มีสมาธิสั้น',
                'พิการซ้ำซ้อน (มีความบกพร่องตั้งแต่ 2 ประเภทขึ้นไป)'
            ];
            $special_need_val = $screenData['special_need_type'] ?? '';
            foreach ($special_need_type as $item): ?>
            <label class="block"><input type="radio" name="special_need_type" value="<?= $item ?>" <?= checked($item, $special_need_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 11. ด้านการใช้เครื่องมือสื่อสารอิเล็กทรอนิกส์ -->
    <div>
        <h3 class="font-bold mb-2">11. ด้านการใช้เครื่องมือสื่อสารอิเล็กทรอนิกส์</h3>
        <label class="mr-4"><input type="radio" name="it_status" value="ปกติ" <?= checked('ปกติ', $screenData['it_status'] ?? '') ?>> ปกติ</label>
        <label class="mr-4"><input type="radio" name="it_status" value="เสี่ยง" <?= checked('เสี่ยง', $screenData['it_status'] ?? '') ?>> เสี่ยง</label>
        <label><input type="radio" name="it_status" value="มีปัญหา" <?= checked('มีปัญหา', $screenData['it_status'] ?? '') ?>> มีปัญหา</label>
        <div id="itRiskFields" class="<?= ($screenData['it_status'] ?? '') === 'เสี่ยง' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
            <?php
            $it_risk = [
                'เคยใช้โทรศัพท์มือถือในระหว่างการเรียนการสอนโดยไม่จำเป็น',
                'เข้าใช้ MSN, Facebook ,Twitter หรือ chat เกินวันละ 1 ชั่วโมง'
            ];
            $it_risk_val = $screenData['it_risk'] ?? [];
            foreach ($it_risk as $item): ?>
            <label class="block"><input type="checkbox" name="it_risk[]" value="<?= $item ?>" <?= checked($item, $it_risk_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
        <div id="itProblemFields" class="<?= ($screenData['it_status'] ?? '') === 'มีปัญหา' ? '' : 'hidden' ?> mt-2">
            <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
            <?php
            $it_problem = [
                'ใช้โทรศัพท์มือถือในระหว่างการเรียนการสอน 2-3 ครั้ง/วัน',
                'เข้าใช้ MSN, Facebook, Twitter หรือ chat เกินวันละ 2 ชั่วโมง'
            ];
            $it_problem_val = $screenData['it_problem'] ?? [];
            foreach ($it_problem as $item): ?>
            <label class="block"><input type="checkbox" name="it_problem[]" value="<?= $item ?>" <?= checked($item, $it_problem_val) ?>> <?= $item ?></label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ปุ่มบันทึกจะอยู่ใน modal-footer ของหน้าหลัก ไม่ต้องใส่ในฟอร์มนี้ -->
</form>
<script>
// Step 1: Show/hide subject fields
document.querySelectorAll('input[name="special_ability"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('specialAbilityFields').classList.toggle('hidden', e.target.value !== 'มี');
    });
});
document.querySelectorAll('.subject-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const subjectInputs = document.querySelector('.subject-inputs[data-subject="' + this.dataset.subject + '"]');
        if (this.checked) {
            subjectInputs.classList.remove('hidden');
        } else {
            subjectInputs.classList.add('hidden');
            subjectInputs.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
        }
    });
});

// Step 2: Show/hide risk/problem fields
document.querySelectorAll('input[name="study_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('studyRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('studyProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// Step 3: Show/hide health risk/problem fields
document.querySelectorAll('input[name="health_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('healthRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('healthProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// Step 4: Show/hide economic risk/problem fields
document.querySelectorAll('input[name="economic_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('economicRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('economicProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// Step 5: Show/hide welfare risk/problem fields
document.querySelectorAll('input[name="welfare_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('welfareRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('welfareProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// Step 6: Show/hide drug risk/problem fields
document.querySelectorAll('input[name="drug_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('drugRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('drugProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// Step 7: Show/hide violence risk/problem fields
document.querySelectorAll('input[name="violence_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('violenceRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('violenceProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// Step 8: Show/hide sex risk/problem fields
document.querySelectorAll('input[name="sex_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('sexRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('sexProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// Step 9: Show/hide game risk/problem fields
document.querySelectorAll('input[name="game_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('gameRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('gameProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// Step 10: Show/hide special need fields
document.querySelectorAll('input[name="special_need_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('specialNeedFields').classList.toggle('hidden', e.target.value !== 'มี');
    });
});

// Step 11: Show/hide IT risk/problem fields
document.querySelectorAll('input[name="it_status"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('itRiskFields').classList.toggle('hidden', e.target.value !== 'เสี่ยง');
        document.getElementById('itProblemFields').classList.toggle('hidden', e.target.value !== 'มีปัญหา');
    });
});

// ฟังก์ชันรวมรายละเอียดความสามารถพิเศษเป็น array แล้ว serialize เป็น JSON
function collectSpecialAbilityDetail() {
    const result = {};
    document.querySelectorAll('.subject-checkbox').forEach(cb => {
        if (cb.checked) {
            const subject = cb.dataset.subject;
            const inputs = document.querySelectorAll('.subject-inputs[data-subject="' + subject + '"] input[type="text"]');
            const details = Array.from(inputs).map(input => input.value.trim()).filter(v => v !== '');
            if (details.length > 0) {
                result['special_' + subject] = details;
            }
        }
    });
    return result;
}

// ก่อน submit form ให้ set ค่า detail ลง hidden input
document.getElementById('screenEditForm').addEventListener('submit', function(e) {
    // รายละเอียดความสามารถพิเศษ
    const detail = collectSpecialAbilityDetail();
    document.getElementById('special_ability_detail').value = Object.keys(detail).length > 0 ? JSON.stringify(detail) : '';
    // ...สามารถเพิ่ม logic สำหรับ field อื่นๆ ที่ต้อง serialize array เป็น JSON ได้ที่นี่...
});

// Optional: Validate before submit (if you want to prevent empty required fields)
document.getElementById('screenEditForm').addEventListener('submit', function(e) {
    // ตรวจสอบว่ามีการเลือก radio ทุกด้านหรือไม่
    let valid = true;
    const requiredRadios = [
        'special_ability','study_status','health_status','economic_status','welfare_status',
        'drug_status','violence_status','sex_status','game_status','special_need_status','it_status'
    ];
    requiredRadios.forEach(name => {
        if (!document.querySelector('input[name="'+name+'"]:checked')) {
            valid = false;
        }
    });
    if (!valid) {
        e.preventDefault();
        Swal.fire('กรุณากรอกข้อมูลให้ครบถ้วน','โปรดเลือกตัวเลือกทุกด้าน','warning');
    }
});
</script>
