<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$student = new Student($db);

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

$term = $user->getTerm();
$pee = $user->getPee();

$officer_name = isset($userData['Name']) ? $userData['Name'] : ($userData['Teach_name'] ?? 'เจ้าหน้าที่');

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));

// Prepare class and room lists from student table
$classes = range(1,6);
$rooms = range(1,12);

require_once('header.php');

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<link rel="stylesheet" href="assets/css/student-management.css">

<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <div class="content-wrapper">

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div>
        </div>
      </div>
    </div>

<section class="content">
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="callout callout-success text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 20px; padding: 30px;">
                <div class="logo-container mb-3">
                    <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="brand-image rounded-full opacity-90 mb-3" style="width: 70px; height: 70px; border: 4px solid rgba(255,255,255,0.3);">
                </div>
                <h2 class="text-center text-2xl font-bold mb-2">รายชื่อนักเรียน (สำหรับ เจ้าหน้าที่)</h2>
                <h4 class="text-center text-lg opacity-90">ปีการศึกษา <?=$pee?></h4>

                <!-- Selectors for Class and Room -->
                <div class="row justify-content-center mb-3">
                    <div class="col-md-4">
                        <label class="text-white font-weight-bold">ระดับชั้น</label>
                        <select id="selectClass" class="form-control">
                            <option value="">-- เลือกระดับชั้น --</option>
                            <?php foreach($classes as $c): ?>
                                <option value="<?=htmlspecialchars($c)?>"><?=htmlspecialchars($c)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="text-white font-weight-bold">ห้อง</label>
                        <select id="selectRoom" class="form-control">
                            <option value="">-- เลือกห้อง --</option>
                            <?php foreach($rooms as $r): ?>
                                <option value="<?=htmlspecialchars($r)?>"><?=htmlspecialchars($r)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="btnLoad" class="btn btn-light btn-block">🔎 แสดง</button>
                    </div>
                </div>

                <!-- Search and Print Row -->
                <div class="search-container mb-3">
                    <div class="search-icon">🔍</div>
                    <input type="text" id="studentSearch" class="search-input" placeholder="ค้นหาชื่อนักเรียน, รหัส, เลขที่ หรือชื่อเล่น...">
                </div>

                <div class="print-container mt-2 mb-3">
                    <button id="printStudentList" class="btn btn-success btn-lg" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; border-radius: 25px; padding: 10px 24px; color: white; font-weight: bold;">
                        <i class="fas fa-print mr-2"></i>🖨️ พิมพ์รายชื่อนักเรียน
                    </button>
                </div>

                <div id="showDataStudent" class="student-grid">
                    <!-- Student cards injected here -->
                </div>
            </div>
        </div>
    </div>
</section>

  </div>
  
  <?php require_once('../footer.php');?>

</div>

