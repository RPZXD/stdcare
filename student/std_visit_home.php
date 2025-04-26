<?php
require_once('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Student_login'])) {
    header("Location: ../login.php");
    exit();
}

include_once("../config/Database.php");
include_once("../class/UserLogin.php");

$studentDb = new Database("phichaia_student");
$studentConn = $studentDb->getConnection();
$user = new UserLogin($studentConn);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

$student_id = $_SESSION['Student_login'];
$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $studentConn->prepare($query);
$stmt->bindParam(":id", $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">บันทึกการเยี่ยมบ้าน</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div id="visit-home-status" class="max-w-2xl mx-auto mt-6"></div>
            
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>

<!-- Modal สำหรับฟอร์มบันทึกการเยี่ยมบ้าน -->
<div class="modal fade" id="addVisitModal" tabindex="-1" aria-labelledby="addVisitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title text-lg font-semibold" id="addVisitModalLabel">บันทึกข้อมูลการเยี่ยมบ้าน</h3>
        <button type="button" class="close text-gray-500 hover:text-gray-700 text-2xl" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="addVisitModalContent" class="modal-body p-6">
        <!-- ฟอร์มจะถูกโหลดที่นี่ -->
      </div>
      <div class="modal-footer flex justify-between">
        <button type="button" class="btn bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600" data-dismiss="modal">ปิดหน้าต่าง</button>
        <button type="button" id="saveVisitBtn" class="btn bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">บันทึก</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
    // Load visit status table for the student
    function loadVisitTable() {
        const container = $('#visit-home-status');
        container.html('<div class="text-center text-gray-500">กำลังโหลดข้อมูล...</div>');
        const pee = <?php echo json_encode($pee); ?>;
        const stuId = <?php echo json_encode($student_id); ?>;

        $.ajax({
            url: 'api/student_visit_status.php',
            method: 'GET',
            dataType: 'json',
            data: { pee: pee, stuId: stuId },
            success: function(data) {
                let html = `
                    <div class="bg-white shadow rounded-lg p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">ครั้งที่</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">สถานะ</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                `;
                for (let i = 1; i <= 2; i++) {
                    const visit = data.visits.find(v => v.visit_no == i);
                    html += `<tr>
                        <td class="px-4 py-2">ครั้งที่ ${i}  (เทอมที่ ${i})</td>`;
                    if (visit && visit.status === 'saved') {
                        html += `<td class="px-4 py-2"><span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded">บันทึกแล้ว</span></td>
                            <td class="px-4 py-2 space-x-2">
                                <button type="button" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded" onclick="viewVisit(${i})">ดู</button>
                                <button type="button" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded" onclick="editVisit(${i})">แก้ไข</button>
                            </td>`;
                    } else {
                        html += `<td class="px-4 py-2"><span class="inline-block px-2 py-1 bg-gray-200 text-gray-700 rounded">ยังไม่ได้บันทึก</span></td>
                            <td class="px-4 py-2">
                                <button type="button" class="inline-block bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded" onclick="addVisit(${i})">บันทึก</button>
                            </td>`;
                    }
                    html += `</tr>`;
                }
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                container.html(html);
            },
            error: function() {
                $('#visit-home-status').html('<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>');
            }
        });
    }

    // Load table on page load
    loadVisitTable();

    // View visit modal logic
    window.viewVisit = function(visitNo) {
        const content = $('#addVisitModalContent');
        const modal = $('#addVisitModal');
        content.html('<div class="text-center text-gray-500">กำลังโหลดข้อมูล...</div>');
        $('#addVisitModalLabel').text('ดูข้อมูลการเยี่ยมบ้าน');
        $('#saveVisitBtn').hide();
        const pee = <?php echo json_encode($pee); ?>;
        const stuId = <?php echo json_encode($student_id); ?>;
        $.ajax({
            url: 'template_form/view_visit_form.php',
            method: 'GET',
            data: { term: visitNo, pee: pee, stuId: stuId },
            dataType: 'html',
            success: function(html) {
                content.html(html);
                modal.modal('show');
            },
            error: function() {
                content.html('<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>');
            }
        });
    };

    // Edit visit modal logic
    window.editVisit = function(visitNo) {
        const content = $('#addVisitModalContent');
        const modal = $('#addVisitModal');
        content.html('<div class="text-center text-gray-500">กำลังโหลดฟอร์ม...</div>');
        $('#addVisitModalLabel').text('แก้ไขข้อมูลการเยี่ยมบ้าน');
        $('#saveVisitBtn').show();
        const pee = <?php echo json_encode($pee); ?>;
        const stuId = <?php echo json_encode($student_id); ?>;
        $.ajax({
            url: 'template_form/edit_visit_form.php',
            method: 'GET',
            data: { term: visitNo, pee: pee, stuId: stuId },
            dataType: 'html',
            success: function(html) {
                content.html(html);
                modal.modal('show');
            },
            error: function() {
                content.html('<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดฟอร์ม</div>');
            }
        });
    };

    // Add visit modal logic (AJAX load form)
    window.addVisit = function(visitNo) {
        const content = $('#addVisitModalContent');
        const modal = $('#addVisitModal');
        content.html('<div class="text-center text-gray-500">กำลังโหลดฟอร์ม...</div>');
        $('#addVisitModalLabel').text('บันทึกข้อมูลการเยี่ยมบ้าน');
        $('#saveVisitBtn').show();
        const pee = <?php echo json_encode($pee); ?>;
        const stuId = <?php echo json_encode($student_id); ?>;
        $.ajax({
            url: 'template_form/add_visit_form.php',
            method: 'GET',
            data: { term: visitNo, pee: pee, stuId: stuId },
            dataType: 'html',
            success: function(html) {
                content.html(html);
                modal.modal('show');
            },
            error: function() {
                content.html('<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดฟอร์ม</div>');
            }
        });
    };

    // Save visit form (AJAX submit)
    $('#saveVisitBtn').on('click', function () {
        const form = $('#addVisitForm');
        if (form.length === 0) return;

        // ตรวจสอบว่าข้อ 1-18 ถูกเลือกครบหรือไม่
        let missing = [];
        for (let i = 1; i <= 18; i++) {
            if (form.find('input[name="vh'+i+'"]:checked').length === 0) {
                missing.push(i);
            }
        }
        if (missing.length > 0) {
            Swal.fire(
                'กรอกข้อมูลไม่ครบ',
                'กรุณากรอกข้อ ' + missing.join(', '),
                'warning'
            );
            return;
        }

        const formData = new FormData(form[0]);
        // ตรวจสอบว่าเป็นโหมดแก้ไขหรือเพิ่มใหม่
        let isEdit = $('#addVisitModalLabel').text().includes('แก้ไข');
        let apiUrl = isEdit ? 'api/update_visit_data.php' : 'api/save_visit_data.php';

        $.ajax({
            url: apiUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                let res;
                if (typeof response === 'string') {
                    try { res = JSON.parse(response); } catch { res = {success: false, message: 'เกิดข้อผิดพลาด'}; }
                } else {
                    res = response;
                }
                if (res.success) {
                    Swal.fire('สำเร็จ', res.message || 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success');
                    $('#addVisitModal').modal('hide');
                    loadVisitTable();
                } else {
                    Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                }
            },
            error: function () {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
            }
        });
    });
});
</script>
</body>
</html>
