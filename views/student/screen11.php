<?php
/**
 * View: Student Screen11 Assessment (11 Aspects Screening)
 * Modern UI with Tailwind CSS & Glassmorphism
 * Mobile-first responsive design
 */
ob_start();

// 11 aspects categories
$aspects = [
    ['id' => 1, 'name' => 'ด้านความสามารถพิเศษ', 'icon' => '🌟', 'color' => 'amber'],
    ['id' => 2, 'name' => 'ด้านการเรียน', 'icon' => '📚', 'color' => 'blue'],
    ['id' => 3, 'name' => 'ด้านสุขภาพ', 'icon' => '🏥', 'color' => 'emerald'],
    ['id' => 4, 'name' => 'ด้านเศรษฐกิจ', 'icon' => '💰', 'color' => 'yellow'],
    ['id' => 5, 'name' => 'ด้านสวัสดิภาพและความปลอดภัย', 'icon' => '🛡️', 'color' => 'indigo'],
    ['id' => 6, 'name' => 'ด้านพฤติกรรมการใช้สารเสพติด', 'icon' => '🚭', 'color' => 'red'],
    ['id' => 7, 'name' => 'ด้านพฤติกรรมการใช้ความรุนแรง', 'icon' => '⚠️', 'color' => 'orange'],
    ['id' => 8, 'name' => 'ด้านพฤติกรรมทางเพศ', 'icon' => '💕', 'color' => 'pink'],
    ['id' => 9, 'name' => 'ด้านการติดเกม', 'icon' => '🎮', 'color' => 'purple'],
    ['id' => 10, 'name' => 'ด้านความต้องการพิเศษ', 'icon' => '♿', 'color' => 'teal'],
    ['id' => 11, 'name' => 'ด้านการใช้สื่ออิเล็กทรอนิกส์', 'icon' => '📱', 'color' => 'cyan'],
];
?>

