<?php
/**
 * Picture Meeting & Minutes Wizard View
 * Consolidated 3-Step Wizard for parent meetings (Minutes, Photos, and Committee).
 * Modern UI with Tailwind CSS & Mobile Responsive
 */
$pageTitle = $title ?? 'ระบบบันทึกการประชุมผู้ปกครอง';

ob_start();
?>

<!-- Custom Styles -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.85);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .image-card, .member-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .image-card:hover, .member-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.2);
    }
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
    }
    .tab-btn {
        transition: all 0.2s ease-in-out;
    }
    .tab-btn.active {
        border-bottom: 3px solid #7c3aed;
        color: #7c3aed;
    }
    /* Hide DataTables on mobile */
    @media (max-width: 767px) {
        #record_table_wrapper { display: none !important; }
    }
    @media (min-width: 768px) {
        #mobileCards { display: none !important; }
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden no-print">
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-violet-500/20 to-purple-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <span class="text-2xl md:text-3xl">📝</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">
                    บันทึกการประชุมผู้ปกครองในแต่ละเทอม
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm mt-1">
                    <i class="fas fa-users text-violet-500 mr-1"></i>
                    ม.<?= htmlspecialchars($class) ?>/<?= htmlspecialchars($room) ?>
                    <span class="mx-1">•</span>
                    <i class="far fa-calendar-alt text-violet-500 mr-1"></i>
                    ภาคเรียนที่ <?= htmlspecialchars($term) ?> ปีการศึกษา <?= htmlspecialchars($pee) ?>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-stretch sm:items-center">
                <!-- Round Selector -->
                <div class="relative">
                    <select onchange="window.location.href='picture_meeting.php?term='+this.value.split('_')[0]+'&pee='+this.value.split('_')[1]" class="w-full sm:w-auto px-4 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl focus:border-violet-500 outline-none font-bold text-slate-700 dark:text-white text-sm shadow-sm cursor-pointer">
                        <?php foreach ($configuredRounds as $round): ?>
                            <option value="<?= $round['key'] ?>" <?= ($round['term'] == $term && $round['pee'] == $pee) ? 'selected' : '' ?>>
                                ภาคเรียนที่ <?= htmlspecialchars($round['term']) ?>/<?= htmlspecialchars($round['pee']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Print Button -->
                <button onclick="window.open('print_meeting_report.php?term=<?= $term ?>&pee=<?= $pee ?>', '_blank')" class="btn-action inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg">
                    <i class="fas fa-print"></i>
                    <span>🖨️ พิมพ์เล่มรายงาน (A4)</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 3-Step Wizard Navigation Tabs -->
<div class="flex border-b border-slate-200 dark:border-slate-700 mb-6 no-print bg-white/50 backdrop-blur rounded-xl p-1 shadow-sm">
    <button onclick="switchTab('tab-minutes')" id="btn-tab-minutes" class="tab-btn active flex-1 text-center py-3 font-bold text-sm rounded-lg outline-none">
        📝 1. บันทึกการประชุม
    </button>
    <button onclick="switchTab('tab-photos')" id="btn-tab-photos" class="tab-btn flex-1 text-center py-3 font-bold text-sm text-slate-500 hover:text-slate-700 outline-none">
        📷 2. รูปภาพการประชุม & วันจัดประชุม
    </button>
    <button onclick="switchTab('tab-board')" id="btn-tab-board" class="tab-btn flex-1 text-center py-3 font-bold text-sm text-slate-500 hover:text-slate-700 outline-none <?= !($agendaConfig['show_committee_page'] ?? true) ? 'hidden' : '' ?>">
        👨‍👩‍👧‍👦 3. คณะกรรมการเครือข่าย
    </button>
</div>

<!-- ================= STEP 1: MINUTES FORM ================= -->
<div id="tab-minutes" class="tab-content glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl no-print">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-gradient-to-br from-violet-400 to-purple-500 rounded-xl flex items-center justify-center shadow">
            <i class="fas fa-edit text-white"></i>
        </div>
        <h3 class="text-lg font-black text-slate-800 dark:text-white">ระเบียบวาระบันทึกการประชุม</h3>
    </div>

    <form id="minutesForm" class="space-y-6">
        <?php for ($i = 1; $i <= 5; $i++): 
            $agenda = $agendaConfig['agendas'][$i] ?? ['title' => 'ระเบียบวาระที่ ' . $i, 'subs' => []];
        ?>
            <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-4">
                <h4 class="font-bold text-violet-600"><?= htmlspecialchars($agenda['title']); ?></h4>
                <div class="space-y-3 pl-2">
                    <?php if (!empty($agenda['subs'])): ?>
                        <?php foreach ($agenda['subs'] as $idx => $subTitle): ?>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1"><?= htmlspecialchars($subTitle); ?></label>
                                <textarea name="agenda_data[<?= $i ?>][]" id="agenda_<?= $i ?>_<?= $idx ?>" rows="2" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-violet-500 outline-none bg-white"></textarea>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-slate-400 text-sm italic">ไม่มีหัวข้อย่อยสำหรับระเบียบวาระนี้</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endfor; ?>

        <!-- Closing time -->
        <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 max-w-sm">
            <label class="block text-sm font-bold text-slate-700 mb-2">เวลาปิดประชุม (เช่น 16.30 หรือ 16:30 น.)</label>
            <input type="text" name="closing_time" id="closing_time" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-violet-500 outline-none bg-white">
        </div>

        <div class="flex justify-end pt-4">
            <button type="button" onclick="submitMinutesForm()" class="btn-action px-8 py-3 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold rounded-xl shadow-lg">
                <i class="fas fa-save mr-2"></i>บันทึกข้อมูลการประชุม
            </button>
        </div>
    </form>
</div>

<!-- ================= STEP 2: PHOTOS & DATE ================= -->
<div id="tab-photos" class="tab-content hidden glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl no-print">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-violet-400 to-purple-500 rounded-xl flex items-center justify-center shadow">
                <i class="fas fa-images text-white"></i>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white">ภาพกิจกรรม & กำหนดวันที่ประชุม</h3>
        </div>
        <button type="button" onclick="openPhotoModal()" class="btn-action inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold text-sm rounded-xl shadow-lg">
            <i class="fas fa-camera"></i>
            <span>📷 อัปโหลดรูปภาพ</span>
        </button>
    </div>

    <!-- Date Config Form -->
    <form id="dateForm" class="bg-violet-50/50 border border-violet-100 rounded-2xl p-4 mb-6 flex flex-col md:flex-row items-end gap-4">
        <div class="flex-1 w-full">
            <label class="block text-sm font-bold text-slate-700 mb-2">กำหนดวันที่ประชุม (แสดงผลในเล่มรายงาน):</label>
            <input type="text" name="meeting_date" id="meeting_date" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-violet-500 outline-none bg-white" placeholder="เช่น วันเสาร์ ที่ 13 เดือน มิถุนายน พ.ศ. 2569">
        </div>
        <button type="button" onclick="submitDateForm()" class="btn-action px-6 py-3 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold rounded-xl w-full md:w-auto">
            <i class="fas fa-save mr-2"></i>บันทึกวันที่จัดประชุม
        </button>
    </form>

    <!-- Photo Grid -->
    <div id="pictureGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div id="loadingState" class="col-span-full text-center py-12">
            <div class="animate-spin w-10 h-10 border-4 border-violet-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-slate-500 font-semibold">กำลังโหลดรูปภาพ...</p>
        </div>
    </div>
</div>

<!-- ================= STEP 3: PARENT NETWORK BOARD ================= -->
<div id="tab-board" class="tab-content hidden glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl no-print">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center shadow">
                <i class="fas fa-user-group text-white"></i>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white">คณะกรรมการเครือข่ายผู้ปกครองในชั้นเรียน</h3>
        </div>
        <button type="button" onclick="openBoardAddModal()" class="btn-action inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg">
            <i class="fas fa-plus-circle"></i>
            <span>➕ เพิ่มกรรมการ</span>
        </button>
    </div>

    <!-- Mobile Search -->
    <div class="md:hidden mb-4">
        <div class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="mobileSearchBoard" placeholder="🔍 ค้นหากรรมการ..." 
                   class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium focus:outline-none focus:border-emerald-500">
        </div>
    </div>

    <!-- Mobile Cards Container -->
    <div id="mobileCards" class="space-y-3">
        <div id="mobileLoading" class="glass-card rounded-2xl p-8 text-center">
            <div class="animate-spin w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-slate-500 font-semibold">กำลังโหลดข้อมูล...</p>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="overflow-x-auto hidden md:block">
        <table id="record_table" class="w-full" style="width:100%">
            <thead>
                <tr class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
                    <th class="px-3 py-3 text-center rounded-tl-xl w-16">ลำดับ</th>
                    <th class="px-3 py-3 text-left">ชื่อ-นามสกุล</th>
                    <th class="px-3 py-3 text-left">ที่อยู่</th>
                    <th class="px-3 py-3 text-center">โทรศัพท์</th>
                    <th class="px-3 py-3 text-center">ตำแหน่ง</th>
                    <th class="px-3 py-3 text-center">รูปถ่าย</th>
                    <th class="px-3 py-3 text-center rounded-tr-xl">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <!-- Filled by JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODALS & TEMPLATES ================= -->

<!-- Image Upload Modal -->
<div class="modal fade" id="addPhotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-violet-500 to-purple-600 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="fas fa-camera"></i> อัปโหลดรูปภาพการประชุม
                </h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50">
                <form id="uploadForm" enctype="multipart/form-data" class="space-y-4">
                    <div class="bg-violet-50 border-l-4 border-violet-400 p-3 rounded-r-xl">
                        <p class="text-sm text-violet-800 font-semibold">📷 คำแนะนำ: สามารถอัปโหลดได้สูงสุด 4 รูปต่อครั้ง</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <?php for($i=1; $i<=4; $i++): ?>
                        <div class="relative group">
                            <div id="dropzone<?= $i ?>" class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-violet-400 transition-colors cursor-pointer">
                                <input type="file" id="uploadImage<?= $i ?>" name="uploadImage[]" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" <?= $i===1 ? 'required' : '' ?>>
                                <img id="preview<?= $i ?>" src="#" alt="" class="hidden w-full h-32 object-cover rounded-lg mb-2">
                                <div id="placeholder<?= $i ?>" class="space-y-2">
                                    <div class="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-slate-400 text-xl"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500">รูปที่ <?= $i ?><?= $i===1 ? ' *' : '' ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-0 py-4 gap-2">
                <button type="button" class="px-5 py-2 bg-slate-400 text-white font-bold rounded-xl" data-dismiss="modal">ปิด</button>
                <button type="button" onclick="submitPhotoUploadForm()" class="px-5 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold rounded-xl">
                    <i class="fas fa-upload mr-2"></i>อัปโหลด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 bg-black/90 rounded-3xl overflow-hidden">
            <div class="modal-header border-0 py-3 px-4">
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <img id="lightboxImage" src="" alt="" class="max-w-full max-h-[70vh] rounded-xl mx-auto shadow-2xl">
            </div>
        </div>
    </div>
</div>

<!-- Committee Add Modal -->
<div class="modal fade" id="addBoardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-emerald-500 to-teal-600 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> เพิ่มข้อมูลคณะกรรมการเครือข่ายผู้ปกครอง
                </h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50">
                <form id="addBoardForm" method="post" enctype="multipart/form-data" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">เป็นผู้ปกครองของ:</label>
                            <select id="addStudentId" name="stu_id" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required>
                                <option value="">-- กรุณาเลือก --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ตำแหน่ง:</label>
                            <select name="pos" id="addPos" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" required>
                                <option value="">-- เลือกตำแหน่ง --</option>
                                <option value="1">👑 ประธาน</option>
                                <option value="2">👥 กรรมการ</option>
                                <option value="3">📝 เลขานุการ</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="parentSelectSection" class="hidden">
                        <label class="block text-sm font-bold text-slate-700 mb-2">เลือกข้อมูลผู้ปกครองจากฐานข้อมูล:</label>
                        <div class="bg-white border border-slate-200 rounded-xl p-3 space-y-2" id="parentOptionsContainer"></div>
                        <div class="mt-2 flex items-center gap-2">
                            <input type="checkbox" id="manualInputCheck" class="w-4 h-4 text-emerald-500 rounded">
                            <label for="manualInputCheck" class="text-sm text-slate-600 cursor-pointer">✏️ พิมพ์ข้อมูลเอง</label>
                        </div>
                    </div>
                    
                    <div id="parentInfoSection">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ชื่อ-สกุลผู้ปกครอง:</label>
                            <input type="text" name="name" id="addName" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="ระบุชื่อ-นามสกุล" required>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">ที่อยู่:</label>
                            <textarea name="address" id="addAddress" rows="2" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="ระบุที่อยู่" required></textarea>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">เบอร์โทรศัพท์:</label>
                            <input type="tel" name="tel" id="addTel" maxlength="10" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="0812345678" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">รูปถ่าย:</label>
                            <input type="file" name="image1" accept="image/*" class="w-full text-sm file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200">
                        </div>
                    </div>
                    <input type="hidden" name="major" value="<?= $class ?>">
                    <input type="hidden" name="room" value="<?= $room ?>">
                    <input type="hidden" name="teacherid" value="<?= $teacher_id ?>">
                    <input type="hidden" name="term" value="<?= $term ?>">
                    <input type="hidden" name="pee" value="<?= $pee ?>">
                </form>
            </div>
            <div class="modal-footer bg-white border-0 py-4 gap-2">
                <button type="button" class="px-5 py-2 bg-slate-400 text-white font-bold rounded-xl" data-dismiss="modal">ปิด</button>
                <button type="button" onclick="submitBoardAddForm()" class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-xl">
                    <i class="fas fa-save mr-2"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Committee Edit Modal -->
<div class="modal fade" id="editBoardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-amber-500 to-orange-600 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลคณะกรรมการ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 md:p-6 bg-slate-50">
                <form id="editBoardForm" method="post" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="edit_id" id="editId">
                    <input type="hidden" name="pee" value="<?= $pee ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">เป็นผู้ปกครองของ:</label>
                            <select id="editStudentId" name="stu_id" class="w-full px-4 py-3 border border-slate-300 rounded-xl bg-slate-100" disabled>
                                <option value="">-- กรุณาเลือก --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ตำแหน่ง:</label>
                            <select name="pos" id="editPos" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500" required>
                                <option value="">-- เลือกตำแหน่ง --</option>
                                <option value="1">👑 ประธาน</option>
                                <option value="2">👥 กรรมการ</option>
                                <option value="3">📝 เลขานุการ</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ชื่อ-สกุลผู้ปกครอง:</label>
                        <input type="text" name="name" id="editName" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ที่อยู่:</label>
                        <textarea name="address" id="editAddress" rows="2" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500" required></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">เบอร์โทรศัพท์:</label>
                            <input type="tel" name="tel" id="editTel" maxlength="10" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">รูปถ่าย:</label>
                            <input type="file" name="image1" accept="image/*" class="w-full text-sm file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                            <img id="editImagePreview" src="#" alt="Preview" class="mt-2 w-20 h-20 rounded-full object-cover hidden">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-0 py-4 gap-2">
                <button type="button" class="px-5 py-2 bg-slate-400 text-white font-bold rounded-xl" data-dismiss="modal">ปิด</button>
                <button type="button" onclick="submitBoardEditForm()" class="px-5 py-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-xl">
                    <i class="fas fa-save mr-2"></i>บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
(function() {
    const classId = <?= json_encode($class) ?>;
    const roomId = <?= json_encode($room) ?>;
    const termValue = <?= json_encode($term) ?>;
    const peeValue = <?= json_encode($pee) ?>;
    let currentParentData = [];

    // Switch Wizard Tabs
    window.switchTab = function(tabId) {
        $('.tab-content').addClass('hidden');
        $('#' + tabId).removeClass('hidden');
        
        $('.tab-btn').removeClass('active text-violet-600').addClass('text-slate-500 hover:text-slate-700');
        $('#btn-' + tabId).addClass('active text-violet-600').removeClass('text-slate-500 hover:text-slate-700');
    };

    // Load meeting details (Minutes, Date, Photos)
    function loadMeetingData() {
        $.ajax({
            url: 'api/fetch_picture_meeting.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classId, room: roomId, term: termValue, pee: peeValue },
            success: function(response) {
                $('#loadingState').remove();
                
                // 1. Populate Step 1 Form
                if (response.success && response.metadata) {
                    const meta = response.metadata;
                    $('#meeting_date').val(meta.meeting_date || <?= json_encode($agendaConfig['meeting_date'] ?? '') ?>);
                    $('#closing_time').val(meta.closing_time || '');
                    
                    if (meta.agenda_data) {
                        try {
                            const dynamicData = JSON.parse(meta.agenda_data);
                            for (let agendaNum in dynamicData) {
                                if (Array.isArray(dynamicData[agendaNum])) {
                                    dynamicData[agendaNum].forEach((text, idx) => {
                                        $(`#agenda_${agendaNum}_${idx}`).val(text);
                                    });
                                }
                            }
                        } catch (e) {
                            console.error("Failed to parse agenda_data JSON, falling back to legacy fields", e);
                        }
                    } else {
                        // Fallback to legacy fields
                        $('#agenda_1_0').val(meta.agenda1_1 || '');
                        $('#agenda_1_1').val(meta.agenda1_2 || '');
                        $('#agenda_1_2').val(meta.agenda1_3 || '');
                        $('#agenda_1_3').val(meta.agenda1_4 || '');
                        $('#agenda_2_0').val(meta.agenda2 || '');
                        $('#agenda_3_0').val(meta.agenda3 || '');
                        $('#agenda_4_0').val(meta.agenda4_1 || '');
                        $('#agenda_4_1').val(meta.agenda4_2 || '');
                        $('#agenda_5_0').val(meta.agenda5_1 || '');
                        $('#agenda_5_1').val(meta.agenda5_2 || '');
                        $('#agenda_5_2').val(meta.agenda5_other || '');
                    }
                }

                // 2. Render Photos Grid (Step 2)
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach((picture, index) => {
                        html += `
                            <div class="image-card bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-lg p-2">
                                <div class="relative group cursor-pointer" onclick="openLightbox('${picture.url}')">
                                    <img src="${picture.url}" alt="${picture.alt || 'ภาพประชุม'}" class="w-full h-32 object-cover rounded-lg">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/35 transition-colors flex items-center justify-center rounded-lg">
                                        <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 text-lg transition-opacity"></i>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-xs text-slate-500 font-bold">รูปภาพที่ ${index + 1}</span>
                                    <button onclick="deletePicture(${picture.id || index})" class="btn-action p-1 bg-gradient-to-r from-rose-400 to-red-500 text-white text-[10px] rounded-lg">
                                        <i class="fas fa-trash"></i> ลบรูป
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    $('#pictureGrid').html(html);
                } else {
                    $('#pictureGrid').html('<p class="col-span-full text-center text-slate-500 py-6">ยังไม่มีรูปถ่ายกิจกรรมการประชุม</p>');
                }
            }
        });
    }

    // Save step 1 (minutes form)
    window.submitMinutesForm = function() {
        const formData = new FormData($('#minutesForm')[0]);
        formData.append('class', classId);
        formData.append('room', roomId);
        formData.append('term', termValue);
        formData.append('pee', peeValue);
        formData.append('meeting_date', $('#meeting_date').val()); // Carry date sync

        Swal.fire({ title: 'กำลังบันทึกวาระการประชุม...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: 'api/save_parent_meeting.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', text: response.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: response.message });
                }
            }
        });
    };

    // Save step 2 (date only)
    window.submitDateForm = function() {
        Swal.fire({ title: 'กำลังบันทึกวันที่จัดประชุม...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            url: 'api/save_parent_meeting.php',
            method: 'POST',
            data: {
                class: classId,
                room: roomId,
                term: termValue,
                pee: peeValue,
                meeting_date: $('#meeting_date').val(),
                closing_time: $('#closing_time').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'บันทึกวันที่เรียบร้อยแล้ว', timer: 1200, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: response.message });
                }
            }
        });
    };

    // Photo uploads
    window.openPhotoModal = function() {
        $('#uploadForm')[0].reset();
        for(let i=1; i<=4; i++) {
            $(`#preview${i}`).addClass('hidden').attr('src', '#');
            $(`#placeholder${i}`).removeClass('hidden');
        }
        $('#addPhotoModal').modal('show');
    };

    window.submitPhotoUploadForm = function() {
        const formData = new FormData($('#uploadForm')[0]);
        formData.append('class', classId);
        formData.append('room', roomId);
        formData.append('term', termValue);
        formData.append('pee', peeValue);

        Swal.fire({ title: 'กำลังอัปโหลดรูปภาพ...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: 'api/insert_picture_meeting.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update meeting date automatically
                    $.ajax({
                        url: 'api/save_parent_meeting.php',
                        method: 'POST',
                        data: {
                            class: classId,
                            room: roomId,
                            term: termValue,
                            pee: peeValue,
                            meeting_date: $('#meeting_date').val()
                        },
                        success: function() {
                            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'อัปโหลดรูปภาพแล้ว', timer: 1200, showConfirmButton: false }).then(() => {
                                $('#addPhotoModal').modal('hide');
                                loadMeetingData();
                            });
                        }
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: response.message });
                }
            }
        });
    };

    window.deletePicture = function(pictureId) {
        Swal.fire({
            title: 'ยืนยันการลบรูป?',
            text: 'คุณต้องการลบรูปภาพนี้ใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบรูป',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/delete_picture_meeting.php',
                    method: 'POST',
                    data: { id: pictureId, class: classId, room: roomId, term: termValue, pee: peeValue },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', timer: 1200 }).then(() => loadMeetingData());
                        } else {
                            Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: res.message });
                        }
                    }
                });
            }
        });
    };

    window.openLightbox = function(url) {
        $('#lightboxImage').attr('src', url);
        $('#lightboxModal').modal('show');
    };

    // Previews upload handler
    function previewImage(input, previewId, placeholderId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    }
    for(let i=1; i<=4; i++) {
        $(`#uploadImage${i}`).on('change', function() { previewImage(this, `preview${i}`, `placeholder${i}`); });
    }

    // ================= STEP 3: BOARD PARENT CODE EMBEDDED =================
    
    // Load students for dropdown
    $.ajax({
        url: '../teacher/api/fetch_student_classroom.php',
        method: 'GET',
        dataType: 'json',
        data: { class: classId, room: roomId },
        success: function(response) {
            if (response.success) {
                response.data.forEach(student => {
                    const option = `<option value="${student.Stu_id}">${student.Stu_pre}${student.Stu_name} ${student.Stu_sur}</option>`;
                    $('#addStudentId, #editStudentId').append(option);
                });
            }
        }
    });

    function getPositionBadge(pos) {
        const positions = {
            '1': { text: 'ประธาน', icon: '👑', color: 'amber' },
            '2': { text: 'กรรมการ', icon: '👥', color: 'blue' },
            '3': { text: 'เลขานุการ', icon: '📝', color: 'violet' }
        };
        const p = positions[pos] || { text: 'ไม่ระบุ', icon: '❓', color: 'slate' };
        return `<span class="inline-flex items-center gap-1 px-2 py-1 bg-${p.color}-100 text-${p.color}-700 text-xs font-bold rounded-full">${p.icon} ${p.text}</span>`;
    }

    // Student select handler
    $('#addStudentId').on('change', function() {
        const stuId = $(this).val();
        if (!stuId) {
            $('#parentSelectSection').addClass('hidden');
            $('#parentOptionsContainer').empty();
            $('#addName, #addAddress, #addTel').val('');
            currentParentData = [];
            return;
        }

        $.ajax({
            url: '../teacher/api/get_parent_info.php',
            method: 'GET',
            dataType: 'json',
            data: { stu_id: stuId },
            success: function(response) {
                if (response.success && response.data.parents.length > 0) {
                    currentParentData = response.data.parents;
                    let html = '';
                    response.data.parents.forEach((parent, index) => {
                        html += `
                            <label class="flex items-center gap-3 p-3 rounded-lg border-2 border-slate-200 hover:border-emerald-400 cursor-pointer transition-all parent-option" data-index="${index}">
                                <input type="radio" name="parent_select" value="${index}" class="w-5 h-5 text-emerald-500">
                                <div class="flex-1">
                                    <span class="font-bold text-slate-800">${parent.label}</span>
                                    <p class="text-sm text-slate-600">${parent.name || 'ไม่ระบุชื่อ'}</p>
                                </div>
                            </label>
                        `;
                    });
                    $('#parentOptionsContainer').html(html);
                    $('#parentSelectSection').removeClass('hidden');
                    
                    if (response.data.parents.length > 0) {
                        $('input[name="parent_select"]:first').prop('checked', true).trigger('change');
                    }
                } else {
                    $('#parentOptionsContainer').html('<div class="text-center py-4 text-slate-500"><p class="text-sm">ไม่พบข้อมูลผู้ปกครองในระบบ</p></div>');
                    $('#parentSelectSection').removeClass('hidden');
                    if (response.data && response.data.student_address) {
                        $('#addAddress').val(response.data.student_address);
                    }
                }
            }
        });
    });

    $(document).on('change', 'input[name="parent_select"]', function() {
        const index = parseInt($(this).val());
        const parent = currentParentData[index];
        if (parent) {
            $('#addName').val(parent.name || '');
            $('#addAddress').val(parent.address || '');
            $('#addTel').val(parent.phone || '');
            $('#addName, #addAddress').prop('readonly', true).addClass('bg-slate-100');
        }
    });

    $('#manualInputCheck').on('change', function() {
        if ($(this).is(':checked')) {
            $('#addName, #addAddress').prop('readonly', false).removeClass('bg-slate-100').val('');
            $('#addTel').val('');
            $('input[name="parent_select"]').prop('checked', false);
        } else {
            const firstChecked = $('input[name="parent_select"]:first');
            if (firstChecked.length) firstChecked.prop('checked', true).trigger('change');
        }
    });

    $(document).on('change', 'input[name="parent_select"]', function() {
        $('.parent-option').removeClass('border-emerald-500 bg-emerald-50');
        $(this).closest('.parent-option').addClass('border-emerald-500 bg-emerald-50');
        $('#manualInputCheck').prop('checked', false);
    });

    function createMobileCardBoard(item, index) {
        const photoUrl = item.parn_photo ? `https://std.phichai.ac.th/teacher/uploads/photopar/${item.parn_photo}` : '../dist/img/user-placeholder.png';
        return `
            <div class="member-card glass-card rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg slide-in" data-name="${item.parn_name.toLowerCase()}">
                <div class="flex items-start gap-4">
                    <img src="${photoUrl}" alt="${item.parn_name}" class="w-16 h-16 rounded-full object-cover shadow-lg flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm">${item.parn_name}</h4>
                            ${getPositionBadge(item.parn_pos)}
                        </div>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">${item.parn_addr || '-'}</p>
                        <p class="text-xs text-slate-600 mt-1"><i class="fas fa-phone text-emerald-500"></i> ${item.parn_tel || '-'}</p>
                    </div>
                </div>
                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                    <button onclick="openBoardEditModal('${item.Stu_id}')" class="btn-action flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-bold text-xs rounded-lg shadow">
                        <i class="fas fa-edit"></i> แก้ไข
                    </button>
                    <button onclick="deleteBoardRecord('${item.Stu_id}')" class="btn-action inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-rose-400 to-red-500 text-white font-bold text-xs rounded-lg shadow">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    $('#mobileSearchBoard').on('input', function() {
        const term = $(this).val().toLowerCase();
        $('.member-card').each(function() {
            $(this).toggle($(this).data('name').includes(term));
        });
    });

    // Load Parent Committee Table
    function loadBoardTable() {
        $.ajax({
            url: '../teacher/api/fetch_boardparent_classroom.php',
            method: 'GET',
            dataType: 'json',
            data: { class: classId, room: roomId, pee: peeValue },
            success: function(response) {
                if (!response.success) return;
                const allBoardData = response.data;
                
                $('#mobileLoading').remove();
                let mobileHtml = '';
                if (allBoardData.length === 0) {
                    mobileHtml = '<div class="glass-card rounded-2xl p-8 text-center text-slate-500">ยังไม่มีรายชื่อคณะกรรมการเครือข่ายผู้ปกครอง</div>';
                } else {
                    allBoardData.forEach((item, index) => {
                        mobileHtml += createMobileCardBoard(item, index);
                    });
                }
                $('#mobileCards').html(mobileHtml);

                // DataTable
                const table = $('#record_table').DataTable({
                    destroy: true,
                    paging: false,
                    searching: false,
                    ordering: false,
                    info: false
                });
                table.clear();

                allBoardData.forEach((item, index) => {
                    const photoUrl = item.parn_photo ? `https://std.phichai.ac.th/teacher/uploads/photopar/${item.parn_photo}` : '../dist/img/user-placeholder.png';
                    const actionBtns = `
                        <div class="flex gap-1 justify-center">
                            <button class="btn-action px-2 py-1 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold rounded-lg" onclick="openBoardEditModal('${item.Stu_id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action px-2 py-1 bg-gradient-to-r from-rose-400 to-red-500 text-white text-xs font-bold rounded-lg" onclick="deleteBoardRecord('${item.Stu_id}')"><i class="fas fa-trash"></i></button>
                        </div>`;

                    table.row.add([
                        `<span class="inline-flex items-center justify-center w-7 h-7 bg-slate-100 text-slate-700 font-bold rounded-lg text-sm">${index + 1}</span>`,
                        `<span class="font-semibold text-sm">${item.parn_name}</span>`,
                        `<span class="text-xs text-slate-600">${item.parn_addr || '-'}</span>`,
                        `<span class="text-sm">${item.parn_tel || '-'}</span>`,
                        getPositionBadge(item.parn_pos),
                        `<img src="${photoUrl}" class="w-10 h-10 rounded-full object-cover mx-auto shadow">`,
                        actionBtns
                    ]);
                });
                table.draw();
            }
        });
    }

    window.openBoardAddModal = function() {
        $('#addBoardForm')[0].reset();
        $('#parentSelectSection').addClass('hidden');
        $('#parentOptionsContainer').empty();
        $('#addName, #addAddress').prop('readonly', false).removeClass('bg-slate-100');
        $('#manualInputCheck').prop('checked', false);
        currentParentData = [];
        $('#addBoardModal').modal('show');
    };

    window.submitBoardAddForm = function() {
        const formData = new FormData($('#addBoardForm')[0]);
        Swal.fire({ title: 'กำลังบันทึกกรรมการ...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            url: '../teacher/api/insert_boardparent.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ!', timer: 1200, showConfirmButton: false }).then(() => {
                        $('#addBoardModal').modal('hide');
                        loadBoardTable();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: response.message });
                }
            }
        });
    };

    window.openBoardEditModal = function(studentId) {
        $.ajax({
            url: '../teacher/api/fetch_boardparent_data.php',
            method: 'GET',
            data: { id: studentId, pee: peeValue },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editId').val(response.data.Stu_id);
                    $('#editStudentId').val(response.data.Stu_id);
                    $('#editName').val(response.data.parn_name);
                    $('#editAddress').val(response.data.parn_addr);
                    $('#editTel').val(response.data.parn_tel);
                    $('#editPos').val(response.data.parn_pos);
                    
                    if (response.data.parn_photo) {
                        $('#editImagePreview').attr('src', `https://std.phichai.ac.th/teacher/uploads/photopar/${response.data.parn_photo}`).removeClass('hidden');
                    } else {
                        $('#editImagePreview').addClass('hidden');
                    }
                    $('#editBoardModal').modal('show');
                }
            }
        });
    };

    window.submitBoardEditForm = function() {
        const formData = new FormData($('#editBoardForm')[0]);
        Swal.fire({ title: 'กำลังบันทึกแก้ไข...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            url: '../teacher/api/update_boardparent_data.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ!', timer: 1200, showConfirmButton: false }).then(() => {
                        $('#editBoardModal').modal('hide');
                        loadBoardTable();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: response.message });
                }
            }
        });
    };

    window.deleteBoardRecord = function(studentId) {
        Swal.fire({
            title: 'ยืนยันการลบข้อมูล?',
            text: 'คุณต้องการลบลายชื่อคณะกรรมการคนนี้ใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../teacher/api/delete_boardparent_data.php',
                    method: 'POST',
                    data: { id: studentId },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', timer: 1200 }).then(() => loadBoardTable());
                        }
                    }
                });
            }
        });
    };

    // Initial Wizard Loader
    loadMeetingData();
    loadBoardTable();
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
