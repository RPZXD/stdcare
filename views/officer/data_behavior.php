<?php
/**
 * View: Behavior Data Management (Officer)
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

    .behavior-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .behavior-card:hover {
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
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
    }
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #1e293b !important;
    }

    /* Smooth input typing */
    .smooth-input {
        will-change: transform;
        -webkit-font-smoothing: antialiased;
    }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-rose-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-700 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-shield-heart text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            จัดการข้อมูล <span class="text-indigo-600 italic">พฤติกรรม</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1 italic">
                            บันทึกพฤติกรรมเชิงบวกและลบ ตรวจสอบประวัติ และจัดการคะแนนความประพฤติ (เทอม <?php echo "$term/$pee"; ?>)
                        </p>
                    </div>
                </div>
                
                <button onclick="openAddModal()" class="w-full md:w-auto px-8 py-5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-black shadow-xl shadow-emerald-500/30 hover:shadow-2xl hover:scale-105 transition-all flex items-center justify-center gap-3 active:scale-95 group">
                    <span class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:rotate-90 transition-transform">
                        <i class="fas fa-plus"></i>
                    </span>
                    เพิ่มข้อมูลพฤติกรรม
                </button>
            </div>
        </div>
    </div>

    <!-- Toolbar Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn" style="animation-delay: 0.1s">
        <div class="flex flex-col lg:flex-row items-center gap-6">
            <div class="flex-1 w-full relative group">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                <input type="text" id="behaviorSearch" placeholder="ค้นหาด้วยชื่อนักเรียน, รหัส, หรือรายการพฤติกรรม..." 
                    class="smooth-input w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 outline-none font-bold text-slate-700 dark:text-white">
            </div>
            
            <div class="flex items-center gap-3 w-full lg:w-auto no-print">
                <button id="btnSearch" class="flex-1 lg:flex-none h-[58px] px-8 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 active:scale-95">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
                <button id="btnClearSearch" class="flex-1 lg:flex-none h-[58px] px-6 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-black rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 active:scale-95">
                    <i class="fas fa-times"></i> ล้าง
                </button>
                <button id="btnRefresh" class="flex-1 lg:flex-none h-[58px] px-8 bg-indigo-500 hover:bg-indigo-600 text-white font-black rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 active:scale-95">
                    <i class="fas fa-sync-alt"></i> รีเฟรช
                </button>
            </div>
        </div>
    </div>

    <!-- Main Data Table -->
    <div class="relative min-h-[400px]">
        <!-- Desktop View -->
        <div id="desktopView" class="hidden lg:block overflow-hidden rounded-[2.5rem] glass-effect shadow-2xl border border-white/50 dark:border-slate-700/50 animate-fadeIn" style="animation-delay: 0.2s">
            <table id="behaviorTable" class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">วันที่บันทึก</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">นักเรียน</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">รายการพฤติกรรม</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">รายละเอียด</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">คะแนน</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest italic">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                    <!-- Dynamic Data via DataTables -->
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Grid of Cards) -->
        <div id="mobileView" class="lg:hidden grid grid-cols-1 md:grid-cols-2 gap-6 animate-fadeIn" style="animation-delay: 0.2s">
            <!-- Dynamic Data via AJAX -->
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div id="behaviorModal" class="fixed inset-0 z-[60] hidden overflow-y-auto no-print">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden animate-fadeIn scale-95 opacity-0 transition-all duration-300" id="modalContent">
            <!-- Modal Header -->
            <div class="p-8 border-b dark:border-slate-700 bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-edit text-xl" id="modalIcon"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black italic tracking-tight" id="modalTitle">จัดการพฤติกรรม</h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-100 mt-0.5 opacity-80" id="modalSubtitle">บันทึกข้อมูลพฤติกรรมนักเรียน</p>
                        </div>
                    </div>
                    <button onclick="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 hover:bg-white/30 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="behaviorForm">
                <input type="hidden" name="id" id="behavior_id">
                <input type="hidden" name="stu_id" id="modalStu_id" required>
                
                <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto">
                    <!-- Student Search Section (Only for Create mode) -->
                    <div id="searchSection" class="relative group">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-focus-within:text-indigo-500 transition-colors mb-2 block">ค้นหานักเรียน</label>
                        <div class="relative flex gap-2">
                            <div class="flex-1 relative">
                                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                <input type="text" id="studentSearchInput" autocomplete="off"
                                    class="smooth-input w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none font-black text-slate-700 dark:text-white"
                                    placeholder="พิมพ์ชื่อ นามสกุล หรือเลขประจำตัว...">
                            </div>
                            <button type="button" id="btnSearchStudent" class="px-6 py-4 bg-indigo-500 hover:bg-indigo-600 text-white font-black rounded-2xl shadow-lg transition-all active:scale-95">
                                <i class="fas fa-search"></i>
                            </button>
                            
                            <!-- Search Loading -->
                            <div id="searchLoading" class="absolute right-20 top-1/2 -translate-y-1/2 hidden">
                                <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>

                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="absolute z-20 w-full mt-2 bg-white dark:bg-slate-800 rounded-[1.5rem] shadow-2xl border border-slate-100 dark:border-slate-700 max-h-64 overflow-y-auto hidden">
                            <!-- Results will be populated here -->
                        </div>
                    </div>

                    <!-- Selected Student Preview -->
                    <div id="selectedStudent" class="hidden animate-fadeIn">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic mb-2 block">นักเรียนที่เลือก</label>
                        <div class="bg-indigo-50 dark:bg-indigo-900/40 p-5 rounded-3xl border border-indigo-100 dark:border-indigo-800/50 flex items-center justify-between gap-5">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-2xl overflow-hidden border-4 border-white dark:border-slate-700 shadow-xl flex-shrink-0 bg-white">
                                    <img id="selectedStudentImg" src="../dist/img/default-avatar.svg" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div id="selectedStudentName" class="text-base font-black text-slate-800 dark:text-white line-clamp-1">ชื่อ-นามสกุล</div>
                                    <div id="selectedStudentInfo" class="text-xs font-bold text-indigo-500 uppercase tracking-widest">ชั้น/ห้อง • ID: XXXXX</div>
                                </div>
                            </div>
                            <button type="button" id="btnClearSelection" onclick="clearSelectedStudent()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-focus-within:text-indigo-500 transition-colors">วันที่</label>
                            <input type="date" name="behavior_date" id="modalDate" required value="<?php echo date('Y-m-d'); ?>"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-black text-slate-700 dark:text-white text-indigo-600">
                        </div>
                        <div class="space-y-2 group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-focus-within:text-indigo-500 transition-colors">รายการพฤติกรรม</label>
                            <select name="behavior_type" id="modalType" required 
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-black text-slate-700 dark:text-white cursor-pointer">
                                <optgroup label="⚠️ ความผิด (หักคะแนน)">
                                    <option value="มาโรงเรียนสาย">มาโรงเรียนสาย</option>
                                    <option value="แต่งกาย/ทรงผมผิดระเบียบ">แต่งกาย/ทรงผมผิดระเบียบ</option>
                                    <option value="หนีเรียนหรือออกนอกสถานศึกษา">หนีเรียนหรือออกนอกสถานศึกษา</option>
                                    <option value="จอดรถในที่ห้ามจอด">จอดรถในที่ห้ามจอด</option>
                                    <option value="แสดงพฤติกรรมก้าวร้าว">แสดงพฤติกรรมก้าวร้าว</option>
                                    <option value="ก่อเหตุทะเลาะวิวาท">ก่อเหตุทะเลาะวิวาท</option>
                                    <option value="สูบบุหรี่">สูบบุหรี่</option>
                                    <option value="เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์">เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์</option>
                                    <option value="เสพยาเสพติด">เสพยาเสพติด</option>
                                    <option value="เล่นการพนัน">เล่นการพนัน</option>
                                    <option value="ลักทรัพย์ กรรโชกทรัพย์">ลักทรัพย์ กรรโชกทรัพย์</option>
                                    <option value="แสดงพฤติกรรมทางชู้สาว">แสดงพฤติกรรมทางชู้สาว</option>
                                    <option value="พกพาอาวุธหรือวัตถุระเบิด">พกพาอาวุธหรือวัตถุระเบิด</option>
                                    <option value="มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ">มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ</option>
                                    <option value="ความผิด">อื่นๆ (ความผิด)</option>
                                </optgroup>
                                <optgroup label="🌟 ความดี (บวกคะแนน)">
                                    <option value="จิตอาสาช่วยเหลือครู">จิตอาสาช่วยเหลือครู</option>
                                    <option value="ช่วยเหลือเพื่อน">ช่วยเหลือเพื่อน</option>
                                    <option value="เก็บของได้ส่งคืน">เก็บของได้ส่งคืน</option>
                                    <option value="บำเพ็ญประโยชน์">บำเพ็ญประโยชน์</option>
                                    <option value="ความดี">อื่นๆ (ความดี)</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2 group">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-focus-within:text-indigo-500 transition-colors">รายละเอียด</label>
                        <input type="text" name="behavior_name" id="modalName" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)..."
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-black text-slate-700 dark:text-white">
                    </div>

                    <div class="p-5 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">คะแนน</label>
                            <span id="scoreValueLabel" class="text-2xl font-black text-rose-500 italic">0</span>
                        </div>
                        <input type="range" name="behavior_score" id="modalScore" min="0" max="100" step="5" value="5"
                            class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                        <div class="flex justify-between mt-2 px-1">
                            <span class="text-[9px] font-bold text-slate-400">0</span>
                            <span class="text-[9px] font-bold text-slate-400">50</span>
                            <span class="text-[9px] font-bold text-slate-400">100</span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-8 border-t dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-8 py-4 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-2xl font-black transition-all active:scale-95">
                        <i class="fas fa-times mr-2"></i> ยกเลิก
                    </button>
                    <button type="submit" id="btnSubmitForm" class="px-12 py-4 bg-gradient-to-r from-indigo-600 to-purple-700 text-white rounded-2xl font-black shadow-xl shadow-indigo-500/30 hover:shadow-2xl hover:scale-[1.02] transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const API_URL = '../controllers/BehaviorController.php';
