<?php
/**
 * View: Admin Log
 * Modern UI with Tailwind CSS & DataTables
 */
ob_start();
?>

<div class="animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3 tracking-tight">
                <span class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl text-xl">
                    <i class="fas fa-history"></i>
                </span>
                ประวัติ <span class="text-indigo-600 italic">การใช้งาน</span>
            </h2>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 italic pl-15">System Activity Logs & Audit Trail</p>
        </div>
        <div class="px-4 py-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-2">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><i class="fas fa-clock mr-1"></i>Update</span>
            <span class="text-sm font-black text-indigo-600"><?php echo date('d/m/Y H:i'); ?></span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                <i class="fas fa-filter"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-800 dark:text-white">ตัวกรองข้อมูล</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Search & Filter Logs</p>
            </div>
        </div>
        
        <form id="logFilterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">User ID</label>
                <input type="text" id="user_id" placeholder="รหัสผู้ใช้" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Role</label>
                <input type="text" id="role" placeholder="บทบาท" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Action</label>
                <select id="action" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
                    <option value="">ทั้งหมด</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Status</label>
                <select id="status" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
                    <option value="">ทั้งหมด</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">จากวันที่</label>
                <input type="date" id="date_from" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ถึงวันที่</label>
                <input type="date" id="date_to" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
            </div>
            <div class="md:col-span-2 lg:col-span-3 flex justify-center pt-4">
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-black text-sm shadow-lg shadow-indigo-600/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table Section -->
    <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border-t border-white/50">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white">
                <i class="fas fa-table"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-800 dark:text-white">ข้อมูลประวัติการใช้งาน</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Activity Log Records</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table id="logTable" class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="bg-indigo-50/50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-xl text-center w-12"></th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">วันที่-เวลา</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ผู้ใช้งาน</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">บทบาท</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">การกระทำ</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-r-xl text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-slate-700 dark:text-slate-300">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load filters
    $.getJSON('api/log_filters.php').done(function(data) {
        if (data.success) {
            const actionSelect = $('#action');
            actionSelect.empty().append('<option value="">ทั้งหมด</option>');
            data.actions.forEach(a => actionSelect.append(`<option value="${a.value}">${a.label}</option>`));
            
            const statusSelect = $('#status');
            statusSelect.empty().append('<option value="">ทั้งหมด</option>');
            data.statuses.forEach(s => statusSelect.append(`<option value="${s.value}">${s.label}</option>`));
        }
    });

    function formatDetails(d) {
        return `<div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border-l-4 border-indigo-500 m-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Message</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 p-3 rounded-xl">${d.message || '-'}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">IP Address</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 p-3 rounded-xl font-mono">${d.ip || '-'}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">User Agent</p>
                    <p class="text-xs text-slate-500 bg-white dark:bg-slate-900 p-3 rounded-xl break-all">${d.user_agent || '-'}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">URL</p>
                    <p class="text-xs text-slate-500 bg-white dark:bg-slate-900 p-3 rounded-xl break-all">${d.url || '-'}</p>
                </div>
            </div>
        </div>`;
    }

    var logTable = $('#logTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'api/log_data.php',
            type: 'GET',
            data: function(d) {
                d.user_id = $('#user_id').val();
                d.role = $('#role').val();
                d.action = $('#action').val();
                d.status = $('#status').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        columns: [
            {
                className: 'text-center',
                orderable: false,
                data: null,
                defaultContent: '<button class="w-8 h-8 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all"><i class="fas fa-plus text-xs"></i></button>',
                width: '5%'
            },
            { data: 'datetime', className: 'text-sm' },
            { data: 'userId', className: 'text-sm' },
            { data: 'role', className: 'text-sm' },
            { data: 'action', orderable: false },
            { data: 'status', className: 'text-center', orderable: false }
        ],
        order: [[1, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
        },
        drawCallback: function() {
            $('.dataTables_paginate .paginate_button').addClass('!rounded-xl !mx-1 !border-none !px-4 !py-2 !font-bold !text-sm');
            $('.dataTables_paginate .paginate_button.current').addClass('!bg-indigo-600 !text-white');
        }
    });

    $('#logFilterForm').on('submit', function(e) {
        e.preventDefault();
        logTable.ajax.reload();
    });

    $('#logTable tbody').on('click', 'td:first-child button', function() {
        var tr = $(this).closest('tr');
        var row = logTable.row(tr);
        var btn = $(this);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            btn.html('<i class="fas fa-plus text-xs"></i>').removeClass('bg-rose-600').addClass('bg-indigo-600');
        } else {
            row.child(formatDetails(row.data())).show();
            tr.addClass('shown');
            btn.html('<i class="fas fa-minus text-xs"></i>').removeClass('bg-indigo-600').addClass('bg-rose-600');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
