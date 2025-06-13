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
            <select name="Stu_sex" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" required>
                <option value="1" <?= $student['Stu_sex'] == 1 ? 'selected' : '' ?>>ชาย</option>
                <option value="2" <?= $student['Stu_sex'] == 2 ? 'selected' : '' ?>>หญิง</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📛 คำนำหน้า</label>
            <select name="Stu_pre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
                <option value="เด็กชาย" <?= $student['Stu_pre'] == 'เด็กชาย' ? 'selected' : '' ?>>เด็กชาย</option>
                <option value="เด็กหญิง" <?= $student['Stu_pre'] == 'เด็กหญิง' ? 'selected' : '' ?>>เด็กหญิง</option>
                <option value="นาย" <?= $student['Stu_pre'] == 'นาย' ? 'selected' : '' ?>>นาย</option>
                <option value="นางสาว" <?= $student['Stu_pre'] == 'นางสาว' ? 'selected' : '' ?>>นางสาว</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👤 ชื่อ</label>
            <input type="text" name="Stu_name" value="<?= htmlspecialchars($student['Stu_name']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👤 นามสกุล</label>
            <input type="text" name="Stu_sur" value="<?= htmlspecialchars($student['Stu_sur']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🏫 ชั้น</label>
            <input type="number" name="Stu_major" value="<?= htmlspecialchars($student['Stu_major']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                min="1" max="6" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🏫 ห้อง</label>
            <input type="number" name="Stu_room" value="<?= htmlspecialchars($student['Stu_room']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                min="1" max="12" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👶 ชื่อเล่น</label>
            <input type="text" name="Stu_nick" value="<?= htmlspecialchars($student['Stu_nick']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🎂 วันเกิด</label>
            <input type="date" name="Stu_birth" value="<?= htmlspecialchars($student['Stu_birth']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🛐 ศาสนา</label>
            <input type="text" name="Stu_religion" value="<?= htmlspecialchars($student['Stu_religion']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🩸 กรุ๊ปเลือด</label>
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
            <input type="text" name="Stu_addr" value="<?= htmlspecialchars($student['Stu_addr']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📞 เบอร์โทร</label>
            <input type="tel" name="Stu_phone" value="<?= htmlspecialchars($student['Stu_phone']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                pattern="\d{10,15}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👨‍👦 ชื่อบิดา</label>
            <input type="text" name="Father_name" value="<?= htmlspecialchars($student['Father_name']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💼 อาชีพบิดา</label>
            <input type="text" name="Father_occu" value="<?= htmlspecialchars($student['Father_occu']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💰 รายได้บิดา</label>
            <input type="number" name="Father_income" value="<?= htmlspecialchars($student['Father_income']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👩‍👦 ชื่อมารดา</label>
            <input type="text" name="Mother_name" value="<?= htmlspecialchars($student['Mother_name']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💼 อาชีพมารดา</label>
            <input type="text" name="Mother_occu" value="<?= htmlspecialchars($student['Mother_occu']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💰 รายได้มารดา</label>
            <input type="number" name="Mother_income" value="<?= htmlspecialchars($student['Mother_income']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">👨‍👩‍👧 ชื่อผู้ปกครอง</label>
            <input type="text" name="Par_name" value="<?= htmlspecialchars($student['Par_name']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🤝 ความสัมพันธ์</label>
            <input type="text" name="Par_relate" value="<?= htmlspecialchars($student['Par_relate']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💼 อาชีพผู้ปกครอง</label>
            <input type="text" name="Par_occu" value="<?= htmlspecialchars($student['Par_occu']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">💰 รายได้ผู้ปกครอง</label>
            <input type="number" name="Par_income" value="<?= htmlspecialchars($student['Par_income']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">🏠 ที่อยู่ผู้ปกครอง</label>
            <input type="text" name="Par_addr" value="<?= htmlspecialchars($student['Par_addr']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">📱 เบอร์ผู้ปกครอง</label>
            <input type="tel" name="Par_phone" value="<?= htmlspecialchars($student['Par_phone']) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base"
                pattern="\d{10,15}">
        </div>
        
    </div>
    <div class="mt-4 text-gray-500 text-xs">
        <span>✏️ กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนกดบันทึก</span>
    </div>
</form>
