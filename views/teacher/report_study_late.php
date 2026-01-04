<?php
/**
 * View: Teacher Report Study Late/Absent (Class-based)
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive Cards
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
    .status-badge {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .glass-effect { box-shadow: none !important; border: 1px solid #eee !important; }
        body { background: white !important; }
    }
    .print-only { display: none; }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-3xl flex items-center justify-center text-white shadow-xl transform hover:rotate-6 transition-transform">
                        <i class="fas fa-clock-rotate-left text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            รายงานสถิติการมาสาย-ขาดเรียน
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                            ตรวจสอบสถานะการเข้าเรียนรายห้องประจำวัน สำหรับคุณครูและสายงานกิจการนักเรียน
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-6 py-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl border border-indigo-100 dark:border-indigo-800 text-center">
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block mb-1">เทอม/ปีการศึกษา</span>
                        <span class="text-lg font-black text-indigo-600 dark:text-indigo-400"><?php echo $term; ?>/<?php echo $pee; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">ระดับชั้น</label>
                <div class="relative">
                    <select id="classSelect" name="class" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <option value="">-- เลือกชั้น --</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">ห้องเรียน</label>
                <div class="relative">
                    <select id="roomSelect" name="room" class="w-full pl-5 pr-10 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white disabled:opacity-50" disabled>
                        <option value="">-- เลือกห้อง --</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">วันที่ต้องการตรวจสอบ</label>
                <div class="relative">
                    <input type="date" id="dateInput" name="date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
                </div>
            </div>

            <div class="flex items-end self-end">
                <button type="submit" class="w-full h-[60px] bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-search"></i> แสดงข้อมูล
                </button>
            </div>
        </form>
    </div>

    <!-- Search & Filter Bar -->
    <div id="searchFilterBar" class="hidden animate-fadeIn mb-6 no-print" style="animation-delay: 0.15s">
        <div class="glass-effect rounded-[1.5rem] p-4 flex flex-col md:flex-row items-center gap-4 border border-white/50 shadow-lg">
            <div class="relative flex-1 w-full">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" id="searchInput" placeholder="ค้นหาชื่อ, นามสกุล หรือเลขประจำตัว..." class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-xl outline-none focus:border-indigo-500 transition-all font-bold">
            </div>
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-hide">
                <button data-filter="all" class="filter-btn active whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black bg-indigo-600 text-white border-2 border-indigo-600 transition-all">ทั้งหมด</button>
                <button data-filter="1" class="filter-btn whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black bg-white dark:bg-slate-800 text-emerald-600 border-2 border-emerald-100 dark:border-emerald-900/30 transition-all">มาเรียน</button>
                <button data-filter="3" class="filter-btn whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black bg-white dark:bg-slate-800 text-amber-600 border-2 border-amber-100 dark:border-amber-900/30 transition-all">มาสาย</button>
                <button data-filter="2" class="filter-btn whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black bg-white dark:bg-slate-800 text-rose-600 border-2 border-rose-100 dark:border-rose-900/30 transition-all">ขาดเรียน</button>
                <button data-filter="leave" class="filter-btn whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black bg-white dark:bg-slate-800 text-blue-600 border-2 border-blue-100 dark:border-blue-900/30 transition-all">ลาป่วย/ลากิจ</button>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div id="statsContainer" class="hidden grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 no-print">
        <!-- Stats cards dynamically injected -->
    </div>

    <!-- Main Content Results -->
    <div id="reportContainer" class="hidden space-y-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-list-ul text-xl"></i>
                    </div>
                    <div>
                        <h3 id="reportTitle" class="text-2xl font-black text-slate-800 dark:text-white">รายชื่อนักเรียน</h3>
                        <p class="text-sm text-slate-500">ข้อมูลการเช็คชื่อ ณ วันที่ระบุ</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 no-print">
                    <button id="printBtn" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl font-black text-slate-600 hover:border-indigo-500 hover:text-indigo-600 transition-all shadow-sm">
                        <i class="fas fa-print"></i> พิมพ์รายงาน
                    </button>
                </div>
            </div>

            <div class="print-only text-center mb-10 border-b-2 border-slate-900 pb-6 uppercase tracking-widest">
                <h1 class="text-3xl font-black">โรงเรียนพิชัยรัตนาคาร</h1>
                <h2 id="printTitle" class="text-xl font-bold mt-2">รายงานการมาสาย-ขาดเรียน</h2>
                <p id="printSubtitle" class="text-sm font-medium mt-1">เทอม <?php echo $term; ?>/<?php echo $pee; ?></p>
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-hidden rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-inner">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">เลขที่</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">ชื่อ-นามสกุล</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">สถานะ</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">เบอร์ผู้ปกครอง</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300"></tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div id="reportCardsBody" class="md:hidden grid grid-cols-1 gap-2"></div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="animate-fadeIn py-20 text-center">
        <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-100 dark:border-slate-800">
            <i class="fas fa-calendar-check text-5xl text-slate-200"></i>
        </div>
        <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest animate-pulse">กรุณาเลือกชั้นและห้องเรียน</h3>
        <p class="text-slate-400 mt-2 font-medium">ระบุเงื่อนไขด้านบนเพื่อแสดงรายงาน</p>
    </div>
</div>

<script>
// State Management
let currentData = [];
let searchTimer;

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial Data (Classes)
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

    // 2. Class Change Listener
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

    // 3. Form Submit
    const filterForm = document.getElementById('filterForm');
    if(filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const classVal = document.getElementById('classSelect').value;
            const roomVal = document.getElementById('roomSelect').value;
            const dateVal = document.getElementById('dateInput').value;

            if (!classVal || !roomVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาระบุข้อมูล',
                    text: 'เลือกชั้นและห้องเรียนให้ครบถ้วน',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            Swal.showLoading();
            fetch(`api/api_get_late_report.php?class=${classVal}&room=${roomVal}&date=${dateVal}`)
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    currentData = data;
                    document.getElementById('searchFilterBar').classList.remove('hidden');
                    document.getElementById('searchInput').value = '';
                    document.querySelectorAll('.filter-btn').forEach(btn => {
                        btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                        if(btn.dataset.filter === 'all') btn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                    });
                    renderResults(data, classVal, roomVal, dateVal);
                })
                .catch(err => {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
                });
        });
    }

    // 4. Search & Filters
    const searchInput = document.getElementById('searchInput');
    if(searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => applyFilters(), 300);
        });
    }

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600'));
            this.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            applyFilters();
        });
    });

    const printBtn = document.getElementById('printBtn');
    if(printBtn) printBtn.addEventListener('click', () => window.print());
});

/**
 * UI Rendering Functions
 */

