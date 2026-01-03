<?php
/**
 * View: Student SDQ Assessment
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design
 */
ob_start();
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-brain text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-black text-white">แบบประเมิน SDQ</h1>
                        <p class="text-purple-200 font-bold">ภาคเรียนที่ <?= htmlspecialchars($term) ?>/<?= htmlspecialchars($pee) ?></p>
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

    <!-- SDQ Info -->
    <div class="glass-effect rounded-2xl p-4 border border-purple-200 dark:border-purple-800 bg-purple-50 dark:bg-purple-900/20">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-purple-500"></i>
            </div>
            <div>
                <h4 class="font-bold text-purple-700 dark:text-purple-400">Strengths and Difficulties Questionnaire</h4>
                <p class="text-sm text-purple-600 dark:text-purple-300 mt-1">
                    แบบคัดกรอง SDQ เป็นเครื่องมือในการประเมินจุดแข็งและจุดอ่อนด้านพฤติกรรม อารมณ์ และความสัมพันธ์ทางสังคม
                </p>
            </div>
        </div>
    </div>

    <!-- SDQ Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        
        <!-- SDQ Self -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl hover:shadow-2xl transition-all">
            <div class="bg-gradient-to-r <?= $selfSaved ? 'from-emerald-500 to-green-600' : 'from-slate-400 to-slate-500' ?> p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-user-check text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white">ประเมินตนเอง</h3>
                            <p class="text-[10px] font-bold text-white/80 uppercase tracking-widest">STUDENT SELF</p>
                        </div>
                    </div>
                    <i class="fas <?= $selfSaved ? 'fa-check-circle' : 'fa-clock' ?> text-2xl text-white/80"></i>
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex items-center justify-center mb-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold <?= $selfSaved ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' ?>">
                        <i class="fas <?= $selfSaved ? 'fa-check-circle' : 'fa-clock' ?>"></i>
                        <?= $selfSaved ? 'บันทึกแล้ว' : 'ยังไม่ได้บันทึก' ?>
                    </span>
                </div>
                
                <?php if ($selfSaved): ?>
                <div class="flex gap-2">
                    <button onclick="loadSDQUnified('self', 'view', 'SDQ นักเรียนประเมินตนเอง')" 
                            class="flex-1 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-eye"></i>
                        <span>ดู</span>
                    </button>
                    <button onclick="loadSDQInterpret('self', 'แปลผล SDQ นักเรียน')" 
                            class="flex-1 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-chart-bar"></i>
                        <span>แปลผล</span>
                    </button>
                </div>
                <?php else: ?>
                <button onclick="loadSDQUnified('self', 'add', 'บันทึก SDQ นักเรียนประเมินตนเอง')" 
                        class="w-full py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>ทำแบบประเมิน</span>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- SDQ Parent -->
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl hover:shadow-2xl transition-all">
            <div class="bg-gradient-to-r <?= $parentSaved ? 'from-emerald-500 to-green-600' : 'from-slate-400 to-slate-500' ?> p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-user-friends text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white">ผู้ปกครองประเมิน</h3>
                            <p class="text-[10px] font-bold text-white/80 uppercase tracking-widest">PARENT ASSESSMENT</p>
                        </div>
                    </div>
                    <i class="fas <?= $parentSaved ? 'fa-check-circle' : 'fa-clock' ?> text-2xl text-white/80"></i>
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex items-center justify-center mb-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold <?= $parentSaved ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' ?>">
                        <i class="fas <?= $parentSaved ? 'fa-check-circle' : 'fa-clock' ?>"></i>
                        <?= $parentSaved ? 'บันทึกแล้ว' : 'ยังไม่ได้บันทึก' ?>
                    </span>
                </div>
                
                <?php if ($parentSaved): ?>
                <div class="flex gap-2">
                    <button onclick="loadSDQUnified('parent', 'view', 'SDQ ผู้ปกครองประเมิน')" 
                            class="flex-1 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-eye"></i>
                        <span>ดู</span>
                    </button>
                    <button onclick="loadSDQInterpret('parent', 'แปลผล SDQ ผู้ปกครอง')" 
                            class="flex-1 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-chart-bar"></i>
                        <span>แปลผล</span>
                    </button>
                </div>
                <?php else: ?>
                <button onclick="loadSDQUnified('parent', 'add', 'บันทึก SDQ ผู้ปกครองประเมิน')" 
                        class="w-full py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>ทำแบบประเมิน</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SDQ Categories Info -->
    <div class="glass-effect rounded-[2rem] p-5 md:p-6 border border-white/50 shadow-xl">
        <h3 class="font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-list-alt text-purple-500"></i>
            หมวดการประเมิน SDQ
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                <div class="w-8 h-8 bg-red-500 text-white rounded-lg flex items-center justify-center text-sm font-bold mb-2">1</div>
                <h4 class="font-bold text-red-700 dark:text-red-400 text-sm">ปัญหาอารมณ์</h4>
                <p class="text-xs text-red-600 dark:text-red-300 mt-1">Emotional Problems</p>
            </div>
            <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl border border-orange-200 dark:border-orange-800">
                <div class="w-8 h-8 bg-orange-500 text-white rounded-lg flex items-center justify-center text-sm font-bold mb-2">2</div>
                <h4 class="font-bold text-orange-700 dark:text-orange-400 text-sm">ปัญหาพฤติกรรม</h4>
                <p class="text-xs text-orange-600 dark:text-orange-300 mt-1">Conduct Problems</p>
            </div>
            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                <div class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center text-sm font-bold mb-2">3</div>
                <h4 class="font-bold text-amber-700 dark:text-amber-400 text-sm">สมาธิสั้น/ซน</h4>
                <p class="text-xs text-amber-600 dark:text-amber-300 mt-1">Hyperactivity</p>
            </div>
            <div class="p-3 bg-sky-50 dark:bg-sky-900/20 rounded-xl border border-sky-200 dark:border-sky-800">
                <div class="w-8 h-8 bg-sky-500 text-white rounded-lg flex items-center justify-center text-sm font-bold mb-2">4</div>
                <h4 class="font-bold text-sky-700 dark:text-sky-400 text-sm">ปัญหาเพื่อน</h4>
                <p class="text-xs text-sky-600 dark:text-sky-300 mt-1">Peer Problems</p>
            </div>
            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
                <div class="w-8 h-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-sm font-bold mb-2">5</div>
                <h4 class="font-bold text-emerald-700 dark:text-emerald-400 text-sm">สัมพันธภาพ</h4>
                <p class="text-xs text-emerald-600 dark:text-emerald-300 mt-1">Prosocial Behavior</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="sdqModal" class="fixed inset-0 z-[60] hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-5 flex items-center justify-between flex-shrink-0">
                <h3 class="text-xl font-black text-white flex items-center gap-2" id="modalTitle">
                    <i class="fas fa-brain"></i> แบบประเมิน SDQ
                </h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1" id="modalBody">
                <div class="text-center py-8">
                    <div class="w-12 h-12 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-slate-500">กำลังโหลด...</p>
                </div>
            </div>
            
            <div id="modalFooter" class="p-5 border-t border-slate-200 dark:border-slate-700 flex gap-3 flex-shrink-0">
                <button onclick="closeModal()" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition">
                    <i class="fas fa-times mr-2"></i>ปิด
                </button>
                <button onclick="saveSDQ()" id="saveSDQBtn" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition hidden">
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
let currentType = '';