let behaviorTable;
let mode = 'create'; // create or update

// Pre-define global functions for HTML event handlers
window.openAddModal = function() {
    mode = 'create';
    $('#behaviorForm')[0].reset();
    $('#behavior_id').val('');
    $('#modalStu_id').val('');
    $('#modalTitle').text('เพิ่มข้อมูลพฤติกรรม');
    $('#modalSubtitle').text('บันทึกพฤติกรรมใหม่สำหรับนักเรียน');
    
    // UI Reset
    $('#searchSection').removeClass('hidden');
    $('#selectedStudent').addClass('hidden');
    $('#searchResults').addClass('hidden');
    $('#studentSearchInput').val('');
    $('#btnClearSelection').addClass('hidden'); // Ensure clear button is hidden initially for create
    $('#modalScore').val(5); // Default score
    $('#scoreValueLabel').text(5); // Default score label
    
    $('#behaviorModal').removeClass('hidden');
    setTimeout(() => $('#modalContent').removeClass('scale-95 opacity-0'), 10);
};

window.closeModal = function() {
    $('#modalContent').addClass('scale-95 opacity-0');
    setTimeout(() => $('#behaviorModal').addClass('hidden'), 250);
};

window.editBehavior = async function(id) {
    mode = 'update';
    try {
        const data = await $.get(`${API_URL}?action=get&id=${id}`);
        if (data && data.id) {
            $('#behavior_id').val(data.id);
            $('#modalStu_id').val(data.stu_id);
            $('#modalDate').val(data.behavior_date);
            $('#modalType').val(data.behavior_type);
            $('#modalName').val(data.behavior_name);
            $('#modalScore').val(data.behavior_score);
            $('#scoreValueLabel').text(data.behavior_score);
            
            // Set correct color base on type
            if (data.behavior_type === 'ความดี') {
                $('#scoreValueLabel').removeClass('text-rose-500').addClass('text-emerald-500');
                $('#modalScore').removeClass('accent-indigo-600').addClass('accent-emerald-500');
            } else {
                $('#scoreValueLabel').removeClass('text-emerald-500').addClass('text-rose-500');
                $('#modalScore').removeClass('accent-emerald-500').addClass('accent-indigo-600');
            }
            
            $('#modalTitle').text('แก้ไขข้อมูลพฤติกรรม');
            $('#modalSubtitle').text(`รหัสบันทึก: ${data.id}`);
            
            // Hide search, show only preview (no clear button on edit)
            $('#searchSection').addClass('hidden');
            $('#btnClearSelection').addClass('hidden');
            
            selectStudent(data.stu_id, `${data.Stu_pre}${data.Stu_name} ${data.Stu_sur}`, `ม.${data.Stu_major}/${data.Stu_room}`, data.Stu_picture);
            
            // Re-apply score color after setting type
            updateScoreColorBasedOnType(data.behavior_type);
            
            $('#behaviorModal').removeClass('hidden');
            setTimeout(() => $('#modalContent').removeClass('scale-95 opacity-0'), 10);
        }
    } catch (error) {
        Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้', 'error');
    }
};

