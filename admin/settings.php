<?php
// (1) !! KEV: แก้ไขส่วน PHP ด้านบน !!
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
use App\DatabaseUsers;
include_once("../class/UserLogin.php");
include_once("../class/Utils.php");

// --- (เพิ่ม) ---
// (เพิ่ม Model สำหรับดึงค่า Setting เวลา)
require_once(__DIR__ . "/../models/SettingModel.php");
use App\Models\SettingModel;
// --- (สิ้นสุดส่วนที่เพิ่ม) ---

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connectDB = new DatabaseUsers();
$db = $connectDB->getPDO();
$user = new UserLogin($db);
// (สิ้นสุดการแก้ไข PHP)

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Admin_login'])) {
    $userid = $_SESSION['Admin_login'];
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

require_once('header.php');

// (ดึงข้อมูล ชั้น/ห้อง สำหรับ dropdown)
$studentClass = $db->query("SELECT DISTINCT Stu_major FROM student WHERE Stu_major IS NOT NULL AND Stu_status = '1' ORDER BY Stu_major")->fetchAll(PDO::FETCH_COLUMN);
$studentRoom = $db->query("SELECT DISTINCT Stu_room FROM student WHERE Stu_room IS NOT NULL AND Stu_status = '1' ORDER BY Stu_room")->fetchAll(PDO::FETCH_COLUMN);

// --- (เพิ่ม) ดึงค่าเวลาปัจจุบัน ---
$settingsModel = new SettingModel($db);
$timeSettings = $settingsModel->getAllTimeSettings();
$arrival_late_time = $timeSettings['arrival_late_time'] ?? '08:00:00';
$arrival_absent_time = $timeSettings['arrival_absent_time'] ?? '10:00:00';
$leave_early_time = $timeSettings['leave_early_time'] ?? '15:40:00';
$scan_crossover_time = $timeSettings['scan_crossover_time'] ?? '12:00:00';
// --- (สิ้นสุดส่วนที่เพิ่ม) ---

