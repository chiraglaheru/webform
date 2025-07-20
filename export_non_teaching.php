<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$conn = new mysqli("localhost", "root", "root", "form_data", 8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$headers = [
    "App ID", "Post Applied For", "Other Post Type", "First Name", "Middle Name", "Last Name", "Email", "Mobile",
    "Address", "Date of Birth", "Mother Tongue", "Other Language", "English Typing Speed", "Marathi Typing Speed",
    "Joining Date", "Expected Salary", "Current Salary", "Comments", "CV File",
    "Qualifications", "Experience"
];
$sheet->fromArray($headers, NULL, 'A1');

// Fetch applications
$sql = "SELECT * FROM non_teaching_applications";
$result = $conn->query($sql);
$rowNum = 2;

while ($app = $result->fetch_assoc()) {
    $app_id = $app['id'];
    
    // Fetch qualifications
    $qualifications = "";
    $qsql = "SELECT * FROM nt_qualifications WHERE application_id = $app_id";
    $qres = $conn->query($qsql);
    while ($q = $qres->fetch_assoc()) {
        $qualifications .= "{$q['degree']} ({$q['university']}, {$q['year']}, {$q['percentage']}%)\n";
    }

    // Fetch experience
    $experience = "";
    $esql = "SELECT * FROM nt_experience WHERE application_id = $app_id";
    $eres = $conn->query($esql);
    while ($e = $eres->fetch_assoc()) {
        $experience .= "{$e['organization']} - {$e['designation']} ({$e['from_date']} to {$e['to_date']})\n";
    }

    // Build row
    $data = [
        $app['id'], $app['post_applied_for'], $app['other_post_type'], $app['first_name'], $app['middle_name'], $app['last_name'],
        $app['email'], $app['mobile'], $app['address'], $app['dob'], $app['mother_tongue'], $app['other_language'],
        $app['english_typing'], $app['marathi_typing'], $app['joining_date'], $app['expected_salary'], $app['current_salary'],
        $app['comments'],
        $app['cv_filename'] ? 'http://localhost:8888/web-form-main/resumes/' . $app['cv_filename'] : '',
        trim($qualifications),
        trim($experience)
    ];

    $sheet->fromArray($data, NULL, "A$rowNum");
    $rowNum++;
}

$filename = "non_teaching_applications_" . date('Y-m-d_H-i-s') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
$conn->close();
exit;
?>
