<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Database connection
$conn = new mysqli('127.0.0.1', 'root', '1234', 'form_data', 8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    $spreadsheet = new Spreadsheet();

    // ========== SHEET 1: Main Application ==========
    $appSheet = $spreadsheet->getActiveSheet();
    $appSheet->setTitle('Main Application');

    // Header style
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ];

    // Headers matching submit.php fields
    $applicationHeaders = [
        "ID", "Post Applied For", "First Name", "Middle Name", "Last Name", 
        "Date of Birth", "Gender", "Marital Status", "Email", "Alternate Email",
        "Caste", "Aadhar", "PAN", "State", "City", "Address", "PinCode",
        "Mobile", "Alternate Mobile", "Institute Applied To", "Current Salary", 
        "Expected Salary", "Extra Curricular", "Reference Name", "Reference Applied For",
        "Resume File", "PhD Status", "PhD University", "PhD Year", "BEd University", 
        "BEd Year", "College Name", "Class Name", "Subject Name", "Years Experience",
        "Courses From Date", "Courses To Date", "Department Type", "Contract Type",
        "Last Salary", "Approved By University", "Letter Number", "Letter Date"
    ];
    
    $appSheet->fromArray($applicationHeaders, NULL, 'A1');
    $appSheet->getStyle('A1:AP1')->applyFromArray($headerStyle);

    // Fetch data from applications table (matches submit.php)
    $applications = $conn->query("SELECT * FROM applications");
    $row = 2;

    while ($app = $applications->fetch_assoc()) {
        $data = [
            $app['id'],
            $app['post_applied_for'],
            $app['first_name'],
            $app['middle_name'],
            $app['last_name'],
            $app['dob'],
            $app['gender'],
            $app['marital_status'],
            $app['email'],
            $app['alternate_email'],
            $app['caste'],
            $app['aadhar'],
            $app['pan'],
            $app['state'],
            $app['city'],
            $app['address'],
            $app['pincode'],
            $app['mobile'],
            $app['alternate_mobile'],
            $app['institute_applied_to'],
            $app['current_salary'],
            $app['expected_salary'],
            $app['extra_curricular'],
            $app['reference_name'],
            $app['reference_applied_for'],
            $app['resume_filename'],
            $app['phd_status'],
            $app['phd_university'],
            $app['phd_year'],
            $app['bed_university'],
            $app['bed_year'],
            $app['college_name'],
            $app['class_name'],
            $app['subject_name'],
            $app['years_experience'],
            $app['courses_from_date'],
            $app['courses_to_date'],
            $app['department_type'],
            $app['contract_type'],
            $app['last_salary'],
            $app['approved_by_university'],
            $app['letter_number'],
            $app['letter_date']
        ];

        $appSheet->fromArray($data, NULL, 'A'.$row);

        // Resume hyperlink
        if (!empty($app['resume_filename'])) {
            $resumeURL = "http://localhost/resumes/" . $app['resume_filename'];
            $appSheet->setCellValue('Z'.$row, '=HYPERLINK("'.$resumeURL.'", "Download")');
            $appSheet->getStyle('Z'.$row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE));
        }
        $row++;
    }

    // ========== SHEET 2: Qualifications ==========
    $qualSheet = $spreadsheet->createSheet();
    $qualSheet->setTitle('Qualifications');
    $qualHeaders = [
        'Application ID', 'Degree', 'Degree Name', 'Education Mode', 
        'University Name', 'Specialization', 'Year of Passing', 
        'Percentage', 'CGPA'
    ];
    $qualSheet->fromArray($qualHeaders, NULL, 'A1');
    $qualSheet->getStyle('A1:I1')->applyFromArray($headerStyle);
    
    $qualifications = $conn->query("SELECT * FROM qualifications");
    $row = 2;
    while ($qual = $qualifications->fetch_assoc()) {
        $qualSheet->fromArray(array_values($qual), NULL, "A$row");
        $row++;
    }

    // ========== SHEET 3: Work Experience ==========
    $expSheet = $spreadsheet->createSheet();
    $expSheet->setTitle('Work Experience');
    $expHeaders = [
        'Application ID', 'Organization', 'Designation', 
        'From Date', 'To Date', 'Current Salary', 'Currently Working'
    ];
    $expSheet->fromArray($expHeaders, NULL, 'A1');
    $expSheet->getStyle('A1:G1')->applyFromArray($headerStyle);
    
    $experiences = $conn->query("SELECT * FROM work_experience");
    $row = 2;
    while ($exp = $experiences->fetch_assoc()) {
        $expData = [
            $exp['application_id'],
            $exp['organization'],
            $exp['designation'],
            $exp['from_date'],
            $exp['to_date'],
            $exp['current_salary'],
            $exp['currently_working'] ? 'Yes' : 'No'
        ];
        $expSheet->fromArray($expData, NULL, "A$row");
        $row++;
    }

    // ========== SHEET 4: Research Publications ==========
    $researchSheet = $spreadsheet->createSheet();
    $researchSheet->setTitle('Research Publications');
    $researchHeaders = [
        'Application ID', 'Scopus Publications', 'Scopus ID', 
        'Conference Presented', 'Paper Title', 'Journal Name',
        'Publication Year', 'Approved Papers'
    ];
    $researchSheet->fromArray($researchHeaders, NULL, 'A1');
    $researchSheet->getStyle('A1:H1')->applyFromArray($headerStyle);
    
    $publications = $conn->query("SELECT * FROM research_publications");
    $row = 2;
    while ($pub = $publications->fetch_assoc()) {
        $pubData = [
            $pub['application_id'],
            $pub['scopus_publications'],
            $pub['scopus_id'],
            $pub['conference_presented'],
            $pub['paper_title'],
            $pub['journal_name'],
            $pub['publication_year'],
            $pub['approved_papers']
        ];
        $researchSheet->fromArray($pubData, NULL, "A$row");
        $row++;
    }

   

