<?php
require_once "../../config/Database.php";
require_once "../../class/Student.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Student class
$student = new Student($db);

// Get student ID from query parameter
$stu_id = isset($_GET['stu_id']) ? $_GET['stu_id'] : '';

if (!empty($stu_id)) {
    $studentData = $student->getStudentById($stu_id);
    if ($studentData) {
        $data = $studentData[0];
        $studentname = $data['Stu_pre'] . $data['Stu_name'] . " " . $data['Stu_sur'];
        ?>
        <form id="editStudentForm" class="space-y-6 ">
            <input type="hidden" name="Stu_id" value="<?= $data['Stu_id'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="Stu_pre" class="block text-sm font-medium text-gray-700">📛 คำนำหน้า</label>
                    <input type="text" name="Stu_pre" value="<?= $data['Stu_pre'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z\s]+" required>
                </div>
                <div>
                    <label for="Stu_name" class="block text-sm font-medium text-gray-700">👤 ชื่อ</label>
                    <input type="text" name="Stu_name" value="<?= $data['Stu_name'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z\s]+" required>
                </div>
                <div>
                    <label for="Stu_sur" class="block text-sm font-medium text-gray-700">👤 นามสกุล</label>
                    <input type="text" name="Stu_sur" value="<?= $data['Stu_sur'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z\s]+" required>
                </div>
                <div>
                    <label for="Stu_no" class="block text-sm font-medium text-gray-700">🔢 เลขที่</label>
                    <input type="number" name="Stu_no" value="<?= $data['Stu_no'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        min="1" max="45" required>
                </div>
                <div>
                    <label for="Stu_password" class="block text-sm font-medium text-gray-700">🔑 รหัสผ่าน</label>
                    <input type="password" name="Stu_password" value="<?= $data['Stu_password'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                </div>
                <div>
                    <label for="Stu_sex" class="block text-sm font-medium text-gray-700">⚧️ เพศ</label>
                    <select name="Stu_sex" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                        <option value="1" <?= $data['Stu_sex'] == 1 ? 'selected' : '' ?>>ชาย</option>
                        <option value="2" <?= $data['Stu_sex'] == 2 ? 'selected' : '' ?>>หญิง</option>
                    </select>
                </div>
                <div>
                    <label for="Stu_major" class="block text-sm font-medium text-gray-700">🏫 ชั้น</label>
                    <input type="text" name="Stu_major" value="<?= $data['Stu_major'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z0-9\s]+" required>
                </div>
                <div>
                    <label for="Stu_room" class="block text-sm font-medium text-gray-700">🏫 ห้อง</label>
                    <input type="text" name="Stu_room" value="<?= $data['Stu_room'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z0-9\s]+" required>
                </div>
                <div>
                    <label for="Stu_nick" class="block text-sm font-medium text-gray-700">👶 ชื่อเล่น</label>
                    <input type="text" name="Stu_nick" value="<?= $data['Stu_nick'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z\s]+" required>
                </div>
                <div>
                    <label for="Stu_birth" class="block text-sm font-medium text-gray-700">🎂 วันเดือนปีเกิด</label>
                    <input type="date" name="Stu_birth" value="<?= $data['Stu_birth'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                </div>
                <div>
                    <label for="Stu_religion" class="block text-sm font-medium text-gray-700">🛐 ศาสนา</label>
                    <input type="text" name="Stu_religion" value="<?= $data['Stu_religion'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z\s]+" required>
                </div>
                <div>
                    <label for="Stu_blood" class="block text-sm font-medium text-gray-700">🩸 กรุ๊ปเลือด</label>
                    <select name="Stu_blood" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                        <option value="" <?= $data['Stu_blood'] == 'null' ? 'selected' : '' ?>>ไม่แสดงกรุ๊ปเลือด</option>
                        <option value="A" <?= $data['Stu_blood'] == 'A' ? 'selected' : '' ?>>A</option>
                        <option value="B" <?= $data['Stu_blood'] == 'B' ? 'selected' : '' ?>>B</option>
                        <option value="AB" <?= $data['Stu_blood'] == 'AB' ? 'selected' : '' ?>>AB</option>
                        <option value="O" <?= $data['Stu_blood'] == 'O' ? 'selected' : '' ?>>O</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="Stu_addr" class="block text-sm font-medium text-gray-700">🏠 ที่อยู่</label>
                    <input type="text" name="Stu_addr" value="<?= $data['Stu_addr'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                </div>
                <div>
                    <label for="Stu_phone" class="block text-sm font-medium text-gray-700">📞 เบอร์โทรศัพท์</label>
                    <input type="tel" name="Stu_phone" value="<?= $data['Stu_phone'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="\d{10}" maxlength="10" required>
                </div>
                <div>
                    <label for="Stu_citizenid" class="block text-sm font-medium text-gray-700">🆔 เลขบัตรประชาชน</label>
                    <input type="text" name="Stu_citizenid" value="<?= $data['Stu_citizenid'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="\d{13}" maxlength="13" required>
                </div>
                <div>
                    <label for="Father_name" class="block text-sm font-medium text-gray-700">👨‍👦 ชื่อบิดา</label>
                    <input type="text" name="Father_name" value="<?= $data['Father_name'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z\s]+" required>
                </div>
                <div>
                    <label for="Father_occu" class="block text-sm font-medium text-gray-700">💼 อาชีพบิดา</label>
                    <input type="text" name="Father_occu" value="<?= $data['Father_occu'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                </div>
                <div>
                    <label for="Father_income" class="block text-sm font-medium text-gray-700">💰 รายได้บิดา</label>
                    <input type="number" name="Father_income" value="<?= $data['Father_income'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        min="0" required>
                </div>
                <div>
                    <label for="Mother_name" class="block text-sm font-medium text-gray-700">👩‍👦 ชื่อมารดา</label>
                    <input type="text" name="Mother_name" value="<?= $data['Mother_name'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z\s]+" required>
                </div>
                <div>
                    <label for="Mother_occu" class="block text-sm font-medium text-gray-700">💼 อาชีพมารดา</label>
                    <input type="text" name="Mother_occu" value="<?= $data['Mother_occu'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                </div>
                <div>
                    <label for="Mother_income" class="block text-sm font-medium text-gray-700">💰 รายได้มารดา</label>
                    <input type="number" name="Mother_income" value="<?= $data['Mother_income'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        min="0" required>
                </div>
                <div>
                    <label for="Par_name" class="block text-sm font-medium text-gray-700">👨‍👩‍👧 ชื่อผู้ปกครอง</label>
                    <input type="text" name="Par_name" value="<?= $data['Par_name'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="[ก-๙a-zA-Z\s]+" required>
                </div>
                <div>
                    <label for="Par_relate" class="block text-sm font-medium text-gray-700">🤝 ความสัมพันธ์</label>
                    <input type="text" name="Par_relate" value="<?= $data['Par_relate'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                </div>
                <div>
                    <label for="Par_occu" class="block text-sm font-medium text-gray-700">💼 อาชีพผู้ปกครอง</label>
                    <input type="text" name="Par_occu" value="<?= $data['Par_occu'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                </div>
                <div>
                    <label for="Par_income" class="block text-sm font-medium text-gray-700">💰 รายได้ผู้ปกครอง</label>
                    <input type="number" name="Par_income" value="<?= $data['Par_income'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        min="0" required>
                </div>
                <div>
                    <label for="Par_addr" class="block text-sm font-medium text-gray-700">🏠 ที่อยู่ผู้ปกครอง</label>
                    <input type="text" name="Par_addr" value="<?= $data['Par_addr'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                </div>
                <div>
                    <label for="Par_phone" class="block text-sm font-medium text-gray-700">📞 เบอร์โทรผู้ปกครอง</label>
                    <input type="tel" name="Par_phone" value="<?= $data['Par_phone'] ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        pattern="\d{10}" maxlength="10" required>
                </div>
                <div>
                    <label for="Stu_status" class="block text-sm font-medium text-gray-700">📜 สถานะ</label>
                    <select name="Stu_status" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base" 
                        required>
                        <option value="1" <?= $data['Stu_status'] == 1 ? 'selected' : '' ?>>ปกติ</option>
                        <option value="2" <?= $data['Stu_status'] == 2 ? 'selected' : '' ?>>จบการศึกษา</option>
                        <option value="3" <?= $data['Stu_status'] == 3 ? 'selected' : '' ?>>ย้ายโรงเรียน</option>
                        <option value="4" <?= $data['Stu_status'] == 4 ? 'selected' : '' ?>>ออกกลางคัน</option>
                        <option value="9" <?= $data['Stu_status'] == 9 ? 'selected' : '' ?>>เสียชีวิต</option>
                    </select>
                </div>
            </div>
             
        </form>
        <?php
    } else {
        echo "<p class='text-red-500'>🚨 ไม่พบข้อมูลนักเรียน</p>";
    }
} else {
    echo "<p class='text-yellow-500'>⚠️ รหัสนักเรียนไม่ถูกต้อง</p>";
}
?>