?>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">⚙️ ตั้งค่าระบบ</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-6">
                        <div class="card card-primary card-outline shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title">1. ตั้งค่าปีการศึกษา / เทอม</h3>
                            </div>
                            <form id="termPeeForm">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="academic_year">ปีการศึกษา:</label>
                                        <input type="number" class="form-control" id="academic_year" name="academic_year" value="<?php echo htmlspecialchars($pee); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="term">เทอม:</label>
                                        <select class="form-control" id="term" name="term" required>
                                            <option value="1" <?php echo ($term == 1) ? 'selected' : ''; ?>>1</option>
                                            <option value="2" <?php echo ($term == 2) ? 'selected' : ''; ?>>2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">บันทึก</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-info card-outline shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title">ตั้งค่าเวลาสแกน (ใหม่)</h3>
                            </div>
                            <form id="timeSettingsForm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="arrival_late_time">เวลาเริ่มสาย:</label>
                                                <input type="time" class="form-control" id="arrival_late_time" name="arrival_late_time" value="<?php echo htmlspecialchars($arrival_late_time); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="arrival_absent_time">เวลาตัดขาดเรียน:</label>
                                                <input type="time" class="form-control" id="arrival_absent_time" name="arrival_absent_time" value="<?php echo htmlspecialchars($arrival_absent_time); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="leave_early_time">เวลาตัดกลับก่อน:</label>
                                                <input type="time" class="form-control" id="leave_early_time" name="leave_early_time" value="<?php echo htmlspecialchars($leave_early_time); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="scan_crossover_time">เวลาตัดเช้า/บ่าย:</label>
                                                <input type="time" class="form-control" id="scan_crossover_time" name="scan_crossover_time" value="<?php echo htmlspecialchars($scan_crossover_time); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">บันทึกการตั้งค่าเวลา</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-danger card-outline shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title">2. เลื่อนชั้นปีนักเรียน (อันตราย)</h3>
                            </div>
                            <div class="card-body">
                                <p>การดำเนินการนี้จะเลื่อนชั้นนักเรียนทั้งหมด และตั้งค่าสถานะ "จบการศึกษา" 
                                   ให้กับนักเรียน ม.3 และ ม.6 <strong>(ควรกระทำเพียงปีละ 1 ครั้ง)</strong></p>
                            </div>
                            <div class="card-footer">
                                <button type="button" id="promoteBtn" class="btn btn-danger">ยืนยันการเลื่อนชั้นปี</button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">3. อัปเดตข้อมูลด้วย CSV (แทนที่ Excel)</h3>
                    </div>
                    <div class="card-body">
                        
                        <div class="mb-4 p-3 border rounded">
                            <h5><i class="fas fa-sort-numeric-down"></i> อัปเดตเลขที่นักเรียน (CSV)</h5>
                            <form id="uploadNumberForm">
                                <div class="form-group">
                                    <label>1. ดาวน์โหลดเทมเพลต (CSV) (รวมข้อมูล นร. ปัจจุบัน)</label><br>
                                    <a href="../controllers/SettingController.php?action=download_number_template" class="btn btn-sm btn-secondary">ดาวน์โหลดเทมเพลตเลขที่</a>
                                </div>
                                <div class="form-group">
                                    <label for="number_csv">2. อัปโหลดไฟล์ CSV (ที่แก้ไข 'Stu_no_new')</label>
                                    <input type="file" class="form-control" id="number_csv" name="number_csv" accept=".csv" required>
                                </div>
                                <button type="submit" class="btn btn-info">อัปโหลด (อัปเดตเลขที่)</button>
                            </form>
                        </div>
                        
                        <div class="mb-4 p-3 border rounded">
                            <h5><i class="fas fa-user-plus"></i> เทมเพลตนักเรียนใหม่ (CSV)</h5>
                            <p>สำหรับเพิ่มนักเรียนใหม่ (ใช้หน้านี้ดาวน์โหลดเทมเพลต แต่ให้อัปโหลดที่หน้า "จัดการข้อมูลนักเรียน")</p>
                            <a href="../controllers/SettingController.php?action=download_new_student_template" class="btn btn-sm btn-secondary">ดาวน์โหลดเทมเพลต นร. ใหม่</a>
                        </div>

                        <div class="p-3 border rounded">
                            <h5><i class="fas fa-file-upload"></i> อัปเดตข้อมูลนักเรียนทั้งหมด (CSV)</h5>
                            <p>ดาวน์โหลดข้อมูลนักเรียนทั้งหมดตามชั้น/ห้อง เพื่อแก้ไข แล้วอัปโหลดกลับ</p>
                            <form id="uploadFullDataForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="pe">เลือกชั้น</label>
                                        <select class="form-control" id="pe" name="pe">
                                            <option value="">ทั้งหมด</option>
                                            <?php foreach ($studentClass as $class): ?>
                                                <option value="<?php echo $class; ?>"><?php echo $class; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="room">เลือกห้อง</label>
                                        <select class="form-control" id="room" name="room">
                                            <option value="">ทั้งหมด</option>
                                            <?php foreach ($studentRoom as $room): ?>
                                                <option value="<?php echo $room; ?>"><?php echo $room; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label><br>
                                        <button type="button" id="downloadFullDataBtn" class="btn btn-secondary">ดาวน์โหลดข้อมูล (CSV)</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group mt-3">
                                    <label for="student_csv">2. อัปโหลดไฟล์ CSV (ที่แก้ไขแล้ว)</label>
                                    <input type="file" class="form-control" id="student_csv" name="student_csv" accept=".csv" required>
                                </div>
                                <button type="submit" class="btn btn-info">อัปโหลด (อัปเดตข้อมูลทั้งหมด)</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // (ฟังก์ชัน Helper สำหรับ Fetch)
    async function handleFetch(url, formData) {
        Swal.fire({
            title: 'กำลังประมวลผล...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const res = await fetch(url, { method: 'POST', body: formData });
            const data = await res.json();

            if (res.ok && data.success) {
                Swal.fire('สำเร็จ!', data.message, 'success');
            } else {
                Swal.fire('ล้มเหลว!', data.message || 'เกิดข้อผิดพลาด', 'error');
            }
        } catch (err) {
            Swal.fire('ล้มเหลว!', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
        }
    }

    // (1. อัปเดตปีการศึกษา/เทอม)
    const termPeeForm = document.getElementById('termPeeForm');
    if (termPeeForm) {
        termPeeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            handleFetch('../controllers/SettingController.php?action=update_term', formData);
        });
    }

    // (2. เลื่อนชั้น)
    const promoteBtn = document.getElementById('promoteBtn');
    if (promoteBtn) {
        promoteBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'ยืนยันการเลื่อนชั้นปี?',
                text: "การดำเนินการนี้ไม่สามารถย้อนกลับได้! (ควรกระทำปีละ 1 ครั้ง)",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ใช่, ยืนยันการเลื่อนชั้น',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    handleFetch('../controllers/SettingController.php?action=promote_students', new FormData());
                }
            });
        });
    }

    // --- (เพิ่ม) 3. อัปเดตเวลาสแกน ---
    const timeSettingsForm = document.getElementById('timeSettingsForm');
    if (timeSettingsForm) {
        timeSettingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            // (เราจะสร้าง SettingController.php เพื่อรับ action นี้)
            handleFetch('../controllers/SettingController.php?action=update_times', formData);
        });
    }

    // (3.1 อัปโหลดเลขที่)
    const uploadNumberForm = document.getElementById('uploadNumberForm');
    if (uploadNumberForm) {
        uploadNumberForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('number_csv');
            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.fire('ล้มเหลว', 'กรุณาเลือกไฟล์ CSV', 'error');
                return;
            }
            const formData = new FormData(this);
            handleFetch('../controllers/SettingController.php?action=upload_number_data', formData);
        });
    }

    // (3.3 ดาวน์โหลดข้อมูลทั้งหมด)
    const downloadFullDataBtn = document.getElementById('downloadFullDataBtn');
    if (downloadFullDataBtn) {
        downloadFullDataBtn.addEventListener('click', function() {
            const pe = document.getElementById('pe').value;
            const room = document.getElementById('room').value;
            const url = `../controllers/SettingController.php?action=download_full_data_template&pe=${pe}&room=${room}`;
            window.open(url, '_blank');
        });
    }

    // (3.3 อัปโหลดข้อมูลทั้งหมด)
    const uploadFullDataForm = document.getElementById('uploadFullDataForm');
    if (uploadFullDataForm) {
        uploadFullDataForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('student_csv');
            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.fire('ล้มเหลว', 'กรุณาเลือกไฟล์ CSV', 'error');
                return;
            }
            const formData = new FormData(this);
            handleFetch('../controllers/SettingController.php?action=upload_full_data', formData);
        });
    }

});
</script>
</body>
</html>