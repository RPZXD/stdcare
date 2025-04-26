<?php
session_start();
if (!isset($_SESSION['Student_login'])) {
    echo '<div class="text-red-500">ไม่ได้รับอนุญาต</div>';
    exit;
}

?>

<!-- เปลี่ยน id ฟอร์ม -->
<form id="screen11Form" method="POST" class="space-y-6">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">


    <div class="bg-blue-100 text-blue-800 px-4 py-3 rounded-md">
        📋 <strong>คำชี้แจง:</strong> กรุณาเลือกคำตอบที่ตรงกับตัวคุณในช่วง 6 เดือนที่ผ่านมา
    </div>

    <!-- Stepper Navigation -->
    <div class="flex items-center justify-between mb-4">
        <button type="button" id="prevStep" class="bg-gray-300 text-gray-700 px-4 py-2 rounded disabled:opacity-50" disabled>ย้อนกลับ</button>
        <span id="stepIndicator" class="font-semibold">ข้อ 1/11</span>
        <button type="button" id="nextStep" class="bg-blue-500 text-white px-4 py-2 rounded">ถัดไป</button>
    </div>

    <!-- Steps Container -->
    <div id="stepsContainer"></div>
        <!-- Step 1 -->
        <div class="step" data-step="1">
            <h3 class="font-bold mb-2">1. ด้านความสามารถพิเศษ (ความถนัดและความสนใจในวิชาที่เรียน)</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="special_ability" value="ไม่มี" required> ไม่มี</label>
                <label><input type="radio" name="special_ability" value="มี"> มี</label>
            </div>
            <div id="specialAbilityFields" class="hidden mt-2 space-y-4">
                <?php
                $subjects = [
                    'คณิตศาสตร์', 'ภาษาไทย', 'ภาษาต่างประเทศ', 'วิทยาศาสตร์',
                    'ศิลปะ', 'การงานอาชีพและเทคโนโลยี', 'สุขศึกษา และพลศึกษา', 'สังคมศึกษา ศาสนา และวัฒนธรรม'
                ];
                foreach ($subjects as $i => $subject): ?>
                <div>
                    <label>
                        <input type="checkbox" class="subject-checkbox" data-subject="<?= $i ?>"> 
                        <span class="font-semibold"><?= $subject ?></span>
                    </label>
                    <div class="flex flex-col gap-2 mt-1 subject-inputs hidden" data-subject="<?= $i ?>">
                        <input type="text" name="special_<?= $i ?>[]" class="input input-bordered w-full" placeholder="รายละเอียด">
                        <input type="text" name="special_<?= $i ?>[]" class="input input-bordered w-full" placeholder="รายละเอียด">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="step hidden" data-step="2">
            <h3 class="font-bold mb-2">2. ด้านการเรียน</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="study_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="study_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="study_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="studyRiskFields" class="hidden mt-2">
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
                foreach ($study_risk as $i => $item): ?>
                <label class="block"><input type="checkbox" name="study_risk[]" value="<?= $item ?>"> <?= $item ?></label>
                <?php endforeach; ?>
            </div>
            <div id="studyProblemFields" class="hidden mt-2">
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
                foreach ($study_problem as $i => $item): ?>
                <label class="block"><input type="checkbox" name="study_problem[]" value="<?= $item ?>"> <?= $item ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        
        <!-- Step 3 -->
        <div class="step hidden" data-step="3">
            <h3 class="font-bold mb-2">3. ด้านสุขภาพ</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="health_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="health_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="health_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="healthRiskFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
                <label class="block"><input type="checkbox" name="health_risk[]" value="ร่างกายไม่แข็งแรง"> ร่างกายไม่แข็งแรง</label>
                <label class="block"><input type="checkbox" name="health_risk[]" value="มีโรคประจำตัวหรือเจ็บป่วยบ่อย"> มีโรคประจำตัวหรือเจ็บป่วยบ่อย</label>
                <label class="block"><input type="checkbox" name="health_risk[]" value="มีปัญหาด้านสายตา (สวมแว่น/คอนแท็คเลนส์)"> มีปัญหาด้านสายตา (สวมแว่น/คอนแท็คเลนส์)</label>
            </div>
            <div id="healthProblemFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
                <label class="block"><input type="checkbox" name="health_problem[]" value="มีภาวะทุพโภชนาการ"> มีภาวะทุพโภชนาการ</label>
                <label class="block"><input type="checkbox" name="health_problem[]" value="มีความพิการทางร่างกาย"> มีความพิการทางร่างกาย</label>
                <label class="block"><input type="checkbox" name="health_problem[]" value="ป่วยเป็นโรคร้ายแรง/เรื้อรัง"> ป่วยเป็นโรคร้ายแรง/เรื้อรัง</label>
                <label class="block"><input type="checkbox" name="health_problem[]" value="มีปัญหาด้านสายตา (ไม่สวมแว่น/คอนแท็คเลนส์)"> มีปัญหาด้านสายตา (ไม่สวมแว่น/คอนแท็คเลนส์)</label>
                <label class="block"><input type="checkbox" name="health_problem[]" value="มีความบกพร่องทางการได้ยิน"> มีความบกพร่องทางการได้ยิน</label>
                <label class="block"><input type="checkbox" name="health_problem[]" value="สมรรถภาพทางร่างกายต่ำ"> สมรรถภาพทางร่างกายต่ำ</label>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="step hidden" data-step="4">
            <h3 class="font-bold mb-2">4. ด้านเศรษฐกิจ</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="economic_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="economic_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="economic_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="economicRiskFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
                <label class="block"><input type="checkbox" name="economic_risk[]" value="รายได้ครอบครัว 5,000-10,000 บาท ต่อเดือน"> รายได้ครอบครัว 5,000-10,000 บาท ต่อเดือน</label>
                <label class="block"><input type="checkbox" name="economic_risk[]" value="บิดาหรือมารดาตกงาน (1 คน) แต่รายได้มากกว่า 5,000 บาท"> บิดาหรือมารดาตกงาน (1 คน) แต่รายได้มากกว่า 5,000 บาท</label>
            </div>
            <div id="economicProblemFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
                <label class="block"><input type="checkbox" name="economic_problem[]" value="รายได้ครอบครัวต่ำกว่า 5,000 บาทต่อเดือน"> รายได้ครอบครัวต่ำกว่า 5,000 บาทต่อเดือน</label>
                <label class="block"><input type="checkbox" name="economic_problem[]" value="บิดาและมารดาตกงาน(ทั้ง 2 คน)"> บิดาและมารดาตกงาน(ทั้ง 2 คน)</label>
                <label class="block"><input type="checkbox" name="economic_problem[]" value="ครอบครัวมีภาระหนี้สินจำนวนมาก"> ครอบครัวมีภาระหนี้สินจำนวนมาก</label>
                <label class="block"><input type="checkbox" name="economic_problem[]" value="รายได้ไม่เพียงพอต่อการใช้จ่ายในชีวิตประจำวัน"> รายได้ไม่เพียงพอต่อการใช้จ่ายในชีวิตประจำวัน</label>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="step hidden" data-step="5">
            <h3 class="font-bold mb-2">5. ด้านสวัสดิภาพและความปลอดภัย</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="welfare_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="welfare_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="welfare_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="welfareRiskFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
                <label class="block"><input type="checkbox" name="welfare_risk[]" value="พ่อแม่แยกทางกัน หรือแต่งงานใหม่"> พ่อแม่แยกทางกัน หรือแต่งงานใหม่</label>
                <label class="block"><input type="checkbox" name="welfare_risk[]" value="ที่พักอาศัยอยู่ในชุมชนแออัด หรือใกล้แหล่งมั่วสุม / สถานเริงรมย์"> ที่พักอาศัยอยู่ในชุมชนแออัด หรือใกล้แหล่งมั่วสุม / สถานเริงรมย์</label>
                <label class="block"><input type="checkbox" name="welfare_risk[]" value="อยู่หอพัก"> อยู่หอพัก</label>
                <label class="block"><input type="checkbox" name="welfare_risk[]" value="มีบุคคลเจ็บป่วยด้วยโรคร้ายแรง/เรื้อรัง"> มีบุคคลเจ็บป่วยด้วยโรคร้ายแรง/เรื้อรัง</label>
                <label class="block"><input type="checkbox" name="welfare_risk[]" value="บุคคลในครอบครัวติดสารเสพติด"> บุคคลในครอบครัวติดสารเสพติด</label>
                <label class="block"><input type="checkbox" name="welfare_risk[]" value="บุคคลในครอบครัวเล่นการพนัน"> บุคคลในครอบครัวเล่นการพนัน</label>
                <label class="block"><input type="checkbox" name="welfare_risk[]" value="มีความขัดแย้ง / ทะเลาะกันในครอบครัว"> มีความขัดแย้ง / ทะเลาะกันในครอบครัว</label>
            </div>
            <div id="welfareProblemFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
                <label class="block"><input type="checkbox" name="welfare_problem[]" value="ไม่มีผู้ดูแล"> ไม่มีผู้ดูแล</label>
                <label class="block"><input type="checkbox" name="welfare_problem[]" value="มีความขัดแย้งและมีการใช้ความรุนแรงในครอบครัว"> มีความขัดแย้งและมีการใช้ความรุนแรงในครอบครัว</label>
                <label class="block"><input type="checkbox" name="welfare_problem[]" value="ถูกทารุณ / ทำร้ายจากบุคคลในครอบครัว"> ถูกทารุณ / ทำร้ายจากบุคคลในครอบครัว</label>
                <label class="block"><input type="checkbox" name="welfare_problem[]" value="ถูกล่วงละเมิดทางเพศ"> ถูกล่วงละเมิดทางเพศ</label>
                <label class="block"><input type="checkbox" name="welfare_problem[]" value="สูบบุหรี่ / กัญชา / ของมึนเมา"> สูบบุหรี่ / กัญชา / ของมึนเมา</label>
                <label class="block"><input type="checkbox" name="welfare_problem[]" value="เล่นการพนัน"> เล่นการพนัน</label>
            </div>
        </div>

        <!-- Step 6 -->
        <div class="step hidden" data-step="6">
            <h3 class="font-bold mb-2">6. ด้านพฤติกรรมการใช้สารเสพติด</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="drug_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="drug_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="drug_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="drugRiskFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
                <label class="block"><input type="checkbox" name="drug_risk[]" value="คบเพื่อนในกลุ่มใช้สารเสพติด เช่น บุหรี่ , สุรา"> คบเพื่อนในกลุ่มใช้สารเสพติด เช่น บุหรี่ , สุรา</label>
                <label class="block"><input type="checkbox" name="drug_risk[]" value="สมาชิกในครอบครัวข้องเกี่ยวกับยาเสพติด"> สมาชิกในครอบครัวข้องเกี่ยวกับยาเสพติด</label>
                <label class="block"><input type="checkbox" name="drug_risk[]" value="เคยลองสูบบุหรี่ / กัญชา /ของมึนเมา"> เคยลองสูบบุหรี่ / กัญชา /ของมึนเมา</label>
                <label class="block"><input type="checkbox" name="drug_risk[]" value="อยู่ในสภาพแวดล้อมที่ใช้สารเสพติด"> อยู่ในสภาพแวดล้อมที่ใช้สารเสพติด</label>
            </div>
            <div id="drugProblemFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
                <label class="block"><input type="checkbox" name="drug_problem[]" value="ใช้หรือเสพเองมากกว่า 2 ครั้ง"> ใช้หรือเสพเองมากกว่า 2 ครั้ง</label>
                <label class="block"><input type="checkbox" name="drug_problem[]" value="มีประวัติเกี่ยวข้องกับสารเสพติด"> มีประวัติเกี่ยวข้องกับสารเสพติด</label>
                <label class="block"><input type="checkbox" name="drug_problem[]" value="เป็นผู้ติดบุหรี่ สุรา หรือสารเสพติดอื่นๆ"> เป็นผู้ติดบุหรี่ สุรา หรือสารเสพติดอื่นๆ</label>
            </div>
        </div>

        <!-- Step 7 -->
        <div class="step hidden" data-step="7">
            <h3 class="font-bold mb-2">7. ด้านพฤติกรรมการใช้ความรุนแรง</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="violence_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="violence_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="violence_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="violenceRiskFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
                <label class="block"><input type="checkbox" name="violence_risk[]" value="ไม่ปฏิบัติตามกฎจารจร"> ไม่ปฏิบัติตามกฎจารจร</label>
                <label class="block"><input type="checkbox" name="violence_risk[]" value="พาหนะและสภาพการเดินทางไม่ปลอดภัย"> พาหนะและสภาพการเดินทางไม่ปลอดภัย</label>
                <label class="block"><input type="checkbox" name="violence_risk[]" value="มีประวัติทะเลาะวิวาท"> มีประวัติทะเลาะวิวาท</label>
                <label class="block"><input type="checkbox" name="violence_risk[]" value="ก้าวร้าว เกเร"> ก้าวร้าว เกเร</label>
            </div>
            <div id="violenceProblemFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
                <label class="block"><input type="checkbox" name="violence_problem[]" value="ไม่ปฏิบัติตามกฎจารจรบ่อยๆ หรือเป็นประจำ"> ไม่ปฏิบัติตามกฎจารจรบ่อยๆ หรือเป็นประจำ</label>
                <label class="block"><input type="checkbox" name="violence_problem[]" value="ทะเลาะวิวาทบ่อยๆ"> ทะเลาะวิวาทบ่อยๆ</label>
                <label class="block"><input type="checkbox" name="violence_problem[]" value="ทำร้ายร่างกายผู้อื่น"> ทำร้ายร่างกายผู้อื่น</label>
            </div>
        </div>

        <!-- Step 8 -->
        <div class="step hidden" data-step="8">
            <h3 class="font-bold mb-2">8. ด้านพฤติกรรมทางเพศ</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="sex_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="sex_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="sex_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="sexRiskFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
                <label class="block"><input type="checkbox" name="sex_risk[]" value="อยู่ในกลุ่มประพฤติตนเหมือนเพศตรงข้าม"> อยู่ในกลุ่มประพฤติตนเหมือนเพศตรงข้าม</label>
                <label class="block"><input type="checkbox" name="sex_risk[]" value="ทำงานพิเศษที่ล่อแหลมต่อการถูกล่วงละเมิดทางเพศ"> ทำงานพิเศษที่ล่อแหลมต่อการถูกล่วงละเมิดทางเพศ</label>
                <label class="block"><input type="checkbox" name="sex_risk[]" value="จับคู่ชัดเจนและแยกกลุ่มอยู่ด้วยกันสองต่อสองบ่อยครั้ง"> จับคู่ชัดเจนและแยกกลุ่มอยู่ด้วยกันสองต่อสองบ่อยครั้ง</label>
                <label class="block"><input type="checkbox" name="sex_risk[]" value="อยู่ในกลุ่มขายบริการ"> อยู่ในกลุ่มขายบริการ</label>
                <label class="block"><input type="checkbox" name="sex_risk[]" value="ใช้เครื่องมือสื่อสารเป็นเวลานานและบ่อยครั้ง"> ใช้เครื่องมือสื่อสารเป็นเวลานานและบ่อยครั้ง</label>
            </div>
            <div id="sexProblemFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
                <label class="block"><input type="checkbox" name="sex_problem[]" value="ประพฤติตนเหมือนเพศตรงข้าม"> ประพฤติตนเหมือนเพศตรงข้าม</label>
                <label class="block"><input type="checkbox" name="sex_problem[]" value="ขาดเรียนไปกับคู่ของตนเสมอๆ"> ขาดเรียนไปกับคู่ของตนเสมอๆ</label>
                <label class="block"><input type="checkbox" name="sex_problem[]" value="อยู่ด้วยกัน"> อยู่ด้วยกัน</label>
                <label class="block"><input type="checkbox" name="sex_problem[]" value="ตั้งครรภ์"> ตั้งครรภ์</label>
                <label class="block"><input type="checkbox" name="sex_problem[]" value="ขายบริการทางเพศ"> ขายบริการทางเพศ</label>
                <label class="block"><input type="checkbox" name="sex_problem[]" value="มีการมั่วสุมทางเพศ"> มีการมั่วสุมทางเพศ</label>
                <label class="block"><input type="checkbox" name="sex_problem[]" value="หมกมุ่นในการใช้เครื่องมือสื่อสารที่เกี่ยวข้องทางเพศ"> หมกมุ่นในการใช้เครื่องมือสื่อสารที่เกี่ยวข้องทางเพศ</label>
            </div>
        </div>

        <!-- Step 9 -->
        <div class="step hidden" data-step="9">
            <h3 class="font-bold mb-2">9. ด้านการติดเกม</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="game_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="game_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="game_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="gameRiskFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
                <label class="block"><input type="checkbox" name="game_risk[]" value="เล่นเกมเกินวันละ 1 ชั่วโมง"> เล่นเกมเกินวันละ 1 ชั่วโมง</label>
                <label class="block"><input type="checkbox" name="game_risk[]" value="ขาดจินตนาการและความคิดสร้างสรรค์"> ขาดจินตนาการและความคิดสร้างสรรค์</label>
                <label class="block"><input type="checkbox" name="game_risk[]" value="เก็บตัว แยกตัวจากกลุ่มเพื่อน"> เก็บตัว แยกตัวจากกลุ่มเพื่อน</label>
                <label class="block"><input type="checkbox" name="game_risk[]" value="ใช้จ่ายเงินผิดปกติ"> ใช้จ่ายเงินผิดปกติ</label>
                <label class="block"><input type="checkbox" name="game_risk[]" value="อยู่ในกลุ่มเพื่อนเล่นเกม"> อยู่ในกลุ่มเพื่อนเล่นเกม</label>
                <label class="block"><input type="checkbox" name="game_risk[]" value="ร้านเกมอยู่ใกล้บ้านหรือโรงเรียน"> ร้านเกมอยู่ใกล้บ้านหรือโรงเรียน</label>
            </div>
            <div id="gameProblemFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
                <label class="block"><input type="checkbox" name="game_problem[]" value="ใช้เวลาเล่นเกมเกิน 2 ชั่วโมง"> ใช้เวลาเล่นเกมเกิน 2 ชั่วโมง</label>
                <label class="block"><input type="checkbox" name="game_problem[]" value="หงุดหงิด ฉุนเฉียว อารมณ์รุนแรง"> หงุดหงิด ฉุนเฉียว อารมณ์รุนแรง</label>
                <label class="block"><input type="checkbox" name="game_problem[]" value="บุคลิกภาพผิดไปจากเดิม"> บุคลิกภาพผิดไปจากเดิม</label>
                <label class="block"><input type="checkbox" name="game_problem[]" value="ขาดความรับผิดชอบ"> ขาดความรับผิดชอบ</label>
                <label class="block"><input type="checkbox" name="game_problem[]" value="หมกมุ่น จริงจังในการเล่นเกม"> หมกมุ่น จริงจังในการเล่นเกม</label>
                <label class="block"><input type="checkbox" name="game_problem[]" value="ใช้เงินสิ้นเปลือง โกหก ลักขโมยเงินเพื่อเล่นเกม"> ใช้เงินสิ้นเปลือง โกหก ลักขโมยเงินเพื่อเล่นเกม</label>
            </div>
        </div>

        <!-- Step 10 -->
        <div class="step hidden" data-step="10">
            <h3 class="font-bold mb-2">10. นักเรียนที่มีความต้องการพิเศษ</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="special_need_status" value="ไม่มี" required> ไม่มี</label>
                <label><input type="radio" name="special_need_status" value="มี"> มี</label>
            </div>
            <div id="specialNeedFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เลือกได้ 1 ข้อ):</div>
                <label class="block"><input type="radio" name="special_need_type" value="มีความบกพร่องทางการเห็น"> มีความบกพร่องทางการเห็น</label>
                <label class="block"><input type="radio" name="special_need_type" value="มีความบกพร่องทางการได้ยิน"> มีความบกพร่องทางการได้ยิน</label>
                <label class="block"><input type="radio" name="special_need_type" value="มีความบกพร่องทางสติปัญญา"> มีความบกพร่องทางสติปัญญา</label>
                <label class="block"><input type="radio" name="special_need_type" value="มีความบกพร่องทางร่างกายและสุขภาพ"> มีความบกพร่องทางร่างกายและสุขภาพ</label>
                <label class="block"><input type="radio" name="special_need_type" value="มีความบกพร่องทางการเรียนรู้"> มีความบกพร่องทางการเรียนรู้</label>
                <label class="block"><input type="radio" name="special_need_type" value="มีความบกพร่องทางพฤติกรรมหรืออารมณ์"> มีความบกพร่องทางพฤติกรรมหรืออารมณ์</label>
                <label class="block"><input type="radio" name="special_need_type" value="มีความบกพร่องทางการพูดและภาษา"> มีความบกพร่องทางการพูดและภาษา</label>
                <label class="block"><input type="radio" name="special_need_type" value="ออทิสติก"> ออทิสติก</label>
                <label class="block"><input type="radio" name="special_need_type" value="มีสมาธิสั้น"> มีสมาธิสั้น</label>
                <label class="block"><input type="radio" name="special_need_type" value="พิการซ้ำซ้อน (มีความบกพร่องตั้งแต่ 2 ประเภทขึ้นไป)"> พิการซ้ำซ้อน (มีความบกพร่องตั้งแต่ 2 ประเภทขึ้นไป)</label>
            </div>
        </div>

        <!-- Step 11 -->
        <div class="step hidden" data-step="11">
            <h3 class="font-bold mb-2">11. ด้านการใช้เครื่องมือสื่อสารอิเล็กทรอนิกส์</h3>
            <div class="mb-2">
                <label class="mr-4"><input type="radio" name="it_status" value="ปกติ" required>ปกติ</label>
                <label class="mr-4"><input type="radio" name="it_status" value="เสี่ยง"> เสี่ยง</label>
                <label><input type="radio" name="it_status" value="มีปัญหา"> มีปัญหา</label>
            </div>
            <div id="itRiskFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (เสี่ยง):</div>
                <label class="block"><input type="checkbox" name="it_risk[]" value="เคยใช้โทรศัพท์มือถือในระหว่างการเรียนการสอนโดยไม่จำเป็น"> เคยใช้โทรศัพท์มือถือในระหว่างการเรียนการสอนโดยไม่จำเป็น</label>
                <label class="block"><input type="checkbox" name="it_risk[]" value="เข้าใช้ MSN, Facebook ,Twitter หรือ chat เกินวันละ 1 ชั่วโมง"> เข้าใช้ MSN, Facebook ,Twitter หรือ chat เกินวันละ 1 ชั่วโมง</label>
            </div>
            <div id="itProblemFields" class="hidden mt-2">
                <div class="font-semibold mb-1">เลือกข้อที่เกี่ยวข้อง (มีปัญหา):</div>
                <label class="block"><input type="checkbox" name="it_problem[]" value="ใช้โทรศัพท์มือถือในระหว่างการเรียนการสอน 2-3 ครั้ง/วัน"> ใช้โทรศัพท์มือถือในระหว่างการเรียนการสอน 2-3 ครั้ง/วัน</label>
                <label class="block"><input type="checkbox" name="it_problem[]" value="เข้าใช้ MSN, Facebook, Twitter หรือ chat เกินวันละ 2 ชั่วโมง"> เข้าใช้ MSN, Facebook, Twitter หรือ chat เกินวันละ 2 ชั่วโมง</label>
            </div>
        </div>

    </div>

    <!-- Submit Button -->
    <div class="flex justify-end mt-6">
        <!-- เปลี่ยน id ปุ่มบันทึก -->
        <button type="submit" id="saveScreen11Btn" class="bg-green-600 text-white px-6 py-2 rounded hidden">บันทึกข้อมูล</button>
    </div>

    <!-- เพิ่ม input hidden สำหรับรายละเอียดความสามารถพิเศษ -->
    <input type="hidden" name="special_ability_detail" id="special_ability_detail">
