<?php
require_once '../../class/SDQ.php';
require_once '../../config/Database.php';

$student_id = $_GET['student_id'] ?? '';
$student_name = $_GET['student_name'] ?? '';
$student_no = $_GET['student_no'] ?? '';
$student_class = $_GET['student_class'] ?? '';
$student_room = $_GET['student_room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

// DB & SDQ
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);

$existingData = $sdq->getSDQSelfData($student_id, $pee, $term);
$answers = $existingData['answers'] ?? [];
$memo = $existingData['memo'] ?? '';
$impact = $existingData['impact'] ?? [];

$categories = [
    'อารมณ์ 😖' => [3, 8, 13, 16, 24],
    'เกเร 😠' => [5, 12, 18, 22],
    'สมาธิ/ไฮเปอร์ ⚡' => [2, 10, 15, 21],
    'เพื่อน 🧍‍♂️🧍‍♀️' => [6, 11, 14, 19, 23],
    'จุดแข็ง 🤝' => [1, 4, 7, 9, 17, 20, 25],
];

$categoryScores = [];
foreach ($categories as $label => $questions) {
    $score = 0;
    foreach ($questions as $qnum) {
        $score += (int)($answers["q$qnum"] ?? 0);
    }
    $categoryScores[$label] = $score;
}

$totalProblemScore = $categoryScores['อารมณ์ 😖'] + $categoryScores['เกเร 😠'] + $categoryScores['สมาธิ/ไฮเปอร์ ⚡'] + $categoryScores['เพื่อน 🧍‍♂️🧍‍♀️'];

function scoreLevel($score, $category) {
    // กำหนดเกณฑ์จาก SDQ จริง
    $cutoffs = [
        'อารมณ์ 😖' => [4, 6],
        'เกเร 😠' => [3, 5],
        'สมาธิ/ไฮเปอร์ ⚡' => [5, 7],
        'เพื่อน 🧍‍♂️🧍‍♀️' => [3, 6],
        'จุดแข็ง 🤝' => [5, 6], // สูงดี
    ];
    [$normal, $borderline] = $cutoffs[$category] ?? [0, 0];
    if ($category == 'จุดแข็ง 🤝') {
        return $score >= $borderline ? 'ปกติ/มีจุดแข็ง' : ($score >= $normal ? 'ภาวะเสี่ยง' : 'มีปัญหา');
    }
    return $score <= $normal ? 'ปกติ' : ($score <= $borderline ? 'ภาวะเสี่ยง' : 'มีปัญหา');
}

function impactText($score) {
    return $score == 0 ? 'ไม่มีปัญหา' : ($score == 1 ? 'มีปัญหาเล็กน้อย' : 'มีปัญหารุนแรง');
}

function impactColor($score) {
    return $score == 0 ? 'bg-green-500' : ($score == 1 ? 'bg-yellow-500' : 'bg-red-500');
}

$impact_score = array_sum([
    $impact['distress'] ?? 0,
    $impact['home'] ?? 0,
    $impact['leisure'] ?? 0,
    $impact['friend'] ?? 0,
    $impact['classroom'] ?? 0,
    $impact['burden'] ?? 0
]);
?>



