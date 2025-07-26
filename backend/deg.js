

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

    const [applicants] = await connection.execute('SELECT * FROM degree_applications');
    const [awards] = await connection.execute('SELECT * FROM degree_awards');
    const [courses_taught] = await connection.execute('SELECT * FROM degree_courses_taught');
    const [phd_details] = await connection.execute('SELECT * FROM degree_phd_details');
    const [qualifications] = await connection.execute('SELECT * FROM degree_qualifications');
    const [research_publications] = await connection.execute('SELECT * FROM degree_research_publications');
    const [work_experience] = await connection.execute('SELECT * FROM degree_work_experience');

    const workbook = new ExcelJS.Workbook();

    // 📄 Applicants
    const applicantSheet = workbook.addWorksheet('Applicants');
applicantSheet.columns = [
  { header: 'ID', key: 'id' },
  { header: 'Application Date', key: 'application_date' },
  { header: 'Post Applied For', key: 'post_applied_for' },
  { header: 'Title', key: 'title' },
  { header: 'First Name', key: 'first_name' },
  { header: 'Middle Name', key: 'middle_name' },
  { header: 'Last Name', key: 'last_name' },
  { header: 'Date of Birth', key: 'dob' },
  { header: 'Age', key: 'age' },
  { header: 'Gender', key: 'gender' },
  { header: 'Marital Status', key: 'marital_status' },
  { header: 'Email', key: 'email' },
  { header: 'Alternate Email', key: 'alternate_email' },
  { header: 'Caste/Subcaste', key: 'caste_subcaste' },
  { header: 'Aadhar Number', key: 'aadhar_no' },
  { header: 'PAN Number', key: 'pan_no' },
  { header: 'State', key: 'state' },
  { header: 'City', key: 'city' },
  { header: 'Address', key: 'address' },
  { header: 'Pincode', key: 'pincode' },
  { header: 'Mobile Number', key: 'mobile_no' },
  { header: 'Alternate Mobile', key: 'alternate_mobile_no' },
  { header: 'Institute Applied To', key: 'institute_applied_to' },
  { header: 'NET Status', key: 'net_status' },
  { header: 'NET Year', key: 'net_year' },
  { header: 'SET Status', key: 'set_status' },
  { header: 'SET Year', key: 'set_year' },
  { header: 'Current Salary', key: 'current_salary' },
  { header: 'Expected Salary', key: 'expected_salary' },
  { header: 'Scopus Publications', key: 'scopus_publications' },
  { header: 'Scopus ID', key: 'scopus_id' },
  { header: 'Conference Presented', key: 'conference_presented' },
  { header: 'Approved Papers', key: 'approved_papers' },
  { header: 'Reference Name', key: 'reference_name' },
  { header: 'Applied Position', key: 'applied_for_position' },
  { header: 'Extracurricular', key: 'extracurricular' },
  { header: 'Resume Filename', key: 'resume_filename' },
  { header: 'Declaration Accepted', key: 'declaration_accepted' }
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
      { header: ' Recognition', key: 'recognition' }
    ];
awards.forEach(row => awardsSheet.addRow(row));

    // 💼 Courses taught
    const teachingExpSheet = workbook.addWorksheet('Teaching Experience');
teachingExpSheet.columns = [
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
    courses_taught.forEach(row => teachingExpSheet.addRow(row));

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
  { header: 'ID', key: 'id', type: 'int' },
  { header: 'Application ID', key: 'application_id', type: 'int' },
  { header: 'Degree', key: 'degree', type: 'varchar' },
  { header: 'Degree Name', key: 'degree_name', type: 'varchar' },
  { header: 'Education Mode', key: 'education_mode', type: 'varchar' },
  { header: 'University', key: 'university', type: 'varchar' },
  { header: 'Specialization', key: 'specialization', type: 'varchar' },
  { header: 'Year of Passing', key: 'year_of_passing', type: 'varchar' },
  { header: 'Percentage', key: 'percentage', type: 'decimal' },
  { header: 'CGPA', key: 'cgpa', type: 'decimal' }
];
qualifications.forEach(row => qualificationsSheet.addRow(row));

//researchpublications

const researchSheet = workbook.addWorksheet('Research Publications');
researchSheet.columns = [
  { header: 'ID', key: 'id', type: 'int' },
  { header: 'Application ID', key: 'application_id', type: 'int' },
  { header: 'Title', key: 'title', type: 'varchar' },
  { header: 'Journal Name', key: 'journal_name', type: 'varchar' },
  { header: 'Year of Publication', key: 'year_of_publication', type: 'year' },
  { header: 'Scopus Publications', key: 'scopus_publications', type: 'int' },
  { header: 'Scopus ID', key: 'scopus_id', type: 'varchar' },
  { header: 'Conference Presented', key: 'conference_presented', type: 'text' },
  { header: 'Approved Papers', key: 'approved_papers', type: 'int' }
];
research_publications.forEach(row => researchSheet.addRow(row));

//work exp

const professionalExpSheet = workbook.addWorksheet('Professional Experience');
professionalExpSheet.columns = [
  { header: 'ID', key: 'id', type: 'int' },
  { header: 'Application ID', key: 'application_id', type: 'int' },
  { header: 'Organization/University', key: 'organization_university', type: 'varchar' },
  { header: 'Designation/Post', key: 'designation_post', type: 'varchar' },
  { header: 'From Date', key: 'from_date', type: 'date' },
  { header: 'To Date', key: 'to_date', type: 'date' },
  { header: 'Currently Working', key: 'currently_working', type: 'tinyint' },
  { header: 'Salary', key: 'salary', type: 'decimal' }
];
work_experience.forEach(row => professionalExpSheet.addRow(row));


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