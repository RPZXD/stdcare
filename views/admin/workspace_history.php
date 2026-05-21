<?php
/**
 * View: Admin Workspace History
 */
ob_start();
$pageTitle = "ประวัติรหัสผ่าน Workspace";
$activePage = "workspace_history"; 
?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 text-indigo-600 border border-indigo-500/20 mb-3">
            <i class="fas fa-history text-sm"></i>
            <span class="text-xs font-bold tracking-wide">WORKSPACE HISTORY</span>
        </div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">ประวัติการตั้งรหัสผ่าน <span class="text-indigo-600">Google Workspace</span></h1>
        <p class="text-slate-500 mt-2 font-medium">ดูรหัสผ่านที่ถูกอัปเดตผ่านระบบ เพื่อใช้แจกจ่ายให้นักเรียนเข้าใช้งาน</p>
    </div>
</div>

<div class="bg-white/80 backdrop-blur-xl border border-slate-200/60 rounded-3xl p-5 sm:p-6 shadow-xl shadow-slate-200/30 mb-6">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="w-full md:w-auto flex-1 md:flex-none">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">ระดับชั้น</label>
            <select id="filterClass" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 p-2.5 transition-all">
                <option value="">-- ทั้งหมด --</option>
                <option value="1">มัธยมศึกษาปีที่ 1</option>
                <option value="2">มัธยมศึกษาปีที่ 2</option>
                <option value="3">มัธยมศึกษาปีที่ 3</option>
                <option value="4">มัธยมศึกษาปีที่ 4</option>
                <option value="5">มัธยมศึกษาปีที่ 5</option>
                <option value="6">มัธยมศึกษาปีที่ 6</option>
            </select>
        </div>
        <div class="w-full md:w-auto flex-1 md:flex-none">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">ห้อง</label>
            <select id="filterRoom" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 p-2.5 transition-all">
                <option value="">-- ทั้งหมด --</option>
                <?php for($i=1; $i<=15; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button id="loadHistoryBtn" class="w-full md:w-auto bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
            <i class="fas fa-search"></i> ค้นหาประวัติ
        </button>
        <button id="printBtn" class="w-full md:w-auto bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-md shadow-indigo-500/30 flex items-center justify-center gap-2 ml-auto" style="display:none;">
            <i class="fas fa-print"></i> พิมพ์รหัสผ่านแจก
        </button>
    </div>
</div>

<div class="bg-white/80 backdrop-blur-xl border border-slate-200/60 rounded-3xl shadow-xl shadow-slate-200/30 overflow-hidden">
    <div class="p-0">
        <table id="historyTable" class="admin-responsive-table w-full text-left border-collapse" style="width:100%">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ชั้น/ห้อง</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">เลขที่</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">รหัสนักเรียน</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ชื่อ-สกุล</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">อีเมล Workspace</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">รหัสผ่านปัจจุบัน</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data loaded via DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- Print Template (Hidden) -->
<div id="printTemplate" class="hidden"></div>

<script>
$(document).ready(function() {
    let dataTable = null;

    $('#loadHistoryBtn').click(function() {
        const major = $('#filterClass').val();
        const room = $('#filterRoom').val();
        
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> กำลังโหลด...').prop('disabled', true);
        
        $.ajax({
            url: '../controllers/StudentController.php',
            type: 'GET',
            data: {
                action: 'list_workspace_history',
                class: major,
                room: room
            },
            success: function(response) {
                let data = response.data || [];
                renderTable(data);
                
                if(data.length > 0) {
                    $('#printBtn').show();
                } else {
                    $('#printBtn').hide();
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

    function renderTable(data) {
        if (dataTable) {
            dataTable.destroy();
        }

        const tbody = $('#historyTable tbody');
        tbody.empty();

        data.forEach(stu => {
            const email = `std${stu.Stu_id}@phichai.ac.th`;
            const pwd = stu.google_password;
            
            tbody.append(`
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="p-4 text-sm font-medium text-slate-800">ม.${stu.Stu_major}/${stu.Stu_room}</td>
                    <td class="p-4 text-sm text-slate-500">${stu.Stu_no || '-'}</td>
                    <td class="p-4 text-sm font-medium text-slate-800">${stu.Stu_id}</td>
                    <td class="p-4 text-sm">${stu.Stu_pre || ''}${stu.Stu_name || ''} ${stu.Stu_sur || ''}</td>
                    <td class="p-4 text-sm text-indigo-600 font-medium">${email}</td>
                    <td class="p-4 text-sm text-emerald-600 font-mono font-bold">${pwd}</td>
                </tr>
            `);
        });

        dataTable = $('#historyTable').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
            dom: '<"flex flex-col md:flex-row justify-between items-center p-4"lf>rt<"flex flex-col md:flex-row justify-between items-center p-4"ip>',
            pageLength: 25,
            order: [[0, 'asc'], [1, 'asc']]
        });
    }
    
    // พิมพ์รหัสผ่าน
    $('#printBtn').click(function() {
        if (!dataTable) return;
        
        let printContent = `
        <html>
        <head>
            <title>พิมพ์รหัสผ่าน Google Workspace</title>
            <style>
                body { font-family: 'Sarabun', sans-serif; font-size: 14px; margin: 20px; }
                h2 { text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .password { font-family: monospace; font-weight: bold; }
                @media print {
                    button { display: none; }
                }
            </style>
        </head>
        <body>
            <h2>ข้อมูลบัญชี Google Workspace นักเรียน</h2>
            <p><strong>ระดับชั้น:</strong> ม.${$('#filterClass').val() || 'ทั้งหมด'} <strong>ห้อง:</strong> ${$('#filterRoom').val() || 'ทั้งหมด'}</p>
            <table>
                <thead>
                    <tr>
                        <th width="5%">ที่</th>
                        <th width="10%">รหัส</th>
                        <th width="25%">ชื่อ - สกุล</th>
                        <th width="35%">อีเมล (@phichai.ac.th)</th>
                        <th width="25%">รหัสผ่าน</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        // Use all filtered data from datatable, not just current page
        let count = 1;
        dataTable.rows({ search: 'applied' }).every(function() {
            let rowData = this.data();
            
            // Helper function to safely strip HTML tags
            let getText = (htmlStr) => {
                let div = document.createElement("div");
                div.innerHTML = htmlStr;
                return div.textContent || div.innerText || "";
            };
            
            printContent += `
                <tr>
                    <td style="text-align: center;">${count++}</td>
                    <td>${getText(rowData[2])}</td>
                    <td>${getText(rowData[3])}</td>
                    <td>${getText(rowData[4])}</td>
                    <td class="password">${getText(rowData[5])}</td>
                </tr>
            `;
        });
        
        printContent += `
                </tbody>
            </table>
            <div style="margin-top:20px; text-align:center;">
                <p>พิมพ์เมื่อ: ${new Date().toLocaleString('th-TH')}</p>
            </div>
            <script>window.onload = function() { window.print(); }<\/script>
        </body>
        </html>
        `;
        
        let printWin = window.open('', '_blank');
        printWin.document.write(printContent);
        printWin.document.close();
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
