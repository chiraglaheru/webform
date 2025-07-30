// Function to calculate age from DOB
function calculateAge() {
  const dobInput = document.getElementById('dob');
  const ageInput = document.getElementById('age');
  if (dobInput.value) {
    const birthDate = new Date(dobInput.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDifference = today.getMonth() - birthDate.getMonth();
    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    ageInput.value = age;
  } else {
    ageInput.value = '';
  }
}

// Attach the calculateAge function to the dob input's change event
document.addEventListener('DOMContentLoaded', () => {
  const dobInput = document.getElementById('dob');
  if (dobInput) {
    dobInput.addEventListener('change', calculateAge);
  }

  // Initial setup for gender, marital status, NET/SET, and work experience toggles
  selectGender('male'); // Default to Male or your preferred default
  selectMaritalStatus('unmarried'); // Default to Unmarried or your preferred default
  toggleExam('net', 'yes'); // Default to Yes
  toggleExam('set', 'yes'); // Default to Yes
  workExperience('experience'); // Default to 'Experience'
});


// Functions for toggle buttons (Gender, Marital Status, NET/SET, Work Experience)
function selectGender(gender) {
  document.getElementById('gender').value = gender;
  // Use a more specific selector to avoid interfering with other button groups
  document.querySelectorAll('.gender-toggle-group .toggle-btn').forEach(button => {
    if (button.dataset.value === gender) {
      button.classList.add('active');
    } else {
      button.classList.remove('active');
    }
  });
}

function selectMaritalStatus(status) {
  document.getElementById('maritalStatus').value = status;
  // Use a more specific selector
  document.querySelectorAll('.marital-status-toggle-group .toggle-btn').forEach(button => {
    if (button.dataset.value === status) {
      button.classList.add('active');
    } else {
      button.classList.remove('active');
    }
  });
}

function toggleExam(examType, status) {
  const statusInput = document.getElementById(`${examType}Status`);
  const yearInput = document.getElementById(`${examType}Year`);

  // Update hidden input value
  statusInput.value = status;

  // Update button active state
  document.querySelectorAll(`.${examType}-toggle-group .toggle-btn`).forEach(button => {
    if (button.dataset.value === status) {
      button.classList.add('active');
    } else {
      button.classList.remove('active');
    }
  });

  // Show/hide and set required for year input
  if (status === 'yes') {
    yearInput.style.display = 'inline-block';
    yearInput.setAttribute('required', 'true');
  } else {
    yearInput.style.display = 'none';
    yearInput.removeAttribute('required');
    yearInput.value = ''; // Clear the year if "No" is selected
  }
}

function workExperience(type) {
  const experienceContainer = document.getElementById('experienceContainer');
  const addExperienceSection = document.querySelector('.add-experience-section');
  const toggleButtons = document.querySelectorAll('.work-exp-toggle button');

  toggleButtons.forEach(button => {
    button.classList.remove('active');
  });

  const selectedButton = document.querySelector(`.work-exp-toggle button[data-value="${type}"]`);
  if (selectedButton) {
    selectedButton.classList.add('active');
  }

  const experienceFields = document.querySelectorAll('#experienceContainer input, #experienceContainer select');

  if (type === 'experience') {
    experienceContainer.style.display = 'block';
    addExperienceSection.style.display = 'block';
    experienceFields.forEach(field => {
      field.setAttribute('required', 'true');
    });
    // Ensure at least one experience item exists if it was somehow removed
    if (experienceContainer.children.length === 0) {
      addExperience(); // Add a default one if none exist
    }
  } else { // type === 'fresher'
    experienceContainer.style.display = 'none';
    addExperienceSection.style.display = 'none';
    experienceFields.forEach(field => {
      field.removeAttribute('required');
      field.value = ''; // Clear values when switching to Fresher
      if (field.type === 'checkbox') field.checked = false; // Uncheck checkboxes
    });
  }
  // Ensure remove buttons are updated
  updateRemoveButtons('experience');
}

// Functions for adding/removing dynamic sections
let qualificationIndex = 0;
function addQualification() {
  qualificationIndex++;
  const container = document.getElementById('qualificationsContainer');
  const newQualification = document.createElement('div');
  newQualification.classList.add('qualification-item');
  newQualification.dataset.index = qualificationIndex;
  newQualification.innerHTML = `
        <div class="form-row">
            <div class="form-field">
                <label>Degree<span class="required">*</span></label>
                <select class="degree-select" required>
                    <option value="">Select degree</option>
                    <option value="phd">Ph.D</option>
                    <option value="masters">Master's</option>
                    <option value="bachelors">Bachelor's</option>
                    <option value="diploma">Diploma</option>
                    <option value="certificate">Certificate</option>
                </select>
                <div class="error-message" style="display: none">Degree is required</div>
            </div>
            <div class="form-field">
                <label>Degree Name</label>
                <input type="text" class="degree-name" placeholder="Enter degree name" required/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>Education Mode<span class="required">*</span></label>
                <select class="education-mode" required>
                    <option value="">Select mode</option>
                    <option value="regular">Regular</option>
                    <option value="distance">Distance</option>
                    <option value="online">Online</option>
                    <option value="correspondence">Correspondence</option>
                </select>
                <div class="error-message" style="display: none">Education mode is required</div>
            </div>
            <div class="form-field">
                <label>University Name<span class="required">*</span></label>
                <input type="text" class="university-name" placeholder="Enter university name" required/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>Specialization</label>
                <input type="text" class="specialization" placeholder="Enter specialization"/>
            </div>
            <div class="form-field">
                <label>Year of Passing<span class="required">*</span></label>
                <input type="date" class="qualification-year-of-passing" required/>
            </div>
            <div class="form-field">
                <label>Percentage</label>
                <input type="tel" class="percentage" placeholder="Enter Percentage"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>CGPA</label>
                <input type="text" class="cgpa" placeholder="Enter CGPA"/>
            </div>
            <div class="form-field remove-btn-container">
                <button type="button" class="remove-qualification-btn" onclick="removeQualification(${qualificationIndex})">✕ Remove</button>
            </div>
        </div>
    `;
  container.appendChild(newQualification);
  updateRemoveButtons('qualification');
}

function removeQualification(index) {
  const item = document.querySelector(`.qualification-item[data-index="${index}"]`);
  if (item) {
    item.remove();
    updateRemoveButtons('qualification');
  }
}

let experienceIndex = 0;
function addExperience() {
  experienceIndex++;
  const container = document.getElementById('experienceContainer');
  const newExperience = document.createElement('div');
  newExperience.classList.add('experience-item');
  newExperience.dataset.index = experienceIndex;
  newExperience.innerHTML = `
        <div class="form-row">
            <div class="form-field">
                <label>Organization / University<span class="required">*</span></label>
                <input type="text" class="experience-organization" placeholder="Enter Organization / University" required/>
            </div>
            <div class="form-field">
                <label>Designation/Post held<span class="required">*</span></label>
                <input type="text" class="experience-designation" placeholder="Enter Designation/Post held" required/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>From Date<span class="required">*</span></label>
                <input type="date" class="experience-from" required/>
            </div>
            <div class="form-field">
                <label>To Date<span class="required">*</span></label>
                <input type="date" class="experience-to" required/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>Current Salary (Gross per month)</label>
                <input type="text" class="experience-salary" placeholder="Enter Current Salary"/>
            </div>
            <div class="form-field checkbox-field">
                <input type="checkbox" class="experience-current-role"/>
                <label>I am currently working in this role</label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field remove-btn-container">
                <button type="button" class="remove-experience-btn" onclick="removeExperience(this)">✕ Remove</button>
            </div>
        </div>
    `;
  container.appendChild(newExperience);
  updateRemoveButtons('experience');
}

function removeExperience(button) {
  const item = button.closest('.experience-item');
  if (item) {
    item.remove();
    updateRemoveButtons('experience');
  }
}

let researchPaperIndex = 0;
function addResearchPaper() {
  researchPaperIndex++;
  const container = document.getElementById('researchPapersContainer');
  const newResearchPaper = document.createElement('div');
  newResearchPaper.classList.add('research-paper-item');
  newResearchPaper.dataset.index = researchPaperIndex;
  newResearchPaper.innerHTML = `
        <div class="form-row">
            <div class="form-field">
                <label>Scopus Indexed Publications</label>
                <input type="text" class="scopus-publications" placeholder="Enter number of publications"/>
            </div>
            <div class="form-field">
                <label>Scopus ID</label>
                <input type="text" class="scopus-id" placeholder="Enter Scopus ID"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>Presented in Conference</label>
                <select class="conference-select">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div class="form-field">
                <label>Title Of The Paper</label>
                <input type="text" class="paper-title" placeholder="Enter journal title"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>Name of Journal</label>
                <input type="text" class="journal-name" placeholder="Enter journal name"/>
            </div>
            <div class="form-field">
                <label>Year of Publication</label>
                <input type="date" class="publication-year"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>Number of Approved Papers</label>
                <input type="number" class="approved-papers" placeholder="Enter number of approved papers"/>
            </div>
            <div class="form-field remove-btn-container">
                <button type="button" class="remove-research-paper-btn" onclick="removeResearchPaper(${researchPaperIndex})">✕ Remove</button>
            </div>
        </div>
    `;
  container.appendChild(newResearchPaper);
  updateRemoveButtons('research-paper');
}

function removeResearchPaper(index) {
  const item = document.querySelector(`.research-paper-item[data-index="${index}"]`);
  if (item) {
    item.remove();
    updateRemoveButtons('research-paper');
  }
}

// Function to control visibility of remove buttons
function updateRemoveButtons(type) {
  let items;
  let removeButtonClass;
  if (type === 'qualification') {
    items = document.querySelectorAll('.qualification-item');
    removeButtonClass = '.remove-qualification-btn';
  } else if (type === 'experience') {
    items = document.querySelectorAll('.experience-item');
    removeButtonClass = '.remove-experience-btn';
  } else if (type === 'research-paper') {
    items = document.querySelectorAll('.research-paper-item');
    removeButtonClass = '.remove-research-paper-btn';
  }

  items.forEach((item, index) => {
    const removeBtn = item.querySelector(removeButtonClass);
    if (removeBtn) {
      if (items.length > 1) {
        removeBtn.style.display = 'block';
      } else {
        removeBtn.style.display = 'none';
      }
    }
  });
}

// Ph.D. fields update
function updatePhdFields() {
    const status = document.getElementById("phdStatus").value;
    const university = document.getElementById("phdUniversity");
    const year = document.getElementById("phdYear");

    if (status === "completed") {
      university.disabled = false;
      year.disabled = false;
    } else if (status === "pursuing" || status === "thesis-submitted") {
      university.disabled = false;
      year.disabled = true;
      year.value = "";
    } else if (status === "not-applicable") {
      university.disabled = true;
      university.value = "";
      year.disabled = true;
      year.value = "";
    } else {
      university.disabled = false;
      year.disabled = false;
    
  };
}

// Resume Upload
function handleFileUpload(input) {
  const uploadText = document.getElementById('uploadText');
  const fileError = document.getElementById('fileError');
  const file = input.files[0];

  if (file) {
    // Check file type
    if (file.type !== 'application/pdf') {
      fileError.textContent = 'Please upload a PDF file.';
      fileError.style.display = 'block';
      input.value = ''; // Clear the selected file
      uploadText.textContent = 'Upload RESUME';
      return;
    }

    // Check file size (5MB limit)
    const maxSize = 5 * 1024 * 1024; // 5 MB in bytes
    if (file.size > maxSize) {
      fileError.textContent = 'File size exceeds 5MB. Please upload a smaller PDF.';
      fileError.style.display = 'block';
      input.value = ''; // Clear the selected file
      uploadText.textContent = 'Upload RESUME';
      return;
    }

    uploadText.textContent = `File Selected: ${file.name}`;
    fileError.style.display = 'none';
  } else {
    uploadText.textContent = 'Upload RESUME';
    fileError.style.display = 'none';
  }
}

// Populate Indian States (simple example, you might fetch this from an API)
const indianStates = [
  "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh",
  "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka",
  "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram",
  "Nagaland", "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu",
  "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"
];

document.addEventListener('DOMContentLoaded', () => {
  const stateSelect = document.getElementById('state');
  if (stateSelect) {
    indianStates.forEach(state => {
      const option = document.createElement('option');
      option.value = state;
      option.textContent = state;
      stateSelect.appendChild(option);
    });
  }

  // Initial update for all dynamic sections on page load
  updateRemoveButtons('qualification');
  updateRemoveButtons('experience');
  updateRemoveButtons('research-paper');
  updatePhdFields(); // Call on page load to set initial state
});

// --- Submission Logic ---

document.getElementById('applicationForm').addEventListener('submit', async function(event) {
  event.preventDefault(); // Prevent default form submission

  // Basic validation
  const declarationCheckbox = document.getElementById('declaration');
  if (!declarationCheckbox.checked) {
    alert('Please agree to the declaration before submitting.');
    return;
  }

  const formData = new FormData();

  // --- Collect data from various sections ---

  // Post Applied For
  formData.append('postApplied', document.getElementById('postApplied').value);

  // Personal Information
  formData.append('title', document.getElementById('personalInfoTitle').value); // Unique ID
  formData.append('firstName', document.getElementById('firstName').value);
  formData.append('middleName', document.getElementById('middleName').value);
  formData.append('lastName', document.getElementById('lastName').value);
  formData.append('dob', document.getElementById('dob').value);
  formData.append('age', document.getElementById('age').value);
  formData.append('gender', document.getElementById('gender').value);
  formData.append('maritalStatus', document.getElementById('maritalStatus').value);
  formData.append('email', document.getElementById('email').value);
  formData.append('altEmail', document.getElementById('altEmail').value);
  formData.append('caste', document.getElementById('caste').value);
  formData.append('aadhar', document.getElementById('aadhar').value);
  formData.append('pan', document.getElementById('pan').value);
  formData.append('state', document.getElementById('state').value);
  formData.append('city', document.getElementById('city').value);
  formData.append('address', document.getElementById('address').value); // Corrected ID
  formData.append('pinCode', document.getElementById('pinCode').value);
  formData.append('mobile', document.getElementById('mobile').value);
  formData.append('altMobile', document.getElementById('altMobile').value);
  formData.append('institute', document.getElementById('institute').value);

  // Qualifications (collect as an array of objects)
  const qualifications = [];
  document.querySelectorAll('.qualification-item').forEach(item => {
    const degree = item.querySelector('.degree-select').value;
    const degreeName = item.querySelector('.degree-name').value;
    const educationMode = item.querySelector('.education-mode').value;
    const universityName = item.querySelector('.university-name').value;
    const specialization = item.querySelector('.specialization').value;
    const yearOfPassing = item.querySelector('.qualification-year-of-passing').value; // Corrected class
    const percentage = item.querySelector('.percentage').value;
    const cgpa = item.querySelector('.cgpa').value;

    qualifications.push({
      degree,
      degreeName,
      educationMode,
      universityName,
      specialization,
      yearOfPassing,
      percentage,
      cgpa
    });
  });
  formData.append('qualifications', JSON.stringify(qualifications)); // Send as JSON string

  // Ph.D.
  formData.append('phdStatus', document.getElementById('phdStatus').value);
  formData.append('phdUniversity', document.getElementById('phdUniversity').value);
  formData.append('phdYear', document.getElementById('phdYear').value);

  // NET/SET Exams
  formData.append('netStatus', document.getElementById('netStatus').value);
  if (document.getElementById('netStatus').value === 'yes') {
    formData.append('netYear', document.getElementById('netYear').value);
  }
  formData.append('setStatus', document.getElementById('setStatus').value);
  if (document.getElementById('setStatus').value === 'yes') {
    formData.append('setYear', document.getElementById('setYear').value);
  }

  // Work Experience (collect as an array of objects)
  const workExperiences = [];
  const activeWorkExpButton = document.querySelector('.work-exp-toggle .active');
  const workExpType = activeWorkExpButton ? activeWorkExpButton.dataset.value : 'fresher'; // Fallback to 'fresher'

  formData.append('workExperienceType', workExpType);

  if (workExpType === 'experience') {
    document.querySelectorAll('.experience-item').forEach(item => {
      const organization = item.querySelector('.experience-organization').value;
      const designation = item.querySelector('.experience-designation').value;
      const fromDate = item.querySelector('.experience-from').value;
      const toDate = item.querySelector('.experience-to').value;
      const salary = item.querySelector('.experience-salary').value;
      const currentRole = item.querySelector('.experience-current-role').checked;

      workExperiences.push({
        organization,
        designation,
        fromDate,
        toDate,
        salary,
        currentRole
      });
    });
  }
  formData.append('workExperiences', JSON.stringify(workExperiences)); // Send as JSON string

  // Courses Taught (Unique IDs now)
  formData.append('courseCollegeName', document.getElementById('courseCollegeName').value);
  formData.append('courseClassName', document.getElementById('courseClassName').value);
  formData.append('courseSubjectName', document.getElementById('courseSubjectName').value);
  formData.append('courseYearsOfExp', document.getElementById('courseYearsOfExp').value);
  formData.append('courseFromDate', document.getElementById('courseFromDate').value);
  formData.append('courseToDate', document.getElementById('courseToDate').value);
  formData.append('courseDepartmentType', document.getElementById('courseDepartmentType').value);
  formData.append('courseTypeOfContract', document.getElementById('courseTypeOfContract').value);
  formData.append('courseLastSalary', document.getElementById('courseLastSalary').value);
  formData.append('courseApprovedByUni', document.getElementById('courseApprovedByUni').value);
  formData.append('courseLetterNumber', document.getElementById('courseLetterNumber').value);
  formData.append('courseLetterDate', document.getElementById('courseLetterDate').value);

  // Research Papers (collect as an array of objects)
  const researchPapers = [];
  document.querySelectorAll('.research-paper-item').forEach(item => {
    const scopusPublications = item.querySelector('.scopus-publications').value;
    const scopusId = item.querySelector('.scopus-id').value;
    const presentedInConference = item.querySelector('.conference-select').value;
    const paperTitle = item.querySelector('.paper-title').value;
    const journalName = item.querySelector('.journal-name').value;
    const publicationYear = item.querySelector('.publication-year').value;
    const approvedPapers = item.querySelector('.approved-papers').value;

    researchPapers.push({
      scopusPublications,
      scopusId,
      presentedInConference,
      paperTitle,
      journalName,
      publicationYear,
      approvedPapers
    });
  });
  formData.append('researchPapers', JSON.stringify(researchPapers)); // Send as JSON string

  // Awards (Unique IDs now)
  formData.append('awardTitle', document.getElementById('awardTitle').value);
  formData.append('awardOrganizationName', document.getElementById('awardOrganizationName').value);
  formData.append('awardNature', document.getElementById('awardNature').value); // Corrected ID
  formData.append('awardOrganizationRecognition', document.getElementById('awardOrganizationRecognition').value);

  // Additional Information (Unique IDs now for referenceName)
  formData.append('referenceName', document.getElementById('referenceName').value);
  formData.append('appliedFor', document.getElementById('appliedFor').value);
  formData.append('currentSalary', document.getElementById('currentSalary').value);
  formData.append('expectedSalary', document.getElementById('expectedSalary').value);
  formData.append('extraCurricular', document.getElementById('extraCurricular').value);

  // Resume Upload
  const resumeFile = document.getElementById('resumeUpload').files[0];
  if (resumeFile) {
    formData.append('resume', resumeFile);
  }

  // --- Send data to backend ---
  const backendUrl = 'http://localhost:5000/api/apply'; // **IMPORTANT: Replace with your actual backend endpoint**

  try {
    const response = await fetch(backendUrl, {
      method: 'POST',
      body: formData // FormData will automatically set the Content-Type to multipart/form-data
    });
    console.log(response)
    if (response.ok) {
      const result = await response.json();
      console.log('Submission successful:', result);
      alert('Application submitted successfully!');
      this.reset(); // Clear the form after successful submission
      // Optionally, redirect the user or show a success message
    } else {
      const errorData = await response.json();
      console.error('Submission failed:', response.status, errorData);
      alert(`Submission failed: ${errorData.message || 'An error occurred.'}`);
    }
  } catch (error) {
    console.error('Error submitting form:', error);
    alert('An unexpected error occurred. Please try again later.');
  }
});