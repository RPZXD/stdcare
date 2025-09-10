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
$classes = [];
$rooms = [];
try {
    // Use Stu_class (not Stu_major) and ensure we ignore NULL/empty values, order numerically when possible
    $sqlC = "SELECT DISTINCT Stu_class AS cls FROM student WHERE Pee = :pee AND Stu_class IS NOT NULL AND Stu_class != '' ORDER BY CAST(Stu_class AS UNSIGNED), Stu_class";
    $stmtC = $db->prepare($sqlC);
    $stmtC->bindParam(':pee', $pee);
    $stmtC->execute();
    while ($row = $stmtC->fetch(PDO::FETCH_ASSOC)) {
        if ($row['cls'] !== null && $row['cls'] !== '') $classes[] = $row['cls'];
    }

    $sqlR = "SELECT DISTINCT Stu_room AS rm FROM student WHERE Pee = :pee AND Stu_room IS NOT NULL AND Stu_room != '' ORDER BY CAST(Stu_room AS UNSIGNED), Stu_room";
    $stmtR = $db->prepare($sqlR);
    $stmtR->bindParam(':pee', $pee);
    $stmtR->execute();
    while ($row = $stmtR->fetch(PDO::FETCH_ASSOC)) {
        if ($row['rm'] !== null && $row['rm'] !== '') $rooms[] = $row['rm'];
    }
} catch (Exception $e) {
    // If query fails, leave arrays empty — JS will handle defaults
}

require_once('../teacher/header.php');

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<link rel="stylesheet" href="../teacher/assets/css/student-management.css">

<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('../teacher/wrapper.php');?>

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
<?php require_once('../teacher/script.php'); ?>

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

            const response = await $.ajax({
                url: '../teacher/api/fetch_data_student.php',
                method: 'GET',
                dataType: 'json',
                data: { class: classValue, room: roomValue }
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
                        <div class="student-card hover-lift"
                            data-name="${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}"
                            data-id="${item.Stu_id}"
                            data-no="${item.Stu_no}"
                            data-nick="${item.Stu_nick}"
                            style="animation-delay: ${index * 0.1}s; opacity: 0;">

                            <div class="student-photo-container" style="position: relative;">
                                <img class="student-photo w-full h-128 object-cover" 
                                     src="../photo/${item.Stu_picture}" 
                                     alt="Student Picture"
                                     onerror="handleImageError(this, '${item.Stu_pre}${item.Stu_name}')"
                                     onload="this.classList.add('animate-fadeInUp')">
                                <div class="absolute top-2 right-2 bg-white bg-opacity-80 rounded-full px-2 py-1 text-xs font-bold text-gray-700">
                                    #${item.Stu_no}
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <h5 class="text-lg font-bold text-purple-500 mb-2">
                                        ${item.Stu_pre}${item.Stu_name} ${item.Stu_sur}
                                    </h5>
                                    <div class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                        รหัส: ${item.Stu_id}
                                    </div>
                                </div>
                                
                                <div class="space-y-2 text-sm">
                                    ${item.Stu_nick ? `<div class="flex justify-between">
                                        <span class="text-gray-900">ชื่อเล่น:</span>
                                        <span class="font-semibold text-purple-600">${item.Stu_nick}</span>
                                    </div>` : ''}
                                    ${item.Stu_phone ? `<div class="flex justify-between">
                                        <span class="text-gray-900">เบอร์:</span>
                                        <a href="tel:${item.Stu_phone}" class="text-gray-900 hover:underline flex items-center">
                                            📞 ${item.Stu_phone}
                                        </a>
                                    </div>` : ''}
                                    ${item.Par_phone ? `<div class="flex justify-between">
                                        <span class="text-gray-900">ผู้ปกครอง:</span>
                                        <a href="tel:${item.Par_phone}" class="text-gray-900 hover:underline flex items-center">
                                            👨‍👩‍👧‍👦 ${item.Par_phone}
                                        </a>
                                    </div>` : ''}
                                </div>
                                
                                <div class="flex justify-center space-x-2 mt-6">
                                    <button class="btn-modern btn-view btn-sm hover-lift" data-id="${item.Stu_id}" title="ดูข้อมูลทั้งหมด">
                                        <span class="text-lg">👀</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    showDataStudent.append(studentCard);
                });

                // entrance animation
                $('.student-card').each(function(index) {
                    $(this).css({
                        'opacity': '0',
                        'transform': 'translateY(50px)'
                    }).delay(index * 100).animate({
                        'opacity': '1'
                    }, 500).css('transform', 'translateY(0)');
                });
            }

        } catch (error) {
            hideLoading();
            Swal.fire('🚨 ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
            console.error(error);
        }
    }

    // view handler
    $(document).on('click', '.btn-view', function() {
        var stuId = $(this).data('id');
        showLoading();
        $.ajax({
            url: '../teacher/api/view_student.php',
            method: 'GET',
            data: { stu_id: stuId },
            success: function(response) {
                hideLoading();
                // reuse modal from teacher area
                $('body').append('<div id="officerModalPlaceholder" class="modal fade" tabindex="-1"></div>');
                $('#officerModalPlaceholder').html(response).modal('show');
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
        showLoading();
        const classValue = $('#selectClass').val() || '';
        const roomValue = $('#selectRoom').val() || '';
        $.ajax({
            url: '../teacher/api/print_student_list.php',
            method: 'GET',
            data: { class: classValue, room: roomValue, format: 'table' },
            success: function(response) {
                hideLoading();
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>รายชื่อนักเรียน</title></head><body>${response}</body></html>`);
                printWindow.document.close();
                printWindow.onload = function() { setTimeout(function(){ printWindow.print(); printWindow.close(); }, 500); };
            },
            error: function() { hideLoading(); Swal.fire('❌ ข้อผิดพลาด', 'ไม่สามารถสร้างรายการพิมพ์ได้', 'error'); }
        });
    });

    // utility functions
    function showLoading() { $('#loadingOverlay').fadeIn(200); }
    function hideLoading() { $('#loadingOverlay').fadeOut(200); }

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
/* minimal styles reuse from teacher file */
.student-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; }
.student-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
.student-photo { width: 100%; height: 200px; object-fit: cover; }
.card-body { padding: 12px 16px; }
</style>
