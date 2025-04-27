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

        echo "<div class='p-6 '>";
        echo "<div class='flex flex-col items-center'>";
        echo "<img class='rounded-lg shadow-md mb-4' src='https://std.phichai.ac.th/photo/" . $data['Stu_picture'] . "' alt='Student Picture' style='max-height:300px;max-width:280px;'>";
        echo "<h1 class='text-2xl font-bold text-gray-800 mb-2'>👨‍🎓 ข้อมูลนักเรียน</h1>";
        echo "</div>";

        echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-4'>";
        echo "<p>📛 <strong>ชื่อ-สกุล:</strong> " . $studentname . "</p>";
        echo "<p>🆔 <strong>เลขประจำตัวนักเรียน:</strong> " . $data['Stu_id'] . "</p>";
        echo "<p>🏫 <strong>ชั้น:</strong> " . $data['Stu_major'] . "/" . $data['Stu_room'] . " <strong>เลขที่:</strong> " . $data['Stu_no'] . "</p>";
        echo "<p>📞 <strong>เบอร์โทรศัพท์:</strong> " . $data['Stu_phone'] . "</p>";
        echo "<p>📞 <strong>เบอร์โทรผู้ปกครอง:</strong> " . $data['Par_phone'] . "</p>";
        echo "<p>🆔 <strong>เลขบัตรประชาชน:</strong> " . $data['Stu_citizenid'] . "</p>";
        echo "</div>";

        echo "<hr class='my-4'>";

        echo "<h2 class='text-lg font-bold text-gray-800 mb-2'>📋 ข้อมูลเพิ่มเติม</h2>";
        echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-4'>";
        echo "<p>🔑 <strong>รหัสผ่าน:</strong> " . $data['Stu_password'] . "</p>";
        echo "<p>⚧️ <strong>เพศ:</strong> " . ($data['Stu_sex'] == 1 ? 'ชาย' : 'หญิง') . "</p>";
        echo "<p>👶 <strong>ชื่อเล่น:</strong> " . $data['Stu_nick'] . "</p>";
        echo "<p>🎂 <strong>วันเดือนปีเกิด:</strong> " . $data['Stu_birth'] . "</p>";
        echo "<p>🛐 <strong>ศาสนา:</strong> " . $data['Stu_religion'] . "</p>";
        echo "<p>🩸 <strong>กรุ๊ปเลือด:</strong> " . $data['Stu_blood'] . "</p>";
        echo "<p>🏠 <strong>ที่อยู่:</strong> " . $data['Stu_addr'] . "</p>";
        echo "</div>";

        echo "<hr class='my-4'>";

        echo "<h2 class='text-lg font-bold text-gray-800 mb-2'>👨‍👩‍👧‍👦 ข้อมูลผู้ปกครอง</h2>";
        echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-4'>";
        echo "<p>👨‍👦 <strong>ชื่อบิดา:</strong> " . $data['Father_name'] . "</p>";
        echo "<p>💼 <strong>อาชีพบิดา:</strong> " . $data['Father_occu'] . "</p>";
        echo "<p>💰 <strong>รายได้บิดา:</strong> " . $data['Father_income'] . "</p>";
        echo "<p>👩‍👦 <strong>ชื่อมารดา:</strong> " . $data['Mother_name'] . "</p>";
        echo "<p>💼 <strong>อาชีพมารดา:</strong> " . $data['Mother_occu'] . "</p>";
        echo "<p>💰 <strong>รายได้มารดา:</strong> " . $data['Mother_income'] . "</p>";
        echo "<p>👨‍👩‍👧 <strong>ชื่อผู้ปกครอง:</strong> " . $data['Par_name'] . "</p>";
        echo "<p>🤝 <strong>ความสัมพันธ์:</strong> " . $data['Par_relate'] . "</p>";
        echo "<p>💼 <strong>อาชีพผู้ปกครอง:</strong> " . $data['Par_occu'] . "</p>";
        echo "<p>💰 <strong>รายได้ผู้ปกครอง:</strong> " . $data['Par_income'] . "</p>";
        echo "<p>🏠 <strong>ที่อยู่ผู้ปกครอง:</strong> " . $data['Par_addr'] . "</p>";
        echo "<p>📞 <strong>เบอร์โทรผู้ปกครอง:</strong> " . $data['Par_phone'] . "</p>";
        echo "</div>";

        echo "<hr class='my-4'>";

        echo "<h2 class='text-lg font-bold text-gray-800 mb-2'>📜 สถานะนักเรียน</h2>";
        echo "<p>📌 <strong>สถานะ:</strong> " . strstatus($data['Stu_status']) . "</p>";
        echo "</div>";
    } else {
        echo "<div class='p-6 bg-red-100 text-red-800 rounded-lg shadow-md'>🚨 <strong>ไม่พบข้อมูลนักเรียน</strong></div>";
    }
} else {
    echo "<div class='p-6 bg-yellow-100 text-yellow-800 rounded-lg shadow-md'>⚠️ <strong>รหัสนักเรียนไม่ถูกต้อง</strong></div>";
}

function strstatus($str) {
    switch ($str) {
        case "1":
            return 'ปกติ';
        case "2":
            return 'จบการศึกษา';
        case "3":
            return 'ย้ายโรงเรียน';
        case "4":
            return 'ออกกลางคัน';
        case "9":
            return 'เสียชีวิต';
        default:
            return 'ไม่ทราบสถานะ';
    }
}
?>