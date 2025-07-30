

import express from 'express';
import mysql from 'mysql2/promise';
import ExcelJS from 'exceljs';
import puppeteer from 'puppeteer';
import dotenv from 'dotenv';
import cors from 'cors';
dotenv.config();

const connection = await mysql.createConnection({
  socketPath: '/Applications/MAMP/tmp/mysql/mysql.sock',
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_DATABASE
});

const app = express();

const dbConfig = {
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_DATABASE,
};

const pool = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_DATABASE,
});

app.use(cors());

app.get('/export', async (req, res) => {
  try {
    // const connection = await mysql.createConnection(dbConfig);

    const [applicants] = await connection.execute('SELECT * FROM applications');
    const [awards] = await connection.execute('SELECT * FROM awards');
    const [bed_details] = await connection.execute('SELECT * FROM bed_details');
    const [courses_taught] = await connection.execute('SELECT * FROM courses_taught');
    const [phd_details] = await connection.execute('SELECT * FROM phd_details');
    const [qualifications] = await connection.execute('SELECT * FROM qualifications');
    const [research_publications] = await connection.execute('SELECT * FROM research_publications');
    const [work_experience] = await connection.execute('SELECT * FROM work_experience');

    const workbook = new ExcelJS.Workbook();

    // 📄 Applicants
    const applicantSheet = workbook.addWorksheet('Applicants');
    applicantSheet.columns = [
      { header: 'ID', key: 'id' },
      { header: 'Post Applied For', key: 'post_applied_for' },
      { header: 'First Name', key: 'first_name' },
      { header: 'Middle Name', key: 'middle_name' },
      { header: 'Last Name', key: 'last_name' },
      { header: 'Date of Birth', key: 'dob' },
      { header: 'Gender', key: 'gender' },
      { header: 'Marital Status', key: 'marital_status' },
      { header: 'Email', key: 'email' },
      { header: 'Alternate Email', key: 'alternate_email' },
      { header: 'Caste', key: 'caste' },
      { header: 'Aadhar', key: 'aadhar' },
      { header: 'PAN', key: 'pan' },
      { header: 'State', key: 'state' },
      { header: 'City', key: 'city' },
      { header: 'Address', key: 'address' },
      { header: 'Pincode', key: 'pincode' },
      { header: 'Mobile', key: 'mobile' },
      { header: 'Alternate Mobile', key: 'alternate_mobile' },
      { header: 'Institute Applied To', key: 'institute_applied_to' },
      { header: 'Current Salary', key: 'current_salary' },
      { header: 'Expected Salary', key: 'expected_salary' },
      { header: 'Extra Curricular', key: 'extra_curricular' },
      { header: 'Reference Name', key: 'reference_name' },
      { header: 'Reference Applied For', key: 'reference_applied_for' },
      { header: 'Resume Filename', key: 'resume_filename' },
      { header: 'Created At', key: 'created_at' },
      { header: 'PhD Status', key: 'phd_status' },
      { header: 'PhD University', key: 'phd_university' },
      { header: 'PhD Year', key: 'phd_year' },
      { header: 'BEd University', key: 'bed_university' },
      { header: 'BEd Year', key: 'bed_year' },
      { header: 'College Name', key: 'college_name' },
      { header: 'Class Name', key: 'class_name' },
      { header: 'Subject Name', key: 'subject_name' },
      { header: 'Years Experience', key: 'years_experience' },
      { header: 'Courses From Date', key: 'courses_from_date' },
      { header: 'Courses To Date', key: 'courses_to_date' },
      { header: 'Department Type', key: 'department_type' },
      { header: 'Contract Type', key: 'contract_type' },
      { header: 'Last Salary', key: 'last_salary' },
      { header: 'Approved By University', key: 'approved_by_university' },
      { header: 'Letter Number', key: 'letter_number' },
      { header: 'Letter Date', key: 'letter_date' },
      { header: 'Title', key: 'title' },
      { header: 'Age', key: 'age' },
    ];
    applicants.forEach(row => applicantSheet.addRow(row));

    // 🎓 Awards
    const awardsSheet = workbook.addWorksheet('Awards');
awardsSheet.columns = [
  { header: 'ID', key: 'id' },
  { header: 'Application ID', key: 'application_id' },
  { header: 'Title', key: 'title' },
  { header: 'Organization Name', key: 'organization_name' },
  { header: 'Nature of Award', key: 'nature_of_award' },
  { header: 'Recognition', key: 'recognition' }
];
awards.forEach(row => awardsSheet.addRow(row));

    // 💼 Courses taught
    const teachingExperienceSheet = workbook.addWorksheet('Teaching Experience');
teachingExperienceSheet.columns = [
  { header: 'ID', key: 'id' },
  { header: 'Application ID', key: 'application_id' },
  { header: 'College Name', key: 'college_name' },
  { header: 'Class Name', key: 'class_name' },
  { header: 'Subject Name', key: 'subject_name' },
  { header: 'Years of Experience', key: 'years_experience' },
  { header: 'From Date', key: 'from_date' },
  { header: 'To Date', key: 'to_date' },
  { header: 'Department Type', key: 'department_type' },
  { header: 'Contract Type', key: 'contract_type' },
  { header: 'Last Salary', key: 'last_salary' },
  { header: 'Approved by University', key: 'approved_by_university' },
  { header: 'Letter Number', key: 'letter_number' },
  { header: 'Letter Date', key: 'letter_date' }
];
    courses_taught.forEach(row => teachingExperienceSheet.addRow(row));

//bed details 

const bedSheet = workbook.addWorksheet('bed');
bedSheet.columns = [
  { header: 'ID', key: 'id' },
  { header: 'Application ID', key: 'application_id' },
  { header: 'Status', key: 'status' },
  { header: 'University/Institute', key: 'university_institute' },
  { header: 'Year of Passing', key: 'year_of_passing' }
];
bed_details.forEach(row => bedSheet.addRow(row));


//phd
const phdSheet = workbook.addWorksheet('phd');
phdSheet.columns = [
  { header: 'ID', key: 'id' },
  { header: 'Application ID', key: 'application_id' },
  { header: 'Status', key: 'status' },
  { header: 'University/Institute', key: 'university_institute' },
  { header: 'Year of Passing', key: 'year_of_passing' }
];
phd_details.forEach(row => phdSheet.addRow(row));


//qualification

const qualificationsSheet = workbook.addWorksheet('Qualifications');
qualificationsSheet.columns = [
  { header: 'ID', key: 'id' },
  { header: 'Application ID', key: 'application_id' },
  { header: 'Degree', key: 'degree' },
  { header: 'Degree Name', key: 'degree_name' },
  { header: 'Education Mode', key: 'education_mode' },
  { header: 'University Name', key: 'university_name' },
  { header: 'Specialization', key: 'specialization' },
  { header: 'Year of Passing', key: 'year_of_passing' },
  { header: 'Percentage', key: 'percentage' },
  { header: 'CGPA', key: 'cgpa' }
];
qualifications.forEach(row => qualificationsSheet.addRow(row));

//researchpublications

const researchPublicationsSheet = workbook.addWorksheet('Research Publications');
researchPublicationsSheet.columns = [
  { header: 'ID', key: 'id' },
  { header: 'Application ID', key: 'application_id' },
  { header: 'Scopus Publications', key: 'scopus_publications' },
  { header: 'Scopus ID', key: 'scopus_id' },
  { header: 'Presented in Conference', key: 'presented_in_conference' },
  { header: 'Paper Title', key: 'paper_title' },
  { header: 'Journal Name', key: 'journal_name' },
  { header: 'Publication Year', key: 'publication_year' },
  { header: 'Approved Papers', key: 'approved_papers' },
  { header: 'Conference Presented', key: 'conference_presented' }
];
research_publications.forEach(row => researchPublicationsSheet.addRow(row));

//work exp

const professionalExperienceSheet = workbook.addWorksheet('Professional Experience');
professionalExperienceSheet.columns = [
  { header: 'ID', key: 'id', type: 'int' },
  { header: 'Application ID', key: 'application_id', type: 'int' },
  { header: 'Organization', key: 'organization', type: 'varchar' },
  { header: 'Designation', key: 'designation', type: 'varchar' },
  { header: 'From Date', key: 'from_date', type: 'date' },
  { header: 'To Date', key: 'to_date', type: 'date' },
  { header: 'Current Role', key: 'current_role', type: 'tinyint' },
  { header: 'Current Salary', key: 'current_salary', type: 'varchar' },
  { header: 'Currently Working', key: 'currently_working', type: 'tinyint' }
];
work_experience.forEach(row => professionalExperienceSheet.addRow(row));


    // 📤 Output
    res.setHeader(
      'Content-Type',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );
    res.setHeader('Content-Disposition', 'attachment; filename=applicants.xlsx');

    await workbook.xlsx.write(res);
    res.end();
    await connection.end();
  } catch (err) {
    console.error(err);
    res.status(500).send('Error exporting data');
  }
});