<!-- Modals (reuse teacher modals) -->
<?php require_once('script.php'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
let cropper;
let currentStudentId;

$(document).ready(function() {
    // Load list when user clicks แสดง or when both selectors have values
    $('#btnLoad').on('click', function() {
        loadStudentData();
    });

    // Also load when selector changes
    $('#selectClass, #selectRoom').on('change', function() {
        // optional: auto-load when both selected
        // loadStudentData();
    });

    async function loadStudentData() {
        try {
            showLoading();
            const classValue = $('#selectClass').val() || '';
            const roomValue = $('#selectRoom').val() || '';
            // fetch รายชื่อจาก Controller
            const response = await $.ajax({
                url: '../controllers/StudentController.php',
                method: 'GET',
                dataType: 'json',
                data: { 
                    // action: 'list', // <-- นี่คือของเดิม
                    action: 'list_for_officer', // <-- ▼▼▼ แก้ไขเป็นชื่อ case ใหม่
                    class: classValue, 
                    room: roomValue, 
                    status: 1 
                }
            });
            hideLoading();
            if (!response.success) {
                Swal.fire('🚨 ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
                return;
            }
            const showDataStudent = $('#showDataStudent');
            showDataStudent.empty();
            if (response.data.length === 0) {
                showDataStudent.html(`
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">📚</div>
                        <p class="text-xl font-semibold text-gray-900">ไม่พบข้อมูลนักเรียน</p>
                    </div>
                `);
            } else {
                response.data.forEach((item, index) => {
                    const studentCard = `
                        <div class="student-card profile-card" 
                            data-name="${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}"
                            data-id="${item.Stu_id}"
                            data-no="${item.Stu_no}"
                            data-nick="${item.Stu_nick}"
                            style="animation-delay: ${index * 0.08}s; opacity: 0;">
                            <div class="profile-top bg-gradient-to-r from-purple-400 to-indigo-500 rounded-t-2xl"></div>
                            <div class="profile-body px-6 pb-6 -mt-12">
                                <div class="avatar-wrap mx-auto w-36 h-36 rounded-full bg-white p-1 shadow-xl">
                                    <img class="profile-avatar zoomable-avatar w-full h-full rounded-full object-cover" src="https://std.phichai.ac.th/photo/${item.Stu_picture}" alt="${item.Stu_pre}${item.Stu_name}" onerror="handleImageError(this, '${item.Stu_pre}${item.Stu_name}')" data-fullsrc="https://std.phichai.ac.th/photo/${item.Stu_picture}">
                                </div>
                                <h3 class="profile-name mt-4 text-2xl font-extrabold text-purple-700">🎓 ${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}</h3>
                                <div class="id-badge inline-block mt-2 px-3 py-1 rounded-full bg-white text-sm text-gray-700 shadow-sm">🆔 รหัส: <span class="font-semibold">${item.Stu_id}</span></div>

                                <div class="badges flex flex-col items-center gap-3 mt-4">
                                    ${item.Stu_nick ? `<div class="info-badge inline-flex items-center px-4 py-2 rounded-full bg-white text-sm text-gray-800 shadow-sm">😊<div class="text-left"><div class="label text-xs text-gray-500">ชื่อเล่น : ${item.Stu_nick}</div></div></div>` : ''}
                                    ${item.Stu_phone ? `<div class="info-badge inline-flex items-center px-4 py-2 rounded-full bg-white text-sm text-gray-800 shadow-sm">📞<div class="text-left"><div class="label text-xs text-gray-500">เบอร์ : <a href="tel:${item.Stu_phone}" class="text-blue-700">${item.Stu_phone}</a></div></div></div>` : ''}
                                    ${item.Par_phone ? `<div class="info-badge inline-flex items-center px-4 py-2 rounded-full bg-white text-sm text-gray-800 shadow-sm">👨‍👩‍👧‍👦<div class="text-left"><div class="label text-xs text-gray-500">ผู้ปกครอง : <a href="tel:${item.Par_phone}" class="text-blue-700">${item.Par_phone}</a></div></div></div>` : ''}
                                </div>

                                <button class="btn-view btn-gradient w-full mt-5" data-id="${item.Stu_id}" title="ดูข้อมูลทั้งหมด">👀 ดูข้อมูล</button>
                            </div>
                        </div>
                    `;
                    showDataStudent.append(studentCard);
                });
                // entrance animation
                $('.student-card').each(function(index) {
                    $(this).css({
                        'opacity': '0',
                        'transform': 'translateY(20px) scale(0.95)'
                    }).delay(index * 80).animate({
                        'opacity': '1'
                    }, 420).queue(function(next){
                        $(this).css('transform', 'translateY(0) scale(1)');
                        next();
                    });
                });
            }
        } catch(error) {
            hideLoading();
            Swal.fire('🚨 ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
            console.error(error);
        }
    }

    // view handler
    // (นี่คือโค้ดที่แก้ไขแล้ว)
    $(document).on('click', '.btn-view', function() {
        var stuId = $(this).data('id');
        showLoading();

        // (เผื่อมี Modal ค้าง ให้ลบทิ้งไปก่อน)
        $('#officerModalPlaceholder').remove(); 

        $.ajax({
            url: '../controllers/StudentController.php',
            method: 'GET',
            data: { action: 'get_modal_student_data', stu_id: stuId },
            success: function(response) {
                hideLoading();
                
                // 1. สร้าง Modal Shell ขึ้นมาใหม่ (ยังไม่เพิ่มเข้า body)
                var $modalShell = $('<div id="officerModalPlaceholder" class="modal fade" tabindex="-1"></div>');
                
                // 2. ยัด HTML (ที่ได้จาก Controller) เข้าไป
                $modalShell.html(response);
                
                // 3. เพิ่ม Modal Shell นี้เข้า body
                $modalShell.appendTo('body');
                
                // 4. สั่ง show
                $modalShell.modal('show');

                // 5. (สำคัญที่สุด!) ตั้ง Event ว่าเมื่อ Modal นี้ถูกปิด (hidden)
                //    ให้ "ลบ" (remove) ตัวมันเองทิ้งจาก DOM ไปเลย
                $modalShell.on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            },
            error: function() {
                hideLoading();
                Swal.fire('❌ ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            }
        });
    });

    // search debounce
    $('#studentSearch').on('input', function() {
        debouncedSearch($(this).val());
    });

    // print uses selected class/room
    $('#printStudentList').on('click', function() {
        const classValue = $('#selectClass').val() || '';
        const roomValue = $('#selectRoom').val() || '';
        
        // (ดึงปีการศึกษาจาก PHP ที่อยู่ด้านบนของไฟล์)
        const peeValue = "<?php echo $pee; ?>"; 

        if (!classValue || !roomValue) {
            Swal.fire('⚠️ โปรดทราบ', 'กรุณาเลือกระดับชั้นและห้องก่อนพิมพ์', 'warning');
            return;
        }

        // สร้าง URL ไปยังไฟล์ใหม่ของเรา
        const url = `print_roster.php?level=${classValue}&room=${roomValue}&year=${peeValue}`;

        // เปิดในแท็บใหม่
        // (Browser จะเปิดแท็บใหม่, โหลดหน้า print_roster.php,
        // และไฟล์นั้นจะสั่ง print() ด้วยตัวเอง)
        window.open(url, '_blank');
    });

    // utility functions
    function showLoading() { $('#loadingOverlay').fadeIn(200); }
    function hideLoading() { $('#loadingOverlay').fadeOut(200); }

        // avatar click -> open modal with large image
        $(document).on('click', '.zoomable-avatar', function(e) {
                e.preventDefault();
                var src = $(this).data('fullsrc') || $(this).attr('src');
                var alt = $(this).attr('alt') || '';

                // remove any existing modal placeholder
                $('#avatarModal').remove();

                var modalHtml = `
                        <div id="avatarModal" class="modal fade" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content bg-transparent border-0">
                                    <div class="modal-body text-center p-0">
                                        <button type="button" class="close modal-close p-2" data-dismiss="modal" aria-label="Close" style="position:absolute; right:8px; top:8px; z-index:1052; background: rgba(255,255,255,0.8); border-radius:50%;">&times;</button>
                                        <img src="${src}" alt="${alt}" style="max-width:90vw; max-height:90vh; border-radius:8px; box-shadow:0 18px 40px rgba(0,0,0,0.45);">
                                    </div>
                                </div>
                            </div>
                        </div>
                `;

                var $modal = $(modalHtml);
                $modal.appendTo('body');
                $modal.modal('show');
                $modal.on('hidden.bs.modal', function() { $(this).remove(); });
        });

    // notification and image handlers from teacher page (minimal implementations)
    function handleImageError(img, studentName) {
        img.src = '../dist/img/default-avatar.svg';
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => { clearTimeout(timeout); func(...args); };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const debouncedSearch = debounce(function(searchValue) {
        const val = searchValue.trim().toLowerCase();
        $('.student-card').each(function() {
            const name = ($(this).attr('data-name') || '').toLowerCase();
            const id = ($(this).attr('data-id') || '').toLowerCase();
            const no = ($(this).attr('data-no') || '').toLowerCase();
            const nick = ($(this).attr('data-nick') || '').toLowerCase();
            const isMatch = name.includes(val) || id.includes(val) || no.includes(val) || nick.includes(val);
            if (isMatch) { $(this).removeClass('hidden'); } else { $(this).addClass('hidden'); }
        });
    }, 300);

});
</script>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none; position: fixed; left:0; top:0; right:0; bottom:0; background: rgba(255,255,255,0.8); z-index: 1050; align-items: center; justify-content: center;">
    <div class="text-center text-gray-900">
        <div class="loading-spinner"></div>
        <p class="mt-3">กำลังประมวลผล...</p>
    </div>
</div>

<style>
/* Profile card styles to complement Tailwind utility classes */
.student-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
.profile-card { position: relative; overflow: visible; border-radius: 18px; background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(245,248,255,0.9)); }
.profile-top { height: 48px; border-top-left-radius: 18px; border-top-right-radius: 18px; }
.profile-body { position: relative; background: transparent; }
.avatar-wrap { position: relative; width: 144px; height: 144px; margin-top: -72px; }
.profile-avatar { display:block; }
.zoomable-avatar { cursor: pointer; transition: transform .18s ease; }
.zoomable-avatar:hover { transform: scale(1.04); }
.profile-name { letter-spacing: 0.6px; }
.id-badge { opacity: 0.95; }
.info-box { backdrop-filter: blur(4px); }
.info-badge { display: inline-flex; align-items: center; gap: 12px; padding: 8px 14px; border-radius: 9999px; background: #ffffff; box-shadow: 0 6px 14px rgba(2,6,23,0.06); }
.badges { width: 100%; }
.btn-gradient { display:inline-block; background: linear-gradient(90deg,#7c3aed,#ec4899); color:#fff; padding:0.6rem 1rem; border-radius:9999px; font-weight:600; box-shadow: 0 10px 20px rgba(124,58,237,0.12); border: none; }
.btn-gradient:hover { transform: translateY(-3px); box-shadow: 0 18px 30px rgba(124,58,237,0.14); }
.profile-card .icon { font-size: 20px; width: 34px; text-align: center; flex: 0 0 34px; }
.profile-card .label { color: #6b7280; font-size: 12px; }
.profile-card .value { color: #111827; font-size: 15px; }
.info-box p.info-row { display: grid; grid-template-columns: 40px 1fr; gap:12px; align-items: start; padding:10px 0; border-bottom: 1px solid rgba(17,24,39,0.04); margin:0; }
.info-box p.info-row:last-child { border-bottom: none; }
.info-box p.info-row .label { font-size: 12px; color: #6b7280; display:block; }
.info-box p.info-row .value { font-size: 15px; color: #111827; margin-top: 6px; display:block; }
.info-box p.info-row .value a { color: #1d4ed8; text-decoration: none; }

/* small responsive tweaks */
@media (max-width: 480px) {
    .avatar-wrap { width: 112px; height: 112px; margin-top: -56px; }
    .profile-name { font-size: 1.125rem; }
}
</style>
