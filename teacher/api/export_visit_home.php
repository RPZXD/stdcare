<?php
session_start();

require_once "../../config/Database.php";
require_once "../../class/UserLogin.php";
require_once "../../class/Utils.php";

// Check if PhpSpreadsheet exists
$useExcel = false;
if (file_exists("../../vendor/autoload.php")) {
    require_once "../../vendor/autoload.php";
    if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        $useExcel = true;
    }
}

// Check authentication
if (!isset($_SESSION['Teacher_login'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo 'Unauthorized';
    exit;
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$user = new UserLogin($db);

// Get parameters
$class = isset($_GET['class']) ? intval($_GET['class']) : 0;
$room = isset($_GET['room']) ? intval($_GET['room']) : 0;
$term = $user->getTerm();
$pee = $user->getPee();

if ($class === 0 || $room === 0) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid parameters';
    exit;
}

try {
    // Fetch students in the class with proper encoding
    $sql = "SELECT s.Stu_id, s.Stu_no, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_phone, s.Par_phone, s.Stu_addr
            FROM student s 
            WHERE s.Stu_major = :class AND s.Stu_room = :room 
            AND s.Stu_status = '1'
            ORDER BY s.Stu_no ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':class', $class, PDO::PARAM_INT);
    $stmt->bindParam(':room', $room, PDO::PARAM_INT);
    $stmt->execute();
    
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process each student's visit data
    $exportData = [];
    
    foreach ($students as $student) {
        // Get visit data for both rounds - check if picture3 exists
        $round1Data = null;
        $round2Data = null;
        
        // Round 1 (Term = 1) - check for picture3
        $visitSql = "SELECT * FROM visithome 
                     WHERE Stu_id = :stu_id AND Term = '1' AND Pee = :pee 
                     AND picture3 IS NOT NULL AND picture3 != ''";
        $visitStmt = $db->prepare($visitSql);
        $visitStmt->bindParam(':stu_id', $student['Stu_id']);
        $visitStmt->bindParam(':pee', $pee);
        $visitStmt->execute();
        $round1Data = $visitStmt->fetch(PDO::FETCH_ASSOC);
        
        // Round 2 (Term = 2) - check for picture3
        $visitSql = "SELECT * FROM visithome 
                     WHERE Stu_id = :stu_id AND Term = '2' AND Pee = :pee 
                     AND picture3 IS NOT NULL AND picture3 != ''";
        $visitStmt = $db->prepare($visitSql);
        $visitStmt->bindParam(':stu_id', $student['Stu_id']);
        $visitStmt->bindParam(':pee', $pee);
        $visitStmt->execute();
        $round2Data = $visitStmt->fetch(PDO::FETCH_ASSOC);
        
        // Check completion status based on picture3
        $round1Complete = $round1Data ? 'เสร็จสิ้น' : 'ยังไม่เสร็จ';
        $round2Complete = $round2Data ? 'เสร็จสิ้น' : 'ยังไม่เสร็จ';
        
        // Overall status
        $overallStatus = 'ยังไม่เริ่ม';
        if ($round1Complete === 'เสร็จสิ้น' && $round2Complete === 'เสร็จสิ้น') {
            $overallStatus = 'เสร็จสิ้นทั้ง 2 รอบ';
        } elseif ($round1Complete === 'เสร็จสิ้น' || $round2Complete === 'เสร็จสิ้น') {
            $overallStatus = 'เสร็จสิ้นบางส่วน';
        }
        
        // Clean data properly for Thai characters
        $addr = trim($student['Stu_addr'] ?? '');
        $prob1 = trim($round1Data['vh20'] ?? '');
        $prob2 = trim($round2Data['vh20'] ?? '');
        
        $exportData[] = [
            'เลขที่' => $student['Stu_no'],
            'รหัสนักเรียน' => $student['Stu_id'],
            'ชื่อ-นามสกุล' => $student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur'],
            'ที่อยู่' => $addr ?: '-',
            'เบอร์โทรนักเรียน' => $student['Stu_phone'] ?: '-',
            'เบอร์โทรผู้ปกครอง' => $student['Par_phone'] ?: '-',
            'การเยี่ยมรอบที่ 1' => $round1Complete,
            'การเยี่ยมรอบที่ 2' => $round2Complete,
            'สถานะรวม' => $overallStatus,
            'ปัญหา/อุปสรรครอบที่ 1' => $prob1 ?: '-',
            'ปัญหา/อุปสรรครอบที่ 2' => $prob2 ?: '-'
        ];
    }
    
    if ($useExcel) {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set default font
            $spreadsheet->getDefaultStyle()->getFont()->setName('TH Sarabun NEW')->setSize(16);

            $spreadsheet->getProperties()
                ->setCreator("ระบบดูแลช่วยเหลือนักเรียน")
                ->setTitle("รายงานการเยี่ยมบ้านนักเรียน")
                ->setSubject("รายงานการเยี่ยมบ้าน ม.{$class}/{$room}")
                ->setDescription("รายงานการเยี่ยมบ้านนักเรียน ระดับชั้น ม.{$class}/{$room} ปีการศึกษา {$pee}");

            // Header information
            $sheet->setCellValue('A1', 'รายงานการเยี่ยมบ้านนักเรียน');
            $sheet->setCellValue('A2', 'ระดับชั้น: ม.' . $class . '/' . $room);
            $sheet->setCellValue('A3', 'ปีการศึกษา: ' . $pee);
            $sheet->setCellValue('A4', 'วันที่ออกรายงาน: ' . date('d/m/Y H:i:s'));

            // Style header
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(20)->setName('TH Sarabun NEW');
            $sheet->getStyle('A2:A4')->getFont()->setBold(true)->setSize(16)->setName('TH Sarabun NEW');
            $sheet->getStyle('A1:A4')->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->mergeCells('A1:K1');
            $sheet->mergeCells('A2:K2');
            $sheet->mergeCells('A3:K3');
            $sheet->mergeCells('A4:K4');

            // Column headers
            $headers = [
                'A6' => 'เลขที่',
                'B6' => 'รหัสนักเรียน',
                'C6' => 'ชื่อ-นามสกุล',
                'D6' => 'ที่อยู่',
                'E6' => 'เบอร์โทรนักเรียน',
                'F6' => 'เบอร์โทรผู้ปกครอง',
                'G6' => 'การเยี่ยมรอบที่ 1',
                'H6' => 'การเยี่ยมรอบที่ 2',
                'I6' => 'สถานะรวม',
                'J6' => 'ปัญหา/อุปสรรครอบที่ 1',
                'K6' => 'ปัญหา/อุปสรรครอบที่ 2'
            ];
            
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Style column headers
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'name' => 'TH Sarabun NEW',
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    ]
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => ['rgb' => '4F8EF7'],
                    'endColor' => ['rgb' => '7F53AC']
                ]
            ];
            $sheet->getStyle('A6:K6')->applyFromArray($headerStyle);

            // Set column widths FIRST - ก่อนเพิ่มข้อมูล
            $columnWidths = [
                'A' => 8,   // เลขที่
                'B' => 15,  // รหัสนักเรียน
                'C' => 25,  // ชื่อ-นามสกุล
                'D' => 40,  // ที่อยู่
                'E' => 15,  // เบอร์โทรนักเรียน
                'F' => 15,  // เบอร์โทรผู้ปกครอง
                'G' => 12,  // การเยี่ยมรอบที่ 1
                'H' => 12,  // การเยี่ยมรอบที่ 2
                'I' => 15,  // สถานะรวม
                'J' => 30,  // ปัญหา/อุปสรรครอบที่ 1
                'K' => 30   // ปัญหา/อุปสรรครอบที่ 2
            ];
            
            foreach ($columnWidths as $column => $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
            }

            // Add data
            $row = 7;
            foreach ($exportData as $data) {
                $col = 'A';
                foreach ($data as $value) {
                    $cleanValue = is_string($value) ? mb_substr($value, 0, 32767, 'UTF-8') : $value;
                    $sheet->setCellValue($col . $row, $cleanValue);
                    $col++;
                }
                $row++;
            }

            // Style data rows
            if (!empty($exportData)) {
                $dataRange = 'A7:K' . ($row - 1);
                $dataStyle = [
                    'font' => [
                        'name' => 'TH Sarabun NEW',
                        'size' => 14
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                        'wrapText' => true
                    ]
                ];
                $sheet->getStyle($dataRange)->applyFromArray($dataStyle);

                // Alternate row colors and status colors
                for ($i = 7; $i < $row; $i++) {
                    // Alternate row background
                    if (($i - 7) % 2 == 1) {
                        $sheet->getStyle('A' . $i . ':K' . $i)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F7F6F3');
                    }

                    // Status column colors
                    $statusG = $sheet->getCell('G' . $i)->getValue();
                    $statusH = $sheet->getCell('H' . $i)->getValue();
                    $statusI = $sheet->getCell('I' . $i)->getValue();

                    // Color coding for visit status
                    $colors = [
                        'เสร็จสิ้น' => ['bg' => 'C6EFCE', 'text' => '006100'],
                        'ยังไม่เสร็จ' => ['bg' => 'FFC7CE', 'text' => '9C0006'],
                        'เสร็จสิ้นทั้ง 2 รอบ' => ['bg' => 'C6EFCE', 'text' => '006100'],
                        'เสร็จสิ้นบางส่วน' => ['bg' => 'FFEB9C', 'text' => '9C6500'],
                        'ยังไม่เริ่ม' => ['bg' => 'FFC7CE', 'text' => '9C0006']
                    ];

                    foreach (['G' => $statusG, 'H' => $statusH, 'I' => $statusI] as $col => $status) {
                        if (isset($colors[$status])) {
                            $sheet->getStyle($col . $i)->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB($colors[$status]['bg']);
                            $sheet->getStyle($col . $i)->getFont()
                                ->getColor()->setRGB($colors[$status]['text']);
                        }
                    }
                }
            }

            // Summary section
            $summaryRow = $row + 2;
            $sheet->setCellValue('A' . $summaryRow, 'สรุปสถิติ');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(18)->setName('TH Sarabun NEW');
            $sheet->mergeCells('A' . $summaryRow . ':K' . $summaryRow);

            // Calculate statistics
            $stats = array_reduce($exportData, function($carry, $data) {
                $carry['total']++;
                if ($data['การเยี่ยมรอบที่ 1'] === 'เสร็จสิ้น') $carry['round1']++;
                if ($data['การเยี่ยมรอบที่ 2'] === 'เสร็จสิ้น') $carry['round2']++;
                if ($data['สถานะรวม'] === 'เสร็จสิ้นทั้ง 2 รอบ') $carry['both']++;
                return $carry;
            }, ['total' => 0, 'round1' => 0, 'round2' => 0, 'both' => 0]);

            $summaryData = [
                ['จำนวนนักเรียนทั้งหมด', $stats['total'], 'คน'],
                ['เยี่ยมบ้านรอบที่ 1 เสร็จสิ้น', $stats['round1'], 'คน (' . ($stats['total'] > 0 ? round(($stats['round1'] / $stats['total']) * 100, 2) : 0) . '%)'],
                ['เยี่ยมบ้านรอบที่ 2 เสร็จสิ้น', $stats['round2'], 'คน (' . ($stats['total'] > 0 ? round(($stats['round2'] / $stats['total']) * 100, 2) : 0) . '%)'],
                ['เยี่ยมบ้านเสร็จสิ้นทั้ง 2 รอบ', $stats['both'], 'คน (' . ($stats['total'] > 0 ? round(($stats['both'] / $stats['total']) * 100, 2) : 0) . '%)']
            ];

            $summaryStartRow = $summaryRow + 1;
            foreach ($summaryData as $index => $summaryItem) {
                $currentRow = $summaryStartRow + $index;
                $sheet->setCellValue('A' . $currentRow, $summaryItem[0]);
                $sheet->setCellValue('B' . $currentRow, $summaryItem[1]);
                $sheet->setCellValue('C' . $currentRow, $summaryItem[2]);
                
                $summaryStyle = [
                    'font' => [
                        'bold' => true,
                        'name' => 'TH Sarabun NEW',
                        'size' => 14
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7E6E6']
                    ]
                ];
                $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->applyFromArray($summaryStyle);
            }

            // Generate filename
            $safeFilename = "รายงานการเยี่ยมบ้าน_ม{$class}_{$room}_ปีการศึกษา{$pee}_" . date('Y-m-d_H-i-s') . ".xlsx";
            
            // Clear output buffer and set headers
            if (ob_get_level()) ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $safeFilename . '"');
            header('Cache-Control: max-age=0');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Pragma: public');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
            
        } catch (Exception $e) {
            $useExcel = false;
        }
    }
    
    if (!$useExcel) {
        // Fallback to CSV
        $filename = "รายงานการเยี่ยมบ้าน_ม{$class}_{$room}_ปีการศึกษา{$pee}_" . date('Y-m-d_H-i-s') . ".csv";
        
        // Clear any output buffers
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        // Add BOM for UTF-8
        echo "\xEF\xBB\xBF";
        
        // Create file pointer connected to php://output
        $output = fopen('php://output', 'w');
        
        // Add header information
        fputcsv($output, ['รายงานการเยี่ยมบ้านนักเรียน']);
        fputcsv($output, ['ระดับชั้น: ม.' . $class . '/' . $room]);
        fputcsv($output, ['ปีการศึกษา: ' . $pee]);
        fputcsv($output, ['วันที่ออกรายงาน: ' . date('d/m/Y H:i:s')]);
        fputcsv($output, []); // Empty row
        
        // Add column headers
        if (!empty($exportData)) {
            fputcsv($output, array_keys($exportData[0]));
            
            // Add data rows
            foreach ($exportData as $row) {
                fputcsv($output, $row);
            }
        } else {
            fputcsv($output, ['ไม่พบข้อมูล']);
        }
        
        // Add summary
        fputcsv($output, []); // Empty row
        fputcsv($output, ['สรุปสถิติ']);
        
        $totalStudents = count($exportData);
        $round1Completed = 0;
        $round2Completed = 0;
        $bothCompleted = 0;
        
        foreach ($exportData as $row) {
            if ($row['การเยี่ยมรอบที่ 1'] === 'เสร็จสิ้น') $round1Completed++;
            if ($row['การเยี่ยมรอบที่ 2'] === 'เสร็จสิ้น') $round2Completed++;
            if ($row['สถานะรวม'] === 'เสร็จสิ้นทั้ง 2 รอบ') $bothCompleted++;
        }
        
        fputcsv($output, ['จำนวนนักเรียนทั้งหมด', $totalStudents, 'คน']);
        fputcsv($output, ['เยี่ยมบ้านรอบที่ 1 เสร็จสิ้น', $round1Completed, 'คน', '(' . ($totalStudents > 0 ? round(($round1Completed / $totalStudents) * 100, 2) : 0) . '%)']);
        fputcsv($output, ['เยี่ยมบ้านรอบที่ 2 เสร็จสิ้น', $round2Completed, 'คน', '(' . ($totalStudents > 0 ? round(($round2Completed / $totalStudents) * 100, 2) : 0) . '%)']);
        fputcsv($output, ['เยี่ยมบ้านเสร็จสิ้นทั้ง 2 รอบ', $bothCompleted, 'คน', '(' . ($totalStudents > 0 ? round(($bothCompleted / $totalStudents) * 100, 2) : 0) . '%)']);
        
        fclose($output);
    }
    
} catch (Exception $e) {
    // Clear any output buffers before sending error
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Error: ' . $e->getMessage();
}
?>
