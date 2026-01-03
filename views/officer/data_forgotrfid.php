<?php
/**
 * View: Manage Forgot RFID Cases (Officer)
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
    
    .history-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .history-card:hover {
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
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-purple-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-purple-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-indigo-700 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-id-card text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            จัดการกรณี <span class="text-purple-600 italic">ลืมบัตร RFID</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1 italic">
                            เช็คชื่อเข้า-ออกด้วยตนเองสำหรับนักเรียนที่ทำบัตรหายหรือลืมบัตร (เทอม <?php echo "$term/$pee"; ?>)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <!-- Left Column: Search & Action -->
        <div class="xl:col-span-5 space-y-8">
            <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn" style="animation-delay: 0.1s">
                <div class="mb-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic mb-2 block">ค้นหานักเรียน</label>
                    <div class="relative group">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-purple-500 transition-colors"></i>
                        <input type="text" id="search-stu" autocomplete="off"
                            class="w-full pl-14 pr-6 py-5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-purple-100 outline-none transition-all font-black text-slate-700 dark:text-white"
                            placeholder="รหัสนักเรียน ชื่อ หรือนามสกุล...">
                        
                        <!-- Search Suggestions -->
                        <div id="search-suggestions" class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 max-h-80 overflow-y-auto hidden"></div>
                    </div>
                </div>

                <!-- Student Preview Display -->
                <div id="stu-preview" class="hidden animate-fadeIn space-y-6">
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/40 dark:to-purple-900/40 p-8 rounded-[2rem] border border-indigo-100 dark:border-indigo-800 shadow-inner">
                        <div class="flex flex-col items-center text-center space-y-4">
                            <div class="relative">
                                <div class="absolute inset-0 bg-white dark:bg-slate-700 rounded-full blur-2xl opacity-50"></div>
                                <img id="stu-photo" src="../dist/img/default-avatar.svg" class="relative w-40 h-40 rounded-full object-cover border-8 border-white dark:border-slate-800 shadow-2xl">
                                <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl shadow-xl flex items-center justify-center text-2xl">
                                    📸
                                </div>
                            </div>
                            <div class="space-y-1">
                                <h3 id="stu-name" class="text-2xl font-black text-slate-800 dark:text-white">...</h3>
                                <p id="stu-class" class="text-sm font-bold text-indigo-500 uppercase tracking-widest">...</p>
                                <p id="stu-id" class="text-xs font-bold text-slate-400 tracking-tighter">...</p>
                            </div>
                            
                            <div class="w-full pt-4">
                                <div class="bg-amber-50 dark:bg-amber-900/30 p-4 rounded-2xl border border-amber-100 dark:border-amber-800/50 flex flex-col items-center">
                                    <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1 italic">สถิติลืมบัตรเทอมนี้</span>
                                    <div class="flex items-center gap-2">
                                        <span id="forgot-count" class="text-3xl font-black text-amber-600 italic">0</span>
                                        <span class="text-sm font-bold text-amber-500">ครั้ง</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-8">
                            <button id="btn-manual-arrival" class="group px-6 py-5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-black shadow-xl shadow-emerald-500/30 hover:shadow-2xl hover:scale-105 transition-all flex flex-col items-center gap-2 disabled:opacity-50 disabled:grayscale">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <span>เช็คชื่อเข้า</span>
                            </button>
                            <button id="btn-manual-leave" class="group px-6 py-5 bg-gradient-to-r from-rose-500 to-pink-600 text-white rounded-2xl font-black shadow-xl shadow-rose-500/30 hover:shadow-2xl hover:scale-105 transition-all flex flex-col items-center gap-2 disabled:opacity-50 disabled:grayscale">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <span>เช็คชื่อออก</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="stu-empty" class="py-20 text-center space-y-4 bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
                    <div class="w-24 h-24 bg-white dark:bg-slate-800 rounded-3xl shadow-xl flex items-center justify-center mx-auto text-4xl animate-bounce">
                        🔍
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-lg font-black text-slate-700 dark:text-white">ค้นหานักเรียนเพื่อเริ่มบันทึก</h4>
                        <p class="text-xs font-bold text-slate-400 italic">กรุณาพิมพ์รหัสนักเรียนหรือชื่อในช่องค้นหาด้านบน</p>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn" style="animation-delay: 0.2s">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">คู่มือการใช้งาน</h4>
                </div>
                <div class="space-y-4">
                    <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black flex-shrink-0">1</span>
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 leading-relaxed">
                            <strong class="text-slate-800 dark:text-slate-200">ค้นหานักเรียน:</strong> ค้นหาด้วยรหัสหรือชื่อ แล้วเลือกนักเรียนจากรายการ
                        </p>
                    </div>
                    <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black flex-shrink-0">2</span>
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 leading-relaxed">
                            <strong class="text-slate-800 dark:text-slate-200">บันทึกเช็คชื่อ:</strong> กดปุ่ม "เช็คชื่อเข้า" หรือ "เช็คชื่อออก" ระบบจะบันทึกว่ามาเรียนปกติเสมอ
                        </p>
                    </div>
                    <div class="flex gap-4 p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800/50">
                        <span class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-900 text-rose-600 dark:text-rose-400 flex items-center justify-center font-black flex-shrink-0">!</span>
                        <p class="text-xs font-bold text-rose-600 dark:text-rose-400 leading-relaxed italic">
                            ระบบจะนับจำนวนครั้งที่ลืมบัตรโดยอัตโนมัติ หากเกิน 3 ครั้งจะมีการส่งข้อมูลเพื่อพิจารณาหักคะแนนพฤติกรรม
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: History -->
        <div class="xl:col-span-7 space-y-8">
            <div class="glass-effect rounded-[2.5rem] p-8 shadow-2xl border border-white/50 dark:border-slate-700/50 animate-fadeIn h-full flex flex-col" style="animation-delay: 0.3s">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">ประวัติการลืมบัตร</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Forgot RFID History Records</p>
                        </div>
                    </div>
                    <div id="table-buttons" class="flex gap-2"></div>
                </div>

                <div class="flex-1 min-h-[500px]">
                    <!-- Desktop Table -->
                    <div id="desktopView" class="hidden md:block">
                        <table id="forgotTable" class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">นักเรียน</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">ชั้น/ห้อง</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">วันที่ลืม</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                                <!-- DataTables -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div id="mobileView" class="md:hidden grid grid-cols-1 gap-6">
                        <!-- Dynamic Cards -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    const API_ATTENDANCE = '../controllers/AttendanceController.php';
    const API_BEHAVIOR = '../controllers/BehaviorController.php';
    let forgotTable;

    // Helper: Reset Preview
    function resetPreview(){
        $('#stu-preview').addClass('hidden');
        $('#btn-manual-arrival, #btn-manual-leave').prop('disabled', true);
        $('#stu-empty').removeClass('hidden');
    }

    // Helper: Debounce
    function debounce(fn, delay){
        let t;
        return function(){
            const args = arguments;
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        }
    }

    const $input = $('#search-stu');
    const $suggest = $('#search-suggestions');
    let suggestions = [];

    // Search Logic
    const fetchSuggestions = debounce(function(){
        const q = $input.val().trim();
        if(q.length < 1){ 
            $suggest.addClass('hidden').empty(); 
            return; 
        }
        $.getJSON(`${API_BEHAVIOR}?action=search_students&q=${encodeURIComponent(q)}&limit=8`, function(rows){
            suggestions = rows || [];
            if(!suggestions.length){
                $suggest.html('<div class="p-6 text-center text-slate-500 font-bold italic">ไม่พบข้อมูลนักเรียน</div>').removeClass('hidden');
                return;
            }
            let html = '';
            suggestions.forEach((s, idx) => {
                const img = s.Stu_picture ? `https://std.phichai.ac.th/photo/${s.Stu_picture}` : '../dist/img/default-avatar.svg';
                html += `
                    <div onclick="selectSuggestion(${idx})" 
                         class="group flex items-center gap-4 p-4 hover:bg-slate-50 dark:hover:bg-slate-900 border-b border-slate-50 dark:border-slate-800 last:border-0 cursor-pointer transition-all">
                        <div class="w-12 h-12 rounded-xl overflow-hidden shadow-md flex-shrink-0 bg-white">
                            <img src="${img}" class="w-full h-full object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-black text-slate-800 dark:text-white group-hover:text-purple-600 transition-colors">${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ม.${s.Stu_major}/${s.Stu_room} • ID: ${s.Stu_id}</div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                `;
            });
            $suggest.html(html).removeClass('hidden');
        }).fail(() => $suggest.addClass('hidden'));
    }, 300);

    window.selectSuggestion = function(idx){
        const s = suggestions[idx];
        if(!s) return;

        const img = s.Stu_picture ? `https://std.phichai.ac.th/photo/${s.Stu_picture}` : '../dist/img/default-avatar.svg';
        $('#stu-photo').attr('src', img);
        $('#stu-name').text(`${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`);
        $('#stu-class').text(`ม.${s.Stu_major}/${s.Stu_room}`);
        $('#stu-id').text(`ID: ${s.Stu_id}`);
        
        $('#btn-manual-arrival, #btn-manual-leave').prop('disabled', false).data('stu', s.Stu_id);
        
        $('#stu-preview').removeClass('hidden');
        $('#stu-empty').addClass('hidden');
        $suggest.addClass('hidden');
        $input.val(s.Stu_id);

        // fetch forgot count
        $('#forgot-count').text('...');
        $.getJSON(`${API_ATTENDANCE}?action=get_forgot_count&student_id=${s.Stu_id}`, function(res){
            $('#forgot-count').text(res.count || 0);
        });
    };

    $input.on('input', fetchSuggestions);
    $(document).on('click', e => { if(!$(e.target).closest('#search-stu, #search-suggestions').length) $suggest.addClass('hidden'); });

    // Action Logic
    async function doScan(stu_id, type) {
        const title = type === 'arrival' ? 'ยืนยันเช็คเข้า ✅' : 'ยืนยันเช็คออก 🔴';
        const confirmColor = type === 'arrival' ? '#10b981' : '#f43f5e';
        
        const result = await Swal.fire({
            title: title,
            html: `บันทึกกรณีลืมบัตรสำหรับ<br><b class="text-purple-600">${stu_id}</b>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: confirmColor,
            borderRadius: '1.5rem'
        });

        if (result.isConfirmed) {
            Swal.showLoading();
            try {
                const res = await $.post(API_ATTENDANCE + '?action=manual_scan', { student_id: stu_id, scan_type: type }, null, 'json');
                if (res.error) throw new Error(res.error);
                
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'บันทึกข้อมูลเรียบร้อยแล้ว',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                
                if (res.forgot_count !== undefined) $('#forgot-count').text(res.forgot_count);
                forgotTable.ajax.reload(null, false);
            } catch (e) {
                Swal.fire('ล้มเหลว', e.message, 'error');
            }
        }
    }

    $('#btn-manual-arrival').on('click', function() { doScan($(this).data('stu'), 'arrival'); });
    $('#btn-manual-leave').on('click', function() { doScan($(this).data('stu'), 'leave'); });

    // DataTable History
    forgotTable = $('#forgotTable').DataTable({
        ajax: {
            url: API_ATTENDANCE + '?action=get_forgot_history',
            dataSrc: 'data'
        },
        columns: [
            { 
                data: null,
                className: "px-6 py-5",
                render: function(data, type, row) {
                    return `
                        <div>
                            <div class="text-[13px] font-black text-slate-800 dark:text-white">${row.fullname}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">ID: ${row.student_id}</div>
                        </div>
                    `;
                }
            },
            { data: 'class', className: "px-6 py-5 text-center text-sm font-black text-purple-600" },
            { 
                data: 'forgot_date', 
                className: "px-6 py-5 text-sm font-black text-slate-500 italic",
                render: d => d.split('-').reverse().join('/')
            },
            { data: 'note', className: "px-6 py-5 text-xs font-bold text-slate-400" }
        ],
        order: [[2, 'desc']],
        pageLength: 10,
        dom: 'rtp',
        drawCallback: function(settings) {
            renderMobileCards(this.api().rows({page:'current'}).data().toArray());
        }
    });

    function renderMobileCards(data) {
        const $container = $('#mobileView').empty();
        data.forEach((row, index) => {
            $container.append(`
                <div class="history-card glass-effect p-6 rounded-[2rem] shadow-xl border border-white/50 animate-fadeIn" style="animation-delay: ${index * 0.05}s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-xs">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 dark:text-white">${row.fullname}</h4>
                                <p class="text-[10px] font-bold text-indigo-500 uppercase">ม.${row.class} • ID: ${row.student_id}</p>
                            </div>
                        </div>
                        <div class="text-[10px] font-black text-slate-300 italic uppercase">#${row.id || index+1}</div>
                    </div>
                    <div class="bg-slate-100/50 dark:bg-slate-900/50 p-4 rounded-2xl flex justify-between items-center">
                        <div>
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">วันที่ลืม</div>
                            <div class="text-xs font-black text-slate-600 dark:text-slate-300 italic">${row.forgot_date.split('-').reverse().join('/')}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">หมายเหตุ</div>
                            <div class="text-xs font-bold text-slate-500 line-clamp-1">${row.note || '-'}</div>
                        </div>
                    </div>
                </div>
            `);
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
