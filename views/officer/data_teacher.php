<?php
/**
 * View: Teacher Data Management (Officer)
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
    
    .loading-spinner {
        border: 3px solid rgba(51, 65, 85, 0.1);
        border-top: 3px solid #6366f1;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .teacher-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .teacher-card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-700 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-chalkboard-teacher text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            ข้อมูลบุคลากร <span class="text-indigo-600 italic">ทั้งหมด</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1 italic">
                            จัดการข้อมูลครูและเจ้าหน้าที่ในระบบ ค้นหา และตรวจสอบสถานะ
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn" style="animation-delay: 0.1s">
        <div class="flex flex-col lg:flex-row items-center gap-6">
            <!-- Search -->
            <div class="flex-1 w-full relative group">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                <input type="text" id="teacherSearch" placeholder="ค้นหาด้วยชื่อ, กลุ่มสาระ, หรือบทบาท..." 
                    class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white shadow-inner">
            </div>
            
            <div class="flex items-center gap-3 w-full lg:w-auto">
                <button id="btnRefresh" class="flex-1 lg:flex-none h-[58px] px-8 bg-indigo-500 hover:bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2 active:scale-95">
                    <i class="fas fa-sync-alt"></i> รีเฟรชข้อมูล
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table & Mobile Cards -->
    <div class="relative min-h-[400px]">
        <!-- Loading State -->
        <div id="loadingState" class="hidden absolute inset-0 z-10 flex flex-col items-center justify-center bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm rounded-[3rem]">
            <div class="loading-spinner mb-4"></div>
            <p class="text-indigo-600 dark:text-indigo-400 font-black animate-pulse">กำลังดึงข้อมูล...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="py-40 text-center glass-effect rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-user-slash text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-black text-slate-400 uppercase tracking-widest italic">ไม่พบข้อมูลบุคลากร</h3>
            <p class="text-slate-400 mt-2 font-medium italic">ไม่พบบุคลากรที่ตรงกับเงื่อนไขการค้นหา</p>
        </div>

        <!-- Desktop View (Table) -->
        <div id="desktopView" class="hidden overflow-hidden rounded-[2.5rem] glass-effect shadow-2xl border border-white/50 dark:border-slate-700/50">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-indigo-50/50 dark:bg-indigo-900/20 border-b border-indigo-100/50 dark:border-indigo-800/50">
                        <th class="px-8 py-6 text-[10px] font-black text-indigo-400/80 uppercase tracking-widest italic">รูป/ชื่อบุคลากร</th>
                        <th class="px-6 py-6 text-[10px] font-black text-indigo-400/80 uppercase tracking-widest italic text-center">บทบาท</th>
                        <th class="px-6 py-6 text-[10px] font-black text-indigo-400/80 uppercase tracking-widest italic">กลุ่มสาระ/ฝ่าย</th>
                        <th class="px-6 py-6 text-[10px] font-black text-indigo-400/80 uppercase tracking-widest italic">ที่ปรึกษา</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-indigo-400/80 uppercase tracking-widest italic">สถานะ</th>
                    </tr>
                </thead>
                <tbody id="teacherTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                    <!-- Dynamic Data -->
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Cards) -->
        <div id="mobileView" class="hidden lg:hidden grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Dynamic Data -->
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let allTeachers = [];

    // Initial Load
    loadTeachers();

    // Helper: Role Mapping
    function getRoleInfo(role) {
        switch(role) {
            case 'T': return { text: 'ครู', icon: 'fa-chalkboard-teacher', color: 'blue' };
            case 'OF': return { text: 'เจ้าหน้าที่', icon: 'fa-user-tie', color: 'indigo' };
            case 'VP': return { text: 'รองผู้อำนวยการ', icon: 'fa-user-graduate', color: 'violet' };
            case 'DIR': return { text: 'ผู้อำนวยการ', icon: 'fa-school', color: 'purple' };
            case 'ADM': return { text: 'Admin', icon: 'fa-shield-alt', color: 'rose' };
            default: return { text: role || 'อื่นๆ', icon: 'fa-user', color: 'slate' };
        }
    }

    // Load Data
    function loadTeachers() {
        $('#loadingState').removeClass('hidden');
        $.ajax({
            url: '../controllers/TeacherController.php?action=list',
            method: 'GET',
            success: function(data) {
                allTeachers = data;
                renderData(data);
                $('#loadingState').addClass('hidden');
            },
            error: function() {
                $('#loadingState').addClass('hidden');
                Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            }
        });
    }

    // Render Data
    function renderData(data) {
        const $tableBody = $('#teacherTableBody').empty();
        const $mobileContainer = $('#mobileView').empty();
        
        if (!data || data.length === 0) {
            $('#emptyState').removeClass('hidden');
            $('#desktopView, #mobileView').addClass('hidden');
            return;
        }

        $('#emptyState').addClass('hidden');
        if (window.innerWidth >= 1024) $('#desktopView').removeClass('hidden');
        else $('#mobileView').removeClass('hidden');

        data.forEach((t, index) => {
            const roleInfo = getRoleInfo(t.role_std);
            const statusBadge = t.Teach_status == '1' 
                ? `<span class="px-4 py-1.5 bg-emerald-500/10 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">ปกติ <i class="fas fa-check-circle ml-1"></i></span>`
                : `<span class="px-4 py-1.5 bg-rose-500/10 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-500/20">ไม่ใช้งาน <i class="fas fa-times-circle ml-1"></i></span>`;
            
            const classRoom = (t.Teach_class && t.Teach_room) ? `ม.${t.Teach_class}/${t.Teach_room}` : '-';
            const photoUrl = t.Teach_photo ? `https://std.phichai.ac.th/teacher/uploads/phototeach/${t.Teach_photo}` : '../dist/img/default-avatar.svg';

            // Desktop Row
            const row = `
                <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors teacher-row" data-search="${t.Teach_name} ${t.Teach_major} ${roleInfo.text}">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl overflow-hidden border-2 border-white shadow-lg flex-shrink-0">
                                <img src="${photoUrl}" class="w-full h-full object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                            </div>
                            <div class="min-w-0">
                                <div class="text-base font-black text-slate-800 dark:text-white truncate">${t.Teach_name}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">ID: ${t.Teach_id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-${roleInfo.color}-50 dark:bg-${roleInfo.color}-900/30 text-${roleInfo.color}-600 dark:text-${roleInfo.color}-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-${roleInfo.color}-500/10">
                            <i class="fas ${roleInfo.icon}"></i> ${roleInfo.text}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm font-black text-slate-600 dark:text-slate-400">${t.Teach_major}</div>
                    </td>
                    <td class="px-6 py-5 text-sm font-black text-indigo-500">${classRoom}</td>
                    <td class="px-8 py-5 text-right">${statusBadge}</td>
                </tr>
            `;
            $tableBody.append(row);

            // Mobile Card
            const card = `
                <div class="teacher-card glass-effect p-6 rounded-[2.5rem] shadow-xl border border-white/50 dark:border-slate-800 animate-fadeIn" style="animation-delay: ${index * 0.05}s">
                    <div class="flex items-center gap-5">
                        <div class="w-20 h-20 rounded-3xl overflow-hidden border-4 border-white dark:border-slate-700 shadow-2xl flex-shrink-0">
                            <img src="${photoUrl}" class="w-full h-full object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-lg font-black text-slate-800 dark:text-white leading-tight mb-2 truncate">${t.Teach_name}</div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-${roleInfo.color}-50 dark:bg-${roleInfo.color}-900/30 text-${roleInfo.color}-600 dark:text-${roleInfo.color}-400 rounded-lg text-[10px] font-black uppercase tracking-widest border border-${roleInfo.color}-500/10">
                                    <i class="fas ${roleInfo.icon}"></i> ${roleInfo.text}
                                </span>
                                ${statusBadge}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t dark:border-slate-700/50">
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">กลุ่มสาระ/ฝ่าย</span>
                            <span class="text-sm font-black text-slate-700 dark:text-slate-300 leading-tight">${t.Teach_major}</span>
                        </div>
                        <div class="bg-indigo-50/50 dark:bg-indigo-900/20 p-4 rounded-2xl">
                            <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest block mb-1 text-right">ครูที่ปรึกษา</span>
                            <span class="text-sm font-black text-indigo-600 block text-right">${classRoom}</span>
                        </div>
                    </div>
                </div>
            `;
            $mobileContainer.append(card);
        });
    }

    // Search Logic
    $('#teacherSearch').on('input', function() {
        const val = $(this).val().toLowerCase();
        $('.teacher-row').each(function() {
            const rowText = $(this).data('search').toLowerCase();
            $(this).toggle(rowText.indexOf(val) > -1);
        });
        // For mobile
        $('#mobileView .teacher-card').each(function() {
            const cardText = $(this).text().toLowerCase();
            $(this).toggle(cardText.indexOf(val) > -1);
        });
    });

    // Refresh
    $('#btnRefresh').on('click', function() {
        $(this).find('i').addClass('fa-spin');
        loadTeachers();
        setTimeout(() => $(this).find('i').removeClass('fa-spin'), 1000);
    });

    // Handle Resize
    window.addEventListener('resize', function() {
        if (allTeachers.length > 0) {
            if (window.innerWidth >= 1024) {
                $('#desktopView').removeClass('hidden');
                $('#mobileView').addClass('hidden');
            } else {
                $('#desktopView').addClass('hidden');
                $('#mobileView').removeClass('hidden');
            }
        }
    });

});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