<div class="flex flex-wrap -mx-4 mb-4">
    <!-- Left Column -->
    <div class="w-full md:w-1/2 px-4">
        <div class="card bg-emerald-500 border  rounded-lg shadow-sm p-4 mb-4">
            <div class="card-body">
                <h2 class="text-lg font-semibold text-white">🎓 ข้อมูลนักเรียน</h2>
                <p class="text-white">
                    ชื่อ: <?= htmlspecialchars($student_name) ?>  
                    เลขที่: <?= htmlspecialchars($student_no) ?>  
                    ชั้น: ม.<?= htmlspecialchars($student_class) ?>/<?= htmlspecialchars($student_room) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="w-full md:w-1/2 px-4">
        <div class="card border rounded-lg shadow-sm p-4 mb-4">
            <div class="card-body">
                <p class="text-center text-uppercase text-gray-900 mb-0">คะแนนรวม</p>
                <p class="text-center text-4xl text-gray-900 font-bold"><?= htmlspecialchars($totalProblemScore) ?></p>
                <p class="text-center text-2xl font-bold 
                    <?php 
                        if ($totalProblemScore >= 20) {
                            echo 'text-red-500'; // สีแดงสำหรับ "มีปัญหา"
                        } elseif ($totalProblemScore >= 14) {
                            echo 'text-yellow-500'; // สีเหลืองสำหรับ "ภาวะเสี่ยง"
                        } else {
                            echo 'text-green-500'; // สีเขียวสำหรับ "ปกติ"
                        }
                    ?>">
                    <?php if ($totalProblemScore >= 20): ?>
                        มีปัญหา 😥
                    <?php elseif ($totalProblemScore >= 14): ?>
                        ภาวะเสี่ยง 😐
                    <?php else: ?>
                        ปกติ 😄
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php foreach ($categoryScores as $label => $score): ?>
    <div class="mb-4">
        <p class="mb-0 font-semibold"><?= $label ?></p>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <?php
                $percent = min(100, round(($score / 10) * 100));
                $status = scoreLevel($score, $label);
                $color = strpos($status, 'ปกติ') !== false ? 'bg-green-500' : (strpos($status, 'เสี่ยง') !== false ? 'bg-yellow-500' : 'bg-red-500');
            ?>
            <div class="<?= $color ?> text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="width: <?= $percent ?>%; min-width: 120px; white-space: nowrap;">
                <?= $score ?> คะแนน = <?= $status ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<div class="bg-white border rounded-lg shadow-md p-4 mb-4">
    <blockquote class="blockquote">
        <p class="mb-0 text-gray-700 font-medium"><i class="fas fa-comment-dots"></i> ความคิดเห็นเพิ่มเติม</p>
        <footer class="blockquote-footer text-gray-500"><?= htmlspecialchars($memo) ?></footer>
    </blockquote>
</div>

<div class="bg-white border rounded-lg shadow-md p-4">
    <p class="mb-2 text-gray-700 font-medium"><i class="fas fa-exclamation-circle"></i> โดยรวม
    <span class="inline-block bg-green-500 text-white text-sm font-semibold px-2 py-1 rounded">
        <?= $total_score > 20 ? 'มีปัญหา' : ($total_score >= 15 ? 'ภาวะเสี่ยง' : 'ไม่มีปัญหา') ?>
    </span>
    ในด้านใดด้านหนึ่ง 
            <span class="text-gray-900 btn-sm text-sm bg-gray-200 mx-1 rounded">#ด้านอารมณ์ </span>
            <span class="text-gray-900 btn-sm text-sm bg-gray-200 mx-1 rounded">#ด้านสมาธิ </span>
            <span class="text-gray-900 btn-sm text-sm bg-gray-200 mx-1 rounded">#ด้านพฤติกรรม </span>
            <span class="text-gray-900 btn-sm text-sm bg-gray-200 mx-1 rounded">#ความสามารถเข้ากับผู้อื่น </span>
    </p>
    <p class="mb-2 text-gray-700 font-medium"><i class="fas fa-exclamation-circle"></i> ปัญหานี้รบกวนชีวิตประจำวันในด้านต่างๆ ต่อไปนี้</p>
    <ul class="list-disc pl-5 text-gray-700">
        <li>ความเป็นอยู่ที่บ้าน: <span class="<?= impactColor($impact['home'] ?? 0) ?> text-white text-sm font-semibold px-2 py-1 rounded"><?= impactText($impact['home'] ?? 0) ?></span></li>
        <li>กิจกรรมยามว่าง: <span class="<?= impactColor($impact['leisure'] ?? 0) ?> text-white text-sm font-semibold px-2 py-1 rounded"><?= impactText($impact['leisure'] ?? 0) ?></span></li>
        <li>การคบเพื่อน: <span class="<?= impactColor($impact['friend'] ?? 0) ?> text-white text-sm font-semibold px-2 py-1 rounded"><?= impactText($impact['friend'] ?? 0) ?></span></li>
        <li>การเรียนในห้องเรียน: <span class="<?= impactColor($impact['classroom'] ?? 0) ?> text-white text-sm font-semibold px-2 py-1 rounded"><?= impactText($impact['classroom'] ?? 0) ?></span></li>
    </ul>
    <p class="mt-2 text-gray-700 font-medium"><i class="fas fa-exclamation-circle"></i> ปัญหานี้</p>
    <span class="<?= impactColor($impact['burden'] ?? 0) ?> text-white text-sm font-semibold px-2 py-1 rounded">
        <?= ($impact['burden'] ?? 0) == 0 ? 'ไม่' : 'เป็นปัญหา' ?>
    </span> ทำให้ตัวเองหรือครอบครัวเกิดความยุ่งยาก
</div>
