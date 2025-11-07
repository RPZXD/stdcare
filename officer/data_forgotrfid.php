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
                        <h5 class="m-0 text-3xl font-bold text-gray-800 flex items-center">
                            <span class="text-4xl mr-3">💳</span>
                            จัดการกรณีลืมบัตร RFID
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 rounded-3xl shadow-2xl border border-purple-100 p-8">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Load Tailwind CDN for styling (play CDN) -->
                            <script src="https://cdn.tailwindcss.com"></script>
                            <div class="bg-white rounded-2xl shadow-lg p-6 border border-purple-200">
                                <div class="mb-6">
                                    <label for="search-stu" class="block text-lg font-bold text-gray-800 mb-3 flex items-center">
                                        <span class="text-2xl mr-2">🔍</span>
                                        ค้นหานักเรียน
                                    </label>
                                    <div class="text-sm text-gray-600 mb-3">ค้นหาด้วยรหัสนักเรียน ชื่อ หรือนามสกุล</div>
                                    <div class="relative">
                                        <input type="text" id="search-stu" autocomplete="off" class="w-full rounded-xl border-2 border-purple-200 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 p-4 text-lg transition-all duration-200 bg-gradient-to-r from-white to-purple-50" placeholder="พิมพ์รหัสหรือชื่อเพื่อค้นหา... ✨">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                            <span class="text-purple-400 text-xl">🎓</span>
                                        </div>
                                        <div id="search-suggestions" class="hidden absolute z-50 w-full bg-white border-2 border-purple-200 rounded-xl mt-2 max-h-60 overflow-auto shadow-2xl"></div>
                                    </div>
                                </div>

                                <div id="stu-preview" class="mt-6 transform transition-all duration-500" style="display:none;">
                                    <!-- Responsive preview: stack on small screens, row on md+ -->
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 shadow-lg border border-blue-200 animate-fade-in">
                                        <div class="flex flex-col md:flex-row md:items-center md:space-x-6 space-y-4 md:space-y-0 text-center md:text-left">
                                            <div class="flex-shrink-0 mx-auto md:mx-0 relative">
                                                <div class="absolute -top-2 -right-2 bg-yellow-400 rounded-full p-2 shadow-lg animate-bounce">
                                                    <span class="text-white text-sm">📸</span>
                                                </div>
                                                <img id="stu-photo" src="../assets/images/profile.png" alt="photo" class="w-32 h-32 md:w-36 lg:w-48 md:h-36 lg:h-48 rounded-full object-cover border-4 border-white shadow-xl cursor-pointer hover:scale-110 transition-transform duration-300">
                                            </div>
                                            <div class="flex-1">
                                                <h5 id="stu-name" class="text-xl font-bold text-gray-800 mb-2">👤</h5>
                                                <p id="stu-class" class="text-sm text-blue-600 font-semibold mb-1">🏫</p>
                                                <p id="stu-id" class="text-sm text-gray-500 mb-4">🆔</p>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <button id="btn-manual-arrival" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-green-400 to-emerald-500 hover:from-green-500 hover:to-emerald-600 text-white font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center" disabled>
                                                        <span class="text-lg mr-2">✅</span>
                                                        เช็คเข้า
                                                    </button>
                                                    <button id="btn-manual-leave" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-red-400 to-pink-500 hover:from-red-500 hover:to-pink-600 text-white font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center" disabled>
                                                        <span class="text-lg mr-2">🔴</span>
                                                        เช็คออก
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 text-center md:text-left bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                                            <div class="flex items-center justify-center md:justify-start">
                                                <span class="text-2xl mr-2">⚠️</span>
                                                <strong class="text-yellow-800">จำนวนวันที่ลืมบัตรในเทอมนี้: </strong>
                                                <span id="forgot-count" class="text-yellow-600 font-bold text-lg ml-2">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="stu-empty" class="mt-6 text-center py-8 text-gray-500 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                                    <div class="text-6xl mb-4">🔍</div>
                                    <div class="text-lg font-semibold">ค้นหานักเรียนเพื่อเริ่มบันทึก</div>
                                    <div class="text-sm mt-2">กรุณาพิมพ์รหัสนักเรียนหรือชื่อในช่องค้นหาด้านบน</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-white rounded-2xl shadow-lg p-6 border border-purple-200">
                                <div class="mb-4">
                                    <h6 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
                                        <span class="text-2xl mr-2">📋</span>
                                        ประวัติการลืมบัตร
                                    </h6>
                                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                        <div class="flex items-center text-blue-700">
                                            <span class="text-lg mr-2">💡</span>
                                            <span class="text-sm">สามารถส่งออกข้อมูลเป็น CSV หรือ Excel ได้จากปุ่มด้านบนตาราง</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-x-auto bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-4 border border-gray-200">
                                    <table id="forgotTable" class="min-w-full divide-y divide-gray-200" style="width:100%">
                                        <thead class="bg-gradient-to-r from-purple-600 to-pink-600 text-white">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-sm font-bold text-white">🆔 รหัส</th>
                                                <th class="px-4 py-3 text-left text-sm font-bold text-white">👤 ชื่อ-สกุล</th>
                                                <th class="px-4 py-3 text-left text-sm font-bold text-white">🏫 ชั้น</th>
                                                <th class="px-4 py-3 text-left text-sm font-bold text-white">📅 วันที่ลืม</th>
                                                <th class="px-4 py-3 text-left text-sm font-bold text-white">📝 หมายเหตุ</th>
                                                <th class="px-4 py-3 text-left text-sm font-bold text-white">⏰ สร้างเมื่อ</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl shadow-lg p-6 border border-indigo-200 mt-6">
                    <div class="flex items-center mb-4">
                        <span class="text-3xl mr-3">📚</span>
                        <h6 class="text-xl font-bold text-gray-800">คู่มือการใช้งาน</h6>
                    </div>
                    <hr class="border-indigo-200 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3">
                                <span class="text-2xl mt-1">1️⃣</span>
                                <div>
                                    <strong class="text-gray-800">ค้นหานักเรียน</strong>
                                    <p class="text-sm text-gray-600 mt-1">กรอกหรือสแกนรหัสนักเรียน แล้วเลือกจากรายการที่ปรากฏ</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="text-2xl mt-1">2️⃣</span>
                                <div>
                                    <strong class="text-gray-800">บันทึกการเช็คชื่อ</strong>
                                    <p class="text-sm text-gray-600 mt-1">กดปุ่ม "✅ เช็คเข้า" และ/หรือ "🔴 เช็คออก" โดยเจ้าหน้าที่ต้องบันทึกทั้งเข้าและออกของวันนั้น</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3">
                                <span class="text-2xl mt-1">⚠️</span>
                                <div>
                                    <strong class="text-red-600">ข้อควรระวัง</strong>
                                    <p class="text-sm text-gray-600 mt-1">การเช็คชื่อโดยเจ้าหน้าที่ (กรณีลืมบัตร) จะ<span class="text-green-600 font-bold">ไม่ตรวจสอบเวลา</span> - บันทึกเป็น <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">มาเรียนปกติ</span> และ <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">กลับปกติ</span> เสมอ</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="text-2xl mt-1">🎯</span>
                                <div>
                                    <strong class="text-gray-800">ระบบจะบันทึกอัตโนมัติ</strong>
                                    <p class="text-sm text-gray-600 mt-1">ระบบจะบันทึกเหตุการณ์ในตาราง "ลืมบัตร" และนับจำนวนวันที่ลืม หากเกิน 3 ครั้ง จะเพิ่มรายการใน "การหักคะแนนพฤติกรรม"</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    confirmButtonText: 'เข้าใจแล้ว 😔',
                    confirmButtonColor: '#ef4444'
                });
            } else {
                // Success => toast-style auto close
                Swal.fire({
                    icon: 'success',
                    text: msg,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                    color: 'white'
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
            const $item = $(`<div class="px-4 py-3 hover:bg-gradient-to-r hover:from-purple-100 hover:to-pink-100 cursor-pointer flex items-center space-x-3 text-sm md:text-base transition-all duration-200 border-b border-gray-100 last:border-b-0" data-idx="${idx}" role="option">` +
                `<div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full overflow-hidden flex items-center justify-center text-white text-lg shadow-md">${s.Stu_picture ? '🖼️' : '👤'}</div>` +
                `<div class="flex-1"><div class="font-semibold text-gray-800">${label}</div><div class="text-xs text-purple-600 font-medium">รหัส: ${s.Stu_id}</div></div>` +
                `<div class="text-purple-500">👆</div>` +
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
        $suggest.children().removeClass('bg-gradient-to-r from-purple-100 to-pink-100').find('.text-purple-500').removeClass('text-purple-700');
        if(selectedIndex >= 0){
            $suggest.children().eq(selectedIndex).addClass('bg-gradient-to-r from-purple-100 to-pink-100').find('.text-purple-500').addClass('text-purple-700');
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
                title: 'ยืนยันการบันทึก ✅',
                html: 'ยืนยันบันทึกเช็คเข้าแบบเจ้าหน้าที่ (ลืมบัตร) สำหรับ: <b class="text-purple-600">' + stu + '</b>? 🎓',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยันเลย 🚀',
                cancelButtonText: 'ยกเลิก 😅',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                background: 'linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%)'
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
                title: 'ยืนยันการบันทึก 🔴',
                html: 'ยืนยันบันทึกเช็คออกแบบเจ้าหน้าที่ (ลืมบัตร) สำหรับ: <b class="text-purple-600">' + stu + '</b>? 🏠',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยันเลย 🚀',
                cancelButtonText: 'ยกเลิก 😅',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                background: 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)'
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
                                { extend: 'csvHtml5', text: '📊 Export CSV', className: 'px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 mr-2' },
                                { extend: 'excelHtml5', text: '📈 Export Excel', className: 'px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200' }
                            ],
                            ajax: {
                                url: '../controllers/AttendanceController.php?action=get_forgot_history',
                                dataSrc: 'data'
                            },
                            columns: [
                                { data: 'student_id', className: 'px-4 py-3 text-sm font-medium text-gray-800' },
                                { data: 'fullname', className: 'px-4 py-3 text-sm text-gray-700' },
                                { data: 'class', className: 'px-4 py-3 text-sm text-blue-600 font-medium' },
                                { data: 'forgot_date', className: 'px-4 py-3 text-sm text-gray-600' },
                                { data: 'note', className: 'px-4 py-3 text-sm text-gray-600' },
                                { data: 'created_at', className: 'px-4 py-3 text-sm text-gray-500' }
                            ],
                            order: [[3, 'desc']],
                            pageLength: 12,
                            responsive: true,
                            language: {
                                "zeroRecords": "😔 ไม่พบข้อมูลการลืมบัตร",
                                "info": "📊 แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                                "processing": "⏳ กำลังโหลด...",
                                "search": "🔍 ค้นหา:",
                                "lengthMenu": "📋 แสดง _MENU_ รายการต่อหน้า",
                                "paginate": {
                                    "first": "⏮️ หน้าแรก",
                                    "last": "⏭️ หน้าสุดท้าย", 
                                    "next": "➡️ ถัดไป",
                                    "previous": "⬅️ ก่อนหน้า"
                                }
                            },
                            initComplete: function() {
                                $('.dataTables_wrapper .dataTables_filter input').addClass('rounded-lg border-2 border-purple-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 px-3 py-2');
                                $('.dataTables_wrapper .dataTables_length select').addClass('rounded-lg border-2 border-purple-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 px-3 py-2');
                            }
                        });
                    });
                });
            });
        });
    })();

});
</script>

