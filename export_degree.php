<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Database configuration
$servername = "localhost";
$username = "root";
$password = "root";
$database = "degree_applications";
$port = 8889;

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create new spreadsheet
$spreadsheet = new Spreadsheet();

// ========== SHEET 1: Main Applications ==========
$appSheet = $spreadsheet->getActiveSheet();
$appSheet->setTitle('Applications');

// Header style
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
];

// Main application headers
$appHeaders = [
    "ID", "Post Applied", "Title", "Full Name", "Email", "Mobile", 
    "DOB", "Gender", "Marital Status", "Aadhar No", "PAN No",
    "Address", "State", "City", "Pincode", "Institute Applied To",
    "NET", "NET Year", "SET", "SET Year", "Current Salary", 
    "Expected Salary", "Scopus Publications", "Scopus ID",
    "Conference Presented", "Approved Papers", "Reference Name",
    "Extra-Curricular", "Resume Link"
];

$appSheet->fromArray($appHeaders, NULL, 'A1');
$appSheet->getStyle('A1:AB1')->applyFromArray($headerStyle);

// Set column widths
$appSheet->getColumnDimension('A')->setWidth(8);
$appSheet->getColumnDimension('B')->setWidth(20);
$appSheet->getColumnDimension('C')->setWidth(10);
$appSheet->getColumnDimension('D')->setWidth(25);
$appSheet->getColumnDimension('E')->setWidth(25);
$appSheet->getColumnDimension('F')->setWidth(15);
$appSheet->getColumnDimension('G')->setWidth(12);
$appSheet->getColumnDimension('H')->setWidth(12);
$appSheet->getColumnDimension('I')->setWidth(15);
$appSheet->getColumnDimension('J')->setWidth(15);
$appSheet->getColumnDimension('K')->setWidth(15);
$appSheet->getColumnDimension('L')->setWidth(30);
$appSheet->getColumnDimension('M')->setWidth(15);
$appSheet->getColumnDimension('N')->setWidth(15);
$appSheet->getColumnDimension('O')->setWidth(10);
$appSheet->getColumnDimension('P')->setWidth(25);
$appSheet->getColumnDimension('Q')->setWidth(10);
$appSheet->getColumnDimension('R')->setWidth(10);
$appSheet->getColumnDimension('S')->setWidth(10);
$appSheet->getColumnDimension('T')->setWidth(10);
$appSheet->getColumnDimension('U')->setWidth(15);
$appSheet->getColumnDimension('V')->setWidth(15);
$appSheet->getColumnDimension('W')->setWidth(15);
$appSheet->getColumnDimension('X')->setWidth(15);
$appSheet->getColumnDimension('Y')->setWidth(15);
$appSheet->getColumnDimension('Z')->setWidth(15);
$appSheet->getColumnDimension('AA')->setWidth(20);
$appSheet->getColumnDimension('AB')->setWidth(30);
$appSheet->getColumnDimension('AC')->setWidth(20);

// Get applications data
$applications = $conn->query("SELECT * FROM degree_applications ORDER BY id DESC");
$row = 2;

