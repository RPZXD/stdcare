<?php
// (1) !! KEV: แก้ไขส่วน PHP ด้านบน !!
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php");
// (ไม่ต้อง include Parent.php ตัวเก่า)
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (ไม่ต้องสร้าง $parent = new StudentParent($db);)
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
                        <h5 class="m-0">👨‍👩‍👧‍👦 จัดการข้อมูลผู้ปกครอง</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-outline card-info shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-csv"></i> อัปเดตข้อมูลด้วย CSV</h3>
                    </div>
                    <div class="card-body">
                        <form id="csvUploadForm" class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label" for="csv_file">เลือกไฟล์ CSV ที่แก้ไขแล้ว</label>
                                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-upload"></i> อัปโหลด</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> ตัวกรอง</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filterClass">ชั้น</label>
                                <select id="filterClass" class="form-control">
                                    <option value="">-- ทั้งหมด --</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filterRoom">ห้อง</label>
                                <select id="filterRoom" class="form-control">
                                    <option value="">-- ทั้งหมด --</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button id="filterButton" class="btn btn-primary" style="margin-top: 32px;">ค้นหา</button>
                            </div>
                            <div class="col-md-3 text-md-left">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="button" id="downloadTemplateBtn" class="btn btn-secondary mt-4">
                                    <i class="fas fa-download"></i> โหลดข้อมูล (CSV)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-body">
                        <!-- Search Filter -->
                        <div class="mb-4">
                            <div class="relative">
                                <input type="text" id="parentSearch" placeholder="ค้นหาผู้ปกครองหรือนักเรียน... 🔍" class="w-full px-4 py-3 pl-12 pr-4 text-gray-700 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-md transition-all duration-200">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-lg">🔍</span>
                                </div>
                            </div>
                        </div>
                        <div id="loading" class="text-center py-8 text-lg font-semibold text-gray-600">
                            กำลังโหลดข้อมูลผู้ปกครอง... ⏳
                        </div>
                        <div id="parentContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="editParentModal" tabindex="-1" role="dialog" aria-labelledby="editParentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editParentModalLabel">📝 แก้ไขข้อมูลผู้ปกครอง</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editParentForm">
                        <div class="modal-body">
                            <input type="hidden" id="editStu_id" name="editStu_id">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>ชื่อบิดา</label>
                                    <input type="text" class="form-control" id="editFather_name" name="editFather_name">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>อาชีพ</label>
                                    <input type="text" class="form-control" id="editFather_occu" name="editFather_occu">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>รายได้</label>
                                    <input type="number" class="form-control" id="editFather_income" name="editFather_income">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>ชื่อมารดา</label>
                                    <input type="text" class="form-control" id="editMother_name" name="editMother_name">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>อาชีพ</label>
                                    <input type="text" class="form-control" id="editMother_occu" name="editMother_occu">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>รายได้</label>
                                    <input type="number" class="form-control" id="editMother_income" name="editMother_income">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>ชื่อผู้ปกครอง</label>
                                    <input type="text" class="form-control" id="editPar_name" name="editPar_name">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>ความเกี่ยวข้อง</label>
                                    <input type="text" class="form-control" id="editPar_relate" name="editPar_relate">
                                </div>
                                 <div class="col-md-4 form-group">
                                    <label>เบอร์โทร</label>
                                    <input type="text" class="form-control" id="editPar_phone" name="editPar_phone">
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>อาชีพ</label>
                                    <input type="text" class="form-control" id="editPar_occu" name="editPar_occu">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>รายได้</label>
                                    <input type="number" class="form-control" id="editPar_income" name="editPar_income">
                                </div>
                                 <div class="col-md-4 form-group">
                                    <label>ที่อยู่</label>
                                    <input type="text" class="form-control" id="editPar_addr" name="editPar_addr">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            <button type="button" id="submitEditParentForm" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fadeInUp 0.6s ease-out forwards;
}
.parent-card {
    transition: all 0.3s ease;
}
.parent-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}
</style>

        <script>
        // API URL for parent data
        const API_URL = '../controllers/ParentController.php';

        let allParents = [];

        function renderParents(data, filterText = '') {
            $('#parentContainer').empty();
            $('#loading').hide();
            
            let filteredData = data;
            if (filterText) {
                filteredData = data.filter(parent => {
                    const studentName = (parent.Stu_name || '') + ' ' + (parent.Stu_sur || '');
                    const parentName = parent.Par_name || '';
                    const fatherName = parent.Father_name || '';
                    const motherName = parent.Mother_name || '';
                    const studentId = parent.Stu_id || '';
                    
                    return studentName.toLowerCase().includes(filterText.toLowerCase()) ||
                           parentName.toLowerCase().includes(filterText.toLowerCase()) ||
                           fatherName.toLowerCase().includes(filterText.toLowerCase()) ||
                           motherName.toLowerCase().includes(filterText.toLowerCase()) ||
                           studentId.toLowerCase().includes(filterText.toLowerCase());
                });
            }
            
            filteredData.forEach((parent, index) => {
                const studentName = (parent.Stu_name || '') + ' ' + (parent.Stu_sur || '');
                const classRoom = 'ม.' + (parent.Stu_major || '') + '/' + (parent.Stu_room || '');
                
                let card = `
                    <div class="parent-card bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 p-6 rounded-2xl shadow-lg border border-gray-200 hover:border-emerald-300 transition-all duration-300 animate-fade-in" style="animation-delay: ${index * 0.1}s;">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 rounded-full flex items-center justify-center text-white text-2xl mr-4 shadow-lg ring-4 ring-emerald-200">
                                👨‍👩‍👧‍👦
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800 mb-1">${studentName}</h3>
                                <p class="text-sm text-emerald-600 font-medium">รหัส: ${parent.Stu_id} 📚</p>
                            </div>
                        </div>
                        <div class="space-y-3 mb-4">
                            <div class="bg-white/60 rounded-lg p-3">
                                <p class="text-sm font-semibold text-gray-700 mb-2">🏫 ข้อมูลนักเรียน</p>
                                <p class="text-sm text-gray-600">${classRoom}</p>
                            </div>
                            <div class="bg-white/60 rounded-lg p-3">
                                <p class="text-sm font-semibold text-gray-700 mb-2">👨‍👩‍👧‍👦 ข้อมูลผู้ปกครอง</p>
                                <div class="grid grid-cols-1 gap-2">
                                    ${parent.Father_name ? `<p class="text-sm"><span class="font-medium">บิดา:</span> ${parent.Father_name} 👨</p>` : ''}
                                    ${parent.Mother_name ? `<p class="text-sm"><span class="font-medium">มารดา:</span> ${parent.Mother_name} 👩</p>` : ''}
                                    ${parent.Par_name ? `<p class="text-sm"><span class="font-medium">ผู้ปกครอง:</span> ${parent.Par_name} ${parent.Par_relate ? `(${parent.Par_relate})` : ''} 👤</p>` : ''}
                                    ${parent.Par_phone ? `<p class="text-sm"><span class="font-medium">โทร:</span> ${parent.Par_phone} 📞</p>` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <button class="editParentBtn bg-gradient-to-r from-emerald-400 to-teal-500 hover:from-emerald-500 hover:to-teal-600 text-white px-6 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 text-sm font-semibold" data-id="${parent.Stu_id}">
                                แก้ไข ✏️
                            </button>
                        </div>
                    </div>
                `;
                $('#parentContainer').append(card);
            });
            
            if (filteredData.length === 0 && filterText) {
                $('#parentContainer').html('<div class="col-span-full text-center py-8 text-gray-500 text-lg">ไม่พบผู้ปกครองที่ตรงกับการค้นหา 😔</div>');
            }
        }

        function loadParents(classVal = '', roomVal = '') {
            $('#loading').show();
            $('#parentContainer').empty();
            
            let url = API_URL + "?action=list";
            if (classVal) url += "&class=" + encodeURIComponent(classVal);
            if (roomVal) url += "&room=" + encodeURIComponent(roomVal);
            
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    allParents = data;
                    renderParents(data);
                },
                error: function() {
                    $('#loading').html('<p class="text-red-500">เกิดข้อผิดพลาดในการโหลดข้อมูล 😞</p>');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadParents();

            // Search functionality
            $('#parentSearch').on('input', function() {
                const searchText = $(this).val();
                renderParents(allParents, searchText);
            });

            // Filter functionality (keep existing)
            document.getElementById('filterButton').addEventListener('click', function() {
                const classVal = document.getElementById('filterClass').value;
                const roomVal = document.getElementById('filterRoom').value;
                loadParents(classVal, roomVal);
            });

            // Populate filter selects (keep existing)
            async function populateFilterSelects() {
                const res = await fetch('../controllers/StudentController.php?action=get_filters');
                const data = await res.json();
                
                const classSel = document.getElementById('filterClass');
                data.majors.forEach(cls => {
                    if (cls) classSel.innerHTML += `<option value="${cls}">${cls}</option>`;
                });

                const roomSel = document.getElementById('filterRoom');
                data.rooms.forEach(room => {
                    if (room) roomSel.innerHTML += `<option value="${room}">${room}</option>`;
                });
            }
            populateFilterSelects();

            // Edit modal functionality (keep existing)
            $(document).on('click', '.editParentBtn', async function() {
                const id = $(this).data('id');
                const res = await fetch(API_URL + "?action=get&id=" + id);
                const p = await res.json();
                
                if (p && p.Stu_id) {
                    document.getElementById('editStu_id').value = p.Stu_id;
                    document.getElementById('editFather_name').value = p.Father_name || '';
                    document.getElementById('editFather_occu').value = p.Father_occu || '';
                    document.getElementById('editFather_income').value = p.Father_income || '';
                    document.getElementById('editMother_name').value = p.Mother_name || '';
                    document.getElementById('editMother_occu').value = p.Mother_occu || '';
                    document.getElementById('editMother_income').value = p.Mother_income || '';
                    document.getElementById('editPar_name').value = p.Par_name || '';
                    document.getElementById('editPar_relate').value = p.Par_relate || '';
                    document.getElementById('editPar_occu').value = p.Par_occu || '';
                    document.getElementById('editPar_income').value = p.Par_income || '';
                    document.getElementById('editPar_addr').value = p.Par_addr || '';
                    document.getElementById('editPar_phone').value = p.Par_phone || '';
                    $('#editParentModalLabel').text('แก้ไขข้อมูลผู้ปกครองของ: ' + p.Stu_name);
                    $('#editParentModal').modal('show');
                }
            });

            // Submit edit form (keep existing)
            $('#submitEditParentForm').on('click', async function() {
                const form = document.getElementById('editParentForm');
                const formData = new FormData(form);
                
                const res = await fetch(API_URL + '?action=update', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    $('#editParentModal').modal('hide');
                    const classVal = document.getElementById('filterClass').value;
                    const roomVal = document.getElementById('filterRoom').value;
                    loadParents(classVal, roomVal);
                    Swal.fire('✅ สำเร็จ', 'บันทึกข้อมูลสำเร็จ', 'success');
                } else {
                    Swal.fire('❌ ล้มเหลว', result.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                }
            });

            // CSV upload functionality (keep existing)
            $('#csvUploadForm').on('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                if (!formData.get('csv_file').name) {
                    Swal.fire('❌ ล้มเหลว', 'กรุณาเลือกไฟล์ CSV', 'error');
                    return;
                }
                
                Swal.fire({
                    title: 'กำลังอัปโหลด...',
                    text: 'กรุณารอสักครู่ กำลังประมวลผลไฟล์ CSV',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const res = await fetch(API_URL + '?action=upload_csv', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.status === 'completed') {
                    Swal.fire(
                        '✅ อัปโหลดสำเร็จ',
                        `บันทึกข้อมูลสำเร็จ: ${result.report.success} รายการ\nล้มเหลว: ${result.report.failed} รายการ`,
                        'success'
                    );
                    const classVal = document.getElementById('filterClass').value;
                    const roomVal = document.getElementById('filterRoom').value;
                    loadParents(classVal, roomVal);
                } else {
                    Swal.fire('❌ ล้มเหลว', result.message || 'ไม่สามารถอัปโหลดไฟล์ได้', 'error');
                }
            });

            // Download template functionality (keep existing)
            $('#downloadTemplateBtn').on('click', function() {
                const classVal = $('#filterClass').val();
                const roomVal = $('#filterRoom').val();
                const url = `${API_URL}?action=download_template&class=${encodeURIComponent(classVal)}&room=${encodeURIComponent(roomVal)}`;
                window.location.href = url;
            });
        });
        </script>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>