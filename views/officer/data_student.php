<?php
/**
 * View: Student Data Management (Officer)
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
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        @page { size: portrait; margin: 1.5cm; }
        body { background: white !important; color: black !important; }
        .glass-effect { border: none !important; box-shadow: none !important; background: white !important; }
        table { width: 100% !important; border-collapse: collapse !important; margin-top: 10px; }
        th, td { border: 1px solid #000 !important; padding: 6px !important; text-align: left !important; font-size: 11px !important; }
        th { background: #f3f4f6 !important; -webkit-print-color-adjust: exact; }
    }
    .print-only { display: none; }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-emerald-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-700 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-users-rectangle text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            รายชื่อนักเรียน <span class="text-emerald-600 italic">ทั้งหมด</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1 italic">
                            จัดการข้อมูลนักเรียน คัดกรอง ค้นหา และพิมพ์ใบรายชื่อนักเรียนรายห้อง
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-4 no-print">
                    <div class="px-6 py-3 bg-white/50 dark:bg-slate-800/50 rounded-2xl border border-white/40 dark:border-slate-700 shadow-sm backdrop-blur-md">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">เทอม/ปีการศึกษา</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400"><?php echo $term; ?>/<?php echo $pee; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Filter: Class -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                <div class="relative">
                    <select id="selectClass" class="w-full pl-5 pr-10 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <option value="">-- ระบุชั้น --</option>
                        <?php for($i=1; $i<=6; $i++): ?>
                            <option value="<?php echo $i; ?>">มัธยมศึกษาปีที่ <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Filter: Room -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ห้องเรียน</label>
                <div class="relative">
                    <select id="selectRoom" class="w-full pl-5 pr-10 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 outline-none transition-all appearance-none cursor-pointer font-bold text-slate-700 dark:text-white">
                        <option value="">-- ระบุห้อง --</option>
                        <?php for($i=1; $i<=12; $i++): ?>
                            <option value="<?php echo $i; ?>">ห้องเรียนที่ <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Search -->
            <div class="lg:col-span-2 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ค้นหาด่วน</label>
                <div class="relative group">
                    <input type="text" id="studentSearch" placeholder="ชื่อ, นามสกุล, รหัสนักเรียน..." class="w-full pl-12 pr-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-3">
                <button id="btnLoad" class="h-[54px] px-6 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white font-black rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button onclick="addStudent()" class="flex-1 h-[54px] bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> เพิ่มนักเรียน
                </button>
                <button id="printStudentList" class="w-[54px] h-[54px] bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-600 hover:text-white rounded-2xl transition-all flex items-center justify-center shadow-lg shadow-emerald-500/10" title="พิมพ์รายชื่อ">
                    <i class="fas fa-print"></i>
                </button>
                <button id="printStudentCards" class="w-[54px] h-[54px] bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400 hover:bg-violet-600 hover:text-white rounded-2xl transition-all flex items-center justify-center shadow-lg shadow-violet-500/10" title="พิมพ์บัตร (Card)">
                    <i class="fas fa-id-card"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Container -->
    <div id="dataContainer" class="animate-fadeIn" style="animation-delay: 0.2s">
        <!-- Loading State -->
        <div id="loadingState" class="hidden py-40 text-center">
            <div class="loading-spinner mx-auto mb-4"></div>
            <p class="text-slate-400 font-bold italic tracking-widest animate-pulse">กำลังโหลดข้อมูลนักเรียน...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="py-40 text-center glass-effect rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-user-slash text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-black text-slate-400 uppercase tracking-widest italic">ไม่พบข้อมูลนักเรียน</h3>
            <p class="text-slate-400 mt-2 font-medium italic">กรุณาระบุเกณฑ์การค้นหาเพื่อแสดงรายชื่อ</p>
        </div>

        <!-- Desktop View (Table) -->
        <div id="desktopView" class="hidden overflow-hidden rounded-[2.5rem] glass-effect shadow-2xl border border-white/50 dark:border-slate-700/50 no-print">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ลำดับ/รูป</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">รหัสนักเรียน</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ชื่อ - นามสกุล</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ชั้น/ห้อง</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ข้อมูลติดต่อ</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest italic">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
                    <!-- Dynamic Data -->
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Cards) -->
        <div id="mobileView" class="hidden lg:hidden grid grid-cols-1 gap-6 no-print">
            <!-- Dynamic Data -->
        </div>
    </div>
</div>

<!-- All Specific Modals -->
<div id="modalContainer">
    <!-- View Modal Wrapper is handled dynamically -->
    
    <!-- Add/Edit Modal Shell -->
    <div id="formModal" class="fixed inset-0 z-50 hidden no-print">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeFormModal()"></div>
        <div class="fixed inset-4 md:inset-x-20 lg:inset-x-1/4 md:inset-y-10 lg:inset-y-16 bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col z-10 transition-all duration-300 scale-95 opacity-0" id="formModalContent">
            <div class="flex items-center justify-between p-6 border-b dark:border-slate-700 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                <h3 class="text-xl font-black flex items-center gap-3">
                    <i class="fas fa-user-edit" id="formModalIcon"></i>
                    <span id="formModalTitle">จัดการข้อมูลนักเรียน</span>
                </h3>
                <button onclick="closeFormModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 hover:bg-white/30 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-8" id="formModalBody">
                <!-- Form Content -->
            </div>
            <div class="p-6 border-t dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row justify-end gap-3">
                <button onclick="closeFormModal()" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-2xl font-black transition-all">
                    ยกเลิก
                </button>
                <button id="btnSaveStudent" class="px-10 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl font-black shadow-lg shadow-blue-500/30 hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    let currentStudentId = null;

    // Initial State
    function showState(state) {
        $('#loadingState, #emptyState, #desktopView, #mobileView').addClass('hidden');
        if (state === 'loading') $('#loadingState').removeClass('hidden');
        else if (state === 'empty') $('#emptyState').removeClass('hidden');
        else if (state === 'data') {
            if (window.innerWidth >= 1024) $('#desktopView').removeClass('hidden');
            else $('#mobileView').removeClass('hidden');
        }
    }

    // Load Data Function
    async function loadData() {
        const classValue = $('#selectClass').val();
        const roomValue = $('#selectRoom').val();
        
        if (!classValue || !roomValue) {
            Swal.fire({
                icon: 'info',
                title: 'โปรดทราบ',
                text: 'กรุณาเลือกระดับชั้นและห้องเรียนที่ต้องการ',
                confirmButtonColor: '#10b981'
            });
            return;
        }

        showState('loading');

        try {
            const response = await $.ajax({
                url: '../controllers/StudentController.php',
                method: 'GET',
                dataType: 'json',
                data: {
                    action: 'list_for_officer',
                    class: classValue,
                    room: roomValue,
                    status: 1
                }
            });

            if (response.success && response.data.length > 0) {
                renderData(response.data);
                showState('data');
            } else {
                showState('empty');
            }
        } catch (error) {
            console.error('Error loading data:', error);
            Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้ในขณะนี้', 'error');
            showState('empty');
        }
    }

    // Render Data
    function renderData(data) {
        const $tableBody = $('#studentTableBody').empty();
        const $mobileContainer = $('#mobileView').empty();

        data.forEach((s, idx) => {
            const fullName = `${s.Stu_pre}${s.Stu_name} ${s.Stu_sur}`;
            const photoUrl = s.Stu_picture ? `https://std.phichai.ac.th/photo/${s.Stu_picture}` : '../dist/img/default-avatar.svg';
            const classRoom = `ม.${s.Stu_major}/${s.Stu_room}`;
            
            // Build Table Row (Desktop)
            const tr = `
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group student-row" data-search="${fullName} ${s.Stu_id} ${s.Stu_nick}">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-black text-slate-300 italic">${s.Stu_no}.</span>
                            <div class="w-12 h-12 rounded-2xl overflow-hidden shadow-lg border-2 border-white dark:border-slate-800 group-hover:scale-110 transition-transform bg-slate-100">
                                <img src="${photoUrl}" class="w-full h-full object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-6 font-black text-blue-600 dark:text-blue-400 tracking-tighter italic">
                        ${s.Stu_id}
                    </td>
                    <td class="px-8 py-6">
                        <div class="font-black text-slate-800 dark:text-white leading-tight">${fullName}</div>
                        ${s.Stu_nick ? `<div class="text-[10px] text-emerald-500 font-black italic mt-0.5 tracking-widest uppercase truncate max-w-[150px]">ชื่อเล่น: ${s.Stu_nick}</div>` : ''}
                    </td>
                    <td class="px-6 py-6">
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-black italic">${classRoom}</span>
                    </td>
                    <td class="px-6 py-6">
                        <div class="flex flex-col gap-1">
                            ${s.Stu_phone ? `<a href="tel:${s.Stu_phone}" class="text-xs text-slate-500 hover:text-blue-500 transition-colors"><i class="fas fa-phone mr-1 opacity-50"></i> ${s.Stu_phone}</a>` : ''}
                            ${s.Par_phone ? `<span class="text-[10px] text-slate-400"><i class="fas fa-people-roof mr-1 opacity-50"></i> ${s.Par_phone}</span>` : ''}
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                            <button class="btn-view w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-400 hover:border-blue-500 hover:text-blue-500 transition-all shadow-sm" data-id="${s.Stu_id}" title="ดูข้อมูล">
                                <i class="fas fa-eye text-[10px]"></i>
                            </button>
                            <button onclick="editStudent('${s.Stu_id}')" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-400 hover:border-amber-500 hover:text-amber-500 transition-all shadow-sm" title="แก้ไข">
                                <i class="fas fa-edit text-[10px]"></i>
                            </button>
                            <button onclick="changePhoto('${s.Stu_id}')" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-400 hover:border-pink-500 hover:text-pink-500 transition-all shadow-sm" title="เปลี่ยนรูป">
                                <i class="fas fa-camera text-[10px]"></i>
                            </button>
                            <div class="w-px h-4 bg-slate-200 dark:bg-slate-700 mx-1"></div>
                            <button onclick="resetPassword('${s.Stu_id}')" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-400 hover:border-indigo-500 hover:text-indigo-500 transition-all shadow-sm" title="รีเซ็ตรหัสผ่าน">
                                <i class="fas fa-key text-[10px]"></i>
                            </button>
                            <button onclick="deleteStudent('${s.Stu_id}')" class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-400 hover:border-red-500 hover:text-red-500 transition-all shadow-sm" title="ลบ">
                                <i class="fas fa-trash text-[10px]"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            $tableBody.append(tr);

            // Build Card (Mobile)
            const card = `
                <div class="glass-effect p-6 rounded-[2rem] border border-white/50 dark:border-slate-700/50 shadow-xl relative overflow-hidden group student-row" data-search="${fullName} ${s.Stu_id} ${s.Stu_nick}">
                    <div class="flex items-start gap-5">
                        <div class="relative">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden border-2 border-emerald-500/20 shadow-lg">
                                <img src="${photoUrl}" class="w-full h-full object-cover" onerror="this.src='../dist/img/default-avatar.svg'">
                            </div>
                            <div class="absolute -bottom-2 -right-2 w-7 h-7 bg-slate-800 text-white rounded-lg flex items-center justify-center text-[10px] font-black italic border-2 border-white">
                                ${idx+1}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-black text-slate-800 dark:text-white leading-tight truncate tracking-tight">
                                ${fullName}
                            </h4>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-md text-[10px] font-black uppercase tracking-tighter border border-blue-500/10">ID: ${s.Stu_id}</span>
                                <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-md text-[10px] font-black uppercase tracking-tighter border border-emerald-500/10">${classRoom}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions Mobile -->
                    <div class="mt-6 flex flex-wrap items-center gap-2 border-t dark:border-slate-700/50 pt-4">
                        <button class="btn-view flex-1 h-10 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-black text-[10px] uppercase italic transition-all active:scale-95" data-id="${s.Stu_id}">
                            View
                        </button>
                        <button onclick="editStudent('${s.Stu_id}')" class="w-10 h-10 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center transition-all active:scale-95">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button onclick="changePhoto('${s.Stu_id}')" class="w-10 h-10 bg-pink-100 dark:bg-pink-900/40 text-pink-600 dark:text-pink-400 rounded-xl flex items-center justify-center transition-all active:scale-95">
                            <i class="fas fa-camera text-xs"></i>
                        </button>
                        <button onclick="resetPassword('${s.Stu_id}')" class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center transition-all active:scale-95">
                            <i class="fas fa-key text-xs"></i>
                        </button>
                        <button onclick="deleteStudent('${s.Stu_id}')" class="w-10 h-10 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center transition-all active:scale-95">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            `;
            $mobileContainer.append(card);
        });
    }

    // Modal Logic
    window.closeFormModal = function() {
        $('#formModalContent').addClass('scale-95 opacity-0');
        setTimeout(() => $('#formModal').addClass('hidden'), 200);
    }

    window.openFormModal = function(title, icon) {
        $('#formModalTitle').text(title);
        $('#formModalIcon').attr('class', 'fas ' + icon);
        $('#formModal').removeClass('hidden');
        setTimeout(() => $('#formModalContent').removeClass('scale-95 opacity-0'), 10);
    }

    // Add Student
    window.addStudent = function() {
        const classVal = $('#selectClass').val() || '';
        const roomVal = $('#selectRoom').val() || '';
        
        const html = `
            <form id="studentActionForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="hidden" name="action" value="create">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">คำนำหน้า</label>
                    <select name="addStu_pre" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                        <option value="เด็กชาย">เด็กชาย</option>
                        <option value="เด็กหญิง">เด็กหญิง</option>
                        <option value="นาย">นาย</option>
                        <option value="นางสาว">นางสาว</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">เลขที่</label>
                    <input type="number" name="addStu_no" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">รหัสนักเรียน</label>
                    <input type="text" name="addStu_id" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ชื่อ (ภาษาไทย)</label>
                    <input type="text" name="addStu_name" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">นามสกุล (ภาษาไทย)</label>
                    <input type="text" name="addStu_sur" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                    <input type="text" name="addStu_major" value="${classVal}" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ห้อง</label>
                    <input type="text" name="addStu_room" value="${roomVal}" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                </div>
            </form>
        `;
        $('#formModalBody').html(html);
        openFormModal('เพิ่มนักเรียนใหม่', 'fa-user-plus');
        $('#btnSaveStudent').off('click').on('click', submitStudentForm);
    }

    // Edit Student
    window.editStudent = async function(id) {
        try {
            const data = await $.ajax({
                url: 'api/api_student.php',
                method: 'GET',
                data: { action: 'get', id: id }
            });

            const html = `
                <form id="studentActionForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="editStu_id_old" value="${data.Stu_id}">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">คำนำหน้า</label>
                        <select name="editStu_pre" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                            <option value="เด็กชาย" ${data.Stu_pre === 'เด็กชาย' ? 'selected' : ''}>เด็กชาย</option>
                            <option value="เด็กหญิง" ${data.Stu_pre === 'เด็กหญิง' ? 'selected' : ''}>เด็กหญิง</option>
                            <option value="นาย" ${data.Stu_pre === 'นาย' ? 'selected' : ''}>นาย</option>
                            <option value="นางสาว" ${data.Stu_pre === 'นางสาว' ? 'selected' : ''}>นางสาว</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">เลขที่</label>
                        <input type="number" name="editStu_no" value="${data.Stu_no}" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">รหัสนักเรียน</label>
                        <input type="text" name="editStu_id" value="${data.Stu_id}" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ชื่อ (ภาษาไทย)</label>
                        <input type="text" name="editStu_name" value="${data.Stu_name}" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">นามสกุล (ภาษาไทย)</label>
                        <input type="text" name="editStu_sur" value="${data.Stu_sur}" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                        <input type="text" name="editStu_major" value="${data.Stu_major}" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ห้อง</label>
                        <input type="text" name="editStu_room" value="${data.Stu_room}" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">สถานะ</label>
                        <select name="editStu_status" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition-all font-bold text-slate-700 dark:text-white">
                            <option value="1" ${data.Stu_status == 1 ? 'selected' : ''}>ปกติ</option>
                            <option value="0" ${data.Stu_status == 0 ? 'selected' : ''}>ย้าย/จำหน่าย</option>
                        </select>
                    </div>
                </form>
            `;
            $('#formModalBody').html(html);
            openFormModal('แก้ไขข้อมูลนักเรียน', 'fa-user-edit');
            $('#btnSaveStudent').off('click').on('click', submitStudentForm);
        } catch (error) {
            Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลนักเรียนได้', 'error');
        }
    }

    // Submit Student Form (Create/Update)
    async function submitStudentForm() {
        const $form = $('#studentActionForm');
        if (!$form[0].checkValidity()) {
            $form[0].reportValidity();
            return;
        }

        const formData = new FormData($form[0]);
        const action = formData.get('action');

        try {
            const response = await $.ajax({
                url: 'api/api_student.php?action=' + action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'บันทึกข้อมูลเรียบร้อยแล้ว',
                    timer: 1500,
                    showConfirmButton: false
                });
                closeFormModal();
                loadData();
            } else {
                Swal.fire('Error', response.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    }

    // Delete Student
    window.deleteStudent = function(id) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "ข้อมูลนักเรียนจะถูกย้ายไปที่ถังขยะ และไม่สามารถเรียกคืนได้ง่าย",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ยืนยัน ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await $.ajax({
                        url: 'api/api_student.php?action=delete',
                        method: 'POST',
                        data: { id: id },
                        dataType: 'json'
                    });
                    if (response.success) {
                        Swal.fire('ลบแล้ว!', 'ข้อมูลนักเรียนถูกลบเรียบร้อยแล้ว', 'success');
                        loadData();
                    } else {
                        Swal.fire('Error', response.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                }
            }
        });
    }

    // Reset Password
    window.resetPassword = function(id) {
        Swal.fire({
            title: 'รีเซ็ตรหัสผ่าน?',
            text: "รหัสผ่านจะถูกเปลี่ยนกลับเป็น 'รหัสนักเรียน'",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ยืนยัน รีเซ็ต',
            cancelButtonText: 'ยกเลิก'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await $.ajax({
                        url: 'api/api_student.php?action=resetpwd',
                        method: 'POST',
                        data: { id: id },
                        dataType: 'json'
                    });
                    if (response.success) {
                        Swal.fire('สำเร็จ!', 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว', 'success');
                    } else {
                        Swal.fire('Error', response.message || 'ไม่สามารถรีเซ็ตได้', 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                }
            }
        });
    }

    // Change Photo
    window.changePhoto = function(id) {
        currentStudentId = id;
        const html = `
            <div class="text-center space-y-6">
                <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-slate-100 shadow-xl bg-slate-50">
                    <img id="photoPreview" src="../dist/img/default-avatar.svg" class="w-full h-full object-cover">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เลือกรูปภาพใหม่</label>
                    <input type="file" id="inputPhoto" accept="image/*" class="hidden">
                    <button onclick="$('#inputPhoto').click()" class="w-full py-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl text-slate-400 hover:border-blue-500 hover:text-blue-500 transition-all font-bold">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> เลือกไฟล์รูปภาพ
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 italic italic font-medium">* แนะนำขนาด 500x500px ไฟล์ .jpg, .png ไม่เกิน 2MB</p>
            </div>
        `;
        $('#formModalBody').html(html);
        openFormModal('เปลี่ยนรูปภาพประจำตัว', 'fa-camera');
        
        // Photo Preview Logic
        $('#inputPhoto').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#photoPreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        $('#btnSaveStudent').off('click').on('click', async function() {
            const file = $('#inputPhoto')[0].files[0];
            if (!file) {
                Swal.fire('⚠️ แจ้งเตือน', 'กรุณาเลือกไฟล์รูปภาพก่อนบันทึก', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('Stu_id', currentStudentId);
            formData.append('profile_pic', file);

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> กำลังอัปโหลด...');

            try {
                const response = await $.ajax({
                    url: 'api/update_profile_pic_std.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json'
                });

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'อัปโหลดรูปภาพเรียบร้อยแล้ว',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    closeFormModal();
                    loadData();
                } else {
                    Swal.fire('Error', response.message || 'ไม่สามารถอัปโหลดได้', 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            } finally {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> บันทึกข้อมูล');
            }
        });
    }

    // Search Logic
    $('#studentSearch').on('input', function() {
        const val = $(this).val().toLowerCase();
        $('.student-row').each(function() {
            const rowText = $(this).data('search').toLowerCase();
            $(this).toggle(rowText.indexOf(val) > -1);
        });
    });

    // View Details Logic
    $(document).on('click', '.btn-view', function() {
        const stuId = $(this).data('id');
        $('#officerModalPlaceholder').remove(); 

        $.ajax({
            url: '../controllers/StudentController.php',
            method: 'GET',
            data: { action: 'get_modal_student_data', stu_id: stuId },
            success: function(response) {
                const $modalShell = $('<div id="officerModalPlaceholder" class="modal fade" tabindex="-1"></div>');
                // Use a standard Bootstrap structure or our premium modal
                // For simplicity and matching the teacher view, we'll use what the controller returns
                $modalShell.html(response).appendTo('body').modal('show');
                $modalShell.on('hidden.bs.modal', function() { $(this).remove(); });
            },
            error: function() { Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้', 'error'); }
        });
    });

    // Handle Resize
    window.addEventListener('resize', function() {
        if ($('#studentTableBody').children().length > 0) {
            showState('data');
        }
    });

    // Button Load Click
    $('#btnLoad').on('click', loadData);

    // Print Functionality
    $('#printStudentList').on('click', function() {
        const classValue = $('#selectClass').val();
        const roomValue = $('#selectRoom').val();
        const peeValue = "<?php echo $pee; ?>"; 

        if (!classValue || !roomValue) {
            Swal.fire('⚠️ แจ้งเตือน', 'กรุณาเลือกระดับชั้นและห้องเรียนก่อนสั่งพิมพ์', 'warning');
            return;
        }

        const url = `print_roster.php?level=${classValue}&room=${roomValue}&year=${peeValue}`;
        window.open(url, '_blank');
    });

    $('#printStudentCards').on('click', function() {
        const classValue = $('#selectClass').val();
        const roomValue = $('#selectRoom').val();
        if (!classValue || !roomValue) {
            Swal.fire('⚠️ แจ้งเตือน', 'กรุณาเลือกระดับชั้นและห้องเรียนก่อนสั่งพิมพ์', 'warning');
            return;
        }
        const url = `print_card_room.php?level=${classValue}&room=${roomValue}`;
        window.open(url, '_blank');
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
