<?php
/**
 * View: Admin Behavior Data
 * Modern UI with Tailwind CSS, Glassmorphism & Full CRUD
 * (Updated to match officer version)
 */
ob_start();
?>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
</style>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-user-clock"></i>
                </span>
                จัดการ <span class="text-rose-600 italic">ข้อมูลพฤติกรรม</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Behavior Data Management | เทอม <?= $term ?>/<?= $pee ?></p>
        </div>
        
        <button onclick="openAddModal()" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> เพิ่มข้อมูลพฤติกรรม
        </button>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-list text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ทั้งหมด</p>
                    <h3 id="totalRecords" class="text-2xl font-black text-slate-800 dark:text-white">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">มาสาย</p>
                    <h3 id="lateCount" class="text-2xl font-black text-amber-600">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-running text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">หนีเรียน</p>
                    <h3 id="escapeCount" class="text-2xl font-black text-red-600">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tshirt text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">แต่งกาย</p>
                    <h3 id="dressCount" class="text-2xl font-black text-indigo-600">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="glass-effect rounded-[2.5rem] p-6 md:p-8 shadow-xl border-t border-white/50">
        <div class="overflow-x-auto">
            <table id="behaviorTable" class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="bg-rose-50/50 dark:bg-slate-800/50">
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center w-24">วันที่</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">รหัส</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-สกุล</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-20">ชั้น/ห้อง</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ประเภท</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-16">คะแนน</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center w-24">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-slate-700 dark:text-slate-300"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Behavior Modal (Add/Edit) -->
<div id="behaviorModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden animate-fadeIn scale-95 opacity-0 transition-all duration-300" id="modalContent">
            <!-- Modal Header -->
            <div class="p-8 border-b dark:border-slate-700 bg-gradient-to-r from-rose-600 to-pink-700 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-edit text-xl" id="modalIcon"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black italic tracking-tight" id="modalTitle">จัดการพฤติกรรม</h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-rose-100 mt-0.5 opacity-80" id="modalSubtitle">บันทึกข้อมูลพฤติกรรมนักเรียน</p>
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
                
                <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto">
                    <!-- Student Search Section -->
                    <div id="searchSection" class="relative group">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic group-focus-within:text-rose-500 transition-colors mb-2 block">ค้นหานักเรียน</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-rose-500 transition-colors"></i>
                            <input type="text" id="studentSearchInput" autocomplete="off"
                                class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-black text-slate-700 dark:text-white"
                                placeholder="พิมพ์ชื่อ นามสกุล หรือเลขประจำตัว...">
                            
                            <!-- Search Loading -->
                            <div id="searchLoading" class="absolute right-5 top-1/2 -translate-y-1/2 hidden">
                                <div class="w-5 h-5 border-2 border-rose-500 border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>

                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="absolute z-20 w-full mt-2 bg-white dark:bg-slate-800 rounded-[1.5rem] shadow-2xl border border-slate-100 dark:border-slate-700 max-h-64 overflow-y-auto hidden"></div>
                    </div>

                    <!-- Selected Student Preview -->
                    <div id="selectedStudent" class="hidden">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic mb-2 block">นักเรียนที่เลือก</label>
                        <div class="bg-rose-50 dark:bg-rose-900/40 p-5 rounded-3xl border border-rose-100 dark:border-rose-800/50 flex items-center justify-between gap-5">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-2xl overflow-hidden border-4 border-white dark:border-slate-700 shadow-xl flex-shrink-0 bg-white">
                                    <img id="selectedStudentImg" src="../dist/img/default-avatar.svg" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div id="selectedStudentName" class="text-base font-black text-slate-800 dark:text-white line-clamp-1">ชื่อ-นามสกุล</div>
                                    <div id="selectedStudentInfo" class="text-xs font-bold text-rose-500 uppercase tracking-widest">ชั้น/ห้อง • ID: XXXXX</div>
                                </div>
                            </div>
                            <button type="button" id="btnClearSelection" onclick="clearSelectedStudent()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-100 dark:bg-rose-900/30 text-rose-500 hover:bg-rose-500 hover:text-white transition-all hidden">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">วันที่</label>
                            <input type="date" name="behavior_date" id="modalDate" required value="<?= date('Y-m-d') ?>"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-black text-slate-700 dark:text-white text-rose-600">
                        </div>
                        <div class="space-y-2 group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">รายการพฤติกรรม</label>
                            <select name="behavior_type" id="modalType" required 
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-black text-slate-700 dark:text-white cursor-pointer">
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
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">รายละเอียด</label>
                        <input type="text" name="behavior_name" id="modalName" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)..."
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-rose-100 outline-none transition-all font-black text-slate-700 dark:text-white">
                    </div>

                    <!-- Score Slider -->
                    <div class="p-5 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">คะแนน</label>
                            <span id="scoreValueLabel" class="text-2xl font-black text-rose-500 italic">5</span>
                        </div>
                        <input type="range" name="behavior_score" id="modalScore" min="0" max="100" step="5" value="5"
                            class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-rose-600">
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
                    <button type="submit" class="px-12 py-4 bg-gradient-to-r from-rose-600 to-pink-700 text-white rounded-2xl font-black shadow-xl shadow-rose-500/30 hover:shadow-2xl hover:scale-[1.02] transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-action { padding: 6px 8px; border: none; border-radius: 8px; cursor: pointer; font-size: 12px; transition: transform 0.15s; margin: 0 2px; }
