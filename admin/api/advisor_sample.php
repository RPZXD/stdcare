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

// Query data
$sql = "SELECT Teach_id, Teach_name, Teach_class, Teach_room , Teach_major
        FROM teacher 
        WHERE Teach_status = 1 
          AND Teach_class > 0 
          AND Teach_room > 0
        ORDER BY Teach_class, Teach_room, Teach_id";
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
$sheet->fromArray(['รหัสครู', 'ชั้นปี', 'ห้อง', 'ชื่อครู', 'กลุ่มสาระ'], NULL, 'A1');

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
$sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

// กำหนดความกว้างของ cell
$sheet->getColumnDimension('A')->setWidth(10); // รหัสครู
$sheet->getColumnDimension('B')->setWidth(10); // ชั้นปี
$sheet->getColumnDimension('C')->setWidth(10); // ห้อง
$sheet->getColumnDimension('D')->setWidth(30); // ชื่อครู
$sheet->getColumnDimension('E')->setWidth(40); // กลุ่มสาระ

// Data
$row = 2;
foreach ($data as $item) {
    $sheet->setCellValue("A$row", $item['Teach_id']);
    $sheet->setCellValue("B$row", $item['Teach_class']);
    $sheet->setCellValue("C$row", $item['Teach_room']);
    $sheet->setCellValue("D$row", $item['Teach_name']);
    $sheet->setCellValue("E$row", $item['Teach_major']);
    $row++;
}

// Output
ob_end_clean(); // ป้องกันปัญหา output ก่อน header (ถ้ามี)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="advisor_sample.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
