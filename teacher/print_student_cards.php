<?php
/**
 * Print Student Cards - Modern Version
 * Supports: 8 Cards per A4, Photo, Dynamic Data, QR ready
 */

session_start();
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Student.php';
require_once __DIR__ . '/../class/Teacher.php';

// Check login
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$studentClass = new Student($db);
$teacherClass = new Teacher($db);

$userid = $_SESSION['Teacher_login'];
$userData = $user->userData($userid);

$term = $user->getTerm();
$pee = $user->getPee();

// Get class/room from query or session
$class = isset($_GET['class']) ? $_GET['class'] : ($userData['Teach_class'] ?? '');
$room = isset($_GET['room']) ? $_GET['room'] : ($userData['Teach_room'] ?? '');

// Get students
$students = $studentClass->getStudentsByMajorRoom($class, $room);

// Prepare data for JS
$studentsJson = json_encode(array_map(function($s) {
    return [
        'no' => $s['Stu_no'],
        'id' => $s['Stu_id'],
        'prefix' => $s['Stu_pre'],
        'name' => $s['Stu_name'],
        'sur' => $s['Stu_sur'],
        'picture' => $s['Stu_picture'] ?: 'default.jpg',
        'sex' => ($s['Stu_pre'] === 'นาย' || $s['Stu_pre'] === 'เด็กชาย') ? 'ชาย' : 'หญิง'
    ];
}, $students ?? []));

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์บัตรนักเรียน ม.<?php echo $class; ?>/<?php echo $room; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @font-face {
            font-family: 'Sarabun';
            src: url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap');
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            body { background-color: white; }
            .no-print { display: none !important; }
            .print-area { 
                width: 210mm;
                margin: 0 auto;
                padding: 10mm;
                box-shadow: none !important;
            }
            .card-wrapper {
                page-break-inside: avoid;
            }
        }

        .paper {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 20px auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
        }

        /* Card Design */
        .student-card {
            width: 90mm;
            height: 55mm;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            background: white;
            display: flex;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 12mm;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            display: flex;
            align-items: center;
            padding: 0 4mm;
            font-size: 10pt;
            font-weight: bold;
        }

        .card-body {
            margin-top: 12mm;
            display: flex;
            width: 100%;
            height: calc(55mm - 12mm);
            padding: 3mm;
        }

        .photo-area {
            width: 30mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-right: 1px dashed #cbd5e1;
        }

        .student-photo {
            width: 22mm;
            height: 28mm;
            object-fit: cover;
            border-radius: 4px;
            border: 2px solid #f1f5f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .info-area {
            flex: 1;
            padding-left: 4mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .school-logo {
            width: 8mm;
            height: 8mm;
            margin-right: 2mm;
        }

        .card-bg-decoration {
            position: absolute;
            bottom: -10mm;
            right: -10mm;
            width: 40mm;
            height: 40mm;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            z-index: 0;
        }

        /* Control Panel */
        .control-panel {
            position: fixed;
            left: 20px;
            top: 20px;
            width: 300px;
            z-index: 100;
        }
    </style>
</head>
<body>

    <!-- Control Panel -->
    <div class="control-panel no-print bg-white p-6 rounded-2xl shadow-2xl border border-slate-200">
        <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2 text-lg">
            <i class="fas fa-id-card text-indigo-600"></i>
            ตั้งค่าพิมพ์บัตร
        </h3>
        
        <div class="space-y-4 mb-6">
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">ชุดสีพื้นหลัง:</label>
                <select id="colorTheme" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="indigo">Indigo (น้ำเงิน-ม่วง)</option>
                    <option value="emerald">Emerald (เขียว)</option>
                    <option value="rose">Rose (ชมพู-แดง)</option>
                    <option value="slate">Slate (หรูหรา)</option>
                </select>
            </div>
            
            <div class="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox" id="show-photo" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                <span class="text-sm text-slate-700 group-hover:text-indigo-600 transition">แสดงรูปถ่ายนักเรียน</span>
            </div>
        </div>

        <div class="space-y-3">
            <button onclick="window.print()" class="w-full bg-gradient-to-r from-indigo-600 to-violet-700 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-indigo-200 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-print"></i> พิมพ์บัตร
            </button>
            <button onclick="window.close()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 rounded-xl transition-all">
                ปิดหน้าต่าง
            </button>
        </div>
        
        <div class="mt-6 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
            <p class="text-[10px] text-indigo-700 leading-relaxed font-medium">
                <i class="fas fa-info-circle mr-1"></i>
                แนะนำ: 1 หน้า A4 จะพิมพ์บัตรได้ 8 ใบ (2 คอลัมน์) เพื่อความสวยงามและประหยัดพื้นที่
            </p>
        </div>
    </div>

    <!-- Paper Content -->
    <div class="paper print-area shadow-2xl rounded-sm" id="renderArea">
        <div class="grid grid-cols-2 gap-y-[10mm] gap-x-[5mm]" id="cardGrid">
            <!-- Cards will be injected here -->
        </div>
    </div>

    <script>
        const students = <?php echo $studentsJson; ?>;
        const classVal = "<?php echo $class; ?>";
        const roomVal = "<?php echo $room; ?>";
        const peeVal = "<?php echo $pee; ?>";

        const themes = {
            indigo: 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)',
            emerald: 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
            rose: 'linear-gradient(135deg, #e11d48 0%, #fb7185 100%)',
            slate: 'linear-gradient(135deg, #1e293b 0%, #475569 100%)'
        };

        function updateCards() {
            const selectedTheme = themes[document.getElementById('colorTheme').value];
            const showPhoto = document.getElementById('show-photo').checked;
            const grid = document.getElementById('cardGrid');
            
            let html = '';
            students.forEach(s => {
                html += `
                <div class="card-wrapper flex justify-center">
                    <div class="student-card">
                        <div class="card-header" style="background: ${selectedTheme}">
                            <img src="../assets/img/logo.png" class="school-logo" onerror="this.src='https://via.placeholder.com/40'">
                            โรงเรียนพิชัย
                            <span class="ml-auto">ม.${classVal}/${roomVal}</span>
                        </div>
                        <div class="card-body">
                            ${showPhoto ? `
                            <div class="photo-area">
                                <img src="https://std.phichai.ac.th/photo/${s.picture}" 
                                     class="student-photo" 
                                     onerror="this.src='https://via.placeholder.com/100x120?text=No+Photo'">
                                <div class="mt-2 text-[8pt] font-black text-indigo-600">เลขที่ ${s.no}</div>
                            </div>
                            ` : ''}
                            <div class="info-area">
                                <div class="text-[7pt] text-slate-400 font-bold uppercase tracking-wider mb-0.5">ชื่อ-นามสกุล</div>
                                <div class="text-[11pt] font-black text-slate-800 mb-2 truncate">${s.prefix}${s.name} ${s.sur}</div>
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <div class="text-[7pt] text-slate-400 font-bold">รหัสประจำตัว</div>
                                        <div class="text-[10pt] font-mono font-bold text-slate-700">${s.id}</div>
                                    </div>
                                    <div>
                                        <div class="text-[7pt] text-slate-400 font-bold text-right mr-3">เพศ</div>
                                        <div class="text-[10pt] font-bold text-slate-700 text-right mr-3">${s.sex}</div>
                                    </div>
                                </div>
                                <div class="mt-3 text-[7pt] text-slate-500 italic">
                                    ปีการศึกษา ${peeVal}
                                </div>
                            </div>
                        </div>
                        <div class="card-bg-decoration"></div>
                    </div>
                </div>
                `;
            });
            grid.innerHTML = html;
        }

        // Listeners
        document.getElementById('colorTheme').addEventListener('change', updateCards);
        document.getElementById('show-photo').addEventListener('change', updateCards);

        // Init
        updateCards();
    </script>
</body>
</html>
