<?php
/**
 * View: Student EQ Assessment
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design
 */
ob_start();
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-rose-500 via-pink-500 to-fuchsia-600 shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-heart text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-black text-white">แบบประเมิน EQ</h1>
                        <p class="text-pink-200 font-bold">ภาคเรียนที่ <?= htmlspecialchars($term) ?>/<?= htmlspecialchars($pee) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl text-white font-bold text-sm">
                        <i class="fas fa-user mr-1"></i> <?= htmlspecialchars($student['Stu_pre'] . $student['Stu_name']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- EQ Info -->
    <div class="glass-effect rounded-2xl p-4 border border-pink-200 dark:border-pink-800 bg-pink-50 dark:bg-pink-900/20">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-pink-500"></i>
            </div>
            <div>
                <h4 class="font-bold text-pink-700 dark:text-pink-400">Emotional Quotient (EQ)</h4>
                <p class="text-sm text-pink-600 dark:text-pink-300 mt-1">
                    แบบประเมินความฉลาดทางอารมณ์ เพื่อวัดความสามารถในการรับรู้ เข้าใจ และจัดการอารมณ์ของตนเองและผู้อื่น
                </p>
            </div>
        </div>
    </div>

    <!-- EQ Card -->
    <div class="max-w-md mx-auto">
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl hover:shadow-2xl transition-all">
            <div class="bg-gradient-to-r <?= $eqSaved ? 'from-emerald-500 to-green-600' : 'from-slate-400 to-slate-500' ?> p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-smile-beam text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white">ประเมินตนเอง</h3>
                            <p class="text-[10px] font-bold text-white/80 uppercase tracking-widest">SELF ASSESSMENT</p>
                        </div>
                    </div>
                    <i class="fas <?= $eqSaved ? 'fa-check-circle' : 'fa-clock' ?> text-2xl text-white/80"></i>
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex items-center justify-center mb-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold <?= $eqSaved ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' ?>">
                        <i class="fas <?= $eqSaved ? 'fa-check-circle' : 'fa-clock' ?>"></i>
                        <?= $eqSaved ? 'บันทึกแล้ว' : 'ยังไม่ได้บันทึก' ?>
                    </span>
                </div>
                
                <?php if ($eqSaved): ?>
                <div class="flex gap-2">
                    <button onclick="loadEQForm('view', 'EQ นักเรียนประเมินตนเอง')" 
                            class="flex-1 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-eye"></i>
                        <span>ดู</span>
                    </button>
                    <button onclick="loadEQInterpret('แปลผล EQ')" 
                            class="flex-1 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-chart-bar"></i>
                        <span>แปลผล</span>
                    </button>
                </div>
                <?php else: ?>
                <button onclick="loadEQForm('add', 'บันทึก EQ นักเรียนประเมินตนเอง')" 
                        class="w-full py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>ทำแบบประเมิน</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- EQ Categories Info -->
    <div class="glass-effect rounded-[2rem] p-5 md:p-6 border border-white/50 shadow-xl">
        <h3 class="font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-list-alt text-pink-500"></i>
            องค์ประกอบ EQ
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-4 bg-rose-50 dark:bg-rose-900/20 rounded-xl border border-rose-200 dark:border-rose-800">
                <div class="w-10 h-10 bg-rose-500 text-white rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-smile"></i>
                </div>
                <h4 class="font-bold text-rose-700 dark:text-rose-400 text-sm">ดี</h4>
                <p class="text-xs text-rose-600 dark:text-rose-300 mt-1">ความสุข ความพอใจในชีวิต</p>
            </div>
            <div class="p-4 bg-fuchsia-50 dark:bg-fuchsia-900/20 rounded-xl border border-fuchsia-200 dark:border-fuchsia-800">
                <div class="w-10 h-10 bg-fuchsia-500 text-white rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-brain"></i>
                </div>
                <h4 class="font-bold text-fuchsia-700 dark:text-fuchsia-400 text-sm">เก่ง</h4>
                <p class="text-xs text-fuchsia-600 dark:text-fuchsia-300 mt-1">ความสามารถในการจัดการปัญหา</p>
            </div>
            <div class="p-4 bg-pink-50 dark:bg-pink-900/20 rounded-xl border border-pink-200 dark:border-pink-800">
                <div class="w-10 h-10 bg-pink-500 text-white rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="font-bold text-pink-700 dark:text-pink-400 text-sm">สุข</h4>
                <p class="text-xs text-pink-600 dark:text-pink-300 mt-1">ความสัมพันธ์กับผู้อื่น</p>
            </div>
        </div>
    </div>

    <!-- Question Count -->
    <div class="glass-effect rounded-2xl p-4 border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-question-circle text-pink-500"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-700 dark:text-slate-300">จำนวนข้อคำถาม</h4>
                    <p class="text-sm text-slate-500">แบบประเมินมีทั้งหมด 52 ข้อ</p>
                </div>
            </div>
            <span class="text-3xl font-black text-pink-500">52</span>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="eqModal" class="fixed inset-0 z-[60] hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="bg-gradient-to-r from-rose-500 to-pink-600 p-5 flex items-center justify-between flex-shrink-0">
                <h3 class="text-xl font-black text-white flex items-center gap-2" id="modalTitle">
                    <i class="fas fa-heart"></i> แบบประเมิน EQ
                </h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1" id="modalBody">
                <div class="text-center py-8">
                    <div class="w-12 h-12 border-4 border-pink-500/30 border-t-pink-500 rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-slate-500">กำลังโหลด...</p>
                </div>
            </div>
            
            <div id="modalFooter" class="p-5 border-t border-slate-200 dark:border-slate-700 flex gap-3 flex-shrink-0">
                <button onclick="closeModal()" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition">
                    <i class="fas fa-times mr-2"></i>ปิด
                </button>
                <button onclick="saveEQ()" id="saveEQBtn" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition hidden">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const pee = <?= json_encode($pee) ?>;
const term = <?= json_encode($term) ?>;
const stuId = <?= json_encode($student_id) ?>;
let currentMode = '';

function openModal() {
    document.getElementById('eqModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('eqModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function loadEQForm(mode, title) {
    currentMode = mode;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-heart"></i> ' + title;
    document.getElementById('saveEQBtn').classList.toggle('hidden', mode === 'view');
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-8"><div class="w-12 h-12 border-4 border-pink-500/30 border-t-pink-500 rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">กำลังโหลด...</p></div>';
    openModal();
    
    $.ajax({
        url: 'template_form/eq_form.php',
        method: 'GET',
        data: { pee: pee, term: term, stuId: stuId, mode: mode },
        success: function(html) {
            document.getElementById('modalBody').innerHTML = html;
        },
        error: function() {
            document.getElementById('modalBody').innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>เกิดข้อผิดพลาดในการโหลด</p></div>';
        }
    });
}

function loadEQInterpret(title) {
    currentMode = 'view';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-chart-bar"></i> ' + title;
    document.getElementById('saveEQBtn').classList.add('hidden');
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-8"><div class="w-12 h-12 border-4 border-pink-500/30 border-t-pink-500 rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">กำลังโหลดผลการประเมิน...</p></div>';
    openModal();
    
    $.ajax({
        url: 'template_form/eq_interpret.php',
        method: 'GET',
        data: { pee: pee, term: term, stuId: stuId },
        success: function(html) {
            document.getElementById('modalBody').innerHTML = html;
        },
        error: function() {
            document.getElementById('modalBody').innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>เกิดข้อผิดพลาดในการโหลด</p></div>';
        }
    });
}

function saveEQ() {
    const form = document.getElementById('eqForm');
    if (!form) return;
    
    // Validate all 52 questions
    let missing = [];
    for (let i = 1; i <= 52; i++) {
        if (!form.querySelector('input[name="eq' + i + '"]:checked')) {
            missing.push(i);
        }
    }
    
    if (missing.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'กรอกข้อมูลไม่ครบ',
            html: 'กรุณากรอกข้อ <b>' + missing.join(', ') + '</b>',
            confirmButtonColor: '#ec4899'
        });
        return;
    }
    
    Swal.fire({
        title: 'กำลังบันทึก...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    const formData = new FormData(form);
    
    $.ajax({
        url: 'api/save_eq_data.php',
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
