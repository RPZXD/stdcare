<?php
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "กรุณาติดตั้ง phpoffice/phpspreadsheet (composer require phpoffice/phpspreadsheet)";
    exit;
}
require_once $autoloadPath;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('TH Sarabun New')->setSize(16);

// Header
$sheet->fromArray(['เลขประจำตัว', 'คำนำหน้า', 'ชื่อ', 'สกุล', 'ชั้น', 'ห้อง', 'เลขที่'], NULL, 'A1');

// ตัวอย่างข้อมูล
$sheet->fromArray(['12345', 'เด็กชาย', 'สมชาย', 'ใจดี', '1', '1', '1'], NULL, 'A2');
$sheet->fromArray(['12346', 'เด็กหญิง', 'สมหญิง', 'เก่งมาก', '1', '1', '2'], NULL, 'A3');

// สร้าง dropdown validation สำหรับคำนำหน้า (B2:B100)
$prefixValidation = $sheet->getCell('B2')->getDataValidation();
$prefixValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
$prefixValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
$prefixValidation->setAllowBlank(false);
$prefixValidation->setShowInputMessage(true);
$prefixValidation->setShowErrorMessage(true);
$prefixValidation->setShowDropDown(true);
$prefixValidation->setFormula1('"เด็กชาย,เด็กหญิง,นาย,นางสาว"');
for ($i = 2; $i <= 100; $i++) {
    $sheet->getCell("B$i")->setDataValidation(clone $prefixValidation);
}

// สร้าง dropdown validation สำหรับชั้น (E2:E100)
$classValidation = $sheet->getCell('E2')->getDataValidation();
$classValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
$classValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
$classValidation->setAllowBlank(false);
$classValidation->setShowInputMessage(true);
$classValidation->setShowErrorMessage(true);
$classValidation->setShowDropDown(true);
$classValidation->setFormula1('"1,4"');
for ($i = 2; $i <= 100; $i++) {
    $sheet->getCell("E$i")->setDataValidation(clone $classValidation);
}

// จัดรูปแบบหัวตาราง
$sheet->getStyle('A1:G1')->applyFromArray([
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
        'startColor' => ['rgb' => '2563eb']
    ]
]);
$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(12);
$sheet->getColumnDimension('C')->setWidth(18);
$sheet->getColumnDimension('D')->setWidth(18);
$sheet->getColumnDimension('E')->setWidth(10);
$sheet->getColumnDimension('F')->setWidth(10);
$sheet->getColumnDimension('G')->setWidth(10);

ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="student_sample.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
