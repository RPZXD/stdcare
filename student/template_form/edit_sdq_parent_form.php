<?php
require_once("../../config/Database.php");
require_once("../../class/SDQ.php");
$stuId = $_GET['stuId'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';
$db = (new Database("phichaia_student"))->getConnection();
$sdq = new SDQ($db);
$data = $sdq->getSDQParData($stuId, $pee, $term);
$answers = $data['answers'] ?? [];
$memo = $data['memo'] ?? '';

// กำหนดคำถามและหมวดหมู่
$questions = [
    ['q1', 'ฉันพยายามจะทำตัวดีกับคนอื่น ฉันใส่ใจความรู้สึกคนอื่น', 'จุดแข็ง 🤝'],
    ['q2', 'ฉันไม่อยู่นิ่ง ฉันนั่งนานๆ ไม่ได้', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['q3', 'ฉันปวดศรีษะ ปวดท้อง หรือไม่สบายบ่อยๆ', 'อารมณ์ 😖'],
    ['q4', 'ฉันเต็มใจแบ่งปันสิ่งของให้คนอื่น (ของกิน เกม ปากกา เป็นต้น)', 'จุดแข็ง 🤝'],
    ['q5', 'ฉันโกรธแรง และมักอารมณ์เสีย', 'เกเร 😠'],
    ['q6', 'ฉันชอบอยู่กับตัวเอง ฉันชอบเล่นคนเดียวอยู่ตามลำพัง', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q7', 'ฉันมักทำตามที่คนอื่นบอก', 'จุดแข็ง 🤝'],
    ['q8', 'ฉันขี้กังวล', 'อารมณ์ 😖'],
    ['q9', 'ใครๆ ก็พึ่งฉันได้ถ้าเขาเสียใจ อารมณ์ไม่ดีหรือไม่สบายใจ', 'จุดแข็ง 🤝'],
    ['q10', 'ฉันอยู่ไม่สุข วุ่นวาย', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['q11', 'ฉันมีเพื่อนสนิท', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q12', 'ฉันมีเรื่องทะเลาะวิวาทบ่อย ฉันทำให้คนอื่น อย่างที่ฉันต้องการได้', 'เกเร 😠'],
    ['q13', 'ฉันไม่มีความสุข ท้อแท้ร้องไห้บ่อยๆ', 'อารมณ์ 😖'],
    ['q14', 'เพื่อนๆ ส่วนมากชอบฉัน', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q15', 'ฉันวอกแวกง่าย ฉันรู้สึกว่าไม่มีสมาธิ', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['q16', 'ฉันกังวลเวลาอยู่ในสถานการณ์ที่ไม่คุ้นเคยและเสียความเชื่อมั่นในตนเองง่าย', 'อารมณ์ 😖'],
    ['q17', 'ฉันใจดีกับเด็กที่เล็กกว่า', 'จุดแข็ง 🤝'],
    ['q18', 'มีคนว่าฉันโกหก หรือขี้โกงบ่อยๆ', 'เกเร 😠'],
    ['q19', 'เด็กๆ คนอื่นล้อเลียนหรือรังแกฉัน', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q20', 'ฉันมักจะอาสาช่วยเหลือคนอื่น (พ่อ แม่ ครู เด็กคนอื่น)', 'จุดแข็ง 🤝'],
    ['q21', 'ฉันคิดก่อนทำ', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['q22', 'ฉันเอาของคนอื่นในบ้าน ที่โรงเรียนหรือที่อื่น', 'เกเร 😠'],
    ['q23', 'ฉันเข้ากับผู้ใหญ่ได้ดีกว่าเด็กวัยเดียวกัน', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q24', 'ฉันขี้กลัว รู้สึกหวาดกลัวได้ง่าย', 'อารมณ์ 😖'],
    ['q25', 'ฉันทำงานได้จนเสร็จ ความตั้งใจในการทำงานของฉันดี', 'จุดแข็ง 🤝'],
];

// ตัวเลือกคำตอบ
$choices = [
    '0' => '❌ ไม่จริงเลย',
    '1' => '😐 จริงบางส่วน',
    '2' => '✅ จริงแน่นอน',
];
?>
<form id="sdqForm" class="space-y-6">
    <input type="hidden" name="type" value="parent">
    <input type="hidden" name="stuId" value="<?= htmlspecialchars($stuId) ?>">
    <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">
    <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">

    <div class="bg-blue-100 text-blue-800 px-4 py-3 rounded-md mb-4">
        📋 <strong>คำชี้แจง:</strong> กรุณาแก้ไขคำตอบที่ตรงกับนักเรียนในช่วง 6 เดือนที่ผ่านมา
    </div>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-blue-500 text-white text-center">
                <th class="border border-gray-300 px-4 py-2">ข้อ</th>
                <th class="border border-gray-300 px-4 py-2">รายการประเมิน</th>
                <th class="border border-gray-300 px-4 py-2">คำตอบ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questions as $index => [$id, $text, $category]): ?>
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2 text-center"><?= ($index + 1) ?></td>
                    <td class="border border-gray-300 px-4 py-2">
                        <?= htmlspecialchars($text) ?> <span class="text-sm text-gray-500">[<?= $category ?>]</span>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <?php foreach ($choices as $value => $label): ?>
                                <label class="inline-flex items-center gap-2 px-3 py-2 border rounded-md cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="<?= $id ?>" value="<?= $value ?>" <?= isset($answers[$id]) && $answers[$id] == $value ? 'checked' : '' ?> required class="form-radio text-blue-600">
                                    <span><?= $label ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="p-4 bg-white border rounded-lg shadow-sm hover:shadow-md transition">
        <p class="mb-2 font-semibold text-gray-800">
            ท่านมีอย่างอื่นที่จะบอกอีกหรือไม่? <span class="text-sm text-gray-500">[เพิ่มเติม]</span>
        </p>
        <textarea name="memo" rows="4" class="w-full border rounded-md p-2" placeholder="กรุณาเขียนข้อความเพิ่มเติมที่นี่..."><?= htmlspecialchars($memo) ?></textarea>
    </div>
</form>
