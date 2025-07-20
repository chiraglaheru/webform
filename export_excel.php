<?php
require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$servername = "localhost";
$username = "root";
$password = "root";
$database = "form_data";
$port = 8889;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$spreadsheet = new Spreadsheet();

// ========== SHEET 1: Applications ==========
$appSheet = $spreadsheet->getActiveSheet();
$appSheet->setTitle('Applications');

$applicationHeaders = [
    "ID", "Post Applied For", "Title", "First Name", "Middle Name", "Last Name", "DOB", "Age", "Gender", "Marital Status",
    "Email", "Alternate Email", "Caste", "Aadhar", "PAN", "State", "City", "Address", "Pincode",
    "Mobile", "Alternate Mobile", "Institute Applied To", "Current Salary", "Expected Salary",
    "Extra Curricular", "Reference Name", "Reference Applied For", "Resume File",
    "PhD Status", "PhD University", "PhD Year", "BED University", "BED Year"
];
$appSheet->fromArray($applicationHeaders, NULL, 'A1');

$applications = $conn->query("SELECT * FROM applications");
$row = 2;

while ($app = $applications->fetch_assoc()) {
    $col = 'A';

    $data = [
        $app['id'], $app['post_applied_for'], $app['title'], $app['first_name'], $app['middle_name'], $app['last_name'],
        $app['dob'], $app['age'], $app['gender'], $app['marital_status'], $app['email'], $app['alternate_email'],
        $app['caste'], $app['aadhar'], $app['pan'], $app['state'], $app['city'], $app['address'],
        $app['pincode'], $app['mobile'], $app['alternate_mobile'], $app['institute_applied_to'],
        $app['current_salary'], $app['expected_salary'], $app['extra_curricular'],
        $app['reference_name'], $app['reference_applied_for']
    ];

    // Fill cells A.. preceding Resume
    foreach ($data as $value) {
        $appSheet->setCellValue($col . $row, $value);
        $col++;
    }

    // === HYPERLINK cell for Resume ===
    $resumeURL = "http://localhost:8888/web-form-main/resumes/" . $app['resume_filename'];
    $appSheet->setCellValueExplicit(
        $col . $row,
        '=HYPERLINK("' . $resumeURL . '", "Download Resume")',
        DataType::TYPE_FORMULA
    );
    // Add blue underline style
    $appSheet->getStyle($col . $row)->applyFromArray([
        'font' => [
            'color' => ['rgb' => '0000FF'],
            'underline' => 'single'
        ]
    ]);
    $col++;

    // Fill remaining PhD and BED info
    $appSheet->setCellValue($col++ . $row, $app['phd_status']);
    $appSheet->setCellValue($col++ . $row, $app['phd_university']);
    $appSheet->setCellValue($col++ . $row, $app['phd_year']);
    $appSheet->setCellValue($col++ . $row, $app['bed_university']);
    $appSheet->setCellValue($col++ . $row, $app['bed_year']);

    $row++;
}

// ========== SHEET 2: Qualifications ==========
$qualSheet = $spreadsheet->createSheet();
$qualSheet->setTitle('Qualifications');
$qualSheet->fromArray([
    'Application ID', 'Degree', 'Degree Name', 'Education Mode', 'University', 'Specialization', 'Year of Passing', 'Percentage', 'CGPA'
], NULL, 'A1');
$q = $conn->query("SELECT * FROM qualifications");
$row = 2;
while ($r = $q->fetch_assoc()) {
    $qualSheet->fromArray(array_values($r), NULL, "A$row");
    $row++;
}

// ========== SHEET 3: Work Experience ==========
$expSheet = $spreadsheet->createSheet();
$expSheet->setTitle('Work Experience');
$expSheet->fromArray([
    'Application ID', 'Organization', 'Designation', 'From', 'To', 'Current Salary', 'Currently Working'
], NULL, 'A1');
$e = $conn->query("SELECT application_id, organization, designation, from_date, to_date, current_salary, currently_working FROM work_experience");
$row = 2;
while ($r = $e->fetch_assoc()) {
    $expSheet->fromArray(array_values($r), NULL, "A$row");
    $row++;
}

// ========== SHEET 4: Courses Taught ==========
$coursesSheet = $spreadsheet->createSheet();
$coursesSheet->setTitle('Courses Taught');
$coursesSheet->fromArray([
    'Application ID', 'College Name', 'Class Name', 'Subject Name', 'Years Experience', 'From Date', 'To Date',
    'Department Type', 'Contract Type', 'Last Salary', 'Approved By University', 'Letter Number', 'Letter Date'
], NULL, 'A1');
$c = $conn->query("SELECT * FROM courses_taught");
$row = 2;
while ($r = $c->fetch_assoc()) {
    $coursesSheet->fromArray(array_values($r), NULL, "A$row");
    $row++;
}

// ========== SHEET 5: Research Publications ==========
$researchSheet = $spreadsheet->createSheet();
$researchSheet->setTitle('Research Publications');
$researchSheet->fromArray([
    'Application ID', 'Scopus Publications', 'Scopus ID', 'Conference Presented',
    'Paper Title', 'Journal Name', 'Publication Year', 'Approved Papers'
], NULL, 'A1');
$r = $conn->query("SELECT * FROM research_publications");
$row = 2;
while ($rowData = $r->fetch_assoc()) {
    $researchSheet->fromArray(array_values($rowData), NULL, "A$row");
    $row++;
}

// ========== SHEET 6: Awards ==========
$awardsSheet = $spreadsheet->createSheet();
$awardsSheet->setTitle('Awards');
$awardsSheet->fromArray([
    'Application ID', 'Title', 'Award Organization', 'Award Nature', 'Award Salary'
], NULL, 'A1');
$a = $conn->query("SELECT application_id, award_title, award_organization, award_nature, award_salary FROM awards");
$row = 2;
while ($rowData = $a->fetch_assoc()) {
    $awardsSheet->fromArray(array_values($rowData), NULL, "A$row");
    $row++;
}

// ========== Final Output ==========
$filename = "application_export_detailed.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
