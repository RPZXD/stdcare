<?php
require_once __DIR__ . '/../../config/Database.php';

// ตรวจสอบ autoload ของ Composer
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "กรุณาติดตั้งไลบรารี phpoffice/phpspreadsheet ก่อนใช้งาน (composer require phpoffice/phpspreadsheet)\n";
    exit;
}
require_once $autoloadPath;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// DB connect
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Query data: เลือกเลขประจำตัว, ชั้นปี, ห้อง, เลขที่ปัจจุบัน, ชื่อ-สกุล
$sql = "SELECT Stu_id, Stu_major, Stu_room, Stu_no, CONCAT(Stu_pre, Stu_name, ' ',Stu_sur) AS fullname
        FROM student
        WHERE Stu_status = 1
        ORDER BY Stu_major, Stu_room, Stu_no, Stu_id";
$stmt = $db->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ตั้งค่าฟอนต์ Sarabun และขนาด 16
$defaultStyle = $spreadsheet->getDefaultStyle();
$defaultStyle->getFont()->setName('TH Sarabun New')->setSize(16);

// Header (ภาษาไทย)
$sheet->fromArray(['เลขประจำตัว', 'ชั้นปี', 'ห้อง', 'เลขที่ใหม่', 'เลขที่เดิม', 'ชื่อ-สกุล'], NULL, 'A1');

// จัดรูปแบบหัวตาราง: ตัวหนา, กึ่งกลาง, bg น้ำเงิน, ตัวอักษรขาว
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'name' => 'TH Sarabun New',
        'size' => 16
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => '2563eb'] // Tailwind blue-600
    ]
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// กำหนดความกว้างของ cell
$sheet->getColumnDimension('A')->setWidth(15); // เลขประจำตัว
$sheet->getColumnDimension('B')->setWidth(10); // ชั้นปี
$sheet->getColumnDimension('C')->setWidth(10); // ห้อง
$sheet->getColumnDimension('D')->setWidth(12); // เลขที่ใหม่
$sheet->getColumnDimension('E')->setWidth(12); // เลขที่เดิม
$sheet->getColumnDimension('F')->setWidth(30); // ชื่อ-สกุล

// Data
$row = 2;
foreach ($data as $item) {
    $sheet->setCellValue("A$row", $item['Stu_id']);
    $sheet->setCellValue("B$row", $item['Stu_major']);
    $sheet->setCellValue("C$row", $item['Stu_room']);
    $sheet->setCellValue("D$row", ''); // เลขที่ใหม่ (ให้กรอก)
    $sheet->setCellValue("E$row", $item['Stu_no']);
    $sheet->setCellValue("F$row", $item['fullname']);
    $row++;
}

// Output
ob_end_clean(); // ป้องกันปัญหา output ก่อน header (ถ้ามี)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="update_number_sample.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
