<?php
/**
 * Teacher Report Behavior Single View
 * Modern UI with Tailwind CSS & Glassmorphism
 */
ob_start();
?>

<!-- Custom Styles for Behavior Report -->
<style>
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    
    .float-animation {
        animation: float 3s ease-in-out infinite;
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
    
    .card-hover {
        transition: all-all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .circle-progress {
        transition: stroke-dashoffset 1s ease-out;
    }

    /* Print Styles */
    @media print {
        body { background: white !important; font-size: 12pt; color: black !important; }
        .no-print, nav, aside, footer, #filterForm, .glass-effect:not(#behaviorContainer *), 
        button:not(.print-specific), .sidebar, .navbar, .mb-10:has(#filterForm),
        .absolute, .float-animation { 
            display: none !important; 
        }
        .max-w-7xl { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
        #behaviorContainer { display: block !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .glass-effect { border: none !important; box-shadow: none !important; background: white !important; backdrop-filter: none !important; }
        .rounded-\[2\.5rem\], .rounded-3xl, .rounded-2xl { border-radius: 0.5rem !important; border: 1px solid #e2e8f0 !important; }
        .bg-amber-500, .bg-rose-500, .bg-teal-500, .bg-emerald-500, .text-amber-500, .text-rose-600, .text-teal-600 { 
            -webkit-print-color-adjust: exact; print-color-adjust: exact; 
        }
        .grid { display: block !important; }
        .lg\:grid-cols-3 { display: grid !important; grid-template-cols: 1fr 2fr !important; gap: 20px !important; }
        .mb-10 { margin-bottom: 2rem !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #cbd5e1 !important; padding: 8px !important; }
    }
</style>

<div class="max-w-7xl mx-auto py-8">
    <!-- Header Section -->
    <div class="mb-10 animate__animated animate__fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-12 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 text-center md:text-left flex flex-col md:flex-row items-center gap-8">
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 bg-gradient-to-br from-amber-400 to-orange-600 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-amber-500/30 float-animation">
                        <i class="fas fa-star text-4xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl md:text-5xl font-black text-slate-800 dark:text-white mb-4 tracking-tight">
                        คะแนนพฤติกรรมรายบุคคล
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 font-medium">
                        ติดตามและวิเคราะห์คะแนนความประพฤติ รวมถึงประวัติการหักคะแนนอย่างละเอียด
                    </p>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="px-6 py-3 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                        <span class="text-xs font-bold text-slate-400 block uppercase mb-1">ปีการศึกษา/ภาคเรียน</span>
                        <span class="text-lg font-black text-amber-600 tracking-wider"><?php echo $pee; ?>/<?php echo $term; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 mb-10 shadow-xl border border-white/50 dark:border-slate-700/50 animate__animated animate__fadeIn" style="animation-delay: 0.1s">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner">
                <i class="fas fa-search text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">เลือกนักเรียน</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">ระบุชั้น ห้อง และชื่อนักเรียนที่ต้องการดูประวัติ</p>
            </div>
        </div>
        
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-2 uppercase tracking-wider">
                    <i class="fas fa-layer-group text-amber-500 mr-2"></i> ระดับชั้น
                </label>
                <div class="relative">
                    <select id="classSelect" name="class" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 dark:focus:ring-amber-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold">
                        <option value="">-- เลือกชั้น --</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-2 uppercase tracking-wider">
                    <i class="fas fa-door-open text-orange-500 mr-2"></i> ห้องเรียน
                </label>
                <div class="relative">
                    <select id="roomSelect" name="room" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 dark:focus:ring-amber-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold" disabled>
                        <option value="">-- เลือกห้อง --</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-2 uppercase tracking-wider">
                    <i class="fas fa-user-graduate text-yellow-500 mr-2"></i> เลือกนักเรียน
                </label>
                <div class="relative">
                    <select id="studentSelect" name="student" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 dark:focus:ring-amber-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold" disabled>
                        <option value="">-- เลือกนักเรียน --</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Results implementation -->
    <div id="behaviorContainer" class="hidden animate__animated animate__fadeIn">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Score Card (Remaining) -->
            <div class="lg:col-span-1 glass-effect rounded-[2.5rem] p-10 shadow-xl border-t-8 border-amber-500 flex flex-col items-center justify-center text-center">
                <p class="text-[10px] font-black text-amber-600 uppercase tracking-[0.2em] mb-8">คะแนนพฤติกรรมคงเหลือ</p>
                
                <div class="relative w-48 h-48 mb-6">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle class="text-slate-100 dark:text-slate-800" stroke-width="12" stroke="currentColor" fill="transparent" r="80" cx="96" cy="96" />
                        <circle id="scoreProgress" class="circle-progress text-amber-500" stroke-width="12" stroke-dasharray="502.6" stroke-dashoffset="502.6" stroke-linecap="round" stroke="currentColor" fill="transparent" r="80" cx="96" cy="96" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span id="remainingScore" class="text-6xl font-black text-slate-800 dark:text-white tracking-tighter">100</span>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">เต็ม 100</span>
                    </div>
                </div>

                <div id="scoreStatus" class="px-6 py-2 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-widest">
                    <i class="fas fa-check-circle mr-2"></i> พฤติกรรมดีเยี่ยม
                </div>
            </div>

            <!-- Profile Summary -->
            <div class="lg:col-span-2 glass-effect rounded-[2.5rem] p-10 shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-10 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fas fa-id-card text-9xl"></i>
                </div>
                
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h4 id="profileName" class="text-3xl font-black text-slate-800 dark:text-white mb-1">-</h4>
                                <p id="profileClass" class="text-slate-500 font-bold uppercase text-xs tracking-widest">มัธยมศึกษาปีที่ -/-</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">เลขประจำตัว</div>
                                <div id="studentIdDisplay" class="text-lg font-black text-slate-700 dark:text-slate-200">-</div>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">เลขที่</div>
                                <div id="studentNoDisplay" class="text-lg font-black text-slate-700 dark:text-slate-200">-</div>
                            </div>
                            <div class="p-4 bg-rose-50 dark:bg-rose-900/20 rounded-2xl border border-rose-100 dark:border-rose-900/30">
                                <div class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">คะแนนที่ถูกหัก</div>
                                <div id="totalDeductionsCount" class="text-lg font-black text-rose-500">0</div>
                            </div>
                            <div class="p-4 bg-teal-50 dark:bg-teal-900/20 rounded-2xl border border-teal-100 dark:border-teal-900/30">
                                <div class="text-[10px] font-black text-teal-400 uppercase tracking-widest mb-1">คะแนนจิตอาสา</div>
                                <div id="totalBonusPoints" class="text-lg font-black text-teal-500">+0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed History Section -->
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-history text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white">ประวัติการหักคะแนน</h3>
                        <p class="text-sm text-slate-500">รายการพฤติกรรมที่ไม่พึงประสงค์ที่มีข้อมูลในระบบ</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button id="printBtn" class="px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-amber-500 transition-all flex items-center gap-2 no-print">
                         <i class="fas fa-print"></i> พิมพ์รายงาน
                    </button>
                </div>
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden md:block overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-inner">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">วันที่</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">เรื่องที่ถูกหัก/ฐานความผิด</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">คะแนน</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ครูผู้ทำโครงการ/หัก</th>
                        </tr>
                    </thead>
                    <tbody id="behaviorTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                        <!-- JS Inject -->
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Cards -->
            <div id="behaviorCardsBody" class="md:hidden grid grid-cols-1 gap-4 mt-6">
                <!-- JS Inject -->
            </div>

            <!-- Detailed History Section (Bonus) -->
            <div id="bonusSection" class="mt-12 hidden">
                 <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/30 text-teal-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white">ประวัติคะแนนจิตอาสา</h3>
                        <p class="text-sm text-slate-500">ชั่วโมงกิจกรรมที่ได้รับการบันทึกเป็นคะแนนบวก</p>
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-inner">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50">
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">วันที่</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ชื่อกิจกรรม/โครงการ</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">ชั่วโมง (คะแนน)</th>
                            </tr>
                        </thead>
                        <tbody id="bonusTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                            <!-- JS Inject -->
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View -->
                <div id="bonusCardsBody" class="md:hidden grid grid-cols-1 gap-4">
                    <!-- JS Inject -->
                </div>
            </div>
        </div>
    </div>

    <!-- Empty/Default State -->
    <div id="emptyState" class="animate__animated animate__fadeIn">
        <div class="text-center py-20">
            <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-user-check text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest">กรุณาเลือกนักเรียน</h3>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // API: Load Classes
    fetch('api/api_get_classes.php')
        .then(res => res.json())
        .then(data => {
            let classSelect = document.getElementById('classSelect');
            data.forEach(cls => {
                let opt = document.createElement('option');
                opt.value = cls.Stu_major;
                opt.textContent = 'มัธยมศึกษาปีที่ ' + cls.Stu_major;
                classSelect.appendChild(opt);
            });
        });

    // Handle Class Select
    document.getElementById('classSelect').addEventListener('change', function() {
        let classVal = this.value;
        let roomSelect = document.getElementById('roomSelect');
        let studentSelect = document.getElementById('studentSelect');
        roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
        studentSelect.disabled = true;
        if (classVal) {
            fetch('api/api_get_rooms.php?class=' + classVal)
                .then(res => res.json())
                .then(data => {
                    roomSelect.disabled = false;
                    data.forEach(room => {
                        let opt = document.createElement('option');
                        opt.value = room.Stu_room;
                        opt.textContent = 'ห้อง ' + room.Stu_room;
                        roomSelect.appendChild(opt);
                    });
                });
        }
    });

    // Handle Room Select
    document.getElementById('roomSelect').addEventListener('change', function() {
        let classVal = document.getElementById('classSelect').value;
        let roomVal = this.value;
        let studentSelect = document.getElementById('studentSelect');
        studentSelect.innerHTML = '<option value="">-- เลือกนักเรียน --</option>';
        if (classVal && roomVal) {
            fetch('api/api_get_students.php?class=' + classVal + '&room=' + roomVal)
                .then(res => res.json())
                .then(data => {
                    studentSelect.disabled = false;
                    data.forEach(stu => {
                        let opt = document.createElement('option');
                        opt.value = stu.Stu_id;
                        opt.textContent = `[No.${stu.Stu_no}] ${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}`;
                        studentSelect.appendChild(opt);
                    });
                });
        }
    });

    // Handle Student Select
    document.getElementById('studentSelect').addEventListener('change', function() {
        let stuId = this.value;
        if (!stuId) return;

        $('#behaviorContainer').removeClass('hidden');
        $('#emptyState').addClass('hidden');

        fetch('api/ajax_get_behavior_self_result.php?stu_id=' + stuId)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire('ไม่พบข้อมูล', 'นักเรียนคนนี้ไม่มีข้อมูลพฤติกรรม', 'info');
                    return;
                }

                // Update Profile
                document.getElementById('profileName').textContent = data.studentInfo.student_name || '-';
                document.getElementById('profileClass').textContent = `มัธยมศึกษาปีที่ ${data.studentInfo.student_class}/${data.studentInfo.student_room}`;
                document.getElementById('studentIdDisplay').textContent = stuId;
                document.getElementById('studentNoDisplay').textContent = data.studentInfo.student_no || '-';

                // Scores from API
                const remaining = data.total_score;
                const totalDeductionScore = data.total_deduction;
                const bonusPoints = data.bonus_points;
                const behaviorList = data.behaviorList || [];

                // Animate Score
                animateValue(document.getElementById('remainingScore'), 0, remaining, 1000);
                document.getElementById('totalDeductionsCount').textContent = `-${totalDeductionScore}`;
                document.getElementById('totalBonusPoints').textContent = `+${bonusPoints}`;

                // Circular Progress
                const circle = document.getElementById('scoreProgress');
                const radius = circle.r.baseVal.value;
                const circumference = radius * 2 * Math.PI;
                const progressValue = Math.max(0, Math.min(100, remaining));
                const offset = circumference - (progressValue / 100) * circumference;
                circle.style.strokeDashoffset = offset;

                // Status Color
                let statusBox = document.getElementById('scoreStatus');
                if (remaining >= 90) {
                    statusBox.innerHTML = '<i class="fas fa-check-circle mr-2"></i> พฤติกรรมดีเยี่ยม';
                    statusBox.className = "px-6 py-2 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-widest";
                    circle.className.baseVal = "circle-progress text-emerald-500";
                } else if (remaining >= 70) {
                    statusBox.innerHTML = '<i class="fas fa-info-circle mr-2"></i> พฤติกรรมปกติ';
                    statusBox.className = "px-6 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-black uppercase tracking-widest";
                    circle.className.baseVal = "circle-progress text-blue-500";
                } else if (remaining >= 50) {
                    statusBox.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> ควรปรับปรุงพฤติกรรม';
                    statusBox.className = "px-6 py-2 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-black uppercase tracking-widest";
                    circle.className.baseVal = "circle-progress text-amber-500";
                } else {
                    statusBox.innerHTML = '<i class="fas fa-skull-crossbones mr-2"></i> วิกฤต';
                    statusBox.className = "px-6 py-2 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-xs font-black uppercase tracking-widest";
                    circle.className.baseVal = "circle-progress text-rose-500";
                }

                // Render Table & Cards
                const tableBody = document.getElementById('behaviorTableBody');
                const cardsBody = document.getElementById('behaviorCardsBody');
                tableBody.innerHTML = '';
                cardsBody.innerHTML = '';

                if (behaviorList.length > 0) {
                    behaviorList.forEach(b => {
                        // Desktop Row
                        const tr = document.createElement('tr');
                        tr.className = "hover:bg-slate-50 transition-colors";
                        tr.innerHTML = `
                            <td class="px-8 py-6 font-bold text-slate-700 dark:text-slate-300">
                                <div class="flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-rose-500 rounded-full"></span>
                                    ${thaiDate(b.behavior_date)}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-800 dark:text-white">${b.behavior_type}</div>
                                <div class="text-xs text-slate-400">${b.behavior_name}</div>
                            </td>
                            <td class="px-8 py-6 text-center font-black text-rose-600">-${b.behavior_score}</td>
                            <td class="px-8 py-6 text-sm text-slate-500 italic">${b.teacher_behavior || '-'}</td>
                        `;
                        tableBody.appendChild(tr);

                        // Mobile Card
                        const card = document.createElement('div');
                        card.className = "p-6 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm";
                        card.innerHTML = `
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">วันที่รายงาน</div>
                                    <div class="font-black text-slate-800 dark:text-white">${thaiDate(b.behavior_date)}</div>
                                </div>
                                <div class="px-3 py-1 bg-rose-100 text-rose-600 rounded-lg font-black text-sm">-${b.behavior_score}</div>
                            </div>
                            <div class="space-y-3">
                                <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl">
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">สาขา/ฐานความผิด</div>
                                    <div class="font-bold text-slate-700 dark:text-slate-200">${b.behavior_type}</div>
                                    <div class="text-xs text-slate-500">${b.behavior_name}</div>
                                </div>
                                <div class="text-xs text-slate-400 italic">รายงานโดย: ${b.teacher_behavior || '-'}</div>
                            </div>
                        `;
                        cardsBody.appendChild(card);
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="4" class="px-8 py-20 text-center font-bold text-slate-300">ไม่เคยถูกหักคะแนนพฤติกรรม</td></tr>';
                    cardsBody.innerHTML = '<div class="px-8 py-20 text-center font-bold text-slate-300">ไม่เคยถูกหักคะแนนพฤติกรรม</div>';
                }

                // Render Bonus Points (Fetching from BehaviorController for details)
                fetch(`../controllers/BehaviorController.php?action=volunteer_bonus_details&stu_id=${stuId}`)
                    .then(res => res.json())
                    .then(bonusData => {
                        const bonusTableBody = document.getElementById('bonusTableBody');
                        const bonusCardsBody = document.getElementById('bonusCardsBody');
                        const bonusSection = document.getElementById('bonusSection');
                        
                        bonusTableBody.innerHTML = '';
                        bonusCardsBody.innerHTML = '';

                        if (bonusData.success && bonusData.data && bonusData.data.length > 0) {
                            bonusSection.classList.remove('hidden');
                            bonusData.data.forEach(item => {
                                // Desktop Row
                                const tr = document.createElement('tr');
                                tr.className = "hover:bg-slate-50 transition-colors";
                                tr.innerHTML = `
                                    <td class="px-8 py-6 font-bold text-slate-700 dark:text-slate-300">${thaiDate(item.activity_date)}</td>
                                    <td class="px-8 py-6 font-bold text-slate-800 dark:text-white">${item.activity_name}</td>
                                    <td class="px-8 py-6 text-center font-black text-teal-600">+${item.hours}</td>
                                `;
                                bonusTableBody.appendChild(tr);

                                // Mobile Card
                                const card = document.createElement('div');
                                card.className = "p-6 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm";
                                card.innerHTML = `
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">วันที่ทำกิจกรรม</div>
                                        <div class="px-2 py-1 bg-teal-100 text-teal-600 rounded-lg font-black text-xs">+${item.hours}</div>
                                    </div>
                                    <div class="font-black text-slate-800 dark:text-white mb-2">${thaiDate(item.activity_date)}</div>
                                    <div class="text-sm font-bold text-slate-600 dark:text-slate-400">${item.activity_name}</div>
                                `;
                                bonusCardsBody.appendChild(card);
                            });
                        } else {
                            bonusSection.classList.add('hidden');
                        }
                    });
            });
    });

    document.getElementById('printBtn').addEventListener('click', () => window.print());

    function thaiDate(strDate) {
        if (!strDate) return '-';
        const months = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
        let d = new Date(strDate);
        return `${d.getDate()} ${months[d.getMonth() + 1]} ${d.getFullYear() + 543}`;
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
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
