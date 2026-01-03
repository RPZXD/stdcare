<?php
/**
 * View: Student Attendance Data (Officer)
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive Design
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
    
    .attendance-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .attendance-card:hover {
        transform: translateY(-5px);
    }

    /* DataTable Overrides */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 1rem !important;
        margin: 0 0.2rem !important;
        border-radius: 1rem !important;
        border: none !important;
        font-weight: 800 !important;
        background: transparent !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        color: white !important;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3) !important;
    }

    .status-badge-custom {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 2rem;
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-emerald-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col xl:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-emerald-600 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-clipboard-check text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            ข้อมูลการ <span class="text-indigo-600 italic">เช็คชื่อนักเรียน</span>
                        </h1>
                        <div class="flex flex-wrap items-center gap-4 mt-2">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl text-xs font-black uppercase tracking-widest border border-indigo-500/10">
                                <i class="fas fa-calendar-day"></i> <?php echo thaiDateView(convertToBuddhistYearView($date)); ?>
                            </span>
                            <?php if ($class): ?>
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl text-xs font-black uppercase tracking-widest border border-emerald-500/10">
                                <i class="fas fa-school"></i> ชั้น ม.<?php echo htmlspecialchars($class); ?><?php echo $room ? '/' . htmlspecialchars($room) : ''; ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Action Filters -->
                <form id="filterForm" method="get" class="flex flex-wrap items-end gap-3 bg-white/50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-white/50 dark:border-slate-700/50">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-focus-within:text-indigo-500 transition-colors block">เลือกวันที่</label>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>"
                            class="px-4 py-3 bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-black text-slate-700 dark:text-white text-sm">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-focus-within:text-indigo-500 transition-colors block">ชั้น</label>
                        <select name="class" class="px-4 py-3 bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-black text-slate-700 dark:text-white text-sm min-w-[100px]">
                            <option value="">ทุกชั้น</option>
                            <?php for($i = 1; $i <= 6; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $class == $i ? 'selected' : ''; ?>>ม.<?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-focus-within:text-indigo-500 transition-colors block">ห้อง</label>
                        <select name="room" class="px-4 py-3 bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-black text-slate-700 dark:text-white text-sm min-w-[100px]">
                            <option value="">ทุกห้อง</option>
                            <?php for($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $room == $i ? 'selected' : ''; ?>>ห้อง <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-3.5 bg-indigo-600 text-white rounded-xl font-black shadow-lg shadow-indigo-600/20 hover:shadow-xl hover:scale-105 transition-all">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Stats Overview (Optional but premium) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 animate-fadeIn" style="animation-delay: 0.1s">
        <div class="glass-effect p-6 rounded-[2rem] shadow-xl border border-white/50 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">มาเรียน</p>
                <p id="stat-present" class="text-xl font-black text-slate-800 dark:text-white">...</p>
            </div>
        </div>
        <div class="glass-effect p-6 rounded-[2rem] shadow-xl border border-white/50 flex items-center gap-4">
            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-2xl flex items-center justify-center text-rose-600">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ขาดเรียน</p>
                <p id="stat-absent" class="text-xl font-black text-slate-800 dark:text-white">...</p>
            </div>
        </div>
        <div class="glass-effect p-6 rounded-[2rem] shadow-xl border border-white/50 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">มาสาย</p>
                <p id="stat-late" class="text-xl font-black text-slate-800 dark:text-white">...</p>
            </div>
        </div>
        <div class="glass-effect p-6 rounded-[2rem] shadow-xl border border-white/50 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600">
                <i class="fas fa-file-medical text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ลา</p>
                <p id="stat-leave" class="text-xl font-black text-slate-800 dark:text-white">...</p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="glass-effect rounded-[2.5rem] p-8 shadow-2xl border border-white/50 dark:border-slate-700/50 animate-fadeIn" style="animation-delay: 0.2s">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-list-ul"></i>
                </div>
                <div>
                    <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">รายชื่อการเข้าเรียน</h4>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Student Attendance List</p>
                </div>
            </div>
            <div id="table-buttons" class="flex gap-2"></div>
        </div>

        <div class="flex-1 min-h-[500px]">
            <!-- Desktop Table View -->
            <div id="desktopView" class="hidden md:block">
                <table id="attendanceTable" class="w-full text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-l-2xl">เลขที่ / นักเรียน</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">สถานะ</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เวลาเข้า-ออก</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">หมายเหตุ/สาเหตุ</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic rounded-r-2xl text-center">บันทึกโดย</th>
                        </tr>
                    </thead>
                    <tbody class="font-bold text-slate-700 dark:text-slate-300">
                        <!-- DataTables Populated -->
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div id="mobileView" class="md:hidden grid grid-cols-1 gap-6">
                <!-- DataTables custom render populated -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    const API_URL = 'api/api_attendance_data.php';
    let attendanceTable;

    // Helper: Escape HTML
    function escapeHtml(str) {
        if (!str && str !== 0) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // Initialize DataTable
    attendanceTable = $('#attendanceTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: API_URL,
            type: 'GET',
            data: function(d) {
                d.date = '<?php echo $date; ?>';
                d.class = '<?php echo $class; ?>';
                d.room = '<?php echo $room; ?>';
                d.term = '<?php echo $term; ?>';
                d.pee = '<?php echo $pee; ?>';
            },
            dataSrc: function(json) {
                // Update stats
                if (json.stats) {
                    $('#stat-present').text(json.stats.present || 0);
                    $('#stat-absent').text(json.stats.absent || 0);
                    $('#stat-late').text(json.stats.late || 0);
                    $('#stat-leave').text(json.stats.leave || 0);
                }
                return json.data;
            }
        },
        columns: [
            { 
                data: null,
                className: "px-6 py-5 rounded-l-2xl bg-white dark:bg-slate-900 shadow-sm",
                render: function(data, type, row) {
                    const firstName = row.stu_name.split(' ')[0] || '';
                    const initial = firstName.charAt(0).toUpperCase();
                    return `
                        <div class="flex items-center gap-4">
                            <div class="hidden sm:flex w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 items-center justify-center text-indigo-500 font-black italic">
                                ${row.stu_no}
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-black shadow-lg">
                                    ${initial}
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="text-[13px] font-black text-slate-800 dark:text-white truncate">${row.stu_name}</div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight italic">ID: ${row.stu_id}</div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'attendance_status',
                className: "px-6 py-5 bg-white dark:bg-slate-900 shadow-sm text-center",
                render: function(data) {
                    const statusConfig = {
                        '1': { label: 'มาเรียน', color: 'emerald', icon: 'check-circle' },
                        '2': { label: 'ขาดเรียน', color: 'rose', icon: 'times-circle' },
                        '3': { label: 'มาสาย', color: 'amber', icon: 'clock' },
                        '4': { label: 'ลาป่วย', color: 'indigo', icon: 'hand-holding-medical' },
                        '5': { label: 'ลากิจ', color: 'indigo', icon: 'file-alt' },
                        '6': { label: 'กิจกรรม', color: 'pink', icon: 'star' }
                    };
                    const cfg = statusConfig[data] || { label: 'ยังไม่เช็ค', color: 'slate', icon: 'question-circle' };
                    return `
                        <span class="status-badge-custom bg-${cfg.color}-50 dark:bg-${cfg.color}-900/30 text-${cfg.color}-600 dark:text-${cfg.color}-400 border border-${cfg.color}-500/10">
                            <i class="fas fa-${cfg.icon}"></i> ${cfg.label}
                        </span>
                    `;
                }
            },
            { 
                data: null,
                className: "px-6 py-5 bg-white dark:bg-slate-900 shadow-sm",
                render: function(data, type, row) {
                    if (!row.arrival_time && !row.leave_time) return `<span class="text-slate-300 italic font-medium text-xs">--:--</span>`;
                    let html = `<div class="flex flex-col gap-1">`;
                    if (row.arrival_time) html += `<div class="text-[11px] font-black text-emerald-500 italic"><i class="fas fa-sign-in-alt w-4 text-center"></i> ${row.arrival_time.substring(0,5)} น.</div>`;
                    if (row.leave_time) html += `<div class="text-[11px] font-black text-rose-500 italic"><i class="fas fa-sign-out-alt w-4 text-center"></i> ${row.leave_time.substring(0,5)} น.</div>`;
                    html += `</div>`;
                    return html;
                }
            },
            { 
                data: 'reason',
                className: "px-6 py-5 bg-white dark:bg-slate-900 shadow-sm",
                render: data => data ? `<span class="text-[11px] font-bold text-slate-500 line-clamp-2">${escapeHtml(data)}</span>` : `<span class="text-slate-300 italic">--</span>`
            },
            { 
                data: 'checked_by',
                className: "px-6 py-5 rounded-r-2xl bg-white dark:bg-slate-900 shadow-sm text-center",
                render: function(data, type, row) {
                    if (!data) return `<span class="text-slate-300 italic">--</span>`;
                    const isSystem = ['system', 'teacher', 'rfid', 'RFID'].includes(data.toLowerCase());
                    const label = isSystem ? (data.toLowerCase().includes('rfid') ? 'สแกนบัตร' : 'ฝ่ายปกครอง') : 'เจ้าหน้าที่';
                    const icon = data.toLowerCase().includes('rfid') ? 'id-card' : 'user-shield';
                    const color = data.toLowerCase().includes('rfid') ? 'amber' : 'indigo';
                    
                    return `
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-${color}-500/10 text-${color}-600 dark:text-${color}-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-${color}-500/20">
                            <i class="fas fa-${icon}"></i> ${label}
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']],
        pageLength: 50,
        dom: 'rtp', // Simplified DOM for premium feel
        drawCallback: function(settings) {
            renderMobileCards(this.api().rows({page:'current'}).data().toArray());
        },
        language: {
            processing: '<div class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-slate-950/80 z-50"><div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div></div>'
        }
    });

    // Mobile View Rendering
    function renderMobileCards(data) {
        const $container = $('#mobileView').empty();
        data.forEach((row, index) => {
            const statusConfig = {
                '1': { label: 'มาเรียน', color: 'emerald', icon: 'check-circle' },
                '2': { label: 'ขาดเรียน', color: 'rose', icon: 'times-circle' },
                '3': { label: 'มาสาย', color: 'amber', icon: 'clock' },
                '4': { label: 'ลาป่วย', color: 'indigo', icon: 'hand-holding-medical' },
                '5': { label: 'ลากิจ', color: 'indigo', icon: 'file-alt' },
                '6': { label: 'กิจกรรม', color: 'pink', icon: 'star' }
            };
            const cfg = statusConfig[row.attendance_status] || { label: 'ยังไม่เช็ค', color: 'slate', icon: 'question-circle' };
            
            $container.append(`
                <div class="attendance-card glass-effect p-6 rounded-[2rem] shadow-xl border border-white/50 animate-fadeIn" style="animation-delay: ${index * 0.05}s">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-black shadow-lg">
                                ${row.stu_no}
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 dark:text-white">${row.stu_name}</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase italic tracking-tight">ID: ${row.stu_id}</p>
                            </div>
                        </div>
                        <span class="status-badge-custom bg-${cfg.color}-50 dark:bg-${cfg.color}-900/30 text-${cfg.color}-600 dark:text-${cfg.color}-400">
                             <i class="fas fa-${cfg.icon}"></i> ${cfg.label}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-indigo-50/50 dark:bg-indigo-900/20 p-4 rounded-2xl border border-indigo-100/50 dark:border-indigo-800/30">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">เวลาสแกน</p>
                            <div class="flex flex-col gap-1">
                                <span class="text-[11px] font-black text-emerald-500 italic"><i class="fas fa-sign-in-alt w-4"></i> ${row.arrival_time ? row.arrival_time.substring(0,5) + ' น.' : '--:--'}</span>
                                <span class="text-[11px] font-black text-rose-500 italic"><i class="fas fa-sign-out-alt w-4"></i> ${row.leave_time ? row.leave_time.substring(0,5) + ' น.' : '--:--'}</span>
                            </div>
                        </div>
                        <div class="bg-slate-50/50 dark:bg-slate-900/20 p-4 rounded-2xl border border-slate-100/50 dark:border-slate-800/30">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">บันทึกโดย</p>
                            <p class="text-[10px] font-black text-slate-600 dark:text-slate-300 truncate"><i class="fas fa-user-check mr-1 text-indigo-400"></i> ${row.checked_by || '--'}</p>
                        </div>
                    </div>
                    
                    ${row.reason ? `
                    <div class="mt-4 p-4 bg-amber-50/30 dark:bg-amber-900/10 rounded-2xl border border-amber-100/30 dark:border-amber-800/20">
                        <p class="text-[9px] font-black text-amber-600/60 dark:text-amber-400/60 uppercase tracking-widest mb-1 italic">หมายเหตุ</p>
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400">${escapeHtml(row.reason)}</p>
                    </div>
                    ` : ''}
                </div>
            `);
        });
    }

    // Filter Form Logic
    $('#filterForm').on('submit', function(e){
        e.preventDefault();
        const params = new URLSearchParams(new FormData(this));
        window.history.pushState({}, '', '?' + params.toString());
        attendanceTable.ajax.reload();
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
