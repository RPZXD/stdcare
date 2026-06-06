<?php
/**
 * Board Parent View
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
$pageTitle = $title ?? 'คณะกรรมการเครือข่ายผู้ปกครอง';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.85);
    }
    .stat-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-4px) scale(1.02);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .member-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .member-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.15);
    }
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .slide-in {
        animation: slideIn 0.4s ease-out forwards;
    }
    /* Hide DataTables on mobile */
    @media (max-width: 767px) {
        #record_table_wrapper { display: none !important; }
    }
    @media (min-width: 768px) {
        #mobileCards { display: none !important; }
    }
    /* Print Styles */
    @media print {
        @page { size: A4 portrait; margin: 10mm; }
        body { background: white !important; font-family: 'Mali', sans-serif !important; }
        .no-print, #sidebar, #navbar, footer { display: none !important; }
        #printHeader, #printTable, #printSignature { display: block !important; }
        .glass-card { display: none !important; }
    }
    @media screen {
        #printHeader, #printTable, #printSignature { display: none !important; }
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden no-print">
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-blue-500/20 to-cyan-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <span class="text-2xl md:text-3xl">👨‍👩‍👧‍👦</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">
                    คณะกรรมการเครือข่ายผู้ปกครอง
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm mt-1">
                    <i class="fas fa-users text-emerald-500 mr-1"></i>
                    ม.<?= htmlspecialchars($class) ?>/<?= htmlspecialchars($room) ?>
                    <span class="mx-1">•</span>
                    <i class="far fa-calendar-alt text-emerald-500 mr-1"></i>
                    ปีการศึกษา <?= htmlspecialchars($pee) ?>
                </p>
            </div>
            <div class="hidden md:block">
                <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-12 h-12 opacity-80">
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-3 gap-2 md:gap-4 mb-4 md:mb-6 no-print">
    <div class="stat-card glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-10 h-10 mx-auto bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mb-2 shadow">
            <span class="text-lg">👑</span>
        </div>
        <p class="text-xl md:text-2xl font-black text-amber-600" id="statChairman">-</p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500 uppercase">ประธาน</p>
    </div>
    <div class="stat-card glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-10 h-10 mx-auto bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mb-2 shadow">
            <span class="text-lg">👥</span>
        </div>
        <p class="text-xl md:text-2xl font-black text-blue-600" id="statCommittee">-</p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500 uppercase">กรรมการ</p>
    </div>
    <div class="stat-card glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-10 h-10 mx-auto bg-gradient-to-br from-violet-400 to-purple-500 rounded-lg flex items-center justify-center mb-2 shadow">
            <span class="text-lg">📝</span>
        </div>
        <p class="text-xl md:text-2xl font-black text-violet-600" id="statSecretary">-</p>
        <p class="text-[9px] md:text-xs font-bold text-slate-500 uppercase">เลขานุการ</p>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-2 mb-4 md:mb-6 no-print">
    <button type="button" onclick="openAddModal()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-plus-circle"></i>
        <span>➕ เพิ่มข้อมูล</span>
    </button>
    <button onclick="window.open('print_meeting_report.php', '_blank')" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-print"></i>
        <span>🖨️ พิมพ์เล่มรายงาน</span>
    </button>
</div>

<!-- Mobile Search -->
<div class="md:hidden mb-4 no-print">
    <div class="relative">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" id="mobileSearch" placeholder="🔍 ค้นหาผู้ปกครอง..." 
               class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
    </div>
</div>

<!-- Mobile Cards Container -->
<div id="mobileCards" class="space-y-3 no-print">
    <div id="mobileLoading" class="glass-card rounded-2xl p-8 text-center">
        <div class="animate-spin w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full mx-auto mb-4"></div>
        <p class="text-slate-500 font-semibold">กำลังโหลดข้อมูล...</p>
    </div>
</div>

<!-- Desktop Table Card -->
<div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl hidden md:block no-print">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center shadow">
            <i class="fas fa-table text-white"></i>
        </div>
        <h3 class="text-lg font-black text-slate-800 dark:text-white">📋 รายชื่อคณะกรรมการ</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table id="record_table" class="w-full" style="width:100%">
            <thead>
                <tr class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
                    <th class="px-3 py-3 text-center rounded-tl-xl w-16">ลำดับ</th>
                    <th class="px-3 py-3 text-left">ชื่อ-นามสกุล</th>
                    <th class="px-3 py-3 text-left">ที่อยู่</th>
                    <th class="px-3 py-3 text-center">โทรศัพท์</th>
                    <th class="px-3 py-3 text-center">ตำแหน่ง</th>
                    <th class="px-3 py-3 text-center">รูปถ่าย</th>
                    <th class="px-3 py-3 text-center rounded-tr-xl">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            </tbody>
        </table>
    </div>
</div>

<!-- Print Layout -->
<div id="printHeader" class="hidden print:block">
    <div class="text-center border-b-2 border-slate-300 pb-4 mb-4">
        <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16 mx-auto mb-2">
        <h1 class="text-xl font-bold">โรงเรียนพิชัย</h1>
        <p class="text-sm text-slate-600 font-bold">คณะกรรมการเครือข่ายผู้ปกครอง</p>
    </div>
    <div class="flex justify-between items-end mb-4 text-sm font-bold">
        <div>ชั้นมัธยมศึกษาปีที่ <?= $class ?>/<?= $room ?></div>
        <div>ปีการศึกษา <?= $pee ?></div>
    </div>
</div>

<div id="printTable" class="hidden print:block mb-8">
    <table class="w-full border-collapse" id="printTableContent">
        <thead>
            <tr class="bg-slate-100">
                <th class="border border-slate-300 px-2 py-2 text-center w-12">ลำดับ</th>
                <th class="border border-slate-300 px-2 py-2 text-left">ชื่อ-นามสกุล</th>
                <th class="border border-slate-300 px-2 py-2 text-left">ที่อยู่</th>
                <th class="border border-slate-300 px-2 py-2 text-center">โทรศัพท์</th>
                <th class="border border-slate-300 px-2 py-2 text-center">ตำแหน่ง</th>
            </tr>
        </thead>
        <tbody id="printTableBody"></tbody>
    </table>
</div>

<div id="printSignature" class="hidden print:block mt-8">
    <div class="grid grid-cols-2 gap-8 px-8">
        <?php foreach ($roomTeachers as $t): ?>
        <div class="text-center mb-2">
            <p class="mb-12">ลงชื่อ..........................................</p>
            <p class="font-bold">(<?= $t['Teach_name'] ?>)</p>
            <p class="text-sm text-slate-600">ครูที่ปรึกษา</p>
        </div>
        <?php endforeach; ?>
    </div>
    <p class="text-center text-[10px] text-slate-400 mt-8 italic">พิมพ์เมื่อ: <?= date('d/m/Y H:i') ?> น.</p>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-emerald-500 to-teal-600 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> เพิ่มข้อมูลคณะกรรมการเครือข่ายผู้ปกครอง
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50">
                <form id="addForm" method="post" enctype="multipart/form-data" class="space-y-4">
                    <!-- Step 1: Select Student -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">เป็นผู้ปกครองของ:</label>
                            <select id="addStudentId" name="stu_id" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required>
                                <option value="">-- กรุณาเลือก --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ตำแหน่ง:</label>
                            <select name="pos" id="addPos" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required>
                                <option value="">-- เลือกตำแหน่ง --</option>
                                <option value="1">👑 ประธาน</option>
                                <option value="2">👥 กรรมการ</option>
                                <option value="3">📝 เลขานุการ</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Step 2: Select Parent from DB or Manual Input -->
                    <div id="parentSelectSection" class="hidden">
                        <label class="block text-sm font-bold text-slate-700 mb-2">เลือกข้อมูลผู้ปกครองจากฐานข้อมูล:</label>
                        <div class="bg-white border border-slate-200 rounded-xl p-3 space-y-2" id="parentOptionsContainer">
                            <!-- Parent options will be loaded here -->
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <input type="checkbox" id="manualInputCheck" class="w-4 h-4 text-emerald-500 rounded">
                            <label for="manualInputCheck" class="text-sm text-slate-600 cursor-pointer">✏️ พิมพ์ข้อมูลเอง</label>
                        </div>
                    </div>
                    
                    <!-- Parent Info Fields -->
                    <div id="parentInfoSection">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ชื่อ-สกุลผู้ปกครอง:</label>
                            <input type="text" name="name" id="addName" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="ระบุชื่อ-นามสกุล" required>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">ที่อยู่:</label>
                            <textarea name="address" id="addAddress" rows="2" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="ระบุที่อยู่" required></textarea>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">เบอร์โทรศัพท์:</label>
                            <input type="tel" name="tel" id="addTel" maxlength="10" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="0812345678" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">รูปถ่าย:</label>
                            <input type="file" name="image1" accept="image/*" class="w-full text-sm file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200">
                        </div>
                    </div>
                    <input type="hidden" name="major" value="<?= $class ?>">
                    <input type="hidden" name="room" value="<?= $room ?>">
                    <input type="hidden" name="teacherid" value="<?= $teacher_id ?>">
                    <input type="hidden" name="term" value="<?= $term ?>">
                    <input type="hidden" name="pee" value="<?= $pee ?>">
                </form>
            </div>
            <div class="modal-footer bg-white border-0 py-4 gap-2">
                <button type="button" class="px-5 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                <button type="button" onclick="submitAddForm()" class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-xl">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-amber-500 to-orange-600 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลคณะกรรมการ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50">
                <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="edit_id" id="editId">
                    <input type="hidden" name="pee" value="<?= $pee ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">เป็นผู้ปกครองของ:</label>
                            <select id="editStudentId" name="stu_id" class="w-full px-4 py-3 border border-slate-300 rounded-xl bg-slate-100" disabled>
                                <option value="">-- กรุณาเลือก --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ตำแหน่ง:</label>
                            <select name="pos" id="editPos" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500" required>
                                <option value="">-- เลือกตำแหน่ง --</option>
                                <option value="1">👑 ประธาน</option>
                                <option value="2">👥 กรรมการ</option>
                                <option value="3">📝 เลขานุการ</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ชื่อ-สกุลผู้ปกครอง:</label>
                        <input type="text" name="name" id="editName" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ที่อยู่:</label>
                        <textarea name="address" id="editAddress" rows="2" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500" required></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">เบอร์โทรศัพท์:</label>
                            <input type="tel" name="tel" id="editTel" maxlength="10" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">รูปถ่าย:</label>
                            <input type="file" name="image1" accept="image/*" class="w-full text-sm file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                            <img id="editImagePreview" src="#" alt="Preview" class="mt-2 w-20 h-20 rounded-full object-cover hidden">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-0 py-4 gap-2">
                <button type="button" class="px-5 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                <button type="button" onclick="submitEditForm()" class="px-5 py-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-xl">
                    <i class="fas fa-save mr-2"></i>บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
(function() {
    const classId = <?= $class ?>;
    const roomId = <?= $room ?>;
    const pee = <?= $pee ?>;
    let allData = [];

    // Load students for dropdown
    $.ajax({
        url: '../teacher/api/fetch_student_classroom.php',
        method: 'GET',
        dataType: 'json',
        data: { class: classId, room: roomId },
        success: function(response) {
            if (response.success) {
                response.data.forEach(student => {
                    const option = `<option value="${student.Stu_id}">${student.Stu_pre}${student.Stu_name} ${student.Stu_sur}</option>`;
                    $('#addStudentId, #editStudentId').append(option);
                });
            }
        }
    });

    // Position helper
    function getPositionBadge(pos) {
        const positions = {
            '1': { text: 'ประธาน', icon: '👑', color: 'amber' },
            '2': { text: 'กรรมการ', icon: '👥', color: 'blue' },
            '3': { text: 'เลขานุการ', icon: '📝', color: 'violet' }
        };
        const p = positions[pos] || { text: 'ไม่ระบุ', icon: '❓', color: 'slate' };
        return `<span class="inline-flex items-center gap-1 px-2 py-1 bg-${p.color}-100 text-${p.color}-700 text-xs font-bold rounded-full">${p.icon} ${p.text}</span>`;
    }

    function getPositionText(pos) {
        const positions = { '1': 'ประธาน', '2': 'กรรมการ', '3': 'เลขานุการ' };
        return positions[pos] || 'ไม่ระบุ';
    }

    // Store parent data for quick access
    let currentParentData = [];

    // Load parent info when student is selected
    $('#addStudentId').on('change', function() {
        const stuId = $(this).val();
        if (!stuId) {
            $('#parentSelectSection').addClass('hidden');
            $('#parentOptionsContainer').empty();
            $('#addName, #addAddress, #addTel').val('');
            currentParentData = [];
            return;
        }

        // Fetch parent info
        $.ajax({
            url: '../teacher/api/get_parent_info.php',
            method: 'GET',
            dataType: 'json',
            data: { stu_id: stuId },
            success: function(response) {
                if (response.success && response.data.parents.length > 0) {
                    currentParentData = response.data.parents;
                    
                    // Build parent options
                    let html = '';
                    response.data.parents.forEach((parent, index) => {
                        html += `
                            <label class="flex items-center gap-3 p-3 rounded-lg border-2 border-slate-200 hover:border-emerald-400 cursor-pointer transition-all parent-option" data-index="${index}">
                                <input type="radio" name="parent_select" value="${index}" class="w-5 h-5 text-emerald-500">
                                <div class="flex-1">
                                    <span class="font-bold text-slate-800">${parent.label}</span>
                                    <p class="text-sm text-slate-600">${parent.name || 'ไม่ระบุชื่อ'}</p>
                                </div>
                            </label>
                        `;
                    });
                    
                    $('#parentOptionsContainer').html(html);
                    $('#parentSelectSection').removeClass('hidden');
                    
                    // Auto-select first parent and fill form
                    if (response.data.parents.length > 0) {
                        $('input[name="parent_select"]:first').prop('checked', true).trigger('change');
                    }
                } else {
                    // No parent data found, show empty state
                    $('#parentOptionsContainer').html(`
                        <div class="text-center py-4 text-slate-500">
                            <span class="text-2xl block mb-2">📭</span>
                            <p class="text-sm">ไม่พบข้อมูลผู้ปกครองในระบบ</p>
                            <p class="text-xs text-slate-400 mt-1">กรุณากรอกข้อมูลด้านล่าง</p>
                        </div>
                    `);
                    $('#parentSelectSection').removeClass('hidden');
                    
                    // Use student address if available
                    if (response.data && response.data.student_address) {
                        $('#addAddress').val(response.data.student_address);
                    }
                }
            }
        });
    });

    // Handle parent selection
    $(document).on('change', 'input[name="parent_select"]', function() {
        const index = parseInt($(this).val());
        const parent = currentParentData[index];
        if (parent) {
            $('#addName').val(parent.name || '');
            $('#addAddress').val(parent.address || '');
            $('#addTel').val(parent.phone || '');
            
            // Disable fields when using DB data
            $('#addName, #addAddress').prop('readonly', true).addClass('bg-slate-100');
        }
    });

    // Handle manual input checkbox
    $('#manualInputCheck').on('change', function() {
        if ($(this).is(':checked')) {
            // Enable manual input
            $('#addName, #addAddress').prop('readonly', false).removeClass('bg-slate-100').val('');
            $('#addTel').val('');
            $('input[name="parent_select"]').prop('checked', false);
        } else {
            // Re-enable first parent selection
            const firstChecked = $('input[name="parent_select"]:first');
            if (firstChecked.length) {
                firstChecked.prop('checked', true).trigger('change');
            }
        }
    });

    // Highlight selected parent option
    $(document).on('change', 'input[name="parent_select"]', function() {
        $('.parent-option').removeClass('border-emerald-500 bg-emerald-50');
        $(this).closest('.parent-option').addClass('border-emerald-500 bg-emerald-50');
        $('#manualInputCheck').prop('checked', false);
    });

    // Create Mobile Card
    function createMobileCard(item, index) {
        const photoUrl = item.parn_photo ? `https://std.phichai.ac.th/teacher/uploads/photopar/${item.parn_photo}` : '../dist/img/user-placeholder.png';
        
        return `
            <div class="member-card glass-card rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg slide-in" 
                 style="animation-delay: ${index * 0.05}s" 
                 data-name="${item.parn_name.toLowerCase()}">
                <div class="flex items-start gap-4">
                    <img src="${photoUrl}" alt="${item.parn_name}" class="w-16 h-16 rounded-full object-cover shadow-lg flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm">${item.parn_name}</h4>
                            ${getPositionBadge(item.parn_pos)}
                        </div>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">${item.parn_addr || '-'}</p>
                        <p class="text-xs text-slate-600 mt-1 flex items-center gap-1">
                            <i class="fas fa-phone text-emerald-500"></i> ${item.parn_tel || 'ไม่มีข้อมูล'}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                    <button onclick="openEditModal('${item.Stu_id}')" class="btn-action flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-bold text-xs rounded-lg shadow">
                        <i class="fas fa-edit"></i> แก้ไข
                    </button>
                    <button onclick="deleteRecord('${item.Stu_id}')" class="btn-action inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-rose-400 to-red-500 text-white font-bold text-xs rounded-lg shadow">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    // Mobile Search
    $('#mobileSearch').on('input', function() {
        const term = $(this).val().toLowerCase();
        $('.member-card').each(function() {
            $(this).toggle($(this).data('name').includes(term));
        });
    });

    // Update Print Table
    function updatePrintTable(data) {
        let html = '';
        data.forEach((item, index) => {
            html += `
                <tr>
                    <td class="border border-slate-300 px-2 py-1 text-center font-bold">${index + 1}</td>
                    <td class="border border-slate-300 px-2 py-1 font-bold">${item.parn_name}</td>
                    <td class="border border-slate-300 px-2 py-1 text-sm">${item.parn_addr || '-'}</td>
                    <td class="border border-slate-300 px-2 py-1 text-center">${item.parn_tel || '-'}</td>
                    <td class="border border-slate-300 px-2 py-1 text-center font-bold">${getPositionText(item.parn_pos)}</td>
                </tr>
            `;
        });
        $('#printTableBody').html(html);
    }

    // Load Table
    function loadTable() {
        $.ajax({
            url: '../teacher/api/fetch_boardparent_classroom.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classId, room: roomId, pee: pee },
            success: function(response) {
                if (!response.success) return;

                allData = response.data;

                // Update stats
                const chairman = allData.filter(i => i.parn_pos === '1').length;
                const committee = allData.filter(i => i.parn_pos === '2').length;
                const secretary = allData.filter(i => i.parn_pos === '3').length;
                
                $('#statChairman').text(chairman);
                $('#statCommittee').text(committee);
                $('#statSecretary').text(secretary);

                // Mobile Cards
                $('#mobileLoading').remove();
                let mobileHtml = '';
                if (allData.length === 0) {
                    mobileHtml = `
                        <div class="glass-card rounded-2xl p-8 text-center">
                            <span class="text-4xl mb-4 block">📭</span>
                            <p class="text-slate-500 font-semibold">ยังไม่มีข้อมูลคณะกรรมการ</p>
                            <button onclick="openAddModal()" class="btn-action mt-4 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl">
                                <i class="fas fa-plus mr-2"></i>เพิ่มข้อมูล
                            </button>
                        </div>
                    `;
                } else {
                    allData.forEach((item, index) => {
                        mobileHtml += createMobileCard(item, index);
                    });
                }
                $('#mobileCards').html(mobileHtml);

                // Print Table
                updatePrintTable(allData);

                // Desktop DataTable
                const table = $('#record_table').DataTable({
                    destroy: true,
                    paging: false,
                    searching: true,
                    ordering: false,
                    info: false,
                    language: { search: "🔍 ค้นหา:" }
                });

                table.clear();

                allData.forEach((item, index) => {
                    const photoUrl = item.parn_photo ? `https://std.phichai.ac.th/teacher/uploads/photopar/${item.parn_photo}` : '../dist/img/user-placeholder.png';
                    const actionBtns = `
                        <div class="flex gap-1 justify-center">
                            <button class="btn-action px-2 py-1 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold rounded-lg" onclick="openEditModal('${item.Stu_id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action px-2 py-1 bg-gradient-to-r from-rose-400 to-red-500 text-white text-xs font-bold rounded-lg" onclick="deleteRecord('${item.Stu_id}')"><i class="fas fa-trash"></i></button>
                        </div>`;

                    table.row.add([
                        `<span class="inline-flex items-center justify-center w-7 h-7 bg-slate-100 text-slate-700 font-bold rounded-lg text-sm">${index + 1}</span>`,
                        `<span class="font-semibold">${item.parn_name}</span>`,
                        `<span class="text-xs text-slate-600">${item.parn_addr || '-'}</span>`,
                        `<span class="text-sm">${item.parn_tel || '-'}</span>`,
                        getPositionBadge(item.parn_pos),
                        `<img src="${photoUrl}" class="w-12 h-12 rounded-full object-cover mx-auto shadow">`,
                        actionBtns
                    ]);
                });

                table.draw();
            }
        });
    }

    window.openAddModal = function() {
        $('#addForm')[0].reset();
        $('#parentSelectSection').addClass('hidden');
        $('#parentOptionsContainer').empty();
        $('#addName, #addAddress').prop('readonly', false).removeClass('bg-slate-100');
        $('#manualInputCheck').prop('checked', false);
        currentParentData = [];
        $('#addModal').modal('show');
    };

    window.submitAddForm = function() {
        const formData = new FormData($('#addForm')[0]);
        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: '../teacher/api/insert_boardparent.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ!', timer: 1500, showConfirmButton: false }).then(() => {
                        $('#addModal').modal('hide');
                        loadTable();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: response.message });
                }
            }
        });
    };

    window.openEditModal = function(studentId) {
        $.ajax({
            url: '../teacher/api/fetch_boardparent_data.php',
            method: 'GET',
            data: { id: studentId, pee: pee },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editId').val(response.data.Stu_id);
                    $('#editStudentId').val(response.data.Stu_id);
                    $('#editName').val(response.data.parn_name);
                    $('#editAddress').val(response.data.parn_addr);
                    $('#editTel').val(response.data.parn_tel);
                    $('#editPos').val(response.data.parn_pos);
                    
                    if (response.data.parn_photo) {
                        $('#editImagePreview').attr('src', `https://std.phichai.ac.th/teacher/uploads/photopar/${response.data.parn_photo}`).removeClass('hidden');
                    } else {
                        $('#editImagePreview').addClass('hidden');
                    }
                    
                    $('#editModal').modal('show');
                }
            }
        });
    };

    window.submitEditForm = function() {
        const formData = new FormData($('#editForm')[0]);
        
        $.ajax({
            url: '../teacher/api/update_boardparent_data.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'แก้ไขสำเร็จ!', timer: 1500, showConfirmButton: false }).then(() => {
                        $('#editModal').modal('hide');
                        loadTable();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: response.message });
                }
            }
        });
    };

    window.deleteRecord = function(studentId) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบข้อมูลนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../teacher/api/delete_boardparent_data.php',
                    method: 'POST',
                    data: { id: studentId },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', timer: 1500 }).then(() => loadTable());
                        }
                    }
                });
            }
        });
    };

    // Initial load
    loadTable();
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
