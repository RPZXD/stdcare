<?php
/**
 * View: Parent Data Management (Officer)
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
        border-top: 3px solid #0ea5e9;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .parent-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .parent-card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-sky-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-cyan-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-sky-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-sky-500 to-cyan-700 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-users-rectangle text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            จัดการข้อมูล <span class="text-sky-600 italic">ผู้ปกครอง</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1 italic">
                            จัดการความสัมพันธ์ ตรวจสอบข้อมูลติดต่อ และอัปเดตข้อมูลผู้ปกครองรายบุคคลหรือแบบกลุ่ม
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8 no-print">
        <!-- CSV Upload / Download -->
        <div class="glass-effect rounded-[2rem] p-8 shadow-xl border border-white/50 dark:border-slate-700/50 flex flex-col justify-center animate-fadeIn" style="animation-delay: 0.1s">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                <i class="fas fa-file-csv text-sky-500 text-lg"></i> เครื่องมือจัดการ CSV
            </h3>
            <form id="csvUploadForm" class="space-y-4">
                <div class="relative group">
                    <input type="file" id="csv_file" name="csv_file" accept=".csv" class="hidden">
                    <button type="button" onclick="$('#csv_file').click()" class="w-full py-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl text-slate-400 hover:border-sky-500 hover:text-sky-500 transition-all font-bold group-hover:bg-sky-50/50 dark:group-hover:bg-sky-900/10">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> เลือกไฟล์ CSV
                    </button>
                    <div id="fileName" class="text-[10px] text-center mt-2 text-slate-400 font-bold italic truncate px-4"></div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-black shadow-lg shadow-sky-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-upload"></i> อัปโหลด
                    </button>
                    <button type="button" id="downloadTemplateBtn" class="flex-1 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-black hover:bg-slate-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i> โหลดเทมเพลต
                    </button>
                </div>
            </form>
        </div>

        <!-- Filter & Search -->
        <div class="xl:col-span-2 glass-effect rounded-[2rem] p-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn" style="animation-delay: 0.2s">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                <i class="fas fa-filter text-sky-500 text-lg"></i> คัดกรองและค้นหา
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ระดับชั้น</label>
                    <select id="filterClass" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white appearance-none cursor-pointer">
                        <option value="">-- ทั้งหมด --</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">ห้อง</label>
                    <select id="filterRoom" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white appearance-none cursor-pointer">
                        <option value="">-- ทั้งหมด --</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="filterButton" class="w-full h-[58px] bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white font-black rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 active:scale-95">
                        <i class="fas fa-search"></i> แสดงผล
                    </button>
                </div>
            </div>
            
            <div class="relative group">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-sky-500 transition-colors"></i>
                <input type="text" id="parentSearch" placeholder="ค้นหาด้วยชื่อนักเรียน, ชื่อผู้ปกครอง, หรือรหัส..." 
                    class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
            </div>
        </div>
    </div>

    <!-- Main Data View -->
    <div class="relative min-h-[400px]">
        <!-- Loading State -->
        <div id="loadingState" class="hidden absolute inset-0 z-10 flex flex-col items-center justify-center bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm rounded-[3rem]">
            <div class="loading-spinner mb-4"></div>
            <p class="text-sky-600 dark:text-sky-400 font-black animate-pulse uppercase tracking-widest">กำลังดึงข้อมูล...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="py-40 text-center glass-effect rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-user-slash text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-black text-slate-400 uppercase tracking-widest italic">ไม่พบข้อมูลผู้ปกครอง</h3>
            <p class="text-slate-400 mt-2 font-medium italic">กรุณาระบุเกณฑ์การค้นหาเพื่อแสดงรายชื่อ</p>
        </div>

        <!-- Desktop View (Table) -->
        <div id="desktopView" class="hidden overflow-hidden rounded-[2.5rem] glass-effect shadow-2xl border border-white/50 dark:border-slate-700/50">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">นักเรียน / ห้อง</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ผู้ปกครองหลัก</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">ความสัมพันธ์</th>
                        <th class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">เบอร์โทรศัพท์</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest italic">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="parentTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 font-bold text-slate-700 dark:text-slate-300">
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

<!-- Edit Modal -->
<div id="editParentModal" class="fixed inset-0 z-[60] hidden overflow-y-auto no-print">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeEditModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-4xl bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden animate-fadeIn scale-95 opacity-0 transition-all duration-300" id="editModalContent">
            <!-- Modal Header -->
            <div class="p-8 border-b dark:border-slate-700 bg-gradient-to-r from-sky-600 to-cyan-700 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-user-edit text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black italic tracking-tight" id="modalTitle">แก้ไขข้อมูลผู้ปกครอง</h3>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-sky-100 mt-0.5 opacity-80" id="modalSubtitle"></p>
                        </div>
                    </div>
                    <button onclick="closeEditModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 hover:bg-white/30 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="editParentForm">
                <input type="hidden" name="editStu_id" id="editStu_id">
                <div class="p-8 overflow-y-auto max-h-[70vh]">
                    <div class="space-y-10">
                        <!-- Father Section -->
                        <section>
                            <h4 class="text-[10px] font-black text-sky-500 uppercase tracking-[0.3em] mb-6 flex items-center gap-3">
                                <span class="w-2 h-2 bg-sky-500 rounded-full"></span> ข้อมูลบิดา
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">ชื่อ-สกุล</label>
                                    <input type="text" name="editFather_name" id="editFather_name" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">อาชีพ</label>
                                    <input type="text" name="editFather_occu" id="editFather_occu" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">รายได้</label>
                                    <input type="number" name="editFather_income" id="editFather_income" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                            </div>
                        </section>

                        <!-- Mother Section -->
                        <section>
                            <h4 class="text-[10px] font-black text-cyan-500 uppercase tracking-[0.3em] mb-6 flex items-center gap-3">
                                <span class="w-2 h-2 bg-cyan-500 rounded-full"></span> ข้อมูลมารดา
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">ชื่อ-สกุล</label>
                                    <input type="text" name="editMother_name" id="editMother_name" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">อาชีพ</label>
                                    <input type="text" name="editMother_occu" id="editMother_occu" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">รายได้</label>
                                    <input type="number" name="editMother_income" id="editMother_income" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                            </div>
                        </section>

                        <!-- Guardian Section -->
                        <section class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-inner">
                            <h4 class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.3em] mb-6 flex items-center gap-3">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full"></span> ผู้ปกครอง (ที่ติดต่อได้)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">ชื่อ-สกุล</label>
                                    <input type="text" name="editPar_name" id="editPar_name" class="w-full px-5 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">ความเกี่ยวข้อง</label>
                                    <input type="text" name="editPar_relate" id="editPar_relate" class="w-full px-5 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">เบอร์โทรศัพท์</label>
                                    <input type="text" name="editPar_phone" id="editPar_phone" class="w-full px-5 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold text-sky-600">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">อาชีพ</label>
                                    <input type="text" name="editPar_occu" id="editPar_occu" class="w-full px-5 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">รายได้</label>
                                    <input type="number" name="editPar_income" id="editPar_income" class="w-full px-5 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">ที่อยู่ผู้ปกครอง</label>
                                    <input type="text" name="editPar_addr" id="editPar_addr" class="w-full px-5 py-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-sky-100 outline-none font-bold">
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-8 border-t dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-8 py-4 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-2xl font-black transition-all active:scale-95">
                        <i class="fas fa-times mr-2"></i> ยกเลิก
                    </button>
                    <button type="button" id="submitEditParentForm" class="px-12 py-4 bg-gradient-to-r from-sky-600 to-cyan-700 text-white rounded-2xl font-black shadow-xl shadow-sky-500/30 hover:shadow-2xl hover:scale-[1.02] transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-save mr-2"></i> บันทึกข้อมูลที่แก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const API_URL = '../controllers/ParentController.php';
    let allParents = [];

    // State Management
    function showState(state) {
        $('#loadingState, #emptyState, #desktopView, #mobileView').addClass('hidden');
        if (state === 'loading') $('#loadingState').removeClass('hidden');
        else if (state === 'empty') $('#emptyState').removeClass('hidden');
        else if (state === 'data') {
            if (window.innerWidth >= 1024) $('#desktopView').removeClass('hidden');
            else $('#mobileView').removeClass('hidden');
        }
    }

    // Load Initial Data
    loadParents();
    populateFilters();

    // Data Loading Logic
    function loadParents(classVal = '', roomVal = '') {
        showState('loading');
        let url = API_URL + "?action=list";
        if (classVal) url += "&class=" + encodeURIComponent(classVal);
        if (roomVal) url += "&room=" + encodeURIComponent(roomVal);
        
        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                allParents = data;
                renderData(data);
                showState(data.length > 0 ? 'data' : 'empty');
            },
            error: function() {
                showState('empty');
                Swal.fire('Error', 'ไม่สามารถเชื่อมต่อข้อมูลได้', 'error');
            }
        });
    }

    // Render Logic
    function renderData(data) {
        const $tableBody = $('#parentTableBody').empty();
        const $mobileContainer = $('#mobileView').empty();
        
        data.forEach((p, index) => {
            const studentName = `${p.Stu_name || ''} ${p.Stu_sur || ''}`;
            const classRoom = `ม.${p.Stu_major || ''}/${p.Stu_room || ''}`;
            const searchData = `${studentName} ${p.Stu_id} ${p.Par_name} ${p.Father_name} ${p.Mother_name}`.toLowerCase();

            // Desktop Row
            const row = `
                <tr class="hover:bg-sky-50/30 dark:hover:bg-sky-900/10 transition-colors parent-row animate-fadeIn" style="animation-delay: ${index * 0.05}s" data-search="${searchData}">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/40 rounded-xl flex items-center justify-center text-sky-600">
                                <i class="fas fa-id-card-clip"></i>
                            </div>
                            <div>
                                <div class="text-[13px] font-black text-slate-800 dark:text-white">${studentName}</div>
                                <div class="text-[10px] font-bold text-sky-500 uppercase tracking-tight">${classRoom} • ID: ${p.Stu_id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-sm font-black text-slate-600 dark:text-slate-400">${p.Par_name || '-'}</td>
                    <td class="px-6 py-5">
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-[10px] font-black uppercase tracking-widest">${p.Par_relate || '-'}</span>
                    </td>
                    <td class="px-6 py-5">
                        <a href="tel:${p.Par_phone}" class="text-sm font-black text-indigo-500 hover:underline">${p.Par_phone || '-'}</a>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button onclick="editParent('${p.Stu_id}')" class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center hover:bg-amber-600 hover:text-white transition-all active:scale-95 shadow-sm">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                    </td>
                </tr>
            `;
            $tableBody.append(row);

            // Mobile Card
            const card = `
                <div class="parent-card glass-effect p-6 rounded-[2.5rem] shadow-xl border border-white/50 dark:border-slate-800 animate-fadeIn" style="animation-delay: ${index * 0.05}s">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-black text-slate-800 dark:text-white truncate">${studentName}</h3>
                                <p class="text-[10px] font-black text-sky-500 uppercase tracking-widest">${classRoom} • ID: ${p.Stu_id}</p>
                            </div>
                        </div>
                        <button onclick="editParent('${p.Stu_id}')" class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center transition-all active:scale-95">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                    </div>

                    <div class="space-y-3 bg-slate-50 dark:bg-slate-900/50 p-5 rounded-3xl border border-slate-100 dark:border-slate-800">
                        <div class="flex flex-col gap-1">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">ผู้ปกครอง</span>
                            <span class="text-sm font-black text-slate-700 dark:text-slate-300">${p.Par_name || '-'} (${p.Par_relate || '-'})</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">เบอร์โทรศัพท์</span>
                            <a href="tel:${p.Par_phone}" class="text-sm font-black text-indigo-500">${p.Par_phone || '-'}</a>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2 px-2">
                        ${p.Father_name ? `<span class="px-3 py-1 bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400 rounded-lg text-[9px] font-black uppercase tracking-tighter">B: ${p.Father_name}</span>` : ''}
                        ${p.Mother_name ? `<span class="px-3 py-1 bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 rounded-lg text-[9px] font-black uppercase tracking-tighter">M: ${p.Mother_name}</span>` : ''}
                    </div>
                </div>
            `;
            $mobileContainer.append(card);
        });
    }

    // Modal Operations
    window.editParent = async function(id) {
        try {
            const response = await $.get(`${API_URL}?action=get&id=${id}`);
            const p = response;
            
            if (p && p.Stu_id) {
                $('#editStu_id').val(p.Stu_id);
                $('#editFather_name').val(p.Father_name || '');
                $('#editFather_occu').val(p.Father_occu || '');
                $('#editFather_income').val(p.Father_income || '');
                $('#editMother_name').val(p.Mother_name || '');
                $('#editMother_occu').val(p.Mother_occu || '');
                $('#editMother_income').val(p.Mother_income || '');
                $('#editPar_name').val(p.Par_name || '');
                $('#editPar_relate').val(p.Par_relate || '');
                $('#editPar_occu').val(p.Par_occu || '');
                $('#editPar_income').val(p.Par_income || '');
                $('#editPar_addr').val(p.Par_addr || '');
                $('#editPar_phone').val(p.Par_phone || '');
                
                $('#modalSubtitle').text(`รหัสนักเรียน: ${p.Stu_id}`);
                $('#modalTitle').text(`แก้ไขข้อมูลของ: ${p.Stu_name}`);
                
                $('#editParentModal').removeClass('hidden');
                setTimeout(() => $('#editModalContent').removeClass('scale-95 opacity-0'), 10);
            }
        } catch (error) {
            Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้', 'error');
        }
    }

    window.closeEditModal = function() {
        $('#editModalContent').addClass('scale-95 opacity-0');
        setTimeout(() => $('#editParentModal').addClass('hidden'), 250);
    }

    // Submit Logic
    $('#submitEditParentForm').on('click', async function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> กำลังบันทึก...');
        
        try {
            const formData = new FormData(document.getElementById('editParentForm'));
            const res = await $.ajax({
                url: API_URL + '?action=update',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            if (res.success) {
                closeEditModal();
                loadParents($('#filterClass').val(), $('#filterRoom').val());
                Swal.fire('✅ สำเร็จ', 'บันทึกข้อมูลสำเร็จ', 'success');
            } else {
                Swal.fire('❌ ล้มเหลว', res.message || 'ไม่สามารถบันทึกได้', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        } finally {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });

    // CSV Logic
    $('#csv_file').on('change', function() {
        const file = this.files[0];
        $('#fileName').text(file ? `ไฟล์ที่เลือก: ${file.name}` : '');
    });

    $('#csvUploadForm').on('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        if (!formData.get('csv_file').name) {
            Swal.fire('⚠️ แจ้งเตือน', 'กรุณาเลือกไฟล์ CSV ก่อนอัปโหลด', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'กำลังอัปโหลด...',
            text: 'ระบบกำลังประมวลผลข้อมูลผู้ปกครอง',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await $.ajax({
                url: API_URL + '?action=upload_csv',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            if (res.status === 'completed') {
                Swal.fire({
                    icon: 'success',
                    title: 'อัปโหลดสำเร็จ!',
                    html: `<div class="text-left py-2 font-bold">บันทึกสำเร็จ: <span class="text-emerald-500">${res.report.success}</span> รายการ<br>ล้มเหลว: <span class="text-rose-500">${res.report.failed}</span> รายการ</div>`,
                    confirmButtonColor: '#0ea5e9'
                });
                loadParents($('#filterClass').val(), $('#filterRoom').val());
                $('#fileName').text('');
                document.getElementById('csvUploadForm').reset();
            } else {
                Swal.fire('❌ ล้มเหลว', res.message || 'ไม่สามารถอัปโหลดได้', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการอัปโหลด', 'error');
        }
    });

    $('#downloadTemplateBtn').on('click', function() {
        const classVal = $('#filterClass').val();
        const roomVal = $('#filterRoom').val();
        window.location.href = `${API_URL}?action=download_template&class=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`;
    });

    // Filter Logic
    $('#filterButton').on('click', function() {
        loadParents($('#filterClass').val(), $('#filterRoom').val());
    });

    async function populateFilters() {
        try {
            const res = await $.get('../controllers/StudentController.php?action=get_filters');
            const classSel = $('#filterClass');
            const roomSel = $('#filterRoom');
            
            res.majors.forEach(m => m && classSel.append(`<option value="${m}">${m}</option>`));
            res.rooms.forEach(r => r && roomSel.append(`<option value="${r}">${r}</option>`));
        } catch (e) {
            console.error('Failed to load filters');
        }
    }

    // Search Interaction
    $('#parentSearch').on('input', function() {
        const val = $(this).val().toLowerCase();
        $('.parent-row').each(function() {
            $(this).toggle($(this).data('search').indexOf(val) > -1);
        });
        $('.parent-card').each(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    // Handle Responsive Resize
    window.addEventListener('resize', function() {
        if (allParents.length > 0) showState('data');
    });

});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
