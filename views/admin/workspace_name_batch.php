<?php
/**
 * View: Admin Workspace Name Batch Update
 */
ob_start();
$pageTitle = "อัปเดตชื่อเมล Workspace (กลุ่ม)";
$activePage = "workspace_name_batch";
?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 text-indigo-600 border border-indigo-500/20 mb-3">
            <i class="fas fa-user-edit text-sm"></i>
            <span class="text-xs font-bold tracking-wide">GOOGLE WORKSPACE NAME BATCH</span>
        </div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">อัปเดตชื่ออีเมล <span class="text-indigo-600">ทั้งห้องเรียน</span></h1>
        <p class="text-slate-500 mt-2 font-medium">อัปเดต ชื่อ-นามสกุล ของบัญชี Google Workspace นักเรียนทั้งห้องพร้อมกันเพื่อความเป็นระเบียบ</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Filter Section (Left) -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Step 1: Select Room -->
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xl shadow-slate-200/30">
            <h3 class="text-lg font-black text-slate-800 mb-4 border-b border-slate-100 pb-2">
                <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-lg text-sm mr-2">1</span>เลือกห้องเรียน
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">ระดับชั้น</label>
                    <select id="filterClass" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 p-2.5 transition-all">
                        <option value="">-- เลือกระดับชั้น --</option>
                        <option value="1">มัธยมศึกษาปีที่ 1</option>
                        <option value="2">มัธยมศึกษาปีที่ 2</option>
                        <option value="3">มัธยมศึกษาปีที่ 3</option>
                        <option value="4">มัธยมศึกษาปีที่ 4</option>
                        <option value="5">มัธยมศึกษาปีที่ 5</option>
                        <option value="6">มัธยมศึกษาปีที่ 6</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">ห้อง</label>
                    <select id="filterRoom" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 p-2.5 transition-all">
                        <option value="">-- เลือกห้อง --</option>
                        <?php for($i=1; $i<=15; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button id="loadStudentsBtn" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 rounded-xl transition-all shadow-md mt-2 flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> โหลดรายชื่อ
                </button>
            </div>
        </div>

        <!-- Step 2: Info Card -->
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xl shadow-slate-200/30">
            <h3 class="text-lg font-black text-slate-800 mb-4 border-b border-slate-100 pb-2">
                <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-lg text-sm mr-2">2</span>รูปแบบการอัปเดต
            </h3>
            
            <div class="space-y-4">
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-3">
                    <div>
                        <span class="text-xs font-bold text-slate-400 block uppercase">ชื่อ (First Name)</span>
                        <span class="text-sm font-bold text-slate-700">เลขที่ 2 หลัก + ชื่อจริง</span>
                        <span class="text-xs text-indigo-600 block font-mono font-medium mt-0.5">เช่น ชานนท์ (เลขที่ 2) -> "02ชานนท์"</span>
                    </div>
                    <div class="border-t border-slate-200/60 pt-2">
                        <span class="text-xs font-bold text-slate-400 block uppercase">นามสกุล (Last Name)</span>
                        <span class="text-sm font-bold text-slate-700">นามสกุลจริงนักเรียน</span>
                        <span class="text-xs text-indigo-600 block font-mono font-medium mt-0.5">เช่น "นิ่มทอง"</span>
                    </div>
                </div>

                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                    <p class="text-xs text-indigo-600 font-bold mb-1"><i class="fas fa-info-circle mr-1"></i>ตัวอย่างผลลัพธ์:</p>
                    <p class="text-xs text-indigo-900 font-semibold mb-1">อีเมล: <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-indigo-200">std28442@phichai.ac.th</span></p>
                    <p class="text-xs text-indigo-900 font-semibold">อัปเดตชื่อในระบบเป็น: <span class="bg-white px-1.5 py-0.5 rounded border border-indigo-200 font-bold">"02ชานนท์ นิ่มทอง"</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table & Process Section (Right) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xl shadow-slate-200/30 flex flex-col h-full">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-800">
                    <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-lg text-sm mr-2">3</span>รายชื่อและข้อมูลใหม่
                </h3>
                
                <button id="startBatchBtn" disabled class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-xl transition-all shadow-lg shadow-emerald-500/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-sync-alt"></i> เริ่มอัปเดตชื่อทั้งห้อง
                </button>
            </div>
            
            <!-- Progress Section (Hidden by default) -->
            <div id="progressSection" class="hidden mb-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <div class="flex justify-between text-sm font-bold text-slate-700 mb-2">
                    <span>ความคืบหน้า: <span id="progressText">0/0</span></span>
                    <span id="progressPercent" class="text-indigo-600">0%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-3 mb-2 overflow-hidden">
                    <div id="progressBar" class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="flex gap-4 text-xs font-medium">
                    <span class="text-emerald-600"><i class="fas fa-check-circle"></i> สำเร็จ: <span id="successCount">0</span></span>
                    <span class="text-rose-600"><i class="fas fa-times-circle"></i> ล้มเหลว: <span id="failCount">0</span></span>
                </div>
            </div>

            <!-- Table -->
            <div class="flex-1 overflow-auto rounded-xl border border-slate-200">
                <table class="admin-responsive-table w-full text-left border-collapse" id="previewTable">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase font-bold sticky top-0 z-10">
                        <tr>
                            <th class="p-3 border-b border-slate-200 w-10 text-center">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer" checked>
                            </th>
                            <th class="p-3 border-b border-slate-200">เลขที่</th>
                            <th class="p-3 border-b border-slate-200">รหัสประจำตัว</th>
                            <th class="p-3 border-b border-slate-200">อีเมลเป้าหมาย</th>
                            <th class="p-3 border-b border-slate-200">ชื่อที่จะอัปเดต (First)</th>
                            <th class="p-3 border-b border-slate-200">นามสกุล (Last)</th>
                            <th class="p-3 border-b border-slate-200 text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody id="studentListBody" class="text-sm">
                        <tr>
                            <td colspan="7" class="text-center p-8 text-slate-400">
                                <i class="fas fa-inbox text-4xl mb-3 opacity-50 block"></i>
                                กรุณาเลือกระดับชั้นและห้องเรียน แล้วกด "โหลดรายชื่อ"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let studentsData = [];
    let isProcessing = false;

    // โหลดรายชื่อนักเรียน
    $('#loadStudentsBtn').click(function() {
        const major = $('#filterClass').val();
        const room = $('#filterRoom').val();
        
        if(!major || !room) {
            Swal.fire({icon: 'warning', title: 'แจ้งเตือน', text: 'กรุณาเลือกระดับชั้นและห้อง'});
            return;
        }
        
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> กำลังโหลด...').prop('disabled', true);
        
        $.ajax({
            url: '../controllers/StudentController.php',
            type: 'GET',
            data: {
                action: 'list_all',
                class: major,
                room: room,
                status: '1' // เฉพาะที่ยังเรียนอยู่
            },
            success: function(response) {
                let data = response.data || response;
                if(Array.isArray(data)) {
                    // เรียงข้อมูลนักเรียนตามเลขที่ (Numeric Sort)
                    data.sort((a, b) => {
                        const noA = parseInt(a.Stu_no) || 999;
                        const noB = parseInt(b.Stu_no) || 999;
                        return noA - noB;
                    });
                    
                    studentsData = data;
                    renderTable();
                    
                    if(studentsData.length > 0) {
                        $('#startBatchBtn').prop('disabled', false);
                    } else {
                        $('#startBatchBtn').prop('disabled', true);
                    }
                }
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
    
    function renderTable() {
        const tbody = $('#studentListBody');
        tbody.empty();
        
        if(studentsData.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center p-8 text-slate-400">ไม่พบรายชื่อนักเรียน</td></tr>');
            updateStartButton();
            return;
        }
        
        $('#selectAll').prop('checked', true); // Reset select all
        
        studentsData.forEach((stu) => {
            const email = `std${stu.Stu_id}@phichai.ac.th`;
            const stuNoStr = stu.Stu_no !== null && stu.Stu_no !== undefined ? String(stu.Stu_no).padStart(2, '0') : '00';
            const firstName = stuNoStr + (stu.Stu_name || '');
            const lastName = stu.Stu_sur || '';
            
            tbody.append(`
                <tr class="border-b border-slate-100 hover:bg-indigo-50/20 transition-colors" id="row-${stu.Stu_id}">
                    <td class="p-3 text-center">
                        <input type="checkbox" class="stu-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer" value="${stu.Stu_id}" checked>
                    </td>
                    <td class="p-3 text-slate-500 font-bold">${stu.Stu_no || '-'}</td>
                    <td class="p-3 font-medium text-slate-800">${stu.Stu_id}</td>
                    <td class="p-3 text-indigo-600 font-medium">${email}</td>
                    <td class="p-3 font-bold text-slate-700">${firstName}</td>
                    <td class="p-3 text-slate-700">${lastName}</td>
                    <td class="p-3 text-center" id="status-${stu.Stu_id}">
                        <span class="text-slate-400 text-xs">รอคิว</span>
                    </td>
                </tr>
            `);
        });
        updateStartButton();
    }

    // Select All logic
    $(document).on('change', '#selectAll', function() {
        $('.stu-checkbox').prop('checked', $(this).prop('checked'));
        updateStartButton();
    });

    // Individual Checkbox logic
    $(document).on('change', '.stu-checkbox', function() {
        if ($('.stu-checkbox:checked').length === $('.stu-checkbox').length) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
        updateStartButton();
    });

    function updateStartButton() {
        const checkedCount = $('.stu-checkbox:checked').length;
        const btn = $('#startBatchBtn');
        if (checkedCount > 0 && studentsData.length > 0) {
            btn.prop('disabled', false).html(`<i class="fas fa-sync-alt"></i> เริ่มอัปเดตชื่อ (${checkedCount} รายการ)`);
        } else {
            btn.prop('disabled', true).html(`<i class="fas fa-sync-alt"></i> เริ่มอัปเดตชื่อ (0 รายการ)`);
        }
    }

    // เริ่มประมวลผล Batch อัปเดตชื่อจริง-นามสกุล
    $('#startBatchBtn').click(function() {
        const selectedIds = $('.stu-checkbox:checked').map(function() { return $(this).val(); }).get();
        if(selectedIds.length === 0) return;
        
        Swal.fire({
            title: 'ยืนยันอัปเดตชื่ออีเมล?',
            html: `ระบบจะทำการอัปเดตชื่อจริงและนามสกุลใน Google Workspace ของนักเรียนจำนวน <b>${selectedIds.length}</b> รายการที่เลือก<br><br><span class="text-rose-600 text-sm">การดำเนินการนี้ใช้เวลาสักครู่ กรุณาอย่าปิดหน้าจอ</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ยืนยันอัปเดตชื่อ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                startBatchProcessing(selectedIds);
            }
        });
    });
    
    async function startBatchProcessing(selectedIds) {
        isProcessing = true;
        $('#startBatchBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลังอัปเดต...');
        $('#loadStudentsBtn, #filterClass, #filterRoom, #selectAll, .stu-checkbox').prop('disabled', true);
        
        $('#progressSection').removeClass('hidden');
        
        let success = 0;
        let fail = 0;
        const selectedStudents = studentsData.filter(stu => selectedIds.includes(stu.Stu_id));
        const total = selectedStudents.length;
        
        for(let i = 0; i < total; i++) {
            const stu = selectedStudents[i];
            const email = `std${stu.Stu_id}@phichai.ac.th`;
            const stuNoStr = stu.Stu_no !== null && stu.Stu_no !== undefined ? String(stu.Stu_no).padStart(2, '0') : '00';
            const firstName = stuNoStr + (stu.Stu_name || '');
            const lastName = stu.Stu_sur || '';
            
            const statusCell = $(`#status-${stu.Stu_id}`);
            
            // Highlight row
            $(`#row-${stu.Stu_id}`).addClass('bg-indigo-50');
            statusCell.html('<i class="fas fa-spinner fa-spin text-indigo-500"></i>');
            
            try {
                const response = await fetch('../api/google_workspace_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=update_name&email=${encodeURIComponent(email)}&firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}`
                });
                
                const result = await response.json();
                
                $(`#row-${stu.Stu_id}`).removeClass('bg-indigo-50');
                
                if(result.status === 'success') {
                    statusCell.html('<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-xs font-bold"><i class="fas fa-check"></i> สำเร็จ</span>');
                    success++;
                } else {
                    statusCell.html(`<span class="bg-rose-100 text-rose-700 px-2 py-1 rounded text-xs font-bold" title="${result.message || 'ผิดพลาด'}"><i class="fas fa-times"></i> ผิดพลาด</span>`);
                    fail++;
                }
            } catch (e) {
                $(`#row-${stu.Stu_id}`).removeClass('bg-indigo-50');
                statusCell.html('<span class="bg-rose-100 text-rose-700 px-2 py-1 rounded text-xs font-bold"><i class="fas fa-exclamation-triangle"></i> Network Error</span>');
                fail++;
            }
            
            // Update Progress
            const done = success + fail;
            const percent = Math.round((done / total) * 100);
            $('#progressText').text(`${done}/${total}`);
            $('#progressPercent').text(`${percent}%`);
            $('#progressBar').css('width', `${percent}%`);
            $('#successCount').text(success);
            $('#failCount').text(fail);
            
            // Wait slightly before next request to not overload GAS
            await new Promise(r => setTimeout(r, 500));
        }
        
        isProcessing = false;
        $('#startBatchBtn').html('<i class="fas fa-check-double"></i> เสร็จสิ้นแล้ว');
        $('#loadStudentsBtn, #filterClass, #filterRoom').prop('disabled', false);
        
        Swal.fire({
            icon: 'success',
            title: 'อัปเดตเสร็จสิ้น!',
            html: `ทำการเปลี่ยนชื่อ-นามสกุลอีเมลเรียบร้อยแล้ว<br><br>สำเร็จ: <b class="text-emerald-500">${success}</b> คน<br>ล้มเหลว: <b class="text-rose-500">${fail}</b> คน`,
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
