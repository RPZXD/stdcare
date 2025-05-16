<?php 

require_once('header.php');
require_once('config/Setting.php');
require_once('class/Utils.php');
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
            background: #22c55e;
            color: #fff;
            font-weight: bold;
            box-shadow: 0 2px 16px rgba(0,0,0,0.12);
            opacity: 0.95;
            display: none;
        }
        .toast-error {
            background: #ef4444;
        }
    </style>

<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6 flex items-center gap-4">
            <h1 class="m-0 text-3xl font-extrabold flex items-center gap-2 text-green-700 drop-shadow">📚 ระบบเช็คชื่อด้วยบัตร RFID <span class="animate-bounce">🪪</span></h1>
            <div id="datetime-panel" class="ml-4 px-4 py-2 rounded-lg bg-green-50 border border-green-200 shadow text-green-800 text-lg font-semibold w-full-md flex flex-col items-start">
                <span id="current-date"></span>
                <span id="current-time" class="font-mono"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <section class="content">
    <div class="container-fluid">
      <!-- RFID input (hidden but focused) -->
      <input type="text" id="rfid-input" autocomplete="off" class="absolute opacity-0 pointer-events-none" style="z-index:-1;">
      <input type="hidden" id="device-id" value="<?= isset($_GET['device_id']) ? htmlspecialchars($_GET['device_id']) : 1 ?>">
      <div class="flex flex-col md:flex-row gap-8">
        <div class="student-info bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center w-full md:w-1/3 mb-6 md:mb-0 transition-all duration-300" id="student-info">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2 text-blue-700">🧑‍🎓 ข้อมูลนักเรียน</h3>
            <div class="relative">
                <img id="student-photo" class="student-photo w-80 h-80 rounded-full border-8 border-blue-300 shadow-lg mb-4 object-cover transition-all duration-300" src="https://std.phichai.ac.th/dist/img/logo-phicha.png" alt="Student Photo">
                <span id="scan-emoji" class="absolute -bottom-4 right-0 text-4xl hidden">🎉</span>
            </div>
            <div id="student-details" class="text-center text-lg text-gray-700 transition-all duration-300">
                <p class="italic text-gray-400">กรุณาแตะบัตร... <span class="animate-pulse">🪪</span></p>
            </div>
        </div>
        <div class="attendance-table bg-white rounded-2xl shadow-2xl p-8 w-full md:w-2/3">
            <h3 class="text-2xl font-bold mb-4 flex items-center gap-2 text-green-700">📝 ประวัติการเช็คชื่อ</h3>
            <table id="attendanceTable" class="display stripe hover w-full text-base" style="width:100%">
                <thead>
                    <tr>
                        <th>🎫 รหัสนักเรียน</th>
                        <th>👤 ชื่อ-สกุล</th>
                        <th>🏫 ชั้น</th>
                        <th>⏰ เวลา</th>
                        <th>✅ สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables จะเติมข้อมูลที่นี่ -->
                </tbody>
            </table>
        </div>
      </div>
    </div>
    </section>
    <!-- Toast -->
    <div id="toast" class="toast"></div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('footer.php');?>
</div>
<!-- ./wrapper -->