<div class="space-y-6 md:space-y-8">
    
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 p-6 md:p-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-black text-white">แบบคัดกรอง 11 ด้าน</h1>
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

    <!-- Info -->
    <div class="glass-effect rounded-2xl p-4 border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/20">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-indigo-500"></i>
            </div>
            <div>
                <h4 class="font-bold text-indigo-700 dark:text-indigo-400">แบบคัดกรองนักเรียนรายบุคคล</h4>
                <p class="text-sm text-indigo-600 dark:text-indigo-300 mt-1">
                    ประเมินสภาพปัญหาและความต้องการของนักเรียน 11 ด้าน เพื่อการดูแลช่วยเหลืออย่างเหมาะสม
                </p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="max-w-lg mx-auto">
        <div class="glass-effect rounded-[2rem] overflow-hidden border border-white/50 shadow-xl hover:shadow-2xl transition-all">
            <div class="bg-gradient-to-r <?= $screen11Saved ? 'from-emerald-500 to-green-600' : 'from-slate-400 to-slate-500' ?> p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-clipboard-check text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white">ประเมินตนเอง</h3>
                            <p class="text-[10px] font-bold text-white/80 uppercase tracking-widest">11 ASPECTS SCREENING</p>
                        </div>
                    </div>
                    <i class="fas <?= $screen11Saved ? 'fa-check-circle' : 'fa-clock' ?> text-2xl text-white/80"></i>
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex items-center justify-center mb-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold <?= $screen11Saved ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' ?>">
                        <i class="fas <?= $screen11Saved ? 'fa-check-circle' : 'fa-clock' ?>"></i>
                        <?= $screen11Saved ? 'บันทึกแล้ว' : 'ยังไม่ได้บันทึก' ?>
                    </span>
                </div>
                
                <?php if ($screen11Saved): ?>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="loadScreen11Form('view', 'ดูแบบคัดกรอง 11 ด้าน')" 
                            class="py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-1">
                        <i class="fas fa-eye"></i>
                        <span>ดู</span>
                    </button>
                    <button onclick="loadScreen11Form('edit', 'แก้ไขแบบคัดกรอง 11 ด้าน')" 
                            class="py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-1">
                        <i class="fas fa-edit"></i>
                        <span>แก้ไข</span>
                    </button>
                    <button onclick="loadScreen11Interpret('แปลผลแบบคัดกรอง 11 ด้าน')" 
                            class="py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-1">
                        <i class="fas fa-chart-bar"></i>
                        <span>แปลผล</span>
                    </button>
                </div>
                <?php else: ?>
                <button onclick="loadScreen11Form('add', 'บันทึกแบบคัดกรอง 11 ด้าน')" 
                        class="w-full py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>ทำแบบคัดกรอง</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 11 Aspects Grid -->
    <div class="glass-effect rounded-[2rem] p-5 md:p-6 border border-white/50 shadow-xl">
        <h3 class="font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-th-list text-indigo-500"></i>
            11 ด้านที่คัดกรอง
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <?php foreach ($aspects as $aspect): 
                $colorMap = [
                    'amber' => 'bg-amber-50 border-amber-200 text-amber-700',
                    'blue' => 'bg-blue-50 border-blue-200 text-blue-700',
                    'emerald' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                    'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
                    'indigo' => 'bg-indigo-50 border-indigo-200 text-indigo-700',
                    'red' => 'bg-red-50 border-red-200 text-red-700',
                    'orange' => 'bg-orange-50 border-orange-200 text-orange-700',
                    'pink' => 'bg-pink-50 border-pink-200 text-pink-700',
                    'purple' => 'bg-purple-50 border-purple-200 text-purple-700',
                    'teal' => 'bg-teal-50 border-teal-200 text-teal-700',
                    'cyan' => 'bg-cyan-50 border-cyan-200 text-cyan-700',
                ];
                $colorClass = $colorMap[$aspect['color']] ?? 'bg-slate-50 border-slate-200 text-slate-700';
            ?>
            <div class="p-3 rounded-xl border <?= $colorClass ?> text-center">
                <span class="text-2xl block mb-1"><?= $aspect['icon'] ?></span>
                <span class="text-[10px] md:text-xs font-bold leading-tight block"><?= $aspect['name'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="glass-effect rounded-2xl p-4 border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
        <h5 class="font-bold text-slate-600 dark:text-slate-400 text-sm mb-3">สถานะการประเมิน</h5>
        <div class="flex flex-wrap gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> ปกติ
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> เสี่ยง
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-red-500"></span> มีปัญหา
            </span>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="screen11Modal" class="fixed inset-0 z-[60] hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-4xl bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-5 flex items-center justify-between flex-shrink-0">
                <h3 class="text-xl font-black text-white flex items-center gap-2" id="modalTitle">
                    <i class="fas fa-clipboard-list"></i> แบบคัดกรอง 11 ด้าน
                </h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1" id="modalBody">
                <div class="text-center py-8">
                    <div class="w-12 h-12 border-4 border-indigo-500/30 border-t-indigo-500 rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-slate-500">กำลังโหลด...</p>
                </div>
            </div>
            
            <div id="modalFooter" class="p-5 border-t border-slate-200 dark:border-slate-700 flex gap-3 flex-shrink-0">
                <button onclick="closeModal()" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition">
                    <i class="fas fa-times mr-2"></i>ปิด
                </button>
                <button onclick="saveScreen11()" id="saveScreen11Btn" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition hidden">
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
    document.getElementById('screen11Modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('screen11Modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function loadScreen11Form(mode, title) {
    currentMode = mode;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-clipboard-list"></i> ' + title;
    document.getElementById('saveScreen11Btn').classList.toggle('hidden', mode === 'view');
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-8"><div class="w-12 h-12 border-4 border-indigo-500/30 border-t-indigo-500 rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">กำลังโหลด...</p></div>';
    openModal();
    
    $.ajax({
        url: 'template_form/screen11_form.php',
        method: 'GET',
        data: { pee: pee, term: term, stuId: stuId, mode: mode },
        success: function(html) {
            $('#modalBody').html(html);
        },
        error: function() {
            $('#modalBody').html('<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>เกิดข้อผิดพลาดในการโหลด</p></div>');
        }
    });
}

function loadScreen11Interpret(title) {
    currentMode = 'view';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-chart-bar"></i> ' + title;
    document.getElementById('saveScreen11Btn').classList.add('hidden');
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-8"><div class="w-12 h-12 border-4 border-indigo-500/30 border-t-indigo-500 rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">กำลังโหลดผลการคัดกรอง...</p></div>';
    openModal();
    
    $.ajax({
        url: 'template_form/screen11_interpret.php',
        method: 'GET',
        data: { pee: pee, term: term, stuId: stuId },
        success: function(html) {
            $('#modalBody').html(html);
        },
        error: function() {
            $('#modalBody').html('<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>เกิดข้อผิดพลาดในการโหลด</p></div>');
        }
    });
}

function saveScreen11() {
    const form = document.getElementById('screen11Form');
    if (!form) return;
    
    Swal.fire({
        title: 'ยืนยันการบันทึก?',
        text: 'คุณต้องการบันทึกข้อมูลแบบคัดกรอง 11 ด้านใช่หรือไม่',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        confirmButtonColor: '#10b981',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กำลังบันทึก...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            const formData = new FormData(form);
            const apiUrl = currentMode === 'edit' ? 'api/update_screen11_data.php' : 'api/save_screen11_data.php';
            
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
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/student_app.php';
?>
