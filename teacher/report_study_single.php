<?php 
session_start();

include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);
// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    /* Custom animations */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    
    @keyframes pulse-glow {
        0% { box-shadow: 0 0 5px rgba(99, 102, 241, 0.5); }
        50% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.8), 0 0 30px rgba(99, 102, 241, 0.6); }
        100% { box-shadow: 0 0 5px rgba(99, 102, 241, 0.5); }
    }
    
    .float-animation {
        animation: float 3s ease-in-out infinite;
    }
    
    .pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite;
    }
    
    .gradient-border {
        position: relative;
        background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #f5576c);
        background-size: 400% 400%;
        animation: gradientShift 3s ease infinite;
    }
    
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, transparent 37%, #f0f0f0 63%);
        background-size: 400% 100%;
        animation: skeleton-loading 1.4s ease infinite;
    }
    
    @keyframes skeleton-loading {
        0% { background-position: 100% 50%; }
        100% { background-position: -100% 50%; }
    }
</style>

<body class="hold-transition sidebar-mini layout-fixed light-mode bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <div class="glass-effect rounded-2xl p-6 mb-4 gradient-border">
                            <div class="bg-white rounded-xl p-1">
                                <div class="text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full mb-4 float-animation">
                                        <i class="fas fa-chart-line text-white text-2xl"></i>
                                    </div>
                                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                        📊 รายงานเวลาเรียนรายบุคคล
                                    </h1>
                                    <p class="text-gray-600 mt-2">ติดตามและวิเคราะห์การเข้าเรียนของนักเรียนแต่ละคน</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid px-4">
                <!-- Filter Form -->
                <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 card-hover">
                    <div class="flex items-center mb-6">
                        <div class="w-1 h-8 bg-gradient-to-b from-purple-600 to-blue-600 rounded-full mr-4"></div>
                        <h3 class="text-xl font-semibold text-gray-800">🔍 เลือกข้อมูลที่ต้องการดู</h3>
                    </div>
                    
                    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-layer-group text-purple-500 mr-2"></i>ระดับชั้น
                            </label>
                            <div class="relative">
                                <select id="classSelect" name="class" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-200 transition-all duration-300 appearance-none bg-white">
                                    <option value="">-- เลือกชั้น --</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-door-open text-blue-500 mr-2"></i>ห้องเรียน
                            </label>
                            <div class="relative">
                                <select id="roomSelect" name="room" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 appearance-none bg-white" disabled>
                                    <option value="">-- เลือกห้อง --</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user-graduate text-green-500 mr-2"></i>นักเรียน
                            </label>
                            <div class="relative">
                                <select id="studentSelect" name="student" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-300 appearance-none bg-white" disabled>
                                    <option value="">-- เลือกนักเรียน --</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="hidden">
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
                        <div class="loading-skeleton h-24 rounded-xl"></div>
                        <div class="loading-skeleton h-24 rounded-xl"></div>
                        <div class="loading-skeleton h-24 rounded-xl"></div>
                        <div class="loading-skeleton h-24 rounded-xl"></div>
                        <div class="loading-skeleton h-24 rounded-xl"></div>
                        <div class="loading-skeleton h-24 rounded-xl"></div>
                    </div>
                </div>

                <!-- Student Summary -->
                <div id="studentSummary" class="hidden animate__animated animate__fadeInUp">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
                        <div class="bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">มาเรียน</span>
                                    <i class="fas fa-check-circle text-2xl"></i>
                                </div>
                                <span id="term-present" class="text-3xl font-bold block">0</span>
                                <span class="text-sm opacity-90">วัน</span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-red-400 to-rose-500 rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">ขาด</span>
                                    <i class="fas fa-times-circle text-2xl"></i>
                                </div>
                                <span id="term-absent" class="text-3xl font-bold block">0</span>
                                <span class="text-sm opacity-90">วัน</span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">สาย</span>
                                    <i class="fas fa-clock text-2xl"></i>
                                </div>
                                <span id="term-late" class="text-3xl font-bold block">0</span>
                                <span class="text-sm opacity-90">วัน</span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">ป่วย</span>
                                    <i class="fas fa-thermometer-half text-2xl"></i>
                                </div>
                                <span id="term-sick" class="text-3xl font-bold block">0</span>
                                <span class="text-sm opacity-90">วัน</span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-400 to-violet-500 rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">กิจ</span>
                                    <i class="fas fa-file-alt text-2xl"></i>
                                </div>
                                <span id="term-activity" class="text-3xl font-bold block">0</span>
                                <span class="text-sm opacity-90">วัน</span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-pink-400 to-fuchsia-500 rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">กิจกรรม</span>
                                    <i class="fas fa-calendar-star text-2xl"></i>
                                </div>
                                <span id="term-event" class="text-3xl font-bold block">0</span>
                                <span class="text-sm opacity-90">วัน</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <div class="glass-effect rounded-2xl p-8 card-hover">
                            <div class="flex items-center mb-6">
                                <div class="w-1 h-8 bg-gradient-to-b from-purple-600 to-blue-600 rounded-full mr-4"></div>
                                <h3 class="text-xl font-semibold text-gray-800">📊 สรุปการเข้าเรียน</h3>
                            </div>
                            <div class="flex justify-center items-center">
                                <div class="relative w-80 h-80">
                                    <canvas id="attendanceChart" width="320" height="320"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="glass-effect rounded-2xl p-8 card-hover">
                            <div class="flex items-center mb-6">
                                <div class="w-1 h-8 bg-gradient-to-b from-green-600 to-emerald-600 rounded-full mr-4"></div>
                                <h3 class="text-xl font-semibold text-gray-800">📈 สถิติการเข้าเรียน</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-l-4 border-green-500">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="font-semibold text-green-800">อัตราการมาเรียน</span>
                                    </div>
                                    <span id="attendanceRate" class="text-2xl font-bold text-green-600">0%</span>
                                </div>
                                <div class="flex justify-between items-center p-4 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border-l-4 border-red-500">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                        <span class="font-semibold text-red-800">อัตราการขาดเรียน</span>
                                    </div>
                                    <span id="absentRate" class="text-2xl font-bold text-red-600">0%</span>
                                </div>
                                <div class="flex justify-between items-center p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border-l-4 border-blue-500">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="font-semibold text-blue-800">วันเรียนทั้งหมด</span>
                                    </div>
                                    <span id="totalSchoolDays" class="text-2xl font-bold text-blue-600">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="glass-effect rounded-2xl p-8 card-hover">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-1 h-8 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full mr-4"></div>
                                <h3 class="text-xl font-semibold text-gray-800">📋 ประวัติการมาเรียน</h3>
                            </div>
                            <button id="exportBtn" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-2 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 flex items-center space-x-2">
                                <i class="fas fa-download"></i>
                                <span>ส่งออกข้อมูล</span>
                            </button>
                        </div>
                        
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                        <tr>
                                            <th class="px-6 py-4 text-center font-semibold">
                                                <i class="fas fa-calendar-alt mr-2"></i>วันที่
                                            </th>
                                            <th class="px-6 py-4 text-center font-semibold">
                                                <i class="fas fa-info-circle mr-2"></i>สถานะ
                                            </th>
                                            <th class="px-6 py-4 text-center font-semibold">
                                                <i class="fas fa-comment mr-2"></i>หมายเหตุ
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendanceTableBody" class="bg-white divide-y divide-gray-200">
                                        <!-- Data will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php require_once('../footer.php'); ?>
