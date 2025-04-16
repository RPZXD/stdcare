<?php
$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['student_name'] ?? '';
$student_no = $_GET['student_no'] ?? '';
$student_class = $_GET['student_class'] ?? '';
$student_room = $_GET['student_room'] ?? '';
$pee = $_GET['pee'] ?? '';

// รายการคำถาม SDQ 25 ข้อ (ตัวอย่างจริง ใช้ทั้งหมด)
// รูปแบบ: [id, คำถาม, หมวด]
$questions = [
    ['sdq1', 'มักจะปวดหัว ปวดท้อง หรือไม่สบาย', 'อารมณ์ 😖'],
    ['sdq2', 'มักจะช่วยเด็กคนอื่น เช่น เด็กที่บาดเจ็บ เสียใจ หรือรู้สึกไม่สบาย', 'จุดแข็ง 🤝'],
    ['sdq3', 'มักอยู่ไม่นิ่ง กระสับกระส่าย', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['sdq4', 'มักมีอารมณ์หงุดหงิดง่าย โกรธง่าย', 'เกเร 😠'],
    ['sdq5', 'มีเพื่อนน้อยหรือไม่มีเลย', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['sdq6', 'มักจะกังวลมาก', 'อารมณ์ 😖'],
    ['sdq7', 'มักจะมีน้ำใจและแบ่งปันสิ่งของให้ผู้อื่น', 'จุดแข็ง 🤝'],
    ['sdq8', 'มักจะวอกแวกง่าย', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['sdq9', 'มักจะทะเลาะกับเด็กคนอื่นหรือชอบรังแก', 'เกเร 😠'],
    ['sdq10', 'มักจะเป็นที่ชื่นชอบในหมู่เพื่อน', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['sdq11', 'มักจะรู้สึกกลัวหรือวิตกกังวล', 'อารมณ์ 😖'],
    ['sdq12', 'มักจะช่วยเหลือเด็กที่อ่อนแอกว่า', 'จุดแข็ง 🤝'],
    ['sdq13', 'มักจะทำสิ่งต่าง ๆ โดยไม่คิด', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['sdq14', 'มักจะโกหกหรือหลอกลวง', 'เกเร 😠'],
    ['sdq15', 'มักจะเข้ากับผู้ใหญ่ได้ดี', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['sdq16', 'มักจะรู้สึกเศร้าหรือไม่มีความสุข', 'อารมณ์ 😖'],
    ['sdq17', 'มักจะมีความเห็นอกเห็นใจต่อความรู้สึกของผู้อื่น', 'จุดแข็ง 🤝'],
    ['sdq18', 'มักจะมีสมาธิสั้น', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['sdq19', 'มักจะถูกกล่าวหาว่าขโมยของ', 'เกเร 😠'],
    ['sdq20', 'มักจะมีเพื่อนสนิท', 'เพื่อน 🧍‍♂️🧍‍♀️'],
    ['sdq21', 'มักจะกลัวสิ่งใหม่ ๆ หรือสถานการณ์ใหม่ ๆ', 'อารมณ์ 😖'],
    ['sdq22', 'มักจะมีน้ำใจต่อสัตว์', 'จุดแข็ง 🤝'],
    ['sdq23', 'มักจะทำสิ่งต่าง ๆ อย่างหุนหันพลันแล่น', 'สมาธิ/ไฮเปอร์ ⚡'],
    ['sdq24', 'มักจะมีพฤติกรรมที่ทำให้เกิดปัญหา', 'เกเร 😠'],
    ['sdq25', 'มักจะเข้ากับเพื่อนในกลุ่มได้ดี', 'เพื่อน 🧍‍♂️🧍‍♀️'],
];

// ตัวเลือกคำตอบ
$choices = [
    '0' => '❌ ไม่จริงเลย',
    '1' => '😐 จริงบางส่วน',
    '2' => '✅ จริงแน่นอน',
];
?>

<form id="sdqForm" class="space-y-6">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">

    <div class="bg-blue-100 text-blue-800 px-4 py-3 rounded-md">
        📋 <strong>คำชี้แจง:</strong> กรุณาเลือกคำตอบที่ตรงกับตัวคุณในช่วง 6 เดือนที่ผ่านมา
    </div>

    <?php foreach ($questions as $index => [$id, $text, $category]): ?>
        <div class="p-4 bg-white border rounded-lg shadow-sm hover:shadow-md transition">
            <p class="mb-2 font-semibold text-gray-800">
                <?= ($index + 1) ?>. <?= htmlspecialchars($text) ?> <span class="text-sm text-gray-500">[<?= $category ?>]</span>
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <?php foreach ($choices as $value => $label): ?>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border rounded-md cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="<?= $id ?>" value="<?= $value ?>" required class="form-radio text-blue-600">
                        <span><?= $label ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="p-4 bg-white border rounded-lg shadow-sm hover:shadow-md transition">
        <p class="mb-2 font-semibold text-gray-800">
            เธอมีอย่างอื่นที่จะบอกอีกหรือไม่? <span class="text-sm text-gray-500">[เพิ่มเติม]</span>
        </p>
        <textarea name="memo" rows="4" class="w-full border rounded-md p-2" placeholder="กรุณาเขียนข้อความเพิ่มเติมที่นี่..."></textarea>
    </div>
</form>