</form>

<script>
// เปลี่ยน id ใน JS ให้ตรงกับฟอร์มและปุ่มใหม่
const steps = document.querySelectorAll('.step');
let currentStep = 0;
const prevBtn = document.getElementById('prevStep');
const nextBtn = document.getElementById('nextStep');
const stepIndicator = document.getElementById('stepIndicator');
const saveScreen = document.getElementById('saveScreen11Btn'); // เปลี่ยนตรงนี้

function showStep(idx) {
    steps.forEach((step, i) => step.classList.toggle('hidden', i !== idx));
    stepIndicator.textContent = `ข้อ ${idx+1}/11`;
    prevBtn.disabled = idx === 0;
    nextBtn.classList.toggle('hidden', idx === steps.length-1);
    saveScreen.classList.toggle('hidden', idx !== steps.length-1);
}
prevBtn.onclick = () => { if(currentStep>0){ currentStep--; showStep(currentStep); } };
nextBtn.onclick = () => {
    // ตรวจสอบว่ามีการเลือก radio ใน step ปัจจุบันหรือยัง
    const currentStepDiv = steps[currentStep];
    const radios = currentStepDiv.querySelectorAll('input[type="radio"][name]');
    let checked = false;
    if (radios.length > 0) {
        const names = Array.from(radios).map(r => r.name);
        const uniqueNames = [...new Set(names)];
        // กรณี step 10 (currentStep === 9) ต้องเลือก special_need_status เสมอ
        if (currentStep === 9) {
            const specialNeedRadio = currentStepDiv.querySelector('input[name="special_need_status"]:checked');
            checked = !!specialNeedRadio;
        } else {
            checked = uniqueNames.every(name => {
                return currentStepDiv.querySelector(`input[type="radio"][name="${name}"]:checked`);
            });
        }
    } else {
        checked = true;
    }
    if (!checked) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกตัวเลือก',
            text: 'โปรดเลือกตัวเลือกก่อนดำเนินการถัดไป',
            confirmButtonText: 'ตกลง'
        });
        return;
    }

    // ตรวจสอบกรณีที่เลือก "เสี่ยง" หรือ "มีปัญหา" หรือ "มี" แล้วต้องกรอก/ติ๊กข้อย่อย
    let requireSub = false;
    let subChecked = true;
    // Step 1: "มี" ต้องติ๊กวิชาอย่างน้อย 1 วิชา และแต่ละวิชาที่ติ๊ก ต้องกรอก input อย่างน้อย 1 ช่อง
    if (currentStep === 0) {
        const specialAbility = currentStepDiv.querySelector('input[name="special_ability"]:checked');
        if (specialAbility && specialAbility.value === 'มี') {
            requireSub = true;
            // ต้องติ๊กวิชาอย่างน้อย 1 วิชา
            const checkedSubjects = currentStepDiv.querySelectorAll('.subject-checkbox:checked');
            if (checkedSubjects.length === 0) {
                subChecked = false;
            } else {
                // แต่ละวิชาที่ติ๊ก ต้องมี input อย่างน้อย 1 ช่องที่ไม่ว่าง
                subChecked = Array.from(checkedSubjects).every(cb => {
                    const subjectInputs = currentStepDiv.querySelector('.subject-inputs[data-subject="' + cb.dataset.subject + '"]');
                    const inputs = subjectInputs.querySelectorAll('input[type="text"]');
                    return Array.from(inputs).some(input => input.value.trim() !== '');
                });
            }
        }
    }
    // Step 2-4, 5-9, 11: "เสี่ยง" หรือ "มีปัญหา" ต้องติ๊ก checkbox อย่างน้อย 1
    // Step 10: "มี" ต้องเลือก radio อย่างน้อย 1, "ไม่มี" ผ่านได้เลย
    if ([1,2,3,4,5,6,7,8,10].includes(currentStep)) {
        let statusRadio = currentStepDiv.querySelector('input[name="radio"]:checked');
        if (statusRadio) {
            if (statusRadio.value === 'เสี่ยง') {
                requireSub = true;
                let subBox = currentStepDiv.querySelectorAll('div[id*="RiskFields"] input[type="checkbox"]');
                subChecked = Array.from(subBox).some(cb => cb.checked);
            }
            if (statusRadio.value === 'มีปัญหา') {
                requireSub = true;
                let subBox = currentStepDiv.querySelectorAll('div[id*="ProblemFields"] input[type="checkbox"]');
                subChecked = Array.from(subBox).some(cb => cb.checked);
            }
            // สำหรับ step 10 (currentStep === 9)
            if (currentStep === 9) {
                if (statusRadio.value === 'มี') {
                    requireSub = true;
                    let subRadio = currentStepDiv.querySelectorAll('#specialNeedFields input[type="radio"]');
                    subChecked = Array.from(subRadio).some(r => r.checked);
                } else if (statusRadio.value === 'ไม่มี') {
                    requireSub = false;
                    subChecked = true;
                }
            }
        } else if (currentStep === 9) {
            // ไม่ได้เลือกอะไรเลยใน step 10
            requireSub = false;
            subChecked = false;
        }
    }

    if (requireSub && !subChecked) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกหรือติ๊กข้อย่อย',
            text: 'โปรดกรอกหรือติ๊กข้อย่อยอย่างน้อย 1 ข้อ',
            confirmButtonText: 'ตกลง'
        });
        return;
    }

    if(currentStep<steps.length-1){
        currentStep++;
        showStep(currentStep);
    }
};

showStep(currentStep);

// Step 1: Show/hide subject fields
document.querySelectorAll('input[name="special_ability"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('specialAbilityFields').classList.toggle('hidden', e.target.value !== 'มี');
    });
});
// Step 1: Show/hide subject input fields by checkbox
document.querySelectorAll('.subject-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const subjectInputs = document.querySelector('.subject-inputs[data-subject="' + this.dataset.subject + '"]');
        if (this.checked) {
            subjectInputs.classList.remove('hidden');
        } else {
            subjectInputs.classList.add('hidden');
            // ล้างค่า input เมื่อยกเลิกติ๊ก
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
document.getElementById('screen11Form').addEventListener('submit', function(e) {
    // รายละเอียดความสามารถพิเศษ
    const detail = collectSpecialAbilityDetail();
    document.getElementById('special_ability_detail').value = Object.keys(detail).length > 0 ? JSON.stringify(detail) : '';

    // ...สามารถเพิ่ม logic สำหรับ field อื่นๆ ที่ต้อง serialize array เป็น JSON ได้ที่นี่...
});

</script>
