<?php 
require_once('header.php');
// Other require_once calls as needed
?>
<style>
    .pulse {
        animation: pulse 1s;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(34,197,94,0.7);}
        70% { box-shadow: 0 0 0 16px rgba(34,197,94,0);}
        100% { box-shadow: 0 0 0 0 rgba(34,197,94,0);}
    }
    .toast {
        position: fixed;
        top: 30px;
        right: 30px;
        z-index: 9999;
        min-width: 220px;
        padding: 16px 24px;
        border-radius: 12px;
        background: #22c55e; /* Green */
        color: #fff;
        font-weight: bold;
        box-shadow: 0 2px 16px rgba(0,0,0,0.12);
        opacity: 0.95;
        display: none;
    }
    .toast-error {
        background: #ef4444; /* Red */
    }
    .toast-warning {
        background: #f59e0b; /* Amber */
    }
</style>

<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                </div>
        </div>
    
        <section class="content">
            <div class="container-fluid">
                <input type="text" id="rfid-input" autocomplete="off" class="absolute opacity-0 pointer-events-none" style="z-index:-1;">
                
                <div class="flex flex-col md:flex-row gap-8">
                    <div id="student-info" class="student-info bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center w-full md:w-1/3">
                        <h3 class="text-2xl font-bold mb-4 text-blue-700">🧑‍🎓 ข้อมูลนักเรียน</h3>
                        <div class="relative">
                            <img id="student-photo" class="w-80 h-80 rounded-full border-8 border-blue-300 shadow-lg mb-4 object-cover" src="https://std.phichai.ac.th/dist/img/logo-phicha.png" alt="Student Photo">
                        </div>
                        <div id="student-details" class="text-center text-lg text-gray-700">
                            <p class="italic text-gray-400">กรุณาแตะบัตร... <span class="animate-pulse">🪪</span></p>
                        </div>
                    </div>
    
                    <div class="attendance-table bg-white rounded-2xl shadow-2xl p-8 w-full md:w-2/3">
                        <h3 class="text-2xl font-bold mb-4 text-green-700">📝 ประวัติการเช็คชื่อวันนี้</h3>
                        <table id="attendanceTable" class="display stripe hover w-full text-base">
                            <thead>
                                <tr>
                                    <th>รหัสนักเรียน</th>
                                    <th>ชื่อ-สกุล</th>
                                    <th>ชั้น</th>
                                    <th>เวลา</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        
        <div id="toast-notification" class="toast"></div>

    </div>

    <?php require_once('footer.php'); ?>
</div>

<?php require_once('script.php'); ?>

<script>
$(document).ready(function() {
    function showToast(message, type = 'success') {
        const $toast = $('#toast-notification'); // Correct ID
        $toast.text(message);

        $toast.removeClass('toast-error toast-warning');
        if (type === 'error') {
            $toast.addClass('toast-error');
        } else if (type === 'warning') {
            $toast.addClass('toast-warning');
        }
        
        $toast.fadeIn(400).delay(3000).fadeOut(400);
    }

    function focusRFID() {
        $('#rfid-input').focus();
    }
    focusRFID();
    $(document).on('click', focusRFID);

    function getDeviceId() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('device_id') || urlParams.get('id') || 1;
    }
    const scanDirection = (parseInt(getDeviceId(), 10) === 1) ? 'arrival' : 'leave';

    var table = $('#attendanceTable').DataTable({
        ajax: {
            url: 'api/get_attendance.php',
            dataSrc: 'data'
        },
        columns: [
            { data: 'student_id' },
            { data: 'fullname' },
            { data: 'class' },
            { data: 'time' },
            { data: 'status' }
        ],
        order: [[3, 'desc']],
        language: { "emptyTable": "ยังไม่มีข้อมูลการเช็คชื่อสำหรับวันนี้" }
    });

    function pulseEffect() {
        $('#student-photo').addClass('pulse');
        setTimeout(() => $('#student-photo').removeClass('pulse'), 1200);
    }

    function showStudentInfo(data, applyPulse = false) {
        if (data && data.student_id) {
            $('#student-photo').attr('src', data.photo);
            let statusText = data.status.includes('<span') ? data.status : `<span>${data.status}</span>`;
            $('#student-details').html(
                `<p class="mb-2"><span class="font-bold">รหัส:</span> ${data.student_id}</p>` +
                `<p class="mb-2"><span class="font-bold">ชื่อ:</span> ${data.fullname}</p>` +
                `<p class="mb-2"><span class="font-bold">ชั้น:</span> ${data.class}</p>` +
                `<p class="mb-2"><span class="font-bold">เวลา:</span> ${data.time}</p>` +
                `<p class="mb-2"><span class="font-bold">สถานะ:</span> ${statusText}</p>`
            );
            if (applyPulse) pulseEffect();
        } else {
            $('#student-photo').attr('src', 'https://std.phichai.ac.th/dist/img/logo-phicha.png');
            $('#student-details').html('<p class="italic text-gray-400">กรุณาแตะบัตร... <span class="animate-pulse">🪪</span></p>');
        }
    }

    function loadLastScan() {
        $.get('api/last_scan.php', function(res) {
            showStudentInfo(res, false);
        }, 'json');
    }

    // Main scan event handler
    $('#rfid-input').on('change', function() {
        const rfid = $(this).val().trim();
        if (rfid.length === 0) return;

        $.post('api/rfid_scan.php', { rfid: rfid, device_id: getDeviceId(), direction: scanDirection }, function(res) {
            if (res && res.student_id) {
                if (res.is_duplicate) {
                    showStudentInfo(res, false);
                    showToast('⚠️ ' + res.fullname + ' ได้สแกนไปแล้ว', 'warning');
                } else {
                    showStudentInfo(res, true);
                    table.ajax.reload(null, false);
                    showToast('✅ สแกนสำเร็จ: ' + res.fullname, 'success');
                }
            } else {
                showStudentInfo(null);
                showToast('❌ ไม่พบข้อมูลบัตรนี้ในระบบ', 'error');
            }
            $('#rfid-input').val('');
        }, 'json').fail(function() {
            showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            $('#rfid-input').val('');
        });
    });

    // Initial load and periodic refresh
    loadLastScan();
    setInterval(() => table.ajax.reload(null, false), 15000); // Refresh table every 15 seconds
});
</script>

</body>
</html>