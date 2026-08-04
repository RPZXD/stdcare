<?php
/**
 * Parent Data View - MVC Pattern
 * View parent/guardian information for students in teacher's class
 */
ob_start();
?>

<!-- Custom Styles -->
<style>
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}
.dark .glass-card {
    background: rgba(30, 41, 59, 0.95);
}

.parent-row:hover {
    transform: translateX(4px);
    transition: all 0.2s ease;
}

.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}
@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.fade-in-up {
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .mobile-card { display: block !important; }
    .desktop-table { display: none !important; }
}
@media (min-width: 769px) {
    .mobile-card { display: none !important; }
    .desktop-table { display: block !important; }
}
</style>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-users text-xl"></i>
                </div>
                ข้อมูลผู้ปกครอง
            </h1>
            <p class="mt-1 text-slate-600 dark:text-slate-400">
                ชั้น ม.<?php echo $class; ?>/<?php echo $room; ?> | ภาคเรียน <?php echo $term; ?>/<?php echo $pee; ?>
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2 md:gap-3">
            <button onclick="printReport()" class="px-4 py-2.5 bg-gradient-to-r from-purple-500 to-violet-600 text-white rounded-xl font-bold shadow-lg hover:shadow-purple-500/30 transition flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span class="hidden sm:inline">พิมพ์</span>
            </button>
            <button onclick="exportExcel()" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-lg hover:shadow-emerald-500/30 transition flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span class="hidden sm:inline">Excel</span>
            </button>
        </div>
    </div>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-user-graduate text-white text-lg"></i>
        </div>
        <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-600" id="totalStudents">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">นักเรียน</p>
    </div>
    
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-teal-400 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-lg"></i>
        </div>
        <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-cyan-600" id="totalParents">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">มีข้อมูลผู้ปกครอง</p>
    </div>
    
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-emerald-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-phone-alt text-white text-lg"></i>
        </div>
        <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-green-600" id="totalPhones">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">มีเบอร์โทร</p>
    </div>
    
    <div class="glass-card rounded-2xl p-4 border border-white/50 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-rose-400 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
        </div>
        <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-500 to-red-600" id="missingData">0</p>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ข้อมูลไม่ครบ</p>
    </div>
</div>

<!-- Search Bar + Filter -->
<div class="glass-card rounded-2xl border border-white/50 dark:border-slate-700/50 shadow-lg p-4 mb-6">
    <div class="flex flex-col md:flex-row gap-3">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-400"></i>
            </div>
            <input type="text" id="searchInput" 
                   class="w-full pl-11 pr-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-teal-400 focus:ring-2 focus:ring-teal-400/20 transition"
                   placeholder="ค้นหา รหัสนักเรียน, ชื่อนักเรียน, ชื่อผู้ปกครอง, เบอร์โทร...">
        </div>
        <select id="filterStatus" class="px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-teal-400 transition min-w-[180px]">
            <option value="all">📋 ทั้งหมด</option>
            <option value="complete">✅ มีข้อมูลครบ</option>
            <option value="incomplete">⚠️ ข้อมูลไม่ครบ</option>
        </select>
    </div>
</div>

