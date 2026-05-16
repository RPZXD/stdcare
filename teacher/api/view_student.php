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

<!-- Student Profile View -->
<div class="student-profile-view p-4 md:p-6 lg:p-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
        <!-- Photo Column -->
        <div class="lg:col-span-4 flex flex-col items-center">
            <div class="relative group w-full max-w-[280px]">
                <div class="absolute -inset-1.5 bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500 rounded-3xl blur opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                <div class="relative bg-white dark:bg-slate-800 rounded-3xl p-3 shadow-2xl overflow-hidden">
                    <img src="https://std.phichai.ac.th/photo/<?php echo htmlspecialchars($data['Stu_picture'] ?: 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($studentname); ?>"
                         class="w-full aspect-[3/4] object-cover rounded-2xl transition-transform duration-700 group-hover:scale-110"
                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($data['Stu_name']); ?>&size=256&background=8b5cf6&color=fff&bold=true'">
                    
                    <!-- Status Overlay -->
                    <div class="absolute top-6 right-6 bg-white/90 dark:bg-slate-900/90 backdrop-blur px-3 py-1.5 rounded-xl shadow-lg border border-white/50 dark:border-slate-700/50 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full animate-pulse <?php echo $data['Stu_status'] == 1 ? 'bg-emerald-500' : 'bg-rose-500'; ?>"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200"><?php echo strstatus($data['Stu_status']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <span class="px-4 py-2 bg-slate-100 dark:bg-slate-700/50 rounded-xl text-xs font-black text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                    เลขที่ <?php echo htmlspecialchars($data['Stu_no']); ?>
                </span>
                <span class="px-4 py-2 bg-violet-100 dark:bg-violet-900/30 rounded-xl text-xs font-black text-violet-600 dark:text-violet-300 border border-violet-200 dark:border-violet-800">
                    ม.<?php echo htmlspecialchars($data['Stu_major']); ?>/<?php echo htmlspecialchars($data['Stu_room']); ?>
                </span>
            </div>
        </div>
        
        <!-- Primary Info Column -->
        <div class="lg:col-span-8">
            <div class="mb-6">
                <p class="text-xs font-black text-violet-500 uppercase tracking-[0.2em] mb-2">Student Profile</p>
                <h2 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white leading-tight">
                    <?php echo htmlspecialchars($studentname); ?>
                </h2>
                <?php if (!empty($data['Stu_nick'])): ?>
                    <p class="text-lg text-slate-500 dark:text-slate-400 font-bold flex items-center gap-2 mt-1">
                        <span class="text-xl">👋</span> "<?php echo htmlspecialchars($data['Stu_nick']); ?>"
                    </p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-sm text-2xl">🆔</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัสนักเรียน</p>
                        <p class="font-mono font-bold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($data['Stu_id']); ?></p>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-sm text-2xl">💳</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เลขบัตรประชาชน</p>
                        <p class="font-mono font-bold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($data['Stu_citizenid']); ?></p>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-sm text-2xl">🎂</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">วันเกิด</p>
                        <p class="font-bold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($data['Stu_birth']); ?></p>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-sm text-2xl">📞</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทรศัพท์</p>
                        <a href="tel:<?php echo htmlspecialchars($data['Stu_phone']); ?>" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">
                            <?php echo htmlspecialchars($data['Stu_phone'] ?: 'ไม่ได้ระบุ'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 p-5 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-3xl text-white shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-2xl">🏠</div>
                    <div>
                        <p class="text-[10px] font-black text-white/70 uppercase tracking-widest mb-1">ที่อยู่ปัจจุบัน</p>
                        <p class="text-sm font-bold leading-relaxed"><?php echo htmlspecialchars($data['Stu_addr'] ?: 'ไม่พบข้อมูลที่อยู่'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Tabs Style Sections -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Family Card -->
        <div class="bg-white dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-700 p-6 md:p-8 shadow-xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 text-rose-500 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">ข้อมูลครอบครัว</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Parental Information</p>
                </div>
            </div>
            
            <div class="space-y-6">
                <!-- Father -->
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center text-lg shadow-sm">👨</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">บิดา</p>
                        <p class="font-bold text-slate-700 dark:text-slate-200 truncate"><?php echo htmlspecialchars($data['Father_name'] ?: 'ไม่ระบุ'); ?></p>
                    </div>
                </div>
                <!-- Mother -->
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-pink-50 dark:bg-pink-900/20 rounded-xl flex items-center justify-center text-lg shadow-sm">👩</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">มารดา</p>
                        <p class="font-bold text-slate-700 dark:text-slate-200 truncate"><?php echo htmlspecialchars($data['Mother_name'] ?: 'ไม่ระบุ'); ?></p>
                    </div>
                </div>
                <!-- Guardian -->
                <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center text-lg shadow-sm">👪</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ผู้ปกครอง (<?php echo htmlspecialchars($data['Par_relate'] ?: 'ไม่ระบุ'); ?>)</p>
                        <p class="font-bold text-slate-700 dark:text-slate-200 truncate mb-1"><?php echo htmlspecialchars($data['Par_name'] ?: 'ไม่ระบุ'); ?></p>
                        <?php if (!empty($data['Par_phone'])): ?>
                            <a href="tel:<?php echo htmlspecialchars($data['Par_phone']); ?>" class="inline-flex items-center gap-2 text-xs font-black text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 px-3 py-1 rounded-full transition-colors border border-emerald-100 dark:border-emerald-800">
                                <i class="fas fa-phone-alt scale-75"></i>
                                <?php echo htmlspecialchars($data['Par_phone']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health/Extra Card -->
        <div class="bg-white dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-700 p-6 md:p-8 shadow-xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">ข้อมูลเพิ่มเติม</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Additional Details</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">กรุ๊ปเลือด</p>
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🩸</span>
                        <span class="text-lg font-black text-red-500"><?php echo htmlspecialchars($data['Stu_blood'] ?: 'ไม่ระบุ'); ?></span>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ศาสนา</p>
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🛐</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($data['Stu_religion'] ?: 'ไม่ระบุ'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 p-5 bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    สถานะปัจจุบัน
                </p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 shadow-sm border border-amber-200 dark:border-amber-800">
                        ปีการศึกษา <?php echo htmlspecialchars($data['Stu_pee'] ?? '-'); ?>
                    </span>
                    <span class="px-3 py-1 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 shadow-sm border border-amber-200 dark:border-amber-800">
                        ภาคเรียนที่ <?php echo htmlspecialchars($data['Stu_term'] ?? '-'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
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