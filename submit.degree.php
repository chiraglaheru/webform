<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "form_data";

$conn = new mysqli('127.0.0.1', 'root', '1234', 'form_data', 8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->error);
}

// Handle resume upload
$resume_filename = "";
if (isset($_FILES['resumeUpload']) && $_FILES['resumeUpload']['error'] == 0) {
    $targetDir = "resumes/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
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

// Format dates from dd/mm/yyyy to yyyy-mm-dd
function formatDate($date) {
    if (empty($date)) return null;
    $parts = explode('/', $date);
    if (count($parts) === 3) {
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return $date;
}

// Get all form values with proper null checks
$post_applied_for = $_POST['post_applied_for'] ?? '';
$title = $_POST['title'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$middle_name = $_POST['middle_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$dob = formatDate($_POST['dob'] ?? '');
$age = (int)($_POST['age'] ?? 0);
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
$current_salary = (float)($_POST['current_salary'] ?? 0);
$expected_salary = (float)($_POST['expected_salary'] ?? 0);
$scopus_publications = (int)($_POST['scopus_publications'] ?? 0);
$scopus_id = $_POST['scopus_id'] ?? '';
$conference_presented = $_POST['conference_presented'] ?? '';
$approved_papers = (int)($_POST['approved_papers'] ?? 0);
$reference_name = $_POST['reference_name'] ?? '';
$applied_for_position = $_POST['applied_for_position'] ?? '';
$extra_curricular = $_POST['extra_curricular'] ?? '';
$net_status = $_POST['net_status'] ?? 'No';
$net_year = $_POST['net_year'] ?? '';
$set_status = $_POST['set_status'] ?? 'No';
$set_year = $_POST['set_year'] ?? '';
$declaration_accepted = isset($_POST['declaration_accepted']) ? 1 : 0;

// Start transaction
$conn->begin_transaction();

try {
    // Insert main application
    $sql = "INSERT INTO degree_applications (
        post_applied_for, title, first_name, middle_name, last_name, 
        dob, age, gender, marital_status, email, alternate_email, 
        caste_subcaste, aadhar_no, pan_no, state, city, address, 
        pincode, mobile_no, alternate_mobile_no, institute_applied_to,
        current_salary, expected_salary, scopus_publications, scopus_id,
        conference_presented, approved_papers, reference_name, 
        applied_for_position, extracurricular, resume_filename,
        net_status, net_year, set_status, set_year, declaration_accepted
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $bind_result = $stmt->bind_param(
        "sssssssissssssssssssddisissssssssssi",
        $post_applied_for, $title, $first_name, $middle_name, $last_name,
        $dob, $age, $gender, $marital_status, $email, $alternate_email,
        $caste_subcaste, $aadhar_no, $pan_no, $state, $city, $address,
        $pincode, $mobile_no, $alternate_mobile_no, $institute_applied_to,
        $current_salary, $expected_salary, $scopus_publications, $scopus_id,
        $conference_presented, $approved_papers, $reference_name, 
        $applied_for_position, $extra_curricular, $resume_filename,
        $net_status, $net_year, $set_status, $set_year, $declaration_accepted
    );

    if (!$bind_result) {
        throw new Exception("Bind failed: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error inserting application: " . $stmt->error);
    }

    $application_id = $conn->insert_id;
    $stmt->close();

    // Insert qualifications
    if (isset($_POST['degree']) && is_array($_POST['degree'])) {
        foreach ($_POST['degree'] as $i => $degree) {
            if (!empty($degree)) {
                $degree_name = $_POST['degree_name'][$i] ?? '';
                $education_mode = $_POST['education_mode'][$i] ?? '';
                $university_name = $_POST['university_name'][$i] ?? '';
                $specialization = $_POST['specialization'][$i] ?? '';
                $year_of_passing = $_POST['year_of_passing'][$i] ?? '';
                $percentage = $_POST['percentage'][$i] ?? '';
                $cgpa = isset($_POST['cgpa'][$i]) && is_numeric($_POST['cgpa'][$i]) ? $_POST['cgpa'][$i] : null;
                
                $stmt = $conn->prepare("INSERT INTO degree_qualifications (
                    application_id, degree, degree_name, education_mode, university, 
                    specialization, year_of_passing, percentage, cgpa
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt) {
                    $stmt->bind_param("issssssss",
                        $application_id,
                        $degree,
                        $degree_name,
                        $education_mode,
                        $university_name,
                        $specialization,
                        $year_of_passing,
                        $percentage,
                        $cgpa
                    );
                    
                    if (!$stmt->execute()) {
                        error_log("Qualification Error: " . $stmt->error);
                    }
                    $stmt->close();
                }
            }
        }
    }

    // Insert work experience
    if (isset($_POST['organization']) && is_array($_POST['organization'])) {
        foreach ($_POST['organization'] as $i => $organization) {
            if (!empty($organization)) {
                $designation = $_POST['designation'][$i] ?? '';
                $from_date = formatDate($_POST['from_date'][$i] ?? '');
                $to_date = formatDate($_POST['to_date'][$i] ?? '');
                $salary = $_POST['salary'][$i] ?? '';
                $currently_working = isset($_POST['currently_working'][$i]) ? 1 : 0;
                
                $stmt = $conn->prepare("INSERT INTO degree_work_experience (
                    application_id, organization_university, designation_post, from_date, to_date, 
                    salary, currently_working
                ) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt) {
                    $stmt->bind_param(
                        "isssssi",
                        $application_id,
                        $organization,
                        $designation,
                        $from_date,
                        $to_date,
                        $salary,
                        $currently_working
                    );
                    
                    if (!$stmt->execute()) {
                        error_log("Work Experience Error: " . $stmt->error);
                    }
                    $stmt->close();
                }
            }
        }
    }

    // Insert research publications - FINAL WORKING VERSION
    // Insert research publications - GUARANTEED WORKING VERSION
// Insert research publications - FINAL WORKING VERSION
if (isset($_POST['paper_title']) && is_array($_POST['paper_title'])) {
    error_log("Starting research publications insertion for application $application_id");
    
    foreach ($_POST['paper_title'] as $i => $title) {
        $title = trim($title);
        if (empty($title)) {
            error_log("Skipping empty title at index $i");
            continue;
        }

        // Prepare all variables first
        $journal_name = trim($_POST['journal_name'][$i] ?? '');
        $year = substr(trim($_POST['year_of_publication'][$i] ?? ''), 0, 4);
        $scopus_pubs = (int)($_POST['scopus_publications'][$i] ?? 0);
        $scopus_id_val = trim($_POST['scopus_id'][$i] ?? '');
        $conference = trim($_POST['conference_presented'][$i] ?? '');
        $approved = (int)($_POST['approved_papers'][$i] ?? 0);

        // Validate year
        if (!preg_match('/^\d{4}$/', $year)) {
            error_log("Invalid year format at index $i: $year");
            continue;
        }

        // Prepare and execute statement
        $stmt = $conn->prepare("INSERT INTO degree_research_publications (
            application_id, title, journal_name, year_of_publication,
            scopus_publications, scopus_id, conference_presented, approved_papers
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            continue;
        }

        // Bind variables (not literals)
        $stmt->bind_param(
            "isssisss",
            $application_id,
            $title,
            $journal_name,
            $year,
            $scopus_pubs,
            $scopus_id_val,
            $conference,
            $approved
        );

        if (!$stmt->execute()) {
            error_log("Insert failed: " . $stmt->error);
            error_log("Data: " . print_r([
                $application_id,
                $title,
                $journal_name,
                $year,
                $scopus_pubs,
                $scopus_id_val,
                $conference,
                $approved
            ], true));
        } else {
            error_log("Successfully inserted publication #$i");
        }

        $stmt->close();
    }
} else {
    error_log("No research publications data found in POST");
}

    // Insert PhD details
    if (isset($_POST['phdStatus']) && !empty($_POST['phdStatus'])) {
        $phd_status = $_POST['phdStatus'];
        $phd_university = $_POST['phdUniversity'] ?? '';
        $phd_year = $_POST['phdYear'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO degree_phd_details (
            application_id, status, university_institute, year_of_passing
        ) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("isss",
                $application_id,
                $phd_status,
                $phd_university,
                $phd_year
            );
            
            if (!$stmt->execute()) {
                error_log("PhD details error: " . $stmt->error);
            }
            $stmt->close();
        }
    }

    // Insert courses taught
    if (isset($_POST['collegeName']) && !empty($_POST['collegeName'])) {
        $collegeName = $_POST['collegeName'];
        $className = $_POST['className'] ?? '';
        $subjectName = $_POST['subjectName'] ?? '';
        $yearsOfExp = $_POST['yearsOfExp'] ?? '';
        $fromDateCourse = formatDate($_POST['fromDateCourse'] ?? '');
        $toDateCourse = formatDate($_POST['toDateCourse'] ?? '');
        $departmentType = $_POST['departmentType'] ?? '';
        $typeOfContract = $_POST['typeOfContract'] ?? '';
        $lastSalary = $_POST['lastSalary'] ?? '';
        $approvedByUni = $_POST['approvedByUni'] ?? '';
        $letterNumber = $_POST['letterNumber'] ?? '';
        $letterDate = formatDate($_POST['letterDate'] ?? '');
        
        $stmt = $conn->prepare("INSERT INTO degree_courses_taught (
            application_id, college_name, class_name, subject_name, years_experience,
            from_date, to_date, department_type, contract_type, last_salary,
            approved_by_university, letter_number, letter_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param(
                "issssssssssss",
                $application_id,
                $collegeName,
                $className,
                $subjectName,
                $yearsOfExp,
                $fromDateCourse,
                $toDateCourse,
                $departmentType,
                $typeOfContract,
                $lastSalary,
                $approvedByUni,
                $letterNumber,
                $letterDate
            );
            
            if (!$stmt->execute()) {
                error_log("Courses Taught Error: " . $stmt->error);
            }
            $stmt->close();
        }
    }

    // Insert awards
    if (isset($_POST['awardTitle']) && !empty($_POST['awardTitle'])) {
        $awardTitle = $_POST['awardTitle'];
        $organizationName = $_POST['organizationName'] ?? '';
        $natureOfAward = $_POST['natureOfAward'] ?? '';
        $expectedAwardSalary = $_POST['expectedAwardSalary'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO degree_awards (
            application_id, title, organization_name, nature_of_award, recognition
        ) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param(
                "issss",
                $application_id,
                $awardTitle,
                $organizationName,
                $natureOfAward,
                $expectedAwardSalary
            );
            
            if (!$stmt->execute()) {
                error_log("Award Error: " . $stmt->error);
            }
            $stmt->close();
        }
    }

    // Commit transaction if all inserts succeeded
    $conn->commit();
    
    // Redirect to success page
    header("Location: application_success.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
} finally {
    $conn->close();
}
?>