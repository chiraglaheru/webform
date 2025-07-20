<?php
// Database connection
$conn = new mysqli('localhost', 'root', 'root', 'form_data', null, '/Applications/MAMP/tmp/mysql/mysql.sock');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate required fields
$errors = [];

$required_fields = [
    'post_applied_for' => "Position Applied For",
    'title' => "Title",
    'first_name' => "First Name",
    'last_name' => "Last Name",
    'dob' => "Date of Birth",
    'gender' => "Gender",
    'marital_status' => "Marital Status",
    'email' => "Email",
    'caste' => "Caste/Subcaste",
    'aadhar' => "Aadhar Number",
    'pan' => "PAN Number",
    'state' => "State",
    'city' => "City",
    'address' => "Address",
    'pincode' => "PIN Code",
    'mobile' => "Mobile Number",
    'expected_salary' => "Expected Salary"
];

foreach ($required_fields as $field => $label) {
    if (empty($_POST[$field])) {
        $errors[] = "$label is required";
    }
}

if (!empty($errors)) {
    echo json_encode(["status" => "error", "message" => implode("\n", $errors)]);
    exit;
}

// Handle CV upload
$cv_filename = "";
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "resumes/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileExtension = strtolower(pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION));
    if ($fileExtension !== 'pdf') {
        echo json_encode(["status" => "error", "message" => "Only PDF files are allowed."]);
        exit;
    }

    $cv_filename = uniqid() . "_" . basename($_FILES["cv"]["name"]);
    $targetPath = $targetDir . $cv_filename;

    if (!move_uploaded_file($_FILES["cv"]["tmp_name"], $targetPath)) {
        echo json_encode(["status" => "error", "message" => "Failed to upload CV."]);
        exit;
    }
}

// Prepare variables
$post_applied_for = $_POST['post_applied_for'];
$other_post_type = ($post_applied_for === 'other') ? $_POST['other_post_type'] : null;
$title = $_POST['title'];
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'] ?? null;
$last_name = $_POST['last_name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$marital_status = $_POST['marital_status'];
$email = $_POST['email'];
$caste = $_POST['caste'];
$aadhar = $_POST['aadhar'];
$pan = $_POST['pan'];
$state = $_POST['state'];
$city = $_POST['city'];
$address = $_POST['address'];
$pincode = $_POST['pincode'];
$mobile = $_POST['mobile'];
$mother_tongue = $_POST['mother_tongue'] ?? null;
$other_language = $_POST['other_language'] ?? null;
$english_typing = $_POST['english_typing'] ?? null;
$marathi_typing = $_POST['marathi_typing'] ?? null;
$joining_date = $_POST['joining_date'] ?? null;
$expected_salary = $_POST['expected_salary'];
$current_salary = $_POST['current_salary'] ?? null;
$comments = $_POST['comments'] ?? null;
$created_at = date('Y-m-d H:i:s');

// Insert into non_teaching_applications
$stmt = $conn->prepare("INSERT INTO non_teaching_applications (
    post_applied_for, other_post_type, title, first_name, middle_name, last_name,
    dob, gender, marital_status, email, caste, aadhar, pan, state, city,
    address, pincode, mobile, mother_tongue, other_language,
    typing_speed_english, typing_speed_marathi, joining_date,
    expected_salary, current_salary, comments, cv_filename, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssssssssssssssssssssssss",
    $post_applied_for, $other_post_type, $title, $first_name, $middle_name, $last_name,
    $dob, $gender, $marital_status, $email, $caste, $aadhar, $pan, $state, $city,
    $address, $pincode, $mobile, $mother_tongue, $other_language,
    $english_typing, $marathi_typing, $joining_date,
    $expected_salary, $current_salary, $comments, $cv_filename, $created_at
);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
    exit;
}

$application_id = $stmt->insert_id;
$stmt->close();

// Insert qualifications
if (isset($_POST['degree']) && is_array($_POST['degree'])) {
    $qualStmt = $conn->prepare("INSERT INTO nt_qualifications (
        application_id, degree, university, year, percentage
    ) VALUES (?, ?, ?, ?, ?)");

    foreach ($_POST['degree'] as $index => $degree) {
        if (!empty($degree)) {
            $university = $_POST['university'][$index] ?? '';
            $year = $_POST['year'][$index] ?? '';
            $percentage = $_POST['percentage'][$index] ?? '';
            $qualStmt->bind_param("issss", $application_id, $degree, $university, $year, $percentage);
            $qualStmt->execute();
        }
    }
    $qualStmt->close();
}

// Insert work experience
if (isset($_POST['organization']) && is_array($_POST['organization'])) {
    $expStmt = $conn->prepare("INSERT INTO nt_experience (
        application_id, organization, designation, from_date, to_date, salary
    ) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($_POST['organization'] as $index => $organization) {
        if (!empty($organization)) {
            $designation = $_POST['designation'][$index] ?? '';
            $from_date = $_POST['from_date'][$index] ?? '';
            $to_date = $_POST['to_date'][$index] ?? '';
            $salary = $_POST['salary'][$index] ?? '';
            $expStmt->bind_param("isssss", $application_id, $organization, $designation, $from_date, $to_date, $salary);
            $expStmt->execute();
        }
    }
    $expStmt->close();
}

$conn->close();

echo json_encode(["status" => "success", "message" => "✅ Non-teaching application submitted successfully."]);
?>