function openModal() {
    document.getElementById('sdqModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('sdqModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function loadSDQForm(formFile, mode, title) {
    currentMode = mode;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-brain"></i> ' + title;
    document.getElementById('saveSDQBtn').classList.toggle('hidden', mode === 'view');
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-8"><div class="w-12 h-12 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">กำลังโหลด...</p></div>';
    openModal();
    
    $.ajax({
        url: 'template_form/' + formFile,
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

function loadSDQUnified(type, mode, title) {
    currentMode = mode;
    currentType = type;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-brain"></i> ' + title;
    document.getElementById('saveSDQBtn').classList.toggle('hidden', mode === 'view');
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-8"><div class="w-12 h-12 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">กำลังโหลด...</p></div>';
    openModal();
    
    $.ajax({
        url: 'template_form/sdq_form.php',
        method: 'GET',
        data: { pee: pee, term: term, stuId: stuId, type: type, mode: mode },
        success: function(html) {
            document.getElementById('modalBody').innerHTML = html;
        },
        error: function() {
            document.getElementById('modalBody').innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>เกิดข้อผิดพลาดในการโหลด</p></div>';
        }
    });
}

function loadSDQInterpret(type, title) {
    currentType = type;
    currentMode = 'view';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-chart-bar"></i> ' + title;
    document.getElementById('saveSDQBtn').classList.add('hidden');
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-8"><div class="w-12 h-12 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">กำลังโหลดผลการประเมิน...</p></div>';
    openModal();
    
    $.ajax({
        url: 'template_form/sdq_interpret.php',
        method: 'GET',
        data: { pee: pee, term: term, stuId: stuId, type: type },
        success: function(html) {
            document.getElementById('modalBody').innerHTML = html;
        },
        error: function() {
            document.getElementById('modalBody').innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>เกิดข้อผิดพลาดในการโหลด</p></div>';
        }
    });
}

function saveSDQ() {
    const form = document.getElementById('sdqForm');
    if (!form) return;
    
    // Validate all 25 questions
    let missing = [];
    for (let i = 1; i <= 25; i++) {
        if (!form.querySelector('input[name="sdq' + i + '"]:checked') && 
            !form.querySelector('input[name="q' + i + '"]:checked')) {
            missing.push(i);
        }
    }
    
    if (missing.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'กรอกข้อมูลไม่ครบ',
            html: 'กรุณากรอกข้อ <b>' + missing.join(', ') + '</b>',
            confirmButtonColor: '#8b5cf6'
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
        url: 'api/save_sdq_data.php',
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
