import express from 'express';
import multer from 'multer';
import path from 'path';
import db from '../models/index.js';
const applicationRouter = express.Router();

const storage = multer.diskStorage({
  destination: './uploads/',
  filename: (_, file, cb) => {
    cb(null, Date.now() + '-' + file.originalname);
  },
});
const upload = multer({ storage });

// POST /api/apply
applicationRouter.post('/', upload.single('resume'), async (req, res) => {
  const t = await db.sequelize.transaction();
  try {
    console.log("Request Body:", req.body); 
    console.log("Uploaded File:", req.file);
    const {
      postAppliedFor,personalInfoTitle, firstName, middleName, lastName, gender,
      dob, email, altEmail, mobile, altMobile, address, state,
      maritalStatus,institute,age,
      city, pinCode, caste, aadhar, pan, declaration,
      qualifications, workExperiences, awardTitle, awardOrganizationName, awardNature, awardOrganizationRecognition, researchPapers,
      courseCollegeName,courseClassName, courseSubjectName,courseYearsOfExp,courseFromDate,courseToDate, courseDepartmentType, courseTypeOfContract, courseLastSalary, courseApprovedByUni,  courseLetterNumber,  courseLetterDate,
      phdStatus, phdUniversity, phdYear, netStatus, netYear, setStatus, setYear, referenceName, appliedFor, currentSalary, expectedSalary, extraCurricular
    } = req.body;

    const app = await db.Application.create({
      postAppliedFor,
      title : personalInfoTitle,
      firstName,
      middleName,
      lastName,
      gender,
      dob,
      age,
      email,
      altEmail,
      mobile,
      maritalStatus,
      institute,
      altMobile,
      address,
      state,
      city,
      pinCode,
      caste,
      aadhar,
      pan,
      declaration: declaration === 'true',
      resumeFile: req.file?.filename,
    }, { transaction: t });

    const applicationId = app.id;

    // Bulk create child tables
    if (qualifications) await db.Qualification.bulkCreate(
      JSON.parse(qualifications).map((q) => ({ ...q, applicationId })), { transaction: t }
    );
    if (workExperiences) await db.Experience.bulkCreate(
      JSON.parse(workExperiences).map((e) => ({ ...e, applicationId })), { transaction: t }
    );

    if (researchPapers) await db.Publication.bulkCreate(
      JSON.parse(researchPapers).map((p) => ({ ...p, applicationId })), { transaction: t }
    );
 
    //single recors
    if (awardTitle || awardOrganizationName || awardNature || awardOrganizationRecognition) {
      await db.Award.create({
        applicationId,
        awardTitle, 
        awardOrganizationName,
        awardNature,
        awardOrganizationRecognition,
      }, { transaction: t });
    }

   if (courseCollegeName || courseClassName || courseSubjectName || courseYearsOfExp) { 
        await db.Course.create({
            applicationId,
            courseCollegeName,
            courseClassName,
            courseSubjectName,
            courseYearsOfExp,
            courseFromDate,
            courseToDate,
            courseDepartmentType,
            courseTypeOfContract,
            courseLastSalary,
            courseApprovedByUni, 
            courseLetterNumber,
            courseLetterDate,
        }, { transaction: t });
    }

  if (referenceName || appliedFor || extraCurricular) {
      await db.AdditionalInfo.create({
        applicationId,
        referenceName,
        appliedFor,
        currentSalary,
        expectedSalary,
        extraCurricular,
      }, { transaction: t });
    }

 if (phdStatus && phdStatus !== 'not-applicable') {
      await db.Phd.create({
        applicationId,
        phdStatus,
        phdUniversity,
        phdYear,
        netYear: netStatus === 'yes' ? netYear : null,
        setYear: setStatus === 'yes' ? setYear : null,
      }, { transaction: t });
    }

    await t.commit();
    res.status(201).json({ message: 'Application submitted successfully' });
  } catch (err) {
    console.error(err);
    await t.rollback();
    res.status(500).json({ error: 'Internal server error' });
  }
});


export default applicationRouter;