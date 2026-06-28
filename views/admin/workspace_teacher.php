<?php
/**
 * View: Admin Workspace Teacher Email Sync
 */
ob_start();
$pageTitle = "จัดการ Workspace ครู";
$activePage = "workspace_teacher";

// Fetch teachers list directly for rendering initial state
$stmt = $db->query("SELECT Teach_id, Teach_name, Teach_email FROM teacher WHERE Teach_status = 1 ORDER BY Teach_id ASC");
$teachersList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 mb-3">
            <i class="fas fa-chalkboard-teacher text-sm"></i>
            <span class="text-xs font-bold tracking-wide">GOOGLE WORKSPACE TEACHER SYNC</span>
        </div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">ดึงอีเมลครูและบุคลากร <span class="text-emerald-600">(@phichai.ac.th)</span></h1>
        <p class="text-slate-500 mt-2 font-medium">เชื่อมโยงและซิงค์อีเมลโรงเรียนจาก Google Workspace ให้กับคุณครูในระบบโดยอัตโนมัติ</p>
    </div>
    
    <div>
        <button id="syncBtn" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3 px-6 rounded-2xl transition-all shadow-lg shadow-emerald-500/20 active:scale-95 flex items-center gap-2">
            <i class="fas fa-sync-alt"></i> ซิงค์ข้อมูลกับ Workspace
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Info Panel (Left) -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Step Summary Card -->
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xl shadow-slate-200/30">
            <h3 class="text-lg font-black text-slate-800 mb-4 border-b border-slate-100 pb-2">
                <i class="fas fa-info-circle text-emerald-500 mr-2"></i>ข้อมูลขั้นตอนการซิงค์
            </h3>
            
            <div class="space-y-4 text-sm text-slate-600">
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-bold flex items-center justify-center text-xs">1</span>
                    <p>ระบบจะเรียกข้อมูลบัญชีทั้งหมดจาก Google Workspace ของโดเมน <b class="text-slate-800">@phichai.ac.th</b></p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-bold flex items-center justify-center text-xs">2</span>
                    <p>เปรียบเทียบชื่อ-สกุลครูในระบบกับชื่อใน Workspace โดยการทำความสะอาดชื่อ (ลบคำนำหน้า นาย/นาง/นางสาว และช่องว่างส่วนเกิน)</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-bold flex items-center justify-center text-xs">3</span>
                    <p>อัปเดตฟิลด์ <code class="bg-slate-100 text-rose-600 px-1 rounded font-mono">Teach_email</code> ในตารางครูของฐานข้อมูลโดยอัตโนมัติเมื่อพบความต่าง</p>
                </div>
            </div>
        </div>

        <!-- Sync Stats Card -->
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xl shadow-slate-200/30">
            <h3 class="text-lg font-black text-slate-800 mb-4 border-b border-slate-100 pb-2">
                <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>สถิติทั่วไปในฐานข้อมูล
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <span class="text-sm font-bold text-slate-500">จำนวนครูทั้งหมด:</span>
                    <span class="text-lg font-black text-slate-800"><?php echo count($teachersList); ?> คน</span>
                </div>
                <div class="flex justify-between items-center bg-emerald-50/50 p-3 rounded-xl border border-emerald-100/60">
                    <span class="text-sm font-bold text-emerald-600">มีอีเมลโรงเรียน (@phichai.ac.th):</span>
                    <?php 
                    $withSchoolMail = 0;
                    foreach ($teachersList as $t) {
                        if (strpos($t['Teach_email'], '@phichai.ac.th') !== false) {
                            $withSchoolMail++;
                        }
                    }
                    ?>
                    <span class="text-lg font-black text-emerald-700"><?php echo $withSchoolMail; ?> คน</span>
                </div>
                <div class="flex justify-between items-center bg-amber-50/50 p-3 rounded-xl border border-amber-100/60">
                    <span class="text-sm font-bold text-amber-600">ไม่มีอีเมลโรงเรียน:</span>
                    <span class="text-lg font-black text-amber-700"><?php echo count($teachersList) - $withSchoolMail; ?> คน</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher List Section (Right) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xl shadow-slate-200/30 flex flex-col h-full">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-800">
                    <i class="fas fa-list text-slate-400 mr-2"></i>รายชื่อคุณครูและสถานะอีเมล
                </h3>
                
                <!-- Quick Filter / Search -->
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" id="searchTeacher" placeholder="ค้นหาครู..." class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl pl-9 pr-4 py-2 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-medium">
                </div>
            </div>

            <!-- Table Container -->
            <div class="flex-1 overflow-auto max-h-[500px] rounded-xl border border-slate-200">
                <table class="admin-responsive-table w-full text-left border-collapse" id="teacherTable">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase font-bold sticky top-0 z-10">
                        <tr>
                            <th class="p-3 border-b border-slate-200 w-12 text-center">#</th>
                            <th class="p-3 border-b border-slate-200">รหัส</th>
                            <th class="p-3 border-b border-slate-200">ชื่อ-นามสกุล</th>
                            <th class="p-3 border-b border-slate-200">อีเมลในฐานข้อมูล</th>
                            <th class="p-3 border-b border-slate-200 text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100" id="teacherListBody">
                        <?php foreach ($teachersList as $idx => $t): 
                            $hasMail = strpos($t['Teach_email'], '@phichai.ac.th') !== false;
                            $emailVal = !empty($t['Teach_email']) ? htmlspecialchars($t['Teach_email']) : '<span class="text-slate-400 italic">ไม่มีข้อมูล</span>';
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors teacher-row">
                                <td class="p-3 text-center text-slate-400 font-bold"><?php echo $idx + 1; ?></td>
                                <td class="p-3 font-semibold text-slate-700"><?php echo htmlspecialchars($t['Teach_id']); ?></td>
                                <td class="p-3 font-bold text-slate-800 teacher-name"><?php echo htmlspecialchars($t['Teach_name']); ?></td>
                                <td class="p-3 font-medium text-indigo-600"><?php echo $emailVal; ?></td>
                                <td class="p-3 text-center">
                                    <?php if ($hasMail): ?>
                                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i>พร้อม</span>
                                    <?php else: ?>
                                        <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs font-bold"><i class="fas fa-exclamation-circle mr-1"></i>ต้องซิงค์</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // ฟังก์ชันค้นหาแบบเรียลไทม์
    $('#searchTeacher').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.teacher-row').each(function() {
            const name = $(this).find('.teacher-name').text().toLowerCase();
            if (name.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // คลิกปุ่มซิงค์ข้อมูล
    $('#syncBtn').click(function() {
        Swal.fire({
            title: 'เริ่มซิงค์ข้อมูลอีเมลครู?',
            text: 'ระบบจะเริ่มดึงบัญชีจาก Google Workspace และเปรียบเทียบในฐานข้อมูลทันที',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ตกลง, ซิงค์เลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดงหน้าจอกำลังทำงาน
                Swal.fire({
                    title: 'กำลังเชื่อมโยงและซิงค์ข้อมูล...',
                    html: 'กรุณาอย่าปิดหน้าต่างระบบ กำลังสื่อสารกับ Google Apps Script Web App',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '../api/google_workspace_api.php',
                    type: 'POST',
                    data: {
                        action: 'sync_teachers_email'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ซิงค์ข้อมูลสำเร็จ!',
                                html: `เชื่อมโยงครูสำเร็จ: <b>${res.matched_count} คน</b><br>อัปเดตข้อมูลอีเมลใหม่: <b class="text-emerald-500">${res.updated_count} รายการ</b>`,
                                confirmButtonText: 'ตกลง'
                            }).then(() => {
                                // โหลดหน้าใหม่เพื่ออัปเดตตารางและสถิติ
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาดในการซิงค์',
                                text: res.message || 'ไม่สามารถติดต่อ Google Apps Script ได้'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'ไม่สามารถส่งคำขอซิงค์ข้อมูลได้ กรุณาลองใหม่อีกครั้ง'
                        });
                    }
                });
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
