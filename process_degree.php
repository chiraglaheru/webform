<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "root";
$database = "degree_applications";
$port = 8889;

// Start session for error messages
session_start();

// CSRF protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: degree.html");
    exit();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    $_SESSION['error'] = "Database connection failed: " . $conn->connect_error;
    header("Location: degree.html");
    exit();
}

// Function to safely get POST values
function getPostValue($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// Function to validate required fields
function validateRequiredFields($fields) {
    $errors = [];
    foreach ($fields as $field => $label) {
        if (empty(getPostValue($field))) {
            $errors[] = "$label is required";
        }
    }
    return $errors;
}

// Required fields with labels for error messages
$requiredFields = [
    'postApplied' => 'Position Applied For',
    'title' => 'Title',
    'firstName' => 'First Name',
    'lastName' => 'Last Name',
    'dob' => 'Date of Birth',
    'gender' => 'Gender',
    'maritalStatus' => 'Marital Status',
    'email' => 'Email',
    'caste' => 'Caste/Subcaste',
    'aadhar' => 'Aadhar Number',
    'pan' => 'PAN Number',
    'state' => 'State',
    'city' => 'City',
    'address' => 'Address',
    'pinCode' => 'PIN Code',
    'mobile' => 'Mobile Number',
    'expectedSalary' => 'Expected Salary'
];

// Validate required fields
$validationErrors = validateRequiredFields($requiredFields);

// Additional validations
if (!filter_var(getPostValue('email'), FILTER_VALIDATE_EMAIL)) {
    $validationErrors[] = "Invalid email format";
}

if (!preg_match('/^[2-9]{1}[0-9]{11}$/', getPostValue('aadhar'))) {
    $validationErrors[] = "Invalid Aadhar number";
}

if (!preg_match('/[A-Z]{5}[0-9]{4}[A-Z]{1}/', getPostValue('pan'))) {
    $validationErrors[] = "Invalid PAN number";
}

if (!empty($validationErrors)) {
    $_SESSION['form_errors'] = $validationErrors;
    $_SESSION['form_data'] = $_POST;
    header("Location: degree.html");
    exit();
}

// Handle file upload
$resumePath = '';
if (isset($_FILES['resumeUpload']) && $_FILES['resumeUpload']['error'] === UPLOAD_ERR_OK) {
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    if ($_FILES['resumeUpload']['size'] > $maxFileSize) {
        $_SESSION['error'] = "File size exceeds 5MB limit";
        header("Location: degree.html");
        exit();
    }
    
    $targetDir = "resumes/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $allowedTypes = ['application/pdf'];
    $fileType = mime_content_type($_FILES['resumeUpload']['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error'] = "Only PDF documents are allowed";
        header("Location: degree.html");
        exit();
    }
    
    $fileExtension = pathinfo($_FILES['resumeUpload']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExtension;
    $targetFile = $targetDir . $fileName;
    
    if (!move_uploaded_file($_FILES['resumeUpload']['tmp_name'], $targetFile)) {
        $_SESSION['error'] = "Failed to upload resume";
        header("Location: degree.html");
        exit();
    }
    
    $resumePath = $fileName;
}

// Get all form values with proper sanitization
$postApplied = getPostValue('postApplied');
$title = getPostValue('title');
$firstName = getPostValue('firstName');
$middleName = getPostValue('middleName');
$lastName = getPostValue('lastName');
$dob = getPostValue('dob');
$age = getPostValue('age', 0);
$gender = getPostValue('gender');
$maritalStatus = getPostValue('maritalStatus');
$email = filter_var(getPostValue('email'), FILTER_SANITIZE_EMAIL);
$altEmail = filter_var(getPostValue('altEmail'), FILTER_SANITIZE_EMAIL);
$caste = getPostValue('caste');
$aadhar = getPostValue('aadhar');
$pan = getPostValue('pan');
$state = getPostValue('state');
$city = getPostValue('city');
$address = getPostValue('address');
$pinCode = getPostValue('pinCode');
$mobile = getPostValue('mobile');
$altMobile = getPostValue('altMobile');
$institute = getPostValue('institute');
$netStatus = getPostValue('netStatus');
$netYear = getPostValue('netYear');
$setStatus = getPostValue('setStatus');
$setYear = getPostValue('setYear');
$currentSalary = floatval(getPostValue('currentSalary', 0));
$expectedSalary = floatval(getPostValue('expectedSalary', 0));
$scopusPublications = intval(getPostValue('scopusPublications', 0));
$scopusId = getPostValue('scopusId');
$conferencePresented = getPostValue('conferencePresented');
$approvedPapers = intval(getPostValue('approvedPapers', 0));
$referenceName = getPostValue('referenceName');
$appliedFor = getPostValue('appliedFor');
$extraCurricular = getPostValue('extraCurricular');
$declarationAccepted = isset($_POST['declaration']) ? 1 : 0;
$createdAt = date('Y-m-d H:i:s');

// Start transaction
$conn->begin_transaction();

try {
    // Insert main application data
    $sql = "INSERT INTO `degree_applications` (
        post_applied_for, title, first_name, middle_name, last_name, 
        dob, age, gender, marital_status, email, 
        alternate_email, caste_subcaste, aadhar_no, pan_no, state, 
        city, address, pincode, mobile_no, alternate_mobile_no, 
        institute_applied_to, net_status, net_year, set_status, set_year,
        current_salary, expected_salary, scopus_publications, scopus_id, conference_presented,
        approved_papers, reference_name, applied_for_position, extracurricular, resume_filename,
        declaration_accepted, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssssisssssssssssssssssdisisississsss",
        $postApplied,
        $title,
        $firstName,
        $middleName,
        $lastName,
        $dob,
        $age,
        $gender,
        $maritalStatus,
        $email,
        $altEmail,
        $caste,
        $aadhar,
        $pan,
        $state,
        $city,
        $address,
        $pinCode,
        $mobile,
        $altMobile,
        $institute,
        $netStatus,
        $netYear,
        $setStatus,
        $setYear,
        $currentSalary,
        $expectedSalary,
        $scopusPublications,
        $scopusId,
        $conferencePresented,
        $approvedPapers,
        $referenceName,
        $appliedFor,
        $extraCurricular,
        $resumePath,
        $declarationAccepted,
        $createdAt
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $applicationId = $stmt->insert_id;
    $stmt->close();

    // Handle qualifications
    if (isset($_POST['degree'])) {
        $qualStmt = $conn->prepare("INSERT INTO degree_qualifications (
            application_id, degree, degree_name, education_mode, university, specialization,
            year_of_passing, percentage, cgpa
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$qualStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        foreach ($_POST['degree'] as $index => $degree) {
            $degreeName = getPostValue("degree_name[$index]");
            $educationMode = getPostValue("education_mode[$index]");
            $university = getPostValue("university_name[$index]");
            $specialization = getPostValue("specialization[$index]");
            $yearOfPassing = getPostValue("year_of_passing[$index]");
            $percentage = floatval(getPostValue("percentage[$index]", 0));
            $cgpa = floatval(getPostValue("cgpa[$index]", 0));
            
            $qualStmt->bind_param("isssssidd",
                $applicationId,
                $degree,
                $degreeName,
                $educationMode,
                $university,
                $specialization,
                $yearOfPassing,
                $percentage,
                $cgpa
            );
            
            if (!$qualStmt->execute()) {
                throw new Exception("Qualification insert failed: " . $qualStmt->error);
            }
        }
        $qualStmt->close();
    }

    // Handle PhD details
    if (!empty(getPostValue('phdStatus'))) {
        $phdStmt = $conn->prepare("INSERT INTO degree_phd_details (
            application_id, status, university_institute, year_of_passing
        ) VALUES (?, ?, ?, ?)");
        
        if (!$phdStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $phdStmt->bind_param("isss",
            $applicationId,
            getPostValue('phdStatus'),
            getPostValue('phdUniversity'),
            getPostValue('phdYear')
        );
        
        if (!$phdStmt->execute()) {
            throw new Exception("PhD details insert failed: " . $phdStmt->error);
        }
        $phdStmt->close();
    }

    // Handle work experience
    if (isset($_POST['organization'])) {
        $expStmt = $conn->prepare("INSERT INTO degree_work_experience (
            application_id, organization_university, designation_post, from_date, to_date,
            currently_working, salary
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if (!$expStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        foreach ($_POST['organization'] as $index => $org) {
            $currentlyWorking = isset($_POST["currently_working"][$index]) ? 1 : 0;
            $expStmt->bind_param("issssid",
                $applicationId,
                $org,
                getPostValue("designation[$index]"),
                getPostValue("from_date[$index]"),
                getPostValue("to_date[$index]"),
                $currentlyWorking,
                floatval(getPostValue("salary[$index]", 0))
            );
            
            if (!$expStmt->execute()) {
                throw new Exception("Work experience insert failed: " . $expStmt->error);
            }
        }
        $expStmt->close();
    }

    // Handle courses taught
    if (isset($_POST['collegeName'])) {
        $courseStmt = $conn->prepare("INSERT INTO degree_courses_taught (
            application_id, college_name, class_name, subject_name, years_experience,
            from_date, to_date, department_type, contract_type, last_salary,
            approved_by_university, letter_number, letter_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$courseStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $courseStmt->bind_param("isssiisssdsss",
            $applicationId,
            getPostValue('collegeName'),
            getPostValue('className'),
            getPostValue('subjectName'),
            intval(getPostValue('yearsOfExp', 0)),
            getPostValue('fromDate'),
            getPostValue('toDate'),
            getPostValue('departmentType'),
            getPostValue('typeOfContract'),
            floatval(getPostValue('lastSalary', 0)),
            getPostValue('approvedByUni'),
            getPostValue('letterNumber'),
            getPostValue('letterDate')
        );
        
        if (!$courseStmt->execute()) {
            throw new Exception("Courses taught insert failed: " . $courseStmt->error);
        }
        $courseStmt->close();
    }

    // Handle research publications
    if (isset($_POST['paper_title'])) {
        $researchStmt = $conn->prepare("INSERT INTO degree_research_publications (
            application_id, title, journal_name, year_of_publication
        ) VALUES (?, ?, ?, ?)");
        
        if (!$researchStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        foreach ($_POST['paper_title'] as $index => $title) {
            $researchStmt->bind_param("isss",
                $applicationId,
                $title,
                getPostValue("journal_name[$index]"),
                getPostValue("year_of_publication[$index]")
            );
            
            if (!$researchStmt->execute()) {
                throw new Exception("Research publication insert failed: " . $researchStmt->error);
            }
        }
        $researchStmt->close();
    }

    // Handle awards
    if (isset($_POST['awardTitle'])) {
        $awardStmt = $conn->prepare("INSERT INTO degree_awards (
            application_id, title, organization_name, nature_of_award, recognition
        ) VALUES (?, ?, ?, ?, ?)");
        
        if (!$awardStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $awardStmt->bind_param("issss",
            $applicationId,
            getPostValue('awardTitle'),
            getPostValue('organizationName'),
            getPostValue('natureOfAward'),
            getPostValue('recognition')
        );
        
        if (!$awardStmt->execute()) {
            throw new Exception("Awards insert failed: " . $awardStmt->error);
        }
        $awardStmt->close();
    }

    // Commit transaction
    $conn->commit();

    // Clear any previous errors
    unset($_SESSION['error']);
    unset($_SESSION['form_errors']);
    unset($_SESSION['form_data']);

    // Redirect to success page
    header("Location: application_success.html");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    // Store error message
    $_SESSION['error'] = "Application submission failed: " . $e->getMessage();
    
    // Store form data for repopulation
    $_SESSION['form_data'] = $_POST;
    
    // Redirect back to form
    header("Location: degree.html");
    exit();
} finally {
    $conn->close();
}