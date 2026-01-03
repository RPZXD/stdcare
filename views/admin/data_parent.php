<?php
/**
 * View: Admin Parent Data
 * Modern UI with Tailwind CSS, Glassmorphism & Full CRUD
 */
ob_start();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-teal-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-users"></i>
                </span>
                จัดการ <span class="text-teal-600 italic">ข้อมูลผู้ปกครอง</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">Parent Data Management</p>
        </div>
    </div>

    <!-- CSV Upload Section -->
    <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl mb-8">
        <h4 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-file-csv text-teal-500"></i> อัปเดตข้อมูลด้วย CSV
        </h4>
        <form id="csvUploadForm" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เลือกไฟล์ CSV</label>
                <input type="file" name="csv_file" accept=".csv" required class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-teal-500 file:text-white file:font-bold file:cursor-pointer">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-teal-600 text-white rounded-xl font-bold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                <i class="fas fa-upload"></i> อัปโหลด
            </button>
            <button type="button" id="downloadTemplateBtn" class="px-5 py-2.5 bg-slate-600 text-white rounded-xl font-bold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                <i class="fas fa-download"></i> โหลดข้อมูล (CSV)
            </button>
        </form>
    </div>

    <!-- Filter Toolbar -->
    <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl mb-8">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-filter"></i>
                </div>
                <span class="text-sm font-black text-slate-600 dark:text-slate-300">ตัวกรอง</span>
            </div>
            
            <div class="flex-1 flex flex-wrap gap-3">
                <select id="filterClass" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none min-w-[120px]">
                    <option value="">ทุกชั้น</option>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?= $i ?>">ม.<?= $i ?></option>
                    <?php endfor; ?>
                </select>
                
                <select id="filterRoom" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none min-w-[120px]">
                    <option value="">ทุกห้อง</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>">ห้อง <?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <button id="filterButton" class="px-5 py-2.5 bg-teal-600 text-white rounded-xl font-bold shadow-lg hover:scale-105 transition-all">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/30 text-teal-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ทั้งหมด</p>
                    <h3 id="totalRecords" class="text-2xl font-black text-slate-800 dark:text-white">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/30 text-sky-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-male text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">มีข้อมูลบิดา</p>
                    <h3 id="fatherCount" class="text-2xl font-black text-sky-600">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 text-pink-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-female text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">มีข้อมูลมารดา</p>
                    <h3 id="motherCount" class="text-2xl font-black text-pink-600">0</h3>
                </div>
            </div>
        </div>
        <div class="glass-effect p-5 rounded-2xl border border-white/50 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-phone text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">มีเบอร์โทร</p>
                    <h3 id="phoneCount" class="text-2xl font-black text-amber-600">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="glass-effect rounded-[2.5rem] p-6 md:p-8 shadow-xl border-t border-white/50">
        <div class="overflow-x-auto">
            <table id="parentTable" class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="bg-teal-50/50 dark:bg-slate-800/50">
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl">รหัส</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อนักเรียน</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ชั้น/ห้อง</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">บิดา</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">มารดา</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ผู้ปกครอง</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">เบอร์โทร</th>
                        <th class="px-3 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center w-20">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-slate-700 dark:text-slate-300"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Parent Modal -->
