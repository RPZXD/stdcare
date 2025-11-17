<?php 
require_once('header.php');
// Other require_once calls as needed
?>
<style>
    body {
        background: linear-gradient(135deg, #ef4444 0%, #f59e0b 100%);
        min-height: 100vh;
    }
    
    .pulse {
        animation: pulse 1s;
    }
    @keyframes pulse {
        0% { 
            box-shadow: 0 0 0 0 rgba(34,197,94,0.7);
            transform: scale(1);
        }
        50% { 
            transform: scale(1.05);
        }
        70% { 
            box-shadow: 0 0 0 20px rgba(34,197,94,0);
        }
        100% { 
            box-shadow: 0 0 0 0 rgba(34,197,94,0);
            transform: scale(1);
        }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    @keyframes glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59,130,246,0.5), 0 0 60px rgba(147,51,234,0.3); }
        50% { box-shadow: 0 0 40px rgba(59,130,246,0.8), 0 0 90px rgba(147,51,234,0.5); }
    }
    
    @keyframes slideInFromTop {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInFromBottom {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes flipOut {
        from {
            transform: perspective(800px) rotateY(0deg);
            opacity: 1;
        }
        to {
            transform: perspective(800px) rotateY(90deg);
            opacity: 0;
        }
    }
    
    @keyframes flipIn {
        from {
            transform: perspective(800px) rotateY(-90deg);
            opacity: 0;
        }
        to {
            transform: perspective(800px) rotateY(0deg);
            opacity: 1;
        }
    }
    
    @keyframes cardSwipeOut {
        from {
            transform: translateX(0) scale(1);
            opacity: 1;
        }
        to {
            transform: translateX(-100%) scale(0.8);
            opacity: 0;
        }
    }
    
    @keyframes cardSwipeIn {
        from {
            transform: translateX(100%) scale(0.8);
            opacity: 0;
        }
        to {
            transform: translateX(0) scale(1);
            opacity: 1;
        }
    }
    
    @keyframes sparkle {
        0%, 100% { 
            opacity: 0;
            transform: scale(0);
        }
        50% { 
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes bounceIn {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .toast {
        position: fixed;
        top: 30px;
        right: 30px;
        z-index: 9999;
        min-width: 320px;
        padding: 24px 32px;
        border-radius: 20px;
        background: #22c55e;
        color: #fff;
        font-weight: bold;
        font-size: 1.2rem;
        box-shadow: 0 15px 50px rgba(0,0,0,0.4);
        opacity: 0;
        transform: translateX(400px) scale(0.8);
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        border: 3px solid rgba(255,255,255,0.3);
    }
    
    .toast.show {
        opacity: 1;
        transform: translateX(0) scale(1);
        animation: bounceIn 0.6s ease-out;
    }
    
    .toast::before {
        font-size: 1.8rem;
        margin-right: 12px;
    }
    
    .toast-error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    
    .toast-error::before {
        content: '❌ ';
    }
    
    .toast-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    
    .toast-warning::before {
        content: '⚠️ ';
    }
    
    .toast-success {
        background: linear-gradient(135deg, #f59e0b, #fb923c);
    }
    
    .toast-success::before {
        content: '✅ ';
    }
    
    /* Digital clock and student card styles with fade transitions */
    #display-area {
        position: relative;
        min-height: 500px;
        perspective: 1000px;
    }
    
    .clock-card {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        opacity: 1;
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: center center;
    }
    
    .clock-card.swipe-out {
        animation: cardSwipeOut 0.5s ease-in-out forwards;
    }
    
    .clock-card .card-inner {
        background: linear-gradient(135deg, rgba(239,68,68,0.95), rgba(249,115,22,0.95));
        border-radius: 32px;
        padding: 80px 40px;
        box-shadow: 0 25px 80px rgba(0,0,0,0.3);
        backdrop-filter: blur(10px);
        animation: glow 3s ease-in-out infinite;
        position: relative;
        overflow: hidden;
    }
    
    .clock-card .card-inner::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #ef4444, #f97316, #ef4444);
        border-radius: 32px;
        z-index: -1;
        opacity: 0.5;
        filter: blur(20px);
    }
    
    .clock-card .card-inner::after {
        content: '✨';
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 2.5rem;
        animation: sparkle 2s ease-in-out infinite;
        color: rgba(255,230,128,0.95);
        text-shadow: 0 6px 18px rgba(255,200,0,0.15);
    }
    
    .digital-clock {
        font-size: 7rem;
        font-weight: 800;
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 350px;
        letter-spacing: 0.1em;
        text-shadow: 0 0 30px rgba(255,255,255,0.5);
    }
    
    .digital-clock .time-display {
        position: relative;
    }
    
    .digital-clock .time-display::before {
        content: '⏰';
        position: absolute;
        top: -60px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 3rem;
        animation: float 3s ease-in-out infinite;
        color: #fff5d6;
        text-shadow: 0 6px 18px rgba(255,180,0,0.12);
    }
    
    .digital-clock .date-display {
        font-size: 1.8rem;
        font-weight: 600;
        margin-top: 15px;
        opacity: 0.9;
        letter-spacing: 0.05em;
    }
    
    .digital-clock .date-display::before {
        content: '📅 ';
        font-size: 1.5rem;
    }
    
    .profile-card { 
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        opacity: 0;
        transform: translateX(100%) scale(0.8);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        transform-origin: center center;
    }
    
    .profile-card.swipe-in {
        animation: cardSwipeIn 0.5s ease-in-out forwards;
        opacity: 1;
        transform: translateX(0) scale(1);
        pointer-events: auto;
    }
    
    .profile-card .card-inner { 
        background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(248,250,252,0.95));
        border-radius: 32px;
        padding: 50px;
        box-shadow: 0 25px 80px rgba(0,0,0,0.3);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.5);
        position: relative;
        overflow: hidden;
    }
    
    .profile-card .card-inner::before {
        content: '🎓';
        position: absolute;
        top: -20px;
        right: -20px;
        font-size: 8rem;
        opacity: 0.08;
        transform: rotate(-15deg);
        color: #f97316;
    }
    
    .profile-card .card-inner::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #ef4444, #fb923c, #f59e0b, #facc15);
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }
    
    .profile-card .info { 
        text-align: center;
    }
    
    .profile-card .info p { 
        margin: 16px 0;
        font-size: 1.5rem;
        color: #1e293b;
        font-weight: 500;
        opacity: 0;
        padding: 12px 20px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(239,68,68,0.04), rgba(249,115,22,0.04));
        transition: all 0.3s ease;
    }
    
    .profile-card .info p:hover {
        background: linear-gradient(135deg, rgba(239,68,68,0.12), rgba(249,115,22,0.12));
        transform: translateX(10px);
    }
    
    .profile-card.swipe-in .info p {
        animation: slideInFromTop 0.5s ease-out forwards;
    }
    
    .profile-card.swipe-in .info p:nth-child(1) { animation-delay: 0.1s; }
    .profile-card.swipe-in .info p:nth-child(2) { animation-delay: 0.2s; }
    .profile-card.swipe-in .info p:nth-child(3) { animation-delay: 0.3s; }
    .profile-card.swipe-in .info p:nth-child(4) { animation-delay: 0.4s; }
    .profile-card.swipe-in .info p:nth-child(5) { animation-delay: 0.5s; }
    
    .profile-card .info p .font-bold {
        color: #3b82f6;
        font-weight: 700;
    }
    
    #student-photo {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
        position: relative;
    }
    
    #student-photo:hover {
        transform: scale(1.05) rotate(2deg);
        filter: drop-shadow(0 25px 50px rgba(0,0,0,0.4));
    }
    
    .photo-section::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #ef4444, #fb923c);
        border-radius: 50%;
        opacity: 0.18;
        z-index: -1;
        animation: float 4s ease-in-out infinite;
    }
    
    .photo-section::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: -20px;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #f59e0b, #facc15);
        border-radius: 50%;
        opacity: 0.14;
        z-index: -1;
        animation: float 5s ease-in-out infinite reverse;
    }
    
    #student-info {
        animation: float 6s ease-in-out infinite;
    }
    
    /* Layout for photo and cards side by side */
    .content-layout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 40px;
    }
    
    .photo-section {
        flex-shrink: 0;
    }
    
    .cards-section {
        flex: 1;
        max-width: 800px;
    }
    
    @media (max-width: 1024px) {
        .content-layout {
            flex-direction: column;
            gap: 30px;
        }
    }
    
    .btn-toggle {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16,185,129,0.4);
    }
    
    .btn-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16,185,129,0.6);
    }
    
    .btn-toggle:active {
        transform: translateY(0);
    }
    
    /* Loading animation */
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    
    .loading-shimmer {
        animation: shimmer 2s infinite;
        background: linear-gradient(to right, #f0f0f0 4%, #e0e0e0 25%, #f0f0f0 36%);
        background-size: 1000px 100%;
    }
    
    /* Scan indicator animation */
    @keyframes scanPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(239,68,68,0.7);
            border-color: rgba(239,68,68,0.9);
        }
        50% {
            box-shadow: 0 0 0 20px rgba(249,115,22,0);
            border-color: rgba(249,115,22,0.9);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(239,68,68,0);
            border-color: rgba(239,68,68,0.9);
        }
    }
    
    #scan-indicator.scanning {
        animation: scanPulse 1s ease-out infinite;
        border-width: 4px;
    }
