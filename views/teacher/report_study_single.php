<?php
/**
 * Teacher Report Study Single View
 * Modern UI with Tailwind CSS & Glassmorphism
 */
ob_start();
?>

<!-- Custom Styles for Report Study Single Page -->
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
    
    .gradient-header {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .glass-effect {
        background: rgba(30, 41, 59, 0.9);
        border-color: rgba(255, 255, 255, 0.05);
    }
    
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
        background-size: 400% 100%;
        animation: skeleton-loading 1.4s ease infinite;
    }

    .dark .loading-skeleton {
        background: linear-gradient(90deg, #1e293b 25%, #334155 37%, #1e293b 63%);
    }
    
    @keyframes skeleton-loading {
        0% { background-position: 100% 50%; }
        100% { background-position: -100% 50%; }
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-weight: 800;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<!-- Core Libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto py-8">
    <!-- Header Section -->
    <div class="mb-10 animate__animated animate__fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-12 relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 text-center md:text-left flex flex-col md:flex-row items-center gap-8">
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-indigo-500/30 float-animation">
                        <i class="fas fa-calendar-check text-4xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl md:text-5xl font-black text-slate-800 dark:text-white mb-4">
                        รายงานเวลาเรียนรายบุคคล
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 font-medium">
                        ติดตามสถิติการมาเรียน ขาด สาย ลากิจ และลาป่วย ของนักเรียนรายบุคคลอย่างเป็นระบบ
                    </p>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="px-6 py-3 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                        <span class="text-xs font-bold text-slate-400 block uppercase">ปีการศึกษา/เทอม</span>
                        <span class="text-lg font-black text-indigo-600"><?php echo $pee; ?>/<?php echo $term; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 mb-10 shadow-xl border border-white/50 dark:border-slate-700/50 animate__animated animate__fadeIn" style="animation-delay: 0.1s">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner">
                <i class="fas fa-search text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">เลือกนักเรียน</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">กรองข้อมูลตามชั้น ห้อง และชื่อนักเรียน</p>
            </div>
        </div>
        
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-2 uppercase tracking-wider">
                    <i class="fas fa-layer-group text-purple-500 mr-2"></i> ระดับชั้น
                </label>
                <div class="relative">
                    <select id="classSelect" name="class" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold">
                        <option value="">-- เลือกชั้น --</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-2 uppercase tracking-wider">
                    <i class="fas fa-door-open text-blue-500 mr-2"></i> ห้องเรียน
                </label>
                <div class="relative">
                    <select id="roomSelect" name="room" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold" disabled>
                        <option value="">-- เลือกห้อง --</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-2 uppercase tracking-wider">
                    <i class="fas fa-user-graduate text-emerald-500 mr-2"></i> นักเรียน
                </label>
                <div class="relative">
                    <select id="studentSelect" name="student" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold" disabled>
                        <option value="">-- เลือกนักเรียน --</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="hidden animate-pulse">
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-10">
            <?php for($i=0; $i<6; $i++): ?>
                <div class="loading-skeleton h-32 rounded-3xl shadow-sm"></div>
            <?php endfor; ?>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <div class="loading-skeleton h-96 rounded-[2.5rem]"></div>
            <div class="loading-skeleton h-96 rounded-[2.5rem]"></div>
        </div>
    </div>

    <!-- Student Summary Implementation -->
    <div id="studentSummary" class="hidden animate__animated animate__fadeInUp">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
            <!-- Present -->
            <div class="card-hover bg-gradient-to-br from-emerald-400 to-teal-500 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80 mb-1">มาเรียน</div>
                    <div class="flex items-baseline gap-2">
                        <span id="term-present" class="text-3xl font-black">0</span>
                        <span class="text-sm font-bold">วัน</span>
                    </div>
                </div>
            </div>

            <!-- Absent -->
            <div class="card-hover bg-gradient-to-br from-rose-400 to-red-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-times text-xl"></i>
                    </div>
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80 mb-1">ขาดเรียน</div>
                    <div class="flex items-baseline gap-2">
                        <span id="term-absent" class="text-3xl font-black">0</span>
                        <span class="text-sm font-bold">วัน</span>
                    </div>
                </div>
            </div>

            <!-- Late -->
            <div class="card-hover bg-gradient-to-br from-amber-400 to-orange-500 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80 mb-1">มาสาย</div>
                    <div class="flex items-baseline gap-2">
                        <span id="term-late" class="text-3xl font-black">0</span>
                        <span class="text-sm font-bold">วัน</span>
                    </div>
                </div>
            </div>

            <!-- Sick -->
            <div class="card-hover bg-gradient-to-br from-blue-400 to-indigo-500 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-thermometer-half text-xl"></i>
                    </div>
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80 mb-1">ลาป่วย</div>
                    <div class="flex items-baseline gap-2">
                        <span id="term-sick" class="text-3xl font-black">0</span>
                        <span class="text-sm font-bold">วัน</span>
                    </div>
                </div>
            </div>

            <!-- Event/Activity (Business Leave) -->
            <div class="card-hover bg-gradient-to-br from-purple-400 to-violet-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-file-signature text-xl"></i>
                    </div>
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80 mb-1">ลากิจ</div>
                    <div class="flex items-baseline gap-2">
                        <span id="term-activity" class="text-3xl font-black">0</span>
                        <span class="text-sm font-bold">วัน</span>
                    </div>
                </div>
            </div>

            <!-- Special Activities -->
            <div class="card-hover bg-gradient-to-br from-pink-400 to-fuchsia-500 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80 mb-1">กิจกรรม</div>
                    <div class="flex items-baseline gap-2">
                        <span id="term-event" class="text-3xl font-black">0</span>
                        <span class="text-sm font-bold">วัน</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <!-- Chart Card -->
            <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 card-hover">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-chart-pie text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white">ภาพรวมสัดส่วน</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">เปรียบเทียบข้อมูลการมาเรียนทั้งหมด</p>
                    </div>
                </div>
                <div class="flex justify-center items-center py-4">
                    <div class="relative w-full max-w-[320px]">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Statistics Breakdown -->
            <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 card-hover">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-list-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white">สถิติรวม</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">สรุปภาพรวมการเข้าเรียนเชิงร้อยละ</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="p-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-[2rem] border border-emerald-100 dark:border-emerald-800/50 group transition-all">
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                                <i class="fas fa-circle text-[8px]"></i> อัตราการมาเรียน
                            </span>
                            <span id="attendanceRate" class="text-3xl font-black text-emerald-600">0%</span>
                        </div>
                        <div class="w-full h-3 bg-white dark:bg-slate-800 rounded-full overflow-hidden shadow-inner flex">
                            <div id="attendanceRateBar" class="bg-gradient-to-r from-emerald-400 to-teal-500 h-full rounded-full transition-all duration-1000" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="p-6 bg-rose-50 dark:bg-rose-900/20 rounded-[2rem] border border-rose-100 dark:border-rose-800/50">
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-bold text-rose-700 dark:text-rose-400 flex items-center gap-2">
                                <i class="fas fa-circle text-[8px]"></i> อัตราการขาดเรียน
                            </span>
                            <span id="absentRate" class="text-3xl font-black text-rose-600">0%</span>
                        </div>
                        <div class="w-full h-3 bg-white dark:bg-slate-800 rounded-full overflow-hidden shadow-inner flex">
                            <div id="absentRateBar" class="bg-gradient-to-r from-rose-400 to-red-600 h-full rounded-full transition-all duration-1000" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-[2rem] border border-indigo-100 dark:border-indigo-800/50 text-center">
                            <div class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-1">วันเรียนทั้งหมด</div>
                            <div id="totalSchoolDays" class="text-3xl font-black text-indigo-600">0</div>
                        </div>
                        <div class="p-6 bg-slate-50 dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 text-center">
                            <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">ข้อมูลนับถึงวันที่</div>
                            <div class="text-lg font-black text-slate-700 dark:text-slate-300"><?php echo date('d/m/') . (date('Y')+543); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 mb-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-clock-rotate-left text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white">ประวัติเวลาเรียน</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">รายการข้อมูลการเช็คชื่อย้อนหลัง</p>
                    </div>
                </div>
                <button id="exportBtn" class="px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-file-excel"></i>
                    <span>ส่งออกข้อมูล EXCEL</span>
                </button>
            </div>

            <!-- Timeline View (Card Based) -->
            <div id="attendanceGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Data will be inserted here by JS -->
                <div class="col-span-full text-center py-20 text-slate-400 font-bold">
                    <i class="fas fa-search text-6xl mb-4 opacity-20 block"></i>
                    กรุณาเลือกนักเรียนเพื่อดูประวัติ
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript will use Chart.js already loaded above -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let attChart;
    
    // UI Helpers
    function showLoading() {
        $('#loadingState').removeClass('hidden');
        $('#studentSummary').addClass('hidden');
    }
    
    function hideLoading() {
        $('#loadingState').addClass('hidden');
    }
    
    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // API Integration: Loading Classes
    fetch('api/api_get_classes.php')
        .then(res => res.json())
        .then(data => {
            const classSelect = document.getElementById('classSelect');
            if (data) {
                data.forEach(cls => {
                    const opt = document.createElement('option');
                    opt.value = cls.Stu_major;
                    opt.textContent = 'มัธยมศึกษาปีที่ ' + cls.Stu_major;
                    classSelect.appendChild(opt);
                });
            }
        });

    // Handle Class Change
    document.getElementById('classSelect').addEventListener('change', function() {
        const classVal = this.value;
        const roomSelect = document.getElementById('roomSelect');
        const studentSelect = document.getElementById('studentSelect');
        
        roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
        roomSelect.disabled = true;
        studentSelect.disabled = true;
        $('#studentSummary').addClass('hidden');
        
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
                });
        }
    });

    // Handle Room Change
    document.getElementById('roomSelect').addEventListener('change', function() {
        const classVal = document.getElementById('classSelect').value;
        const roomVal = this.value;
        const studentSelect = document.getElementById('studentSelect');
        
        studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
        studentSelect.disabled = true;
        $('#studentSummary').addClass('hidden');
        
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
                        opt.textContent = `[No.${stu.Stu_no}] ${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}`;
                        studentSelect.appendChild(opt);
                    });
                });
        }
    });

    // Handle Student Selection (The Heart)
    document.getElementById('studentSelect').addEventListener('change', function() {
        const stuId = this.value;
        if (!stuId) {
            $('#studentSummary').addClass('hidden');
            return;
        }

        showLoading();
        
        fetch('api/ajax_get_student_attendance.php?stu_id=' + stuId)
            .then(res => res.json())
            .then(data => {
                hideLoading();
                
                $('#studentSummary').removeClass('hidden');
                
                // Data Mapping
                const summary = data.summary || {};
                const present = parseInt(summary.present || 0);
                const absent = parseInt(summary.absent || 0);
                const late = parseInt(summary.late || 0);
                const sick = parseInt(summary.sick || 0);
                const activity = parseInt(summary.activity || 0);
                const event = parseInt(summary.event || 0);
                const total = present + absent + late + sick + activity + event;

                // Animate Numbers
                animateValue(document.getElementById('term-present'), 0, present, 1000);
                animateValue(document.getElementById('term-absent'), 0, absent, 1000);
                animateValue(document.getElementById('term-late'), 0, late, 1000);
                animateValue(document.getElementById('term-sick'), 0, sick, 1000);
                animateValue(document.getElementById('term-activity'), 0, activity, 1000);
                animateValue(document.getElementById('term-event'), 0, event, 1000);
                animateValue(document.getElementById('totalSchoolDays'), 0, total, 1000);

                // Progress Bars & Rates
                const attRate = total > 0 ? Math.round((present / total) * 100) : 0;
                const absRate = total > 0 ? Math.round((absent / total) * 100) : 0;
                
                document.getElementById('attendanceRate').textContent = `${attRate}%`;
                document.getElementById('attendanceRateBar').style.width = `${attRate}%`;
                document.getElementById('absentRate').textContent = `${absRate}%`;
                document.getElementById('absentRateBar').style.width = `${absRate}%`;

                // Render Cards
                const grid = document.getElementById('attendanceGrid');
                grid.innerHTML = '';
                
                if(data.records && data.records.length > 0) {
                    data.records.forEach((row, i) => {
                        // Status styling from API
                        const statusText = row.status_text || 'ไม่ระบุ';
                        const statusEmoji = row.status_emoji || '';
                        
                        let dotColor = 'bg-slate-400';
                        let cardBorder = 'border-slate-100';
                        let badgeClass = 'bg-slate-100 text-slate-700';

                        if (statusText === 'มาเรียน') {
                            dotColor = 'bg-emerald-500';
                            cardBorder = 'border-emerald-100';
                            badgeClass = 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
                        } else if (statusText === 'ขาดเรียน') {
                            dotColor = 'bg-rose-500';
                            cardBorder = 'border-rose-100';
                            badgeClass = 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400';
                        } else if (statusText === 'มาสาย') {
                            dotColor = 'bg-amber-500';
                            cardBorder = 'border-amber-100';
                            badgeClass = 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
                        } else if (statusText === 'ลาป่วย') {
                            dotColor = 'bg-blue-500';
                            cardBorder = 'border-blue-100';
                            badgeClass = 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
                        } else if (statusText === 'ลากิจ') {
                            dotColor = 'bg-purple-500';
                            cardBorder = 'border-purple-100';
                            badgeClass = 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400';
                        } else if (statusText === 'เข้าร่วมกิจกรรม') {
                            dotColor = 'bg-pink-500';
                            cardBorder = 'border-pink-100';
                            badgeClass = 'bg-pink-50 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400';
                        }

                        const card = document.createElement('div');
                        card.className = `group animate__animated animate__fadeInUp bg-white dark:bg-slate-800/50 rounded-3xl p-6 border-2 ${cardBorder} dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 shadow-sm hover:shadow-xl transition-all duration-300`;
                        card.style.animationDelay = `${i * 0.05}s`;
                        
                        card.innerHTML = `
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 font-black">
                                        ${i + 1}
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-0.5">วันที่เช็คชื่อ</div>
                                        <div class="text-[15px] font-black text-slate-700 dark:text-white">${thaiDate(row.attendance_date)}</div>
                                    </div>
                                </div>
                                <div class="w-2 h-2 rounded-full ${dotColor} animate-pulse"></div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 rounded-2xl ${badgeClass}">
                                    <span class="text-sm font-black uppercase tracking-wider">สถานะการมาเรียน</span>
                                    <span class="flex items-center gap-2 font-black">
                                        <span>${statusEmoji}</span>
                                        <span>${statusText}</span>
                                    </span>
                                </div>
                                
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-700">
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">หมายเหตุหลักฐาน</div>
                                    <div class="text-sm text-slate-500 dark:text-slate-400 italic">
                                        ${row.reason || '<span class="opacity-30">ไม่มีหมายเหตุ</span>'}
                                    </div>
                                </div>
                            </div>
                        `;
                        grid.appendChild(card);
                    });
                } else {
                    grid.innerHTML = '<div class="col-span-full text-center py-20 text-slate-400 font-bold">ไม่พบข้อมูลประวัติ</div>';
                }

                // Render Chart
                renderChart([present, absent, late, sick, activity, event]);
            });
    });

    function renderChart(dataValues) {
        if (attChart) attChart.destroy();
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        attChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['มาเรียน', 'ขาด', 'สาย', 'ป่วย', 'ลากิจ', 'กิจกรรม'],
                datasets: [{
                    data: dataValues,
                    backgroundColor: ['#10b981', '#f43f5e', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'],
                    borderWidth: 8,
                    borderColor: 'white',
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Export Excel
    document.getElementById('exportBtn').addEventListener('click', function() {
        const stuId = document.getElementById('studentSelect').value;
        if(stuId) {
            window.open('api/export_attendance_single.php?stu_id=' + stuId, '_blank');
        }
    });

    function thaiDate(strDate) {
        const months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
        const d = new Date(strDate);
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