<style>
/* Custom animations and effects */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.animate-bounce-in {
    animation: bounceIn 0.5s ease-out;
}

.search-suggestions {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Enhanced table styling */
#forgotTable tbody tr {
    transition: all 0.3s ease;
}

#forgotTable tbody tr:hover {
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
}

/* Button hover effects */
.btn-manual {
    position: relative;
    overflow: hidden;
}

.btn-manual::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-manual:hover::before {
    left: 100%;
}

/* Loading animation */
.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #a855f7;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 20px auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Enhanced search input */
#search-stu:focus {
    box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
    background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding: 10px;
    }
    
    .btn-manual {
        padding: 12px 16px;
        font-size: 14px;
    }
    
    #stu-photo {
        width: 100px !important;
        height: 100px !important;
    }
}

/* SweetAlert2 custom styling */
.swal2-popup {
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
}

.swal2-confirm {
    background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%) !important;
    border-radius: 12px !important;
    font-weight: bold !important;
}

.swal2-cancel {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
    border-radius: 12px !important;
    font-weight: bold !important;
}

/* DataTables custom styling */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%) !important;
    color: white !important;
    border-radius: 8px !important;
}

.dataTables_wrapper .dataTables_info {
    color: #6b7280 !important;
    font-weight: 600 !important;
    padding: 15px !important;
}

/* Gradient text effects */
.gradient-text {
    background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Floating animation for icons */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.float-animation {
    animation: float 3s ease-in-out infinite;
}

/* Enhanced focus states */
.focus\:ring-purple-300:focus {
    --tw-ring-color: rgba(196, 181, 253, 0.5);
}
</style>

    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>