</style>

<body class="hold-transition sidebar-mini layout-fixed light-mode">

    
        <section class="content">
            <div class="container-fluid">
                <input type="text" id="rfid-input" autocomplete="off" class="absolute opacity-0 pointer-events-none" style="z-index:-1;">
                
                <div class="flex justify-center items-center min-h-screen">
                    <div id="student-info" class="student-info bg-white rounded-3xl shadow-2xl p-12 w-full" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); max-width: 1600px;">
                        <div class="content-layout">
                            <div class="photo-section relative">
                                <img id="student-photo" class="rounded-full border-8 border-white shadow-2xl object-cover" style="width: 480px; height: 480px;" src="https://std.phichai.ac.th/dist/img/logo-phicha.png" alt="Student Photo">
                                <div id="scan-indicator" class="absolute inset-0 rounded-full border-8 border-transparent" style="display: none;"></div>
                            </div>
                            <div id="display-area" class="cards-section">
                            <!-- Clock Card -->
                            <div id="clock-card" class="clock-card">
                                <div class="card-inner">
                                    <div class="digital-clock">
                                        <div class="time-display">--:--:--</div>
                                        <div class="date-display"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Card -->
                            <div id="profile-card" class="profile-card">
                                <div class="card-inner">
                                    <div class="info">
                                        <p class="mb-3 text-xl"><span class="font-bold">🆔 รหัส:</span> <span id="card-student-id">-</span></p>
                                        <p class="mb-3 text-xl"><span class="font-bold">👤 ชื่อ:</span> <span id="card-fullname">-</span></p>
                                        <p class="mb-3 text-xl"><span class="font-bold">🏫 ชั้น:</span> <span id="card-class">-</span></p>
                                        <p class="mb-3 text-xl"><span class="font-bold">🕐 เวลา:</span> <span id="card-time">-</span></p>
                                        <p class="mb-3 text-xl"><span class="font-bold">✅ สถานะ:</span> <span id="card-status">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <div id="toast-notification" class="toast"></div>

 


