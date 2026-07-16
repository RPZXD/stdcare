<?php
/**
 * View: Weekend Attendance Report
 * Modern UI with Tailwind CSS & DataTables
 */
ob_start();
$pageTitle = "รายงานประวัติการสแกนบัตร เสาร์-อาทิตย์";
$activePage = "weekend_attendance";
?>

<div class="animate-fadeIn">
    <!-- Page Header -->
    <?php 
    $headerData = [
        'title' => 'รายงานสแกนบัตร <span class="text-orange-600 italic">เสาร์-อาทิตย์</span>',
        'subtitle' => 'Weekend RFID Attendance Scan History',
        'icon' => 'fa-calendar-alt',
        'color' => 'orange'
    ];
    include __DIR__ . '/../components/ui_header.php'; 
    ?>

    <!-- Filter Section -->
    <div class="glass-effect rounded-2xl lg:rounded-[2rem] p-4 lg:p-6 border border-white/50 shadow-xl mb-6 md:mb-8 bg-white/80 dark:bg-slate-900/80">
        <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
            <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center text-white">
                <i class="fas fa-filter"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-slate-800 dark:text-white">ตัวกรองข้อมูล</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Filter Weekend Logs</p>
            </div>
        </div>
        
        <form id="weekendFilterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ประเภทสแกน</label>
                <select id="scan_type" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-orange-500/20 outline-none transition-all">
                    <option value="">ทั้งหมด</option>
                    <option value="arrival">🔵 เข้าเรียน</option>
                    <option value="leave">🔴 ออกโรงเรียน</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">จากวันที่</label>
                <input type="date" id="date_from" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-orange-500/20 outline-none transition-all">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ถึงวันที่</label>
                <input type="date" id="date_to" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-orange-500/20 outline-none transition-all">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-black text-sm shadow-lg shadow-orange-600/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> กรองข้อมูล
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="glass-effect rounded-2xl lg:rounded-[2.5rem] p-4 lg:p-8 shadow-xl border-t border-white/50 bg-white/80 dark:bg-slate-900/80">
        <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
            <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white">
                <i class="fas fa-list-ul"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-slate-800 dark:text-white">ข้อมูลการสแกนวันหยุดเสาร์-อาทิตย์</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Weekend Scan Records</p>
            </div>
        </div>
        
        <div class="admin-table-shell overflow-x-auto">
            <table id="weekendTable" class="admin-responsive-table w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="bg-orange-50/50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-16 rounded-l-xl">รูปโปรไฟล์</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">รหัสนักเรียน</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ-นามสกุล</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ระดับชั้น</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">วันเวลาสแกน</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">ประเภท</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center rounded-r-xl">สถานะ (เทียบวันปกติ)</th>
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
    var weekendTable = $('#weekendTable').DataTables ? $('#weekendTable') : $('#weekendTable');
    
    var dataTable = $('#weekendTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'api/weekend_data.php',
            type: 'GET',
            data: function(d) {
                d.scan_type = $('#scan_type').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        columns: [
            { 
                data: 'photo',
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `<img src="${data}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-md mx-auto" onerror="this.src='../dist/img/logo-phicha.png'">`;
                }
            },
            { data: 'student_id', className: 'text-sm' },
            { data: 'fullname', className: 'text-sm' },
            { data: 'class', className: 'text-sm' },
            { data: 'formatted_date', className: 'text-sm' },
            { 
                data: 'scan_type', 
                className: 'text-center text-sm',
                render: function(data, type, row) {
                    if (row.scan_type.includes('เข้าเรียน')) {
                        return `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">${data}</span>`;
                    } else {
                        return `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">${data}</span>`;
                    }
                }
            },
            { 
                data: 'status_type', 
                className: 'text-center text-sm',
                render: function(data, type, row) {
                    switch(data) {
                        case 'normal_arrival':
                            return '<span class="inline-block px-3 py-1 bg-green-100 text-green-800 border border-green-200 rounded-full text-xs font-semibold">✅ มาเรียนปกติ</span>';
                        case 'late_arrival':
                            return '<span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 border border-yellow-200 rounded-full text-xs font-semibold">⚠️ มาสาย</span>';
                        case 'absent_arrival':
                            return '<span class="inline-block px-3 py-1 bg-rose-100 text-rose-800 border border-rose-200 rounded-full text-xs font-semibold">❌ ขาดเรียน</span>';
                        case 'early_leave':
                            return '<span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 border border-indigo-200 rounded-full text-xs font-semibold">🏃 กลับก่อนเวลา</span>';
                        case 'normal_leave':
                            return '<span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 border border-purple-200 rounded-full text-xs font-semibold">🏠 กลับปกติ</span>';
                        default:
                            return '<span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 border border-slate-200 rounded-full text-xs font-semibold">ไม่ทราบสถานะ</span>';
                    }
                }
            }
        ],
        order: [[4, 'desc']], // Order by scan_timestamp DESC
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
        },
        drawCallback: function() {
            $('.dataTables_paginate .paginate_button').addClass('!rounded-xl !mx-1 !border-none !px-4 !py-2 !font-bold !text-sm');
            $('.dataTables_paginate .paginate_button.current').addClass('!bg-orange-600 !text-white');
        }
    });

    $('#weekendFilterForm').on('submit', function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
