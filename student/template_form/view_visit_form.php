<?php
include_once("../../config/Database.php");
include_once("../../class/StudentVisit.php");

$stuId = $_GET['stuId'] ?? '';
$term = $_GET['term'] ?? '';
$pee = $_GET['pee'] ?? '';

$db = (new Database("phichaia_student"))->getConnection();
$visit = new StudentVisit($db);
$data = $visit->getVisitData($stuId, $term, $pee);

if (!$data) {
    echo '<div class="text-center text-red-500">ไม่พบข้อมูลการเยี่ยมบ้าน</div>';
    exit;
}
?>
<div class="flex flex-col items-center">
    <div class="w-full">
        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 rounded-lg text-center">
            <h5 class="text-xl font-bold">..:: ข้อมูลการเยี่ยมบ้านนักเรียน ::..</h5>
            <hr class="my-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><h6 class="text-base font-medium">🆔 เลขประจำตัวนักเรียน: <?= htmlspecialchars($data['Stu_id']) ?></h6></div>
                <div><h6 class="text-base font-medium">👤 ชื่อ-สกุล: <?= htmlspecialchars($data['Stu_pre'] . $data['Stu_name'] . " " . $data['Stu_sur']) ?></h6></div>
                <div><h6 class="text-base font-medium">🏫 ชั้น: <?= htmlspecialchars($data['Stu_major'] . "/" . $data['Stu_room']) ?></h6></div>
                <div><h6 class="text-base font-medium">🏠 ที่อยู่: <?= htmlspecialchars($data['Stu_addr']) ?></h6></div>
                <div><h6 class="text-base font-medium">📞 เบอร์โทรศัพท์: <?= htmlspecialchars($data['Stu_phone']) ?></h6></div>
            </div>
        </div>
    </div>
    <div class="w-full mt-6">
        <div class="bg-gray-100 border-l-4 border-gray-400 p-6 rounded-lg">
            <?php
            for ($i = 1; $i <= 18; $i++) {
                $q = $visit->getAllAnswersForQuestion($i);
                $question = $visit->getQuestionAnswer($i, $data["vh$i"]);
                echo '<div class="mb-4">';
                echo '<h5 class="text-base font-bold mb-2">' . htmlspecialchars($question['question']) . '</h5>';
                echo '<div class="flex flex-wrap gap-4">';
                foreach ($q as $idx => $ans) {
                    $checked = ($data["vh$i"] == ($idx + 1)) ? 'checked' : '';
                    echo '<div class="flex items-center space-x-2">';
                    echo '<input type="radio" disabled ' . $checked . '>';
                    echo '<label class="text-base">' . htmlspecialchars($ans) . '</label>';
                    echo '</div>';
                }
                echo '</div>';
                echo '</div>';
            }
            ?>
            <div class="mb-4">
                <h5 class="text-base font-bold mb-2">20. ปัญหา/อุปสรรค และความต้องการความช่วยเหลือ</h5>
                <textarea readonly cols="30" rows="5" class="w-full p-2 border border-gray-300 rounded-lg"><?= htmlspecialchars($data['vh20']) ?></textarea>
            </div>
        </div>
    </div>
</div>