</div>

<?php require_once('script.php'); ?>

<script>
$(document).ready(function() {

    $('body').addClass('sidebar-collapse');
    
    function showToast(message, type = 'success') {
        const $toast = $('#toast-notification');
        
        // Remove emoji from message since we add it in CSS
        const cleanMessage = message.replace(/^[❌⚠️✅]\s*/, '');
        $toast.text(cleanMessage);

        $toast.removeClass('toast-error toast-warning toast-success');
        if (type === 'error') {
            $toast.addClass('toast-error');
        } else if (type === 'warning') {
            $toast.addClass('toast-warning');
        } else {
            $toast.addClass('toast-success');
        }
        
        $toast.addClass('show');
        setTimeout(function() {
            $toast.removeClass('show');
        }, 3500);
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

    function pulseEffect() {
        $('#student-photo').addClass('pulse');
        setTimeout(() => $('#student-photo').removeClass('pulse'), 1200);
    }
    
    // Clock + profile display handling with fade effects
    var profileTimeout = null;
    var clockInterval = null;

    function updateClock() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        
        // Update time
        $('#clock-card .time-display').text(hh + ':' + mm + ':' + ss);
        
        // Update date (Thai format)
        const thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        
        const dayName = thaiDays[now.getDay()];
        const date = now.getDate();
        const month = thaiMonths[now.getMonth()];
        const year = now.getFullYear() + 543;
        
        $('#clock-card .date-display').text(`วัน${dayName}ที่ ${date} ${month} ${year}`);
    }

    function startClock() {
        updateClock();
        if (clockInterval) clearInterval(clockInterval);
        clockInterval = setInterval(updateClock, 1000);
    }

    // Logo URL constant and helper to adjust photo size
    const logoUrl = 'https://std.phichai.ac.th/dist/img/logo-phicha.png';

    function adjustPhotoSize(src) {
        // Normalize src (ignore query string)
        if (!src) return;
        const cleanSrc = src.split('?')[0];
        if (cleanSrc === logoUrl) {
            // Logo: square
            $('#student-photo').css({ width: '480px', height: '480px', 'object-fit': 'contain' });
        } else {
            // Student photo: portrait
            $('#student-photo').css({ width: '480px', height: '600px', 'object-fit': 'cover' });
        }
    }

    function showClockCard() {
        // Remove swipe-in from profile card
        $('#profile-card').removeClass('swipe-in');
        
        // Add swipe-out to clock card to remove it
        $('#clock-card').addClass('swipe-out');
        
        // Wait for animation, then reset
        setTimeout(function() {
            $('#clock-card').removeClass('swipe-out');
            $('#student-photo').attr('src', logoUrl);
            adjustPhotoSize(logoUrl);
        }, 500);
    }

    function showProfileCard(data, applyPulse = false) {
        if (data && data.student_id) {
            // Update student info
            $('#student-photo').attr('src', data.photo);
            adjustPhotoSize(data.photo);
            $('#card-student-id').text(data.student_id);
            $('#card-fullname').text(data.fullname);
            $('#card-class').text(data.class);
            $('#card-time').text(data.time);
            $('#card-status').html(data.status || '');

            if (applyPulse) pulseEffect();

            // Swipe out clock card
            $('#clock-card').addClass('swipe-out');
            
            // After clock card animates out, swipe in profile card
            setTimeout(function() {
                $('#profile-card').addClass('swipe-in');
            }, 250);

            // Reset existing timeout so consecutive scans show immediately
            if (profileTimeout) clearTimeout(profileTimeout);
            
            // Auto-hide after 5 seconds
            profileTimeout = setTimeout(function() {
                showClockCard();
                profileTimeout = null;
            }, 5000);
        } else {
            // No data: show clock
            showClockCard();
        }
    }

    // Main scan event handler
    $('#rfid-input').on('change', function() {
        const rfid = $(this).val().trim();
        if (rfid.length === 0) return;

        // Show scanning indicator
        $('#scan-indicator').css({
            'border-color': '#3b82f6',
            'display': 'block'
        }).addClass('scanning');
        
        setTimeout(function() {
            $('#scan-indicator').css('display', 'none').removeClass('scanning');
        }, 1000);

        $.post('controllers/AttendanceController.php?action=scan', { 
            rfid: rfid, 
            device_id: getDeviceId(), 
            direction: scanDirection 
        }, function(res) {
            if (res && res.student_id) {
                if (res.is_duplicate) {
                    showProfileCard(res, false);
                    showToast('⚠️ ' + res.fullname + ' ได้สแกนไปแล้ว', 'warning');
                } else {
                    showProfileCard(res, true);
                    showToast('✅ สแกนสำเร็จ: ' + res.fullname, 'success');
                }
            } else {
                showProfileCard(null);
                showToast('❌ ไม่พบข้อมูลบัตรนี้ในระบบ', 'error');
            }
            $('#rfid-input').val('');
        }, 'json').fail(function(jqXHR) {
            let errorMsg = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
            
            if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                errorMsg = jqXHR.responseJSON.error;
            }

            showToast('❌ ' + errorMsg, 'error');
            $('#rfid-input').val('');
        });
    });

    // Initialize: Start the clock
    startClock();
    // Ensure photo size matches initial src
    // Fallback handler: if photo fails to load, replace with logo and adjust size
    $('#student-photo').on('error', function() {
        $(this).off('error');
        $(this).attr('src', logoUrl);
        adjustPhotoSize(logoUrl);
    });

    adjustPhotoSize($('#student-photo').attr('src'));
    showClockCard();
});
</script>

</body>
</html>