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

        echo "<style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out; }
        .animate-pulse-slow { animation: pulse 2s infinite; }
        .animate-slideIn { animation: slideIn 0.6s ease-out; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .info-card { background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); }
        .section-header { background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%); }
        .emoji-float { display: inline-block; transition: transform 0.3s ease; }
        .emoji-float:hover { transform: scale(1.2) rotate(5deg); }
        </style>";

        echo "<div class='p-8 gradient-bg min-h-screen'>";
        echo "<div class='max-w-6xl mx-auto'>";
        
        // Header section with photo and title
        echo "<div class='animate-fadeInUp info-card rounded-2xl shadow-2xl p-8 mb-8 card-hover'>";
        echo "<div class='flex flex-col lg:flex-row items-center gap-8'>";
        echo "<div class='relative'>";
        echo "<img class='rounded-2xl shadow-2xl animate-pulse-slow border-4 border-white' src='../photo/" . $data['Stu_picture'] . "' alt='Student Picture' style='max-height:320px;max-width:300px;'>";
        echo "<div class='absolute -top-2 -right-2 bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center animate-pulse'>✓</div>";
        echo "</div>";
        echo "<div class='text-center lg:text-left'>";
        echo "<h1 class='text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-4'>";
        echo "<span class='emoji-float'>👨‍🎓</span> ข้อมูลนักเรียน";
        echo "</h1>";
        echo "<h2 class='text-lg font-semibold text-gray-700 mb-2'>" . $studentname . "</h2>";
        echo "<p class='text-base text-gray-600'>รหัสนักเรียน: " . $data['Stu_id'] . "</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";

        // Basic info section
        echo "<div class='animate-slideIn info-card rounded-2xl shadow-xl p-8 mb-8 card-hover' style='animation-delay: 0.2s;'>";
        echo "<div class='section-header -m-8 mb-6 p-6 rounded-t-2xl'>";
        echo "<h2 class='text-2xl font-bold text-white'><span class='emoji-float'>📋</span> ข้อมูลพื้นฐาน</h2>";
        echo "</div>";
        echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'>";
        
        $basicInfo = [
            ['📛', 'ชื่อ-สกุล', $studentname],
            ['🆔', 'เลขประจำตัวนักเรียน', $data['Stu_id']],
            ['🏫', 'ชั้น', $data['Stu_major'] . "/" . $data['Stu_room'] . " เลขที่ " . $data['Stu_no']],
            ['📞', 'เบอร์โทรศัพท์', $data['Stu_phone']],
            ['🆔', 'เลขบัตรประชาชน', $data['Stu_citizenid']],
            ['📞', 'เบอร์โทรผู้ปกครอง', $data['Par_phone']]
        ];
        
        foreach ($basicInfo as $info) {
            echo "<div class='bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-xl border border-blue-200 hover:shadow-lg transition-all duration-300'>";
            echo "<div class='flex items-center gap-3'>";
            echo "<span class='emoji-float text-2xl'>" . $info[0] . "</span>";
            echo "<div>";
            echo "<p class='text-sm text-gray-600 font-medium'>" . $info[1] . "</p>";
            echo "<p class='text-base font-semibold text-gray-800'>" . $info[2] . "</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
        echo "</div>";

        // Additional info section
        echo "<div class='animate-slideIn info-card rounded-2xl shadow-xl p-8 mb-8 card-hover' style='animation-delay: 0.4s;'>";
        echo "<div class='section-header -m-8 mb-6 p-6 rounded-t-2xl' style='background: linear-gradient(90deg, #ff758c 0%, #ff7eb3 100%);'>";
        echo "<h2 class='text-2xl font-bold text-white'><span class='emoji-float'>🎯</span> ข้อมูลเพิ่มเติม</h2>";
        echo "</div>";
        echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'>";
        
        $additionalInfo = [
            ['🔑', 'รหัสผ่าน', str_repeat('*', strlen($data['Stu_password']))],
            ['⚧️', 'เพศ', ($data['Stu_sex'] == 1 ? 'ชาย' : 'หญิง')],
            ['👶', 'ชื่อเล่น', $data['Stu_nick']],
            ['🎂', 'วันเดือนปีเกิด', $data['Stu_birth']],
            ['🛐', 'ศาสนา', $data['Stu_religion']],
            ['🩸', 'กรุ๊ปเลือด', $data['Stu_blood']],
            ['🏠', 'ที่อยู่', $data['Stu_addr']]
        ];
        
        foreach ($additionalInfo as $info) {
            echo "<div class='bg-gradient-to-br from-pink-50 to-rose-100 p-4 rounded-xl border border-pink-200 hover:shadow-lg transition-all duration-300'>";
            echo "<div class='flex items-center gap-3'>";
            echo "<span class='emoji-float text-2xl'>" . $info[0] . "</span>";
            echo "<div>";
            echo "<p class='text-sm text-gray-600 font-medium'>" . $info[1] . "</p>";
            echo "<p class='text-lg font-semibold text-gray-800'>" . ($info[2] ?: 'ไม่ระบุ') . "</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
        echo "</div>";

        // Parent info section
        echo "<div class='animate-slideIn info-card rounded-2xl shadow-xl p-8 mb-8 card-hover' style='animation-delay: 0.6s;'>";
        echo "<div class='section-header -m-8 mb-6 p-6 rounded-t-2xl' style='background: linear-gradient(90deg, #a8edea 0%, #fed6e3 100%);'>";
        echo "<h2 class='text-2xl font-bold text-gray-800'><span class='emoji-float'>👨‍👩‍👧‍👦</span> ข้อมูลผู้ปกครอง</h2>";
        echo "</div>";
        
        // Family info in organized cards
        echo "<div class='grid grid-cols-1 lg:grid-cols-3 gap-8'>";
        
        // Father info
        echo "<div class='bg-gradient-to-br from-blue-50 to-cyan-100 p-6 rounded-xl border border-blue-200'>";
        echo "<h3 class='text-xl font-bold text-blue-800 mb-4 flex items-center gap-2'>";
        echo "<span class='emoji-float'>👨‍👦</span> ข้อมูลบิดา";
        echo "</h3>";
        echo "<div class='space-y-3'>";
        echo "<p class='text-gray-800'><strong>ชื่อ:</strong> " . ($data['Father_name'] ?: 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>อาชีพ:</strong> " . ($data['Father_occu'] ?: 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>รายได้:</strong> " . ($data['Father_income'] ? number_format($data['Father_income']) . ' บาท' : 'ไม่ระบุ') . "</p>";
        echo "</div>";
        echo "</div>";
        
        // Mother info
        echo "<div class='bg-gradient-to-br from-pink-50 to-rose-100 p-6 rounded-xl border border-pink-200'>";
        echo "<h3 class='text-xl font-bold text-pink-800 mb-4 flex items-center gap-2'>";
        echo "<span class='emoji-float'>👩‍👦</span> ข้อมูลมารดา";
        echo "</h3>";
        echo "<div class='space-y-3'>";
        echo "<p class='text-gray-800'><strong>ชื่อ:</strong> " . ($data['Mother_name'] ?: 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>อาชีพ:</strong> " . ($data['Mother_occu'] ?: 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>รายได้:</strong> " . ($data['Mother_income'] ? number_format($data['Mother_income']) . ' บาท' : 'ไม่ระบุ') . "</p>";
        echo "</div>";
        echo "</div>";
        
        // Guardian info
        echo "<div class='bg-gradient-to-br from-green-50 to-emerald-100 p-6 rounded-xl border border-green-200'>";
        echo "<h3 class='text-xl font-bold text-green-800 mb-4 flex items-center gap-2'>";
        echo "<span class='emoji-float'>👨‍👩‍👧</span> ข้อมูลผู้ปกครอง";
        echo "</h3>";
        echo "<div class='space-y-3'>";
        echo "<p class='text-gray-800'><strong>ชื่อ:</strong> " . ($data['Par_name'] ?: 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>ความสัมพันธ์:</strong> " . ($data['Par_relate'] ?: 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>อาชีพ:</strong> " . ($data['Par_occu'] ?: 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>รายได้:</strong> " . ($data['Par_income'] ? number_format($data['Par_income']) . ' บาท' : 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>ที่อยู่:</strong> " . ($data['Par_addr'] ?: 'ไม่ระบุ') . "</p>";
        echo "<p class='text-gray-800'><strong>เบอร์โทร:</strong> " . ($data['Par_phone'] ?: 'ไม่ระบุ') . "</p>";
        echo "</div>";
        echo "</div>";
        
        echo "</div>";
        echo "</div>";

        // Status section
        echo "<div class='animate-slideIn info-card rounded-2xl shadow-xl p-8 card-hover' style='animation-delay: 0.8s;'>";
        echo "<div class='section-header -m-8 mb-6 p-6 rounded-t-2xl' style='background: linear-gradient(90deg, #ffecd2 0%, #fcb69f 100%);'>";
        echo "<h2 class='text-2xl font-bold text-gray-800'><span class='emoji-float'>📜</span> สถานะนักเรียน</h2>";
        echo "</div>";
        echo "<div class='text-center py-8'>";
        $statusColor = $data['Stu_status'] == '1' ? 'green' : ($data['Stu_status'] == '2' ? 'blue' : 'red');
        echo "<div class='inline-flex items-center gap-4 bg-gradient-to-r from-" . $statusColor . "-100 to-" . $statusColor . "-200 px-8 py-4 rounded-full border-2 border-" . $statusColor . "-300'>";
        echo "<span class='emoji-float text-3xl'>📌</span>";
        echo "<span class='text-2xl font-bold text-" . $statusColor . "-800'>" . strstatus($data['Stu_status']) . "</span>";
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo "</div>";
        echo "</div>";

        // Add JavaScript for enhanced interactions
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-hover');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });
        });
        </script>";
        
    } else {
        echo "<div class='min-h-screen flex items-center justify-center gradient-bg'>";
        echo "<div class='bg-white p-8 rounded-2xl shadow-2xl text-center max-w-md mx-auto animate-fadeInUp'>";
        echo "<div class='text-6xl mb-4'>🚨</div>";
        echo "<h2 class='text-2xl font-bold text-red-600 mb-2'>ไม่พบข้อมูลนักเรียน</h2>";
        echo "<p class='text-gray-600'>ไม่สามารถค้นหาข้อมูลนักเรียนที่ต้องการได้</p>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<div class='min-h-screen flex items-center justify-center gradient-bg'>";
    echo "<div class='bg-white p-8 rounded-2xl shadow-2xl text-center max-w-md mx-auto animate-fadeInUp'>";
    echo "<div class='text-6xl mb-4'>⚠️</div>";
    echo "<h2 class='text-2xl font-bold text-yellow-600 mb-2'>รหัสนักเรียนไม่ถูกต้อง</h2>";
    echo "<p class='text-gray-600'>โปรดระบุรหัสนักเรียนที่ถูกต้อง</p>";
    echo "</div>";
    echo "</div>";
}

function strstatus($str) {
    switch ($str) {
        case "1":
            return '🟢 ปกติ';
        case "2":
            return '🎓 จบการศึกษา';
        case "3":
            return '🏫 ย้ายโรงเรียน';
        case "4":
            return '❌ ออกกลางคัน';
        case "9":
            return '💔 เสียชีวิต';
        default:
            return '❓ ไม่ทราบสถานะ';
    }
}
?>