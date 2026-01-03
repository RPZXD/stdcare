<?php
$pageTitle = $title ?? 'การคัดกรองนักเรียนรายบุคคล 11 ด้าน';

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
    .stat-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-4px) scale(1.02);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .student-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .student-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.15);
    }
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
    }
    .search-input:focus {
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .slide-in {
        animation: slideIn 0.4s ease-out forwards;
    }
    /* Hide DataTables on mobile */
    @media (max-width: 767px) {
        #record_table_wrapper { display: none !important; }
    }
    @media (min-width: 768px) {
        #mobileCards { display: none !important; }
    }
    /* Print Styles */
    @media print {
        .no-print { display: none !important; }
        .glass-card { background: white !important; box-shadow: none !important; }
    }
</style>

<!-- Page Header -->
<div class="relative mb-6 overflow-hidden">
    <div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-rose-500/20 to-orange-500/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 rounded-full blur-3xl -z-10"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-rose-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-xl floating-icon">
                    <span class="text-2xl md:text-3xl">🔍</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-lg md:text-2xl font-black text-slate-800 dark:text-white">
                    การคัดกรองนักเรียนรายบุคคล 11 ด้าน
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-semibold text-sm mt-1">
                    <i class="fas fa-users text-rose-500 mr-1"></i>
                    ม.<?php echo htmlspecialchars($class); ?>/<?php echo htmlspecialchars($room); ?>
                    <span class="mx-1">•</span>
                    <i class="far fa-calendar-alt text-rose-500 mr-1"></i>
                    ภาคเรียน <?php echo htmlspecialchars($term); ?>/<?php echo htmlspecialchars($pee); ?>
                </p>
            </div>
            <div class="hidden md:block">
                <img src="../dist/img/logo-phicha.png" alt="Logo" class="w-12 h-12 opacity-80">
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
    <div class="stat-card glass-card rounded-xl p-2 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mb-1 md:mb-2 shadow">
            <span class="text-sm md:text-lg">👨‍🎓</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-slate-800 dark:text-white" id="totalStudents">-</p>
        <p class="text-[8px] md:text-xs font-bold text-slate-500 uppercase">ทั้งหมด</p>
    </div>
    <div class="stat-card glass-card rounded-xl p-2 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto bg-gradient-to-br from-emerald-400 to-green-500 rounded-lg flex items-center justify-center mb-1 md:mb-2 shadow">
            <span class="text-sm md:text-lg">✅</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-emerald-600" id="screenedCount">-</p>
        <p class="text-[8px] md:text-xs font-bold text-slate-500 uppercase">คัดกรองแล้ว</p>
    </div>
    <div class="stat-card glass-card rounded-xl p-2 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto bg-gradient-to-br from-rose-400 to-red-500 rounded-lg flex items-center justify-center mb-1 md:mb-2 shadow">
            <span class="text-sm md:text-lg">❌</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-rose-600" id="notScreenedCount">-</p>
        <p class="text-[8px] md:text-xs font-bold text-slate-500 uppercase">รอคัดกรอง</p>
    </div>
    <div class="stat-card glass-card rounded-xl p-2 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg text-center">
        <div class="w-8 h-8 md:w-10 md:h-10 mx-auto bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mb-1 md:mb-2 shadow">
            <span class="text-sm md:text-lg">📊</span>
        </div>
        <p class="text-lg md:text-2xl font-black text-amber-600" id="progressPercent">-</p>
        <p class="text-[8px] md:text-xs font-bold text-slate-500 uppercase">สำเร็จ</p>
    </div>
</div>

<!-- Progress Bar -->
<div class="glass-card rounded-xl p-3 md:p-4 border border-white/30 dark:border-slate-700/50 shadow-lg mb-4 md:mb-6">
    <div class="flex justify-between items-center mb-2">
        <span class="text-xs md:text-sm font-bold text-slate-600 dark:text-slate-300">ความคืบหน้าการคัดกรอง</span>
        <span class="text-xs md:text-sm font-bold text-indigo-600" id="progressText">0/0</span>
    </div>
    <div class="w-full h-2 md:h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
        <div id="progressBar" class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full transition-all duration-700" style="width: 0%"></div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-2 mb-4 md:mb-6 no-print">
    <a href="report_screen_all.php" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-chart-bar"></i>
        <span>📊 สถิติ</span>
    </a>
    <button onclick="printPage()" class="btn-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg">
        <i class="fas fa-print"></i>
        <span>🖨️ พิมพ์</span>
    </button>
</div>

<!-- Search Box (Mobile) -->
<div class="md:hidden mb-4">
    <div class="relative">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" id="mobileSearch" placeholder="🔍 ค้นหานักเรียน..." 
               class="search-input w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium focus:outline-none focus:border-indigo-500">
    </div>
</div>

<!-- Mobile Cards Container -->
<div id="mobileCards" class="space-y-3">
    <!-- Loading State -->
    <div id="mobileLoading" class="glass-card rounded-2xl p-8 text-center">
        <div class="animate-spin w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-4"></div>
        <p class="text-slate-500 font-semibold">กำลังโหลดข้อมูล...</p>
    </div>
</div>

<!-- Desktop Table Card -->
<div class="glass-card rounded-2xl p-4 md:p-6 border border-white/30 dark:border-slate-700/50 shadow-2xl hidden md:block">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center shadow">
            <i class="fas fa-table text-white"></i>
        </div>
        <h3 class="text-lg font-black text-slate-800 dark:text-white">📋 รายชื่อนักเรียน</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table id="record_table" class="w-full display responsive nowrap" style="width:100%">
            <thead>
                <tr class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                    <th class="px-3 py-3 text-center rounded-tl-xl">เลขที่</th>
                    <th class="px-3 py-3 text-center">รหัส</th>
                    <th class="px-3 py-3 text-left">ชื่อ-นามสกุล</th>
                    <th class="px-3 py-3 text-center">คัดกรอง</th>
                    <th class="px-3 py-3 text-center">แปลผล</th>
                    <th class="px-3 py-3 text-center rounded-tr-xl">สถานะ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            </tbody>
        </table>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const classValue = <?= $class ?>;
    const roomValue = <?= $room ?>;
    const peeValue = <?= $pee ?>;
    const termValue = <?= $term ?>;
    let allStudents = [];

    // Print function
    window.printPage = function() {
        $('.no-print').hide();
        setTimeout(() => { window.print(); $('.no-print').show(); }, 100);
    };

    // Create Mobile Card HTML
    function createMobileCard(item, index) {
        const isScreened = item.screen_ishave === 1;
        const statusColor = isScreened ? 'emerald' : 'rose';
        const statusIcon = isScreened ? '✅' : '❌';
        const statusText = isScreened ? 'คัดกรองแล้ว' : 'รอคัดกรอง';
        
        return `
            <div class="student-card glass-card rounded-2xl p-4 border border-white/30 dark:border-slate-700/50 shadow-lg slide-in" style="animation-delay: ${index * 0.05}s" data-name="${item.full_name.toLowerCase()}" data-id="${item.Stu_id}">
                <div class="flex items-start gap-3">
                    <!-- Student Number -->
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold shadow flex-shrink-0">
                        ${item.Stu_no}
                    </div>
                    
                    <!-- Student Info -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm truncate">${item.full_name}</h4>
                        <p class="text-xs text-slate-500 font-mono">${item.Stu_id}</p>
                        
                        <!-- Status Badge -->
                        <div class="mt-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-${statusColor}-100 dark:bg-${statusColor}-900/30 text-${statusColor}-700 dark:text-${statusColor}-300 text-xs font-bold rounded-full">
                                ${statusIcon} ${statusText}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                    ${isScreened ? `
                        <button onclick="openEditModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classValue}', '${roomValue}', '${termValue}', '${peeValue}')"
                                class="btn-action flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-bold text-xs rounded-lg shadow">
                            <i class="fas fa-edit"></i> แก้ไข
                        </button>
                        <button onclick="openResultModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classValue}', '${roomValue}', '${termValue}', '${peeValue}')"
                                class="btn-action flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-gradient-to-r from-purple-400 to-violet-500 text-white font-bold text-xs rounded-lg shadow">
                            <i class="fas fa-chart-pie"></i> แปลผล
                        </button>
                    ` : `
                        <button onclick="openAddModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classValue}', '${roomValue}', '${termValue}', '${peeValue}')"
                                class="btn-action w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-400 to-indigo-500 text-white font-bold text-sm rounded-lg shadow">
                            <i class="fas fa-plus-circle"></i> บันทึกการคัดกรอง
                        </button>
                    `}
                </div>
            </div>
        `;
    }

    // Mobile Search
    $('#mobileSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.student-card').each(function() {
            const name = $(this).data('name');
            const id = $(this).data('id');
            $(this).toggle(name.includes(searchTerm) || id.toString().includes(searchTerm));
        });
    });

    // Load table data
    async function loadTable() {
        try {
            const response = await $.ajax({
                url: '../teacher/api/fetch_screen_classroom.php',
                method: 'GET',
                dataType: 'json',
                data: { class: classValue, room: roomValue, pee: peeValue }
            });

            if (!response.success) {
                Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: 'ไม่สามารถโหลดข้อมูลได้' });
                return;
            }

            allStudents = response.data;

            // Update stats
            const totalStudents = allStudents.length;
            const screenedCount = allStudents.filter(item => item.screen_ishave === 1).length;
            const notScreenedCount = totalStudents - screenedCount;
            const progressPercent = totalStudents > 0 ? Math.round((screenedCount / totalStudents) * 100) : 0;

            $('#totalStudents').text(totalStudents);
            $('#screenedCount').text(screenedCount);
            $('#notScreenedCount').text(notScreenedCount);
            $('#progressPercent').text(progressPercent + '%');
            $('#progressText').text(`${screenedCount}/${totalStudents}`);
            $('#progressBar').css('width', progressPercent + '%');

            // Render Mobile Cards
            $('#mobileLoading').remove();
            let mobileHtml = '';
            allStudents.forEach((item, index) => {
                mobileHtml += createMobileCard(item, index);
            });
            $('#mobileCards').html(mobileHtml);

            // Desktop DataTable
            const table = $('#record_table').DataTable({
                destroy: true,
                pageLength: 25,
                order: [[0, 'asc']],
                responsive: true,
                language: {
                    search: "🔍 ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    paginate: { first: "«", previous: "‹", next: "›", last: "»" }
                }
            });

            table.clear();

            allStudents.forEach((item) => {
                const isHave = item.screen_ishave === 1;
                
                const actionBtn = isHave 
                    ? `<button class="btn-action px-3 py-1.5 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-bold text-xs rounded-lg" onclick="openEditModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classValue}', '${roomValue}', '${termValue}', '${peeValue}')"><i class="fas fa-edit mr-1"></i>แก้ไข</button>`
                    : `<button class="btn-action px-3 py-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 text-white font-bold text-xs rounded-lg" onclick="openAddModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classValue}', '${roomValue}', '${termValue}', '${peeValue}')"><i class="fas fa-plus mr-1"></i>บันทึก</button>`;

                const resultBtn = isHave
                    ? `<button class="btn-action px-3 py-1.5 bg-gradient-to-r from-purple-400 to-violet-500 text-white font-bold text-xs rounded-lg" onclick="openResultModal('${item.Stu_id}', '${item.full_name}', '${item.Stu_no}', '${classValue}', '${roomValue}', '${termValue}', '${peeValue}')"><i class="fas fa-chart-pie mr-1"></i>แปลผล</button>`
                    : `<span class="text-xs text-slate-400">-</span>`;

                const statusBadge = isHave
                    ? `<span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">✅ แล้ว</span>`
                    : `<span class="inline-flex items-center gap-1 px-2 py-1 bg-rose-100 text-rose-700 text-xs font-bold rounded-full">❌ รอ</span>`;

                table.row.add([
                    `<span class="inline-flex items-center justify-center w-7 h-7 bg-slate-100 text-slate-700 font-bold rounded-lg text-sm">${item.Stu_no}</span>`,
                    `<span class="font-mono text-xs text-slate-600">${item.Stu_id}</span>`,
                    `<span class="font-semibold text-slate-800">${item.full_name}</span>`,
                    actionBtn,
                    resultBtn,
                    statusBadge
                ]);
            });

            table.draw();
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: 'เกิดข้อผิดพลาดในการดึงข้อมูล' });
            console.error(error);
        }
    }

    // Modal Functions
    window.openAddModal = function(studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
        $.ajax({
            url: '../teacher/template_form/form_screen.php',
            method: 'GET',
            data: { student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
            success: function(response) {
                const modalHtml = `
                    <div class="modal fade" id="screenModal" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
                                <div class="modal-header bg-gradient-to-r from-indigo-500 to-purple-600 text-white border-0 py-4">
                                    <h5 class="modal-title font-bold"><i class="fas fa-clipboard-list mr-2"></i>แบบฟอร์มคัดกรอง 11 ด้าน</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 bg-slate-50">${response}</div>
                                <div class="modal-footer bg-white border-0 py-3">
                                    <button type="button" class="px-4 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('body').append(modalHtml);
                const modal = new bootstrap.Modal(document.getElementById('screenModal'));
                modal.show();

                $('#saveScreen').on('click', function() {
                    Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    $.ajax({
                        url: '../teacher/api/insert_screen.php',
                        method: 'POST',
                        data: $('#screenForm').serialize(),
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({ icon: 'success', title: 'สำเร็จ!', timer: 1500, showConfirmButton: false }).then(() => {
                                    modal.hide();
                                    $('#screenModal').remove();
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({ icon: 'error', title: 'ข้อผิดพลาด', text: res.message });
                            }
                        }
                    });
                });

                document.getElementById('screenModal').addEventListener('hidden.bs.modal', function() { $(this).remove(); });
            }
        });
    };

    window.openEditModal = function(studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
        $.ajax({
            url: '../teacher/template_form/form_screen_edit.php',
            method: 'GET',
            data: { student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
            success: function(response) {
                const modalHtml = `
                    <div class="modal fade" id="editscreenModal" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl">
                                <div class="modal-header bg-gradient-to-r from-amber-500 to-orange-600 text-white border-0 py-4">
                                    <h5 class="modal-title font-bold"><i class="fas fa-edit mr-2"></i>แก้ไขข้อมูลการคัดกรอง</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 bg-slate-50">${response}</div>
                                <div class="modal-footer bg-white border-0 py-3 gap-2">
                                    <button type="button" class="px-4 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                                    <button type="button" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl" id="updateScreen"><i class="fas fa-save mr-2"></i>บันทึก</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('body').append(modalHtml);
                const modal = new bootstrap.Modal(document.getElementById('editscreenModal'));
                modal.show();

                $('#updateScreen').on('click', function() {
                    Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    $.ajax({
                        url: '../teacher/api/update_screen.php',
                        method: 'POST',
                        data: $('#screenEditForm').serialize(),
                        success: function() {
                            Swal.fire({ icon: 'success', title: 'สำเร็จ!', timer: 1500, showConfirmButton: false }).then(() => {
                                modal.hide();
                                $('#editscreenModal').remove();
                                window.location.reload();
                            });
                        }
                    });
                });

                document.getElementById('editscreenModal').addEventListener('hidden.bs.modal', function() { $(this).remove(); });
            }
        });
    };

    window.openResultModal = function(studentId, studentName, studentNo, studentClass, studentRoom, Term, Pee) {
        $.ajax({
            url: '../teacher/template_form/form_screen_result.php',
            method: 'GET',
            data: { student_id: studentId, student_name: studentName, student_no: studentNo, student_class: studentClass, student_room: studentRoom, pee: Pee, term: Term },
            success: function(response) {
                const modalHtml = `
                    <div class="modal fade" id="resultModal" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 rounded-3xl overflow-hidden shadow-2xl" id="resultModalContent">
                                <div class="modal-header bg-gradient-to-r from-purple-500 to-violet-600 text-white border-0 py-4">
                                    <h5 class="modal-title font-bold"><i class="fas fa-chart-pie mr-2"></i>แปลผลการคัดกรอง 11 ด้าน</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 bg-slate-50">${response}</div>
                                <div class="modal-footer bg-white border-0 py-3 gap-2">
                                    <button type="button" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl" id="printResultModal"><i class="fas fa-print mr-2"></i>พิมพ์</button>
                                    <button type="button" class="px-4 py-2 bg-slate-400 text-white font-bold rounded-xl" data-bs-dismiss="modal">ปิด</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('body').append(modalHtml);
                const modal = new bootstrap.Modal(document.getElementById('resultModal'));
                modal.show();

                $('#printResultModal').on('click', function() {
                    const printContents = document.querySelector('#resultModalContent').innerHTML;
                    const printWindow = window.open('', '', 'height=800,width=900');
                    printWindow.document.write('<html><head><title>พิมพ์แปลผลการคัดกรอง</title>');
                    $('link[rel=stylesheet]').each(function() {
                        printWindow.document.write('<link rel="stylesheet" href="' + $(this).attr('href') + '" />');
                    });
                    printWindow.document.write('<style>@media print { .modal-footer { display: none !important; } }</style>');
                    printWindow.document.write('</head><body>' + printContents + '</body></html>');
                    printWindow.document.close();
                    setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
                });

                document.getElementById('resultModal').addEventListener('hidden.bs.modal', function() { $(this).remove(); });
            }
        });
    };

    loadTable();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