.btn-edit { background: #f59e0b; color: white; }
.btn-delete { background: #ef4444; color: white; }
.btn-action:hover { transform: scale(1.15); }

@media (max-width: 768px) {
    #behaviorTable { font-size: 12px; }
    #behaviorTable th, #behaviorTable td { padding: 8px 4px; }
}
</style>

<script>
const API_URL = '../controllers/BehaviorController.php';
let behaviorTable;
let mode = 'create';

// Debounce function
function debounce(func, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}

// Open Add Modal
window.openAddModal = function() {
    mode = 'create';
    $('#behaviorForm')[0].reset();
    $('#behavior_id').val('');
    $('#modalStu_id').val('');
    $('#modalTitle').text('เพิ่มข้อมูลพฤติกรรม');
    $('#modalSubtitle').text('บันทึกพฤติกรรมใหม่สำหรับนักเรียน');
    
    $('#searchSection').removeClass('hidden');
    $('#selectedStudent').addClass('hidden');
    $('#searchResults').addClass('hidden');
    $('#studentSearchInput').val('');
    $('#btnClearSelection').addClass('hidden');
    $('#modalScore').val(5);
    $('#scoreValueLabel').text(5).removeClass('text-emerald-500').addClass('text-rose-500');
    
    $('#behaviorModal').removeClass('hidden');
    setTimeout(() => $('#modalContent').removeClass('scale-95 opacity-0'), 10);
};

// Close Modal
window.closeModal = function() {
    $('#modalContent').addClass('scale-95 opacity-0');
    setTimeout(() => $('#behaviorModal').addClass('hidden'), 250);
};

// Edit Behavior
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
            
            $('#modalTitle').text('แก้ไขข้อมูลพฤติกรรม');
            $('#modalSubtitle').text(`รหัสบันทึก: ${data.id}`);
            
            $('#searchSection').addClass('hidden');
            $('#btnClearSelection').addClass('hidden');
            
            selectStudent(data.stu_id, `${data.Stu_pre || ''}${data.Stu_name} ${data.Stu_sur}`, `ม.${data.Stu_major}/${data.Stu_room}`, data.Stu_picture);
            updateScoreColor(data.behavior_type);
            
            $('#behaviorModal').removeClass('hidden');
            setTimeout(() => $('#modalContent').removeClass('scale-95 opacity-0'), 10);
        }
    } catch (error) {
        Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้', 'error');
    }
};

// Delete Behavior
window.deleteBehavior = async function(id) {
    const result = await Swal.fire({
        title: 'ยืนยันการลบ? 🗑️',
        text: "ข้อมูลนี้จะถูกลบออกและไม่สามารถกู้คืนได้",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    });

    if (result.isConfirmed) {
        try {
            const res = await $.post(API_URL + "?action=delete", { id: id });
            if (res.success) {
                behaviorTable.ajax.reload();
                Swal.fire('สำเร็จ ✅', 'ลบข้อมูลเรียบร้อย', 'success');
            }
        } catch (e) {
            Swal.fire('Error', 'ไม่สามารถลบข้อมูลได้', 'error');
        }
    }
};

// Search Students Live
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
                     class="group flex items-center gap-4 p-4 hover:bg-rose-50 dark:hover:bg-slate-900 border-b border-slate-50 dark:border-slate-800 last:border-0 cursor-pointer transition-all">
                    <div class="w-12 h-12 rounded-xl overflow-hidden shadow-md flex-shrink-0 bg-white">
                        <img src="${img}" class="w-full h-full object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-black text-slate-800 dark:text-white group-hover:text-rose-600 transition-colors">${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}</div>
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

// Select Student
window.selectStudent = function(id, name, info, picture) {
    $('#modalStu_id').val(id);
    $('#selectedStudentName').text(name);
    $('#selectedStudentInfo').text(`${info} • ID: ${id}`);
    
    const imgPath = picture ? `https://std.phichai.ac.th/photo/${picture}` : '../dist/img/default-avatar.svg';
    $('#selectedStudentImg').attr('src', imgPath);
    
    $('#selectedStudent').removeClass('hidden');
    $('#searchResults').addClass('hidden');
    $('#studentSearchInput').val('');
    if (mode === 'create') $('#btnClearSelection').removeClass('hidden');
};

