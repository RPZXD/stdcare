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
        $isMale = ($data['Stu_sex'] == 1 || $data['Stu_pre'] == 'นาย' || $data['Stu_pre'] == 'เด็กชาย');
?>

<!-- Student Profile View - Premium Modern UI -->
<div class="student-profile-view">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row gap-6 mb-6">
        <!-- Photo Card -->
        <div class="flex-shrink-0">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-violet-500 via-purple-500 to-pink-500 rounded-2xl blur opacity-40 group-hover:opacity-60 transition-opacity"></div>
                <div class="relative bg-white dark:bg-slate-800 rounded-2xl p-2 shadow-xl">
                    <img src="https://std.phichai.ac.th/photo/<?php echo htmlspecialchars($data['Stu_picture'] ?: 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($studentname); ?>"
                         class="w-40 h-52 md:w-48 md:h-64 object-cover rounded-xl"
                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($data['Stu_name']); ?>&size=256&background=8b5cf6&color=fff&bold=true'">
                    <!-- Status Badge -->
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-sm font-bold shadow-lg <?php echo getStatusClass($data['Stu_status']); ?>">
                        <?php echo strstatus($data['Stu_status']); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Header -->
        <div class="flex-1">
            <div class="bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 rounded-2xl p-5 md:p-6 text-white shadow-xl">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <?php echo $isMale ? '<i class="fas fa-mars text-xl"></i>' : '<i class="fas fa-venus text-xl"></i>'; ?>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black"><?php echo htmlspecialchars($studentname); ?></h2>
                        <?php if (!empty($data['Stu_nick'])): ?>
                            <p class="text-white/80 text-sm">ชื่อเล่น: <?php echo htmlspecialchars($data['Stu_nick']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                    <div class="bg-white/10 backdrop-blur rounded-xl p-3 text-center">
                        <p class="text-2xl font-black"><?php echo htmlspecialchars($data['Stu_no']); ?></p>
                        <p class="text-xs text-white/70">เลขที่</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-3 text-center">
                        <p class="text-lg font-bold">ม.<?php echo htmlspecialchars($data['Stu_major']); ?>/<?php echo htmlspecialchars($data['Stu_room']); ?></p>
                        <p class="text-xs text-white/70">ชั้นเรียน</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-3 text-center col-span-2 md:col-span-2">
                        <p class="text-sm font-bold font-mono"><?php echo htmlspecialchars($data['Stu_id']); ?></p>
                        <p class="text-xs text-white/70">รหัสนักเรียน</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Basic Info Section -->
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center shadow">
                <i class="fas fa-user text-white text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">📋 ข้อมูลพื้นฐาน</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php
            $basicInfo = [
                ['icon' => '🆔', 'label' => 'เลขบัตรประชาชน', 'value' => $data['Stu_citizenid'], 'color' => 'blue'],
                ['icon' => '🎂', 'label' => 'วันเกิด', 'value' => $data['Stu_birth'], 'color' => 'pink'],
                ['icon' => '🩸', 'label' => 'กรุ๊ปเลือด', 'value' => $data['Stu_blood'], 'color' => 'red'],
                ['icon' => '🛐', 'label' => 'ศาสนา', 'value' => $data['Stu_religion'], 'color' => 'amber'],
                ['icon' => '📞', 'label' => 'เบอร์โทรศัพท์', 'value' => $data['Stu_phone'], 'color' => 'green', 'link' => true],
                ['icon' => '🏠', 'label' => 'ที่อยู่', 'value' => $data['Stu_addr'], 'color' => 'indigo'],
            ];
            foreach ($basicInfo as $info):
                $colorClass = getColorClass($info['color']);
            ?>
            <div class="<?php echo $colorClass; ?> rounded-xl p-4 border hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-start gap-3">
                    <span class="text-2xl group-hover:scale-110 transition-transform"><?php echo $info['icon']; ?></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium"><?php echo $info['label']; ?></p>
                        <?php if (!empty($info['link']) && !empty($info['value'])): ?>
                            <a href="tel:<?php echo htmlspecialchars($info['value']); ?>" class="text-sm font-bold text-blue-600 hover:underline break-all">
                                <?php echo htmlspecialchars($info['value']); ?>
                            </a>
                        <?php else: ?>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 break-words"><?php echo htmlspecialchars($info['value'] ?: 'ไม่ระบุ'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Family Section -->
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-rose-500 rounded-lg flex items-center justify-center shadow">
                <i class="fas fa-heart text-white text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">👨‍👩‍👧‍👦 ข้อมูลครอบครัว</h3>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Father -->
            <div class="bg-gradient-to-br from-blue-50 to-sky-100 dark:from-blue-900/30 dark:to-sky-900/30 rounded-2xl p-5 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-xl">👨</span>
                    </div>
                    <h4 class="font-bold text-blue-800 dark:text-blue-300">ข้อมูลบิดา</h4>
                </div>
                <div class="space-y-2 text-sm">
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">ชื่อ:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($data['Father_name'] ?: 'ไม่ระบุ'); ?></span></p>
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">อาชีพ:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($data['Father_occu'] ?: 'ไม่ระบุ'); ?></span></p>
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">รายได้:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo $data['Father_income'] ? number_format($data['Father_income']) . ' บาท' : 'ไม่ระบุ'; ?></span></p>
                </div>
            </div>
            
            <!-- Mother -->
            <div class="bg-gradient-to-br from-pink-50 to-rose-100 dark:from-pink-900/30 dark:to-rose-900/30 rounded-2xl p-5 border border-pink-200 dark:border-pink-800">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-xl">👩</span>
                    </div>
                    <h4 class="font-bold text-pink-800 dark:text-pink-300">ข้อมูลมารดา</h4>
                </div>
                <div class="space-y-2 text-sm">
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">ชื่อ:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($data['Mother_name'] ?: 'ไม่ระบุ'); ?></span></p>
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">อาชีพ:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($data['Mother_occu'] ?: 'ไม่ระบุ'); ?></span></p>
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">รายได้:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo $data['Mother_income'] ? number_format($data['Mother_income']) . ' บาท' : 'ไม่ระบุ'; ?></span></p>
                </div>
            </div>
            
            <!-- Guardian -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-2xl p-5 border border-green-200 dark:border-green-800">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-xl">👪</span>
                    </div>
                    <h4 class="font-bold text-green-800 dark:text-green-300">ข้อมูลผู้ปกครอง</h4>
                </div>
                <div class="space-y-2 text-sm">
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">ชื่อ:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($data['Par_name'] ?: 'ไม่ระบุ'); ?></span></p>
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">ความสัมพันธ์:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($data['Par_relate'] ?: 'ไม่ระบุ'); ?></span></p>
                    <p class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">อาชีพ:</span><span class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($data['Par_occu'] ?: 'ไม่ระบุ'); ?></span></p>
                    <?php if (!empty($data['Par_phone'])): ?>
                    <p class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">โทร:</span>
                        <a href="tel:<?php echo htmlspecialchars($data['Par_phone']); ?>" class="font-bold text-green-600 hover:underline flex items-center gap-1">
                            <i class="fas fa-phone-alt text-xs"></i>
                            <?php echo htmlspecialchars($data['Par_phone']); ?>
                        </a>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contact Quick Actions -->
    <?php if (!empty($data['Stu_phone']) || !empty($data['Par_phone'])): ?>
    <div class="bg-gradient-to-r from-slate-100 to-gray-100 dark:from-slate-800 dark:to-gray-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-3">📱 ติดต่อด่วน</p>
        <div class="flex flex-wrap gap-3">
            <?php if (!empty($data['Stu_phone'])): ?>
            <a href="tel:<?php echo htmlspecialchars($data['Stu_phone']); ?>" class="flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                <i class="fas fa-phone-alt"></i>
                <span>โทรนักเรียน</span>
            </a>
            <?php endif; ?>
            <?php if (!empty($data['Par_phone'])): ?>
            <a href="tel:<?php echo htmlspecialchars($data['Par_phone']); ?>" class="flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                <i class="fas fa-phone-alt"></i>
                <span>โทรผู้ปกครอง</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
    } else {
        // Student not found
?>
<div class="text-center py-12">
    <div class="w-20 h-20 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
        <i class="fas fa-user-slash text-red-500 text-3xl"></i>
    </div>
    <h3 class="text-xl font-bold text-red-600 mb-2">ไม่พบข้อมูลนักเรียน</h3>
    <p class="text-gray-500">ไม่สามารถค้นหาข้อมูลนักเรียนรหัส <?php echo htmlspecialchars($stu_id); ?></p>
</div>
<?php
    }
} else {
    // No student ID provided
?>
<div class="text-center py-12">
    <div class="w-20 h-20 mx-auto bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mb-4">
        <i class="fas fa-exclamation-triangle text-amber-500 text-3xl"></i>
    </div>
    <h3 class="text-xl font-bold text-amber-600 mb-2">ไม่ได้ระบุรหัสนักเรียน</h3>
    <p class="text-gray-500">กรุณาเลือกนักเรียนที่ต้องการดูข้อมูล</p>
</div>
<?php
}

