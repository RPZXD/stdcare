<?php
/**
 * View: Class Visit Home Report
 * Modern UI with Tailwind CSS, Chart.js, and Mobile-Friendly Cards
 */
ob_start();
?>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.5s ease-out forwards;
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .dark .glass-effect {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.05);
    }
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .glass-effect { box-shadow: none !important; border: 1px solid #eee !important; }
    }
    .print-only { display: none; }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-red-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-3xl flex items-center justify-center text-white shadow-xl transform hover:rotate-6 transition-transform">
                        <i class="fas fa-home text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            รายงานการเยี่ยมบ้านรายห้อง
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                            ติดตามและจัดการผลการเยี่ยมบ้านนักเรียนภาพรวมระดับห้องเรียน
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-6 py-3 bg-orange-50 dark:bg-orange-900/30 rounded-2xl border border-orange-100 dark:border-orange-800 text-center">
                        <span class="text-[10px] font-black text-orange-400 uppercase tracking-widest block mb-1">เทอม/ปีการศึกษา</span>
                        <span class="text-lg font-black text-orange-600 dark:text-orange-400"><?php echo $term; ?>/<?php echo $pee; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                <div class="relative">
                    <select id="classSelect" name="class" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-orange-100 dark:focus:ring-orange-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <option value="">-- เลือกชั้น --</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ห้องเรียน</label>
                <div class="relative">
                    <select id="roomSelect" name="room" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-orange-100 dark:focus:ring-orange-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white disabled:opacity-50" disabled>
                        <option value="">-- เลือกห้อง --</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="flex items-end self-end">
                <button type="submit" class="w-full h-[60px] bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-black rounded-2xl shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-search-plus"></i> ค้นหาข้อมูล
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section (Initially Hidden) -->
    <div id="reportContainer" class="hidden space-y-8 animate-fadeIn">
        
        <!-- Summary Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 no-print">
            <!-- Stat Cards -->
            <div class="glass-effect p-6 rounded-[2rem] border-b-4 border-indigo-500 shadow-lg shadow-indigo-500/5">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">นักเรียนทั้งหมด</span>
                </div>
                <div id="total-count" class="text-3xl font-black text-slate-800 dark:text-white">0</div>
            </div>

            <div class="glass-effect p-6 rounded-[2rem] border-b-4 border-emerald-500 shadow-lg shadow-emerald-500/5">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-double text-lg"></i>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เยี่ยมบ้านแล้ว</span>
                </div>
                <div id="visited-count" class="text-3xl font-black text-slate-800 dark:text-white text-emerald-600">0</div>
            </div>

            <div class="glass-effect p-6 rounded-[2rem] border-b-4 border-amber-500 shadow-lg shadow-amber-500/5">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">รอการเยี่ยม</span>
                </div>
                <div id="pending-count" class="text-3xl font-black text-slate-800 dark:text-white text-amber-600">0</div>
            </div>

            <div class="glass-effect p-6 rounded-[2rem] border-b-4 border-rose-500 shadow-lg shadow-rose-500/5">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ความสำเร็จ</span>
                </div>
                <div id="success-rate" class="text-3xl font-black text-slate-800 dark:text-white text-rose-600">0%</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 no-print">
            <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-pie-chart"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white italic">สัดส่วนการเยี่ยมบ้าน</h3>
                </div>
                <div class="relative h-[300px] flex items-center justify-center">
                    <canvas id="visitChart"></canvas>
                </div>
            </div>

            <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white italic">ความก้าวหน้ารายเทอม</h3>
                </div>
                <div class="space-y-8 py-4">
                    <div class="group">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-black text-slate-600 dark:text-slate-300 uppercase italic">ภาคเรียนที่ 1</span>
                            <span id="p1-text" class="text-xl font-black text-indigo-600">0%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-900 rounded-full h-4 relative overflow-hidden ring-4 ring-slate-50 dark:ring-slate-900/50">
                            <div id="p1-bar" class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 to-blue-600 rounded-full transition-all duration-1000 ease-out shadow-lg" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="group">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-black text-slate-600 dark:text-slate-300 uppercase italic">ภาคเรียนที่ 2</span>
                            <span id="p2-text" class="text-xl font-black text-emerald-600">0%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-900 rounded-full h-4 relative overflow-hidden ring-4 ring-slate-50 dark:ring-slate-900/50">
                            <div id="p2-bar" class="absolute top-0 left-0 h-full bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full transition-all duration-1000 ease-out shadow-lg" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500/5 to-red-600/5 rounded-3xl p-6 border border-orange-100 dark:border-orange-900/30 mt-6 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 text-orange-600/5 pointer-events-none">
                            <i class="fas fa-medal text-8xl"></i>
                        </div>
                        <div class="flex justify-between items-center relative z-10">
                            <div>
                                <h4 class="text-xs font-black text-orange-400 uppercase tracking-widest italic mb-1">ภาพรวมความสำเร็จ</h4>
                                <p class="text-slate-500 text-xs">คำนวณจากจำนวนนักเรียนที่ได้รับการเยี่ยมอย่างน้อย 1 ครั้ง</p>
                            </div>
                            <div id="total-percentage" class="text-4xl font-black text-orange-600">0%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student List Container -->
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-xl"></i>
                    </div>
                    <div>
                        <h3 id="reportTitle" class="text-2xl font-black text-slate-800 dark:text-white italic">รายชื่อนักเรียน</h3>
                        <p class="text-sm text-slate-500">ผลการเยี่ยมบ้านแยกรายบุคคล</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 no-print">
                    <button id="exportExcelBtn" class="flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-black shadow-lg shadow-emerald-500/25 transition-all">
                        <i class="fas fa-file-excel"></i> ส่งออกข้อมูล
                    </button>
                </div>
            </div>

            <!-- Table (Desktop) -->
            <div class="hidden lg:block overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-inner">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เลขที่</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic leading-relaxed">ข้อมูลพื้นฐาน</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ภาคเรียนที่ 1</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ภาคเรียนที่ 2</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">สถานะ</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ตัวเลือก</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                    </tbody>
                </table>
            </div>

            <!-- Cards (Mobile) -->
            <div id="studentCardBody" class="lg:hidden grid grid-cols-1 gap-4">
            </div>
        </div>
    </div>

    <!-- Empty/Loading State -->
    <div id="emptyState" class="animate-fadeIn py-20 text-center">
        <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-100 dark:border-slate-800 transform transition-transform hover:scale-110">
            <i class="fas fa-search text-5xl text-slate-200"></i>
        </div>
        <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest animate-pulse">กรุณาเลือกชั้นและห้องเรียน</h3>
        <p class="text-slate-400 mt-2 font-medium italic">ระบุเงื่อนไขด้านบนเพื่อดึงข้อมูลรายงานการเยี่ยมบ้าน</p>
    </div>