// Clear Selected Student
window.clearSelectedStudent = function() {
    $('#modalStu_id').val('');
    $('#selectedStudent').addClass('hidden');
    $('#studentSearchInput').focus();
    $('#btnClearSelection').addClass('hidden');
};

// Update score color based on behavior type
window.updateScoreColor = function(type) {
    const goodDeeds = ["ความดี", "จิตอาสาช่วยเหลือครู", "ช่วยเหลือเพื่อน", "เก็บของได้ส่งคืน", "บำเพ็ญประโยชน์"];
    if (goodDeeds.includes(type)) {
        $('#scoreValueLabel').removeClass('text-rose-500').addClass('text-emerald-500');
        $('#modalScore').removeClass('accent-rose-600').addClass('accent-emerald-500');
    } else {
        $('#scoreValueLabel').removeClass('text-emerald-500').addClass('text-rose-500');
        $('#modalScore').removeClass('accent-emerald-500').addClass('accent-rose-600');
    }
};

$(document).ready(function() {
    // Initialize DataTable
    behaviorTable = $('#behaviorTable').DataTable({
        processing: true,
        serverSide: false,
        deferRender: true,
        ajax: {
            url: API_URL + '?action=list',
            dataSrc: function(json) {
                setTimeout(() => updateStats(json), 0);
                return json;
            }
        },
        columns: [
            { data: 'behavior_date', className: 'text-center', width: '90px' },
            { data: 'stu_id', className: 'font-bold text-rose-600', width: '90px' },
            { data: 'Stu_name', render: (d, t, r) => `${r.Stu_pre || ''}${d || ''} ${r.Stu_sur || ''}` },
            { data: 'Stu_major', render: (d, t, r) => `ม.${d || '-'}/${r.Stu_room || '-'}`, className: 'text-center', width: '70px' },
            { data: 'behavior_type', render: d => `<span class="text-xs">${d || '-'}</span>` },
            { data: 'behavior_score', className: 'text-center font-bold text-rose-600', width: '60px' },
            {
                data: 'id',
                render: function(data) {
                    return `<button class="btn-action btn-edit" onclick="editBehavior(${data})">✏️</button>
                            <button class="btn-action btn-delete" onclick="deleteBehavior(${data})">🗑️</button>`;
                },
                orderable: false,
                className: 'text-center',
                width: '90px'
            }
        ],
        order: [[0, 'desc']],
        pageLength: 50,
        language: {
            processing: '<i class="fas fa-spinner fa-spin text-2xl text-rose-600"></i>',
            zeroRecords: 'ไม่พบข้อมูล',
            info: 'แสดง _START_-_END_ จาก _TOTAL_',
            search: 'ค้นหา:',
            paginate: { first: '«', previous: '‹', next: '›', last: '»' }
        }
    });

    function updateStats(data) {
        $('#totalRecords').text(data.length);
        $('#lateCount').text(data.filter(r => (r.behavior_type || '').includes('สาย')).length);
        $('#escapeCount').text(data.filter(r => (r.behavior_type || '').includes('หนีเรียน')).length);
        $('#dressCount').text(data.filter(r => (r.behavior_type || '').includes('แต่งกาย')).length);
    }

    // Student search with debounce
    $('#studentSearchInput').on('input', debounce(function() {
        searchStudentsLive(this.value);
    }, 400));

    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchSection').length) {
            $('#searchResults').addClass('hidden');
        }
    });

    // Score slider update
    $('#modalScore').on('input', function() {
        $('#scoreValueLabel').text(this.value);
    });

    // Type change - update score color
    $('#modalType').on('change', function() {
        updateScoreColor(this.value);
    });

    // Form Submit
    $('#behaviorForm').on('submit', async function(e) {
        e.preventDefault();
        
        if (!$('#modalStu_id').val()) {
            Swal.fire('แจ้งเตือน', 'กรุณาเลือกนักเรียน', 'warning');
            return;
        }
        
        const action = mode === 'create' ? 'create' : 'update';
        const formData = $(this).serialize();
        
        try {
            const res = await $.post(`${API_URL}?action=${action}`, formData);
            if (res.success) {
                closeModal();
                behaviorTable.ajax.reload();
                Swal.fire({ icon: 'success', title: 'สำเร็จ!', timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire('ผิดพลาด', res.message || 'ไม่สามารถบันทึกได้', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'เกิดข้อผิดพลาด', 'error');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
