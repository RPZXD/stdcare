<?php
/**
 * View: Student Visit Home Report (Officer)
 * MVC Pattern - Search and view individual student home visit details
 */
ob_start();
?>

<!-- jQuery UI for Autocomplete -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

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
    
    /* Autocomplete Styles Override */
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border: 1px solid #e2e8f0 !important;
        z-index: 9999 !important;
        padding: 8px;
    }
    .ui-menu-item { border-radius: 8px; margin-bottom: 2px; }
    .ui-menu-item-wrapper { padding: 10px 15px !important; transition: all 0.2s; font-size: 0.9rem; font-weight: 600; }
    .ui-state-active, .ui-state-active:hover {
        background: linear-gradient(90deg, #f97316, #ea580c) !important;
        color: white !important;
        border: none !important;
    }
    
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .glass-effect { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-red-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-3xl flex items-center justify-center text-white shadow-xl transform hover:rotate-6 transition-transform">
                        <i class="fas fa-home-user text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                            รายงานการเยี่ยมบ้านรายบุคคล
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                            ค้นหาและแสดงรายละเอียดการเยี่ยมบ้านนักเรียนเป็นรายบุคคล
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 no-print">
                    <a href="report.php" class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-white rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-2 border border-slate-200 dark:border-slate-700 transition-all shadow-sm">
                        <i class="fas fa-arrow-left"></i> กลับหน้าเมนู
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <form id="searchForm" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="md:col-span-3 space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ค้นหาชื่อ นามสกุล หรือเลขประจำตัวนักเรียน</label>
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-search text-lg"></i>
                    </div>
                    <input type="text" id="studentSearch" placeholder="พิมพ์ชื่อ นามสกุล หรือรหัสประจำตัวนักเรียนเพื่อค้นหา..." 
                           value="<?php echo htmlspecialchars($preloadStudentName ?? ''); ?>"
                           class="w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-orange-100 dark:focus:ring-orange-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
                    <input type="hidden" id="selectedStudentId" name="student_id" value="<?php echo htmlspecialchars($student_id ?? ''); ?>">
                </div>
            </div>

            <div class="flex items-end self-end">
                <button type="submit" class="w-full h-[60px] bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-black rounded-2xl shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-eye"></i> แสดงรายงาน
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section (Initially Hidden) -->
    <div id="reportContainer" class="hidden animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-white/50 dark:border-slate-700/50 relative overflow-hidden">
            <div id="reportContent">
                <!-- Fetch HTML will be rendered here -->
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="animate-fadeIn py-20 text-center">
        <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-100 dark:border-slate-800 transform transition-transform hover:scale-110">
            <i class="fas fa-search-plus text-5xl text-slate-200"></i>
        </div>
        <h3 class="text-2xl font-black text-slate-400 uppercase tracking-widest">กรุณาเลือกนักเรียนเพื่อแสดงข้อมูล</h3>
        <p class="text-slate-400 mt-2 font-medium italic">พิมพ์ชื่อหรือรหัสประจำตัวในกล่องค้นหาเพื่อดึงรายละเอียดการเยี่ยมบ้านรายบุคคล</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Autocomplete
    if (typeof $.fn.autocomplete === 'function') {
        $('#studentSearch').autocomplete({
            source: (req, resp) => {
                $.ajax({
                    url: '../teacher/api/search_autocomplete.php',
                    data: { term: req.term, type: 'student' },
                    dataType: 'json',
                    success: d => resp(d)
                });
            },
            minLength: 2,
            select: (e, ui) => {
                $('#studentSearch').val(ui.item.value);
                $('#selectedStudentId').val(ui.item.id);
                // Trigger form submission
                $('#searchForm').submit();
                return false;
            }
        });
    }

    // 2. Form Submission Handling
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const studentId = document.getElementById('selectedStudentId').value;
            const searchValue = document.getElementById('studentSearch').value;

            if (!studentId || !searchValue) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกนักเรียน',
                    text: 'กรุณาพิมพ์ค้นหาและเลือกรายชื่อนักเรียนจากรายการค้นหาที่ปรากฏ',
                    confirmButtonColor: '#f97316'
                });
                return;
            }

            // Show loading spinner
            Swal.showLoading();
            const reportContainer = document.getElementById('reportContainer');
            const emptyState = document.getElementById('emptyState');
            const reportContent = document.getElementById('reportContent');

            emptyState.classList.add('hidden');
            reportContainer.classList.remove('hidden');
            reportContent.innerHTML = `
                <div class="flex flex-col items-center justify-center py-20 text-slate-300">
                    <i class="fas fa-circle-notch fa-spin text-4xl mb-4 text-orange-500"></i>
                    <p class="font-black italic uppercase tracking-widest text-sm text-slate-500">กำลังดึงรายงานข้อมูลการเยี่ยมบ้าน...</p>
                </div>
            `;

            fetch(`../teacher/api/get_visit_details_full.php?student_id=${studentId}`)
                .then(res => {
                    Swal.close();
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.text();
                })
                .then(html => {
                    reportContent.innerHTML = `<div class="animate-fadeIn">${html}</div>`;
                })
                .catch(err => {
                    Swal.close();
                    reportContent.innerHTML = `
                        <div class="p-8 text-center text-rose-500 font-black italic">
                            <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                            <p>เกิดข้อผิดพลาดในการโหลดข้อมูล หรือไม่พบข้อมูลการเยี่ยมบ้านของนักเรียนคนนี้</p>
                        </div>
                    `;
                });
        });
    }

    // Clear selectedStudentId on manual typing changes
    document.getElementById('studentSearch').addEventListener('input', function() {
        document.getElementById('selectedStudentId').value = '';
    });

    // 3. Auto-submit if student_id is preloaded
    const preloadedId = '<?php echo htmlspecialchars($student_id ?? ""); ?>';
    if (preloadedId) {
        $('#searchForm').submit();
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
