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
    
    .visit-status-complete {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .visit-status-pending {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    
    .visit-status-overdue {
        background: linear-gradient(135deg, #ef4444, #dc2626);
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
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-orange-600 to-red-600 rounded-full mb-4 float-animation">
                                        <i class="fas fa-home text-white text-2xl"></i>
                                    </div>
                                    <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                                        🏠 รายงานการเยี่ยมบ้านรายห้อง
                                    </h1>
                                    <p class="text-gray-600 mt-2">ติดตามและจัดการการเยี่ยมบ้านนักเรียนในแต่ละห้องเรียน</p>
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
                        <div class="w-1 h-8 bg-gradient-to-b from-orange-600 to-red-600 rounded-full mr-4"></div>
                        <h3 class="text-xl font-semibold text-gray-800">🔍 เลือกห้องเรียนที่ต้องการดู</h3>
                    </div>
                    
                    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-layer-group text-orange-500 mr-2"></i>ระดับชั้น
                            </label>
                            <div class="relative">
                                <select id="classSelect" name="class" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-200 transition-all duration-300 appearance-none bg-white">
                                    <option value="">-- เลือกชั้น --</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-door-open text-red-500 mr-2"></i>ห้องเรียน
                            </label>
                            <div class="relative">
                                <select id="roomSelect" name="room" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-200 transition-all duration-300 appearance-none bg-white" disabled>
                                    <option value="">-- เลือกห้อง --</option>
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="loading-skeleton h-32 rounded-xl"></div>
                        <div class="loading-skeleton h-32 rounded-xl"></div>
                        <div class="loading-skeleton h-32 rounded-xl"></div>
                    </div>
                </div>

                <!-- Visit Summary -->
                <div id="visitSummary" class="hidden animate__animated animate__fadeInUp">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="visit-status-complete rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">เยี่ยมแล้ว</span>
                                    <i class="fas fa-check-circle text-3xl"></i>
                                </div>
                                <span id="visited-count" class="text-4xl font-bold block">0</span>
                                <span class="text-sm opacity-90">ครอบครัว</span>
                            </div>
                        </div>

                        <div class="visit-status-pending rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">รอเยี่ยม</span>
                                    <i class="fas fa-clock text-3xl"></i>
                                </div>
                                <span id="pending-count" class="text-4xl font-bold block">0</span>
                                <span class="text-sm opacity-90">ครอบครัว</span>
                            </div>
                        </div>

                        <div class="visit-status-overdue rounded-2xl p-6 text-white card-hover relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-semibold">เกินกำหนด</span>
                                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                                </div>
                                <span id="overdue-count" class="text-4xl font-bold block">0</span>
                                <span class="text-sm opacity-90">ครอบครัว</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chart and Progress Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <div class="glass-effect rounded-2xl p-8 card-hover">
                            <div class="flex items-center mb-6">
                                <div class="w-1 h-8 bg-gradient-to-b from-orange-600 to-red-600 rounded-full mr-4"></div>
                                <h3 class="text-xl font-semibold text-gray-800">📊 สถิติการเยี่ยมบ้าน</h3>
                            </div>
                            <div class="flex justify-center items-center">
                                <div class="relative w-80 h-80">
                                    <canvas id="visitChart" width="320" height="320"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="glass-effect rounded-2xl p-8 card-hover">
                            <div class="flex items-center mb-6">
                                <div class="w-1 h-8 bg-gradient-to-b from-green-600 to-emerald-600 rounded-full mr-4"></div>
                                <h3 class="text-xl font-semibold text-gray-800">📈 ความคืบหน้า</h3>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-semibold text-gray-700">ความสำเร็จรอบ 1</span>
                                        <span id="round1-percentage" class="text-lg font-bold text-green-600">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div id="round1-progress" class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-1000" style="width: 0%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-semibold text-gray-700">ความสำเร็จรอบ 2</span>
                                        <span id="round2-percentage" class="text-lg font-bold text-blue-600">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div id="round2-progress" class="bg-gradient-to-r from-blue-500 to-cyan-500 h-3 rounded-full transition-all duration-1000" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-4 border-l-4 border-orange-500">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-orange-800">ความสำเร็จรวม</span>
                                        <span id="total-percentage" class="text-2xl font-bold text-orange-600">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div class="glass-effect rounded-2xl p-8 card-hover">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-1 h-8 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full mr-4"></div>
                                <h3 class="text-xl font-semibold text-gray-800">👥 รายชื่อนักเรียนและสถานะการเยี่ยมบ้าน</h3>
                            </div>
                            <div class="flex space-x-2">

                                <button id="exportBtn" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-2 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 flex items-center space-x-2">
                                    <i class="fas fa-download"></i>
                                    <span>ส่งออกข้อมูล</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                        <tr>
                                            <th class="px-6 py-4 text-center font-semibold">เลขที่</th>
                                            <th class="px-6 py-4 text-left font-semibold">ชื่อ - นามสกุล</th>
                                            <th class="px-6 py-4 text-center font-semibold">รอบที่ 1</th>
                                            <th class="px-6 py-4 text-center font-semibold">รอบที่ 2</th>
                                            <th class="px-6 py-4 text-center font-semibold">สถานะ</th>
                                            <th class="px-6 py-4 text-center font-semibold">การจัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentsTableBody" class="bg-white divide-y divide-gray-200">
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

<!-- Visit Detail Modal -->
<div class="modal fade" id="visitDetailModal" tabindex="-1" role="dialog" aria-labelledby="visitDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-to-r from-orange-500 to-red-600 text-white">
                <h5 class="modal-title font-bold" id="visitDetailModalLabel">
                    <i class="fas fa-home mr-2"></i>รายละเอียดการเยี่ยมบ้าน
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="visitDetailBody">
                <!-- Visit details will be loaded here -->
            </div>
            <div class="modal-footer bg-gray-50">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<?php require_once('script.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let visitChart;
    
    // Enhanced loading states
    function showLoading() {
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('visitSummary').classList.add('hidden');
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
    
    function animateProgress(element, targetWidth, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const width = progress * targetWidth;
            element.style.width = width + '%';
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
        
        // Reset dependent selects
        roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        document.getElementById('visitSummary').classList.add('hidden');
        
        if (classVal) {
            roomSelect.disabled = false;
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
        
        if (classVal && roomVal) {
            showLoading();
            
            fetch(`api/ajax_get_class_visit_home.php?class=${classVal}&room=${roomVal}`)
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    
                    // Show summary with animation
                    const summaryDiv = document.getElementById('visitSummary');
                    summaryDiv.classList.remove('hidden');
                    summaryDiv.classList.add('animate__animated', 'animate__fadeInUp');
                    
                    // Animate counter values
                    animateValue(document.getElementById('visited-count'), 0, data.summary.visited, 1000);
                    animateValue(document.getElementById('pending-count'), 0, data.summary.pending, 1200);
                    animateValue(document.getElementById('overdue-count'), 0, data.summary.overdue, 1400);
                    
                    // Calculate progress percentages
                    const totalStudents = data.summary.total || 1;
                    const round1Percentage = Math.round((data.summary.round1_completed / totalStudents) * 100);
                    const round2Percentage = Math.round((data.summary.round2_completed / totalStudents) * 100);
                    const totalPercentage = Math.round((data.summary.visited / totalStudents) * 100);
                    
                    // Animate progress bars
                    setTimeout(() => {
                        document.getElementById('round1-percentage').textContent = round1Percentage + '%';
                        document.getElementById('round2-percentage').textContent = round2Percentage + '%';
                        document.getElementById('total-percentage').textContent = totalPercentage + '%';
                        
                        animateProgress(document.getElementById('round1-progress'), round1Percentage, 1500);
                        animateProgress(document.getElementById('round2-progress'), round2Percentage, 1700);
                    }, 500);
                    
                    // Enhanced table with animations
                    const tbody = document.getElementById('studentsTableBody');
                    tbody.innerHTML = '';
                    
                    if (data.students.length > 0) {
                        data.students.forEach((student, i) => {
                            setTimeout(() => {
                                const tr = document.createElement('tr');
                                tr.className = `animate__animated animate__fadeInUp hover:bg-gray-50 transition-all duration-300 ${i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}`;
                                tr.style.animationDelay = `${i * 0.1}s`;
                                
                                const round1Status = getVisitStatus(student.round1_visit);
                                const round2Status = getVisitStatus(student.round2_visit);
                                const overallStatus = getOverallStatus(student.round1_visit, student.round2_visit);
                                
                                tr.innerHTML = `
                                    <td class="px-6 py-4 text-center font-bold text-gray-700">${student.Stu_no}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                                ${student.Stu_name.charAt(0)}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">${student.Stu_pre}${student.Stu_name} ${student.Stu_sur}</div>
                                                <div class="text-sm text-gray-500">${student.Stu_id}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">${round1Status}</td>
                                    <td class="px-6 py-4 text-center">${round2Status}</td>
                                    <td class="px-6 py-4 text-center">${overallStatus}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button class="btn-view-visit bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm transition-all duration-300" data-student-id="${student.Stu_id}" title="ดูรายละเอียด">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        
                                        </div>
                                    </td>
                                `;
                                tbody.appendChild(tr);
                            }, i * 100);
                        });
                    } else {
                        tbody.innerHTML = `
                            <tr class="animate__animated animate__fadeIn">
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center space-y-4">
                                        <i class="fas fa-users text-6xl text-gray-300"></i>
                                        <p class="text-gray-500 text-lg">ไม่พบข้อมูลนักเรียน</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                    
                    // Enhanced chart
                    if (visitChart) visitChart.destroy();
                    const ctx = document.getElementById('visitChart').getContext('2d');
                    
                    visitChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['เยี่ยมแล้ว', 'รอเยี่ยม', 'เกินกำหนด'],
                            datasets: [{
                                data: [
                                    data.summary.visited,
                                    data.summary.pending,
                                    data.summary.overdue
                                ],
                                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
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
                                            return `${label}: ${value} ครอบครัว (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 1500,
                                easing: 'easeOutCubic'
                            }
                        }
                    });
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error loading visit data:', error);
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลการเยี่ยมบ้านได้', 'error');
                });
        } else {
            document.getElementById('visitSummary').classList.add('hidden');
        }
    });
    
    // Helper functions for status display
    function getVisitStatus(visitData) {
        if (!visitData || !visitData.visit_date) {
            return '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">ยังไม่เยี่ยม</span>';
        }
        
        const visitDate = new Date(visitData.visit_date);
        const formattedDate = visitDate.toLocaleDateString('th-TH');
        
        return `<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-check mr-1"></i>${formattedDate}
                </span>`;
    }
    
    function getOverallStatus(round1, round2) {
        const hasRound1 = round1 && round1.visit_date;
        const hasRound2 = round2 && round2.visit_date;
        
        if (hasRound1 && hasRound2) {
            return '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold"><i class="fas fa-check-double mr-1"></i>เสร็จสิ้น</span>';
        } else if (hasRound1 || hasRound2) {
            return '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold"><i class="fas fa-clock mr-1"></i>กำลังดำเนิน</span>';
        } else {
            return '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold"><i class="fas fa-exclamation mr-1"></i>ยังไม่เริ่ม</span>';
        }
    }
    
    // Event handlers for buttons
    $(document).on('click', '.btn-view-visit', function() {
        const studentId = $(this).data('student-id');
        const classVal = document.getElementById('classSelect').value;
        const roomVal = document.getElementById('roomSelect').value;
        
        // Show loading
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Load visit details using the same method as visithome.php
        $.ajax({
            url: 'api/get_visit_details_full.php',
            method: 'GET',
            data: { 
                student_id: studentId, 
                class: classVal, 
                room: roomVal 
            },
            success: function(response) {
                Swal.close();
                document.getElementById('visitDetailBody').innerHTML = response;
                $('#visitDetailModal').modal('show');
            },
            error: function() {
                Swal.close();
                console.error('Error loading visit details');
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดรายละเอียดการเยี่ยมได้', 'error');
            }
        });
    });
    
    $(document).on('click', '.btn-edit-visit', function() {
        const studentId = $(this).data('student-id');
        // Redirect to visit form
        window.location.href = `visit_home_form.php?student_id=${studentId}`;
    });
    
    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        const classVal = document.getElementById('classSelect').value;
        const roomVal = document.getElementById('roomSelect').value;
        
        if (classVal && roomVal) {
            window.open(`api/export_visit_home.php?class=${classVal}&room=${roomVal}`, '_blank');
        } else {
            Swal.fire('แจ้งเตือน', 'กรุณาเลือกชั้นและห้องเรียนก่อน', 'warning');
        }
    });
    
    // Add visit functionality
    document.getElementById('addVisitBtn').addEventListener('click', function() {
        const classVal = document.getElementById('classSelect').value;
        const roomVal = document.getElementById('roomSelect').value;
        
        if (classVal && roomVal) {
            window.location.href = `visit_home_form.php?class=${classVal}&room=${roomVal}`;
        } else {
            Swal.fire('แจ้งเตือน', 'กรุณาเลือกชั้นและห้องเรียนก่อน', 'warning');
        }
    });
});
</script>
</body>
</html>
