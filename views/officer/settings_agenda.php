<?php
/**
 * View: Meeting Agenda Settings (Officer)
 * Premium UI with Tailwind CSS & Glassmorphism
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
    .form-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>

<div class="max-w-[1200px] mx-auto py-8 px-4 sm:px-6 lg:px-8 animate-fadeIn">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="glass-effect rounded-[2rem] p-6 md:p-8 relative overflow-hidden shadow-xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-violet-500/10 to-fuchsia-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-4 md:gap-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-violet-500 to-fuchsia-600 rounded-2xl flex items-center justify-center text-white shadow-lg transform rotate-3 hover:rotate-0 transition-transform">
                        <i class="fas fa-list-check text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white leading-tight">
                            ตั้งค่า <span class="text-violet-600 italic">วาระการประชุมผู้ปกครอง</span>
                        </h1>
                        <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 font-bold mt-1">
                            กำหนดหัวข้อระเบียบวาระสำหรับการประชุมผู้ปกครองนักเรียนในแต่ละรอบการประชุม
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Round Selector -->
    <div class="form-card glass-effect rounded-2xl p-6 border border-slate-100 dark:border-slate-800 shadow-md mb-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400 rounded-xl flex items-center justify-center font-black">
                    <i class="fas fa-calendar-days"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">เลือกรอบการประชุมผู้ปกครอง</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">รอบที่กำลังตั้งค่า: ภาคเรียนที่ <?= htmlspecialchars($term) ?>/<?= htmlspecialchars($pee) ?></p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Selector Dropdown -->
                <div class="flex-1 md:flex-initial">
                    <select onchange="window.location.href='settings_agenda.php?term='+this.value.split('_')[0]+'&pee='+this.value.split('_')[1]" class="w-full px-4 py-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:border-violet-500 outline-none font-bold text-slate-700 dark:text-white">
                        <?php foreach ($configuredRounds as $round): ?>
                            <option value="<?= $round['key'] ?>" <?= ($round['term'] == $term && $round['pee'] == $pee) ? 'selected' : '' ?>>
                                ภาคเรียนที่ <?= htmlspecialchars($round['term']) ?>/<?= htmlspecialchars($round['pee']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Create New Round Button -->
                <button type="button" onclick="createNewRoundModal()" class="px-4 py-2 bg-gradient-to-r from-violet-500 to-fuchsia-600 hover:scale-105 active:scale-95 text-white font-bold text-sm rounded-xl transition-all shadow-md">
                    <i class="fas fa-plus mr-1"></i> เพิ่มรอบการประชุมใหม่
                </button>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form id="agendaSettingsForm" class="space-y-6">
        
        <!-- Committee & Report Layout Settings Card -->
        <div class="form-card glass-effect rounded-2xl p-6 border border-slate-100 dark:border-slate-800 shadow-md">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-fuchsia-600 text-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-cog"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">ตั้งค่าการแสดงผลและข้อมูลรอบการประชุม</h3>
            </div>
            <div class="space-y-4 pl-11">
                <!-- Round Metadata Hidden Fields -->
                <input type="hidden" name="term" value="<?= htmlspecialchars($term) ?>">
                <input type="hidden" name="pee" value="<?= htmlspecialchars($pee) ?>">

                <!-- Meeting Date Config -->
                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        วันที่จัดประชุม (เช่น วันเสาร์ ที่ 13 เดือน มิถุนายน พ.ศ. 2569)
                    </label>
                    <input type="text" name="meeting_date" required value="<?= htmlspecialchars($agendaConfig['meeting_date'] ?? '') ?>" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 outline-none transition-all font-semibold text-slate-700 dark:text-white" placeholder="พิมพ์วันเดือนปีที่จัดประชุม...">
                    <p class="text-xs text-slate-400 mt-1 font-medium">ครูที่ปรึกษาจะได้รับข้อมูลนี้เป็นค่าเริ่มต้นสำหรับแสดงในหน้าพิมพ์รายงาน</p>
                </div>

                <div class="h-px bg-slate-100 dark:bg-slate-800 my-4"></div>

                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" name="show_committee_election" value="1" <?= ($agendaConfig['show_committee_election'] ?? true) ? 'checked' : '' ?> class="w-5 h-5 text-violet-600 bg-slate-100 border-slate-300 rounded focus:ring-violet-500 cursor-pointer">
                    <span class="ml-3 text-sm font-semibold text-slate-700 dark:text-slate-300 group-hover:text-violet-600 transition-colors">
                        แสดงหัวข้อการคัดเลือกคณะกรรมการเครือข่ายผู้ปกครอง (ในระเบียบวาระที่ 4.1)
                    </span>
                </label>
                <p class="text-xs text-slate-400 pl-8 font-medium">หากเลือกตัวเลือกนี้ เล่มรายงานจะพิมพ์ตารางตำแหน่ง ประธาน กรรมการ เลขานุการ ในระเบียบวาระที่ 4.1</p>
                
                <label class="flex items-center cursor-pointer group mt-4">
                    <input type="checkbox" name="show_committee_page" value="1" <?= ($agendaConfig['show_committee_page'] ?? true) ? 'checked' : '' ?> class="w-5 h-5 text-violet-600 bg-slate-100 border-slate-300 rounded focus:ring-violet-500 cursor-pointer">
                    <span class="ml-3 text-sm font-semibold text-slate-700 dark:text-slate-300 group-hover:text-violet-600 transition-colors">
                        แสดงหน้าทำเนียบรายชื่อคณะกรรมการเครือข่ายผู้ปกครองในชั้นเรียน (หน้า 4 ในเล่มรายงาน)
                    </span>
                </label>
                <p class="text-xs text-slate-400 pl-8 font-medium">หากเลือกตัวเลือกนี้ เล่มรายงานจะพิมพ์หน้า 4 (บัญชีรายชื่อคณะกรรมการพร้อมรูปถ่าย) และครูจะสามารถบันทึกข้อมูลในแท็บเครือข่ายผู้ปกครองได้</p>
            </div>
        </div>

        <?php for ($agendaNum = 1; $agendaNum <= 5; $agendaNum++): 
            $agendaData = $agendaConfig['agendas'][$agendaNum] ?? ['title' => 'ระเบียบวาระที่ ' . $agendaNum, 'subs' => []];
        ?>
            <!-- Agenda <?= $agendaNum ?> -->
            <div class="form-card glass-effect rounded-2xl p-6 border border-slate-100 dark:border-slate-800 shadow-md">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400 rounded-lg flex items-center justify-center font-black"><?= $agendaNum ?></div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($agendaData['title']) ?></h3>
                            <input type="hidden" name="agenda_titles[<?= $agendaNum ?>]" value="<?= htmlspecialchars($agendaData['title']) ?>">
                        </div>
                    </div>
                    <button type="button" onclick="addSubAgenda(<?= $agendaNum ?>)" class="px-3 py-1.5 bg-violet-500 hover:bg-violet-600 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1">
                        <i class="fas fa-plus"></i> เพิ่มหัวข้อย่อย
                    </button>
                </div>
                <div id="agenda-container-<?= $agendaNum ?>" class="space-y-4 pl-11">
                    <?php if (!empty($agendaData['subs'])): ?>
                        <?php foreach ($agendaData['subs'] as $idx => $subTitle): ?>
                            <div class="flex items-start gap-3 agenda-item">
                                <div class="flex-1">
                                    <textarea name="agenda[<?= $agendaNum ?>][]" rows="2" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 outline-none transition-all font-semibold text-slate-700 dark:text-white"><?= htmlspecialchars($subTitle) ?></textarea>
                                </div>
                                <button type="button" onclick="removeSubAgenda(this)" class="p-2.5 bg-rose-100 hover:bg-rose-200 text-rose-600 rounded-xl transition-colors self-center">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-slate-400 text-sm italic py-2 no-item-placeholder">ยังไม่มีหัวข้อย่อยในวาระนี้</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endfor; ?>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-600 text-white font-black rounded-xl shadow-lg hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> บันทึกการตั้งค่าวาระประชุม
            </button>
        </div>
    </form>
</div>

<script>
function createNewRoundModal() {
    Swal.fire({
        title: 'เพิ่มรอบการประชุมใหม่',
        html: `
            <div class="space-y-4 text-left">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">ภาคเรียน</label>
                    <select id="new_round_term" class="swal2-select w-full m-0 px-4 py-2 border rounded-xl" style="display: block; width: 100%; box-sizing: border-box;">
                        <option value="1">ภาคเรียนที่ 1</option>
                        <option value="2">ภาคเรียนที่ 2</option>
                    </select>
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-bold text-slate-700 mb-1">ปีการศึกษา (พ.ศ.)</label>
                    <input type="number" id="new_round_pee" class="swal2-input w-full m-0 px-4 py-2 border rounded-xl" style="width: 100%; box-sizing: border-box;" placeholder="เช่น 2569" value="<?= date('Y') + 543 ?>">
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'ตั้งค่าและสร้างรอบนี้',
        cancelButtonText: 'ยกเลิก',
        preConfirm: () => {
            const term = Swal.getPopup().querySelector('#new_round_term').value;
            const pee = Swal.getPopup().querySelector('#new_round_pee').value;
            if (!term || !pee) {
                Swal.showValidationMessage(`กรุณากรอกข้อมูลให้ครบถ้วน`);
            }
            return { term: term, pee: pee };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `settings_agenda.php?term=${result.value.term}&pee=${result.value.pee}`;
        }
    });
}

function addSubAgenda(agendaNum) {
    const container = $('#agenda-container-' + agendaNum);
    // Remove placeholder if exists
    container.find('.no-item-placeholder').remove();

    const html = `
        <div class="flex items-start gap-3 agenda-item" style="display: none;">
            <div class="flex-1">
                <textarea name="agenda[${agendaNum}][]" rows="2" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 outline-none transition-all font-semibold text-slate-700 dark:text-white" placeholder="กรอกรายละเอียดหัวข้อย่อย..."></textarea>
            </div>
            <button type="button" onclick="removeSubAgenda(this)" class="p-2.5 bg-rose-100 hover:bg-rose-200 text-rose-600 rounded-xl transition-colors self-center">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    const $item = $(html);
    container.append($item);
    $item.slideDown(200);
}

function removeSubAgenda(btn) {
    const item = $(btn).closest('.agenda-item');
    const container = item.parent();
    item.slideUp(200, function() {
        item.remove();
        if (container.children('.agenda-item').length === 0) {
            container.html('<p class="text-slate-400 text-sm italic py-2 no-item-placeholder">ยังไม่มีหัวข้อย่อยในวาระนี้</p>');
        }
    });
}

$(document).ready(function() {
    $('#agendaSettingsForm').on('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'api/save_agenda_settings.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload to reflect settings properly
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                });
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