<!-- Parent Data List -->
<div class="glass-card rounded-2xl border border-white/50 dark:border-slate-700/50 shadow-xl overflow-hidden">
    <!-- Header -->
    <div class="p-4 md:p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-teal-500 to-cyan-600">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fas fa-address-book"></i>
            รายชื่อนักเรียนและผู้ปกครอง
        </h2>
    </div>
    
    <!-- Desktop Table -->
    <div class="desktop-table overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th class="px-3 py-3 text-center text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-12">ที่</th>
                    <th class="px-3 py-3 text-center text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-20">รหัส</th>
                    <th class="px-3 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">ชื่อ-สกุล</th>
                    <th class="px-3 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">ชื่อผู้ปกครอง</th>
                    <th class="px-3 py-3 text-center text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-24">ความสัมพันธ์</th>
                    <th class="px-3 py-3 text-center text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-28">เบอร์โทร</th>
                    <th class="px-3 py-3 text-center text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-24">จัดการ</th>
                </tr>
            </thead>
            <tbody id="parentTableBody" class="divide-y divide-slate-200 dark:divide-slate-700">
                <!-- Loading Skeletons -->
                <?php for($i = 0; $i < 5; $i++): ?>
                <tr class="skeleton-row">
                    <td class="px-4 py-4"><div class="skeleton h-4 w-8 rounded mx-auto"></div></td>
                    <td class="px-4 py-4"><div class="skeleton h-4 w-16 rounded mx-auto"></div></td>
                    <td class="px-4 py-4"><div class="skeleton h-4 w-40 rounded"></div></td>
                    <td class="px-4 py-4"><div class="skeleton h-4 w-32 rounded"></div></td>
                    <td class="px-4 py-4"><div class="skeleton h-4 w-24 rounded mx-auto"></div></td>
                    <td class="px-4 py-4"><div class="skeleton h-8 w-20 rounded mx-auto"></div></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Cards -->
    <div id="mobileCards" class="mobile-card p-4 space-y-3">
        <!-- Will be populated by JS -->
    </div>
    
    <!-- Empty State -->
    <div id="emptyState" class="hidden p-8 text-center">
        <div class="w-20 h-20 mx-auto mb-4 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
            <i class="fas fa-users-slash text-3xl text-slate-400"></i>
        </div>
        <h3 class="font-bold text-slate-700 dark:text-slate-300 mb-1">ไม่พบข้อมูลนักเรียน</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">ยังไม่มีข้อมูลนักเรียนในชั้นเรียนนี้</p>
    </div>
    
    <!-- Pagination -->
    <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-sm text-slate-600 dark:text-slate-400" id="paginationInfo">แสดง 0 รายการ</p>
        <div class="flex gap-2" id="paginationButtons"></div>
    </div>
</div>

<!-- View Student Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute inset-0 bg-black/50" onclick="closeViewModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl shadow-2xl transform transition-all fade-in-up">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-user-circle"></i>
                        ข้อมูลนักเรียนและผู้ปกครอง
                    </h3>
                    <button onclick="closeViewModal()" class="text-white/80 hover:text-white text-2xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto" id="viewModalContent">
                <!-- Content loaded dynamically -->
            </div>
            <div class="p-6 border-t border-slate-200 dark:border-slate-700">
                <button onclick="closeViewModal()" class="w-full py-3.5 px-6 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold hover:bg-slate-200 transition">
                    <i class="fas fa-times mr-2"></i>ปิด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay absolute inset-0 bg-black/50" onclick="closeEditModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-3xl shadow-2xl transform transition-all fade-in-up">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-amber-500 to-orange-600 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        แก้ไขข้อมูลผู้ปกครอง
                    </h3>
                    <button onclick="closeEditModal()" class="text-white/80 hover:text-white text-2xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <form id="editParentForm">
                    <input type="hidden" id="editStuId" name="stu_id">
                    
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl border border-blue-200 dark:border-blue-700">
                        <h4 id="editStudentName" class="font-bold text-blue-800 dark:text-blue-300"></h4>
                        <p id="editStudentInfo" class="text-sm text-blue-600 dark:text-blue-400"></p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-user mr-1 text-teal-500"></i> ชื่อผู้ปกครอง
                            </label>
                            <input type="text" id="editParentName" name="par_name"
                                   class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-teal-400 transition">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-phone-alt mr-1 text-teal-500"></i> เบอร์โทรผู้ปกครอง
                            </label>
                            <input type="tel" id="editParentPhone" name="par_phone"
                                   class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-teal-400 transition"
                                   placeholder="0XX-XXX-XXXX">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                <i class="fas fa-heart mr-1 text-teal-500"></i> ความสัมพันธ์
                            </label>
                            <select id="editRelation" name="par_relation"
                                    class="w-full px-4 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl dark:bg-slate-700 dark:text-white focus:border-teal-400 transition">
                                <option value="">-- เลือก --</option>
                                <option value="บิดา">บิดา</option>
                                <option value="มารดา">มารดา</option>
                                <option value="ปู่">ปู่</option>
                                <option value="ย่า">ย่า</option>
                                <option value="ตา">ตา</option>
                                <option value="ยาย">ยาย</option>
                                <option value="ลุง">ลุง</option>
                                <option value="ป้า">ป้า</option>
                                <option value="อื่นๆ">อื่นๆ</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-3">
                <button onclick="closeEditModal()" class="flex-1 py-3.5 px-6 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-bold">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </button>
                <button onclick="saveParentData()" class="flex-1 py-3.5 px-6 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold shadow-lg">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
