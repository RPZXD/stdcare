<?php
$pageTitle = $title ?? 'ข้อมูลนักเรียนยากจน';

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
    .student-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .student-card:hover {
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
    .search-input:focus {
        box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.2);
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
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        body {
            background: white !important;
            font-family: 'Mali', sans-serif !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .no-print, #sidebar, #navbar, #preloader, footer, .glass-card {
            display: none !important;
        }
        #printHeader, #printTable, #printSignature {
            display: block !important;
        }
        #printTableContent th, #printTableContent td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
        }
        .status-received { color: #16a34a; font-weight: bold; }
        .status-not-received { color: #d97706; font-weight: bold; }
    }
    @media screen {
        #printHeader, #printTable, #printSignature {
            display: none !important;
        }
    }
</style>

<!-- Page Header (Screen) -->
<div class="relative mb-6 overflow-hidden no-print">
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-500/20 to-rose-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-amber-500/20 to-orange-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <span class="text-2xl md:text-3xl">💰</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">
                    ข้อมูลนักเรียนยากจน
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm mt-1">
                    <i class="fas fa-users text-pink-500 mr-1"></i>
                    ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?>
                    <span class="mx-1">•</span>
                    <i class="far fa-calendar-alt text-pink-500 mr-1"></i>
                    ปีการศึกษา <?php echo htmlspecialchars($pee); ?>
                </p>
            </div>
            <div class="hidden md:block">
                <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-12 h-12 opacity-80">
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats (Screen) -->
<div class="grid grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6 no-print">
    <div class="stat-card glass-card rounded-xl p-2 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto bg-gradient-to-br from-pink-400 to-rose-500 rounded-lg flex items-center justify-center mb-1 md:mb-2 shadow">
            <span class="text-sm md:text-lg">👨‍🎓</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-slate-800 dark:text-white" id="totalPoor">-</p>
        <p class="text-[8px] md:text-xs font-bold text-slate-500 uppercase">ยากจน</p>
    </div>
    <div class="stat-card glass-card rounded-xl p-2 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto bg-gradient-to-br from-emerald-400 to-green-500 rounded-lg flex items-center justify-center mb-1 md:mb-2 shadow">
            <span class="text-sm md:text-lg">🎓</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-emerald-600" id="receivedScholarship">-</p>
        <p class="text-[8px] md:text-xs font-bold text-slate-500 uppercase">ได้รับทุน</p>
    </div>
    <div class="stat-card glass-card rounded-xl p-2 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mb-1 md:mb-2 shadow">
            <span class="text-sm md:text-lg">📋</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-amber-600" id="notReceived">-</p>
        <p class="text-[8px] md:text-xs font-bold text-slate-500 uppercase">รอรับทุน</p>
    </div>
    <div class="stat-card glass-card rounded-xl p-2 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto bg-gradient-to-br from-violet-400 to-purple-500 rounded-lg flex items-center justify-center mb-1 md:mb-2 shadow">
            <span class="text-sm md:text-lg">🏠</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-violet-600" id="visitedHome">-</p>
        <p class="text-[8px] md:text-xs font-bold text-slate-500 uppercase">เยี่ยมบ้าน</p>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-2 mb-4 md:mb-6 no-print">
    <button type="button" onclick="openAddModal()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-plus-circle"></i>
        <span>➕ เพิ่มข้อมูล</span>
    </button>
    <button onclick="window.print()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-print"></i>
        <span>🖨️ พิมพ์รายงาน</span>
    </button>
</div>

<!-- Search Box (Mobile) -->
<div class="md:hidden mb-4 no-print">
    <div class="relative">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" id="mobileSearch" placeholder="🔍 ค้นหานักเรียน..." 
               class="search-input w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium focus:outline-none focus:border-pink-500">
    </div>
</div>

<!-- Mobile Cards Container -->
<div id="mobileCards" class="space-y-3 no-print">
    <!-- Loading State -->
    <div id="mobileLoading" class="glass-card rounded-2xl p-8 text-center">
        <div class="animate-spin w-10 h-10 border-4 border-pink-500 border-t-transparent rounded-full mx-auto mb-4"></div>
        <p class="text-slate-500 font-semibold">กำลังโหลดข้อมูล...</p>
    </div>