</div>

<?php require_once('script.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let attChart;
    
    // Enhanced loading states
    function showLoading() {
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('studentSummary').classList.add('hidden');
    }
    
    function hideLoading() {
        document.getElementById('loadingState').classList.add('hidden');
    }
    
    // Enhanced animations
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = value;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
    // Load classes with enhanced error handling
    fetch('api/api_get_classes.php')
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            const classSelect = document.getElementById('classSelect');
            data.forEach(cls => {
                const opt = document.createElement('option');
                opt.value = cls.Stu_major;
                opt.textContent = 'ม.' + cls.Stu_major;
                classSelect.appendChild(opt);
            });
        })
        .catch(error => {
            console.error('Error loading classes:', error);
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลชั้นเรียนได้', 'error');
        });

    // Enhanced class selection
    document.getElementById('classSelect').addEventListener('change', function() {
        const classVal = this.value;
        const roomSelect = document.getElementById('roomSelect');
        const studentSelect = document.getElementById('studentSelect');
        
        // Reset dependent selects
        roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
        studentSelect.disabled = true;
        document.getElementById('studentSummary').classList.add('hidden');
        
        if (classVal) {
            roomSelect.disabled = false;
            // Add loading state to room select
            roomSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
            
            fetch('api/api_get_rooms.php?class=' + classVal)
                .then(res => res.json())
                .then(data => {
                    roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
                    data.forEach(room => {
                        const opt = document.createElement('option');
                        opt.value = room.Stu_room;
                        opt.textContent = 'ห้อง ' + room.Stu_room;
                        roomSelect.appendChild(opt);
                    });
                })
                .catch(error => {
                    console.error('Error loading rooms:', error);
                    roomSelect.innerHTML = '<option value="">เกิดข้อผิดพลาด</option>';
                });
        } else {
            roomSelect.disabled = true;
        }
    });

    // Enhanced room selection
    document.getElementById('roomSelect').addEventListener('change', function() {
        const classVal = document.getElementById('classSelect').value;
        const roomVal = this.value;
        const studentSelect = document.getElementById('studentSelect');
        
        studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
        document.getElementById('studentSummary').classList.add('hidden');
        
        if (classVal && roomVal) {
            studentSelect.disabled = false;
            studentSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
            
            fetch('api/api_get_students.php?class=' + classVal + '&room=' + roomVal)
                .then(res => res.json())
                .then(data => {
                    studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
                    data.forEach(stu => {
                        const opt = document.createElement('option');
                        opt.value = stu.Stu_id;
                        opt.textContent = stu.Stu_no + ' ' + stu.Stu_pre + stu.Stu_name + ' ' + stu.Stu_sur;
                        studentSelect.appendChild(opt);
                    });
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    studentSelect.innerHTML = '<option value="">เกิดข้อผิดพลาด</option>';
                });
        } else {
            studentSelect.disabled = true;
        }
    });

    // Enhanced student selection with animations
    document.getElementById('studentSelect').addEventListener('change', function() {
        const stuId = this.value;
        if (stuId) {
            showLoading();
            
            fetch('api/ajax_get_student_attendance.php?stu_id=' + stuId)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    hideLoading();
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load data');
                    }
                    
                    // Show summary with animation
                    const summaryDiv = document.getElementById('studentSummary');
                    summaryDiv.classList.remove('hidden');
                    summaryDiv.classList.add('animate__animated', 'animate__fadeInUp');
                    
                    // Animate counter values
                    const summary = data.summary || {};
                    animateValue(document.getElementById('term-present'), 0, summary.present || 0, 1000);
                    animateValue(document.getElementById('term-absent'), 0, summary.absent || 0, 1200);
                    animateValue(document.getElementById('term-late'), 0, summary.late || 0, 1400);
                    animateValue(document.getElementById('term-sick'), 0, summary.sick || 0, 1600);
                    animateValue(document.getElementById('term-activity'), 0, summary.activity || 0, 1800);
                    animateValue(document.getElementById('term-event'), 0, summary.event || 0, 2000);
                    
                    // Calculate statistics
                    const total = (summary.present || 0) + (summary.absent || 0) + (summary.late || 0) + 
                                (summary.sick || 0) + (summary.activity || 0) + (summary.event || 0);
                    const attendanceRate = total > 0 ? Math.round(((summary.present || 0) / total) * 100) : 0;
                    const absentRate = total > 0 ? Math.round(((summary.absent || 0) / total) * 100) : 0;
                    
                    setTimeout(() => {
                        document.getElementById('attendanceRate').textContent = attendanceRate + '%';
                        document.getElementById('absentRate').textContent = absentRate + '%';
                        document.getElementById('totalSchoolDays').textContent = total;
                    }, 500);
                    
                    // Enhanced table with animations
                    const tbody = document.getElementById('attendanceTableBody');
                    tbody.innerHTML = '';
                    
                    const records = data.records || [];
                    if (records.length > 0) {
                        records.forEach((row, i) => {
                            setTimeout(() => {
                                const tr = document.createElement('tr');
                                tr.className = `animate__animated animate__fadeInUp hover:bg-gray-50 transition-all duration-300 ${i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}`;
                                tr.style.animationDelay = `${i * 0.1}s`;
                                
                                const date = row.attendance_date ? thaiDate(row.attendance_date) : '-';
                                const status = row.status_text ? 
                                    `<span class="${row.status_color || 'text-gray-600'} font-bold flex items-center justify-center space-x-2">
                                        <span>${row.status_emoji || ''}</span>
                                        <span>${row.status_text}</span>
                                    </span>` : '-';
                                const reason = row.reason ? row.reason : '-';
                                
                                tr.innerHTML = `
                                    <td class="px-6 py-4 text-center font-medium">${date}</td>
                                    <td class="px-6 py-4 text-center">${status}</td>
                                    <td class="px-6 py-4 text-center text-gray-600">${reason}</td>
                                `;
                                tbody.appendChild(tr);
                            }, i * 100);
                        });
                    } else {
                        tbody.innerHTML = `
                            <tr class="animate__animated animate__fadeIn">
                                <td colspan="3" class="text-center py-12">
                                    <div class="flex flex-col items-center space-y-4">
                                        <i class="fas fa-inbox text-6xl text-gray-300"></i>
                                        <p class="text-gray-500 text-lg">ไม่พบข้อมูลการมาเรียน</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                    
                    // Enhanced chart
                    if (attChart) attChart.destroy();
                    const ctx = document.getElementById('attendanceChart').getContext('2d');
                    
                    attChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['มาเรียน', 'ขาด', 'สาย', 'ป่วย', 'กิจ', 'กิจกรรม'],
                            datasets: [{
                                data: [
                                    summary.present || 0,
                                    summary.absent || 0,
                                    summary.late || 0,
                                    summary.sick || 0,
                                    summary.activity || 0,
                                    summary.event || 0
                                ],
                                backgroundColor: [
                                    '#10b981', // เขียว - มาเรียน
                                    '#ef4444', // แดง - ขาด
                                    '#f59e0b', // เหลือง - สาย
                                    '#3b82f6', // น้ำเงิน - ป่วย
                                    '#8b5cf6', // ม่วง - กิจ
                                    '#ec4899'  // ชมพู - กิจกรรม
                                ],
                                borderWidth: 3,
                                borderColor: '#ffffff',
                                hoverBorderWidth: 5,
                                hoverBorderColor: '#ffffff',
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            cutout: '50%',
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        font: { 
                                            size: 13,
                                            family: 'Sarabun'
                                        },
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                return data.labels.map((label, i) => {
                                                    const dataset = data.datasets[0];
                                                    const value = dataset.data[i];
                                                    const total = dataset.data.reduce((a, b) => a + b, 0);
                                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                                    
                                                    return {
                                                        text: `${label}: ${value} (${percentage}%)`,
                                                        fillStyle: dataset.backgroundColor[i],
                                                        hidden: false,
                                                        index: i
                                                    };
                                                });
                                            }
                                            return [];
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label;
                                            const value = context.parsed;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                            return `${label}: ${value} วัน (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 1500,
                                easing: 'easeOutCubic'
                            },
                            elements: {
                                arc: {
                                    borderWidth: 2
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error loading attendance data:', error);
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลการเข้าเรียนได้: ' + error.message, 'error');
                });
        } else {
            document.getElementById('studentSummary').classList.add('hidden');
        }
    });
    
    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        const stuId = document.getElementById('studentSelect').value;
        if (stuId) {
            window.open(`api/export_attendance.php?stu_id=${stuId}`, '_blank');
        } else {
            Swal.fire('แจ้งเตือน', 'กรุณาเลือกนักเรียนก่อน', 'warning');
        }
    });

    // Enhanced Thai date function
    function thaiDate(strDate) {
        if (!strDate) return '-';
        const months = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
            "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
        const d = new Date(strDate);
        const day = d.getDate();
        const month = months[d.getMonth() + 1];
        const year = d.getFullYear();
        if (isNaN(day) || !month || isNaN(year)) return strDate;
        return `${day} ${month} ${year}`;
    }
});
</script>
</body>
</html>
