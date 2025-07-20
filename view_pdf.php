<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$applicationId = $_GET['id'] ?? 0;
if (!$applicationId) die("No application ID.");


$conn = new mysqli("localhost", "root", "root", "form_data", 8889);
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);

// Get main application
$app = $conn->query("SELECT * FROM applications WHERE id = $applicationId")->fetch_assoc();
if (!$app) die("Application not found.");

// Get related data
function fetchList($conn, $table, $id) {
    return $conn->query("SELECT * FROM $table WHERE application_id = $id")->fetch_all(MYSQLI_ASSOC);
}
$qualifications = fetchList($conn, "qualifications", $applicationId);
$experience     = fetchList($conn, "work_experience", $applicationId);
$courses        = fetchList($conn, "courses_taught", $applicationId);
$research       = fetchList($conn, "research_publications", $applicationId);
$awards         = fetchList($conn, "awards", $applicationId);

// Build HTML
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
  <style>
    body { font-family: Arial; font-size: 12px; }
    h1 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    th, td { border: 1px solid #000; padding: 5px; }
    th { background: #eee; }
    ul { padding-left: 20px; }
  </style>
</head>
<body>
  <h1>Application #<?= $applicationId ?></h1>

  <h2>Personal Info</h2>
  <table>
    <tr><th>Post Applied</th><td><?= $app['post_applied_for'] ?></td></tr>
    <tr><th>Name</th><td><?= $app['first_name'] . ' ' . $app['middle_name'] . ' ' . $app['last_name'] ?></td></tr>
    <tr><th>DOB</th><td><?= $app['dob'] ?></td></tr>
    <tr><th>Gender</th><td><?= $app['gender'] ?></td></tr>
    <tr><th>Marital Status</th><td><?= $app['marital_status'] ?></td></tr>
    <tr><th>Email</th><td><?= $app['email'] ?></td></tr>
    <tr><th>Alternate Email</th><td><?= $app['alternate_email'] ?></td></tr>
    <tr><th>Mobile</th><td><?= $app['mobile'] ?></td></tr>
    <tr><th>Alternate Mobile</th><td><?= $app['alternate_mobile'] ?></td></tr>
    <tr><th>Caste</th><td><?= $app['caste'] ?></td></tr>
    <tr><th>Aadhar</th><td><?= $app['aadhar'] ?></td></tr>
    <tr><th>PAN</th><td><?= $app['pan'] ?></td></tr>
    <tr><th>State</th><td><?= $app['state'] ?></td></tr>
    <tr><th>City</th><td><?= $app['city'] ?></td></tr>
    <tr><th>Address</th><td><?= $app['address'] ?></td></tr>
    <tr><th>Pincode</th><td><?= $app['pincode'] ?></td></tr>
    <tr><th>Institute Applied To</th><td><?= $app['institute_applied_to'] ?></td></tr>
    <tr><th>Current Salary</th><td><?= $app['current_salary'] ?></td></tr>
    <tr><th>Expected Salary</th><td><?= $app['expected_salary'] ?></td></tr>
    <tr><th>Extra Curricular</th><td><?= $app['extra_curricular'] ?></td></tr>
    <tr><th>Reference Name</th><td><?= $app['reference_name'] ?></td></tr>
    <tr><th>Reference Applied For</th><td><?= $app['reference_applied_for'] ?></td></tr>
    <p><strong>Resume:</strong> <a href="http://localhost:8888/web-form-main/resumes/<?= $app['resume_filename'] ?>" target="_blank">Download Resume</a></p>
  </table>

  <h2>Ph.D. Details</h2>
  <table>
    <tr><th>Status</th><td><?= $app['phd_status'] ?></td></tr>
    <tr><th>University</th><td><?= $app['phd_university'] ?></td></tr>
    <tr><th>Year</th><td><?= $app['phd_year'] ?></td></tr>
  </table>

  <h2>B.Ed. Details</h2>
  <table>
    <tr><th>University</th><td><?= $app['bed_university'] ?></td></tr>
    <tr><th>Year</th><td><?= $app['bed_year'] ?></td></tr>
  </table>

  <h2>Qualifications</h2>
  <table>
    <tr><th>Degree</th><th>University</th><th>Year</th><th>Percentage</th><th>CGPA</th></tr>
    <?php foreach ($qualifications as $q): ?>
    <tr>
      <td><?= $q['degree'] ?> (<?= $q['degree_name'] ?>)</td>
      <td><?= $q['university_name'] ?></td>
      <td><?= $q['year_of_passing'] ?></td>
      <td><?= $q['percentage'] ?></td>
      <td><?= $q['cgpa'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h2>Work Experience</h2>
  <table>
    <tr><th>Organization</th><th>Designation</th><th>From</th><th>To</th><th>Salary</th><th>Currently Working</th></tr>
    <?php foreach ($experience as $e): ?>
    <tr>
      <td><?= $e['organization'] ?></td>
      <td><?= $e['designation'] ?></td>
      <td><?= $e['from_date'] ?></td>
      <td><?= $e['to_date'] ?></td>
      <td><?= $e['current_salary'] ?></td>
      <td><?= $e['currently_working'] ? "Yes" : "No" ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h2>Courses Taught</h2>
  <table>
    <tr><th>College</th><th>Class</th><th>Subject</th><th>Years</th><th>From</th><th>To</th><th>Dept</th><th>Contract</th><th>Last Salary</th><th>Approved</th><th>Letter No</th><th>Letter Date</th></tr>
    <?php foreach ($courses as $c): ?>
    <tr>
      <td><?= $c['college_name'] ?></td>
      <td><?= $c['class_name'] ?></td>
      <td><?= $c['subject_name'] ?></td>
      <td><?= $c['years_experience'] ?></td>
      <td><?= $c['from_date'] ?></td>
      <td><?= $c['to_date'] ?></td>
      <td><?= $c['department_type'] ?></td>
      <td><?= $c['contract_type'] ?></td>
      <td><?= $c['last_salary'] ?></td>
      <td><?= $c['approved_by_university'] ?></td>
      <td><?= $c['letter_number'] ?></td>
      <td><?= $c['letter_date'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h2>Research Publications</h2>
  <table>
    <tr><th>Title</th><th>Journal</th><th>Year</th><th>Scopus ID</th><th>Presented</th><th>Scopus Pubs</th><th>Approved Papers</th></tr>
    <?php foreach ($research as $r): ?>
    <tr>
      <td><?= $r['paper_title'] ?></td>
      <td><?= $r['journal_name'] ?></td>
      <td><?= $r['publication_year'] ?></td>
      <td><?= $r['scopus_id'] ?></td>
      <td><?= $r['conference_presented'] ?></td>
      <td><?= $r['scopus_publications'] ?></td>
      <td><?= $r['approved_papers'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h2>Awards</h2>
  <table>
    <tr><th>Title</th><th>Organization</th><th>Nature</th><th>Salary</th></tr>
    <?php foreach ($awards as $a): ?>
    <tr>
      <td><?= $a['award_title'] ?></td>
      <td><?= $a['award_organization'] ?></td>
      <td><?= $a['award_nature'] ?></td>
      <td><?= $a['award_salary'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

</body>
</html>
<?php
$html = ob_get_clean();

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Application_$applicationId.pdf", ["Attachment" => false]);