<div class="modal fade" id="editParentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content !rounded-3xl !border-0 !shadow-2xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-teal-500 to-cyan-600 text-white !border-0 p-6">
                <h5 class="modal-title text-xl font-black flex items-center gap-3" id="editParentModalLabel">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลผู้ปกครอง
                </h5>
                <button type="button" class="close text-white text-2xl" data-dismiss="modal">&times;</button>
            </div>
            <form id="editParentForm">
                <input type="hidden" id="editStu_id" name="editStu_id">
                <div class="modal-body p-6 md:p-8 bg-gradient-to-br from-white to-teal-50 dark:from-slate-900 dark:to-slate-800 max-h-[70vh] overflow-y-auto">
                    
                    <!-- Father Info -->
                    <h6 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-teal-200 pb-2">
                        <i class="fas fa-male text-sky-500"></i> ข้อมูลบิดา
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อบิดา</label>
                            <input type="text" id="editFather_name" name="editFather_name" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">อาชีพ</label>
                            <input type="text" id="editFather_occu" name="editFather_occu" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">รายได้/เดือน</label>
                            <input type="number" id="editFather_income" name="editFather_income" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                    </div>

                    <!-- Mother Info -->
                    <h6 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-teal-200 pb-2">
                        <i class="fas fa-female text-pink-500"></i> ข้อมูลมารดา
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อมารดา</label>
                            <input type="text" id="editMother_name" name="editMother_name" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">อาชีพ</label>
                            <input type="text" id="editMother_occu" name="editMother_occu" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">รายได้/เดือน</label>
                            <input type="number" id="editMother_income" name="editMother_income" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                    </div>

                    <!-- Guardian Info -->
                    <h6 class="text-sm font-black text-slate-700 dark:text-white mb-4 flex items-center gap-2 border-b border-teal-200 pb-2">
                        <i class="fas fa-user-shield text-amber-500"></i> ข้อมูลผู้ปกครอง
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ชื่อผู้ปกครอง</label>
                            <input type="text" id="editPar_name" name="editPar_name" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ความเกี่ยวข้อง</label>
                            <input type="text" id="editPar_relate" name="editPar_relate" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">เบอร์โทร</label>
                            <input type="tel" id="editPar_phone" name="editPar_phone" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">อาชีพ</label>
                            <input type="text" id="editPar_occu" name="editPar_occu" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">รายได้/เดือน</label>
                            <input type="number" id="editPar_income" name="editPar_income" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ที่อยู่</label>
                            <input type="text" id="editPar_addr" name="editPar_addr" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-teal-500/20 outline-none">
                        </div>
                    </div>
                </div>
                <div class="modal-footer !border-0 p-6 bg-slate-50 dark:bg-slate-800 flex justify-end gap-3">
                    <button type="button" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold" data-dismiss="modal">ยกเลิก</button>
                    <button type="button" id="submitEditParentForm" class="px-6 py-3 bg-teal-600 text-white rounded-xl font-bold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-edit { padding: 6px 10px; border: none; background: #f59e0b; color: white; border-radius: 8px; cursor: pointer; font-size: 12px; transition: transform 0.15s; }
