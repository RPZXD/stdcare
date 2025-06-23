<?php
session_start();
if (!isset($_SESSION['Student_login'])) {
    exit('กรุณาเข้าสู่ระบบ');
}
include_once("../config/Database.php");
$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();

$student_id = $_SESSION['Student_login'];
$stmt = $studentConn->prepare("SELECT * FROM student WHERE Stu_id = :id LIMIT 1");
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    exit('<p class="text-red-500">🚨 ไม่พบข้อมูลนักเรียน</p>');
}
?>
<form id="editStudentForm" class="space-y-6">
    <input type="hidden" name="Stu_id" value="<?= htmlspecialchars($student['Stu_id']) ?>">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">⚧️ เพศ</label>
            <span class="text-xs text-gray-500">เลือกเพศของนักเรียน</span>
            <select name="Stu_sex" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" required>
                <option value="1" <?= $student['Stu_sex'] == 1 ? 'selected' : '' ?>>ชาย</option>
                <option value="2" <?= $student['Stu_sex'] == 2 ? 'selected' : '' ?>>หญิง</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📛 คำนำหน้า</label>
            <span class="text-xs text-gray-500">เลือกคำนำหน้าชื่อที่ตรงกับนักเรียน</span>
            <select name="Stu_pre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
                <option value="เด็กชาย" <?= $student['Stu_pre'] == 'เด็กชาย' ? 'selected' : '' ?>>เด็กชาย</option>
                <option value="เด็กหญิง" <?= $student['Stu_pre'] == 'เด็กหญิง' ? 'selected' : '' ?>>เด็กหญิง</option>
                <option value="นาย" <?= $student['Stu_pre'] == 'นาย' ? 'selected' : '' ?>>นาย</option>
                <option value="นางสาว" <?= $student['Stu_pre'] == 'นางสาว' ? 'selected' : '' ?>>นางสาว</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👤 ชื่อ</label>
            <span class="text-xs text-gray-500">กรอกชื่อจริงของนักเรียน <br>ตัวอย่าง: สมชาย</span>
            <input type="text" name="Stu_name" value="<?= htmlspecialchars($student['Stu_name']) ?>"
                placeholder="กรอกชื่อจริงของนักเรียน"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👤 นามสกุล</label>
            <span class="text-xs text-gray-500">กรอกนามสกุลของนักเรียน <br>ตัวอย่าง: ใจดี</span>
            <input type="text" name="Stu_sur" value="<?= htmlspecialchars($student['Stu_sur']) ?>"
                placeholder="กรอกนามสกุลของนักเรียน"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🏫 ชั้น</label>
            <span class="text-xs text-gray-500">ชั้นปีที่เรียน (ไม่สามารถแก้ไขได้)</span>
            <input type="number" name="Stu_major" value="<?= htmlspecialchars($student['Stu_major']) ?>"
                placeholder="ชั้นปีที่เรียน (เช่น 1, 2, 3...)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                min="1" max="6" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🏫 ห้อง</label>
            <span class="text-xs text-gray-500">ห้องเรียน (ไม่สามารถแก้ไขได้)</span>
            <input type="number" name="Stu_room" value="<?= htmlspecialchars($student['Stu_room']) ?>"
                placeholder="ห้องเรียน (เช่น 1, 2, 3...)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                min="1" max="12" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👶 ชื่อเล่น</label>
            <span class="text-xs text-gray-500">กรอกชื่อเล่นของนักเรียน (ถ้ามี) <br>ตัวอย่าง: ต้น</span>
            <input type="text" name="Stu_nick" value="<?= htmlspecialchars($student['Stu_nick']) ?>"
                placeholder="กรอกชื่อเล่นของนักเรียน"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🎂 วันเกิด</label>
            <span class="text-xs text-gray-500">เลือกวันเดือนปีเกิดของนักเรียน <br>ตัวอย่าง: 2008-05-21</span>
            <input type="date" name="Stu_birth" value="<?= htmlspecialchars($student['Stu_birth']) ?>"
                placeholder="เลือกวันเกิดของนักเรียน"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🛐 ศาสนา</label>
            <span class="text-xs text-gray-500">กรอกศาสนาของนักเรียน (เช่น พุทธ, คริสต์, อิสลาม)</span>
            <input type="text" name="Stu_religion" value="<?= htmlspecialchars($student['Stu_religion']) ?>"
                placeholder="กรอกศาสนาของนักเรียน"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🩸 กรุ๊ปเลือด</label>
            <span class="text-xs text-gray-500">เลือกกรุ๊ปเลือดของนักเรียน (ถ้าทราบ) <br>ตัวอย่าง: A, B, AB, O</span>
            <select name="Stu_blood" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
                <option value="">ไม่ระบุ</option>
                <option value="A" <?= $student['Stu_blood'] == 'A' ? 'selected' : '' ?>>A</option>
                <option value="B" <?= $student['Stu_blood'] == 'B' ? 'selected' : '' ?>>B</option>
                <option value="AB" <?= $student['Stu_blood'] == 'AB' ? 'selected' : '' ?>>AB</option>
                <option value="O" <?= $student['Stu_blood'] == 'O' ? 'selected' : '' ?>>O</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🏠 ที่อยู่</label>
            <span class="text-xs text-gray-500">กรอกที่อยู่ปัจจุบันของนักเรียน <br>ตัวอย่าง: 123 หมู่ 4 ต.ท่ามะเขือ อ.คลองขลุง จ.กำแพงเพชร</span>
            <input type="text" name="Stu_addr" value="<?= htmlspecialchars($student['Stu_addr']) ?>"
                placeholder="กรอกที่อยู่ปัจจุบันของนักเรียน"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📞 เบอร์โทร</label>
            <span class="text-xs text-gray-500">กรอกเบอร์โทรศัพท์ที่ติดต่อได้ <br>ตัวอย่าง: 0812345678 <br>ห้ามกรอกตัวอักษรหรือสัญลักษณ์อื่น</span>
            <input type="tel" name="Stu_phone" value="<?= htmlspecialchars($student['Stu_phone']) ?>"
                placeholder="กรอกเบอร์โทรศัพท์นักเรียน (เช่น 0812345678)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                pattern="\d{10,15}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👨‍👦 ชื่อบิดา</label>
            <span class="text-xs text-gray-500">กรอกชื่อบิดาของนักเรียน <br>ตัวอย่าง: สมชาย ใจดี</span>
            <input type="text" name="Father_name" value="<?= htmlspecialchars($student['Father_name']) ?>"
                placeholder="กรอกชื่อบิดา"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💼 อาชีพบิดา</label>
            <span class="text-xs text-gray-500">กรอกอาชีพบิดาของนักเรียน <br>ตัวอย่าง: รับจ้าง, ข้าราชการ</span>
            <input type="text" name="Father_occu" value="<?= htmlspecialchars($student['Father_occu']) ?>"
                placeholder="กรอกอาชีพบิดา"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💰 รายได้บิดา</label>
            <span class="text-xs text-gray-500">กรอกรายได้บิดาต่อเดือน (บาท) <br>ห้ามกรอกตัวอักษรหรือสัญลักษณ์อื่น</span>
            <input type="number" name="Father_income" value="<?= htmlspecialchars($student['Father_income']) ?>"
                placeholder="กรอกรายได้บิดาต่อเดือน (บาท)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👩‍👦 ชื่อมารดา</label>
            <span class="text-xs text-gray-500">กรอกชื่อมารดาของนักเรียน <br>ตัวอย่าง: สมหญิง ใจดี</span>
            <input type="text" name="Mother_name" value="<?= htmlspecialchars($student['Mother_name']) ?>"
                placeholder="กรอกชื่อมารดา"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💼 อาชีพมารดา</label>
            <span class="text-xs text-gray-500">กรอกอาชีพมารดาของนักเรียน <br>ตัวอย่าง: ค้าขาย, รับจ้าง</span>
            <input type="text" name="Mother_occu" value="<?= htmlspecialchars($student['Mother_occu']) ?>"
                placeholder="กรอกอาชีพมารดา"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💰 รายได้มารดา</label>
            <span class="text-xs text-gray-500">กรอกรายได้มารดาต่อเดือน (บาท) <br>ห้ามกรอกตัวอักษรหรือสัญลักษณ์อื่น</span>
            <input type="number" name="Mother_income" value="<?= htmlspecialchars($student['Mother_income']) ?>"
                placeholder="กรอกรายได้มารดาต่อเดือน (บาท)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👨‍👩‍👧 ชื่อผู้ปกครอง</label>
            <span class="text-xs text-gray-500">กรอกชื่อผู้ปกครอง (ถ้าไม่ใช่บิดาหรือมารดา) <br>ตัวอย่าง: สมปอง ใจดี</span>
            <input type="text" name="Par_name" value="<?= htmlspecialchars($student['Par_name']) ?>"
                placeholder="กรอกชื่อผู้ปกครอง"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🤝 ความสัมพันธ์</label>
            <span class="text-xs text-gray-500">ระบุความสัมพันธ์กับนักเรียน (เช่น พ่อ, แม่, ญาติ) <br>ห้ามกรอกตัวเลข</span>
            <input type="text" name="Par_relate" value="<?= htmlspecialchars($student['Par_relate']) ?>"
                placeholder="ระบุความสัมพันธ์กับนักเรียน (เช่น พ่อ, แม่, ญาติ)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💼 อาชีพผู้ปกครอง</label>
            <span class="text-xs text-gray-500">กรอกอาชีพของผู้ปกครอง <br>ตัวอย่าง: รับจ้าง, ค้าขาย</span>
            <input type="text" name="Par_occu" value="<?= htmlspecialchars($student['Par_occu']) ?>"
                placeholder="กรอกอาชีพผู้ปกครอง"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💰 รายได้ผู้ปกครอง</label>
            <span class="text-xs text-gray-500">กรอกรายได้ผู้ปกครองต่อเดือน (บาท) <br>ห้ามกรอกตัวอักษรหรือสัญลักษณ์อื่น</span>
            <input type="number" name="Par_income" value="<?= htmlspecialchars($student['Par_income']) ?>"
                placeholder="กรอกรายได้ผู้ปกครองต่อเดือน (บาท)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🏠 ที่อยู่ผู้ปกครอง</label>
            <span class="text-xs text-gray-500">กรอกที่อยู่ของผู้ปกครอง <br>ตัวอย่าง: 123 หมู่ 4 ต.ท่ามะเขือ อ.คลองขลุง จ.กำแพงเพชร</span>
            <input type="text" name="Par_addr" value="<?= htmlspecialchars($student['Par_addr']) ?>"
                placeholder="กรอกที่อยู่ของผู้ปกครอง"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📱 เบอร์ผู้ปกครอง</label>
            <span class="text-xs text-gray-500">กรอกเบอร์โทรศัพท์ของผู้ปกครอง <br>ตัวอย่าง: 0891234567 <br>ห้ามกรอกตัวอักษรหรือสัญลักษณ์อื่น</span>
            <input type="tel" name="Par_phone" value="<?= htmlspecialchars($student['Par_phone']) ?>"
                placeholder="กรอกเบอร์โทรศัพท์ผู้ปกครอง (เช่น 0812345678)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                pattern="\d{10,15}">
        </div>
        
    </div>
    <div class="mt-4 text-gray-500 text-xs">
        <span>✏️ กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนกดบันทึก</span>
    </div>
</form>
