<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Behavior.php");
include_once("../class/Utils.php");
include_once("../config/Setting.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$behavior = new Behavior($db);

if (isset($_SESSION['Director_login'])) {
    $userid = $_SESSION['Director_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
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
                        <h5 class="m-0">จัดการข้อมูลพฤติกรรมนักเรียน</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="card container mx-auto px-4 py-6 ">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">จัดการข้อมูลพฤติกรรมนักเรียน</h2>
                    <button id="btnAddBehavior" class="btn btn-primary ml-2">+ เพิ่มพฤติกรรม</button>
                </div>
                <div class="overflow-x-auto">
                    <table id="behaviorTable" class="min-w-full divide-y divide-gray-200 table-auto" style="width:100%">
                        <thead class="bg-indigo-500">
                            <tr>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">วันที่</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">รหัสนักเรียน</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">ชื่อ-นามสกุล</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">ชั้น/ห้อง</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">ประเภท</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">รายละเอียด</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">คะแนน</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">ครูผู้บันทึก</th>
                                <th class="px-4 py-2 text-center font-medium text-white uppercase tracking-wider border-b">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="behaviorTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <!-- Modal for adding/editing behavior -->
        <div class="modal fade" id="addBehaviorModal" tabindex="-1" role="dialog" aria-labelledby="addBehaviorModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBehaviorModalLabel">เพิ่มข้อมูลพฤติกรรม</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addBehaviorForm">
                            <!-- Student Selection -->
                            <div class="form-group">
                                <div id="searchResults" class="text-center"></div>
                            </div>
                            <div class="form-group">
                                <label for="addStu_id">รหัสนักเรียน : </label>
                                <input type="text" class="form-control text-center" id="addStu_id" name="addStu_id" maxlength="5" required>
                            </div>
                            <div class="form-group">
                                <label for="addBehavior_date">วันที่ : </label>
                                <input type="date" class="form-control text-center" id="addBehavior_date" name="addBehavior_date" value="<?= date('Y-m-d')?>" required>
                            </div>
                            <div class="form-group">
                                <label for="addBehavior_type">ประเภท : </label>
                                <select name="addBehavior_type" id="addBehavior_type" class="form-control text-center" required>
                                    <option value="">-- เลือกประเภทพฤติกรรม --</option>
                                    <option value="หนีเรียนหรือออกนอกสถานศึกษา">หนีเรียนหรือออกนอกสถานศึกษา</option>
                                    <option value="เล่นการพนัน">เล่นการพนัน</option>
                                    <option value="มาโรงเรียนสาย">มาโรงเรียนสาย</option>
                                    <option value="แต่งกาย/ทรงผมผิดระเบียบ">แต่งกาย/ทรงผมผิดระเบียบ</option>
                                    <option value="พกพาอาวุธหรือวัตถุระเบิด">พกพาอาวุธหรือวัตถุระเบิด</option>
                                    <option value="เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์">เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์</option>
                                    <option value="สูบบุหรี่">สูบบุหรี่</option>
                                    <option value="เสพยาเสพติด">เสพยาเสพติด</option>
                                    <option value="ลักทรัพย์ กรรโชกทรัพย์">ลักทรัพย์ กรรโชกทรัพย์</option>
                                    <option value="ก่อเหตุทะเลาะวิวาท">ก่อเหตุทะเลาะวิวาท</option>
                                    <option value="แสดงพฤติกรรมทางชู้สาว">แสดงพฤติกรรมทางชู้สาว</option>
                                    <option value="จอดรถในที่ห้ามจอด">จอดรถในที่ห้ามจอด</option>
                                    <option value="แสดงพฤติกรรมก้าวร้าว">แสดงพฤติกรรมก้าวร้าว</option>
                                    <option value="มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ">มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="addBehavior_name">รายละเอียด : </label>
                                <input type="text" class="form-control text-center" id="addBehavior_name" name="addBehavior_name" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <label for="addBehavior_score">คะแนน : </label>
                                <input type="number" class="form-control text-center" id="addBehavior_score" name="addBehavior_score" required>
                            </div>
                            <input type="hidden" id="addTeach_id" name="addTeach_id" value="<?= $userData['Teach_id'] ?>" required>
                            <input type="hidden" id="addBehavior_term" name="addBehavior_term" value="<?= $term ?>" required>
                            <input type="hidden" id="addBehavior_pee" name="addBehavior_pee" value="<?= $pee ?>" required>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="button" id="submitAddBehaviorForm" class="btn btn-primary">บันทึก</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Modal -->
        <div class="modal fade" id="editBehaviorModal" tabindex="-1" role="dialog" aria-labelledby="editBehaviorModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBehaviorModalLabel">แก้ไขข้อมูลพฤติกรรม</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editBehaviorForm">
                            <input type="hidden" id="editBehavior_id" name="editBehavior_id" required>
                            <!-- Student Selection -->
                            <div class="form-group">
                                <div id="searchResultsEdit" class="text-center"></div>
                            </div>
                            <div class="form-group">
                                <label for="editStu_id">รหัสนักเรียน : </label>
                                <input type="text" class="form-control text-center" id="editStu_id" name="editStu_id" maxlength="5" required>
                            </div>
                            <div class="form-group">
                                <label for="editBehavior_date">วันที่ : </label>
                                <input type="date" class="form-control text-center" id="editBehavior_date" name="editBehavior_date" required>
                            </div>
                            <div class="form-group">
                                <label for="editBehavior_type">ประเภท : </label>
                                <select name="editBehavior_type" id="editBehavior_type" class="form-control text-center" required>
                                    <option value="">-- เลือกประเภทพฤติกรรม --</option>
                                    <option value="หนีเรียนหรือออกนอกสถานศึกษา">หนีเรียนหรือออกนอกสถานศึกษา</option>
                                    <option value="เล่นการพนัน">เล่นการพนัน</option>
                                    <option value="มาโรงเรียนสาย">มาโรงเรียนสาย</option>
                                    <option value="แต่งกาย/ทรงผมผิดระเบียบ">แต่งกาย/ทรงผมผิดระเบียบ</option>
                                    <option value="พกพาอาวุธหรือวัตถุระเบิด">พกพาอาวุธหรือวัตถุระเบิด</option>
                                    <option value="เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์">เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์</option>
                                    <option value="สูบบุหรี่">สูบบุหรี่</option>
                                    <option value="เสพยาเสพติด">เสพยาเสพติด</option>
                                    <option value="ลักทรัพย์ กรรโชกทรัพย์">ลักทรัพย์ กรรโชกทรัพย์</option>
                                    <option value="ก่อเหตุทะเลาะวิวาท">ก่อเหตุทะเลาะวิวาท</option>
                                    <option value="แสดงพฤติกรรมทางชู้สาว">แสดงพฤติกรรมทางชู้สาว</option>
                                    <option value="จอดรถในที่ห้ามจอด">จอดรถในที่ห้ามจอด</option>
                                    <option value="แสดงพฤติกรรมก้าวร้าว">แสดงพฤติกรรมก้าวร้าว</option>
                                    <option value="มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ">มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editBehavior_name">รายละเอียด : </label>
                                <input type="text" class="form-control text-center" id="editBehavior_name" name="editBehavior_name" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <label for="editBehavior_score">คะแนน : </label>
                                <input type="number" class="form-control text-center" id="editBehavior_score" name="editBehavior_score" required>
                            </div>
                            <div class="form-group">
                                <label for="editTeach_id">รหัสครูผู้บันทึก : </label>
                                <input type="text" class="form-control text-center" id="editTeach_id" name="editTeach_id" maxlength="10" required>
                            </div>
                            <div class="form-group">
                                <label for="editBehavior_term">ภาคเรียน : </label>
                                <input type="number" class="form-control text-center" id="editBehavior_term" name="editBehavior_term" min="1" max="2" required>
                            </div>
                            <div class="form-group">
                                <label for="editBehavior_pee">ปีการศึกษา : </label>
                                <input type="number" class="form-control text-center" id="editBehavior_pee" name="editBehavior_pee" min="2560" max="2700" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="button" id="submitEditBehaviorForm" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
        // ใส่ token key ที่นี่ (ต้องตรงกับใน api_behavior.php)
        const API_TOKEN_KEY = 'YOUR_SECURE_TOKEN_HERE';
        let behaviorTable;
        $(document).ready(function() {
            behaviorTable = $('#behaviorTable').DataTable({
                columnDefs: [
                    { className: 'text-center', width: '10%', targets: 0 },
                    { className: 'text-center', width: '10%', targets: 1 },
                    { className: 'text-left', width: '20%', targets: 2 },
                    { className: 'text-center', width: '10%', targets: 3 },
                    { className: 'text-center', width: '10%', targets: 4 },
                    { className: 'text-left', width: '15%', targets: 5 },
                    { className: 'text-center', width: '5%', targets: 6 },
                    { className: 'text-center', width: '10%', targets: 7 },
                    { className: 'text-center', width: '10%', targets: 8 }
                ],
                autoWidth: false,
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                pagingType: 'full_numbers',
                searching: true,
            });
            loadBehaviors();

            $('#btnAddBehavior').on('click', function() {
                $('#addBehaviorForm')[0].reset();
                $('#searchResults').html('');
                $('#addBehaviorModalLabel').text('เพิ่มข้อมูลพฤติกรรม');
                $('#addBehaviorModal').modal('show');
            });

            // --- เพิ่ม event สำหรับค้นหานักเรียน ---
            $('#addStu_id').on('input', function() {
                const stuId = $(this).val().trim();
                if (stuId.length === 0) {
                    $('#searchResults').html('');
                    return;
                }
                fetch('api/api_student_search.php?stu_id=' + encodeURIComponent(stuId) + '&token=' + encodeURIComponent(API_TOKEN_KEY))
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.Stu_id) {
                            $('#searchResults').html(`
                                <div class="flex items-center justify-center gap-4 my-2">
                                    <img src="${data.Stu_picture ? '<?=$setting->getImgProfileStudent()?>' + data.Stu_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.Stu_name + ' ' + data.Stu_sur)}" alt="student" class="w-25 h-25 rounded-full border border-gray-300 object-cover">
                                    <div class="text-left">
                                        <div class="font-bold text-lg">${data.Stu_pre}${data.Stu_name} ${data.Stu_sur}</div>
                                        <div class="text-sm text-gray-600">เลขที่ <span class="font-semibold">${data.Stu_no}</span> | ชั้น <span class="font-semibold">ม.${data.Stu_major}/${data.Stu_room}</span></div>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#searchResults').html('<div class="text-red-500 text-sm">ไม่พบนักเรียน</div>');
                        }
                    })
                    .catch(() => $('#searchResults').html('<div class="text-red-500 text-sm">ไม่พบนักเรียน</div>'));
            });

            // สำหรับ modal edit
            $('#editStu_id').on('input', function() {
                const stuId = $(this).val().trim();
                if (stuId.length === 0) {
                    $('#searchResultsEdit').html('');
                    return;
                }
                fetch('api/api_student_search.php?stu_id=' + encodeURIComponent(stuId) + '&token=' + encodeURIComponent(API_TOKEN_KEY))
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.Stu_id) {
                            $('#searchResultsEdit').html(`
                                <div class="flex items-center justify-center gap-4 my-2">
                                    <img src="${data.Stu_picture ? '<?=$setting->getImgProfileStudent()?>' + data.Stu_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.Stu_name + ' ' + data.Stu_sur)}" alt="student" class="w-25 h-25 rounded-full border border-gray-300 object-cover">
                                    <div class="text-left">
                                        <div class="font-bold text-lg">${data.Stu_pre}${data.Stu_name} ${data.Stu_sur}</div>
                                        <div class="text-sm text-gray-600">เลขที่ <span class="font-semibold">${data.Stu_no}</span> | ชั้น <span class="font-semibold">ม.${data.Stu_major}/${data.Stu_room}</span></div>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#searchResultsEdit').html('<div class="text-red-500 text-sm">ไม่พบนักเรียน</div>');
                        }
                    })
                    .catch(() => $('#searchResultsEdit').html('<div class="text-red-500 text-sm">ไม่พบนักเรียน</div>'));
            });

            // แสดงข้อมูลทันทีเมื่อเปิด modal edit
            $(document).on('shown.bs.modal', '#editBehaviorModal', function () {
                const stuId = $('#editStu_id').val().trim();
                if (stuId.length === 0) {
                    $('#searchResultsEdit').html('');
                    return;
                }
                fetch('api/api_student_search.php?stu_id=' + encodeURIComponent(stuId) + '&token=' + encodeURIComponent(API_TOKEN_KEY))
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.Stu_id) {
                            $('#searchResultsEdit').html(`
                                <div class="flex items-center justify-center gap-4 my-2">
                                    <img src="${data.Stu_picture ? '<?=$setting->getImgProfileStudent()?>' + data.Stu_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.Stu_name + ' ' + data.Stu_sur)}" alt="student" class="w-25 h-25 rounded-full border border-gray-300 object-cover">
                                    <div class="text-left">
                                        <div class="font-bold text-lg">${data.Stu_pre}${data.Stu_name} ${data.Stu_sur}</div>
                                        <div class="text-sm text-gray-600">เลขที่ <span class="font-semibold">${data.Stu_no}</span> | ชั้น <span class="font-semibold">ม.${data.Stu_major}/${data.Stu_room}</span></div>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#searchResultsEdit').html('<div class="text-red-500 text-sm">ไม่พบนักเรียน</div>');
                        }
                    })
                    .catch(() => $('#searchResultsEdit').html('<div class="text-red-500 text-sm">ไม่พบนักเรียน</div>'));
            });
            // ตั้งค่าคะแนนอัตโนมัติตามประเภทพฤติกรรม
            $('#addBehavior_type').on('change', function() {
                const type = $(this).val();
                let results = '';
                switch (type) {
                    case "หนีเรียนหรือออกนอกสถานศึกษา":
                        results = 10;
                        break;
                    case "เล่นการพนัน":
                        results = 20;
                        break;
                    case "มาโรงเรียนสาย":
                        results = 5;
                        break;
                    case "แต่งกาย/ทรงผมผิดระเบียบ":
                        results = 5;
                        break;
                    case "พกพาอาวุธหรือวัตถุระเบิด":
                        results = 20;
                        break;
                    case "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์":
                        results = 20;
                        break;
                    case "สูบบุหรี่":
                        results = 30;
                        break;
                    case "เสพยาเสพติด":
                        results = 30;
                        break;
                    case "ลักทรัพย์ กรรโชกทรัพย์":
                        results = 30;
                        break;
                    case "ก่อเหตุทะเลาะวิวาท":
                        results = 20;
                        break;
                    case "แสดงพฤติกรรมทางชู้สาว":
                        results = 20;
                        break;
                    case "จอดรถในที่ห้ามจอด":
                        results = 10;
                        break;
                    case "แสดงพฤติกรรมก้าวร้าว":
                        results = 10;
                        break;
                    case "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ":
                        results = 5;
                        break;
                    default:
                        results = '';
                }
                $('#addBehavior_score').val(results);
            });

            $('#submitAddBehaviorForm').on('click', async function() {
                const form = document.getElementById('addBehaviorForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                const formData = new FormData(form);
                formData.append('token', API_TOKEN_KEY);
                const res = await fetch('api/api_behavior.php?action=create&token=' + encodeURIComponent(API_TOKEN_KEY), {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#addBehaviorModal').modal('hide');
                    loadBehaviors();
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: result.message || 'ไม่สามารถบันทึกข้อมูลได้'
                    });
                }
            });

            $('#submitEditBehaviorForm').on('click', async function() {
                const form = document.getElementById('editBehaviorForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                const formData = new FormData(form);
                formData.append('token', API_TOKEN_KEY);
                const res = await fetch('api/api_behavior.php?action=update&token=' + encodeURIComponent(API_TOKEN_KEY), {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#editBehaviorModal').modal('hide');
                    loadBehaviors();
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: result.message || 'ไม่สามารถบันทึกข้อมูลได้'
                    });
                }
            });
        });

        async function loadBehaviors() {
            const res = await fetch('api/api_behavior.php?action=list&token=' + encodeURIComponent(API_TOKEN_KEY));
            const data = await res.json();
            behaviorTable.clear();
            data.forEach(behavior => {
                behaviorTable.row.add([
                    behavior.behavior_date,
                    behavior.Stu_id,
                    behavior.Stu_pre + behavior.Stu_name + ' ' + behavior.Stu_sur,
                    'ม.' + behavior.Stu_major + '/' + behavior.Stu_room,
                    behavior.behavior_type,
                    behavior.behavior_name,
                    behavior.behavior_score,
                    behavior.teacher_behavior,
                    `<button class="btn btn-warning btn-sm editBehaviorBtn" data-id="${behavior.id}">แก้ไข</button>
                     <button class="btn btn-danger btn-sm deleteBehaviorBtn" data-id="${behavior.id}">ลบ</button>`
                ]);
            });
            behaviorTable.draw();
        }

        $(document).on('click', '.editBehaviorBtn', function() {
            const id = $(this).data('id');
            openEditBehaviorModal(id);
        });
        $(document).on('click', '.deleteBehaviorBtn', function() {
            const id = $(this).data('id');
            deleteBehavior(id);
        });

        async function openEditBehaviorModal(id) {
            const res = await fetch('api/api_behavior.php?action=get&id=' + id + '&token=' + encodeURIComponent(API_TOKEN_KEY));
            const data = await res.json();
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: data.message || 'ไม่สามารถโหลดข้อมูลได้'
                });
                return;
            }
            const form = document.getElementById('editBehaviorForm');
            form.reset();
            const dateParts = data.behavior_date.split('-'); // แยกปี-เดือน-วัน
            const year = parseInt(dateParts[0], 10) - 543; // แปลงปี พ.ศ. เป็น ค.ศ.
            const month = dateParts[1];
            const day = dateParts[2];
            const newDate = `${year}-${month}-${day}`; // เอาปีที่ลบ 543 แล้วมาประกอบใหม่
            

            document.getElementById('editBehavior_id').value = data.id;
            document.getElementById('editStu_id').value = data.stu_id;
            document.getElementById('editBehavior_date').value = newDate;
            document.getElementById('editBehavior_type').value = data.behavior_type;
            document.getElementById('editBehavior_name').value = data.behavior_name;
            document.getElementById('editBehavior_score').value = data.behavior_score;
            document.getElementById('editTeach_id').value = data.teach_id;
            document.getElementById('editBehavior_term').value = data.behavior_term;
            document.getElementById('editBehavior_pee').value = data.behavior_pee;
            $('#editBehaviorModalLabel').text('แก้ไขข้อมูลพฤติกรรม');
            $('#editBehaviorModal').modal('show');
        }

        async function deleteBehavior(id) {
            const result = await Swal.fire({
                title: 'ยืนยันการลบข้อมูลพฤติกรรมนี้?',
                text: "คุณต้องการลบข้อมูลนี้หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            });
            if (!result.isConfirmed) return;
            const res = await fetch('api/api_behavior.php?action=delete&token=' + encodeURIComponent(API_TOKEN_KEY), {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id) + '&token=' + encodeURIComponent(API_TOKEN_KEY)
            });
            const response = await res.json();
            if (response.success) {
                loadBehaviors();
                Swal.fire({
                    icon: 'success',
                    title: 'ลบข้อมูลสำเร็จ',
                    showConfirmButton: false,
                    timer: 1200
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถลบข้อมูลได้'
                });
            }
        }
        </script>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
