<?php
/**
 * Search Data View - SMART SEARCH UPGRADE
 * Modern UI with Quick Actions, Recent Searches & Intelligent Results
 */
$pageTitle = $title ?? 'ค้นหาข้อมูลสมาร์ท';

ob_start();
?>

<!-- jQuery UI for Autocomplete -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- Custom Styles -->
<style>
    .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); }
    .floating-icon { animation: float 3s ease-in-out infinite; }
    @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-8px); } }
    .search-input:focus { box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.3); }
    .result-card { animation: slideUp 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .quick-action-btn { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .quick-action-btn:hover { transform: scale(1.1); }
    
    /* Recent Search Tags */
    .recent-tag { transition: all 0.2s ease; cursor: pointer; }
    .recent-tag:hover { transform: translateY(-2px); background-color: #e0e7ff; }

    /* Autocomplete Styles */
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border: none !important;
        z-index: 9999 !important;
        padding: 8px;
    }
    .ui-menu-item { border-radius: 8px; margin-bottom: 2px; }
    .ui-menu-item-wrapper { padding: 10px 15px !important; transition: all 0.2s; }
    .ui-state-active, .ui-state-active:hover {
        background: linear-gradient(90deg, #6366f1, #8b5cf6) !important;
        color: white !important;
        border: none !important;
    }

    /* Modal Enhancements */
    #viewModalBackdrop.show { opacity: 1; }
    #viewModalContent.show { opacity: 1; transform: scale(1) translateY(0); }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden no-print">
    <div class="glass-card rounded-2xl p-5 md:p-8 border border-white shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-full blur-3xl -z-10"></div>
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="relative">
                <div class="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-3xl flex items-center justify-center shadow-2xl floating-icon">
                    <i class="fas fa-search-plus text-4xl text-white"></i>
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white dark:bg-slate-800 rounded-2xl shadow-lg flex items-center justify-center">
                    <span class="text-xl">✨</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-2xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                    Smart Search <span class="text-indigo-600">Pro</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-bold text-lg mt-1">
                    ค้นหาข้อมูลนักเรียนและครูด้วยความเร็วสูง
                </p>
                <!-- Recent Searches Bar -->
                <div id="recentContainer" class="flex flex-wrap gap-2 mt-4 justify-center md:justify-start hidden">
                    <span class="text-sm font-bold text-slate-400 self-center">ค้นหาล่าสุด:</span>
                    <div id="recentList" class="flex flex-wrap gap-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="glass-card rounded-3xl p-6 md:p-8 border border-white shadow-xl mb-8 no-print">
    <form id="searchForm" class="space-y-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Type Toggle -->
            <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl lg:w-48">
                <button type="button" onclick="setType('student')" id="btn-student" class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all bg-white dark:bg-indigo-600 shadow-md text-indigo-600 dark:text-white">
                    🎓 นักเรียน
                </button>
                <button type="button" onclick="setType('teacher')" id="btn-teacher" class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all text-slate-500">
                    👨‍🏫 ครู
                </button>
                <input type="hidden" name="type" id="type" value="student">
            </div>
            
            <!-- Search Input Box -->
            <div class="flex-1 relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-keyboard text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                </div>
                <input type="search" name="search" id="search" 
                       class="search-input w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl text-lg font-bold text-slate-700 dark:text-white focus:border-indigo-500 focus:bg-white transition-all outline-none"
                       placeholder="ค้นหาด้วย ชื่อ, นามสกุล, เบอร์โทร, ชื่อเล่น..."
                       autocomplete="off">
                <div class="absolute inset-y-0 right-4 flex items-center">
                    <kbd class="hidden md:inline-flex px-2 py-1 bg-slate-200 text-slate-500 rounded-lg text-xs font-bold uppercase">Enter</kbd>
                </div>
            </div>
            
            <!-- Search Button -->
            <button type="submit" class="lg:w-40 py-4 bg-gradient-to-r from-indigo-600 to-indigo-800 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 hover:shadow-2xl hover:scale-105 transition-all">
                <i class="fas fa-bolt mr-2"></i> ค้นหา
            </button>
        </div>
        
        <!-- Smart Shortcuts -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button type="button" onclick="quickSearch('ม.3/5')" class="p-3 border-2 border-slate-100 hover:border-indigo-200 rounded-2xl text-left hover:bg-indigo-50 transition-all group">
                <div class="text-xs font-bold text-slate-400 group-hover:text-indigo-400">ระบุห้องเรียน</div>
                <div class="text-sm font-black text-slate-700">ม.3/5</div>
            </button>
            <button type="button" onclick="quickSearch('089')" class="p-3 border-2 border-slate-100 hover:border-blue-200 rounded-2xl text-left hover:bg-blue-50 transition-all group">
                <div class="text-xs font-bold text-slate-400 group-hover:text-blue-400">ค้นหาเบอร์โทร</div>
                <div class="text-sm font-black text-slate-700">089-XXX-XXXX</div>
            </button>
            <button type="button" onclick="quickSearch('วิทยาศาสตร์')" class="p-3 border-2 border-slate-100 hover:border-purple-200 rounded-2xl text-left hover:bg-purple-50 transition-all group">
                <div class="text-xs font-bold text-slate-400 group-hover:text-purple-400">ค้นกลุ่มสาระ</div>
                <div class="text-sm font-black text-slate-700">วิทยาศาสตร์...</div>
            </button>
            <button type="button" onclick="quickSearch('หัวหน้า')" class="p-3 border-2 border-slate-100 hover:border-rose-200 rounded-2xl text-left hover:bg-rose-50 transition-all group">
                <div class="text-xs font-bold text-slate-400 group-hover:text-rose-400">ค้นหาตำแหน่ง</div>
                <div class="text-sm font-black text-slate-700">หัวหน้า / เลขา</div>
            </button>
        </div>
    </form>
</div>

<!-- Results Display Area -->
<div id="resultContainer" class="space-y-6 pb-20">
    <div id="emptyState" class="text-center py-20 opacity-50">
        <div class="text-8xl mb-4">🚀</div>
        <h3 class="text-2xl font-black text-slate-400">พร้อมค้นหาแล้วครับครู!</h3>
        <p class="font-bold text-slate-300">ลองพิมพ์เลขที่นักเรียน หรือเบอร์ที่โทรหาครูสิ</p>
    </div>
</div>

<!-- Profile View Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center p-4 md:p-8">
    <div id="viewModalBackdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeViewModal()"></div>
    <div id="viewModalContent" class="modal-content relative w-full max-w-6xl h-[90vh] md:h-[85vh] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col z-10">
        <!-- Modern Header -->
        <div class="relative p-6 md:p-8 flex items-center justify-between z-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200 floating-icon">
                    <i class="fas fa-id-card-alt text-xl md:text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white leading-tight">ข้อมูลเชิงลึก</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Detailed Analytics</p>
                </div>
            </div>
            <button onclick="closeViewModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-500 hover:bg-rose-50 transition-all group">
                <i class="fas fa-times text-xl group-hover:rotate-90 transition-transform"></i>
            </button>
        </div>
        
        <!-- Scrolled Content -->
        <div class="flex-1 overflow-y-auto px-6 md:px-10 pb-10 custom-scrollbar" id="viewModalBody">
            <!-- Content will be injected here -->
        </div>
        
        <!-- Subtle Footer Accent -->
        <div class="h-2 w-full bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[9999] hidden">
    <div class="bg-white rounded-3xl p-10 text-center shadow-2xl">
        <div class="relative w-20 h-20 mx-auto mb-6">
            <div class="absolute inset-0 border-4 border-indigo-100 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <p class="text-xl font-black text-slate-800">กำลังดึงข้อมูลอัจฉริยะ...</p>
        <p class="text-slate-500 font-bold mt-2 animate-pulse">เทคโนโลยีค้นหารวดเร็วเป็นพิเศษ</p>
    </div>
</div>

<script>
// Recent Search Logic
let recentSearches = JSON.parse(localStorage.getItem('recent_searches') || '[]');

function saveRecent(item) {
    recentSearches = recentSearches.filter(i => i !== item);
    recentSearches.unshift(item);
    recentSearches = recentSearches.slice(0, 5);
    localStorage.setItem('recent_searches', JSON.stringify(recentSearches));
    renderRecent();
}

function renderRecent() {
    const list = $('#recentList');
    if (recentSearches.length === 0) {
        $('#recentContainer').addClass('hidden');
        return;
    }
    $('#recentContainer').removeClass('hidden');
    list.empty();
    recentSearches.forEach(term => {
        list.append(`<span onclick="quickSearch('${term}')" class="recent-tag px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg border border-slate-200">${term}</span>`);
    });
}

function quickSearch(term) {
    $('#search').val(term);
    $('#searchForm').submit();
}

function setType(t) {
    $('#type').val(t);
    const isStudent = t === 'student';
    $('#btn-student').toggleClass('bg-white shadow-md text-indigo-600 dark:bg-indigo-600 dark:text-white', isStudent)
                     .toggleClass('text-slate-500', !isStudent);
    $('#btn-teacher').toggleClass('bg-white shadow-md text-indigo-600 dark:bg-indigo-600 dark:text-white', !isStudent)
                     .toggleClass('text-slate-500', isStudent);
    $('#search').attr('placeholder', isStudent ? 'ค้นหาเด็กๆ... ชื่อ, เบอร์พ่อแม่, ชื่อเล่น, ม.3/5' : 'ค้นหาเพื่อนครู... ชื่อ, กลุ่มสาระ, เบอร์โทร');
}

$(document).ready(function() {
    renderRecent();
    const imgProfileTeacher = '<?= htmlspecialchars($imgProfileTeacher) ?>';
    const imgProfileStudent = '<?= htmlspecialchars($imgProfileStudent) ?>';
    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const type = $('#type').val();
        const search = $('#search').val().trim();
        
        if (!search) return;
        saveRecent(search);
        
        $('#loadingOverlay').removeClass('hidden');
        
        $.ajax({
            url: '../teacher/api/search_data.php',
            method: 'POST',
            data: { type, search },
            dataType: 'json',
            success: function(response) {
                const container = $('#resultContainer');
                container.empty();
                
                if (response && response.length > 0) {
                    container.append(`<div class="flex items-center gap-3 mb-6"><div class="h-8 w-2 bg-indigo-600 rounded-full"></div><h3 class="font-black text-slate-800 text-xl">พบขุมทรัพย์ข้อมูล ${response.length} รายการ</h3></div>`);
                    
                    if (type === 'teacher') {
                        response.forEach((item, idx) => container.append(renderTeacher(item, imgProfileTeacher, idx)));
                    } else {
                        const grid = $('<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>');
                        response.forEach((item, idx) => grid.append(renderStudent(item, imgProfileStudent, idx)));
                        container.append(grid);
                    }
                } else {
                    container.html('<div class="text-center py-20"><div class="text-8xl mb-6">🏜️</div><h3 class="text-2xl font-black text-slate-400">เงียบกริบ... ไม่พบข้อมูลเลยครับ</h3><p class="font-bold text-slate-300">ลองตรวจสอบตัวสะกด หรือใช้คำสั้นลงดูนะ</p></div>');
                }
            },
            error: () => Swal.fire('Error', 'ค้นหาไม่ได้ครับ', 'error'),
            complete: () => $('#loadingOverlay').addClass('hidden')
        });
    });

    function renderTeacher(item, path, idx) {
        return `
            <div class="result-card glass-card rounded-3xl overflow-hidden border border-slate-100 shadow-xl" style="animation-delay: ${idx * 0.1}s">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6 items-center">
                        <img src="${path}${item.Teach_photo}" class="w-32 h-32 rounded-3xl object-cover shadow-2xl border-4 border-white" onerror="this.src='../dist/img/default-avatar.svg'">
                        <div class="flex-1 text-center md:text-left space-y-2">
                            <h3 class="text-2xl font-black text-slate-800">${item.Teach_name}</h3>
                            <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">📚 ${item.Teach_major}</span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold">🏫 ม.${item.Teach_class}/${item.Teach_room}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <a href="tel:${item.Teach_phone}" class="quick-action-btn flex items-center justify-center gap-2 py-3 bg-green-500 text-white rounded-2xl font-black shadow-lg shadow-green-200">
                                    <i class="fas fa-phone-alt"></i> โทรหาครู
                                </a>
                                <button onclick="viewTeacherDetail('${item.Teach_id}')" class="quick-action-btn py-3 bg-slate-800 text-white rounded-2xl font-black shadow-xl">
                                    โปรไฟล์เต็ม
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
    }

    function renderStudent(item, path, idx) {
        const addr = encodeURIComponent(item.Stu_addr);
        let statusBadge = '';
        if (item.Stu_status !== undefined && item.Stu_status !== null) {
            const status = String(item.Stu_status);
            if (status === '1') {
                statusBadge = `<span class="px-3 py-1 bg-emerald-500 text-white rounded-full text-[10px] font-black shadow-sm">🟢 ปกติ</span>`;
            } else if (status === '2') {
                statusBadge = `<span class="px-3 py-1 bg-blue-500 text-white rounded-full text-[10px] font-black shadow-sm">🎓 จบการศึกษา</span>`;
            } else if (status === '3') {
                statusBadge = `<span class="px-3 py-1 bg-amber-500 text-white rounded-full text-[10px] font-black shadow-sm">🏫 ย้ายโรงเรียน</span>`;
            } else if (status === '4') {
                statusBadge = `<span class="px-3 py-1 bg-rose-500 text-white rounded-full text-[10px] font-black shadow-sm">❌ ออกกลางคัน</span>`;
            } else if (status === '9') {
                statusBadge = `<span class="px-3 py-1 bg-slate-500 text-white rounded-full text-[10px] font-black shadow-sm">💔 เสียชีวิต</span>`;
            } else if (status === '0') {
                statusBadge = `<span class="px-3 py-1 bg-rose-400 text-white rounded-full text-[10px] font-black shadow-sm">ย้าย/จำหน่าย</span>`;
            } else {
                statusBadge = `<span class="px-3 py-1 bg-slate-400 text-white rounded-full text-[10px] font-black shadow-sm">สถานะ: ${status}</span>`;
            }
        }
        return `
            <div class="result-card glass-card rounded-3xl overflow-hidden border border-slate-100 shadow-xl group hover:shadow-2xl transition-all" style="animation-delay: ${idx * 0.05}s">
                <div class="relative">
                    <img src="${path}${item.Stu_picture}" class="w-full h-56 object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                    <div class="absolute top-4 left-4 flex flex-wrap gap-2 max-w-[90%]">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur rounded-full text-[10px] font-black shadow-sm">เลขที่ ${item.Stu_no}</span>
                        <span class="px-3 py-1 bg-indigo-600 text-white rounded-full text-[10px] font-black shadow-sm">ม.${item.Stu_major}/${item.Stu_room}</span>
                        ${statusBadge}
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-800">${item.Stu_name} ${item.Stu_sur}</h3>
                            <p class="text-indigo-500 font-bold text-sm">ชื่อเล่น: ${item.Stu_nick || '-'}</p>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query=${item.latitude && item.longitude ? item.latitude + ',' + item.longitude : addr}" target="_blank" class="w-10 h-10 ${item.latitude && item.longitude ? 'bg-emerald-50 text-emerald-500 border-emerald-100' : 'bg-rose-50 text-rose-500 border-rose-100'} rounded-xl flex items-center justify-center quick-action-btn border shadow-sm" title="${item.latitude && item.longitude ? 'ใช้พิกัด GPS แม่นยำสูง' : 'ค้นหาด้วยที่อยู่'}">
                            <i class="fas ${item.latitude && item.longitude ? 'fa-crosshairs' : 'fa-map-marker-alt'}"></i>
                        </a>
                    </div>
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-xs font-bold text-slate-400">
                            <i class="fas fa-id-card w-6"></i> รหัส: <span class="ml-auto text-slate-700">${item.Stu_id}</span>
                        </div>
                        <div class="flex items-center text-xs font-bold text-slate-400">
                            <i class="fas fa-user-friends w-6"></i> ผู้ปกครอง: <span class="ml-auto text-indigo-600 underline">${item.Par_name || '-'}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="tel:${item.Par_phone}" class="quick-action-btn flex items-center justify-center gap-2 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs shadow-lg shadow-indigo-100">
                            <i class="fas fa-phone"></i> โทรหาพ่อแม่
                        </a>
                        <button onclick="viewStudentDetail('${item.Stu_id}')" class="quick-action-btn py-3 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs">
                            ดูประวัติ 📝
                        </button>
                    </div>
                </div>
            </div>`;
    }

    // New Autocomplete for Smart Search
    if (typeof $.fn.autocomplete === 'function') {
        $('#search').autocomplete({
            source: (req, resp) => {
                $.ajax({
                    url: '../teacher/api/search_autocomplete.php',
                    data: { term: req.term, type: $('#type').val() },
                    dataType: 'json',
                    success: d => resp(d)
                });
            },
            minLength: 2,
            select: (e, ui) => {
                $('#search').val(ui.item.value);
                $('#searchForm').submit();
                return false;
            }
        });
    }
    window.viewStudentDetail = function(stuId) {
        openModal();
        $('#viewModalBody').html(`
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="relative w-24 h-24 mb-8">
                    <div class="absolute inset-0 border-8 border-indigo-50 dark:border-slate-800 rounded-full"></div>
                    <div class="absolute inset-0 border-8 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-fingerprint text-3xl text-indigo-200 animate-pulse"></i>
                    </div>
                </div>
                <h4 class="text-2xl font-black text-slate-800 dark:text-white mb-2 tracking-tight">กำลังยืนยันตัวตน...</h4>
                <p class="text-slate-500 font-bold">เทคโนโลยีประมวลผลข้อมูลอัจฉริยะ</p>
            </div>
        `);
        
        $.get('../teacher/api/view_student.php', { stu_id: stuId }, function(html) {
            $('#viewModalBody').hide().html(html).fadeIn(500);
        });
    }

    window.viewTeacherDetail = function(teachId) {
        openModal();
        $('#viewModalBody').html(`
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="relative w-24 h-24 mb-8">
                    <div class="absolute inset-0 border-8 border-purple-50 dark:border-slate-800 rounded-full"></div>
                    <div class="absolute inset-0 border-8 border-purple-600 rounded-full border-t-transparent animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-user-shield text-3xl text-purple-200 animate-pulse"></i>
                    </div>
                </div>
                <h4 class="text-2xl font-black text-slate-800 dark:text-white mb-2 tracking-tight">กำลังเรียกข้อมูลโปรไฟล์...</h4>
                <p class="text-slate-500 font-bold">ระบบตรวจสอบความปลอดภัยขั้นสูง</p>
            </div>
        `);
        
        $.post('../teacher/api/search_data.php', { type: 'teacher', search: teachId }, function(data) {
            if(data && data.length > 0) {
                const t = data[0];
                $('#viewModalBody').hide().html(`
                    <div class="text-center animate-fadeIn">
                        <div class="relative inline-block mb-8">
                            <div class="absolute -inset-4 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-[3rem] blur-xl opacity-20 animate-pulse"></div>
                            <img src="${imgProfileTeacher}${t.Teach_photo}" class="relative w-48 h-48 rounded-[2.5rem] object-cover shadow-2xl border-8 border-white dark:border-slate-800">
                        </div>
                        <h2 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white leading-tight mb-2">${t.Teach_name}</h2>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full font-black text-xs uppercase tracking-widest mb-10">
                            ID: ${t.Teach_id}
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-3xl mx-auto">
                            <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm group hover:border-indigo-200 transition-colors">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <i class="fas fa-book-open text-indigo-500"></i> กลุ่มสาระ
                                </div>
                                <div class="text-xl font-black text-slate-700 dark:text-slate-200">${t.Teach_major}</div>
                            </div>
                            <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm group hover:border-purple-200 transition-colors">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <i class="fas fa-chalkboard-teacher text-purple-500"></i> ที่ปรึกษาประจำชั้น
                                </div>
                                <div class="text-xl font-black text-slate-700 dark:text-slate-200">ม.${t.Teach_class}/${t.Teach_room}</div>
                            </div>
                            <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm group hover:border-green-200 transition-colors">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <i class="fas fa-phone-alt text-green-500"></i> เบอร์โทรศัพท์
                                </div>
                                <a href="tel:${t.Teach_phone}" class="text-xl font-black text-green-600 hover:underline decoration-2 underline-offset-4">${t.Teach_phone || '-'}</a>
                            </div>
                             <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm group hover:border-rose-200 transition-colors">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <i class="fas fa-birthday-cake text-rose-500"></i> วันเกิด
                                </div>
                                <div class="text-xl font-black text-slate-700 dark:text-slate-200">${t.Teach_birth || '-'}</div>
                            </div>
                        </div>
                    </div>
                `).fadeIn(500);
            }
        }, 'json');
    }

    function openModal() {
        const modal = $('#viewModal');
        const backdrop = $('#viewModalBackdrop');
        const content = $('#viewModalContent');
        
        modal.removeClass('hidden').addClass('flex');
        setTimeout(() => {
            backdrop.addClass('show');
            content.addClass('show');
        }, 10);
        
        $('body').addClass('overflow-hidden');
    }

    window.closeViewModal = function() {
        const modal = $('#viewModal');
        const backdrop = $('#viewModalBackdrop');
        const content = $('#viewModalContent');
        
        backdrop.removeClass('show');
        content.removeClass('show');
        
        setTimeout(() => {
            modal.addClass('hidden').removeClass('flex');
            $('body').removeClass('overflow-hidden');
        }, 300);
    }

    // Close on ESC
    $(document).keyup(function(e) {
        if (e.key === "Escape") closeViewModal();
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