</div>

<!-- Details Modal -->
<div id="detailModal" class="hidden fixed inset-0 z-[100] overflow-y-auto no-print">
    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-[3rem] shadow-2xl max-w-4xl w-full h-[90vh] flex flex-col overflow-hidden transform transition-all border border-white/20">
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-orange-500 to-red-600"></div>
            
            <div class="flex flex-col h-full">
                <!-- Modal Header -->
                <div class="px-8 md:px-10 pt-8 md:pt-10 flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-home text-xl"></i>
                        </div>
                        <div>
                            <h3 id="modalTitle" class="text-2xl font-black text-slate-800 dark:text-white italic">รายละเอียดการเยี่ยมบ้าน</h3>
                            <p id="modalSubtitle" class="text-sm text-slate-500">ข้อมูลเชิงลึกจากการลงพื้นที่</p>
                        </div>
                    </div>
                    <button onclick="closeModal()" class="w-12 h-12 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-2xl transition-all flex items-center justify-center border border-slate-100 dark:border-slate-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div id="modalBody" class="flex-1 overflow-y-auto px-8 md:px-10 py-4 scrollbar-thin scrollbar-thumb-orange-200">
                    <div class="flex flex-col items-center justify-center py-20 text-slate-300">
                        <i class="fas fa-circle-notch fa-spin text-4xl mb-4"></i>
                        <p class="font-black italic uppercase tracking-widest text-sm">กำลังโหลดข้อมูล...</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-8 md:px-10 py-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button onclick="closeModal()" class="px-8 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-2xl font-black hover:bg-slate-200 transition-all">
                        ปิดหน้าต่าง
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let visitChart;
let lastResults = [];

