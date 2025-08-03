<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "form_data";

$conn = new mysqli('127.0.0.1', 'root', '1234', 'form_data', 8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle resume upload
$resume_filename = "";
if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
    $targetDir = "resumes/";
    $fileExt = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));
    if ($fileExt !== 'pdf') die("Only PDF files are allowed.");
    $resume_filename = uniqid() . "_" . basename($_FILES["resume"]["name"]);
    $targetFilePath = $targetDir . $resume_filename;
    if (!move_uploaded_file($_FILES["resume"]["tmp_name"], $targetFilePath)) {
        die("Failed to upload resume.");
    }
}

// Get main form values
$post_applied_for     = $_POST['post_applied_for'];
$first_name           = $_POST['first_name'];
$middle_name          = $_POST['middle_name'];
$last_name            = $_POST['last_name'];
$dob                  = $_POST['dob'];
$gender               = $_POST['gender'];
$marital_status       = $_POST['marital_status'];
$email                = $_POST['email'];
$alternate_email      = $_POST['alternate_email'];
$caste                = $_POST['caste'];
$aadhar               = $_POST['aadhar'];
$pan                  = $_POST['pan'];
$state                = $_POST['state'];
$city                 = $_POST['city'];
$address              = $_POST['address'];
$pincode              = $_POST['pincode'];
$mobile               = $_POST['mobile'];
$alternate_mobile     = $_POST['alternate_mobile'];
$institute_applied_to = $_POST['institute_applied_to'];
$current_salary       = $_POST['current_salary'];
$expected_salary      = $_POST['expected_salary'];
$extra_curricular     = $_POST['extra_curricular'];
$reference_name       = $_POST['reference_name'];
$reference_applied_for= $_POST['reference_applied_for'];

// Insert main application
$stmt = $conn->prepare("INSERT INTO applications (
    post_applied_for, first_name, middle_name, last_name, dob, gender, marital_status,
    email, alternate_email, caste, aadhar, pan, state, city, address, pincode,
    mobile, alternate_mobile, institute_applied_to, current_salary, expected_salary,
    extra_curricular, reference_name, reference_applied_for, resume_filename,
    phd_status, phd_university, phd_year, bed_university, bed_year,
    college_name, class_name, subject_name, years_experience, courses_from_date, courses_to_date,
    department_type, contract_type, last_salary, approved_by_university, letter_number, letter_date
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssss",
    $post_applied_for, $first_name, $middle_name, $last_name, $dob, $gender, $marital_status,
    $email, $alternate_email, $caste, $aadhar, $pan, $state, $city, $address, $pincode,
    $mobile, $alternate_mobile, $institute_applied_to, $current_salary, $expected_salary,
    $extra_curricular, $reference_name, $reference_applied_for, $resume_filename,
    $_POST['phd_status'], $_POST['phd_university'], $_POST['phd_year'],
    $_POST['bed_university'], $_POST['bed_year'],
    $_POST['college_name'], $_POST['class_name'], $_POST['subject_name'], $_POST['years_experience'],
    $_POST['courses_from_date'], $_POST['courses_to_date'],
    $_POST['department_type'], $_POST['contract_type'], $_POST['last_salary'],
    $_POST['approved_by_university'], $_POST['letter_number'], $_POST['letter_date']
);

$stmt->execute();
$application_id = $stmt->insert_id;
$stmt->close();

// === Qualifications ===
foreach ($_POST['degree'] as $i => $val) {
    $stmt = $conn->prepare("INSERT INTO qualifications (application_id, degree, degree_name, education_mode, university_name, specialization, year_of_passing, percentage, cgpa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss",
        $application_id,
        $_POST['degree'][$i],
        $_POST['degree_name'][$i],
        $_POST['education_mode'][$i],
        $_POST['university_name'][$i],
        $_POST['specialization'][$i],
        $_POST['year_of_passing'][$i],
        $_POST['percentage'][$i],
        $_POST['cgpa'][$i]
    );
    $stmt->execute();
    $stmt->close();
}

// === Work Experience ===
foreach ($_POST['experience_organization'] as $i => $val) {
    $stmt = $conn->prepare("INSERT INTO work_experience (application_id, organization, designation, from_date, to_date, current_salary, currently_working) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $currently_working = isset($_POST['experience_current_role'][$i]) ? 1 : 0;
    $stmt->bind_param("isssssi",
        $application_id,
        $_POST['experience_organization'][$i],
        $_POST['experience_designation'][$i],
        $_POST['experience_from'][$i],
        $_POST['experience_to'][$i],
        $_POST['experience_salary'][$i],
        $currently_working
    );
    $stmt->execute();
    $stmt->close();
}

// === Research Publications ===
foreach ($_POST['scopus_publications'] as $i => $val) {
    $stmt = $conn->prepare("INSERT INTO research_publications (application_id, scopus_publications, scopus_id, conference_presented, paper_title, journal_name, publication_year, approved_papers) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssii",
        $application_id,
        $_POST['scopus_publications'][$i],
        $_POST['scopus_id'][$i],
        $_POST['conference_presented'][$i],
        $_POST['paper_title'][$i],
        $_POST['journal_name'][$i],
        $_POST['publication_year'][$i],
        $_POST['approved_papers'][$i]
    );
    $stmt->execute();
    $stmt->close();
}



// Insert PhD details if provided
if (!empty($_POST['phd_status'])) {
    $stmt = $conn->prepare("INSERT INTO phd_details (
        application_id, status, university_institute, year_of_passing
    ) VALUES (?, ?, ?, ?)");
    
    $stmt->bind_param("isss",
        $application_id,
        $_POST['phd_status'],
        $_POST['phd_university'],
        $_POST['phd_year']
    );
    
    if (!$stmt->execute()) {
        error_log("PhD details error: " . $stmt->error);
    }
    $stmt->close();
}


// === Courses Taught ===
$stmt = $conn->prepare("INSERT INTO courses_taught (
    application_id, college_name, class_name, subject_name, years_experience,
    from_date, to_date, department_type, contract_type, last_salary,
    approved_by_university, letter_number, letter_date
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("isssissssssss",
    $application_id,
    $_POST['college_name'],
    $_POST['class_name'],
    $_POST['subject_name'],
    $_POST['years_experience'],
    $_POST['courses_from_date'],
    $_POST['courses_to_date'],
    $_POST['department_type'],
    $_POST['contract_type'],
    $_POST['last_salary'],
    $_POST['approved_by_university'],
    $_POST['letter_number'],
    $_POST['letter_date']
);

if (!$stmt->execute()) {
    error_log("Courses Taught error: " . $stmt->error);
}




// Insert B.Ed details
$stmt = $conn->prepare("INSERT INTO bed_details (
    application_id, university_institute, year_of_passing
) VALUES (?, ?, ?)");

$stmt->bind_param("iss",
    $application_id,
    $_POST['bed_university'],
    $_POST['bed_year']
);

if (!$stmt->execute()) {
    error_log("B.Ed details error: " . $stmt->error);
}
$stmt->close();



// === Awards ===
$stmt = $conn->prepare("INSERT INTO awards (application_id, award_title, award_organization, award_nature, award_salary) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss",
    $application_id,
    $_POST['award_title'],
    $_POST['award_organization'],
    $_POST['award_nature'],
    $_POST['award_salary']
);
$stmt->execute();

$stmt->close();

$conn->close();

echo "✅ Application submitted successfully.";
?>
