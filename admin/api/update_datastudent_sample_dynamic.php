<?php
require_once("../../config/Database.php");
require_once("../../class/Student.php");

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

// รับค่า GET
$pe = isset($_GET['pe']) ? intval($_GET['pe']) : 0;
$room = isset($_GET['room']) ? intval($_GET['room']) : 0;
if ($pe < 1 || $room < 1) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "กรุณาระบุชั้นและห้องให้ถูกต้อง";
    exit;
}

// DB connect
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Query data: ดึงข้อมูลทุกฟิลด์
$sql = "SELECT 
    Stu_id, Stu_no, Stu_password, Stu_sex, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_nick, Stu_birth, Stu_religion, Stu_blood, Stu_addr, Stu_phone,
    Father_name, Father_occu, Father_income, Mother_name, Mother_occu, Mother_income, Par_name, Par_relate, Par_occu, Par_income, Par_addr, Par_phone, Risk_group, Stu_status, Stu_citizenid
    FROM student
    WHERE Stu_major = ? AND Stu_room = ? AND Stu_status = 1
    ORDER BY Stu_no ASC";
$stmt = $db->prepare($sql);
$stmt->execute([$pe, $room]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ตั้งค่าฟอนต์ Sarabun และขนาด 16
$defaultStyle = $spreadsheet->getDefaultStyle();
$defaultStyle->getFont()->setName('TH Sarabun New')->setSize(16);

// Header (ชื่อฟิลด์)
$headers = [
    'Stu_id','Stu_no','Stu_password','Stu_sex','Stu_pre','Stu_name','Stu_sur','Stu_major','Stu_room','Stu_nick','Stu_birth','Stu_religion','Stu_blood','Stu_addr','Stu_phone',
    'Father_name','Father_occu','Father_income','Mother_name','Mother_occu','Mother_income','Par_name','Par_relate','Par_occu','Par_income','Par_addr','Par_phone','Risk_group','Stu_status','Stu_citizenid'
];
$sheet->fromArray($headers, NULL, 'A1');

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
$sheet->getStyle('A1:AE1')->applyFromArray($headerStyle);

// กำหนดความกว้างของ cell (ตัวอย่าง)
foreach (range('A', 'AE') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Data
$row = 2;
foreach ($data as $item) {
    $col = 'A';
    foreach ($headers as $field) {
        // ป้องกันปัญหา formula-injection (Excel security)
        $value = isset($item[$field]) ? $item[$field] : '';
        if (is_string($value) && in_array(substr($value, 0, 1), ['=', '+', '-', '@'])) {
            $value = "'" . $value;
        }
        $sheet->setCellValueExplicit($col . $row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $col++;
    }
    $row++;
}

// Output
ob_end_clean(); // ป้องกันปัญหา output ก่อน header (ถ้ามี)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="student_update_pe'.$pe.'_room'.$room.'_'.date('Ymd_His').'.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
