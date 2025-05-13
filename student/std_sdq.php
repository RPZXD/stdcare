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
                        <h5 class="m-0">บันทึกข้อมูลแบบประเมิน SDQ</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div id="visit-home-status" class="max-w-4xl mx-auto mt-6"></div>
            
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>

<!-- Modal สำหรับฟอร์มบันทึก SDQ -->
<div class="modal fade" id="addVisitModal" tabindex="-1" aria-labelledby="addVisitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title text-lg font-semibold" id="addVisitModalLabel">บันทึก SDQ</h3>
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
    // Load SDQ status table for the student (ปีละครั้ง)
    function loadSDQTable() {
        const container = $('#visit-home-status');
        container.html('<div class="text-center text-gray-500">กำลังโหลดข้อมูล...</div>');
        const pee = <?php echo json_encode($pee); ?>;
        const term = <?php echo json_encode($term); ?>;
        const stuId = <?php echo json_encode($student_id); ?>;

        $.ajax({
            url: 'api/student_sdq_status.php', // คุณต้องสร้างไฟล์นี้ให้ส่งข้อมูล SDQ self/parent
            method: 'GET',
            dataType: 'json',
            data: { pee: pee, stuId: stuId, term: term },
            success: function(data) {
                let html = `
                    <div class="bg-white shadow rounded-lg p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">แบบประเมิน</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">สถานะ</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                `;
                // SDQ Self
                html += `<tr>
                    <td class="px-4 py-2">SDQ นักเรียนประเมินตนเอง</td>`;
                if (data.self && data.self.status === 'saved') {
                    html += `<td class="px-4 py-2"><span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded">บันทึกแล้ว</span></td>
                        <td class="px-4 py-2 space-x-2">
                            <button type="button" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded" onclick="viewSDQSelf()">ดู</button>
                            // <button type="button" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded" onclick="editSDQSelf()">แก้ไข</button>
                            <button type="button" class="inline-block bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded" onclick="interpretSDQSelf()">แปลผล</button>
                        </td>`;
                } else {
                    html += `<td class="px-4 py-2"><span class="inline-block px-2 py-1 bg-gray-200 text-gray-700 rounded">ยังไม่ได้บันทึก</span></td>
                        <td class="px-4 py-2">
                            <button type="button" class="inline-block bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded" onclick="addSDQSelf()">บันทึก</button>
                        </td>`;
                }
                html += `</tr>`;

                // SDQ Parent
                html += `<tr>
                    <td class="px-4 py-2">SDQ ผู้ปกครองประเมิน</td>`;
                if (data.parent && data.parent.status === 'saved') {
                    html += `<td class="px-4 py-2"><span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded">บันทึกแล้ว</span></td>
                        <td class="px-4 py-2 space-x-2">
                            <button type="button" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded" onclick="viewSDQParent()">ดู</button>
                            // <button type="button" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded" onclick="editSDQParent()">แก้ไข</button>
                            <button type="button" class="inline-block bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded" onclick="interpretSDQParent()">แปลผล</button>
                        </td>`;
                } else {
                    html += `<td class="px-4 py-2"><span class="inline-block px-2 py-1 bg-gray-200 text-gray-700 rounded">ยังไม่ได้บันทึก</span></td>
                        <td class="px-4 py-2">
                            <button type="button" class="inline-block bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded" onclick="addSDQParent()">บันทึก</button>
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
                $('#visit-home-status').html('<div class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>');
            }
        });
    }

    // Load SDQ table on page load
    loadSDQTable();

    // Modal logic for SDQ Self
    window.addSDQSelf = function() {
        showSDQModal('template_form/add_sdq_self_form.php', 'บันทึก SDQ นักเรียนประเมินตนเอง', true);
    };
    window.editSDQSelf = function() {
        showSDQModal('template_form/edit_sdq_self_form.php', 'แก้ไข SDQ นักเรียนประเมินตนเอง', true);
    };
    window.viewSDQSelf = function() {
        showSDQModal('template_form/view_sdq_self_form.php', 'ดู SDQ นักเรียนประเมินตนเอง', false);
    };
    window.interpretSDQSelf = function() {
        showSDQModal('template_form/interpret_sdq_self.php', 'แปลผล SDQ นักเรียนประเมินตนเอง', false);
    };

    // Modal logic for SDQ Parent
    window.addSDQParent = function() {
        showSDQModal('template_form/add_sdq_parent_form.php', 'บันทึก SDQ ผู้ปกครองประเมิน', true);
    };
    window.editSDQParent = function() {
        showSDQModal('template_form/edit_sdq_parent_form.php', 'แก้ไข SDQ ผู้ปกครองประเมิน', true);
    };
    window.viewSDQParent = function() {
        showSDQModal('template_form/view_sdq_parent_form.php', 'ดู SDQ ผู้ปกครองประเมิน', false);
    };
    window.interpretSDQParent = function() {
        showSDQModal('template_form/interpret_sdq_parent.php', 'แปลผล SDQ ผู้ปกครองประเมิน', false);
    };

    // Generic modal loader
    function showSDQModal(url, title, showSave) {
        const content = $('#addVisitModalContent');
        const modal = $('#addVisitModal');
        content.html('<div class="text-center text-gray-500">กำลังโหลดฟอร์ม...</div>');
        $('#addVisitModalLabel').text(title);
        if (showSave) {
            $('#saveVisitBtn').show();
        } else {
            $('#saveVisitBtn').hide();
        }
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

    // Save SDQ form (AJAX submit)
    $('#saveVisitBtn').on('click', function () {
        const form = $('#sdqForm');
        if (form.length === 0) return;

        // ตรวจสอบว่ากรอกครบ (ตัวอย่าง: 25 ข้อ)
        let missing = [];
        for (let i = 1; i <= 25; i++) {
            // ตรวจสอบทั้ง sdq1 และ q1
            if (
                form.find('input[name="sdq'+i+'"]:checked').length === 0 &&
                form.find('input[name="q'+i+'"]:checked').length === 0
            ) {
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
        let apiUrl = isEdit ? 'api/update_sdq_data.php' : 'api/save_sdq_data.php';

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
                    loadSDQTable();
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