</div>

<!-- Desktop Table Card -->
<div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl hidden md:block no-print">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl flex items-center justify-center shadow">
            <i class="fas fa-table text-white"></i>
        </div>
        <h3 class="text-lg font-black text-slate-800 dark:text-white">📋 รายชื่อนักเรียนยากจน</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table id="record_table" class="w-full display responsive nowrap" style="width:100%">
            <thead>
                <tr class="bg-gradient-to-r from-pink-500 to-rose-600 text-white">
                    <th class="px-3 py-3 text-center rounded-tl-xl">ลำดับ</th>
                    <th class="px-3 py-3 text-left">ชื่อ-นามสกุล</th>
                    <th class="px-3 py-3 text-left">เหตุผล</th>
                    <th class="px-3 py-3 text-center">ทุน</th>
                    <th class="px-3 py-3 text-center">เยี่ยมบ้าน</th>
                    <th class="px-3 py-3 text-center rounded-tr-xl">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== Print Layout (Hidden on Screen) ==================== -->

<!-- Print Header -->
<div id="printHeader" class="hidden print:block">
    <div class="text-center border-b-2 border-slate-300 pb-4 mb-4">
        <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-16 h-16 mx-auto mb-2">
        <h1 class="text-xl font-bold">โรงเรียนพิชัย</h1>
        <p class="text-sm text-slate-600 font-bold">รายชื่อนักเรียนยากจน</p>
    </div>
    <div class="flex justify-between items-end mb-4 text-sm font-bold">
        <div>ชั้นมัธยมศึกษาปีที่ <?= $class ?>/<?= $room ?></div>
        <div>ปีการศึกษา <?= $pee ?></div>
    </div>
    
    <div class="grid grid-cols-4 gap-4 mb-6 text-center text-sm">
        <div class="border p-2 rounded">นักเรียนยากจน: <span id="print_total" class="font-bold">-</span> คน</div>
        <div class="border p-2 rounded text-emerald-600">เคยได้รับทุน: <span id="print_received" class="font-bold">-</span> คน</div>
        <div class="border p-2 rounded text-amber-600">ยังไม่ได้รับทุน: <span id="print_not_received" class="font-bold">-</span> คน</div>
        <div class="border p-2 rounded text-violet-600">เยี่ยมบ้าน: <span id="print_visited" class="font-bold">-</span> คน</div>
    </div>
</div>

<!-- Print Table -->
<div id="printTable" class="hidden print:block mb-8">
    <table class="w-full border-collapse" id="printTableContent">
        <thead>
            <tr class="bg-slate-100">
                <th class="border border-slate-300 px-2 py-2 text-center w-12">ลำดับ</th>
                <th class="border border-slate-300 px-2 py-2 text-left">ชื่อ-นามสกุล</th>
                <th class="border border-slate-300 px-2 py-2 text-left">เหตุผล</th>
                <th class="border border-slate-300 px-2 py-2 text-center w-28">การได้รับทุน</th>
                <th class="border border-slate-300 px-2 py-2 text-left">รายละเอียดทุน</th>
            </tr>
        </thead>
        <tbody id="printTableBody">
            <!-- Populated by JS -->
        </tbody>
    </table>
</div>

<!-- Print Signature -->
<div id="printSignature" class="hidden print:block mt-8">
    <div class="grid grid-cols-2 gap-8 px-8">
        <div class="text-center mb-2">
            <p class="mb-12">ลงชื่อ..........................................</p>
            <p class="font-bold">(<?= $teacher_name ?>)</p>
            <p class="text-sm text-slate-600">ครูที่ปรึกษา</p>
        </div>
        <div class="text-center mb-2">
            <p class="mb-12">ลงชื่อ..........................................</p>
            <p class="font-bold">(........................................)</p>
            <p class="text-sm text-slate-600">หัวหน้าระดับชั้น</p>
        </div>
    </div>
    <p class="text-center text-[10px] text-slate-400 mt-8 italic">พิมพ์เมื่อ: <?= date('d/m/Y H:i') ?> น.</p>
</div>