window.deleteBehavior = async function(id) {
    const result = await Swal.fire({
        title: 'ยืนยันการลบ? 🗑️',
        text: "ข้อมูลพฤติกรรมนี้จะถูกลบออกจากระบบและไม่สามารถกู้คืนได้",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก',
        borderRadius: '1.5rem'
    });

    if (result.isConfirmed) {
        try {
            const res = await $.ajax({
                url: API_URL + "?action=delete",
                method: 'POST',
                data: { id: id },
                dataType: 'json'
            });
            if (res.success) {
                if (behaviorTable) behaviorTable.ajax.reload();
                Swal.fire('สำเร็จ ✅', 'ลบข้อมูลเรียบร้อยแล้ว', 'success');
            }
        } catch (e) {
            Swal.fire('Error', 'ไม่สามารถลบข้อมูลได้', 'error');
        }
    }
};

window.searchStudentsLive = async function(query) {
    const $results = $('#searchResults');
    const $loading = $('#searchLoading');
    
    if (query.length < 2) {
        $results.addClass('hidden').empty();
        return;
    }

    $loading.removeClass('hidden');
    try {
        const data = await $.get(`${API_URL}?action=search_students&q=${encodeURIComponent(query)}&limit=8`);
        $loading.addClass('hidden');
        
        if (data.length === 0) {
            $results.html('<div class="p-6 text-center text-slate-500 font-bold italic">ไม่พบข้อมูลนักเรียน</div>').removeClass('hidden');
            return;
        }

        let html = '';
        data.forEach(s => {
            const img = s.Stu_picture ? `https://std.phichai.ac.th/photo/${s.Stu_picture}` : '../dist/img/default-avatar.svg';
            html += `
                <div onclick="selectStudent('${s.Stu_id}', '${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}', 'ม.${s.Stu_major}/${s.Stu_room}', '${s.Stu_picture}')" 
                     class="group flex items-center gap-4 p-4 hover:bg-slate-50 dark:hover:bg-slate-900 border-b border-slate-50 dark:border-slate-800 last:border-0 cursor-pointer transition-all">
                    <div class="w-12 h-12 rounded-xl overflow-hidden shadow-md flex-shrink-0 bg-white">
                        <img src="${img}" class="w-full h-full object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-black text-slate-800 dark:text-white group-hover:text-indigo-600 transition-colors">${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ม.${s.Stu_major}/${s.Stu_room} • ID: ${s.Stu_id}</div>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300 group-hover:translate-x-1 transition-transform"></i>
                </div>
            `;
        });
        $results.html(html).removeClass('hidden');
    } catch (e) {
        $loading.addClass('hidden');
        $results.html('<div class="p-6 text-center text-rose-500 font-bold italic">เกิดข้อผิดพลาด</div>').removeClass('hidden');
    }
};

