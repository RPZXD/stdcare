<?php
require_once('../class/Student.php');
require_once('../config/Database.php');

$db = (new Database("phichaia_student"))->getConnection();
$student = new Student($db);

$stu_id = $_GET['stu_id'] ?? '';
$rfid = $_GET['rfid'] ?? '';

$studentData = $student->getStudentDataByStuId($stu_id);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์บัตร RFID</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mali:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body, .card, .card-content, .student-photo, .logo-phicha, .divider {
            font-family: 'Mali', 'sans-serif' !important;
        }
        .card {
            width: 54mm;
            height: 86mm;
            /* แนวตั้ง */
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            position: relative;
            background: linear-gradient(135deg, #fff7e6 0%, #ffe5e5 100%);
            border-radius: 18px;
            box-shadow: 0 10px 36px 0 rgba(220,38,38,0.13), 0 2px 8px 0 rgba(251,191,36,0.13);
            overflow: hidden;
            border: 2.5px solid #fbbf24;
        }
        .card-bar {
            width: 100%;
            height: 12mm;
            background: linear-gradient(90deg, #dc2626 0%, #fbbf24 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            box-shadow: 0 2px 8px 0 rgba(220,38,38,0.10);
        }
        .school-title {
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-shadow: 0 2px 8px rgba(220,38,38,0.22);
            font-family: 'Mali', 'sans-serif';
        }
        .card-bar-bottom {
            width: 100%;
            height: 9mm;
            background: linear-gradient(90deg, #fbbf24 0%, #dc2626 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 6mm;
            position: absolute;
            bottom: 0;
            left: 0;
            z-index: 2;
            box-shadow: 0 -2px 8px 0 rgba(220,38,38,0.10);
        }
        .logo-phicha-bottom {
            width: 6mm;
            height: 6mm;
            object-fit: contain;
            filter: drop-shadow(0 1px 2px rgba(220,38,38,0.13));
        }
        .footer-card {
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            opacity: 0.98;
            text-shadow: 0 1px 4px #dc262644;
        }
        .card-content {
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            position: relative;
            z-index: 2;
            padding: 0 4mm 0 4mm;
        }
        .student-photo-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            margin-top: 2mm;
            margin-bottom: 0;
            min-width: 31mm;
        }
        .student-photo {
            width: 28mm;
            height: 36mm;
            border: 3px solid #fbbf24;
            box-shadow: 0 4px 16px 0 rgba(220,38,38,0.18);
            background: #fff7e6;
            margin-bottom: 1mm;
            transition: transform 0.2s;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .student-photo-box .photo-placeholder {
            width: 28mm;
            height: 36mm;
            border-radius: 50%;
            background: #ffe5e5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbbf24;
            font-size: 2.2rem;
            border: 3px solid #fbbf24;
            margin-bottom: 2mm;
        }
        .info-section {
            width: 100%;
            margin-top: 1mm;
            display: flex;
            flex-direction: column;
            gap: 2.5mm;
            align-items: center;
        }
        .student-name {
            font-size: 0.8rem;
            font-weight: 800;
            color: #dc2626;
            letter-spacing: 0.7px;
            margin-bottom: 0.5mm;
            margin-top: 0;
            text-shadow: 0 1px 0 #fff, 0 2px 4px rgba(251,191,36,0.10);
            background: linear-gradient(90deg, #fff7e6 60%, #ffe5e5 100%);
            border-radius: 10px;
            padding: 2mm 0;
            box-shadow: 0 1px 6px 0 rgba(251,191,36,0.10);
            text-align: center;
            border: 1.2px solid #fbbf24;
            width: 100%;
        }
        .info-box {
            background: #fff7e6;
            border-radius: 8px;
            box-shadow: 0 1px 4px 0 rgba(251,191,36,0.10);
            padding: 0.8mm 0;
            display: flex;
            align-items: center;
            gap: 0.7em;
            border-left: 4px solid #dc2626;
            width: 100%;
            justify-content: center;
        }
        .info-label {
            font-size: 0.65rem;
            color: #dc2626;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4em;
        }
        .info-value {
            font-size: 0.65rem;
            color: #b45309;
            font-family: 'Mali', 'sans-serif';
            font-weight: 800;
            margin-left: 0.5em;
        }
        .rfid-value {
            color: #d97706;
            letter-spacing: 0.14em;
            font-size: 1.05rem;
            font-family: 'Mali', 'sans-serif';
            font-weight: 800;
            text-shadow: 0 1px 0 #fff;
        }
        @media print {
            body, html {
                background: #fff !important;
            }
            .no-print, body > *:not(.card) {
                display: none !important;
            }
            .card {
                box-shadow: none !important;
                position: absolute !important;
                left: 0; top: 0; right: 0; bottom: 0;
                margin: auto !important;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-yellow-100 to-red-100 flex flex-col items-center justify-center min-h-screen" style="font-family: 'Mali', 'sans-serif'">
    <div class="no-print flex gap-4 mb-4">
        <button onclick="window.print()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded font-semibold text-xs shadow">🖨️ พิมพ์บัตร</button>
        <button onclick="window.close()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1 rounded font-semibold text-xs shadow">ปิด</button>
    </div>
    <div class="card rounded-2xl">
        <div class="card-bar">
            <span class="school-title">โรงเรียนพิชัย</span>
        </div>
        <div class="card-content">
            <div class="student-photo-box">
                <?php
                $photoUrl = 'https://std.phichai.ac.th/photo/' . ($studentData['Stu_picture'] ?? '');
                $hasPhoto = !empty($studentData['Stu_picture']);
                ?>
                <img src="<?php echo $photoUrl; ?>"
                    alt="student"
                    class="student-photo rounded-full object-cover"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="photo-placeholder" style="display:none;">
                    <svg width="38" height="38" fill="#fbbf24" viewBox="0 0 24 24"><path d="M12 12c2.7 0 8 1.34 8 4v2H4v-2c0-2.66 5.3-4 8-4zm0-2a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                </div>
            </div>
            <div class="info-section">
                <div class="student-name">
                    <?php echo htmlspecialchars(($studentData['Stu_name'] ?? '') . ' ' . ($studentData['Stu_sur'] ?? '')); ?>
                </div>
                <div class="info-box">
                    <svg width="18" height="18" fill="#dc2626" viewBox="0 0 24 24"><path d="M12 12c2.7 0 8 1.34 8 4v2H4v-2c0-2.66 5.3-4 8-4zm0-2a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                    <span class="info-label">รหัสนักเรียน</span>
                    <span class="info-value"><?php echo htmlspecialchars($stu_id); ?></span>
                </div>
                <div class="info-box">
                    <svg width="18" height="18" fill="#fbbf24" viewBox="0 0 24 24"><path d="M12 7V3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                    <span class="info-label">ชั้น/ห้อง</span>
                    <span class="info-value"><?php echo htmlspecialchars($studentData['Stu_major'] ?? ''); ?> / <?php echo htmlspecialchars($studentData['Stu_room'] ?? ''); ?></span>
                </div>
                <div class="info-box">
                    <svg width="18" height="18" fill="#d97706" viewBox="0 0 24 24"><path d="M20 4H4v16h16V4zm-2 14H6V6h12v12zm-6-1a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"/></svg>
                    <span class="info-label">RFID</span>
                    <span class="rfid-value"><?php echo htmlspecialchars($rfid); ?></span>
                </div>
            </div>
        </div>
        <div class="card-bar-bottom">
            <img src="../dist/img/logo-phicha.png" alt="logo" class="logo-phicha-bottom">
            <span class="footer-card">Student Card</span>
        </div>
    </div>
</body>
</html>