.btn-edit:hover { transform: scale(1.1); background: #d97706; }

@media (max-width: 768px) {
    #parentTable { font-size: 12px; }
    #parentTable th, #parentTable td { padding: 8px 4px; }
}
</style>

<script>
const API_URL = '../controllers/ParentController.php';
let parentTable;
let allParentsData = [];

$(document).ready(function() {
    parentTable = $('#parentTable').DataTable({
        processing: true,
        serverSide: false,
        deferRender: true,
        ajax: {
            url: API_URL + '?action=list',
            dataSrc: function(json) {
                allParentsData = json;
                setTimeout(() => updateStats(json), 0);
                return json;
            }
        },
        columns: [
            { data: 'Stu_id', className: 'font-bold text-teal-600', width: '90px' },
            {
                data: 'Stu_name',
                render: function(data, type, row) {
                    return `${data || ''} ${row.Stu_sur || ''}`;
                }
            },
            {
                data: 'Stu_major',
                render: function(data, type, row) {
                    return `ม.${data || '-'}/${row.Stu_room || '-'}`;
                },
                className: 'text-center',
                width: '80px'
            },
            { data: 'Father_name', render: d => d || '-' },
            { data: 'Mother_name', render: d => d || '-' },
            { data: 'Par_name', render: d => d || '-' },
            { 
                data: 'Par_phone', 
                render: d => d ? `<span class="text-teal-600">${d}</span>` : '<span class="text-slate-400">-</span>',
                width: '100px'
            },
            {
                data: 'Stu_id',
                render: function(data) {
                    return `<button class="btn-edit editParentBtn" data-id="${data}">✏️ แก้ไข</button>`;
                },
                orderable: false,
                className: 'text-center'
            }
        ],
        order: [[0, 'asc']],
        pageLength: 50,
        lengthMenu: [25, 50, 100],
        language: {
            processing: '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-teal-600"></i></div>',
            zeroRecords: 'ไม่พบข้อมูล',
            info: 'แสดง _START_-_END_ จาก _TOTAL_',
            infoEmpty: 'ไม่มีข้อมูล',
            lengthMenu: 'แสดง _MENU_ รายการ',
            search: 'ค้นหา:',
            paginate: { first: '«', previous: '‹', next: '›', last: '»' }
        }
    });

    function updateStats(data) {
        $('#totalRecords').text(data.length);
        $('#fatherCount').text(data.filter(r => r.Father_name && r.Father_name.trim()).length);
        $('#motherCount').text(data.filter(r => r.Mother_name && r.Mother_name.trim()).length);
        $('#phoneCount').text(data.filter(r => r.Par_phone && r.Par_phone.trim()).length);
    }

    // Custom search filter
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'parentTable') return true;
        const row = allParentsData[dataIndex];
        if (!row) return true;
        
        const cls = $('#filterClass').val();
        const room = $('#filterRoom').val();
        
        if (cls && String(row.Stu_major) !== cls) return false;
        if (room && String(row.Stu_room) !== room) return false;
        return true;
    });

    // Filter button
    $('#filterButton').click(function() {
        parentTable.draw();
    });

    // Load parents with filter
    window.loadParents = function() {
        const cls = $('#filterClass').val();
        const room = $('#filterRoom').val();
        parentTable.ajax.url(`${API_URL}?action=list&class=${cls}&room=${room}`).load();
    };

    // Edit Parent
    $('#parentTable').on('click', '.editParentBtn', async function() {
        const id = $(this).data('id');
        const res = await fetch(`${API_URL}?action=get&id=${id}`);
        const p = await res.json();
        
        if (p && p.Stu_id) {
            $('#editStu_id').val(p.Stu_id);
            $('#editFather_name').val(p.Father_name || '');
            $('#editFather_occu').val(p.Father_occu || '');
            $('#editFather_income').val(p.Father_income || '');
            $('#editMother_name').val(p.Mother_name || '');
            $('#editMother_occu').val(p.Mother_occu || '');
            $('#editMother_income').val(p.Mother_income || '');
            $('#editPar_name').val(p.Par_name || '');
            $('#editPar_relate').val(p.Par_relate || '');
            $('#editPar_phone').val(p.Par_phone || '');
            $('#editPar_occu').val(p.Par_occu || '');
            $('#editPar_income').val(p.Par_income || '');
            $('#editPar_addr').val(p.Par_addr || '');
            $('#editParentModalLabel').html(`<i class="fas fa-edit"></i> แก้ไขข้อมูลผู้ปกครองของ: ${p.Stu_name}`);
            $('#editParentModal').modal('show');
        }
    });

    // Submit Edit
    $('#submitEditParentForm').click(async function() {
        const formData = new FormData($('#editParentForm')[0]);
        const res = await fetch(`${API_URL}?action=update`, { method: 'POST', body: formData });
        const result = await res.json();
        
        if (result.success) {
            $('#editParentModal').modal('hide');
            parentTable.ajax.reload();
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'บันทึกข้อมูลเรียบร้อย', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message || 'ไม่สามารถบันทึกข้อมูลได้' });
        }
    });

    // CSV Upload
    $('#csvUploadForm').submit(async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'กำลังอัปโหลด...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const res = await fetch(`${API_URL}?action=upload_csv`, { method: 'POST', body: formData });
        const result = await res.json();

        if (result.status === 'completed') {
            Swal.fire({
                icon: 'success',
                title: 'อัปโหลดสำเร็จ!',
                html: `สำเร็จ: <b>${result.report.success}</b> รายการ<br>ล้มเหลว: <b>${result.report.failed}</b> รายการ`
            });
            parentTable.ajax.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message || 'ไม่สามารถอัปโหลดได้' });
        }
    });

    // Download Template
    $('#downloadTemplateBtn').click(function() {
        const cls = $('#filterClass').val();
        const room = $('#filterRoom').val();
        window.location.href = `${API_URL}?action=download_template&class=${cls}&room=${room}`;
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
