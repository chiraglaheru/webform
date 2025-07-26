

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

    const [applicants] = await connection.execute('SELECT * FROM non_teaching_applications');
    const [qualifications] = await connection.execute('SELECT * FROM nt_qualifications');
    const [experience] = await connection.execute('SELECT * FROM nt_experience');

    const workbook = new ExcelJS.Workbook();

    // 📄 Applicants
    const applicantSheet = workbook.addWorksheet('Applicants');
    applicantSheet.columns = [
      { header: 'ID', key: 'id' },
      { header: 'Post', key: 'post_applied_for' },
      { header: 'Other Post', key: 'other_post_type' },
      { header: 'First Name', key: 'first_name' },
      { header: 'Middle Name', key: 'middle_name' },
      { header: 'Last Name', key: 'last_name' },
      { header: 'Email', key: 'email' },
      { header: 'Mobile', key: 'mobile_number' },
      { header: 'Address', key: 'address' },
      { header: 'DOB', key: 'dob' },
      { header: 'Mother Tongue', key: 'mother_tongue' },
      { header: 'Other Language', key: 'other_language' },
      { header: 'Eng Typing Speed', key: 'typing_speed_english' },
      { header: 'Mar Typing Speed', key: 'typing_speed_marathi' },
      { header: 'Joining Date', key: 'joining_date' },
      { header: 'Expected Salary', key: 'expected_salary' },
      { header: 'Current Salary', key: 'current_salary' },
      { header: 'Comments', key: 'comments' },
    ];
    applicants.forEach(row => applicantSheet.addRow(row));

    // 🎓 Qualifications
    const qualSheet = workbook.addWorksheet('Qualifications');
    qualSheet.columns = [
      { header: 'ID', key: 'id' },
      { header: 'Applicant ID', key: 'application_id' },
      { header: 'Degree', key: 'degree' },
      { header: 'University', key: 'university' },
      { header: 'Year', key: 'year' },
      { header: 'Percentage', key: 'percentage' },
    ];
    qualifications.forEach(row => qualSheet.addRow(row));

    // 💼 Work Experience
    const expSheet = workbook.addWorksheet('Work Experience');
    expSheet.columns = [
      { header: 'ID', key: 'id' },
      { header: 'Applicant ID', key: 'application_id' },
      { header: 'Organization', key: 'organization' },
      { header: 'designation', key: 'designation' },
      { header: 'from_date', key: 'from_date' },
      { header: 'to_date', key: 'to_date' },
      { header: 'salary', key: 'salary' },
    ];
    experience.forEach(row => expSheet.addRow(row));

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