// ========== SHEET 5: Courses Taught ==========
$coursesSheet = $spreadsheet->createSheet();
$coursesSheet->setTitle('Courses Taught');
$coursesHeaders = [
    'Application ID', 'College Name', 'Class Name', 'Subject Name', 
    'Years of Experience', 'From Date', 'To Date', 'Department Type',
    'Contract Type', 'Last Salary', 'Approved By University',
    'Letter Number', 'Letter Date'
];
$coursesSheet->fromArray($coursesHeaders, NULL, 'A1');
$coursesSheet->getStyle('A1:M1')->applyFromArray($headerStyle);

// Set column widths
$coursesSheet->getColumnDimension('A')->setWidth(12);
$coursesSheet->getColumnDimension('B')->setWidth(25);
$coursesSheet->getColumnDimension('C')->setWidth(15);
$coursesSheet->getColumnDimension('D')->setWidth(25);
$coursesSheet->getColumnDimension('E')->setWidth(15);
$coursesSheet->getColumnDimension('F')->setWidth(12);
$coursesSheet->getColumnDimension('G')->setWidth(12);
$coursesSheet->getColumnDimension('H')->setWidth(20);
$coursesSheet->getColumnDimension('I')->setWidth(15);
$coursesSheet->getColumnDimension('J')->setWidth(15);
$coursesSheet->getColumnDimension('K')->setWidth(25);
$coursesSheet->getColumnDimension('L')->setWidth(15);
$coursesSheet->getColumnDimension('M')->setWidth(12);

$courses = $conn->query("SELECT * FROM courses_taught");
$row = 2;
while ($course = $courses->fetch_assoc()) {
    $courseData = [
        $course['application_id'],
        $course['college_name'],
        $course['class_name'],
        $course['subject_name'],
        $course['years_experience'],
        $course['from_date'],
        $course['to_date'],
        $course['department_type'],
        $course['contract_type'],
        $course['last_salary'],
        $course['approved_by_university'],
        $course['letter_number'],
        $course['letter_date']
    ];
    $coursesSheet->fromArray($courseData, NULL, "A$row");
    $row++;
}

// ... [rest of the export code remains the same] ...

    // ========== SHEET 6: Awards ==========
    $awardsSheet = $spreadsheet->createSheet();
    $awardsSheet->setTitle('Awards');
    $awardsHeaders = [
        'Application ID', 'Award Title', 'Award Organization', 
        'Award Nature', 'Award Salary'
    ];
    $awardsSheet->fromArray($awardsHeaders, NULL, 'A1');
    $awardsSheet->getStyle('A1:E1')->applyFromArray($headerStyle);
    
    $awards = $conn->query("SELECT * FROM awards");
    $row = 2;
    while ($award = $awards->fetch_assoc()) {
        $awardsSheet->fromArray(array_values($award), NULL, "A$row");
        $row++;
    }

    // ========== Output ==========
    $filename = "applications_export_" . date('Y-m-d_His') . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    die("Error generating Excel: " . $e->getMessage());
} 