const classId = <?php echo $class; ?>;
const roomId = <?php echo $room; ?>;

let allStudents = [];
let filteredStudents = [];
let currentPage = 1;
const itemsPerPage = 20;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadStudents();
    
    document.getElementById('searchInput').addEventListener('input', function() {
        filterStudents();
    });
    
    document.getElementById('filterStatus').addEventListener('change', function() {
        filterStudents();
    });
});

async function loadStudents() {
    try {
        const response = await fetch(`../teacher/api/fetch_data_student.php?class=${classId}&room=${roomId}`);
        const result = await response.json();
        
        if (result.success) {
            allStudents = result.data || [];
            filteredStudents = [...allStudents];
            renderStudents();
            updateStats();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function filterStudents() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    
    filteredStudents = allStudents.filter(s => {
        const hasParent = s.Par_name && s.Par_name.trim();
        const hasPhone = s.Par_phone && s.Par_phone.trim();
        const isComplete = hasParent && hasPhone;
        
        // Status filter
        if (statusFilter === 'complete' && !isComplete) return false;
        if (statusFilter === 'incomplete' && isComplete) return false;
        
        // Search filter
        return !search || 
            (s.Stu_id && s.Stu_id.includes(search)) ||
            (s.Stu_name && s.Stu_name.toLowerCase().includes(search)) ||
            (s.Stu_sur && s.Stu_sur.toLowerCase().includes(search)) ||
            (s.Par_name && s.Par_name.toLowerCase().includes(search)) ||
            (s.Par_phone && s.Par_phone.includes(search));
    });
    
    currentPage = 1;
    renderStudents();
}

function renderStudents() {
    const tbody = document.getElementById('parentTableBody');
    const mobileCards = document.getElementById('mobileCards');
    const emptyState = document.getElementById('emptyState');
    
    document.querySelectorAll('.skeleton-row').forEach(el => el.remove());
    
    if (filteredStudents.length === 0) {
        tbody.innerHTML = '';
        mobileCards.innerHTML = '';
        emptyState.classList.remove('hidden');
        document.getElementById('paginationInfo').textContent = 'ไม่พบข้อมูล';
        return;
    }
    
    emptyState.classList.add('hidden');
    
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginated = filteredStudents.slice(start, end);
    
    // Desktop Table
    let tableHtml = '';
    paginated.forEach((s, i) => {
        const hasParent = s.Par_name && s.Par_name.trim();
        const hasPhone = s.Par_phone && s.Par_phone.trim();
        
        tableHtml += `
        <tr class="parent-row hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
            <td class="px-3 py-3 text-center font-medium text-slate-700 dark:text-slate-300">${start + i + 1}</td>
            <td class="px-3 py-3 text-center font-mono text-sm text-slate-600 dark:text-slate-400">${s.Stu_id}</td>
            <td class="px-3 py-3 font-medium text-slate-800 dark:text-white">${s.Stu_pre || ''}${s.Stu_name} ${s.Stu_sur}</td>
            <td class="px-3 py-3">
                ${hasParent ? 
                    `<span class="text-slate-700 dark:text-slate-300">${s.Par_name}</span>` : 
                    `<span class="text-red-500 italic text-sm"><i class="fas fa-exclamation-circle mr-1"></i>ไม่มีข้อมูล</span>`
                }
            </td>
            <td class="px-3 py-3 text-center">
                ${s.Par_relate ? 
                    `<span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">${s.Par_relate}</span>` : 
                    `<span class="text-slate-400 text-sm">-</span>`
                }
            </td>
            <td class="px-3 py-3 text-center">
                ${hasPhone ? 
                    `<a href="tel:${s.Par_phone}" class="text-teal-600 hover:text-teal-800 font-medium text-sm"><i class="fas fa-phone-alt mr-1"></i>${s.Par_phone}</a>` : 
                    `<span class="text-slate-400 text-sm">-</span>`
                }
            </td>
            <td class="px-3 py-3 text-center">
                <div class="flex items-center justify-center gap-1">
                    <button onclick="viewStudent('${s.Stu_id}')" class="p-2 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition" title="ดูข้อมูล">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="editStudent('${s.Stu_id}')" class="p-2 text-amber-600 hover:bg-amber-100 dark:hover:bg-amber-900/30 rounded-lg transition" title="แก้ไข">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    });
    tbody.innerHTML = tableHtml;
    
    // Mobile Cards
    let cardsHtml = '';
    paginated.forEach((s, i) => {
        const hasParent = s.Par_name && s.Par_name.trim();
        const hasPhone = s.Par_phone && s.Par_phone.trim();
        
        cardsHtml += `
        <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4 fade-in-up" style="animation-delay: ${i * 0.05}s">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-white">${s.Stu_pre || ''}${s.Stu_name} ${s.Stu_sur}</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">รหัส: ${s.Stu_id}</p>
                </div>
                <span class="text-sm font-bold text-slate-500">#${start + i + 1}</span>
            </div>
            
            <div class="space-y-2 text-sm mb-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-tie text-teal-500 w-5"></i>
                    ${hasParent ? 
                        `<span class="text-slate-700 dark:text-slate-300">${s.Par_name}</span>
                         ${s.Par_relate ? `<span class="px-2 py-0.5 rounded-full text-xs bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">${s.Par_relate}</span>` : ''}` : 
                        `<span class="text-red-500 italic">ไม่มีข้อมูลผู้ปกครอง</span>`
                    }
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-phone-alt text-teal-500 w-5"></i>
                    ${hasPhone ? 
                        `<a href="tel:${s.Par_phone}" class="text-teal-600 font-medium">${s.Par_phone}</a>` : 
                        `<span class="text-slate-400">-</span>`
                    }
                </div>
            </div>
            
            <div class="flex gap-2 pt-3 border-t border-slate-200 dark:border-slate-600">
                <button onclick="viewStudent('${s.Stu_id}')" class="flex-1 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg font-medium text-sm">
                    <i class="fas fa-eye mr-1"></i>ดู
                </button>
                <button onclick="editStudent('${s.Stu_id}')" class="flex-1 py-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-lg font-medium text-sm">
                    <i class="fas fa-edit mr-1"></i>แก้ไข
                </button>
                ${hasPhone ? `
                <a href="tel:${s.Par_phone}" class="flex-1 py-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-lg font-medium text-sm text-center">
                    <i class="fas fa-phone-alt mr-1"></i>โทร
                </a>
                ` : ''}
            </div>
        </div>
        `;
    });
    mobileCards.innerHTML = cardsHtml;
    
    document.getElementById('paginationInfo').textContent = `แสดง ${start + 1}-${Math.min(end, filteredStudents.length)} จาก ${filteredStudents.length} รายการ`;
    
    const totalPages = Math.ceil(filteredStudents.length / itemsPerPage);
    let paginationHtml = '';
    
    if (totalPages > 1) {
        paginationHtml += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 disabled:opacity-50"><i class="fas fa-chevron-left"></i></button>`;
        
        for (let p = 1; p <= totalPages; p++) {
            if (p === currentPage) {
                paginationHtml += `<button class="px-3 py-2 rounded-lg bg-teal-500 text-white font-bold">${p}</button>`;
            } else {
                paginationHtml += `<button onclick="changePage(${p})" class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700">${p}</button>`;
            }
        }
        
        paginationHtml += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 disabled:opacity-50"><i class="fas fa-chevron-right"></i></button>`;
    }
    document.getElementById('paginationButtons').innerHTML = paginationHtml;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredStudents.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderStudents();
    }
}

