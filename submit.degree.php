<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('127.0.0.1', 'root', '1234', 'form_data', 8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle resume upload
$resume_filename = "";
if (isset($_FILES['resumeUpload'])) {
    $targetDir = "resumes/";
    $fileExt = strtolower(pathinfo($_FILES["resumeUpload"]["name"], PATHINFO_EXTENSION));
    if ($fileExt !== 'pdf') {
        die("Only PDF files are allowed.");
    }
    $resume_filename = uniqid() . "_" . basename($_FILES["resumeUpload"]["name"]);
    $targetFilePath = $targetDir . $resume_filename;
    if (!move_uploaded_file($_FILES["resumeUpload"]["tmp_name"], $targetFilePath)) {
        die("Failed to upload resume.");
    }
}

// Get and sanitize main form values
$post_applied_for = $_POST['post_applied_for'] ?? '';
$title = $_POST['title'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$middle_name = $_POST['middle_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$dob = $_POST['dob'] ?? '';
$age = isset($_POST['age']) && is_numeric($_POST['age']) ? intval($_POST['age']) : 0;
$gender = $_POST['gender'] ?? '';
$marital_status = $_POST['marital_status'] ?? '';
$email = $_POST['email'] ?? '';
$alternate_email = $_POST['alternate_email'] ?? '';
$caste_subcaste = $_POST['caste_subcaste'] ?? '';
$aadhar_no = $_POST['aadhar_no'] ?? '';
$pan_no = $_POST['pan_no'] ?? '';
$state = $_POST['state'] ?? '';
$city = $_POST['city'] ?? '';
$address = $_POST['address'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$mobile_no = $_POST['mobile_no'] ?? '';
$alternate_mobile_no = $_POST['alternate_mobile_no'] ?? '';
$institute_applied_to = $_POST['institute_applied_to'] ?? '';
$current_salary = isset($_POST['current_salary']) && is_numeric($_POST['current_salary']) ? floatval($_POST['current_salary']) : 0;
$expected_salary = isset($_POST['expected_salary']) && is_numeric($_POST['expected_salary']) ? floatval($_POST['expected_salary']) : 0;
$scopus_publications = isset($_POST['scopus_publications']) && is_numeric($_POST['scopus_publications']) ? intval($_POST['scopus_publications']) : 0;
$scopus_id = $_POST['scopus_id'] ?? '';
$conference_presented_main = isset($_POST['conference_presented_main'])
    ? (is_array($_POST['conference_presented_main']) 
        ? implode(",", $_POST['conference_presented_main']) 
        : $_POST['conference_presented_main'])
    : '';
$approved_papers = isset($_POST['approved_papers']) && is_numeric($_POST['approved_papers']) ? intval($_POST['approved_papers']) : 0;
$reference_name = $_POST['reference_name'] ?? '';
$applied_for_position = $_POST['applied_for_position'] ?? '';
$extra_curricular = $_POST['extra_curricular'] ?? '';
$net_status = $_POST['net_status'] ?? 'No';
$net_year = isset($_POST['net_year']) && is_numeric($_POST['net_year']) ? intval($_POST['net_year']) : null;
$set_status = $_POST['set_status'] ?? 'No';
$set_year = isset($_POST['set_year']) && is_numeric($_POST['set_year']) ? intval($_POST['set_year']) : null;
$declaration_accepted = isset($_POST['declaration_accepted']) ? 1 : 0;

// Insert into degree_applications
$stmt = $conn->prepare("INSERT INTO degree_applications (
    post_applied_for, title, first_name, middle_name, last_name, dob, age, gender, marital_status,
    email, alternate_email, caste_subcaste, aadhar_no, pan_no, state, city, address, pincode,
    mobile_no, alternate_mobile_no, institute_applied_to, current_salary, expected_salary,
    scopus_publications, scopus_id, conference_presented, approved_papers, reference_name,
    applied_for_position, extracurricular, resume_filename, net_status, net_year, set_status, set_year,
    declaration_accepted
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}


$conference_presented_main = isset($_POST['conference_presented_main'])
    ? (is_array($_POST['conference_presented_main']) 
        ? implode(",", $_POST['conference_presented_main']) 
        : $_POST['conference_presented_main'])
    : '';

// 36 values → 36 types in bind_param
$stmt->bind_param(
    "ssssssssssssssssssssddssssssssssssi",
    $post_applied_for, $title, $first_name, $middle_name, $last_name, $dob, $age, $gender, $marital_status,
    $email, $alternate_email, $caste_subcaste, $aadhar_no, $pan_no, $state, $city, $address, $pincode,
    $mobile_no, $alternate_mobile_no, $institute_applied_to, $current_salary, $expected_salary,
    $scopus_publications, $scopus_id, $conference_presented_main, $approved_papers, $reference_name,
    $applied_for_position, $extra_curricular, $resume_filename, $net_status, $net_year, $set_status, $set_year,
    $declaration_accepted
);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$application_id = $conn->insert_id;
$stmt->close();


// === Qualifications ===
if (isset($_POST['degree']) && is_array($_POST['degree'])) {
    foreach ($_POST['degree'] as $i => $val) {
        if (!empty($val)) {
            $stmt = $conn->prepare("INSERT INTO degree_qualifications (
                application_id, degree, degree_name, education_mode, university, 
                specialization, year_of_passing, percentage, cgpa
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param("issssssss",
                $application_id,
                $_POST['degree'][$i],
                $_POST['degree_name'][$i] ?? '',
                $_POST['education_mode'][$i] ?? '',
                $_POST['university_name'][$i] ?? '',
                $_POST['specialization'][$i] ?? '',
                $_POST['year_of_passing'][$i] ?? '',
                $_POST['percentage'][$i] ?? '',
                $_POST['cgpa'][$i] ?? ''
            );
            $stmt->execute();
            $stmt->close();
        }
    }
}

// === Work Experience ===
if (isset($_POST['organization']) && is_array($_POST['organization'])) {
    foreach ($_POST['organization'] as $i => $val) {
        if (!empty($val)) {
            $stmt = $conn->prepare("INSERT INTO degree_work_experience (
                application_id, organization_university, designation_post, from_date, to_date, 
                salary, currently_working
            ) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $currently_working = isset($_POST['currently_working'][$i]) ? 1 : 0;
            $stmt->bind_param("isssssi",
                $application_id,
                $_POST['organization'][$i],
                $_POST['designation'][$i] ?? '',
                $_POST['from_date'][$i] ?? '',
                $_POST['to_date'][$i] ?? '',
                $_POST['salary'][$i] ?? '',
                $currently_working
            );
            $stmt->execute();
            $stmt->close();
        }
    }
}

// === Research Publications ===
if (isset($_POST['paper_title']) && is_array($_POST['paper_title'])) {
    foreach ($_POST['paper_title'] as $i => $val) {
        if (!empty($val)) {
            $stmt = $conn->prepare("INSERT INTO degree_research_publications (
                application_id, title, journal_name, year_of_publication,
                scopus_publications, scopus_id, conference_presented, approved_papers
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param("isssisss",
                $application_id,
                $_POST['paper_title'][$i],
                $_POST['journal_name'][$i] ?? '',
                $_POST['year_of_publication'][$i] ?? '',
                $_POST['scopus_publications'][$i] ?? 0,
                $_POST['scopus_id'][$i] ?? '',
                $_POST['conference_presented'][$i] ?? '',
                $_POST['approved_papers'][$i] ?? 0
            );
            $stmt->execute();
            $stmt->close();
        }
    }
}

// === PhD Details ===
if (!empty($_POST['phdStatus'])) {
    $stmt = $conn->prepare("INSERT INTO degree_phd_details (
        application_id, status, university_institute, year_of_passing
    ) VALUES (?, ?, ?, ?)");
    
    $stmt->bind_param("isss",
        $application_id,
        $_POST['phdStatus'],
        $_POST['phdUniversity'] ?? '',
        $_POST['phdYear'] ?? ''
    );
    $stmt->execute();
    $stmt->close();
}

// === Courses Taught ===
if (!empty($_POST['collegeName'])) {
    $stmt = $conn->prepare("INSERT INTO degree_courses_taught (
        application_id, college_name, class_name, subject_name, years_experience,
        from_date, to_date, department_type, contract_type, last_salary,
        approved_by_university, letter_number, letter_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("isssissssssss",
        $application_id,
        $_POST['collegeName'],
        $_POST['className'] ?? '',
        $_POST['subjectName'] ?? '',
        $_POST['yearsOfExp'] ?? 0,
        $_POST['fromDateCourse'] ?? '',
        $_POST['toDateCourse'] ?? '',
        $_POST['departmentType'] ?? '',
        $_POST['typeOfContract'] ?? '',
        $_POST['lastSalary'] ?? '',
        $_POST['approvedByUni'] ?? '',
        $_POST['letterNumber'] ?? '',
        $_POST['letterDate'] ?? ''
    );
    $stmt->execute();
    $stmt->close();
}

// === Awards ===
if (!empty($_POST['awardTitle'])) {
    $stmt = $conn->prepare("INSERT INTO degree_awards (
        application_id, title, organization_name, nature_of_award, recognition
    ) VALUES (?, ?, ?, ?, ?)");
    
    $stmt->bind_param("issss",
        $application_id,
        $_POST['awardTitle'],
        $_POST['organizationName'] ?? '',
        $_POST['natureOfAward'] ?? '',
        $_POST['expectedAwardSalary'] ?? ''
    );
    $stmt->execute();
    $stmt->close();
}

$conn->close();
echo "✅ Application submitted successfully.";
?>