function applyFilters() {
    const searchInput = document.getElementById('searchInput');
    const activeBtn = document.querySelector('.filter-btn.bg-indigo-600');
    if(!searchInput || !activeBtn) return;
    
    const searchTerm = searchInput.value.toLowerCase();
    const activeFilter = activeBtn.dataset.filter;
    
    const filtered = currentData.filter(stu => {
        const matchesSearch = 
            (stu.Stu_name && stu.Stu_name.toLowerCase().includes(searchTerm)) || 
            (stu.Stu_sur && stu.Stu_sur.toLowerCase().includes(searchTerm)) || 
            (stu.Stu_id && stu.Stu_id.toString().includes(searchTerm));
        
        let matchesStatus = true;
        if (activeFilter === 'leave') {
            matchesStatus = ['4', '5'].includes(stu.attendance_status);
        } else if (activeFilter !== 'all') {
            matchesStatus = stu.attendance_status == activeFilter;
        }
        return matchesSearch && matchesStatus;
    });

    const classVal = document.getElementById('classSelect').value;
    const roomVal = document.getElementById('roomSelect').value;
    const dateVal = document.getElementById('dateInput').value;
    renderResults(filtered, classVal, roomVal, dateVal, true);
}

function renderResults(data, classVal, roomVal, dateVal, isFiltering = false) {
    const container = document.getElementById('reportContainer');
    const emptyState = document.getElementById('emptyState');
    const statsContainer = document.getElementById('statsContainer');
    const tableBody = document.getElementById('reportTableBody');
    const cardsBody = document.getElementById('reportCardsBody');
    const reportTitle = document.getElementById('reportTitle');
    const printTitle = document.getElementById('printTitle');

    if (!data || data.length === 0) {
        if (!isFiltering) {
            if(container) container.classList.add('hidden');
            if(statsContainer) statsContainer.classList.add('hidden');
            if(document.getElementById('searchFilterBar')) document.getElementById('searchFilterBar').classList.add('hidden');
        }
        if(emptyState) {
            emptyState.classList.remove('hidden');
            emptyState.innerHTML = `
                <div class="py-20">
                    <i class="fas fa-folder-open text-5xl text-slate-200 mb-4 block"></i>
                    <h3 class="text-xl font-black text-slate-400 uppercase">ไม่พบข้อมูล</h3>
                </div>`;
        }
        return;
    }

    if(emptyState) emptyState.classList.add('hidden');
    if(container) container.classList.remove('hidden');
    if(!isFiltering && statsContainer) statsContainer.classList.remove('hidden');

    const formattedDate = thaiDate(dateVal);
    if(reportTitle) reportTitle.innerHTML = `ชั้น ม.${classVal}/${roomVal} <span class="text-indigo-500 ml-2 font-black text-sm block md:inline">${formattedDate}</span>`;
    if(printTitle) printTitle.innerText = `รายงานการเข้าเรียนชั้น ม.${classVal}/${roomVal} ประจำวันที่ ${formattedDate}`;

    let stat = { 1: 0, 2: 0, 3: 0, leave: 0 };
    data.forEach(s => {
        if (s.attendance_status == '1') stat[1]++;
        else if (s.attendance_status == '2') stat[2]++;
        else if (s.attendance_status == '3') stat[3]++;
        else if (['4', '5'].includes(s.attendance_status)) stat.leave++;
    });

    if(statsContainer) {
        statsContainer.innerHTML = `
            ${renderStatCard('ทั้งหมด', data.length, 'fa-users', 'indigo')}
            ${renderStatCard('มาเรียน', stat[1], 'fa-check-circle', 'emerald')}
            ${renderStatCard('มาสาย', stat[3], 'fa-clock', 'amber')}
            ${renderStatCard('ขาด/ลา', stat[2] + stat.leave, 'fa-times-circle', 'rose')}
        `;
    }

    if(tableBody) tableBody.innerHTML = '';
    if(cardsBody) cardsBody.innerHTML = '';

    data.forEach(stu => {
        const info = stu.attendance_status_info;
        const stuImg = stu.Stu_picture ? `../img/student/${stu.Stu_picture}` : `../dist/img/default-avatar.svg`;
        const onerror = "this.src='../dist/img/default-avatar.svg';";
        
        // Desktop
        const tr = document.createElement('tr');
        tr.className = "hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group";
        tr.innerHTML = `
            <td class="px-8 py-6">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-black text-slate-400 w-6">${stu.Stu_no}</span>
                    <div class="relative">
                        <img src="${stuImg}" onerror="${onerror}" class="w-12 h-12 rounded-2xl object-cover shadow-sm group-hover:scale-105 transition-transform border border-slate-100 dark:border-slate-800">
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white dark:bg-slate-900 rounded-lg flex items-center justify-center shadow-sm border border-slate-50">
                            <span class="text-[10px]">${info.emoji}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-8 py-6">
                <div class="font-black text-slate-800 dark:text-white leading-tight">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">ID: ${stu.Stu_id}</div>
            </td>
            <td class="px-8 py-6 text-center">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black ${info.color.replace('text-', 'bg-').replace('600', '100').replace('700', '100')} ${info.color}">
                    ${info.text}
                </span>
            </td>
            <td class="px-8 py-6">
                <div class="flex items-center gap-3">
                    <a href="tel:${stu.parent_tel}" class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all">
                        <i class="fas fa-phone-alt text-xs"></i>
                    </a>
                    <span class="text-sm font-black text-slate-600 dark:text-slate-400">${stu.parent_tel || '-'}</span>
                </div>
            </td>`;
        if(tableBody) tableBody.appendChild(tr);

        // Mobile
        const card = document.createElement('div');
        card.className = "glass-effect p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden group transition-all";
        card.innerHTML = `
            <div class="flex items-start gap-4 mb-6">
                <div class="relative flex-shrink-0">
                    <img src="${stuImg}" onerror="${onerror}" class="w-20 h-20 rounded-[1.8rem] object-cover shadow-lg border-2 border-white dark:border-slate-700">
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-md border border-slate-50 dark:border-slate-700">
                        <span class="text-lg">${info.emoji}</span>
                    </div>
                </div>
                <div class="flex-1 min-w-0 pt-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ลำดับที่ ${stu.Stu_no}</span>
                        <span class="text-[10px] font-black text-indigo-500 bg-indigo-50 dark:bg-indigo-900/40 px-2 py-0.5 rounded-md">ID: ${stu.Stu_id}</span>
                    </div>
                    <h4 class="text-base font-black text-slate-800 dark:text-white leading-tight break-words">${stu.Stu_pre}${stu.Stu_name} ${stu.Stu_sur}</h4>
                    <div class="mt-2">
                         <span class="inline-flex px-3 py-1 bg-slate-100 dark:bg-slate-900 text-[10px] font-black ${info.color} rounded-lg uppercase border border-slate-200 dark:border-slate-800">${info.text}</span>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-slate-50/50 dark:bg-slate-900/50 rounded-[1.5rem] border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white dark:bg-slate-800 shadow-sm text-indigo-600 rounded-xl flex items-center justify-center border border-slate-100 dark:border-slate-700">
                            <i class="fas fa-phone-alt text-sm"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทรผู้ปกครอง</span>
                            <span class="text-sm font-black text-slate-700 dark:text-slate-300">${stu.parent_tel || '-'}</span>
                        </div>
                    </div>
                    <a href="tel:${stu.parent_tel}" class="w-12 h-12 bg-indigo-600 text-white rounded-xl shadow-lg flex items-center justify-center active:scale-95 transition-transform">
                        <i class="fas fa-phone-volume"></i>
                    </a>
                </div>
            </div>`;
        if(cardsBody) cardsBody.appendChild(card);
    });
}

function renderStatCard(label, value, icon, color) {
    return `
        <div class="glass-effect p-6 rounded-3xl border-b-4 border-${color}-500 shadow-lg shadow-${color}-500/10">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-${color}-100 dark:bg-${color}-900/30 text-${color}-600 dark:text-${color}-400 rounded-lg flex items-center justify-center">
                    <i class="fas ${icon} text-sm"></i>
                </div>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">${label}</span>
            </div>
            <div class="text-2xl font-black text-slate-800 dark:text-white tabular-nums">${value}</div>
        </div>`;
}

function thaiDate(strDate) {
    if (!strDate) return '-';
    const months = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
    const d = new Date(strDate);
    if(isNaN(d.getTime())) return strDate;
    return `${d.getDate()} ${months[d.getMonth() + 1]} ${d.getFullYear() + 543}`;
}

function shortThaiDate(strDate) {
    if (!strDate) return '-';
    const months = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
    const d = new Date(strDate);
    if(isNaN(d.getTime())) return strDate;
    return `${d.getDate()} ${months[d.getMonth() + 1]} ${d.getFullYear() + 543}`;
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
