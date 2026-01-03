<?php
/**
 * View: Student Visit Home
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design
 */
ob_start();
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-orange-500 via-amber-500 to-yellow-500 shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-home text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-black text-white">บันทึกการเยี่ยมบ้าน</h1>
                        <p class="text-amber-100 font-bold">ปีการศึกษา <?= htmlspecialchars($pee) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl text-white font-bold text-sm">
                        <i class="fas fa-user mr-1"></i> <?= htmlspecialchars($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Visit Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <?php for ($i = 1; $i <= 2; $i++): 
            $visit = $visits[$i] ?? null;
            $isSaved = $visit !== null;
            
            if ($isSaved) {
                $statusColor = 'emerald';
                $statusText = 'บันทึกแล้ว';
                $statusIcon = 'fa-check-circle';
                $gradient = 'from-emerald-500 to-green-600';
            } else {
                $statusColor = 'slate';
                $statusText = 'ยังไม่ได้บันทึก';
                $statusIcon = 'fa-clock';
                $gradient = 'from-slate-400 to-slate-500';
            }
        ?>
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl hover:shadow-2xl transition-all">
            <!-- Card Header -->
            <div class="bg-gradient-to-r <?= $gradient ?> p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <span class="text-2xl font-black text-white"><?= $i ?></span>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white">ครั้งที่ <?= $i ?></h3>
                            <p class="text-[10px] font-bold text-white/80 uppercase tracking-widest">ภาคเรียนที่ <?= $i ?></p>
                        </div>
                    </div>
                    <i class="fas <?= $statusIcon ?> text-2xl text-white/80"></i>
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="p-5">
                <!-- Status Badge -->
                <div class="flex items-center justify-center mb-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-<?= $statusColor ?>-100 dark:bg-<?= $statusColor ?>-900/30 text-<?= $statusColor ?>-600 rounded-full font-bold">
                        <i class="fas <?= $statusIcon ?>"></i>
                        <?= $statusText ?>
                    </span>
                </div>
                
                <?php if ($isSaved): ?>
                <!-- Visit Info -->
                <div class="flex items-center justify-center text-emerald-600 mb-4">
                    <i class="fas fa-check-double mr-2"></i>
                    <span class="font-bold text-sm">ข้อมูลครบถ้วน</span>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button onclick="viewVisit(<?= $i ?>)" class="flex-1 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-eye"></i>
                        <span>ดูข้อมูล</span>
                    </button>
                    <button onclick="editVisit(<?= $i ?>)" class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-edit"></i>
                        <span>แก้ไข</span>
                    </button>
                </div>
                <?php else: ?>
                <!-- Add Button -->
                <button onclick="addVisit(<?= $i ?>)" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>บันทึกข้อมูล</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endfor; ?>
    </div>

    <!-- Info Note -->
    <div class="glass-effect rounded-2xl p-4 border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-amber-500"></i>
            </div>
            <div>
                <h4 class="font-bold text-amber-700 dark:text-amber-400">คำแนะนำ</h4>
                <p class="text-sm text-amber-600 dark:text-amber-300 mt-1">
                    การบันทึกการเยี่ยมบ้านจะต้องกรอกข้อมูลทั้ง 18 ข้อให้ครบถ้วน ก่อนทำการบันทึก
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="visitModal" class="fixed inset-0 z-[60] hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-5 flex items-center justify-between flex-shrink-0">
                <h3 class="text-xl font-black text-white flex items-center gap-2" id="modalTitle">
                    <i class="fas fa-home"></i> บันทึกการเยี่ยมบ้าน
                </h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto flex-1" id="modalBody">
                <div class="text-center py-8">
                    <div class="w-12 h-12 border-4 border-amber-500/30 border-t-amber-500 rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-slate-500">กำลังโหลด...</p>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div id="modalFooter" class="p-5 border-t border-slate-200 dark:border-slate-700 flex gap-3 flex-shrink-0">
                <button onclick="closeModal()" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition">
                    <i class="fas fa-times mr-2"></i>ปิด
                </button>
                <button onclick="saveVisit()" id="saveVisitBtn" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition hidden">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const pee = <?= json_encode($pee) ?>;
const stuId = <?= json_encode($student_id) ?>;
let currentMode = '';

function openModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('visitModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function loadForm(visitNo, mode) {
    currentMode = mode;
    
    const titles = {
        'view': '<i class="fas fa-eye"></i> ดูข้อมูลการเยี่ยมบ้าน ครั้งที่ ' + visitNo,
        'edit': '<i class="fas fa-edit"></i> แก้ไขข้อมูลการเยี่ยมบ้าน ครั้งที่ ' + visitNo,
        'add': '<i class="fas fa-plus-circle"></i> บันทึกข้อมูลการเยี่ยมบ้าน ครั้งที่ ' + visitNo
    };
    
    document.getElementById('modalTitle').innerHTML = titles[mode];
    document.getElementById('saveVisitBtn').classList.toggle('hidden', mode === 'view');
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-8"><div class="w-12 h-12 border-4 border-amber-500/30 border-t-amber-500 rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">กำลังโหลด...</p></div>';
    openModal();
    
    $.ajax({
        url: 'template_form/visit_form.php',
        method: 'GET',
        data: { term: visitNo, pee: pee, stuId: stuId, mode: mode },
        success: function(html) {
            document.getElementById('modalBody').innerHTML = html;
        },
        error: function() {
            document.getElementById('modalBody').innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>เกิดข้อผิดพลาดในการโหลดข้อมูล</p></div>';
        }
    });
}

function viewVisit(visitNo) { loadForm(visitNo, 'view'); }
function editVisit(visitNo) { loadForm(visitNo, 'edit'); }
function addVisit(visitNo) { loadForm(visitNo, 'add'); }

function saveVisit() {
    const form = document.getElementById('addVisitForm');
    if (!form) return;
    
    // Validate all 18 questions
    let missing = [];
    for (let i = 1; i <= 18; i++) {
        if (!form.querySelector('input[name="vh' + i + '"]:checked')) {
            missing.push(i);
        }
    }
    
    if (missing.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'กรอกข้อมูลไม่ครบ',
            html: 'กรุณากรอกข้อ <b>' + missing.join(', ') + '</b>',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'กำลังบันทึก...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    const formData = new FormData(form);
    const apiUrl = currentMode === 'edit' ? 'api/update_visit_data.php' : 'api/save_visit_data.php';
    
    $.ajax({
        url: apiUrl,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            let res = typeof response === 'string' ? JSON.parse(response) : response;
            if (res.success) {
                Swal.fire({ 
                    icon: 'success', 
                    title: 'สำเร็จ', 
                    text: res.message || 'บันทึกข้อมูลเรียบร้อยแล้ว', 
                    confirmButtonColor: '#10b981' 
                }).then(() => location.reload());
            } else {
                Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
            }
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/student_app.php';
?>
