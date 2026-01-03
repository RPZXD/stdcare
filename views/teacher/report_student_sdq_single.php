<?php
/**
 * Teacher Report Student SDQ Single View
 * Modern UI with Tailwind CSS & Glassmorphism
 */
ob_start();
?>

<!-- Custom Styles for SDQ Report -->
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
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .status-pill {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .progress-glow {
        box-shadow: 0 0 15px currentColor;
    }

    /* Print Styles */
    @media print {
        body { background: white !important; font-size: 12pt; color: black !important; }
        .no-print, nav, aside, footer, #filterForm, .glass-effect:not(#sdqResultContainer *), 
        button:not(.print-specific), .sidebar, .navbar, .mb-10:has(#filterForm) { 
            display: none !important; 
        }
        .max-w-7xl { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
        #sdqResultContainer { display: block !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .glass-effect { border: none !important; box-shadow: none !important; background: white !important; backdrop-filter: none !important; }
        .rounded-\[2\.5rem\], .rounded-3xl, .rounded-2xl { border-radius: 0.5rem !important; border: 1px solid #e2e8f0 !important; }
        .bg-emerald-500, .bg-rose-500, .bg-amber-500, .bg-indigo-500 { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        h1, h2, h3 { color: black !important; }
        .grid { display: block !important; }
        .md\:grid-cols-2 { grid-template-cols: 1fr 1fr !important; display: grid !important; gap: 20px !important; }
        .mb-10 { margin-bottom: 2rem !important; }
        .p-10 { padding: 1.5rem !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #cbd5e1 !important; padding: 8px !important; }
        .status-pill { border: 1px solid currentColor !important; }
        .print-header { display: block !important; text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
    }
    .print-header { display: none; }
</style>

<div class="max-w-7xl mx-auto py-8">
    <!-- Header Section -->
    <div class="mb-10 animate__animated animate__fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-12 relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 text-center md:text-left flex flex-col md:flex-row items-center gap-8">
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-emerald-500/30 float-animation">
                        <i class="fas fa-brain text-4xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl md:text-5xl font-black text-slate-800 dark:text-white mb-4">
                        รายงาน SDQ รายบุคคล
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 font-medium">
                        ประเมินจุดแข็งและจุดอ่อนของนักเรียน (Strengths and Difficulties Questionnaire)
                    </p>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="px-6 py-3 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                        <span class="text-xs font-bold text-slate-400 block uppercase">ปีการศึกษา/เทอม</span>
                        <span class="text-lg font-black text-emerald-600"><?php echo $pee; ?>/<?php echo $term; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 mb-10 shadow-xl border border-white/50 dark:border-slate-700/50 animate__animated animate__fadeIn" style="animation-delay: 0.1s">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center shadow-inner">
                <i class="fas fa-search text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">เลือกข้อมูลนักเรียน</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">กรองรายชื่อนักเรียนเพื่อดูผลประเมินรายบุคคล</p>
            </div>
        </div>
        
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-2 uppercase tracking-wider">
                    <i class="fas fa-layer-group text-emerald-500 mr-2"></i> ระดับชั้น
                </label>
                <div class="relative">
                    <select id="classSelect" name="class" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold">
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
                    <select id="roomSelect" name="room" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold" disabled>
                        <option value="">-- เลือกห้อง --</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-2 uppercase tracking-wider">
                    <i class="fas fa-user-graduate text-teal-500 mr-2"></i> นักเรียน
                </label>
                <div class="relative">
                    <select id="studentSelect" name="student" class="w-full pl-5 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all dark:text-white appearance-none cursor-pointer font-bold" disabled>
                        <option value="">-- เลือกนักเรียน --</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    <div id="sdqResultContainer" class="animate__animated animate__fadeIn">
        <div class="text-center py-20">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full mb-6">
                <i class="fas fa-mouse-pointer text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest">กรุณาเลือกนักเรียนเพื่อดูข้อมูล</h3>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // UI Loading state
    function showLoading() {
        document.getElementById('sdqResultContainer').innerHTML = `
            <div class="flex flex-col items-center justify-center py-20 space-y-6">
                <div class="w-16 h-16 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-xl font-bold text-emerald-600 animate-pulse">กำลังดึงข้อมูลคะแนน SDQ...</p>
            </div>
        `;
    }

    // Load Classes
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

    // Class Change
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

    // Room Change
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

    // Student Change
    document.getElementById('studentSelect').addEventListener('change', function() {
        let stuId = this.value;
        if (!stuId) return;

        showLoading();
        
        fetch('api/ajax_get_sdq_self_result.php?stu_id=' + stuId)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    document.getElementById('sdqResultContainer').innerHTML = `
                        <div class="glass-effect rounded-[2.5rem] p-12 text-center shadow-xl">
                            <div class="w-20 h-20 bg-rose-100 dark:bg-rose-900/30 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-exclamation-triangle text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">ไม่พบข้อมูล</h3>
                            <p class="text-slate-500 dark:text-slate-400">นักเรียนคนนี้ยังไม่ได้ทำแบบประเมิน SDQ ในเทอมนี้</p>
                        </div>
                    `;
                    return;
                }

                renderResult(data);
            });
    });

    function renderResult(data) {
        const container = document.getElementById('sdqResultContainer');
        
        // Helper for summary status
        let summaryStatus = 'ปกติ';
        let summaryColor = 'emerald';
        if (data.totalProblemScore >= 20) {
            summaryStatus = 'มีปัญหา';
            summaryColor = 'rose';
        } else if (data.totalProblemScore >= 14) {
            summaryStatus = 'ภาวะเสี่ยง';
            summaryColor = 'amber';
        }

        let html = `
            <!-- Print Content Header -->
            <div class="print-header">
                <h1 class="text-2xl font-bold">รายงานสรุปผลการประเมินตนเอง (SDQ) รายบุคคล</h1>
                <p class="text-lg">โรงเรียนพิชัยรัตนาคาร | ปีการศึกษา ${data.student_year || '2567'} ภาคเรียนที่ ${data.student_term || '2'}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <!-- Profile Card -->
                <div class="glass-effect rounded-[2.5rem] p-10 shadow-xl border-l-8 border-emerald-500 transition-all hover:scale-[1.02]">
                    <div class="flex items-start gap-6">
                        <div class="w-20 h-20 bg-emerald-500 rounded-3xl flex items-center justify-center text-white text-3xl shadow-lg shadow-emerald-500/30">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-1">ข้อมูลผู้เข้ารับการประเมิน</p>
                            <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-2">${data.student_name}</h2>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-400">เลขที่ ${data.student_no}</span>
                                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-400">ชั้น ม.${data.student_class}/${data.student_room}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Score Card -->
                <div class="glass-effect rounded-[2.5rem] p-10 shadow-xl border-l-8 border-${summaryColor}-500 transition-all hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-${summaryColor}-600 uppercase tracking-[0.2em] mb-1">คะแนนรวมความยากลำบาก</p>
                            <h2 class="text-4xl font-black text-slate-800 dark:text-white mb-1">${data.totalProblemScore} <span class="text-sm font-bold text-slate-400">คะแนน</span></h2>
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-${summaryColor}-100 dark:bg-${summaryColor}-900/30 text-${summaryColor}-600 dark:text-${summaryColor}-400 text-sm font-black uppercase">
                                <i class="fas fa-circle text-[8px] mr-2"></i> ${summaryStatus}
                            </span>
                        </div>
                        <div class="text-5xl opacity-20 text-${summaryColor}-600">
                             <i class="fas ${data.totalProblemScore >= 20 ? 'fa-frown' : (data.totalProblemScore >= 14 ? 'fa-meh' : 'fa-smile')}"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Results Table/Cards -->
            <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl mb-10">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-list-ul text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white">คะแนนรายด้าน</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">สรุปคะแนนประเมินทั้ง 5 ด้าน</p>
                    </div>
                </div>

                <!-- Desktop Table / Mobile Card Switcher -->
                <div class="hidden md:block overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-800 mb-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50">
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest border-b">ชื่อด้านการประเมิน</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest text-center border-b">คะแนน</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest border-b">ระดับผลประเมิน</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest border-b">กราฟความรุนแรง</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            ${Object.entries(data.categoryScores).map(([label, score]) => {
                                let status = data.categoryLevels[label];
                                let color = 'emerald';
                                if (status.includes('มีปัญหา')) color = 'rose';
                                else if (status.includes('เสี่ยง')) color = 'amber';
                                
                                // Strength category logic is inverted in API
                                let percent = (score / 10) * 100;

                                return `
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="px-8 py-6 font-bold text-slate-700 dark:text-slate-200 border-b">${label}</td>
                                        <td class="px-8 py-6 text-center font-black text-xl text-slate-800 dark:text-white border-b">${score}</td>
                                        <td class="px-8 py-6 border-b">
                                            <span class="status-pill bg-${color}-100 text-${color}-600 dark:bg-${color}-900/30 dark:text-${color}-400">
                                                ${status}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 min-w-[200px] border-b">
                                            <div class="w-full h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden no-print">
                                                <div class="h-full bg-${color}-500 rounded-full transition-all duration-1000 progress-glow text-${color}-500" style="width: ${percent}%"></div>
                                            </div>
                                            <!-- Print specific text replacement for progress bar if needed -->
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden grid grid-cols-1 gap-4 no-print">
                    ${Object.entries(data.categoryScores).map(([label, score]) => {
                        let status = data.categoryLevels[label];
                        let color = 'emerald';
                        if (status.includes('มีปัญหา')) color = 'rose';
                        else if (status.includes('เสี่ยง')) color = 'amber';
                        let percent = (score/10)*100;
                        
                        return `
                            <div class="p-6 rounded-3xl border-2 border-slate-100 dark:border-slate-800 space-y-4">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-black text-slate-800 dark:text-white text-lg">${label}</h4>
                                    <div class="text-2xl font-black text-slate-800 dark:text-white">${score} <span class="text-[10px] text-slate-400">PTS</span></div>
                                </div>
                                <div class="flex items-center gap-3">
                                     <span class="status-pill bg-${color}-100 text-${color}-600 dark:bg-${color}-900/30 dark:text-${color}-400 inline-block">
                                        ${status}
                                    </span>
                                </div>
                                <div class="w-full h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-${color}-500 rounded-full text-${color}-500 progress-glow" style="width: ${percent}%"></div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>

            <!-- Additional Info Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                <!-- Impact Analysis -->
                <div class="glass-effect rounded-[2.5rem] p-10 shadow-xl border border-white/50 dark:border-slate-700/50">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white">ผลกระทบของปัญหา</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">การรบกวนการใช้ชีวิตประจำวัน</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        ${Object.entries(data.impactTexts).map(([key, text]) => {
                            let label = '';
                            let icon = '';
                            switch(key) {
                                case 'home': label = 'ความเป็นอยู่ที่บ้าน'; icon = 'fa-home'; break;
                                case 'leisure': label = 'กิจกรรมยามว่าง'; icon = 'fa-gamepad'; break;
                                case 'friend': label = 'การคบเพื่อน'; icon = 'fa-user-friends'; break;
                                case 'classroom': label = 'การเรียนในห้องเรียน'; icon = 'fa-school'; break;
                                case 'burden': label = 'ภาระต่อตนเอง/ครอบครัว'; icon = 'fa-hand-holding-heart'; break;
                            }
                            const colorClass = data.impactColors[key];
                            
                            return `
                                <div class="flex items-center justify-between p-4 bg-slate-50/50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center text-slate-400 no-print">
                                            <i class="fas ${icon} text-xs"></i>
                                        </div>
                                        <span class="font-bold text-slate-700 dark:text-slate-300">${label}</span>
                                    </div>
                                    <span class="px-3 py-1 ${colorClass} text-white rounded-lg text-[10px] font-black uppercase tracking-widest border border-slate-200">${text}</span>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>

                <!-- Feedback -->
                <div class="glass-effect rounded-[2.5rem] p-10 shadow-xl border border-white/50 dark:border-slate-700/50 flex flex-col no-print-container">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-comment-dots text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white">ความคิดเห็นเพิ่มเติม</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">ข้อมูลเพิ่มเติมจากการประเมิน</p>
                        </div>
                    </div>
                    
                    <div class="flex-1 p-8 bg-amber-50 dark:bg-amber-900/10 rounded-[2rem] border-2 border-dashed border-amber-200 dark:border-amber-800/50 flex items-center justify-center italic text-center">
                        <span class="text-amber-800 dark:text-amber-400 font-medium">"${data.memo || 'ไม่มีข้อมูลเพิ่มเติม'}"</span>
                    </div>
                    
                    <button id="printBtn" class="mt-8 w-full py-4 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl font-black text-slate-600 dark:text-slate-300 transition-all flex items-center justify-center gap-3 no-print">
                         <i class="fas fa-print"></i> พิมพ์รายงานสรุป
                    </button>
                </div>
            </div>
        `;
        
        container.innerHTML = html;

        // Add print listener
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
