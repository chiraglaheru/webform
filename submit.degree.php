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
if (isset($_FILES['resumeUpload']) && $_FILES['resumeUpload']['error'] == 0) {
    $targetDir = "resumes/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileExt = strtolower(pathinfo($_FILES["resumeUpload"]["name"], PATHINFO_EXTENSION));
    if ($fileExt !== 'pdf') die("Only PDF files are allowed.");
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

// Store all values in variables first (required for bind_param by reference)
$post_applied_for = isset($_POST['post_applied_for']) ? $_POST['post_applied_for'] : '';
$title = isset($_POST['title']) ? $_POST['title'] : '';
$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
$middle_name = isset($_POST['middle_name']) ? $_POST['middle_name'] : '';
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
$dob = formatDate(isset($_POST['dob']) ? $_POST['dob'] : '');
$age = isset($_POST['age']) ? (int)$_POST['age'] : 0;
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$marital_status = isset($_POST['marital_status']) ? $_POST['marital_status'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$alternate_email = isset($_POST['alternate_email']) ? $_POST['alternate_email'] : '';
$caste_subcaste = isset($_POST['caste_subcaste']) ? $_POST['caste_subcaste'] : '';
$aadhar_no = isset($_POST['aadhar_no']) ? $_POST['aadhar_no'] : '';
$pan_no = isset($_POST['pan_no']) ? $_POST['pan_no'] : '';
$state = isset($_POST['state']) ? $_POST['state'] : '';
$city = isset($_POST['city']) ? $_POST['city'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$pincode = isset($_POST['pincode']) ? $_POST['pincode'] : '';
$mobile_no = isset($_POST['mobile_no']) ? $_POST['mobile_no'] : '';
$alternate_mobile_no = isset($_POST['alternate_mobile_no']) ? $_POST['alternate_mobile_no'] : '';
$institute_applied_to = isset($_POST['institute_applied_to']) ? $_POST['institute_applied_to'] : '';
$current_salary = isset($_POST['current_salary']) ? (double)$_POST['current_salary'] : 0.0;
$expected_salary = isset($_POST['expected_salary']) ? (double)$_POST['expected_salary'] : 0.0;
$scopus_publications = isset($_POST['scopus_publications']) ? (int)$_POST['scopus_publications'] : 0;
$scopus_id = isset($_POST['scopus_id']) ? $_POST['scopus_id'] : '';
$conference_presented = isset($_POST['conference_presented']) ? $_POST['conference_presented'] : '';
$approved_papers = isset($_POST['approved_papers']) ? (int)$_POST['approved_papers'] : 0;
$reference_name = isset($_POST['reference_name']) ? $_POST['reference_name'] : '';
$applied_for_position = isset($_POST['applied_for_position']) ? $_POST['applied_for_position'] : '';
$extra_curricular = isset($_POST['extra_curricular']) ? $_POST['extra_curricular'] : '';
$net_status = isset($_POST['net_status']) ? $_POST['net_status'] : 'No';
$net_year = isset($_POST['net_year']) ? $_POST['net_year'] : '';
$set_status = isset($_POST['set_status']) ? $_POST['set_status'] : 'No';
$set_year = isset($_POST['set_year']) ? $_POST['set_year'] : '';
$declaration_accepted = isset($_POST['declaration_accepted']) ? 1 : 0;

// Prepare statement
$sql = "INSERT INTO degree_applications (
    post_applied_for, title, first_name, middle_name, last_name, 
    dob, age, gender, marital_status, email, alternate_email, 
    caste_subcaste, aadhar_no, pan_no, state, city, address, 
    pincode, mobile_no, alternate_mobile_no, institute_applied_to,
    current_salary, expected_salary, scopus_publications, scopus_id,
    conference_presented, approved_papers, reference_name, 
    applied_for_position, extracurricular, resume_filename,
    net_status, net_year, set_status, set_year, declaration_accepted
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Count placeholders and parameters
$placeholder_count = substr_count($sql, '?');
echo "DEBUG: Found $placeholder_count placeholders in SQL<br>";

// FIXED: Correct type string with exactly 36 characters for 36 parameters
// Parameters breakdown:
// 1-5: post_applied_for, title, first_name, middle_name, last_name (s,s,s,s,s)
// 6: dob (s - date as string)
// 7: age (i - integer) 
// 8-11: gender, marital_status, email, alternate_email (s,s,s,s)
// 12-21: caste_subcaste through institute_applied_to (10 strings: s,s,s,s,s,s,s,s,s,s)
// 22-23: current_salary, expected_salary (d,d - doubles)
// 24: scopus_publications (i - integer)
// 25-26: scopus_id, conference_presented (s,s)
// 27: approved_papers (i - integer)
// 28-31: reference_name, applied_for_position, extracurricular, resume_filename (s,s,s,s)
// 32-35: net_status, net_year, set_status, set_year (s,s,s,s)
// 36: declaration_accepted (i - integer)

$type_string = "sssssssissssssssssssddisissssssssssi";
echo "DEBUG: Type string length: " . strlen($type_string) . "<br>";

// Bind parameters
$bind_result = $stmt->bind_param($type_string,
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
    die("Bind failed: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("Error inserting application: " . $stmt->error);
}

$application_id = $conn->insert_id;
$stmt->close();

// Insert into degree_qualifications
if (isset($_POST['degree']) && is_array($_POST['degree'])) {
    foreach ($_POST['degree'] as $i => $degree) {
        if (!empty($degree)) {
            $stmt = $conn->prepare("INSERT INTO degree_qualifications (
                application_id, degree, degree_name, education_mode, university, 
                specialization, year_of_passing, percentage, cgpa
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $app_id = $application_id;
            $deg = $degree;
            $deg_name = isset($_POST['degree_name'][$i]) ? $_POST['degree_name'][$i] : '';
            $edu_mode = isset($_POST['education_mode'][$i]) ? $_POST['education_mode'][$i] : '';
            $uni_name = isset($_POST['university_name'][$i]) ? $_POST['university_name'][$i] : '';
            $spec = isset($_POST['specialization'][$i]) ? $_POST['specialization'][$i] : '';
            $year_pass = isset($_POST['year_of_passing'][$i]) ? $_POST['year_of_passing'][$i] : '';
            $percent = isset($_POST['percentage'][$i]) ? $_POST['percentage'][$i] : '';
            $cgpa = isset($_POST['cgpa'][$i]) ? $_POST['cgpa'][$i] : '';
            
            $stmt->bind_param("issssssss",
                $app_id, $deg, $deg_name, $edu_mode, $uni_name,
                $spec, $year_pass, $percent, $cgpa
            );
            
            if (!$stmt->execute()) {
                die("Error inserting qualification: " . $stmt->error);
            }
            $stmt->close();
        }
    }
}

// Insert into degree_phd_details if applicable
if (isset($_POST['phd_status']) && $_POST['phd_status'] !== 'not-applicable') {
    $stmt = $conn->prepare("INSERT INTO degree_phd_details (
        application_id, status, university_institute, year_of_passing
    ) VALUES (?, ?, ?, ?)");
    
    $app_id = $application_id;
    $phd_stat = isset($_POST['phd_status']) ? $_POST['phd_status'] : '';
    $phd_uni = isset($_POST['phd_university']) ? $_POST['phd_university'] : '';
    $phd_yr = isset($_POST['phd_year']) ? $_POST['phd_year'] : '';
    
    $stmt->bind_param("isss", $app_id, $phd_stat, $phd_uni, $phd_yr);
    
    if (!$stmt->execute()) {
        die("Error inserting PhD details: " . $stmt->error);
    }
    $stmt->close();
}

// Insert into degree_work_experience
if (isset($_POST['organization']) && is_array($_POST['organization'])) {
    foreach ($_POST['organization'] as $i => $organization) {
        if (!empty($organization)) {
            $currently_working = isset($_POST['currently_working'][$i]) ? 1 : 0;
            
            $stmt = $conn->prepare("INSERT INTO degree_work_experience (
                application_id, organization_university, designation_post, from_date, to_date, 
                salary, currently_working
            ) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $app_id = $application_id;
            $org = $organization;
            $desig = isset($_POST['designation'][$i]) ? $_POST['designation'][$i] : '';
            $from_dt = formatDate(isset($_POST['from_date'][$i]) ? $_POST['from_date'][$i] : '');
            $to_dt = formatDate(isset($_POST['to_date'][$i]) ? $_POST['to_date'][$i] : '');
            $sal = isset($_POST['salary'][$i]) ? $_POST['salary'][$i] : '';
            $curr_work = $currently_working;
            
            $stmt->bind_param("isssssi", $app_id, $org, $desig, $from_dt, $to_dt, $sal, $curr_work);
            
            if (!$stmt->execute()) {
                die("Error inserting work experience: " . $stmt->error);
            }
            $stmt->close();
        }
    }
}

// Insert into degree_courses_taught
if (!empty($_POST['collegeName'])) {
    $stmt = $conn->prepare("INSERT INTO degree_courses_taught (
        application_id, college_name, class_name, subject_name, years_experience,
        from_date, to_date, department_type, contract_type, last_salary,
        approved_by_university, letter_number, letter_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $app_id = $application_id;
    $coll_name = isset($_POST['collegeName']) ? $_POST['collegeName'] : '';
    $class_name = isset($_POST['className']) ? $_POST['className'] : '';
    $subj_name = isset($_POST['subjectName']) ? $_POST['subjectName'] : '';
    $years_exp = isset($_POST['yearsOfExp']) ? $_POST['yearsOfExp'] : '';
    $from_dt_course = formatDate(isset($_POST['fromDateCourse']) ? $_POST['fromDateCourse'] : '');
    $to_dt_course = formatDate(isset($_POST['toDateCourse']) ? $_POST['toDateCourse'] : '');
    $dept_type = isset($_POST['departmentType']) ? $_POST['departmentType'] : '';
    $contract_type = isset($_POST['typeOfContract']) ? $_POST['typeOfContract'] : '';
    $last_sal = isset($_POST['lastSalary']) ? $_POST['lastSalary'] : '';
    $approved_uni = isset($_POST['approvedByUni']) ? $_POST['approvedByUni'] : '';
    $letter_num = isset($_POST['letterNumber']) ? $_POST['letterNumber'] : '';
    $letter_dt = formatDate(isset($_POST['letterDate']) ? $_POST['letterDate'] : '');
    
    $stmt->bind_param("issssssssssss",
        $app_id, $coll_name, $class_name, $subj_name, $years_exp,
        $from_dt_course, $to_dt_course, $dept_type, $contract_type, $last_sal,
        $approved_uni, $letter_num, $letter_dt
    );
    
    if (!$stmt->execute()) {
        die("Error inserting course taught: " . $stmt->error);
    }
    $stmt->close();
}

// Insert into degree_research_publications
// Insert into degree_research_publications with proper year handling

function formatYear($year) {
    if (empty($year)) return null;
    
    // If it's already a 4-digit year, return it
    if (preg_match('/^\d{4}$/', $year)) {
        return $year;
    }
    
    // If it's a date string (dd/mm/yyyy), extract the year
    $parts = explode('/', $year);
    if (count($parts) === 3 && strlen($parts[2]) === 4) {
        return $parts[2];
    }
    
    // If we can't determine the year, return null
    return null;
}


if (isset($_POST['paper_title']) && is_array($_POST['paper_title'])) {
    foreach ($_POST['paper_title'] as $i => $title) {
        if (!empty($title)) {
            $year_of_publication = formatYear($_POST['year_of_publication'][$i] ?? '');
            
            // Validate the year
            if ($year_of_publication === null || !preg_match('/^\d{4}$/', $year_of_publication)) {
                error_log("Invalid year format for publication: " . ($_POST['year_of_publication'][$i] ?? ''));
                continue; // Skip this record
            }
            
            $stmt = $conn->prepare("INSERT INTO degree_research_publications (
                application_id, scopus_publications, scopus_id, conference_presented, 
                title, journal_name, year_of_publication, approved_papers
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param("iisssssi",
                $application_id,
                (int)($_POST['scopus_publications'][$i] ?? 0),
                $_POST['scopus_id'][$i] ?? '',
                $_POST['conference_presented'][$i] ?? '',
                $title,
                $_POST['journal_name'][$i] ?? '',
                $year_of_publication,
                (int)($_POST['approved_papers'][$i] ?? 0)
            );
            
            if (!$stmt->execute()) {
                error_log("Publication Error: " . $stmt->error);
                continue; // Continue with next record
            }
            $stmt->close();
        }
    }
}

// Insert into degree_awards
if (!empty($_POST['awardTitle'])) {
    $stmt = $conn->prepare("INSERT INTO degree_awards (
        application_id, title, organization_name, nature_of_award, recognition
    ) VALUES (?, ?, ?, ?, ?)");
    
    $app_id = $application_id;
    $award_title = isset($_POST['awardTitle']) ? $_POST['awardTitle'] : '';
    $org_name = isset($_POST['organizationName']) ? $_POST['organizationName'] : '';
    $nature_award = isset($_POST['natureOfAward']) ? $_POST['natureOfAward'] : '';
    $expected_award_sal = isset($_POST['expectedAwardSalary']) ? $_POST['expectedAwardSalary'] : '';
    
    $stmt->bind_param("issss", $app_id, $award_title, $org_name, $nature_award, $expected_award_sal);
    
    if (!$stmt->execute()) {
        die("Error inserting award: " . $stmt->error);
    }
    $stmt->close();
}

$conn->close();

// Redirect to success page
header("Location: application_success.php");
exit();
?>