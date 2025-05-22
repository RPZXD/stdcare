<?php
require_once '../../class/SDQ.php';
require_once '../../config/Database.php'; // Assuming this file initializes $db

$student_id = $_GET['student_id'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';


// Initialize SDQ class
// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);

// Fetch existing data
$existingData = $sdq->getSDQSelfData($student_id, $pee, $term);
$answers = $existingData['answers'] ?? [];
$memo = $existingData['memo'] ?? '';

// ...existing code defining $questions and $choices...
$questions = [
    ['q1', 'พยายามจะทำตัวดีกับคนอื่น ใส่ใจความรู้สึกคนอื่น', 'จุดแข็ง 🤝'],
    ['q2', 'ไม่อยู่นิ่ง นั่งนานๆ ไม่ได้', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['q3', 'ปวดศรีษะ ปวดท้อง หรือไม่สบายบ่อยๆ', 'อารมณ์ 😖'],
    ['q4', 'เต็มใจแบ่งปันสิ่งของให้คนอื่น (ของกิน เกม ปากกา เป็นต้น)', 'จุดแข็ง 🤝'],
    ['q5', 'โกรธแรง และมักอารมณ์เสีย', 'เกเร 😠'],
    ['q6', 'ชอบอยู่กับตัวเอง ชอบเล่นคนเดียวอยู่ตามลำพัง', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q7', 'มักทำตามที่คนอื่นบอก', 'จุดแข็ง 🤝'],
    ['q8', 'ขี้กังวล', 'อารมณ์ 😖'],
    ['q9', 'ใครๆ ก็พึ่งได้ถ้าเขาเสียใจ อารมณ์ไม่ดีหรือไม่สบายใจ', 'จุดแข็ง 🤝'],
    ['q10', 'อยู่ไม่สุข วุ่นวาย', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['q11', 'มีเพื่อนสนิท', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q12', 'มีเรื่องทะเลาะวิวาทบ่อย ทำให้คนอื่น อย่างที่ต้องการได้', 'เกเร 😠'],
    ['q13', 'ไม่มีความสุข ท้อแท้ร้องไห้บ่อยๆ', 'อารมณ์ 😖'],
    ['q14', 'เพื่อนๆ ส่วนมากชอบ', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q15', 'วอกแวกง่าย รู้สึกว่าไม่มีสมาธิ', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['q16', 'กังวลเวลาอยู่ในสถานการณ์ที่ไม่คุ้นเคยและเสียความเชื่อมั่นในตนเองง่าย', 'อารมณ์ 😖'],
    ['q17', 'ใจดีกับเด็กที่เล็กกว่า', 'จุดแข็ง 🤝'],
    ['q18', 'มีคนว่าโกหก หรือขี้โกงบ่อยๆ', 'เกเร 😠'],
    ['q19', 'เด็กๆ คนอื่นล้อเลียนหรือรังแก', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q20', 'มักจะอาสาช่วยเหลือคนอื่น (พ่อ แม่ ครู เด็กคนอื่น)', 'จุดแข็ง 🤝'],
    ['q21', 'คิดก่อนทำ', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['q22', 'เอาของคนอื่นในบ้าน ที่โรงเรียนหรือที่อื่น', 'เกเร 😠'],
    ['q23', 'เข้ากับผู้ใหญ่ได้ดีกว่าเด็กวัยเดียวกัน', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['q24', 'ขี้กลัว รู้สึกหวาดกลัวได้ง่าย', 'อารมณ์ 😖'],
    ['q25', 'ทำงานได้จนเสร็จ ความตั้งใจในการทำงานของดี', 'จุดแข็ง 🤝'],
];

// ตัวเลือกคำตอบ
$choices = [
    '0' => '❌ ไม่จริงเลย',
    '1' => '😐 จริงบางส่วน',
    '2' => '✅ จริงแน่นอน',
];
?>

<form id="sdqEditForm" class="space-y-6">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">

    <div class="bg-emerald-500 border rounded-lg shadow-sm p-4 mb-4">
        <h2 class="text-lg font-semibold text-white">🎓 แก้ไขข้อมูลนักเรียน</h2>
        <p class="text-white">ชื่อ: <?= htmlspecialchars($_GET['student_name'] ?? '') ?>  เลขที่: <?= htmlspecialchars($_GET['student_no'] ?? '') ?>   ชั้น: ม.<?= htmlspecialchars($_GET['student_class'] ?? '') ?>/<?= htmlspecialchars($_GET['student_room'] ?? '') ?></p>
        <p class="text-white">บันทึกข้อมูลของ ภาคเรียนที่ <?= htmlspecialchars($term) ?> ปีการศึกษา <?= htmlspecialchars($pee) ?></p>
    </div>

    <div class="bg-blue-100 text-blue-800 px-4 py-3 rounded-md">
        📋 <strong>คำชี้แจง:</strong> กรุณาแก้ไขคำตอบที่ตรงกับตัวคุณในช่วง 6 เดือนที่ผ่านมา
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
            เธอมีอย่างอื่นที่จะบอกอีกหรือไม่? <span class="text-sm text-gray-500">[เพิ่มเติม]</span>
        </p>
        <textarea name="memo" rows="4" class="w-full border rounded-md p-2" placeholder="กรุณาเขียนข้อความเพิ่มเติมที่นี่..."><?= htmlspecialchars($memo) ?></textarea>
    </div>
    <input type="text" name="pee" value="<?= htmlspecialchars($pee) ?>" class="hidden">
    <input type="text" name="term" value="<?= htmlspecialchars($term) ?>" class="hidden">
    <input type="text" name="student_id" value="<?= htmlspecialchars($student_id) ?>" class="hidden">
</form>