document.addEventListener('DOMContentLoaded', () => {
    // 1. Load Classes
    fetch('api/api_get_classes.php')
        .then(res => res.json())
        .then(data => {
            const classSelect = document.getElementById('classSelect');
            if(!classSelect) return;
            data.forEach(cls => {
                const opt = document.createElement('option');
                opt.value = cls.Stu_major;
                opt.textContent = `มัธยมศึกษาปีที่ ${cls.Stu_major}`;
                classSelect.appendChild(opt);
            });
        });

    // 2. Class Change -> Load Rooms
    const classSelect = document.getElementById('classSelect');
    if(classSelect) {
        classSelect.addEventListener('change', function() {
            const classVal = this.value;
            const roomSelect = document.getElementById('roomSelect');
            if(!roomSelect) return;
            roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
            roomSelect.disabled = true;
            if (classVal) {
                fetch(`api/api_get_rooms.php?class=${classVal}`)
                    .then(res => res.json())
                    .then(data => {
                        roomSelect.disabled = false;
                        data.forEach(room => {
                            const opt = document.createElement('option');
                            opt.value = room.Stu_room;
                            opt.textContent = `ห้องเรียน ${room.Stu_room}`;
                            roomSelect.appendChild(opt);
                        });
                    });
            }
        });
    }

    // 3. Form Submission
    const filterForm = document.getElementById('filterForm');
    if(filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const classVal = document.getElementById('classSelect').value;
            const roomVal = document.getElementById('roomSelect').value;

            if (!classVal || !roomVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาระบุข้อมูล',
                    text: 'เลือกชั้นและห้องเรียนให้ครบถ้วนก่อนการค้นหา',
                    confirmButtonColor: '#f97316'
                });
                return;
            }

            Swal.showLoading();
            fetch(`api/ajax_get_class_visit_home.php?class=${classVal}&room=${roomVal}`)
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if(data.success) {
                        lastResults = data;
                        renderUI(data);
                    } else {
                        Swal.fire('Error', data.message || 'Error occurred', 'error');
                    }
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                });
        });
    }

    // 4. Export
    document.getElementById('exportExcelBtn').addEventListener('click', () => {
        const classVal = document.getElementById('classSelect').value;
        const roomVal = document.getElementById('roomSelect').value;
        if(classVal && roomVal) {
            window.open(`api/export_visit_home.php?class=${classVal}&room=${roomVal}`, '_blank');
        }
    });
});

function renderUI(data) {
    const container = document.getElementById('reportContainer');
    const emptyState = document.getElementById('emptyState');
    const reportTitle = document.getElementById('reportTitle');
    
    emptyState.classList.add('hidden');
    container.classList.remove('hidden');

    reportTitle.innerHTML = `สรุปผลการเยี่ยมบ้านชั้น ม.${data.class_info.class}/${data.class_info.room} <span class="text-orange-500 font-black italic ml-2">ปีการศึกษา ${data.class_info.pee}</span>`;

    // 1. Stats Counter
    const summary = data.summary;
    const total = summary.total || 1;
    const visited = summary.visited;
    const pending = summary.pending;
    const successRate = Math.round((visited / total) * 100);

    animateCounter('total-count', total);
    animateCounter('visited-count', visited);
    animateCounter('pending-count', pending);
    animateCounter('success-rate', successRate, '%');
    animateCounter('total-percentage', successRate, '%');

    // 2. Progress Bars
    const r1 = Math.round((summary.round1_completed / total) * 100);
    const r2 = Math.round((summary.round2_completed / total) * 100);
    
    updateProgressBar('p1', r1);
    updateProgressBar('p2', r2);

    // 3. Chart
    renderChart(visited, pending, summary.overdue || 0);

    // 4. Student List
    const tableBody = document.getElementById('studentTableBody');
    const cardBody = document.getElementById('studentCardBody');
    tableBody.innerHTML = '';
    cardBody.innerHTML = '';

    data.students.forEach(stu => {
        const row = createTableRow(stu);
        tableBody.appendChild(row);
        
        const card = createMobileCard(stu);
        cardBody.appendChild(card);
    });
}

function animateCounter(id, end, suffix = '') {
    const el = document.getElementById(id);
    let start = 0;
    const duration = 1000;
    const increment = end / (duration / 16);
    
    const count = () => {
        start += increment;
        if(start < end) {
            el.innerText = Math.floor(start) + suffix;
            requestAnimationFrame(count);
        } else {
            el.innerText = end + suffix;
        }
    };
    count();
}

function updateProgressBar(id, perc) {
    const text = document.getElementById(`${id}-text`);
    const bar = document.getElementById(`${id}-bar`);
    text.innerText = perc + '%';
    setTimeout(() => {
        bar.style.width = perc + '%';
    }, 100);
}

function renderChart(v, p, o) {
    const ctx = document.getElementById('visitChart').getContext('2d');
    if(visitChart) visitChart.destroy();
    
    visitChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['เยี่ยมแล้ว', 'ยังไม่เยี่ยม', 'เกินกำหนด'],
            datasets: [{
                data: [v, p, o],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                hoverOffset: 15,
                borderRadius: 15,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11, weight: '900', family: 'Mali' },
                        padding: 20
                    }
                }
            },
            animation: { animateRotate: true, animateScale: true }
        }
    });
}

