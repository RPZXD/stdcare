<?php
// (1) !! KEV: แก้ไขส่วน PHP ด้านบน !!
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php");
include_once("../class/Utils.php");
include_once("../config/Setting.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok'); // (เพิ่ม)

$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (สิ้นสุดการแก้ไข PHP)


if (isset($_SESSION['Officer_login'])) {
    $userid = $_SESSION['Officer_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

$term = $user->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
$pee = $user->getPee() ?: (date('Y') + 543);
$setting = new Setting();

require_once('header.php');


?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">ลืมบัตร</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">

            <div class="container-fluid">
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Load Tailwind CDN for styling (play CDN) -->
                                <script src="https://cdn.tailwindcss.com"></script>
                                <div class="mb-4">
                                    <label for="search-stu" class="block text-sm font-medium text-gray-700">🔎 ค้นหานักเรียน (ID, ชื่อ, นามสกุล)</label>
                                    <div class="relative mt-1">
                                        <input type="text" id="search-stu" autocomplete="off" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2" placeholder="พิมพ์รหัสหรือชื่อเพื่อค้นหา...">
                                        <div id="search-suggestions" class="hidden absolute z-50 w-full bg-white border border-gray-200 rounded-md mt-1 max-h-60 overflow-auto shadow-lg"></div>
                                    </div>
                                </div>
                            <div id="stu-preview" class="mt-3" style="display:none;">
                                <!-- Responsive preview: stack on small screens, row on md+ -->
                                <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-3 md:space-y-0 text-center md:text-left">
                                    <div class="flex-shrink-0 mx-auto md:mx-0">
                                        <img id="stu-photo" src="../assets/images/profile.png" alt="photo" class="w-32 h-32 md:w-36 lg:w-48 md:h-36 lg:h-48 rounded-full object-cover border-2 border-gray-200 cursor-pointer">
                                    </div>
                                    <div class="flex-1">
                                        <h5 id="stu-name" class="text-lg font-semibold">-</h5>
                                        <p id="stu-class" class="text-sm text-gray-600">-</p>
                                        <p id="stu-id" class="text-sm text-gray-500">-</p>
                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
                                            <button id="btn-manual-arrival" class="w-full md:w-auto px-3 py-2 rounded bg-green-500 text-white hover:bg-green-600" disabled>✅ เช็คเข้า</button>
                                            <button id="btn-manual-leave" class="w-full md:w-auto px-3 py-2 rounded bg-red-500 text-white hover:bg-red-600" disabled>🔴 เช็คออก</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 text-center md:text-left">
                                    <strong>จำนวนวันที่ลืมบัตรในเทอมนี้: <span id="forgot-count">0</span></strong>
                                </div>
                            </div>
                            <div id="stu-empty" class="mt-3 text-muted">ค้นหาด้วยรหัสนักเรียนเพื่อบันทึกกรณีลืมบัตร</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-4">
                                <h6 class="mb-2">📋 ประวัติการลืมบัตร</h6>
                                <div class="mb-2 text-sm text-gray-600">สามารถส่งออกเป็น CSV / Excel ได้จากปุ่มด้านบน</div>
                                <div class="overflow-x-auto bg-white rounded-md p-2 border">
                                    <table id="forgotTable" class="min-w-full divide-y divide-gray-200" style="width:100%">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-2 py-2 text-left">รหัส</th>
                                                <th class="px-2 py-2 text-left">ชื่อ-สกุล</th>
                                                <th class="px-2 py-2 text-left">ชั้น</th>
                                                <th class="px-2 py-2 text-left">วันที่ลืม</th>
                                                <th class="px-2 py-2 text-left">เจ้าหน้าที่</th>
                                                <th class="px-2 py-2 text-left">หมายเหตุ</th>
                                                <th class="px-2 py-2 text-left">สร้างเมื่อ</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card card-body mt-4">
                    <h6>คู่มือใช้งาน</h6>
                    <hr>
                        <ol>
                            <li>กรอกหรือสแกนรหัสนักเรียน แล้วกดเลือกจากรายการที่ปรากฏ</li>
                            <li>เมื่อพบข้อมูล ให้กดปุ่ม "✅ เช็คเข้า" และ/หรือ "🔴 เช็คออก" โดยเจ้าหน้าที่ต้องบันทึกทั้งเข้าและออกของวันนั้น</li>
                            <li><strong>⚠️ หมายเหตุ:</strong> การเช็คชื่อโดยเจ้าหน้าที่ (กรณีลืมบัตร) จะ<span class="text-success font-weight-bold">ไม่ตรวจสอบเวลา</span> - บันทึกเป็น <span class="badge badge-success">มาเรียนปกติ</span> และ <span class="badge badge-info">กลับปกติ</span> เสมอ</li>
                            <li>ระบบจะบันทึกเหตุการณ์ในตาราง <code>ลืมบัตร</code> และนับจำนวนวันที่ลืม หากเกิน 3 ครั้ง จะเพิ่มรายการใน <code>การหักคะแนนพฤติกรรม</code></li>
                        </ol>
                </div>
            </div>

        </section>


<script>
 
$(document).ready(function(){
    function showMessage(msg, isError=false){
        if(window.Swal){
            if(isError){
                // Error => show confirm dialog so user can acknowledge
                Swal.fire({
                    icon: 'error',
                    html: msg.replace(/\n/g, '<br/>'),
                    confirmButtonText: 'ตกลง'
                });
            } else {
                // Success => toast-style auto close
                Swal.fire({
                    icon: 'success',
                    text: msg,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        } else {
            alert(msg);
        }
    }

    function resetPreview(){
        $('#stu-preview').hide();
        $('#btn-manual-arrival').prop('disabled', true);
        $('#btn-manual-leave').prop('disabled', true);
        $('#stu-empty').show();
    }

    resetPreview();

    // Debounce helper
    function debounce(fn, delay){
        let t;
        return function(){
            const args = arguments;
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        }
    }

    const $input = $('#search-stu');
    const $suggest = $('#search-suggestions');
    let suggestions = [];
    let selectedIndex = -1;

    function renderSuggestions(list){
        suggestions = list || [];
        selectedIndex = -1;
        if(!suggestions.length){
            $suggest.addClass('hidden').empty();
            return;
        }
        $suggest.empty();
        suggestions.forEach((s, idx) => {
            const label = `${s.Stu_id} — ${s.Stu_pre || ''}${s.Stu_name} ${s.Stu_sur} (ม.${s.Stu_major}/${s.Stu_room})`;
            const $item = $(`<div class="px-4 py-3 hover:bg-gray-100 cursor-pointer flex items-center space-x-3 text-sm md:text-base" data-idx="${idx}" role="option">` +
                `<div class="w-10 h-10 bg-gray-100 rounded-full overflow-hidden flex items-center justify-center text-sm text-gray-600">${s.Stu_picture ? '🖼️' : '👤'}</div>` +
                `<div class="flex-1"><div class="font-medium text-gray-800">${label}</div><div class="text-xs text-gray-500">รหัส: ${s.Stu_id}</div></div>` +
                `</div>`);
            $item.on('click', function(){ selectSuggestion(idx); });
            $suggest.append($item);
        });
        $suggest.removeClass('hidden');
    }

    function selectSuggestion(idx){
        if(idx < 0 || idx >= suggestions.length) return;
        const s = suggestions[idx];
        // populate preview
        $('#stu-photo').attr('src', s.Stu_picture ? ('https://std.phichai.ac.th/photo/' + s.Stu_picture) : '../assets/images/profile.png');
        $('#stu-name').text((s.Stu_pre || '') + (s.Stu_name || '') + ' ' + (s.Stu_sur || ''));
        $('#stu-class').text('ม.' + (s.Stu_major || '') + '/' + (s.Stu_room || ''));
        $('#stu-id').text('รหัส: ' + s.Stu_id);
        $('#stu-preview').show();
        $('#stu-empty').hide();
        $('#btn-manual-arrival').prop('disabled', false).data('stu', s.Stu_id);
        $('#btn-manual-leave').prop('disabled', false).data('stu', s.Stu_id);
        $suggest.addClass('hidden');
        $input.val(s.Stu_id);
        // fetch forgot count
        $('#forgot-count').text('...');
        $.getJSON('../controllers/AttendanceController.php?action=get_forgot_count&student_id=' + encodeURIComponent(s.Stu_id), function(cnt){
            if(cnt && typeof cnt.count !== 'undefined'){
                $('#forgot-count').text(cnt.count);
            } else {
                $('#forgot-count').text('0');
            }
        }).fail(function(){
            $('#forgot-count').text('0');
        });

        // Bind click-to-enlarge on photo (mobile friendly)
        $('#stu-photo').off('click').on('click', function(){
            const src = $(this).attr('src');
            if(window.Swal){
                Swal.fire({
                    imageUrl: src,
                    imageAlt: 'Photo',
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: '80%'
                });
            } else {
                window.open(src, '_blank');
            }
        });
    }

    const fetchSuggestions = debounce(function(){
        const q = $input.val().trim();
        if(q.length < 1){ renderSuggestions([]); return; }
        $.getJSON('../controllers/BehaviorController.php?action=search_students&q=' + encodeURIComponent(q) + '&limit=12', function(rows){
            renderSuggestions(rows || []);
        }).fail(function(){ renderSuggestions([]); });
    }, 250);

    $input.on('input', fetchSuggestions);
    $input.on('keydown', function(e){
        if($suggest.hasClass('hidden')) return;
        if(e.key === 'ArrowDown'){
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, suggestions.length - 1);
            highlightSuggestion();
        } else if(e.key === 'ArrowUp'){
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, 0);
            highlightSuggestion();
        } else if(e.key === 'Enter'){
            e.preventDefault();
            if(selectedIndex >= 0) selectSuggestion(selectedIndex);
        } else if(e.key === 'Escape'){
            $suggest.addClass('hidden');
        }
    });

    function highlightSuggestion(){
        $suggest.children().removeClass('bg-gray-100');
        if(selectedIndex >= 0){
            $suggest.children().eq(selectedIndex).addClass('bg-gray-100');
        }
    }

    // click outside closes suggestions
    $(document).on('click', function(e){ if(!$(e.target).closest('#search-stu, #search-suggestions').length){ $suggest.addClass('hidden'); } });

    function doManualScan(stu_id, scan_type){
        $.post('../controllers/AttendanceController.php?action=manual_scan', { student_id: stu_id, scan_type: scan_type }, function(res){
            if(res && res.error){
                showMessage(res.error, true);
                return;
            }
            showMessage('บันทึกสำเร็จ: ' + (res.fullname || stu_id));
            if(res.forgot_count !== undefined){
                $('#forgot-count').text(res.forgot_count);
            }
            // reload forgot history table if present
            if(typeof forgotTable !== 'undefined' && forgotTable) {
                // if a student is selected, reload with that student filter
                const sid = $('#search-stu').val().trim();
                if(sid) {
                    forgotTable.ajax.url('../controllers/AttendanceController.php?action=get_forgot_history&student_id=' + encodeURIComponent(sid)).load();
                } else {
                    forgotTable.ajax.reload(null, false);
                }
            }
        }, 'json').fail(function(xhr){
            let msg = 'บันทึกล้มเหลว';
            if(xhr && xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
            showMessage(msg, true);
        });
    }

    $('#btn-manual-arrival').on('click', function(){
        const stu = $(this).data('stu');
        if(!stu) return;
        // Use SweetAlert2 for confirmation if available, fallback to native confirm
        if(window.Swal){
            Swal.fire({
                title: 'ยืนยันการบันทึก',
                html: 'ยืนยันบันทึกเช็คเข้าแบบเจ้าหน้าที่ (ลืมบัตร) สำหรับ: <b>' + stu + '</b>?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if(result.isConfirmed){
                    doManualScan(stu, 'arrival');
                }
            });
        } else {
            if(confirm('ยืนยันบันทึกเช็คเข้าแบบเจ้าหน้าที่ (ลืมบัตร) สำหรับ: ' + stu + '?')){
                doManualScan(stu, 'arrival');
            }
        }
    });

    $('#btn-manual-leave').on('click', function(){
        const stu = $(this).data('stu');
        if(!stu) return;
        if(window.Swal){
            Swal.fire({
                title: 'ยืนยันการบันทึก',
                html: 'ยืนยันบันทึกเช็คออกแบบเจ้าหน้าที่ (ลืมบัตร) สำหรับ: <b>' + stu + '</b>?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if(result.isConfirmed){
                    doManualScan(stu, 'leave');
                }
            });
        } else {
            if(confirm('ยืนยันบันทึกเช็คออกแบบเจ้าหน้าที่ (ลืมบัตร) สำหรับ: ' + stu + '?')){
                doManualScan(stu, 'leave');
            }
        }
    });

    // Initialize DataTable for forgot history (load current term/year by default)
    let forgotTable;
    (function initForgotTable(){
        // load DataTables (CDN) + Buttons + JSZip
        $('head').append('<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">');
        $('head').append('<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">');

        $.getScript('https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js', function(){
            // load JSZip for excel export
            $.getScript('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', function(){
                // load Buttons extension
                $.getScript('https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js', function(){
                    $.getScript('https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js', function(){
                        // init table with export buttons (styled for mobile)
                        forgotTable = $('#forgotTable').DataTable({
                            dom: 'Bfrtip',
                            buttons: [
                                { extend: 'csvHtml5', text: 'Export CSV', className: 'px-2 py-1 bg-indigo-600 text-white rounded text-xs' },
                                { extend: 'excelHtml5', text: 'Export Excel', className: 'px-2 py-1 bg-indigo-600 text-white rounded text-xs' }
                            ],
                            ajax: {
                                url: '../controllers/AttendanceController.php?action=get_forgot_history',
                                dataSrc: 'data'
                            },
                            columns: [
                                { data: 'student_id' },
                                { data: 'fullname' },
                                { data: 'class' },
                                { data: 'forgot_date' },
                                { data: 'staff_id' },
                                { data: 'note' },
                                { data: 'created_at' }
                            ],
                            order: [[3, 'desc']],
                            pageLength: 12,
                            responsive: true
                        });
                    });
                });
            });
        });
    })();

});
</script>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>