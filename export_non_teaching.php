<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "form_data";
$port = 8889;
$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';

$conn = new mysqli($servername, $username, $password, $database, $port, $socket);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create new Spreadsheet
$spreadsheet = new Spreadsheet();

// ========== SHEET 1: Main Applications ==========
$appSheet = $spreadsheet->getActiveSheet();
$appSheet->setTitle('Applications');

// Styling for headers
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4']
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
];

// Non-Teaching Application Headers (exactly matching your form)
$applicationHeaders = [
    "ID", "Post Applied For", "Other Post Type", 
    "First Name", "Middle Name", "Last Name",
    "Email", "Mobile Number", "Address", 
    "Date of Birth", "Mother Tongue", "Other Language",
    "English Typing Speed", "Marathi Typing Speed",
    "Joining Date", "Expected Salary", "Current Salary",
    "Comments", "CV Filename"
];

$appSheet->fromArray($applicationHeaders, NULL, 'A1');
$appSheet->getStyle('A1:S1')->applyFromArray($headerStyle);

// Set column widths
$columnWidths = [
    'A' => 8, 'B' => 20, 'C' => 15, 'D' => 15, 'E' => 15, 'F' => 15,
    'G' => 25, 'H' => 15, 'I' => 30, 'J' => 12, 'K' => 15, 'L' => 15,
    'M' => 20, 'N' => 20, 'O' => 15, 'P' => 15, 'Q' => 15, 'R' => 30,
    'S' => 20
];

foreach ($columnWidths as $column => $width) {
    $appSheet->getColumnDimension($column)->setWidth($width);
}

// Get all applications data
$applications = $conn->query("SELECT * FROM non_teaching_applications ORDER BY created_at DESC");
$row = 2;

while ($app = $applications->fetch_assoc()) {
    $appSheet->setCellValue('A'.$row, $app['id']);
    $appSheet->setCellValue('B'.$row, $app['post_applied_for']);
    $appSheet->setCellValue('C'.$row, $app['other_post_type'] ?? 'N/A');
    $appSheet->setCellValue('D'.$row, $app['first_name']);
    $appSheet->setCellValue('E'.$row, $app['middle_name'] ?? '');
    $appSheet->setCellValue('F'.$row, $app['last_name']);
    $appSheet->setCellValue('G'.$row, $app['email']);
    $appSheet->setCellValue('H'.$row, $app['mobile']);
    $appSheet->setCellValue('I'.$row, $app['address']);
    $appSheet->setCellValue('J'.$row, $app['dob']);
    $appSheet->setCellValue('K'.$row, $app['mother_tongue'] ?? 'N/A');
    $appSheet->setCellValue('L'.$row, $app['other_language'] ?? 'N/A');
    $appSheet->setCellValue('M'.$row, $app['english_typing'] ?? 'N/A');
    $appSheet->setCellValue('N'.$row, $app['marathi_typing'] ?? 'N/A');
    $appSheet->setCellValue('O'.$row, $app['joining_date'] ?? 'N/A');
    $appSheet->setCellValue('P'.$row, $app['expected_salary'] ?? 'N/A');
    $appSheet->setCellValue('Q'.$row, $app['current_salary'] ?? 'N/A');
    $appSheet->setCellValue('R'.$row, $app['comments'] ?? 'None');
    
    // CV file hyperlink
    if (!empty($app['cv_filename'])) {
        $cvURL = "http://" . $_SERVER['HTTP_HOST'] . "/web-form-main/resumes/" . $app['cv_filename'];
        $appSheet->setCellValueExplicit(
            'S'.$row,
            '=HYPERLINK("' . $cvURL . '", "Download CV")',
            DataType::TYPE_FORMULA
        );
        $appSheet->getStyle('S'.$row)->applyFromArray([
            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single']
        ]);
    } else {
        $appSheet->setCellValue('S'.$row, 'No CV');
    }

    // Apply data row styling
    $appSheet->getStyle('A'.$row.':S'.$row)->applyFromArray([
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ]);
    
    $row++;
}

// ========== SHEET 2: Qualifications ==========
$qualSheet = $spreadsheet->createSheet();
$qualSheet->setTitle('Qualifications');

$qualHeaders = [
    'ID', 'Application ID', 'Degree', 'University', 'Year', 'Percentage'
];
$qualSheet->fromArray($qualHeaders, NULL, 'A1');
$qualSheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// Set column widths
$qualSheet->getColumnDimension('A')->setWidth(8);
$qualSheet->getColumnDimension('B')->setWidth(12);
$qualSheet->getColumnDimension('C')->setWidth(25);
$qualSheet->getColumnDimension('D')->setWidth(25);
$qualSheet->getColumnDimension('E')->setWidth(8);
$qualSheet->getColumnDimension('F')->setWidth(12);

$qualifications = $conn->query("SELECT * FROM nt_qualifications ORDER BY application_id, year DESC");

$row = 2;
while ($qual = $qualifications->fetch_assoc()) {
    $qualSheet->setCellValue('A'.$row, $qual['id']);
    $qualSheet->setCellValue('B'.$row, $qual['application_id']);
    $qualSheet->setCellValue('C'.$row, $qual['degree']);
    $qualSheet->setCellValue('D'.$row, $qual['university']);
    $qualSheet->setCellValue('E'.$row, $qual['year']);
    $qualSheet->setCellValue('F'.$row, $qual['percentage']);
    
    $row++;
}

// ========== SHEET 3: Work Experience ==========
$expSheet = $spreadsheet->createSheet();
$expSheet->setTitle('Experience');

$expHeaders = [
    'ID', 'Application ID', 'Organization', 'Designation', 
    'From Date', 'To Date', 'Salary'
];
$expSheet->fromArray($expHeaders, NULL, 'A1');
$expSheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Set column widths
$expSheet->getColumnDimension('A')->setWidth(8);
$expSheet->getColumnDimension('B')->setWidth(12);
$expSheet->getColumnDimension('C')->setWidth(25);
$expSheet->getColumnDimension('D')->setWidth(20);
$expSheet->getColumnDimension('E')->setWidth(12);
$expSheet->getColumnDimension('F')->setWidth(12);
$expSheet->getColumnDimension('G')->setWidth(15);

$experiences = $conn->query("SELECT * FROM nt_experience ORDER BY application_id, from_date DESC");

$row = 2;
while ($exp = $experiences->fetch_assoc()) {
    $expSheet->setCellValue('A'.$row, $exp['id']);
    $expSheet->setCellValue('B'.$row, $exp['application_id']);
    $expSheet->setCellValue('C'.$row, $exp['organization']);
    $expSheet->setCellValue('D'.$row, $exp['designation']);
    $expSheet->setCellValue('E'.$row, $exp['from_date']);
    $expSheet->setCellValue('F'.$row, $exp['to_date'] ?? 'Present');
    $expSheet->setCellValue('G'.$row, $exp['salary'] ?? 'N/A');
    
    $row++;
}

// ========== Final Output ==========
$filename = "Non_Teaching_Applications_Export_" . date('Y-m-d_His') . ".xlsx";

// Redirect output to a client's web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');

// Save Excel 2007 file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>