function createTableRow(stu) {
    const tr = document.createElement('tr');
    tr.className = "hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group";
    
    const r1 = getStatusBadge(stu.round1_visit);
    const r2 = getStatusBadge(stu.round2_visit);
    const overall = getOverallBadge(stu.status);

    tr.innerHTML = `
        <td class="px-8 py-6">
            <span class="text-xs font-black text-slate-400 w-6 italic">#${stu.Stu_no}</span>
        </td>
        <td class="px-8 py-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform">
                    ${stu.Stu_name.charAt(0)}
                </div>
                <div>
                    <div class="font-black text-slate-800 dark:text-white leading-tight">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 italic">ID: ${stu.Stu_id}</div>
                </div>
            </div>
        </td>
        <td class="px-8 py-6 text-center">${r1}</td>
        <td class="px-8 py-6 text-center">${r2}</td>
        <td class="px-8 py-6 text-center">${overall}</td>
        <td class="px-8 py-6">
            <div class="flex justify-center">
                <button onclick="viewVisit('${stu.Stu_id}')" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 hover:bg-orange-600 hover:text-white transition-all shadow-sm flex items-center justify-center border border-slate-100 dark:border-slate-700">
                    <i class="fas fa-eye text-sm"></i>
                </button>
            </div>
        </td>
    `;
    return tr;
}

function createMobileCard(stu) {
    const card = document.createElement('div');
    card.className = "glass-effect p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl relative overflow-hidden group";
    
    const r1 = getStatusBadge(stu.round1_visit);
    const r2 = getStatusBadge(stu.round2_visit);
    const overall = getOverallBadge(stu.status);

    card.innerHTML = `
        <div class="flex items-start gap-4 mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-3xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-orange-500/20">
                ${stu.Stu_name.charAt(0)}
            </div>
            <div class="flex-1 min-w-0 pt-1">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-black text-slate-400 italic">เลขที่ ${stu.Stu_no}</span>
                    <span class="text-[10px] font-black text-orange-500 bg-orange-50 dark:bg-orange-900/40 px-2 py-0.5 rounded-md italic">ID: ${stu.Stu_id}</span>
                </div>
                <h4 class="text-base font-black text-slate-800 dark:text-white leading-tight break-words">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</h4>
                <div class="mt-3">
                    ${overall}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="p-4 bg-slate-50/50 dark:bg-slate-900/50 rounded-[1.8rem] border border-slate-100 dark:border-slate-800">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">เทอม 1</span>
                ${r1}
            </div>
            <div class="p-4 bg-slate-50/50 dark:bg-slate-900/50 rounded-[1.8rem] border border-slate-100 dark:border-slate-800">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">เทอม 2</span>
                ${r2}
            </div>
        </div>

        <button onclick="viewVisit('${stu.Stu_id}')" class="w-full py-4 bg-gradient-to-r from-slate-800 to-slate-900 hover:from-orange-600 hover:to-red-600 text-white rounded-2xl font-black transition-all shadow-xl flex items-center justify-center gap-2">
            <i class="fas fa-eye"></i> ดูรายละเอียดฉบับเต็ม
        </button>
    `;
    return card;
}

function getStatusBadge(visit) {
    if(!visit) return `<span class="px-3 py-1 bg-slate-100 dark:bg-slate-900 text-slate-400 text-[10px] font-black rounded-lg uppercase italic tracking-wider">ยังไม่เยี่ยม</span>`;
    return `<span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400 text-[10px] font-black rounded-lg uppercase italic tracking-wider">เยี่ยมแล้ว</span>`;
}

function getOverallBadge(status) {
    switch(status) {
        case 'completed':
            return `<span class="px-4 py-1.5 bg-emerald-500 text-white text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg shadow-emerald-500/20">ครบรอบการเยี่ยม</span>`;
        case 'partial':
            return `<span class="px-4 py-1.5 bg-amber-500 text-white text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg shadow-amber-500/20">เยี่ยมบางส่วน</span>`;
        case 'overdue':
            return `<span class="px-4 py-1.5 bg-rose-500 text-white text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg shadow-rose-500/20">เกินกำหนด</span>`;
        default:
            return `<span class="px-4 py-1.5 bg-slate-400 text-white text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg shadow-slate-500/20">รอดำเนินการ</span>`;
    }
}

function viewVisit(id) {
    const modal = document.getElementById('detailModal');
    const modalBody = document.getElementById('modalBody');
    const classVal = document.getElementById('classSelect').value;
    const roomVal = document.getElementById('roomSelect').value;

    modal.classList.remove('hidden');
    modalBody.innerHTML = `
        <div class="flex flex-col items-center justify-center py-20 text-slate-300">
            <i class="fas fa-circle-notch fa-spin text-4xl mb-4 text-orange-500"></i>
            <p class="font-black italic uppercase tracking-widest text-sm text-slate-500">กำลังดึงข้อมูล...</p>
        </div>
    `;

    fetch(`api/get_visit_details_full.php?student_id=${id}&class=${classVal}&room=${roomVal}`)
        .then(res => res.text())
        .then(html => {
            modalBody.innerHTML = `<div class="animate-fadeIn">${html}</div>`;
        });
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
