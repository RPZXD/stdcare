<?php
/**
 * Teacher Student Data View
 * MVC Pattern - Premium Modern UI with Tailwind CSS
 * Mobile-First: Card Layout for Mobile, Grid for Desktop
 */
$pageTitle = $pageTitle ?? 'ข้อมูลนักเรียน';

ob_start();
?>

<!-- Premium Custom Styles -->
<style>
    /* Glass Card Effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.9);
    }
    
    /* Floating Animation */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .floating { animation: float 3s ease-in-out infinite; }
    
    /* Pulse Glow */
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 15px rgba(139, 92, 246, 0.3); }
        50% { box-shadow: 0 0 30px rgba(139, 92, 246, 0.5); }
    }
    .pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
    
    /* Card Hover Effect */
    .student-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        transform-style: preserve-3d;
    }
    .student-card:hover {
        transform: translateY(-12px) rotateX(2deg);
        box-shadow: 0 30px 60px rgba(139, 92, 246, 0.2);
    }
    .student-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 60%, rgba(139, 92, 246, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: inherit;
        pointer-events: none;
    }
    .student-card:hover::before {
        opacity: 1;
    }
    
    /* Action Button */
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: scale(1.15) translateY(-2px);
    }
    .btn-action:active {
        transform: scale(0.95);
    }
    
    /* Toggle Switch */
    .toggle-switch {
        width: 56px;
        height: 28px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-radius: 14px;
        cursor: pointer;
        position: relative;
        transition: all 0.4s ease;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.2);
    }
    .toggle-switch::after {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        background: white;
        border-radius: 50%;
        top: 3px;
        left: 3px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    .toggle-switch.active {
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }
    .toggle-switch.active::after {
        left: 31px;
    }
    
    /* Skeleton Loading */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.2s infinite;
    }
    .dark .skeleton {
        background: linear-gradient(90deg, #334155 25%, #475569 50%, #334155 75%);
        background-size: 200% 100%;
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    /* Image Placeholder */
    .img-placeholder {
        background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 50%, #d946ef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: bold;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    /* Stats Card Hover */
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    /* Fade In Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .fade-in-up {
        animation: fadeInUp 0.5s ease forwards;
    }
    
    /* Modal Overlay */
    .modal-overlay {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .modal-content {
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    /* Gender Icon */
    .gender-male { color: #3b82f6; }
    .gender-female { color: #ec4899; }
    
    /* Search Focus */
    .search-input:focus {
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.2);
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden">
    <div class="glass-card rounded-2xl md:rounded-3xl p-5 md:p-8 border border-white/50 dark:border-slate-700/50 shadow-2xl relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute top-0 right-0 w-40 md:w-80 h-40 md:h-80 bg-gradient-to-br from-violet-400/30 to-purple-500/30 rounded-full blur-3xl -z-10 floating"></div>
        <div class="absolute bottom-0 left-0 w-32 md:w-64 h-32 md:h-64 bg-gradient-to-tr from-pink-400/30 to-rose-500/30 rounded-full blur-3xl -z-10 floating" style="animation-delay: -1.5s;"></div>
        <div class="absolute top-1/2 left-1/2 w-20 md:w-40 h-20 md:h-40 bg-gradient-to-br from-fuchsia-400/20 to-purple-500/20 rounded-full blur-2xl -z-10 floating" style="animation-delay: -0.75s;"></div>
        
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <!-- Title Section -->
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl blur-xl opacity-60"></div>
                    <div class="relative w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-2xl pulse-glow">
                        <i class="fas fa-user-graduate text-white text-2xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-1">
                        👨‍🎓 ข้อมูลนักเรียน
                    </h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-violet-100 to-purple-100 dark:from-violet-900/40 dark:to-purple-900/40 text-violet-700 dark:text-violet-300 rounded-full font-bold text-sm shadow-sm">
                            <i class="fas fa-chalkboard-teacher"></i>
                            ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/40 dark:to-pink-900/40 text-purple-700 dark:text-purple-300 rounded-full font-bold text-sm shadow-sm">
                            <i class="fas fa-calendar-alt"></i>
                            ปีการศึกษา <?php echo htmlspecialchars($pee); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Search & Controls -->
            <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 lg:w-72">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-violet-400"></i>
                    </div>
                    <input type="text" id="studentSearch" placeholder="🔍 ค้นหาชื่อ, รหัส, ชื่อเล่น..." 
                           class="search-input w-full pl-11 pr-4 py-3 border-2 border-violet-200 dark:border-slate-600 rounded-2xl text-sm dark:bg-slate-700/80 dark:text-white focus:border-violet-500 transition-all font-medium placeholder-gray-400">
                </div>
                
                <!-- Toggle Edit Permission -->
                <div class="flex items-center gap-3 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl px-5 py-3 border border-white/50 dark:border-slate-600/50 shadow-lg">
                    <div class="toggle-switch" id="allowEditSwitch"></div>
                    <div class="flex flex-col">
                        <span class="text-xs text-slate-500 dark:text-slate-400">สิทธิ์แก้ไข</span>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200" id="editStatusText">🔒 ปิด</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <!-- Total Students -->
    <div class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-violet-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-lg"></i>
        </div>
        <p class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-purple-600" id="totalCount">-</p>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">นักเรียนทั้งหมด</p>
    </div>
    
    <!-- Male -->
    <div class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-mars text-white text-lg"></i>
        </div>
        <p class="text-3xl md:text-4xl font-black text-blue-600" id="maleCount">-</p>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">ชาย</p>
    </div>
    
    <!-- Female -->
    <div class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl text-center">
        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-pink-400 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-venus text-white text-lg"></i>
        </div>
        <p class="text-3xl md:text-4xl font-black text-pink-600" id="femaleCount">-</p>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">หญิง</p>
    </div>
    
    <!-- Print Buttons -->
    <div class="stat-card glass-card rounded-2xl p-4 md:p-5 border border-white/50 dark:border-slate-700/50 shadow-xl flex flex-col justify-center gap-2">
        <button id="printStudentList" class="w-full bg-gradient-to-r from-emerald-500 to-green-600 text-white py-2.5 rounded-xl font-bold text-xs md:text-sm shadow-lg hover:shadow-xl hover:shadow-emerald-500/30 transition-all flex items-center justify-center gap-2 hover:scale-[1.02]">
            <i class="fas fa-list-ul"></i>
            <span>พิมพ์รายชื่อ</span>
        </button>
        <button id="printStudentCards" class="w-full bg-gradient-to-r from-violet-500 to-purple-600 text-white py-2.5 rounded-xl font-bold text-xs md:text-sm shadow-lg hover:shadow-xl hover:shadow-violet-500/30 transition-all flex items-center justify-center gap-2 hover:scale-[1.02]">
            <i class="fas fa-id-card"></i>
            <span>พิมพ์บัตร (Card)</span>
        </button>
    </div>
</div>

<!-- Student Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6" id="studentGrid">
    <!-- Loading Skeletons -->
    <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="skeleton-card glass-card rounded-2xl overflow-hidden shadow-lg <?php echo $i > 0 ? 'hidden sm:block' : ''; ?> <?php echo $i > 1 ? 'lg:block' : ''; ?> <?php echo $i > 2 ? 'xl:block' : ''; ?>">
        <div class="skeleton h-48 w-full"></div>
        <div class="p-5 space-y-3">
            <div class="skeleton h-5 w-3/4 rounded-lg"></div>
            <div class="skeleton h-3 w-1/2 rounded-lg"></div>
            <div class="skeleton h-3 w-2/3 rounded-lg"></div>
            <div class="flex gap-2 mt-4">
                <div class="skeleton h-9 w-9 rounded-lg"></div>
                <div class="skeleton h-9 w-9 rounded-lg"></div>
                <div class="skeleton h-9 w-9 rounded-lg"></div>
            </div>
        </div>
    </div>
    <?php endfor; ?>
</div>

<!-- No Data Message -->
<div id="noDataMessage" class="hidden glass-card rounded-3xl p-12 text-center border border-white/50 dark:border-slate-700/50 shadow-xl">
    <div class="w-28 h-28 mx-auto bg-gradient-to-br from-gray-200 to-gray-300 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center mb-8 floating shadow-inner">
        <i class="fas fa-users-slash text-gray-400 dark:text-slate-500 text-5xl"></i>
    </div>
    <p class="text-2xl font-black text-slate-700 dark:text-slate-300 mb-3">📭 ไม่พบข้อมูลนักเรียน</p>
    <p class="text-slate-500 dark:text-slate-400">กรุณาตรวจสอบข้อมูลหรือติดต่อผู้ดูแลระบบ</p>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden modal-overlay">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeViewModal()"></div>
    <div class="modal-content fixed inset-4 md:inset-8 lg:inset-16 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col z-10">
        <div class="flex items-center justify-between p-5 border-b dark:border-slate-700 bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 text-white">
            <h3 class="text-xl font-black flex items-center gap-2">
                <i class="fas fa-user-circle"></i>
                <span>ข้อมูลนักเรียน</span>
            </h3>
            <button onclick="closeViewModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 hover:bg-white/30 transition-all hover:scale-110">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 md:p-8" id="viewModalBody">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden modal-overlay">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="modal-content fixed inset-4 md:inset-8 lg:inset-16 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col z-10">
        <div class="flex items-center justify-between p-5 border-b dark:border-slate-700 bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 text-white">
            <h3 class="text-xl font-black flex items-center gap-2">
                <i class="fas fa-user-edit"></i>
                <span>แก้ไขข้อมูลนักเรียน</span>
            </h3>
            <button onclick="closeEditModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 hover:bg-white/30 transition-all hover:scale-110">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 md:p-8" id="editModalBody">
            <!-- Content loaded via AJAX -->
        </div>
        <div class="p-5 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-900 flex flex-col sm:flex-row justify-end gap-3">
            <button onclick="closeEditModal()" class="px-6 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                <i class="fas fa-times"></i>
                <span>ยกเลิก</span>
            </button>
            <button id="saveChanges" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-save"></i>
                <span>บันทึกข้อมูล</span>
            </button>
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div id="photoModal" class="fixed inset-0 z-50 hidden modal-overlay">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closePhotoModal()"></div>
    <div class="modal-content fixed inset-4 md:inset-20 lg:inset-y-16 lg:inset-x-1/4 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col z-10">
        <div class="flex items-center justify-between p-5 border-b dark:border-slate-700 bg-gradient-to-r from-pink-500 via-rose-500 to-red-500 text-white">
            <h3 class="text-xl font-black flex items-center gap-2">
                <i class="fas fa-camera-retro"></i>
                <span>เปลี่ยนรูปภาพ</span>
            </h3>
            <button onclick="closePhotoModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 hover:bg-white/30 transition-all hover:scale-110">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 md:p-8" id="photoModalBody">
            <div class="text-center">
                <div class="mb-6">
                    <label class="block w-full p-10 border-3 border-dashed border-violet-300 dark:border-slate-600 rounded-3xl cursor-pointer hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-slate-700/50 transition-all group">
                        <input type="file" id="photoInput" accept="image/*" class="hidden">
                        <div class="text-6xl mb-4 group-hover:scale-110 transition-transform">📷</div>
                        <p class="text-lg text-slate-700 dark:text-slate-300 font-bold mb-2">คลิกเพื่อเลือกรูปภาพ</p>
                        <p class="text-sm text-slate-500">หรือลากไฟล์มาวางที่นี่</p>
                        <p class="text-xs text-slate-400 mt-2">รองรับ JPG, PNG, WEBP (ไม่เกิน 5MB)</p>
                    </label>
                </div>
                <div id="photoPreview" class="hidden mb-4">
                    <div class="relative inline-block">
                        <img id="previewImg" src="" class="max-h-72 mx-auto rounded-2xl shadow-2xl border-4 border-white dark:border-slate-700">
                        <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-green-500 text-white px-4 py-1 rounded-full text-sm font-bold shadow-lg">
                            ✓ พร้อมอัปโหลด
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-5 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-900 flex flex-col sm:flex-row justify-end gap-3">
            <button onclick="closePhotoModal()" class="px-6 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                <i class="fas fa-times"></i>
                <span>ยกเลิก</span>
            </button>
            <button id="uploadPhoto" class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100" disabled>
                <i class="fas fa-cloud-upload-alt"></i>
                <span>อัปโหลดรูปภาพ</span>
            </button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
const classValue = <?php echo json_encode($class); ?>;
const roomValue = <?php echo json_encode($room); ?>;
const teacherName = <?php echo json_encode($teacher_name); ?>;
let currentStudentId = null;
let allStudents = [];

document.addEventListener('DOMContentLoaded', function() {
    loadStudentData();
    loadEditPermission();
    
    // Search with debounce
    document.getElementById('studentSearch').addEventListener('input', debounce(searchStudents, 300));
    
    // Toggle Switch
    document.getElementById('allowEditSwitch').addEventListener('click', toggleEditPermission);
    
    // Photo input
    document.getElementById('photoInput').addEventListener('change', handlePhotoSelect);
    
    // Upload photo
    document.getElementById('uploadPhoto').addEventListener('click', uploadPhoto);
    
    // Print
    document.getElementById('printStudentList').addEventListener('click', printStudentList);
    document.getElementById('printStudentCards').addEventListener('click', printStudentCards);
    
    // Drag and drop for photo
    setupDragAndDrop();
});

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function setupDragAndDrop() {
    const dropZone = document.querySelector('label[for="photoInput"]');
    if (!dropZone) return;
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('border-violet-500', 'bg-violet-50'), false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-violet-500', 'bg-violet-50'), false);
    });
    
    dropZone.addEventListener('drop', function(e) {
        const files = e.dataTransfer.files;
        if (files.length) {
            document.getElementById('photoInput').files = files;
            handlePhotoSelect({ target: { files } });
        }
    }, false);
}

async function loadStudentData() {
    try {
        const response = await fetch(`api/fetch_data_student.php?class=${classValue}&room=${roomValue}`);
        const result = await response.json();
        
        // Remove skeletons
        document.querySelectorAll('.skeleton-card').forEach(el => el.remove());
        
        if (!result.success || !result.data || result.data.length === 0) {
            document.getElementById('noDataMessage').classList.remove('hidden');
            updateStats([]);
            return;
        }
        
        allStudents = result.data;
        renderStudents(result.data);
        updateStats(result.data);
        
    } catch (error) {
        console.error('Error loading students:', error);
        document.querySelectorAll('.skeleton-card').forEach(el => el.remove());
        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: 'ไม่สามารถโหลดข้อมูลได้' });
    }
}

function renderStudents(students) {
    const grid = document.getElementById('studentGrid');
    grid.innerHTML = '';
    
    students.forEach((item, index) => {
        const card = document.createElement('div');
        card.className = 'student-card glass-card rounded-2xl overflow-hidden shadow-xl border border-white/50 dark:border-slate-700/50 fade-in-up relative';
        card.dataset.id = item.Stu_id;
        card.dataset.name = `${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}`.toLowerCase();
        card.dataset.nick = (item.Stu_nick || '').toLowerCase();
        card.style.animationDelay = `${index * 0.08}s`;
        
        const isMale = item.Stu_pre === 'นาย' || item.Stu_pre === 'เด็กชาย';
        const genderIcon = isMale ? '<i class="fas fa-mars gender-male"></i>' : '<i class="fas fa-venus gender-female"></i>';
        const genderBg = isMale ? 'from-blue-500 to-indigo-600' : 'from-pink-500 to-rose-600';
        
        card.innerHTML = `
            <div class="relative group">
                <img src="https://std.phichai.ac.th/photo/${item.Stu_picture || 'default.jpg'}" 
                     alt="${item.Stu_name}"
                     class="w-full h-44 md:h-52 object-cover transition-transform duration-500 group-hover:scale-110"
                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'img-placeholder w-full h-44 md:h-52\\'>${getInitials(item.Stu_name)}</div>'">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="absolute top-3 right-3 bg-gradient-to-r ${genderBg} text-white rounded-xl w-10 h-10 flex items-center justify-center text-lg font-black shadow-lg border-2 border-white/30">
                    ${item.Stu_no}
                </div>
                <div class="absolute top-3 left-3 w-8 h-8 bg-white/90 dark:bg-slate-800/90 backdrop-blur rounded-lg flex items-center justify-center shadow-lg">
                    ${genderIcon}
                </div>
            </div>
            <div class="p-4 md:p-5">
                <h3 class="font-black text-slate-800 dark:text-white text-base mb-1.5 truncate">${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono bg-slate-100 dark:bg-slate-700/50 inline-block px-2 py-0.5 rounded mb-2">${item.Stu_id}</p>
                ${item.Stu_nick ? `<p class="text-sm text-violet-600 dark:text-violet-400 font-bold mb-3 flex items-center gap-1"><span class="text-xs">👋</span> ${item.Stu_nick}</p>` : '<div class="mb-3"></div>'}
                
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-slate-700">
                    ${item.Stu_phone ? `
                        <a href="tel:${item.Stu_phone}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold flex items-center gap-1">
                            <i class="fas fa-phone-alt"></i>
                            <span>${item.Stu_phone}</span>
                        </a>` : '<span></span>'}
                    
                    <div class="flex gap-2">
                        <button onclick="viewStudent('${item.Stu_id}')" class="btn-action w-9 h-9 bg-gradient-to-r from-blue-400 to-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg" title="ดูข้อมูล">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="editStudent('${item.Stu_id}')" class="btn-action w-9 h-9 bg-gradient-to-r from-amber-400 to-orange-500 text-white rounded-xl flex items-center justify-center shadow-lg" title="แก้ไข">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="changePhoto('${item.Stu_id}')" class="btn-action w-9 h-9 bg-gradient-to-r from-pink-400 to-rose-500 text-white rounded-xl flex items-center justify-center shadow-lg" title="เปลี่ยนรูป">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        grid.appendChild(card);
    });
}

function getInitials(name) {
    return name ? name.charAt(0).toUpperCase() : '👤';
}

function updateStats(students) {
    const total = students.length;
    const male = students.filter(s => s.Stu_pre === 'นาย' || s.Stu_pre === 'เด็กชาย').length;
    const female = total - male;
    
    animateNumber('totalCount', total);
    animateNumber('maleCount', male);
    animateNumber('femaleCount', female);
}

function animateNumber(elementId, target) {
    const el = document.getElementById(elementId);
    const duration = 800;
    const start = parseInt(el.textContent) || 0;
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const current = Math.round(start + (target - start) * easeOutQuart);
        el.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

function searchStudents() {
    const query = document.getElementById('studentSearch').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.student-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const name = card.dataset.name;
        const id = card.dataset.id;
        const nick = card.dataset.nick;
        const match = name.includes(query) || id.includes(query) || nick.includes(query);
        card.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });
    
    document.getElementById('noDataMessage').classList.toggle('hidden', visibleCount > 0 || query === '');
}

// Edit Permission
async function loadEditPermission() {
    try {
        const response = await fetch(`api/get_student_edit_permission.php?room_key=${classValue}-${roomValue}`);
        const data = await response.json();
        updatePermissionUI(data.allowEdit);
    } catch (error) {
        console.error('Error loading permission:', error);
    }
}

async function toggleEditPermission() {
    const switchEl = document.getElementById('allowEditSwitch');
    const isActive = switchEl.classList.contains('active');
    const newState = !isActive;
    
    try {
        const formData = new FormData();
        formData.append('room_key', `${classValue}-${roomValue}`);
        formData.append('allowEdit', newState ? 1 : 0);
        formData.append('by', teacherName);
        
        await fetch('api/set_student_edit_permission.php', {
            method: 'POST',
            body: formData
        });
        
        updatePermissionUI(newState);
        Swal.fire({
            icon: 'success',
            title: newState ? '🔓 เปิดการแก้ไข' : '🔒 ปิดการแก้ไข',
            text: newState ? 'นักเรียนสามารถแก้ไขข้อมูลได้' : 'ปิดการแก้ไขข้อมูลแล้ว',
            toast: true,
            position: 'top-end',
            timer: 2500,
            showConfirmButton: false,
            background: newState ? '#10b981' : '#f59e0b',
            color: '#fff'
        });
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
    }
}

function updatePermissionUI(isAllowed) {
    const switchEl = document.getElementById('allowEditSwitch');
    const textEl = document.getElementById('editStatusText');
    
    if (isAllowed) {
        switchEl.classList.add('active');
        textEl.textContent = '🔓 เปิด';
        textEl.className = 'text-sm font-bold text-green-600';
    } else {
        switchEl.classList.remove('active');
        textEl.textContent = '🔒 ปิด';
        textEl.className = 'text-sm font-bold text-slate-600 dark:text-slate-300';
    }
}

// View Student
async function viewStudent(stuId) {
    document.getElementById('viewModal').classList.remove('hidden');
    document.getElementById('viewModalBody').innerHTML = '<div class="text-center py-16"><div class="inline-block"><i class="fas fa-spinner fa-spin text-5xl text-violet-500 mb-4 block"></i><p class="text-slate-500 font-medium">กำลังโหลดข้อมูล...</p></div></div>';
    
    try {
        const response = await fetch(`api/view_student.php?stu_id=${stuId}`);
        const html = await response.text();
        document.getElementById('viewModalBody').innerHTML = html;
    } catch (error) {
        document.getElementById('viewModalBody').innerHTML = '<div class="text-center py-16 text-red-500"><i class="fas fa-exclamation-circle text-5xl mb-4"></i><p class="font-bold">ไม่สามารถโหลดข้อมูลได้</p></div>';
    }
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

// Edit Student
async function editStudent(stuId) {
    currentStudentId = stuId;
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModalBody').innerHTML = '<div class="text-center py-16"><div class="inline-block"><i class="fas fa-spinner fa-spin text-5xl text-amber-500 mb-4 block"></i><p class="text-slate-500 font-medium">กำลังโหลดแบบฟอร์ม...</p></div></div>';
    
    try {
        const response = await fetch(`api/edit_student_form.php?stu_id=${stuId}`);
        const html = await response.text();
        document.getElementById('editModalBody').innerHTML = html;
        document.getElementById('saveChanges').onclick = saveStudentChanges;
    } catch (error) {
        document.getElementById('editModalBody').innerHTML = '<div class="text-center py-16 text-red-500"><i class="fas fa-exclamation-circle text-5xl mb-4"></i><p class="font-bold">ไม่สามารถโหลดข้อมูลได้</p></div>';
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    currentStudentId = null;
}

async function saveStudentChanges() {
    const form = document.getElementById('editStudentForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const saveBtn = document.getElementById('saveChanges');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังบันทึก...';
    
    try {
        const response = await fetch('api/update_student.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({ 
                icon: 'success', 
                title: '✅ บันทึกสำเร็จ!', 
                timer: 1500, 
                showConfirmButton: false,
                background: '#10b981',
                color: '#fff'
            });
            closeEditModal();
            loadStudentData();
        } else {
            Swal.fire({ icon: 'error', title: 'บันทึกล้มเหลว', text: result.error || '' });
        }
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> บันทึกข้อมูล';
    }
}

// Photo
function changePhoto(stuId) {
    currentStudentId = stuId;
    document.getElementById('photoInput').value = '';
    document.getElementById('photoPreview').classList.add('hidden');
    document.getElementById('uploadPhoto').disabled = true;
    document.getElementById('photoModal').classList.remove('hidden');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
    currentStudentId = null;
}

function handlePhotoSelect(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({ icon: 'warning', title: 'ไฟล์ใหญ่เกินไป', text: 'ขนาดไม่เกิน 5MB' });
        return;
    }
    
    if (!file.type.startsWith('image/')) {
        Swal.fire({ icon: 'warning', title: 'ไฟล์ไม่ถูกต้อง', text: 'กรุณาเลือกไฟล์รูปภาพ' });
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('photoPreview').classList.remove('hidden');
        document.getElementById('uploadPhoto').disabled = false;
    };
    reader.readAsDataURL(file);
}

async function uploadPhoto() {
    const file = document.getElementById('photoInput').files[0];
    if (!file || !currentStudentId) return;
    
    const formData = new FormData();
    formData.append('stu_id', currentStudentId);
    formData.append('photo', file);
    
    const btn = document.getElementById('uploadPhoto');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังอัปโหลด...';
    
    try {
        const response = await fetch('api/upload_student_photo.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({ 
                icon: 'success', 
                title: '✅ อัปโหลดสำเร็จ!', 
                timer: 1500, 
                showConfirmButton: false,
                background: '#10b981',
                color: '#fff'
            });
            closePhotoModal();
            loadStudentData();
        } else {
            Swal.fire({ icon: 'error', title: 'อัปโหลดล้มเหลว', text: result.error || '' });
        }
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด' });
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-cloud-upload-alt mr-1"></i> อัปโหลดรูปภาพ';
    }
}

function printStudentList() {
    window.open(`print_student_list.php?class=${classValue}&room=${roomValue}`, '_blank');
}

function printStudentCards() {
    window.open(`print_student_cards.php?class=${classValue}&room=${roomValue}`, '_blank');
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
