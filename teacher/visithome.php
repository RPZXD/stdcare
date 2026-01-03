<?php
/**
 * Teacher Visit Home Page - MVC Entry Point
 * Handles student home visit records
 */
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

/**
 * Helper to generate the visit form HTML
 * This is used for AJAX requests to populate the modal content
 */
function generateVisitForm($data, $isEdit = false, $currentTerm = null, $currentPee = null) {
    $questions = [
        "1. บ้านที่อยู่อาศัย" => ["บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น"],
        "2. ระยะทางระหว่างบ้านกับโรงเรียน" => ["1-5 กิโลเมตร", "6-10 กิโลเมตร", "11-15 กิโลเมตร", "16-20 กิโลเมตร", "20 กิโลเมตรขึ้นไป"],
        "3. การเดินทางไปโรงเรียนของนักเรียน" => ["เดิน", "รถจักรยาน", "รถจักรยานยนต์", "รถยนต์ส่วนตัว", "รถรับส่งรถโดยสาร", "อื่นๆ"],
        "4. สภาพแวดล้อมของบ้าน" => ["ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง"],
        "5. อาชีพของผู้ปกครอง" => ["เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ"],
        "6. สถานที่ทำงานของบิดามารดา" => ["ในอำเภอเดียวกัน", "ในจังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ"],
        "7. สถานภาพของบิดามารดา" => ["บิดามารดาอยู่ด้วยกัน", "บิดามารดาหย่าร้างกัน", "บิดาถึงแก่กรรม", "มารดาถึงแก่กรรม", "บิดาและมารดาถึงแก่กรรม"],
        "8. วิธีการที่ผู้ปกครองอบรมเลี้ยงดูนักเรียน" => ["เข้มงวดกวดขัน", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ"],
        "9. โรคประจำตัวของนักเรียน" => ["ไม่มี", "มี"],
        "10. ความสัมพันธ์ของสมาชิกในครอบครัว" => ["อบอุ่น", "เฉยๆ", "ห่างเหิน"],
        "11. หน้าที่รับผิดชอบภายในบ้าน" => ["มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี"],
        "12. สมาชิกในครอบครัวนักเรียนสนิทสนมกับใครมากที่สุด" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"],
        "13. รายได้กับการใช้จ่ายในครอบครัว" => ["เพียงพอ", "ไม่เพียงพอในบางครั้ง", "ขัดสน"],
        "14. ลักษณะเพื่อนเล่นที่บ้านของนักเรียนโดยปกติเป็น" => ["เพื่อนรุ่นเดียวกัน", "เพื่อนรุ่นน้อง", "เพื่อนรุ่นพี่", "เพื่อนทุกรุ่น"],
        "15. ความต้องการของผู้ปกครอง เมื่อนักเรียนจบชั้นสูงสุดของโรงเรียน" => ["ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ"],
        "16. เมื่อนักเรียนมีปัญหา นักเรียนจะปรึกษาใคร" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"],
        "17. ความรู้สึกของผู้ปกครองที่มีต่อครูที่มาเยี่ยมบ้าน" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"],
        "18. ทัศนคติ/ความรู้สึกของผู้ปกครองที่มีต่อโรงเรียน" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"],
    ];

    $images = [
        ["id" => "image1", "label" => "รูปภาพที่ 1", "description" => "* ภาพตัวบ้านนักเรียน (ให้เห็นทั้งหลัง)", "required" => true],
        ["id" => "image2", "label" => "รูปภาพที่ 2", "description" => "* ภาพภายในบ้านนักเรียน", "required" => true],
        ["id" => "image3", "label" => "รูปภาพที่ 3", "description" => "* ภาพขณะครูเยี่ยมบ้านกับนักเรียนและผู้ปกครอง", "required" => true],
        ["id" => "image4", "label" => "รูปภาพที่ 4", "description" => "=> ภาพเพิ่มเติม", "required" => false],
        ["id" => "image5", "label" => "รูปภาพที่ 5", "description" => "=> ภาพเพิ่มเติม", "required" => false],
    ];

    $formId = $isEdit ? 'editVisitForm' : 'addVisitForm';
    
    ob_start();
    ?>
    <div class="space-y-8">
        <!-- Student Identity Summary -->
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-3xl p-6 flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-3xl font-bold shadow-lg shadow-indigo-500/30">
                <?= mb_substr($data['Stu_name'], 0, 1) ?>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h4 class="text-xl font-black text-slate-800 dark:text-white"><?= $data['Stu_pre'] . $data['Stu_name'] . " " . $data['Stu_sur']; ?></h4>
                <p class="text-slate-500 dark:text-slate-400 font-medium">รหัสนักเรียน: <span class="text-indigo-600 dark:text-indigo-400"><?= $data['Stu_id']; ?></span> • เลขที่ <?= $data['Stu_no'] ?? '-'; ?></p>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm w-full md:w-auto">
                <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-indigo-50 dark:border-slate-700">
                    <p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">ห้องเรียน</p>
                    <p class="text-slate-800 dark:text-white font-bold"><?= $data['Stu_major'] . "/" . $data['Stu_room']; ?></p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-indigo-50 dark:border-slate-700">
                    <p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">เบอร์โทร</p>
                    <p class="text-slate-800 dark:text-white font-bold"><?= $data['Stu_phone'] ?: '-'; ?></p>
                </div>
            </div>
        </div>

        <form id="<?= $formId ?>" enctype="multipart/form-data">
            <input type="hidden" name="stuId" value="<?= $data['Stu_id']; ?>">
            <input type="hidden" name="term" value="<?= $isEdit ? $data['Term'] : $currentTerm; ?>">
            <input type="hidden" name="pee" value="<?= $isEdit ? $data['Pee'] : $currentPee; ?>">

            <div class="space-y-6">
                <?php
                $i = 1;
                foreach ($questions as $question => $options) {
                ?>
                <div class="bg-white dark:bg-slate-800/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-700 hover:border-blue-200 dark:hover:border-blue-900/30 transition-all group">
                    <h5 class="text-md font-black text-slate-800 dark:text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-sm font-bold group-hover:scale-110 transition-transform"><?= $i ?></span>
                        <?= substr($question, 2) ?>
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <?php foreach ($options as $index => $option): 
                            $val = $index + 1;
                            $checked = ($isEdit && isset($data['vh' . $i]) && $data['vh' . $i] == $val) ? 'checked' : '';
                        ?>
                        <label class="relative flex items-center p-4 rounded-2xl border border-slate-100 dark:border-slate-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all has-[:checked]:bg-blue-600 has-[:checked]:border-blue-600 has-[:checked]:text-white group/option">
                            <input type="radio" name="vh<?= $i ?>" value="<?= $val ?>" <?= $checked ?> required class="peer absolute opacity-0">
                            <span class="text-sm font-bold peer-checked:text-white text-slate-600 dark:text-slate-400 transition-colors"><?= $option ?></span>
                            <div class="ml-auto w-5 h-5 rounded-full border-2 border-slate-200 dark:border-slate-600 peer-checked:border-white peer-checked:bg-white flex items-center justify-center transition-all">
                                <div class="w-2 h-2 rounded-full bg-blue-600 scale-0 peer-checked:scale-100 transition-transform"></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php
                    $i++;
                }
                ?>

                <!-- Photos Section -->
                <div class="bg-white dark:bg-slate-800/50 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700">
                    <h5 class="text-lg font-black text-slate-800 dark:text-white mb-6 flex items-center gap-3">
                         <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center"><i class="fas fa-camera"></i></span>
                         รูปถ่ายการเยี่ยมบ้านนักเรียน
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach($images as $img): 
                            $id = $img['id'];
                            $num = substr($id, -1);
                            $oldImg = ($isEdit && !empty($data['picture'.$num])) ? $data['picture'.$num] : '';
                            $imgUrl = $oldImg ? "../teacher/uploads/visithome" . ($data['Pee'] - 543) . "/" . $oldImg : '';
                        ?>
                        <div class="space-y-3">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest px-2"><?= $img['label'] ?> <?= $img['required'] ? '<span class="text-rose-500">*</span>' : '' ?></p>
                            <label for="<?= $id ?>" class="group block relative aspect-[4/3] rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500 transition-all overflow-hidden cursor-pointer">
                                <input type="file" name="<?= $id ?>" id="<?= $id ?>" class="hidden" accept="image/*" onchange="handleImagePreview(this, 'preview-<?= $id ?>')">
                                <input type="hidden" name="remove_<?= $id ?>" id="remove_<?= $id ?>" value="0">
                                
                                <div id="preview-<?= $id ?>" class="absolute inset-0">
                                    <?php if($imgUrl): ?>
                                        <img src="<?= $imgUrl ?>" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="px-4 py-2 bg-white text-slate-800 rounded-xl font-bold text-sm">เปลี่ยนรูปภาพ</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="h-full flex flex-col items-center justify-center p-6 text-center">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                <i class="fas fa-cloud-upload-alt text-xl"></i>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed"><?= $img['description'] ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Problem Textarea -->
                <div class="bg-white dark:bg-slate-800/50 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700">
                    <h5 class="text-md font-black text-slate-800 dark:text-white mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center text-sm font-bold">20</span>
                        ปัญหา อุปสรรค และความต้องการความช่วยเหลือ
                    </h5>
                    <textarea name="vh20" rows="4" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all text-slate-700 dark:text-slate-300 font-medium" placeholder="ระบุปัญหา หรือข้อเสนอแนะเพิ่มเติม..."><?= $isEdit ? htmlspecialchars($data['vh20']) : '' ?></textarea>
                </div>
            </div>
        </form>

        <script>
        function handleImagePreview(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.getElementById(previewId);
                    container.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <span class="px-4 py-2 bg-white text-slate-800 rounded-xl font-bold text-sm">เลือกแล้ว</span>
                        </div>
                    `;
                }
                reader.readAsDataURL(input.files[0]);
                // Reset remove flag
                const removeId = 'remove_' + input.id;
                document.getElementById(removeId).value = "0";
            }
        }
        </script>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * AJAX Handler
 */
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'get_edit_form') {
        $term_v = $_GET['term'];
        $pee_v = $_GET['pee'];
        $stuId = $_GET['stuId'];
        
        // Find existing record
        $sql = "SELECT v.*, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_addr, s.Stu_phone 
                FROM visithome v 
                JOIN student s ON v.Stu_id = s.Stu_id 
                WHERE v.Term = ? AND v.Pee = ? AND v.Stu_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$term_v, $pee_v, $stuId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            echo generateVisitForm($data, true);
        }
        exit;
    }
    
    if ($_GET['action'] === 'get_add_form') {
        $term_v = $_GET['term'];
        $pee_v = $_GET['pee'];
        $stuId = $_GET['stuId'];
        
        // Student basic info
        $sql = "SELECT s.* FROM student s WHERE s.Stu_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$stuId]);
        $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($studentData) {
            echo generateVisitForm($studentData, false, $term_v, $pee_v);
        }
        exit;
    }
}

// Main page configuration
$pageTitle = "เยี่ยมบ้านนักเรียน";
$activePage = "visithome";

// Include view
include __DIR__ . '/../views/teacher/visithome.php';