window.selectStudent = function(id, name, info, picture) {
    $('#modalStu_id').val(id);
    $('#selectedStudentName').text(name);
    $('#selectedStudentInfo').text(info);
    
    const imgPath = picture ? `https://std.phichai.ac.th/photo/${picture}` : '../dist/img/default-avatar.svg';
    $('#selectedStudentImg').attr('src', imgPath);
    
    $('#selectedStudent').removeClass('hidden');
    $('#searchResults').addClass('hidden');
    $('#studentSearchInput').val('');
    $('#btnClearSelection').removeClass('hidden');
};

window.clearSelectedStudent = function() {
    $('#modalStu_id').val('');
    $('#selectedStudent').addClass('hidden');
    $('#studentSearchInput').focus();
    $('#btnClearSelection').addClass('hidden');
};

    // Score color helper
    window.updateScoreColorBasedOnType = function(type) {
        const goodDeeds = ["ความดี", "จิตอาสาช่วยเหลือครู", "ช่วยเหลือเพื่อน", "เก็บของได้ส่งคืน", "บำเพ็ญประโยชน์"];
        const isGood = goodDeeds.includes(type);
        
        if (isGood) {
            $('#scoreValueLabel').removeClass('text-rose-500').addClass('text-emerald-500');
            $('#modalScore').removeClass('accent-indigo-600').addClass('accent-emerald-500');
        } else {
            $('#scoreValueLabel').removeClass('text-emerald-500').addClass('text-rose-500');
            $('#modalScore').removeClass('accent-emerald-500').addClass('accent-indigo-600');
        }
    };

    $(document).ready(function() {

    // (เพิ่ม) debounce สำหรับการพิมพ์
    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Search student when button clicked
    $('#btnSearchStudent').on('click', function() {
        searchStudentsLive($('#studentSearchInput').val());
    });

    // Search student when Enter pressed
    $('#studentSearchInput').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchStudentsLive(this.value);
        }
    });

    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchSection').length) {
            $('#searchResults').addClass('hidden');
        }
    });

    // Score slider label update
    $('#modalScore').on('input', function() {
        $('#scoreValueLabel').text(this.value);
    });

    // (เพิ่ม) รายการพฤติกรรม
    const behaviorDataMap = {
        "หนีเรียนหรือออกนอกสถานศึกษา": 10,
        "เล่นการพนัน": 20,
        "มาโรงเรียนสาย": 5,
        "แต่งกาย/ทรงผมผิดระเบียบ": 5,
        "พกพาอาวุธหรือวัตถุระเบิด": 20,
        "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์": 20,
        "สูบบุหรี่": 30,
        "เสพยาเสพติด": 30,
        "ลักทรัพย์ กรรโชกทรัพย์": 30,
        "ก่อเหตุทะเลาะวิวาท": 20,
        "แสดงพฤติกรรมทางชู้สาว": 20,
        "จอดรถในที่ห้ามจอด": 10,
        "แสดงพฤติกรรมก้าวร้าว": 10,
        "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ": 5,
        "จิตอาสาช่วยเหลือครู": 10, // Good behavior examples
        "ช่วยเหลือเพื่อน": 5,
        "เก็บของได้ส่งคืน": 20
    };

    const behaviorSuggestionsByType = {
        "ความดี": ["จิตอาสา", "ช่วยเหลือครู", "เก็บของได้ส่งคืน", "ช่วยเหลือเพื่อน", "อื่นๆ (ความดี)"],
        "ความผิด": [
            "หนีเรียนหรือออกนอกสถานศึกษา", "เล่นการพนัน", "มาโรงเรียนสาย", 
            "แต่งกาย/ทรงผมผิดระเบียบ", "พกพาอาวุธหรือวัตถุระเบิด", 
            "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์", "สูบบุหรี่", "เสพยาเสพติด", 
            "ลักทรัพย์ กรรโชกทรัพย์", "ก่อเหตุทะเลาะวิวาท", "แสดงพฤติกรรมทางชู้สาว", 
            "จอดรถในที่ห้ามจอด", "แสดงพฤติกรรมก้าวร้าว", "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ"
        ]
    };

    function updateBehaviorNameSuggestions(type) {
        const $datalist = $('#behaviorSuggestions').empty();
        const options = behaviorSuggestionsByType[type] || [];
        options.forEach(opt => {
            $datalist.append(`<option value="${opt}">`);
        });
    }

    $('#modalType').on('change', function() {
        const val = this.value;
        const score = behaviorDataMap[val] || 5;
        
        $('#modalScore').val(score);
        $('#scoreValueLabel').text(score);

        updateScoreColorBasedOnType(val);
    });

    // Default suggestions
    $('#modalType').trigger('change');
   
    // Initialize DataTable
    behaviorTable = $('#behaviorTable').DataTable({
        ajax: {
            url: API_URL + "?action=list",
            dataSrc: ""
        },
        columns: [
            { 
                data: "behavior_date", 
                className: "px-8 py-5 text-sm font-black text-slate-400 italic" 
            },
            { 
                data: null,
                className: "px-6 py-5",
                render: function(data, type, row) {
                    return `
                        <div>
                            <div class="text-[13px] font-black text-slate-800 dark:text-white">${row.Stu_name} ${row.Stu_sur}</div>
                            <div class="text-[10px] font-bold text-indigo-500 uppercase tracking-tight">ม.${row.Stu_major}/${row.Stu_room} • ID: ${row.stu_id}</div>
                        </div>
                    `;
                }
            },
            { 
                data: "behavior_type",
                className: "px-6 py-5 text-center",
                render: function(data, type, row) {
                    const goodDeeds = ["ความดี", "จิตอาสาช่วยเหลือครู", "ช่วยเหลือเพื่อน", "เก็บของได้ส่งคืน", "บำเพ็ญประโยชน์"];
                    const isGood = goodDeeds.includes(data);
                    const color = isGood ? 'emerald' : 'rose';
                    const emoji = isGood ? '🌟' : '⚠️';
                    return `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-${color}-50 dark:bg-${color}-900/30 text-${color}-600 dark:text-${color}-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-${color}-500/10">
                            ${emoji} ${data}
                        </span>
                    `;
                }
            },
            { 
                data: "behavior_name", 
                className: "px-6 py-5 text-sm font-black text-slate-600 dark:text-slate-400" 
            },
            { 
                data: "behavior_score",
                className: "px-6 py-5 text-center",
                render: function(data, type, row) {
                    const goodDeeds = ["ความดี", "จิตอาสาช่วยเหลือครู", "ช่วยเหลือเพื่อน", "เก็บของได้ส่งคืน", "บำเพ็ญประโยชน์"];
                    const isGood = goodDeeds.includes(row.behavior_type);
                    return `<span class="text-xl font-black ${isGood ? 'text-emerald-500' : 'text-rose-500'}">${isGood ? '+' : '-'}${data}</span>`;
                }
            },
            { 
                data: "id",
                className: "px-8 py-5 text-right",
                render: function(data) {
                    return `
                        <div class="flex justify-end gap-2">
                            <button onclick="editBehavior('${data}')" class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center hover:bg-amber-600 hover:text-white transition-all active:scale-95 shadow-sm">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <button onclick="deleteBehavior('${data}')" class="w-10 h-10 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all active:scale-95 shadow-sm">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        drawCallback: function(settings) {
            $('.dataTables_filter').hide();
            renderMobileCards(this.api().data().toArray());
        },
        language: {
            zeroRecords: "ไม่พบข้อมูล",
            info: "แสดง _START_ - _END_ จาก _TOTAL_ รายการ",
            paginate: {
                previous: '<i class="fas fa-chevron-left text-xs"></i>',
                next: '<i class="fas fa-chevron-right text-xs"></i>'
            }
        }
    });

    // Button-based Search
    $('#btnSearch').on('click', function() {
        behaviorTable.search($('#behaviorSearch').val()).draw();
    });

    // Search when Enter pressed
    $('#behaviorSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            behaviorTable.search(this.value).draw();
        }
    });

    // Clear search
    $('#btnClearSearch').on('click', function() {
        $('#behaviorSearch').val('');
        behaviorTable.search('').draw();
    });

    // Mobile Rendering
    function renderMobileCards(data) {
        const $container = $('#mobileView').empty();
        data.forEach((row, index) => {
            const isGood = row.behavior_type === 'ความดี';
            const color = isGood ? 'emerald' : 'rose';
            const emoji = isGood ? '🌟' : '⚠️';
            
            const card = `
                <div class="behavior-card glass-effect p-6 rounded-[2.5rem] shadow-xl border border-white/50 dark:border-slate-800 animate-fadeIn" style="animation-delay: ${index * 0.05}s">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-black text-slate-800 dark:text-white truncate">${row.Stu_name} ${row.Stu_sur}</h3>
                                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">ม.${row.Stu_major}/${row.Stu_room} • ID: ${row.stu_id}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                             <button onclick="editBehavior('${row.id}')" class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                             <button onclick="deleteBehavior('${row.id}')" class="w-10 h-10 bg-rose-50 dark:bg-rose-900/30 text-rose-600 rounded-xl flex items-center justify-center transition-all">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/50 p-5 rounded-3xl border border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between items-center mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-${color}-50 dark:bg-${color}-900/30 text-${color}-600 dark:text-${color}-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-${color}-500/10">
                                ${emoji} ${row.behavior_type}
                            </span>
                            <span class="text-xl font-black ${isGood ? 'text-emerald-500' : 'text-rose-500'}">${isGood ? '+' : '-'}${row.behavior_score}</span>
                        </div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">${row.behavior_date}</div>
                        <div class="text-sm font-black text-slate-700 dark:text-slate-300">${row.behavior_name}</div>
                    </div>
                </div>
            `;
            $container.append(card);
        });
    }

    // Form Submit
    $('#behaviorForm').on('submit', async function(e) {
        e.preventDefault();
        const $btn = $('#btnSubmitForm');
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> กำลังบันทึก...');
        
        const action = mode === 'create' ? 'create' : 'update';
        const formData = new FormData(this);
        // Map common fields to action-specific names if needed by controller
        if (mode === 'create') {
            formData.set('addStu_id', formData.get('stu_id'));
            formData.set('addBehavior_date', formData.get('behavior_date'));
            formData.set('addBehavior_type', formData.get('behavior_type'));
            formData.set('addBehavior_name', formData.get('behavior_name'));
            formData.set('addBehavior_score', formData.get('behavior_score'));
        } else {
            formData.set('editId', formData.get('id'));
            formData.set('editStu_id', formData.get('stu_id'));
            formData.set('editBehavior_date', formData.get('behavior_date'));
            formData.set('editBehavior_type', formData.get('behavior_type'));
            formData.set('editBehavior_name', formData.get('behavior_name'));
            formData.set('editBehavior_score', formData.get('behavior_score'));
        }

        try {
            const res = await $.ajax({
                url: API_URL + `?action=${action}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            if (res.success) {
                closeModal();
                behaviorTable.ajax.reload();
                Swal.fire('สำเร็จ 🎉', `บันทึกข้อมูลพฤติกรรมเรียบร้อยแล้ว`, 'success');
            } else {
                Swal.fire('ล้มเหลว 😞', res.message || 'ไม่สามารถบันทึกได้', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        } finally {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });

    $('#btnRefresh').on('click', function() {
        $(this).find('i').addClass('fa-spin');
        behaviorTable.ajax.reload();
        setTimeout(() => $(this).find('i').removeClass('fa-spin'), 1000);
    });

});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
