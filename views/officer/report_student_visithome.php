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
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        border: 1px solid #e2e8f0 !important;
        z-index: 9999 !important;
        padding: 8px;
        transition: background-color 0.3s;
    }
    .dark .ui-autocomplete {
        background: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }
    .ui-menu-item { border-radius: 12px; margin-bottom: 2px; }
    .ui-menu-item-wrapper { padding: 12px 18px !important; transition: all 0.2s; font-size: 0.9rem; font-weight: 600; color: #334155; }
    .dark .ui-menu-item-wrapper { color: #cbd5e1; }
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
    
    /* Focus & Typing Interactions */
    #studentSearch {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #studentSearch:focus {
        border-color: #f97316 !important;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.15);
        background-color: white;
    }
    .dark #studentSearch:focus {
        background-color: rgba(15, 23, 42, 0.6);
    }
    
    /* Custom Animations for Empty State */
    @keyframes bounceSlow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .animate-bounce-slow {
        animation: bounceSlow 3s ease-in-out infinite;
    }
    .text-gradient {
        background: linear-gradient(135deg, #f97316, #dc2626);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
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
                    <!-- Print Button (Hidden by default, shown when reportContainer is visible) -->
                    <button type="button" id="printReportBtn" class="hidden px-5 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-2 transition-all shadow-md hover:scale-105 hover:shadow-lg active:scale-95">
                        <i class="fas fa-print animate-bounce"></i> พิมพ์รายงาน
                    </button>
                    <a href="report.php" class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-white rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-2 border border-slate-200 dark:border-slate-700 transition-all shadow-sm active:scale-95">
                        <i class="fas fa-arrow-left"></i> กลับหน้าเมนู
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="glass-effect rounded-[2rem] p-8 mb-8 shadow-xl border border-white/50 dark:border-slate-700/50 animate-fadeIn no-print" style="animation-delay: 0.1s">
        <form id="searchForm" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="md:col-span-3 space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 italic">ค้นหาชื่อ นามสกุล หรือเลขประจำตัวนักเรียน</label>
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-search text-lg"></i>
                    </div>
                    <input type="text" id="studentSearch" placeholder="พิมพ์ชื่อ นามสกุล หรือรหัสประจำตัวนักเรียนเพื่อค้นหา..." 
                           value="<?php echo htmlspecialchars($preloadStudentName ?? ''); ?>"
                           class="w-full pl-12 pr-12 py-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-2xl focus:ring-4 focus:ring-orange-100 dark:focus:ring-orange-900/20 outline-none transition-all font-bold text-slate-700 dark:text-white">
                    <button type="button" id="clearSearchBtn" class="<?php echo empty($student_id) ? 'hidden' : ''; ?> absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500 transition-colors w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center">
                        <i class="fas fa-times-circle text-lg"></i>
                    </button>
                    <input type="hidden" id="selectedStudentId" name="student_id" value="<?php echo htmlspecialchars($student_id ?? ''); ?>">
                </div>
            </div>

            <div class="flex items-end self-end">
                <button type="submit" class="w-full h-[60px] bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-black rounded-2xl shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-eye"></i> แสดงรายงาน
                </button>
            </div>
        </form>

        <?php if (!empty($recentStudents)): ?>
        <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800/80">
            <span class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5 italic">
                <i class="fas fa-history text-orange-500"></i> ประวัติเยี่ยมบ้านล่าสุด:
            </span>
            <?php foreach ($recentStudents as $rs): 
                $rsName = $rs['Stu_pre'] . $rs['Stu_name'] . ' ' . $rs['Stu_sur'];
                $rsLabel = $rsName . ' (ม.' . $rs['Stu_major'] . '/' . $rs['Stu_room'] . ')';
            ?>
                <button type="button" class="recent-student-chip px-4 py-2 bg-slate-50 hover:bg-orange-500/10 text-slate-600 dark:bg-slate-900/50 dark:text-slate-300 dark:hover:bg-orange-500/10 hover:text-orange-600 dark:hover:text-orange-400 rounded-full font-bold text-xs border border-slate-100 dark:border-slate-800 transition-all hover:scale-105 active:scale-95 flex items-center gap-1.5 shadow-sm"
                        data-id="<?php echo $rs['Stu_id']; ?>"
                        data-label="<?php echo htmlspecialchars($rsLabel); ?>"
                        data-value="<?php echo htmlspecialchars($rsName); ?>">
                    <i class="fas fa-user-circle text-slate-400"></i>
                    <?php echo htmlspecialchars($rsName); ?>
                    <span class="opacity-60 font-medium">(ม.<?php echo $rs['Stu_major']; ?>/<?php echo $rs['Stu_room']; ?>)</span>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
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
    <div id="emptyState" class="animate-fadeIn py-20 text-center relative overflow-hidden">
        <!-- Interactive Glowing/Floating background effects -->
        <div class="relative w-48 h-48 mx-auto mb-8 flex items-center justify-center">
            <div class="absolute inset-0 bg-gradient-to-tr from-orange-500/10 to-red-500/10 rounded-full animate-ping duration-1000 opacity-75"></div>
            <div class="absolute w-36 h-36 bg-orange-500/5 rounded-full border border-orange-500/20 animate-pulse"></div>
            <div class="absolute w-28 h-28 bg-white dark:bg-slate-900 rounded-[2rem] flex items-center justify-center shadow-xl border border-slate-100 dark:border-slate-800 transform hover:scale-110 transition-transform duration-500">
                <i class="fas fa-search-location text-5xl text-gradient animate-bounce-slow"></i>
            </div>
        </div>
        <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-wide uppercase">พร้อมค้นหารายงานการเยี่ยมบ้าน</h3>
        <p class="text-slate-400 mt-3 max-w-md mx-auto font-medium text-sm leading-relaxed">
            พิมพ์ชื่อ นามสกุล หรือรหัสประจำตัวนักเรียน ในช่องค้นหาด้านบน เพื่อตรวจสอบและจัดพิมพ์รายงานผลการเยี่ยมบ้านรายบุคคลทันที
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const clearBtn = document.getElementById('clearSearchBtn');
    const searchInput = document.getElementById('studentSearch');
    const printBtn = document.getElementById('printReportBtn');

    // Helper function to load home visit details via AJAX
    function loadReport(studentId) {
        if (!studentId) return;

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
                // Show print button when report is successfully loaded
                if (printBtn) {
                    printBtn.classList.remove('hidden');
                }
            })
            .catch(err => {
                Swal.close();
                reportContent.innerHTML = `
                    <div class="p-8 text-center text-rose-500 font-black italic">
                        <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                        <p>เกิดข้อผิดพลาดในการโหลดข้อมูล หรือไม่พบข้อมูลการเยี่ยมบ้านของนักเรียนคนนี้</p>
                    </div>
                `;
                if (printBtn) {
                    printBtn.classList.add('hidden');
                }
            });
    }

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
                if (clearBtn) clearBtn.classList.remove('hidden');
                loadReport(ui.item.id);
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

            loadReport(studentId);
        });
    }

    // Toggle clear button and reset selection on input changes
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            document.getElementById('selectedStudentId').value = '';
            if (this.value.trim() !== '') {
                if (clearBtn) clearBtn.classList.remove('hidden');
            } else {
                if (clearBtn) clearBtn.classList.add('hidden');
            }
        });
    }

    // Clear Button Action
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            document.getElementById('selectedStudentId').value = '';
            clearBtn.classList.add('hidden');

            const reportContainer = document.getElementById('reportContainer');
            const emptyState = document.getElementById('emptyState');
            if (reportContainer) reportContainer.classList.add('hidden');
            if (emptyState) emptyState.classList.remove('hidden');
            if (printBtn) printBtn.classList.add('hidden');
        });
    }

    // Print Button Action
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }

    // 2.5 Recent Student Chips Interaction
    $('.recent-student-chip').on('click', function() {
        const id = $(this).data('id');
        const value = $(this).data('value');

        if (searchInput) searchInput.value = value;
        document.getElementById('selectedStudentId').value = id;
        if (clearBtn) clearBtn.classList.remove('hidden');
        loadReport(id);
    });

    // 3. Auto-submit if student_id is preloaded
    const preloadedId = '<?php echo htmlspecialchars($student_id ?? ""); ?>';
    if (preloadedId) {
        if (clearBtn) clearBtn.classList.remove('hidden');
        loadReport(preloadedId);
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