<script>
$(document).ready(function() {
    // Toast function
    function showToast(msg, error = false) {
        var $toast = $('#toast');
        $toast.text(msg);
        $toast.removeClass('toast-error');
        if(error) $toast.addClass('toast-error');
        $toast.fadeIn(200);
        setTimeout(function() { $toast.fadeOut(400); }, 1800);
    }

    // Focus RFID input on page load and click
    function focusRFID() {
        $('#rfid-input').focus();
    }
    focusRFID();
    $(document).on('click', focusRFID);

    // Get device_id from query string (GET) instead of hidden input
    function getDeviceId() {
        const urlParams = new URLSearchParams(window.location.search);
        // รองรับทั้ง device_id และ id (เช่น ?device_id=2 หรือ ?id=2)
        return urlParams.get('device_id') || urlParams.get('id') || 1;
        console.log('Device ID:', device_id);
    }

    // Real-time date & time
    function updateDateTime() {
        const now = new Date();
        // วันที่แบบไทย
        const thMonths = ["ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค."];
        const day = now.getDate();
        const month = thMonths[now.getMonth()];
        const year = now.getFullYear() + 543;
        const dateStr = `📅 ${day} ${month} ${year}`;
        // เวลาแบบ HH:MM:SS
        const timeStr = `⏰ ${now.toLocaleTimeString('th-TH', { hour12: false })}`;
        $('#current-date').text(dateStr);
        $('#current-time').text(timeStr);
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Initialize DataTable
    var table = $('#attendanceTable').DataTable({
        ajax: {
            url: 'api/get_attendance.php',
            dataSrc: function(json) {
                // กรณี error หรือไม่ได้รูปแบบ {data:[]} ให้ fallback
                if (!json || !json.data) {
                    // DEBUG: ลอง log json ที่ได้กลับมา
                    // console.log('DataTables ajax response:', json);
                    return [];
                }
                // DEBUG: log ข้อมูลที่จะแสดง
                // console.log('DataTables data:', json.data);
                return json.data;
            }
        },
        columns: [
            { data: 'student_id', defaultContent: '-' },
            { data: 'fullname', defaultContent: '-' },
            { data: 'class', defaultContent: '-' },
            { data: 'time', defaultContent: '-' },
            { data: 'status',
              defaultContent: '-',
              render: function(data, type, row) {
                if (data === 'มาเรียน') return '<span class="text-green-600 font-bold">🟢 ' + data + '</span>';
                if (data === 'สาย') return '<span class="text-yellow-500 font-bold">🟠 ' + data + '</span>';
                if (data === 'ขาดเรียน') return '<span class="text-red-600 font-bold">🔴 ' + data + '</span>';
                return '<span class="text-gray-400">⚪ ' + (data ? data : '-') + '</span>';
              }
            }
        ],
        order: [[3, 'desc']],
        language: {
            "emptyTable": "ไม่มีข้อมูลการเช็คชื่อ"
        }
    });

    // Pulse effect & emoji
    function pulseEffect() {
        $('#student-photo').addClass('pulse');
        $('#scan-emoji').removeClass('hidden').addClass('animate-bounce');
        setTimeout(function() {
            $('#student-photo').removeClass('pulse');
            $('#scan-emoji').addClass('hidden').removeClass('animate-bounce');
        }, 1200);
    }

    // Function to update student info panel
    function showStudentInfo(data, scanned = false) {
        if (data) {
            $('#student-photo').attr('src', data.photo || 'https://std.phichai.ac.th/dist/img/logo-phicha.png');
            let statusHtml = '';
            if (data.status === 'มาเรียน') {
                statusHtml = '<span class="text-green-600 font-bold">🟢 ' + data.status + '</span>';
            } else if (data.status === 'สาย') {
                statusHtml = '<span class="text-yellow-500 font-bold">🟠 ' + data.status + '</span>';
            } else if (data.status === 'ขาดเรียน') {
                statusHtml = '<span class="text-red-600 font-bold">🔴 ' + data.status + '</span>';
            } else {
                statusHtml = '<span class="text-gray-400">⚪ ' + data.status + '</span>';
            }
            $('#student-details').html(
                '<p class="mb-2"><span class="font-bold">🎫 รหัส:</span> <span class="text-blue-700">' + data.student_id + '</span></p>' +
                '<p class="mb-2"><span class="font-bold">👤 ชื่อ:</span> <span class="text-green-700">' + data.fullname + '</span></p>' +
                '<p class="mb-2"><span class="font-bold">🏫 ชั้น:</span> <span class="text-purple-700">' + data.class + '</span></p>' +
                '<p class="mb-2"><span class="font-bold">⏰ เวลา:</span> <span class="text-gray-700">' + data.time + '</span></p>' +
                '<p class="mb-2"><span class="font-bold">✅ สถานะ:</span> ' + statusHtml + '</p>'
            );
            if(scanned) pulseEffect();
        } else {
            $('#student-photo').attr('src', 'https://std.phichai.ac.th/dist/img/logo-phicha.png');
            $('#student-details').html('<p class="italic text-gray-400">กรุณาแตะบัตร... <span class="animate-pulse">🪪</span></p>');
        }
    }

    // Polling for RFID scan (โหลดข้อมูลล่าสุดหลัง scan เท่านั้น)
    function loadLastRFID() {
        $.get('api/last_scan.php', { device_id: getDeviceId() }, function(res) {
            if (res && res.student_id) {
                showStudentInfo(res);
                table.ajax.reload(null, false);
            }
        }, 'json');
    }

    // เรียก loadLastRFID หลัง scan สำเร็จเท่านั้น
    $('#rfid-input').on('change', function() {
        var rfid = $(this).val().trim();
        var device_id = getDeviceId();
        if (rfid.length > 0) {
            $.post('api/rfid_scan.php', { rfid: rfid, device_id: device_id }, function(res) {
                if (res && res.student_id) {
                    showStudentInfo(res, true);
                    table.ajax.reload(null, false);
                    showToast('✅ สแกนสำเร็จ: ' + res.fullname);
                    loadLastRFID(); // โหลดข้อมูลล่าสุดหลัง scan
                } else {
                    showStudentInfo(null);
                    showToast('❌ ไม่พบข้อมูลบัตรนี้', true);
                }
                $('#rfid-input').val('');
                focusRFID();
            }, 'json').fail(function() {
                showToast('เกิดข้อผิดพลาดในการบันทึกข้อมูล', true);
                $('#rfid-input').val('');
                focusRFID();
            });
        }
    });

    // Polling สำหรับ DataTable ทุก 5 วินาที (ลดภาระ server)
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 5000);

    // Initial clear
    showStudentInfo(null);
});
</script>
<?php require_once('script.php');?>
</body>
</html>