// Helper functions
function strstatus($str) {
    switch ($str) {
        case "1": return '🟢 ปกติ';
        case "2": return '🎓 จบการศึกษา';
        case "3": return '🏫 ย้ายโรงเรียน';
        case "4": return '❌ ออกกลางคัน';
        case "9": return '💔 เสียชีวิต';
        default: return '❓ ไม่ทราบ';
    }
}

function getStatusClass($str) {
    switch ($str) {
        case "1": return 'bg-green-500 text-white';
        case "2": return 'bg-blue-500 text-white';
        case "3": return 'bg-amber-500 text-white';
        case "4": return 'bg-red-500 text-white';
        case "9": return 'bg-gray-500 text-white';
        default: return 'bg-gray-400 text-white';
    }
}

function getColorClass($color) {
    $classes = [
        'blue' => 'bg-gradient-to-br from-blue-50 to-sky-100 dark:from-blue-900/20 dark:to-sky-900/20 border-blue-200 dark:border-blue-800',
        'pink' => 'bg-gradient-to-br from-pink-50 to-rose-100 dark:from-pink-900/20 dark:to-rose-900/20 border-pink-200 dark:border-pink-800',
        'red' => 'bg-gradient-to-br from-red-50 to-rose-100 dark:from-red-900/20 dark:to-rose-900/20 border-red-200 dark:border-red-800',
        'amber' => 'bg-gradient-to-br from-amber-50 to-yellow-100 dark:from-amber-900/20 dark:to-yellow-900/20 border-amber-200 dark:border-amber-800',
        'green' => 'bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 border-green-200 dark:border-green-800',
        'indigo' => 'bg-gradient-to-br from-indigo-50 to-violet-100 dark:from-indigo-900/20 dark:to-violet-900/20 border-indigo-200 dark:border-indigo-800',
    ];
    return $classes[$color] ?? $classes['blue'];
}
?>