async function getAllApplicantsWithDetails() {
  const [applicants] = await pool.query('SELECT * FROM applicants');

  for (const applicant of applicants) {
    const [qualifications] = await pool.query(
      'SELECT * FROM qualifications WHERE applicant_id = ?',
      [applicant.id]
    );
    const [workExperiences] = await pool.query(
      'SELECT * FROM work_experience WHERE applicant_id = ?',
      [applicant.id]
    );
    applicant.qualifications = qualifications;
    applicant.workExperiences = workExperiences;
  }

  return applicants;
}

app.get('/export/pdf', async (req, res) => {
  try {
    const applicants = await getAllApplicantsWithDetails();

    const html = `
      <html>
      <head>
        <style>
          body { font-family: Arial, sans-serif; }
          h1, h2 { color: #333; }
          table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
          th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
          th { background-color: #f2f2f2; }
          .section { margin-bottom: 40px; }
        </style>
      </head>
      <body>
        <h1>Applicants Report</h1>
        ${applicants
          .map((app) => {
            const qualRows = app.qualifications
              .map(
                (q) => `
              <tr>
                <td>${q.degree}</td>
                <td>${q.university}</td>
                <td>${q.year}</td>
                
              </tr>`
              )
              .join('');

            const expRows = app.workExperiences
              .map(
                (e) => `
              <tr>
                <td>${e.organization}</td>
                <td>${e.role}</td>
                <td>${e.duration}</td>
               
              </tr>`
              )
              .join('');

            return `
            <div class="section">
              <h2>${app.first_name} ${app.middle_name} ${app.last_name} (${app.post_applied_for} - ${app.other_post_type})</h2>
              <p><strong>Email:</strong> ${app.email} | <strong>Phone:</strong> ${app.mobile_number}</p>
              <p><strong>D.O.B:</strong> ${app.dob}
              <p><strong>Address:</strong> ${app.address}</p>

              <h3>Qualifications</h3>
              <table>
                <tr>
                  <th>Exam Passed</th>
                  <th>Board/University</th>
                  <th>Year</th>
                 
                </tr>
                ${qualRows || '<tr><td colspan="4">No qualifications listed.</td></tr>'}
              </table>

              <h3>Work Experience</h3>
              <table>
                <tr>
                  <th>Organization</th>
                  <th>Role</th>
                  <th>Duration</th>
                
                </tr>
                ${expRows || '<tr><td colspan="4">No work experience listed.</td></tr>'}
              </table>

              <h3>Additiona Inforamtion</h3>
              <p><strong>Mother Tongue:</strong> ${app.mother_tongue} </p>
              <p><strong>Other Language:</strong> ${app.other_language}</p>
              <p><strong>English Typing Speed Per Minute:</strong> ${app.english_typing_speed}</p>
              <p><strong>Marathi Typing Speed Per Minute:</strong> ${app.marathi_typing_speed} </p>
              <p><strong>Joining Date If Selected:</strong> ${app.joining_date}</p>
              <p><strong>Expected Salary:</strong> ${app.expected_salary}</p>
              <p><strong>Current Salary:</strong> ${app.current_salary}</p>
              <p><strong>Comments:</strong> ${app.comments}</p>

              <h3>Resume</h3>
              <a href="${app.cv_file_path}">Download resume</a>
            </div>
          `;
          })
          .join('')}
      </body>
      </html>
    `;

    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.setContent(html, { waitUntil: 'networkidle0' });

    const pdfBuffer = await page.pdf({ format: 'A4', printBackground: true });

    await browser.close();

    res.set({
      'Content-Type': 'application/pdf',
      'Content-Disposition': 'attachment; filename="applicants.pdf"',
    });
    
    res.send(pdfBuffer);
  
  } catch (err) {
    console.error('PDF export error:', err);
    res.status(500).send('PDF export failed');
  }
});



app.listen(3000, () => console.log('✅ Server running on http://localhost:3000/export'));