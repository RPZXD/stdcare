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
                        <h5 class="m-0">บันทึกแบบคัดกรองนักเรียน 11 ด้าน</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div id="screen11-status" class="max-w-4xl mx-auto mt-6"></div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>

<!-- Modal สำหรับฟอร์มบันทึก 11 ด้าน -->
<div class="modal fade" id="addScreen11Modal" tabindex="-1" aria-labelledby="addScreen11ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title text-lg font-semibold" id="addScreen11ModalLabel">บันทึกแบบคัดกรองนักเรียน 11 ด้าน</h3>
        <button type="button" class="close text-gray-500 hover:text-gray-700 text-2xl" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="addScreen11ModalContent" class="modal-body p-6">
        <!-- ฟอร์มจะถูกโหลดที่นี่ -->
      </div>
      <div class="modal-footer flex justify-between">
        <button type="button" class="btn bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600" data-dismiss="modal">ปิดหน้าต่าง</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
    // โหลดสถานะ 11 ด้าน
    function loadScreen11Table() {
        const container = $('#screen11-status');
        container.html('<div class="text-center text-gray-500">กำลังโหลดข้อมูล...</div>');
        const pee = <?php echo json_encode($pee); ?>;
        const term = <?php echo json_encode($term); ?>;
        const stuId = <?php echo json_encode($student_id); ?>;

        $.ajax({
            url: 'api/student_screen11_status.php', // คุณต้องสร้างไฟล์นี้
            method: 'GET',
            dataType: 'json',
            data: { pee: pee, stuId: stuId, term: term },
            success: function(data) {
                let html = `
                    <div class="bg-white shadow rounded-lg p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">แบบคัดกรอง</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">สถานะ</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                `;
                // 11 ด้าน Self
                html += `<tr>
                    <td class="px-4 py-2">แบบคัดกรองนักเรียน 11 ด้าน (นักเรียนประเมินตนเอง)</td>`;
                if (data.self && data.self.status === 'saved') {
                    html += `<td class="px-4 py-2"><span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded">บันทึกแล้ว</span></td>
                        <td class="px-4 py-2 space-x-2">
                            <button type="button" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded" onclick="viewScreen11Self()">ดู</button>
                            <button type="button" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded" onclick="editScreen11Self()">แก้ไข</button>
                            <button type="button" class="inline-block bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded" onclick="interpretScreen11Self()">แปลผล</button>
                        </td>`;
                } else {
                    html += `<td class="px-4 py-2"><span class="inline-block px-2 py-1 bg-gray-200 text-gray-700 rounded">ยังไม่ได้บันทึก</span></td>
                        <td class="px-4 py-2">
                            <button type="button" class="inline-block bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded" onclick="addScreen11Self()">บันทึก</button>
                        </td>`;
                }
                html += `</tr>`;

                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                container.html(html);
            },
            error: function() {
                $('#screen11-status').html('<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>');
            }
        });
    }

    // โหลด 11 ด้าน table เมื่อเปิดหน้า
    loadScreen11Table();

    // Modal logic for 11 ด้าน Self
    window.addScreen11Self = function() {
        showScreen11Modal('template_form/add_screen11_self_form.php', 'บันทึกแบบคัดกรองนักเรียน 11 ด้าน (นักเรียนประเมินตนเอง)', true);
    };
    window.editScreen11Self = function() {
        showScreen11Modal('template_form/edit_screen11_self_form.php', 'แก้ไขแบบคัดกรองนักเรียน 11 ด้าน (นักเรียนประเมินตนเอง)', true);
    };
    window.viewScreen11Self = function() {
        showScreen11Modal('template_form/view_screen11_self_form.php', 'ดูแบบคัดกรองนักเรียน 11 ด้าน (นักเรียนประเมินตนเอง)', false);
    };
    window.interpretScreen11Self = function() {
        showScreen11Modal('template_form/interpret_screen11_self.php', 'แปลผลแบบคัดกรองนักเรียน 11 ด้าน (นักเรียนประเมินตนเอง)', false);
    };

    // Generic modal loader
    function showScreen11Modal(url, title, showSave) {
        const content = $('#addScreen11ModalContent');
        const modal = $('#addScreen11Modal');
        content.html('<div class="text-center text-gray-500">กำลังโหลดฟอร์ม...</div>');
        $('#addScreen11ModalLabel').text(title);

        const pee = <?php echo json_encode($pee); ?>;
        const term = <?php echo json_encode($term); ?>;
        const stuId = <?php echo json_encode($student_id); ?>;
        $.ajax({
            url: url,
            method: 'GET',
            data: { pee: pee, stuId: stuId, term: term },
            dataType: 'html',
            success: function(html) {
                content.html(html);
                modal.modal('show');
            },
            error: function() {
                content.html('<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดฟอร์ม</div>');
            }
        });
    }

    // Save 11 ด้าน form (AJAX submit)
    $('#saveScreen11Btn').on('click', function () {
        const form = $('#screen11Form');


        const formData = new FormData(form[0]);
        let isEdit = $('#addScreen11ModalLabel').text().includes('แก้ไข');
        let apiUrl = isEdit ? 'api/update_screen11_data.php' : 'api/save_screen11_data.php';

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
                    $('#addScreen11Modal').modal('hide');
                    loadScreen11Table();
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
