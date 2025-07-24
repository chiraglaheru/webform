<?php
require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$servername = "localhost";
$username = "root";
$password = "1234";
$database = "form_data";
$port = 8889;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$spreadsheet = new Spreadsheet();

// ========== SHEET 1: Main Application ==========
$appSheet = $spreadsheet->getActiveSheet();
$appSheet->setTitle('Main Application');

$applicationHeaders = [
    "ID", "Post Applied For", "Title", "First Name", "Middle Name", "Last Name", 
    "Date of Birth", "Age", "Gender", "Marital Status", "Email ID", "Alternate Email ID",
    "Caste/Sub-Caste", "Aadhar No", "PAN No", "State", "City", "Address", "PinCode",
    "Mobile No", "Alternate Mobile No", "Institute Applied To", "NET Status", "NET Year",
    "SET Status", "SET Year", "Current Salary", "Expected Salary", "Extra-Curricular",
    "Reference Name", "Applied For", "Resume File", "Declaration"
];
$appSheet->fromArray($applicationHeaders, NULL, 'A1');

$applications = $conn->query("SELECT * FROM degree_applications");
$row = 2;

while ($app = $applications->fetch_assoc()) {
    $col = 'A';

    $data = [
        $app['id'], $app['post_applied_for'], $app['title'], $app['first_name'], $app['middle_name'], 
        $app['last_name'], $app['dob'], $app['age'], $app['gender'], $app['marital_status'], 
        $app['email'], $app['alternate_email'], $app['caste'], $app['aadhar_no'], $app['pan_no'],
        $app['state'], $app['city'], $app['address'], $app['pincode'], $app['mobile_no'],
        $app['alternate_mobile_no'], $app['institute_applied_to'], $app['net_status'], $app['net_year'],
        $app['set_status'], $app['set_year'], $app['current_salary'], $app['expected_salary'],
        $app['extra_curricular'], $app['reference_name'], $app['applied_for']
    ];

    // Fill cells A.. preceding Resume
    foreach ($data as $value) {
        $appSheet->setCellValue($col . $row, $value);
        $col++;
    }

    // === HYPERLINK cell for Resume ===
    $resumeURL = "http://localhost:8888/degree-applications/resumes/" . $app['resume_filename'];
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

    // Declaration
    $appSheet->setCellValue($col . $row, $app['declaration'] ? "Yes" : "No");

    $row++;
}

// ========== SHEET 2: Qualifications ==========
$qualSheet = $spreadsheet->createSheet();
$qualSheet->setTitle('Qualifications');
$qualSheet->fromArray([
    'Application ID', 'Degree', 'Degree Name', 'Education Mode', 'University Name', 
    'Specialization', 'Year of Passing', 'Percentage', 'CGPA'
], NULL, 'A1');
$q = $conn->query("SELECT * FROM degree_qualifications");
$row = 2;
while ($r = $q->fetch_assoc()) {
    $qualSheet->fromArray(array_values($r), NULL, "A$row");
    $row++;
}

// ========== SHEET 3: PhD Details ==========
$phdSheet = $spreadsheet->createSheet();
$phdSheet->setTitle('PhD Details');
$phdSheet->fromArray([
    'Application ID', 'Status', 'University/Institute', 'Year of Passing'
], NULL, 'A1');
$p = $conn->query("SELECT * FROM degree_phd_details");
$row = 2;
while ($r = $p->fetch_assoc()) {
    $phdSheet->fromArray(array_values($r), NULL, "A$row");
    $row++;
}

// ========== SHEET 4: Work Experience ==========
$expSheet = $spreadsheet->createSheet();
$expSheet->setTitle('Work Experience');
$expSheet->fromArray([
    'Application ID', 'Organization/University', 'Designation/Post held', 
    'From Date', 'To Date', 'Current Salary', 'Currently Working'
], NULL, 'A1');
$e = $conn->query("SELECT * FROM degree_work_experience");
$row = 2;
while ($r = $e->fetch_assoc()) {
    $expSheet->fromArray(array_values($r), NULL, "A$row");
    $row++;
}

// ========== SHEET 5: Courses Taught ==========
$coursesSheet = $spreadsheet->createSheet();
$coursesSheet->setTitle('Courses Taught');
$coursesSheet->fromArray([
    'Application ID', 'College Name', 'Class Name', 'Subject Name', 
    'Years of Experience', 'From Date', 'To Date', 'Department Type',
    'Type of Contract', 'Last Salary', 'Approved By University',
    'Letter Number', 'Letter Date'
], NULL, 'A1');
$c = $conn->query("SELECT * FROM degree_courses_taught");
$row = 2;
while ($r = $c->fetch_assoc()) {
    $coursesSheet->fromArray(array_values($r), NULL, "A$row");
    $row++;
}

// ========== SHEET 6: Research Publications ==========
$researchSheet = $spreadsheet->createSheet();
$researchSheet->setTitle('Research Publications');
$researchSheet->fromArray([
    'Application ID', 'Scopus Indexed Publications', 'Scopus ID', 
    'Presented in Conference', 'Title Of The Paper', 'Name of Journal',
    'Year of Publication', 'Number of Approved Papers'
], NULL, 'A1');
$r = $conn->query("SELECT * FROM degree_research_publications");
$row = 2;
while ($rowData = $r->fetch_assoc()) {
    $researchSheet->fromArray(array_values($rowData), NULL, "A$row");
    $row++;
}

// ========== SHEET 7: Awards ==========
$awardsSheet = $spreadsheet->createSheet();
$awardsSheet->setTitle('Awards');
$awardsSheet->fromArray([
    'Application ID', 'Title', 'Organization Name', 
    'Nature of Award', 'Expected Award Salary'
], NULL, 'A1');
$a = $conn->query("SELECT * FROM degree_awards");
$row = 2;
while ($rowData = $a->fetch_assoc()) {
    $awardsSheet->fromArray(array_values($rowData), NULL, "A$row");
    $row++;
}

// ========== Final Output ==========
$filename = "degree_application_export_" . date('Y-m-d') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>