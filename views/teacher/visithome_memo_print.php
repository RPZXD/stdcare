<?php
if (!function_exists('toThaiNumber')) {
    function toThaiNumber($text) {
        $thai = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
        $arabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($arabic, $thai, (string)$text);
    }
}

// Format date range dynamically based on term
if ($term == 2) {
    $dateRange = "วันที่ 2 พฤศจิกายน " . $pee . " - 26 พฤศจิกายน " . $pee;
} else {
    $dateRange = "วันที่ 2 มิถุนายน " . $pee . " - 26 มิถุนายน " . $pee;
}

// Get current date details in Thai
$currentDay = date('j');
$currentMonthNum = date('n');
$thaiMonths = [
    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
    5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
    9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
];
$currentMonthName = $thaiMonths[$currentMonthNum];
$currentYear = date('Y') + 543; // Buddhist year
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกข้อความเยี่ยมบ้านนักเรียน ม.<?= toThaiNumber(htmlspecialchars($class . "/" . $room)) ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../dist/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --doc-font-size: 10pt; /* Default to 10pt as requested */
        }

        body {
            font-family: 'Sarabun', sans-serif;
            font-size: var(--doc-font-size);
            line-height: 1.6;
            color: black;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        /* Print container styling for web preview */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 25mm 20mm 20mm 30mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
            font-size: var(--doc-font-size);
        }

        .print-container table, 
        .print-container td, 
        .print-container th, 
        .print-container p, 
        .print-container div, 
        .print-container span {
            font-size: var(--doc-font-size) !important;
        }

        /* Specific override for the 'บันทึกข้อความ' title to be root + 2pt */
        .print-container .memo-title {
            font-size: calc(var(--doc-font-size) + 2pt) !important;
        }

        .page-break {
            page-break-before: always;
        }

        /* Dotted lines rendering */
        .dotted-line-fill {
            border-bottom: 1px dotted #000;
            flex-grow: 1;
            margin-left: 5px;
            height: 1.2em;
        }

        /* Top Action Bar for web preview */
        .no-print-bar {
            width: 210mm;
            margin: 20px auto 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 15px 25px;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            box-sizing: border-box;
            border: 1px solid #e5e7eb;
        }

        @media print {
            @page {
                size: A4;
                margin-left: 30mm;
                margin-right: 20mm;
                margin-top: 25mm;
                margin-bottom: 25mm;
            }
            body {
                background-color: white;
            }
            .print-container {
                width: auto;
                min-height: auto;
                padding: 0;
                margin: 0;
                box-shadow: none;
                background: transparent;
            }
            .no-print, .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 py-6">

    <!-- Web Preview Action Bar -->
    <div class="no-print-bar no-print">
        <div>
            <h1 class="font-bold text-slate-800 text-base">บันทึกข้อความเยี่ยมบ้านนักเรียน (A4)</h1>
            <p class="text-xs text-slate-500">ตรวจสอบความถูกต้องก่อนสั่งพิมพ์</p>
        </div>
        
        <!-- Font Size Adjuster Control -->
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-bold mr-1">ขนาดฟอนต์:</span>
            <button onclick="changeFontSize(-1)" class="w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg border border-slate-200 transition-all flex items-center justify-center" title="ลดขนาดฟอนต์">
                <i class="fas fa-minus text-xs"></i>
            </button>
            <span id="fontSizeDisplay" class="text-sm font-bold text-slate-700 min-w-[40px] text-center">10pt</span>
            <button onclick="changeFontSize(1)" class="w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg border border-slate-200 transition-all flex items-center justify-center" title="เพิ่มขนาดฟอนต์">
                <i class="fas fa-plus text-xs"></i>
            </button>
        </div>

        <div class="flex gap-2">
            <button onclick="window.close()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition-all border border-slate-200 flex items-center gap-2">
                <i class="fas fa-times"></i> ปิดหน้านี้
            </button>
            <button onclick="exportToWord()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-file-word"></i> ดาวน์โหลด Word
            </button>
            <button onclick="window.print()" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> พิมพ์
            </button>
        </div>
    </div>

    <!-- Page 1: บันทึกข้อความ (Memo) -->
    <div class="print-container">
        <!-- Garuda Emblem and Header -->
        <div style="position: relative; margin-bottom: 25px; min-height: 1.5cm;">
            <img src="../dist/img/ตราครุฑ.jpg" alt="ตราครุฑ" style="position: absolute; left: 0; top: 0; width: 1.5cm; height: 1.5cm;">
            <div class="memo-title" style="text-align: center; font-weight: bold; line-height: 1.5cm; margin: 0; padding: 0;">
                บันทึกข้อความ
            </div>
        </div>

        <!-- Memo Metadata Header Table -->
        <table style="width: 100%; border: none; margin-bottom: 10px; border-collapse: collapse;">
            <tr style="border: none;">
                <td style="border: none; padding: 2px 0; line-height: 1.6;" colspan="2">
                    <strong>ส่วนราชการ</strong> &nbsp; โรงเรียนพิชัย อำเภอพิชัย จังหวัดอุตรดิตถ์
                </td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 2px 0; width: 40%; line-height: 1.6;">
                    <strong>ที่</strong> &nbsp; <?= toThaiNumber("ศธ. 04323.53 (ก9/" . $pee . ")") ?>
                </td>
                <td style="border: none; padding: 2px 0; width: 60%; text-align: right; line-height: 1.6;">
                    <strong>วันที่</strong> &nbsp;<?= toThaiNumber($currentDay) ?>&nbsp; <strong>เดือน</strong> &nbsp;<?= htmlspecialchars($currentMonthName) ?>&nbsp; <strong>พ.ศ.</strong> &nbsp;<?= toThaiNumber($currentYear) ?>
                </td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 2px 0; line-height: 1.6;" colspan="2">
                    <strong>เรื่อง</strong> &nbsp; รายงานการเยี่ยมบ้านนักเรียนระดับชั้นมัธยมศึกษาปีที่ <?= toThaiNumber(htmlspecialchars($class . "/" . $room)) ?> ภาคเรียนที่ <?= toThaiNumber($term) ?> ปีการศึกษา <?= toThaiNumber($pee) ?>
                </td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 2px 0; line-height: 1.6;" colspan="2">
                    <strong>เรียน</strong> &nbsp; ผู้อำนวยการโรงเรียนพิชัย
                </td>
            </tr>
        </table>
        
        <hr style="border: none; border-top: 1.5px solid black; margin: 5px 0 20px 0; padding: 0;">

        <!-- Memo Body Text -->
        <div style="text-align: justify; line-height: 1.8;">
            <p style="text-indent: 1.5cm; margin: 0 0 15px 0; padding: 0;">
                ตามคำสั่งที่ <?= toThaiNumber("117/" . $pee) ?> เรื่อง แต่งตั้งคณะกรรมการดำเนินงานเยี่ยมบ้านนักเรียน ครั้งที่ <?= toThaiNumber($term) ?> ปีการศึกษา <?= toThaiNumber($pee) ?> กำหนดให้ครูที่ปรึกษาออกเยี่ยมบ้าน ระหว่าง<?= toThaiNumber($dateRange) ?> โดยครูที่ปรึกษาออกเยี่ยมบ้านของนักเรียนในห้องพร้อมทั้งลงข้อมูลในระบบการดูแลช่วยเหลือนักเรียน ทั้งนี้มีจุดมุ่งหมายเพื่อสร้างความสัมพันธ์และความเข้าใจที่ดี ระหว่างผู้ปกครอง กับครูและโรงเรียน และเพื่อรวมพลังขับเคลื่อนระบบดูแลช่วยเหลือนักเรียนให้มีประสิทธิภาพและยั่งยืน ต่อไป
            </p>
            <p style="text-indent: 1.5cm; margin: 0 0 15px 0; padding: 0;">
                บัดนี้ ข้าพเจ้าได้ออกเยี่ยมบ้านนักเรียนพร้อมทั้งลงข้อมูลในระบบการดูแลช่วยเหลือนักเรียน ครบ <?= toThaiNumber(100) ?> เปอร์เซนแล้ว พร้อมนี้ได้แนบรายชื่อนักเรียนที่มีความจำเป็นทางด้านเศรษฐกิจ (ยากจน ) มาพร้อมบันทึกข้อความฉบับนี้
            </p>
            <p style="text-indent: 1.5cm; margin: 0 0 35px 0; padding: 0;">
                จึงเรียนมาเพื่อโปรดทราบ
            </p>
        </div>

        <!-- stacked Advisor Signature Blocks on the right side of Page 1 -->
        <div style="width: 100%; margin-top: 40px;">
            <div style="float: right; width: 320px; line-height: 1.6;">
                <?php foreach ($roomTeachers as $t): ?>
                <div style="margin-bottom: 80px; text-align: center;">
                    <p style="margin: 0; padding: 0;">( &nbsp;<?= htmlspecialchars($t['Teach_name']) ?>&nbsp; )</p>
                    <p style="margin: 3px 0 0 0; padding: 0;">
                        ครูที่ปรึกษาชั้นมัธยมศึกษาปีที่ <?= toThaiNumber(htmlspecialchars($class . "/" . $room)) ?>
                    </p>
                    <p style="margin: 3px 0 0 0; padding: 0;">วันที่ &nbsp;<?= toThaiNumber($currentDay) ?>&nbsp; เดือน &nbsp;<?= htmlspecialchars($currentMonthName) ?>&nbsp; พ.ศ. &nbsp;<?= toThaiNumber($currentYear) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- Page 2: รายชื่อนักเรียนยากจน -->
    <div class="print-container page-break">
        <!-- Document Title -->
        <div style="text-align: center; font-weight: bold; line-height: 1.6; margin-bottom: 25px;">
            <p style="margin: 0 0 5px 0; padding: 0;">รายชื่อนักเรียนที่มีความจำเป็นทางด้านเศรษฐกิจ (ยากจน )</p>
            <p style="margin: 0 0 5px 0; padding: 0;">ปีการศึกษา <?= toThaiNumber($pee) ?></p>
            <p style="margin: 0; padding: 0;">ชั้นมัธยมศึกษาปีที่ <?= toThaiNumber(htmlspecialchars($class . "/" . $room)) ?></p>
            <p style="margin: 8px 0 0 0; padding: 0; letter-spacing: 2px;">******************************************</p>
        </div>
        
        <!-- Poor Students List -->
        <div style="margin: 25px auto; width: 95%; line-height: 2.0;">
            <?php 
            $poorList = [];
            foreach ($poorStudents as $p) {
                $poorList[] = $p['Stu_pre'] . $p['Stu_name'] . ' ' . $p['Stu_sur'];
            }
            if (empty($poorList)): 
            ?>
                <div style="text-align: center; font-weight: normal; font-style: italic; margin-top: 20px;">
                    - ไม่มีรายชื่อนักเรียนที่มีความจำเป็นทางด้านเศรษฐกิจ (ยากจน) -
                </div>
            <?php 
            else:
                foreach ($poorList as $i => $studentName): 
                    $num = $i + 1;
                ?>
                    <div style="margin-bottom: 12px; display: flex; align-items: baseline;">
                        <span style="width: 30px; flex-shrink: 0; font-weight: normal;"><?= toThaiNumber($num) ?>.</span>
                        <span style="font-weight: normal; padding-left: 5px;">
                            <?= htmlspecialchars($studentName) ?>
                        </span>
                    </div>
                <?php 
                endforeach; 
            endif;
            ?>
        </div>
        
        <!-- Certification Signatures Footer -->
        <div style="width: 100%; margin-top: 50px;">
            <div style="float: right; width: 320px; text-align: center; line-height: 1.6;">
                <p style="margin: 0 0 35px 0; padding: 0; font-weight: bold;">ผู้รับรองข้อมูล</p>
                <?php foreach ($roomTeachers as $t): ?>
                <div style="margin-bottom: 25px;margin-top: 50px;">
                    <p style="margin: 0; padding: 0;">( &nbsp;<?= htmlspecialchars($t['Teach_name']) ?>&nbsp; )</p>
                    <p style="margin: 3px 0 0 0; padding: 0;">ครูที่ปรึกษา</p>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- JavaScript Actions -->
    <script>
        let currentSize = 10;
        function changeFontSize(diff) {
            currentSize = Math.max(8, Math.min(24, currentSize + diff));
            document.documentElement.style.setProperty('--doc-font-size', currentSize + 'pt');
            document.getElementById('fontSizeDisplay').textContent = currentSize + 'pt';
        }

        function exportToWord() {
            // Document styles for Word export
            const header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>" +
                "<head><meta charset='utf-8'><title>บันทึกข้อความเยี่ยมบ้าน</title>" +
                "<style>" +
                "@page {" +
                "size: 21cm 29.7cm;" + /* A4 size */
                "margin-left: 3cm;" +
                "margin-right: 2cm;" +
                "margin-top: 2.5cm;" +
                "margin-bottom: 2.5cm;" +
                "}" +
                "body { font-family: 'Sarabun', sans-serif; font-size: " + (currentSize * 1.33) + "pt; line-height: 1.6; color: black; }" +
                ".page-break { page-break-before: always; }" +
                "table { width: 100%; border-collapse: collapse; border: none; }" +
                "td { border: none; }" +
                ".dotted-line-fill { border-bottom: 1px dotted #000; width: 100%; min-width: 200px; }" +
                ".memo-title { font-size: " + ((currentSize + 2) * 1.33) + "pt !important; }" +
                "</style></head><body>";
            const footer = "</body></html>";
            
            let content = "";
            const containers = document.querySelectorAll('.print-container');
            containers.forEach((container, index) => {
                let inner = container.innerHTML;
                
                // For Page 2 onwards, add Word page-break class
                if (index > 0) {
                    content += "<div class='page-break'>" + inner + "</div>";
                } else {
                    content += "<div>" + inner + "</div>";
                }
            });
            
            // Convert relative image path to absolute URL so Word can resolve it
            const baseUrl = window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
            content = content.replace(/src="\.\.\//g, 'src="' + baseUrl + '../');
            
            const html = header + content + footer;
            
            const blob = new Blob(['\ufeff' + html], {
                type: 'application/msword'
            });
            
            // Trigger download
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'บันทึกข้อความเยี่ยมบ้าน_ม_' + '<?= htmlspecialchars($class . "_" . $room) ?>' + '.doc';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