while ($app = $applications->fetch_assoc()) {
    $fullName = $app['first_name'] . ' ' . ($app['middle_name'] ? $app['middle_name'] . ' ' : '') . $app['last_name'];
    
    $appSheet->setCellValue('A'.$row, $app['id']);
    $appSheet->setCellValue('B'.$row, $app['post_applied_for']);
    $appSheet->setCellValue('C'.$row, $app['title']);
    $appSheet->setCellValue('D'.$row, $fullName);
    $appSheet->setCellValue('E'.$row, $app['email']);
    $appSheet->setCellValue('F'.$row, $app['mobile_no']);
    $appSheet->setCellValue('G'.$row, $app['dob']);
    $appSheet->setCellValue('H'.$row, $app['gender']);
    $appSheet->setCellValue('I'.$row, $app['marital_status']);
    $appSheet->setCellValue('J'.$row, $app['aadhar_no']);
    $appSheet->setCellValue('K'.$row, $app['pan_no']);
    $appSheet->setCellValue('L'.$row, $app['address']);
    $appSheet->setCellValue('M'.$row, $app['state']);
    $appSheet->setCellValue('N'.$row, $app['city']);
    $appSheet->setCellValue('O'.$row, $app['pincode']);
    $appSheet->setCellValue('P'.$row, $app['institute_applied_to']);
    $appSheet->setCellValue('Q'.$row, $app['net_status']);
    $appSheet->setCellValue('R'.$row, $app['net_year']);
    $appSheet->setCellValue('S'.$row, $app['set_status']);
    $appSheet->setCellValue('T'.$row, $app['set_year']);
    $appSheet->setCellValue('U'.$row, $app['current_salary']);
    $appSheet->setCellValue('V'.$row, $app['expected_salary']);
    $appSheet->setCellValue('W'.$row, $app['scopus_publications']);
    $appSheet->setCellValue('X'.$row, $app['scopus_id']);
    $appSheet->setCellValue('Y'.$row, $app['conference_presented']);
    $appSheet->setCellValue('Z'.$row, $app['approved_papers']);
    $appSheet->setCellValue('AA'.$row, $app['reference_name']);
    $appSheet->setCellValue('AB'.$row, $app['extracurricular']);
    
    // Hyperlink for resume
    if (!empty($app['resume_filename'])) {
        $resumeURL = "http://localhost:8888/degree-form/resumes/" . $app['resume_filename'];
        $appSheet->setCellValueExplicit(
            'AC'.$row,
            '=HYPERLINK("' . $resumeURL . '", "Download Resume")',
            DataType::TYPE_FORMULA
        );
        $appSheet->getStyle('AC'.$row)->applyFromArray([
            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single']
        ]);
    } else {
        $appSheet->setCellValue('AC'.$row, 'No Resume');
    }

    // Apply row styling
    $appSheet->getStyle('A'.$row.':AC'.$row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// ========== SHEET 2: Qualifications ==========
$qualSheet = $spreadsheet->createSheet();
$qualSheet->setTitle('Qualifications');

$qualHeaders = [
    'App ID', 'Degree', 'Degree Name', 'Education Mode', 'University', 
    'Specialization', 'Year of Passing', 'Percentage', 'CGPA'
];
$qualSheet->fromArray($qualHeaders, NULL, 'A1');
$qualSheet->getStyle('A1:I1')->applyFromArray($headerStyle);

// Set column widths
$qualSheet->getColumnDimension('A')->setWidth(8);
$qualSheet->getColumnDimension('B')->setWidth(15);
$qualSheet->getColumnDimension('C')->setWidth(20);
$qualSheet->getColumnDimension('D')->setWidth(15);
$qualSheet->getColumnDimension('E')->setWidth(25);
$qualSheet->getColumnDimension('F')->setWidth(20);
$qualSheet->getColumnDimension('G')->setWidth(12);
$qualSheet->getColumnDimension('H')->setWidth(12);
$qualSheet->getColumnDimension('I')->setWidth(10);

// Get qualifications data
$qualifications = $conn->query("
    SELECT q.*, a.first_name, a.last_name 
    FROM degree_qualifications q
    JOIN degree_applications a ON q.application_id = a.id
    ORDER BY q.application_id, q.year_of_passing DESC
");

$row = 2;
while ($qual = $qualifications->fetch_assoc()) {
    $qualSheet->setCellValue('A'.$row, $qual['application_id']);
    $qualSheet->setCellValue('B'.$row, $qual['degree']);
    $qualSheet->setCellValue('C'.$row, $qual['degree_name']);
    $qualSheet->setCellValue('D'.$row, $qual['education_mode']);
    $qualSheet->setCellValue('E'.$row, $qual['university']);
    $qualSheet->setCellValue('F'.$row, $qual['specialization']);
    $qualSheet->setCellValue('G'.$row, $qual['year_of_passing']);
    $qualSheet->setCellValue('H'.$row, $qual['percentage']);
    $qualSheet->setCellValue('I'.$row, $qual['cgpa']);
    
    $qualSheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// ========== SHEET 3: PhD Details ==========
$phdSheet = $spreadsheet->createSheet();
$phdSheet->setTitle('PhD Details');

$phdHeaders = ['App ID', 'Status', 'University/Institute', 'Year of Passing'];
$phdSheet->fromArray($phdHeaders, NULL, 'A1');
$phdSheet->getStyle('A1:D1')->applyFromArray($headerStyle);

// Set column widths
$phdSheet->getColumnDimension('A')->setWidth(8);
$phdSheet->getColumnDimension('B')->setWidth(15);
$phdSheet->getColumnDimension('C')->setWidth(30);
$phdSheet->getColumnDimension('D')->setWidth(12);

// Get PhD data
$phdData = $conn->query("
    SELECT p.*, a.first_name, a.last_name 
    FROM degree_phd_details p
    JOIN degree_applications a ON p.application_id = a.id
    ORDER BY p.application_id
");

$row = 2;
while ($phd = $phdData->fetch_assoc()) {
    $phdSheet->setCellValue('A'.$row, $phd['application_id']);
    $phdSheet->setCellValue('B'.$row, $phd['status']);
    $phdSheet->setCellValue('C'.$row, $phd['university_institute']);
    $phdSheet->setCellValue('D'.$row, $phd['year_of_passing']);
    
    $phdSheet->getStyle('A'.$row.':D'.$row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// ========== SHEET 4: Work Experience ==========
$expSheet = $spreadsheet->createSheet();
$expSheet->setTitle('Experience');

$expHeaders = [
    'App ID', 'Organization/University', 'Designation/Post', 
    'From Date', 'To Date', 'Currently Working', 'Salary'
];
$expSheet->fromArray($expHeaders, NULL, 'A1');
$expSheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Set column widths
$expSheet->getColumnDimension('A')->setWidth(8);
$expSheet->getColumnDimension('B')->setWidth(30);
$expSheet->getColumnDimension('C')->setWidth(25);
$expSheet->getColumnDimension('D')->setWidth(12);
$expSheet->getColumnDimension('E')->setWidth(12);
$expSheet->getColumnDimension('F')->setWidth(15);
$expSheet->getColumnDimension('G')->setWidth(15);

// Get experience data
$experiences = $conn->query("
    SELECT e.*, a.first_name, a.last_name 
    FROM degree_work_experience e
    JOIN degree_applications a ON e.application_id = a.id
    ORDER BY e.application_id, e.from_date DESC
");

$row = 2;
while ($exp = $experiences->fetch_assoc()) {
    $expSheet->setCellValue('A'.$row, $exp['application_id']);
    $expSheet->setCellValue('B'.$row, $exp['organization_university']);
    $expSheet->setCellValue('C'.$row, $exp['designation_post']);
    $expSheet->setCellValue('D'.$row, $exp['from_date']);
    $expSheet->setCellValue('E'.$row, $exp['to_date'] ?? 'Present');
    $expSheet->setCellValue('F'.$row, $exp['currently_working'] ? 'Yes' : 'No');
    $expSheet->setCellValue('G'.$row, $exp['salary']);
    
    $expSheet->getStyle('A'.$row.':G'.$row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// ========== SHEET 5: Courses Taught ==========
$courseSheet = $spreadsheet->createSheet();
$courseSheet->setTitle('Courses Taught');

$courseHeaders = [
    'App ID', 'College Name', 'Class Name', 'Subject Name', 
    'Years Experience', 'From Date', 'To Date', 'Department Type',
    'Contract Type', 'Last Salary', 'Approved By University',
    'Letter Number', 'Letter Date'
];
$courseSheet->fromArray($courseHeaders, NULL, 'A1');
$courseSheet->getStyle('A1:M1')->applyFromArray($headerStyle);

// Set column widths
$courseSheet->getColumnDimension('A')->setWidth(8);
$courseSheet->getColumnDimension('B')->setWidth(25);
$courseSheet->getColumnDimension('C')->setWidth(20);
$courseSheet->getColumnDimension('D')->setWidth(25);
$courseSheet->getColumnDimension('E')->setWidth(12);
$courseSheet->getColumnDimension('F')->setWidth(12);
$courseSheet->getColumnDimension('G')->setWidth(12);
$courseSheet->getColumnDimension('H')->setWidth(15);
$courseSheet->getColumnDimension('I')->setWidth(15);
$courseSheet->getColumnDimension('J')->setWidth(15);
$courseSheet->getColumnDimension('K')->setWidth(20);
$courseSheet->getColumnDimension('L')->setWidth(15);
$courseSheet->getColumnDimension('M')->setWidth(12);

// Get courses data
$courses = $conn->query("
    SELECT c.*, a.first_name, a.last_name 
    FROM degree_courses_taught c
    JOIN degree_applications a ON c.application_id = a.id
    ORDER BY c.application_id, c.from_date DESC
");

$row = 2;
while ($course = $courses->fetch_assoc()) {
    $courseSheet->setCellValue('A'.$row, $course['application_id']);
    $courseSheet->setCellValue('B'.$row, $course['college_name']);
    $courseSheet->setCellValue('C'.$row, $course['class_name']);
    $courseSheet->setCellValue('D'.$row, $course['subject_name']);
    $courseSheet->setCellValue('E'.$row, $course['years_experience']);
    $courseSheet->setCellValue('F'.$row, $course['from_date']);
    $courseSheet->setCellValue('G'.$row, $course['to_date'] ?? 'Present');
    $courseSheet->setCellValue('H'.$row, $course['department_type']);
    $courseSheet->setCellValue('I'.$row, $course['contract_type']);
    $courseSheet->setCellValue('J'.$row, $course['last_salary']);
    $courseSheet->setCellValue('K'.$row, $course['approved_by_university']);
    $courseSheet->setCellValue('L'.$row, $course['letter_number']);
    $courseSheet->setCellValue('M'.$row, $course['letter_date']);
    
    $courseSheet->getStyle('A'.$row.':M'.$row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// ========== SHEET 6: Research Publications ==========
$researchSheet = $spreadsheet->createSheet();
$researchSheet->setTitle('Research');

$researchHeaders = [
    'App ID', 'Title', 'Journal Name', 'Year of Publication'
];
$researchSheet->fromArray($researchHeaders, NULL, 'A1');
$researchSheet->getStyle('A1:D1')->applyFromArray($headerStyle);

// Set column widths
$researchSheet->getColumnDimension('A')->setWidth(8);
$researchSheet->getColumnDimension('B')->setWidth(40);
$researchSheet->getColumnDimension('C')->setWidth(30);
$researchSheet->getColumnDimension('D')->setWidth(12);

// Get research data
$research = $conn->query("
    SELECT r.*, a.first_name, a.last_name 
    FROM degree_research_publications r
    JOIN degree_applications a ON r.application_id = a.id
    ORDER BY r.application_id, r.year_of_publication DESC
");

$row = 2;
while ($pub = $research->fetch_assoc()) {
    $researchSheet->setCellValue('A'.$row, $pub['application_id']);
    $researchSheet->setCellValue('B'.$row, $pub['title']);
    $researchSheet->setCellValue('C'.$row, $pub['journal_name']);
    $researchSheet->setCellValue('D'.$row, $pub['year_of_publication']);
    
    $researchSheet->getStyle('A'.$row.':D'.$row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// ========== SHEET 7: Awards ==========
$awardSheet = $spreadsheet->createSheet();
$awardSheet->setTitle('Awards');

$awardHeaders = [
    'App ID', 'Title', 'Organization Name', 'Nature of Award', 'Recognition'
];
$awardSheet->fromArray($awardHeaders, NULL, 'A1');
$awardSheet->getStyle('A1:E1')->applyFromArray($headerStyle);

// Set column widths
$awardSheet->getColumnDimension('A')->setWidth(8);
$awardSheet->getColumnDimension('B')->setWidth(30);
$awardSheet->getColumnDimension('C')->setWidth(30);
$awardSheet->getColumnDimension('D')->setWidth(25);
$awardSheet->getColumnDimension('E')->setWidth(25);

// Get awards data
$awards = $conn->query("
    SELECT aw.*, a.first_name, a.last_name 
    FROM degree_awards aw
    JOIN degree_applications a ON aw.application_id = a.id
    ORDER BY aw.application_id
");

$row = 2;
while ($award = $awards->fetch_assoc()) {
    $awardSheet->setCellValue('A'.$row, $award['application_id']);
    $awardSheet->setCellValue('B'.$row, $award['title']);
    $awardSheet->setCellValue('C'.$row, $award['organization_name']);
    $awardSheet->setCellValue('D'.$row, $award['nature_of_award']);
    $awardSheet->setCellValue('E'.$row, $award['recognition']);
    
    $awardSheet->getStyle('A'.$row.':E'.$row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// ========== Final Output ==========
$conn->close();

$filename = "Degree_Applications_Export_" . date('Y-m-d') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>