<!-- ==================== Modals ==================== -->

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-pink-500 to-rose-600 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> เพิ่มข้อมูลนักเรียนยากจน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50 dark:bg-slate-800">
                <div class="bg-amber-50 border-l-4 border-amber-400 p-3 rounded-lg mb-4">
                    <p class="text-sm text-amber-800 font-semibold">📋 คำชี้แจง: กรุณาเลือกนักเรียนยากจนในชั้นเรียนจำนวน 10 ลำดับ เรียงจากยากจนมากที่สุดไปน้อยที่สุด</p>
                </div>
                <form id="addForm" method="post" enctype="multipart/form-data" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ลำดับความยากจน:</label>
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500" id="addNumber" name="number" required>
                                <option value="">-- กรุณาเลือก --</option>
                                <?php for($i=1; $i<=10; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ชื่อ-สกุล นักเรียน:</label>
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500" id="addStudent" name="student" required>
                                <option value="">-- กรุณาเลือก --</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">เหตุผลประกอบ:</label>
                        <textarea class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500" name="reason" id="addReason" rows="3" placeholder="ระบุเหตุผลที่แสดงว่านักเรียนมีความยากจน..." required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">เคยได้รับทุนการศึกษา:</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="received" value="1" class="w-5 h-5 text-pink-500 focus:ring-pink-500">
                                <span class="font-semibold">✅ เคย</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="received" value="2" class="w-5 h-5 text-pink-500 focus:ring-pink-500">
                                <span class="font-semibold">❌ ไม่เคย</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">รายละเอียดทุนการศึกษา:</label>
                        <textarea class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500" name="detail" id="addDetail" rows="2" placeholder="ระบุรายละเอียดทุนที่เคยได้รับ (ถ้ามี)..."></textarea>
                    </div>
                    <input type="hidden" name="teacherid" value="<?= $teacher_id ?>">
                </form>
            </div>
            <div class="modal-footer bg-white dark:bg-slate-900 border-0 py-4 gap-2">
                <button type="button" class="btn-action px-5 py-2 bg-slate-400 hover:bg-slate-500 text-white font-bold rounded-xl" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>ปิด
                </button>
                <button type="button" onclick="submitAddForm()" class="btn-action px-5 py-2 bg-gradient-to-r from-pink-500 to-rose-600 text-white font-bold rounded-xl">
                    <i class="fas fa-save mr-2"></i>บันทึกข้อมูล
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
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลนักเรียนยากจน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50 dark:bg-slate-800">
                <form id="editForm" method="post" class="space-y-4">
                    <input type="hidden" id="editStudentId" name="student_id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ลำดับความยากจน:</label>
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl" id="editNumber" name="number" required>
                                <option value="">-- กรุณาเลือก --</option>
                                <?php for($i=1; $i<=10; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ชื่อ-สกุล นักเรียน:</label>
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl" id="editStudent" name="student" required>
                                <option value="">-- กรุณาเลือก --</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">เหตุผลประกอบ:</label>
                        <textarea class="w-full px-4 py-3 border border-slate-300 rounded-xl" name="reason" id="editReason" rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">เคยได้รับทุนการศึกษา:</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" id="editReceived1" name="received" value="1" class="w-5 h-5">
                                <span class="font-semibold">✅ เคย</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" id="editReceived2" name="received" value="2" class="w-5 h-5">
                                <span class="font-semibold">❌ ไม่เคย</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">รายละเอียดทุนการศึกษา:</label>
                        <textarea class="w-full px-4 py-3 border border-slate-300 rounded-xl" name="detail" id="editDetail" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white dark:bg-slate-900 border-0 py-4 gap-2">
                <button type="button" class="btn-action px-5 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                <button type="button" onclick="submitEditForm()" class="btn-action px-5 py-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-xl">
                    <i class="fas fa-save mr-2"></i>บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Visit Modal -->
<div class="modal fade" id="visitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-cyan-500 to-blue-600 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="fas fa-home"></i> ข้อมูลการเยี่ยมบ้าน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50 dark:bg-slate-800">
                <div id="visitContent"></div>
            </div>
            <div class="modal-footer bg-white border-0 py-4">
                <button type="button" class="btn-action px-5 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
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
    let allPoorData = [];

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
                    $('#addStudent, #editStudent').append(option);
                });
            }
        }
    });

    // Create Mobile Card HTML
    function createMobileCard(item, index) {
        const hasScholarship = item.poor_even === '1';
        const statusColor = hasScholarship ? 'emerald' : 'amber';
        const statusIcon = hasScholarship ? '✅' : '❌';
        const statusText = hasScholarship ? 'เคยได้รับทุน' : 'ยังไม่ได้รับทุน';
        const fullName = `${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}`;
        
        return `
            <div class="student-card glass-card rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg slide-in" 
                 style="animation-delay: ${index * 0.05}s" 
                 data-name="${fullName.toLowerCase()}" 
                 data-id="${item.Stu_id}">
                
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl flex items-center justify-center text-white font-bold shadow flex-shrink-0">
                        ${item.poor_no}
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm truncate">${fullName}</h4>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">${item.poor_reason || 'ไม่ระบุเหตุผล'}</p>
                        
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-${statusColor}-100 dark:bg-${statusColor}-900/30 text-${statusColor}-700 dark:text-${statusColor}-300 text-xs font-bold rounded-full">
                                ${statusIcon} ${statusText}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                    <button onclick="openEditModal('${item.Stu_id}')"
                            class="btn-action flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-bold text-xs rounded-lg shadow">
                        <i class="fas fa-edit"></i> แก้ไข
                    </button>
                    <button onclick="openVisit('${item.Stu_id}')"
                            class="btn-action flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-gradient-to-r from-cyan-400 to-blue-500 text-white font-bold text-xs rounded-lg shadow">
                        <i class="fas fa-home"></i> เยี่ยมบ้าน
                    </button>
                    <button onclick="deleteRecord('${item.Stu_id}')"
                            class="btn-action inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-rose-400 to-red-500 text-white font-bold text-xs rounded-lg shadow">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    // Mobile Search
    $('#mobileSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.student-card').each(function() {
            const name = $(this).data('name');
            const id = $(this).data('id');
            $(this).toggle(name.includes(searchTerm) || id.toString().includes(searchTerm));
        });
    });

    // Update Print Table
    function updatePrintTable(data) {
        const tbody = $('#printTableBody');
        let html = '';
        
        data.forEach(item => {
            const fullName = `${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}`;
            const hasScholarship = item.poor_even === '1';
            
            html += `
                <tr>
                    <td class="border border-slate-300 px-2 py-1 text-center font-bold">${item.poor_no}</td>
                    <td class="border border-slate-300 px-2 py-1 text-left font-bold">${fullName}</td>
                    <td class="border border-slate-300 px-2 py-1 text-left text-sm">${item.poor_reason || '-'}</td>
                    <td class="border border-slate-300 px-2 py-1 text-center ${hasScholarship ? 'status-received' : 'status-not-received'}">
                        ${hasScholarship ? '✅ เคยได้รับ' : '❌ ยังไม่ได้รับ'}
                    </td>
                    <td class="border border-slate-300 px-2 py-1 text-left text-sm">${item.poor_schol || '-'}</td>
                </tr>
            `;
        });
        
        tbody.html(html);
    }

    // Load Table
    function loadTable() {
        $.ajax({
            url: '../teacher/api/fetch_poor_classroom.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classId, room: roomId },
            success: function(response) {
                if (!response.success) return;

                allPoorData = response.data;

                // Update stats
                const total = allPoorData.length;
                const received = allPoorData.filter(i => i.poor_even === '1').length;
                const notReceived = total - received;
                
                $('#totalPoor').text(total);
                $('#receivedScholarship').text(received);
                $('#notReceived').text(notReceived);
                $('#visitedHome').text('-');
                
                // Update print stats
                $('#print_total').text(total);
                $('#print_received').text(received);
                $('#print_not_received').text(notReceived);
                $('#print_visited').text('-');

                // Render Mobile Cards
                $('#mobileLoading').remove();
                let mobileHtml = '';
                if (allPoorData.length === 0) {
                    mobileHtml = `
                        <div class="glass-card rounded-2xl p-8 text-center">
                            <span class="text-4xl mb-4 block">📭</span>
                            <p class="text-slate-500 font-semibold">ยังไม่มีข้อมูลนักเรียนยากจน</p>
                            <button onclick="openAddModal()" class="btn-action mt-4 px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-600 text-white font-bold text-sm rounded-xl">
                                <i class="fas fa-plus mr-2"></i>เพิ่มข้อมูล
                            </button>
                        </div>
                    `;
                } else {
                    allPoorData.forEach((item, index) => {
                        mobileHtml += createMobileCard(item, index);
                    });
                }
                $('#mobileCards').html(mobileHtml);

                // Update Print Table
                updatePrintTable(allPoorData);

                // Desktop DataTable
                const table = $('#record_table').DataTable({
                    destroy: true,
                    pageLength: 25,
                    order: [[0, 'asc']],
                    responsive: true,
                    language: {
                        search: "🔍 ค้นหา:",
                        lengthMenu: "แสดง _MENU_ รายการ",
                        info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                        paginate: { first: "«", previous: "‹", next: "›", last: "»" }
                    }
                });

                table.clear();

                allPoorData.forEach((item) => {
                    const fullName = `${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}`;
                    const scholarshipBadge = item.poor_even === '1' 
                        ? '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">✅ ได้รับ</span>'
                        : '<span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">❌ รอ</span>';
                    
                    const visitBtn = `<button class="btn-action px-2 py-1 bg-gradient-to-r from-cyan-400 to-blue-500 text-white text-xs font-bold rounded-lg" onclick="openVisit('${item.Stu_id}')"><i class="fas fa-home"></i></button>`;

                    const actionBtns = `
                        <div class="flex gap-1 justify-center">
                            <button class="btn-action px-2 py-1 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold rounded-lg" onclick="openEditModal('${item.Stu_id}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action px-2 py-1 bg-gradient-to-r from-rose-400 to-red-500 text-white text-xs font-bold rounded-lg" onclick="deleteRecord('${item.Stu_id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>`;

                    table.row.add([
                        `<span class="inline-flex items-center justify-center w-7 h-7 bg-gradient-to-br from-pink-100 to-rose-100 text-pink-700 font-bold rounded-lg text-sm">${item.poor_no}</span>`,
                        `<span class="font-semibold text-sm">${fullName}</span>`,
                        `<span class="text-xs text-slate-600 line-clamp-1">${item.poor_reason || '-'}</span>`,
                        scholarshipBadge,
                        visitBtn,
                        actionBtns
                    ]);
                });

                table.draw();
            }
        });
    }

    window.openAddModal = function() {
        $('#addForm')[0].reset();
        new bootstrap.Modal(document.getElementById('addModal')).show();
    };

    window.submitAddForm = function() {
        const formData = new FormData($('#addForm')[0]);
        
        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: '../teacher/api/insert_poor.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ!', timer: 1500, showConfirmButton: false }).then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
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
            url: '../teacher/api/fetch_poor_data.php',
            method: 'GET',
            data: { id: studentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editStudentId').val(response.data.Stu_id);
                    $('#editNumber').val(response.data.poor_no);
                    $('#editStudent').val(response.data.Stu_id);
                    $('#editReason').val(response.data.poor_reason);
                    $('#editDetail').val(response.data.poor_schol);
                    $(`#editReceived${response.data.poor_even}`).prop('checked', true);
                    new bootstrap.Modal(document.getElementById('editModal')).show();
                }
            }
        });
    };

    window.submitEditForm = function() {
        const formData = new FormData($('#editForm')[0]);
        
        $.ajax({
            url: '../teacher/api/update_poor_data.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'แก้ไขสำเร็จ!', timer: 1500, showConfirmButton: false }).then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
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
                    url: '../teacher/api/delete_poor_data.php',
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

    window.openVisit = function(studentId) {
        $.ajax({
            url: '../teacher/api/get_visit_data.php',
            method: 'GET',
            data: { term: 1, pee: pee, stuId: studentId },
            dataType: 'html',
            success: function(response) {
                if (response.trim() === '') {
                    Swal.fire('ไม่พบข้อมูล', 'ไม่พบข้อมูลการเยี่ยมบ้าน', 'info');
                    return;
                }
                $('#visitContent').html(response);
                new bootstrap.Modal(document.getElementById('visitModal')).show();
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
