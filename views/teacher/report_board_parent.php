<?php
/**
 * View: Board Parent Report
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
    
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .glass-effect { box-shadow: none !important; border: 1px solid #1e293b !important; background: white !important; }
        body { background: white !important; }
        .content-wrapper { margin: 0 !important; }
        @page { size: portrait; margin: 1cm; }
        #report-table-container { display: block !important; width: 100% !important; margin: 0 !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #e2e8f0 !important; padding: 12px 10px !important; color: black !important; font-size: 10pt !important; }
    }
    .print-only { display: none; }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-teal-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl flex items-center justify-center text-white shadow-xl transform hover:rotate-6 transition-transform">
                        <i class="fas fa-users-cog text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            คณะกรรมการเครือข่ายผู้ปกครอง
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1 uppercase tracking-wider text-sm">
                            ปีการศึกษา <?php echo $pee; ?>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 no-print">
                    <button onclick="window.print()" class="px-6 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl font-black text-slate-600 hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm flex items-center gap-2">
                        <i class="fas fa-print"></i> พิมพ์รายงาน
                    </button>
                    <!-- Small Indicator -->
                    <div class="hidden md:flex px-6 py-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl border border-emerald-100 dark:border-emerald-800 text-center">
                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest block mb-1">ปีการศึกษา</span>
                        <span class="text-lg font-black text-emerald-600 dark:text-emerald-400"><?php echo $pee; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <div class="max-w-md mx-auto space-y-3">
            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic block text-center">กรุณาเลือกระดับชั้นเพื่อแสดงข้อมูล</label>
            <div class="relative group">
                <select id="class_select" class="w-full pl-6 pr-12 py-5 bg-slate-50 dark:bg-slate-900 border-4 border-white dark:border-slate-800 rounded-[1.5rem] focus:ring-8 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all appearance-none cursor-pointer font-black text-lg text-slate-700 dark:text-white shadow-inner group-hover:border-emerald-100">
                    <option value="">-- เลือกระดับชั้น --</option>
                    <?php foreach ($available_classes as $lvl) : ?>
                        <option value="<?php echo $lvl; ?>">มัธยมศึกษาปีที่ <?php echo $lvl; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-500 text-xl">
                    <i class="fas fa-chevron-circle-down"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State (Hidden by default) -->
    <div id="loading_state" class="hidden py-20 text-center animate-fadeIn">
        <div class="inline-block relative">
            <div class="w-16 h-16 border-4 border-emerald-100 border-t-emerald-500 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-4 h-4 bg-emerald-500 rounded-full animate-pulse"></div>
            </div>
        </div>
        <p class="mt-4 text-emerald-500 font-black italic animate-pulse">กำลังเรียกข้อมูล...</p>
    </div>

    <!-- Print Only Header -->
    <div class="print-only text-center mb-10 pb-6 border-b-2 border-slate-900">
        <h1 class="text-3xl font-black">โรงเรียนพิชัย</h1>
        <h2 class="text-xl font-bold mt-1 italic italic">รายงานคณะกรรมการเครือข่ายผู้ปกครอง</h2>
        <p class="mt-2 text-sm font-black italic tracking-widest uppercase">ปีการศึกษา <?php echo $pee; ?></p>
    </div>

    <!-- Results Area -->
    <div id="results_area" class="hidden space-y-6 animate-fadeIn" style="animation-delay: 0.2s">
        <!-- Desktop Table view -->
        <div class="hidden lg:block glass-effect rounded-[2.5rem] p-6 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden">
            <div id="report-table-container">
                <table class="w-full text-left" id="report_table">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center w-[8%]">ลำดับ</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center w-[12%]">ชั้น/ห้อง</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic w-[25%]">ชื่อ - นามสกุล</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ที่อยู่</th>
                            <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center w-[15%]">เบอร์โทรศัพท์</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div id="mobile_cards" class="lg:hidden grid grid-cols-1 gap-4 no-print">
            <!-- Filled by JS -->
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty_state" class="glass-effect rounded-[3rem] py-20 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-inner animate-fadeIn">
        <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-100 dark:border-slate-800">
            <i class="fas fa-id-card-alt text-5xl text-slate-200"></i>
        </div>
        <h3 class="text-2xl font-black text-slate-300 uppercase tracking-widest italic">โปรดเลือกระดับชั้น</h3>
        <p class="text-slate-400 mt-2 font-medium italic tracking-tight">เพื่อแสดงรายชื่อประธาน/เลขานุการ เครือข่ายผู้ปกครองครับผม</p>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#class_select').on('change', function() {
        const classVal = $(this).val();
        const resultsArea = $('#results_area');
        const emptyState = $('#empty_state');
        const loadingState = $('#loading_state');
        const tableBody = $('#report_table tbody');
        const mobileCards = $('#mobile_cards');

        if (!classVal) {
            resultsArea.addClass('hidden');
            emptyState.removeClass('hidden');
            return;
        }

        // Show Loading
        resultsArea.addClass('hidden');
        emptyState.addClass('hidden');
        loadingState.removeClass('hidden');

        $.ajax({
            url: 'api/fetch_boardparent_by_class.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classVal, pee: <?php echo json_encode($pee); ?> },
            success: function(response) {
                loadingState.addClass('hidden');
                tableBody.empty();
                mobileCards.empty();

                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(item, idx) {
                        const roomLabel = `ม.${item.parn_lev}/${item.parn_room}`;
                        const phone = item.parn_tel || 'ไม่มีข้อมูล';
                        
                        // Add Table Row
                        tableBody.append(`
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-6 text-center text-sm tabular-nums">${idx + 1}</td>
                                <td class="px-6 py-6 text-center">
                                    <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-black italic tracking-tighter border border-emerald-100/50">${roomLabel}</span>
                                </td>
                                <td class="px-8 py-6 text-base font-black text-slate-800 dark:text-white leading-tight">${item.parn_name}</td>
                                <td class="px-8 py-6 text-sm font-medium italic text-slate-500">${item.parn_addr || '-'}</td>
                                <td class="px-6 py-6 text-center text-sm tabular-nums font-black text-emerald-600 dark:text-emerald-400">${phone}</td>
                            </tr>
                        `);

                        // Add Mobile Card
                        mobileCards.append(`
                            <div class="glass-effect p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl relative overflow-hidden group animate-fadeIn" style="animation-delay: ${idx * 0.05}s">
                                <div class="absolute top-0 right-0 p-4">
                                    <span class="px-2 py-1 bg-emerald-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20">${roomLabel}</span>
                                </div>
                                <div class="flex items-center gap-4 mb-6 pt-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg shadow-emerald-500/20">
                                        ${idx + 1}
                                    </div>
                                    <div class="flex-1 min-w-0 pr-12">
                                        <h4 class="text-base font-black text-slate-800 dark:text-white leading-tight break-words uppercase tracking-tight">${item.parn_name}</h4>
                                        <div class="flex items-center gap-1.5 mt-2 text-emerald-600 dark:text-emerald-400">
                                            <i class="fas fa-phone-alt text-[10px]"></i>
                                            <span class="text-xs font-black tabular-nums">${phone}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-800">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 italic">ที่อยู่ติดต่อ:</span>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400 italic leading-relaxed">
                                        ${item.parn_addr || 'ไม่มีข้อมูลที่อยู่'}
                                    </p>
                                </div>
                            </div>
                        `);
                    });
                    resultsArea.removeClass('hidden');
                } else {
                    // Show some empty state for the class
                    emptyState.removeClass('hidden');
                    emptyState.find('h3').text('ไม่พบข้อมูลสำหรับชั้นนี้');
                    emptyState.find('p').text('ขออภัย! ในปีการศึกษานี้ยังไม่มีการระบุรายชื่อคณะกรรมการสำหรับชั้นดังกล่าครับผม');
                }
            },
            error: function() {
                loadingState.addClass('hidden');
                emptyState.removeClass('hidden');
                emptyState.find('h3').text('เกิดข้อผิดพลาด');
                emptyState.find('p').text('ไม่สามารถโหลดข้อมูลได้ในขณะนี้ โปรดลองใหม่อีกครั้งครับผม');
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