function updateStats() {
    document.getElementById('totalStudents').textContent = allStudents.length;
    
    const withParent = allStudents.filter(s => s.Par_name && s.Par_name.trim()).length;
    document.getElementById('totalParents').textContent = withParent;
    
    const withPhone = allStudents.filter(s => s.Par_phone && s.Par_phone.trim()).length;
    document.getElementById('totalPhones').textContent = withPhone;
    
    const missing = allStudents.length - withParent;
    document.getElementById('missingData').textContent = missing;
}

async function viewStudent(stuId) {
    try {
        const response = await fetch(`../teacher/api/view_student.php?stu_id=${stuId}`);
        const html = await response.text();
        
        document.getElementById('viewModalContent').innerHTML = html;
        document.getElementById('viewModal').classList.remove('hidden');
    } catch (e) {
        Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูล', 'error');
    }
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

async function editStudent(stuId) {
    const student = allStudents.find(s => s.Stu_id === stuId);
    if (!student) return;
    
    document.getElementById('editStuId').value = student.Stu_id;
    document.getElementById('editStudentName').textContent = `${student.Stu_pre || ''}${student.Stu_name} ${student.Stu_sur}`;
    document.getElementById('editStudentInfo').textContent = `รหัส: ${student.Stu_id}`;
    document.getElementById('editParentName').value = student.Par_name || '';
    document.getElementById('editParentPhone').value = student.Par_phone || '';
    document.getElementById('editRelation').value = student.Par_relate || '';
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

async function saveParentData() {
    const formData = new FormData(document.getElementById('editParentForm'));
    
    try {
        const response = await fetch('../teacher/api/update_parent.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success');
            closeEditModal();
            loadStudents();
        } else {
            Swal.fire('ข้อผิดพลาด', result.message || 'ไม่สามารถบันทึกได้', 'error');
        }
    } catch (e) {
        Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
    }
}

function printReport() {
    const termPee = 'ภาคเรียนที่ <?php echo $term; ?>/<?php echo $pee; ?>';
    const className = 'ม.<?php echo $class; ?>/<?php echo $room; ?>';
    const teacherName = '<?php echo htmlspecialchars($teacher_name ?? ""); ?>';
    const roomTeachers = <?php echo json_encode(array_map(function($t) { return ['id' => $t['Teach_id'], 'name' => $t['Teach_name']]; }, $roomTeachers ?? [])); ?>;
    const currentDate = new Date().toLocaleDateString('th-TH', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    // Summary stats
    const totalStudents = allStudents.length;
    const withParent = allStudents.filter(s => s.Par_name && s.Par_name.trim()).length;
    const withPhone = allStudents.filter(s => s.Par_phone && s.Par_phone.trim()).length;
    const missing = totalStudents - withParent;
    
    let tableRows = '';
    allStudents.forEach((s, i) => {
        const hasParent = s.Par_name && s.Par_name.trim();
        const hasPhone = s.Par_phone && s.Par_phone.trim();
        tableRows += `
            <tr style="background: ${i % 2 === 0 ? '#f8fafc' : '#ffffff'};">
                <td style="padding: 8px 6px; border: 1px solid #e2e8f0; text-align: center; font-weight: 500;">${i + 1}</td>
                <td style="padding: 8px 6px; border: 1px solid #e2e8f0; text-align: center; font-family: monospace; font-size: 9pt;">${s.Stu_id}</td>
                <td style="padding: 8px 6px; border: 1px solid #e2e8f0;">${s.Stu_pre || ''}${s.Stu_name} ${s.Stu_sur}</td>
                <td style="padding: 8px 6px; border: 1px solid #e2e8f0; ${!hasParent ? 'color: #dc2626; font-style: italic;' : ''}">${s.Par_name || 'ไม่มีข้อมูล'}</td>
                <td style="padding: 8px 6px; border: 1px solid #e2e8f0; text-align: center;">${s.Par_relate || '-'}</td>
                <td style="padding: 8px 6px; border: 1px solid #e2e8f0; text-align: center;">${s.Par_phone || '-'}</td>
                <td style="padding: 8px 6px; border: 1px solid #e2e8f0; text-align: center;"></td>
            </tr>
        `;
    });
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>ข้อมูลผู้ปกครอง - ${className}</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap');
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Sarabun', sans-serif; padding: 15px; font-size: 10pt; color: #1e293b; }
                .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #0d9488; }
                .header img { width: 50px; height: 50px; margin-bottom: 8px; }
                .header h1 { font-size: 16pt; color: #0d9488; margin-bottom: 3px; }
                .header h2 { font-size: 12pt; color: #374151; margin-bottom: 3px; }
                .header p { font-size: 10pt; color: #64748b; }
                .info-box { display: flex; justify-content: center; gap: 30px; margin-bottom: 15px; background: linear-gradient(135deg, #f0fdfa, #ccfbf1); padding: 12px; border-radius: 8px; }
                .info-box div { text-align: center; }
                .info-box .label { font-size: 9pt; color: #64748b; }
                .info-box .value { font-size: 12pt; font-weight: bold; }
                .info-box .value.teal { color: #0d9488; }
                .info-box .value.green { color: #059669; }
                .info-box .value.red { color: #dc2626; }
                table { width: 100%; border-collapse: collapse; font-size: 9pt; }
                thead th { background: linear-gradient(135deg, #0d9488, #0891b2); color: white; padding: 10px 8px; border: 1px solid #0d9488; font-weight: 600; }
                .signature { margin-top: 40px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px; }
                .signature-box { text-align: center; min-width: 180px; flex: 1; }
                .signature-line { border-top: 1px solid #1e293b; margin-top: 50px; padding-top: 8px; font-size: 9pt; }
                .footer { margin-top: 20px; text-align: right; font-size: 9pt; color: #64748b; }
                @media print {
                    body { padding: 10px; }
                    @page { size: A4 portrait; margin: 0.8cm; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="../dist/img/logo-phicha.png" alt="Logo">
                <h1>📋 ข้อมูลผู้ปกครองนักเรียน</h1>
                <h2>ชั้น ${className}</h2>
                <p>${termPee} | วันที่พิมพ์: ${currentDate}</p>
            </div>
            
            <div class="info-box">
                <div>
                    <div class="label">นักเรียนทั้งหมด</div>
                    <div class="value teal">${totalStudents} คน</div>
                </div>
                <div>
                    <div class="label">มีข้อมูลผู้ปกครอง</div>
                    <div class="value green">${withParent} คน</div>
                </div>
                <div>
                    <div class="label">มีเบอร์โทร</div>
                    <div class="value green">${withPhone} คน</div>
                </div>
                <div>
                    <div class="label">ข้อมูลไม่ครบ</div>
                    <div class="value red">${missing} คน</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">ที่</th>
                        <th style="width: 55px;">รหัส</th>
                        <th>ชื่อ-สกุลนักเรียน</th>
                        <th style="width: 130px;">ชื่อผู้ปกครอง</th>
                        <th style="width: 55px;">สัมพันธ์</th>
                        <th style="width: 85px;">เบอร์โทร</th>
                        <th style="width: 60px;">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows || '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">ไม่พบข้อมูล</td></tr>'}
                </tbody>
            </table>
            
            <div class="signature">
                ${roomTeachers.map((t, i) => `
                <div class="signature-box">
                    <div class="signature-line">
                        (${t.name})<br>
                        ครูที่ปรึกษา
                    </div>
                </div>
                `).join('')}
            </div>
            
            <div class="footer">
                <p>ระบบดูแลช่วยเหลือนักเรียน - โรงเรียนพิชัย</p>
            </div>
            
            <scr` + `ipt>
                window.onload = function() {
                    window.print();
                };
            </scr` + `ipt>
        </body>
        </html>
    `;
    
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(printContent);
    printWindow.document.close();
}

function exportExcel() {
    if (allStudents.length === 0) {
        Swal.fire('ไม่มีข้อมูล', 'ไม่พบข้อมูลนักเรียนที่จะส่งออก', 'warning');
        return;
    }
    
    const className = 'ม.<?php echo $class; ?>-<?php echo $room; ?>';
    const termPee = '<?php echo $term; ?>-<?php echo $pee; ?>';
    
    // Build CSV content with BOM for Thai encoding
    let csvContent = '\ufeff'; // UTF-8 BOM
    csvContent += 'ที่,รหัสนักเรียน,คำนำหน้า,ชื่อ,นามสกุล,ชื่อผู้ปกครอง,เบอร์โทรผู้ปกครอง,ความสัมพันธ์\n';
    
    allStudents.forEach((s, i) => {
        const row = [
            i + 1,
            s.Stu_id || '',
            s.Stu_pre || '',
            s.Stu_name || '',
            s.Stu_sur || '',
            (s.Par_name || '').replace(/,/g, ' '),
            (s.Par_phone || '').replace(/,/g, ' '),
            (s.Par_relate || '').replace(/,/g, ' ')
        ];
        csvContent += row.join(',') + '\n';
    });
    
    // Create blob and download
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `ข้อมูลผู้ปกครอง_${className}_${termPee}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    Swal.fire({
        icon: 'success',
        title: 'ส่งออกสำเร็จ!',
        text: `ดาวน์โหลดไฟล์ ข้อมูลผู้ปกครอง_${className}_${termPee}.csv เรียบร้อย`,
        timer: 2000,
        showConfirmButton: false
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
