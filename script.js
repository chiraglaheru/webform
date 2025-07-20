
document.addEventListener("DOMContentLoaded", () => {
  // Gender toggle logic
  window.selectGender = function (value) {
    const genderButtons = document.querySelectorAll('[onclick^="selectGender"]');
    genderButtons.forEach((btn) => btn.classList.remove("active"));
    const selected = document.querySelector(`[onclick="selectGender('${value}')"]`);
    if (selected) selected.classList.add("active");
    document.getElementById("gender").value = value;
  };

  // Marital status toggle
  window.selectMaritalStatus = function (value) {
    const statusButtons = document.querySelectorAll('[onclick^="selectMaritalStatus"]');
    statusButtons.forEach((btn) => btn.classList.remove("active"));
    const selected = document.querySelector(`[onclick="selectMaritalStatus('${value}')"]`);
    if (selected) selected.classList.add("active");
    document.getElementById("maritalStatus").value = value;
  };

  // Paper Presented toggle
  window.togglePaperPresented = function (value) {
    const buttons = document.querySelectorAll('[onclick^="togglePaperPresented"]');
    buttons.forEach((btn) => btn.classList.remove("active"));
    const selected = document.querySelector(`[onclick="togglePaperPresented('${value}')"]`);
    if (selected) selected.classList.add("active");
    document.getElementById("paperPresented").value = value;
  };

  // Exam toggle (NET/SET/SLET/GATE)
  window.toggleExam = function (exam, value) {
    const yesBtn = document.querySelector(`[onclick="toggleExam('${exam}', 'yes')"]`);
    const noBtn = document.querySelector(`[onclick="toggleExam('${exam}', 'no')"]`);
    const yearInput = document.getElementById(`${exam}Year`);
    const statusInput = document.getElementById(`${exam}Status`);

    if (value === "yes") {
      yearInput.disabled = false;
      yesBtn.classList.add("active");
      noBtn.classList.remove("active");
    } else {
      yearInput.disabled = true;
      yearInput.value = "";
      yesBtn.classList.remove("active");
      noBtn.classList.add("active");
    }
    statusInput.value = value;
  };

  // Work experience toggle
  window.toggleWorkExperience = function (value) {
    const experienceSection = document.getElementById("experienceFields");
    const buttons = document.querySelectorAll(".work-exp-toggle .toggle-btn");

    buttons.forEach((btn) => btn.classList.remove("active"));
    const selected = document.querySelector(`[onclick="toggleWorkExperience('${value}')"]`);
    if (selected) selected.classList.add("active");

    experienceSection.style.display = value === "experience" ? "block" : "none";
  };

  // Ph.D. section logic

  document.addEventListener("DOMContentLoaded", () => {
    const phdStatusEl = document.getElementById("phdStatus");
    const phdUniversityField = document.getElementById("phdUniversityField");
    const phdYearField = document.getElementById("phdYearField");
  
    if (!phdStatusEl || !phdUniversityField || !phdYearField) {
      console.warn("Ph.D. fields not found in the DOM.");
      return;
    }
  
    function updatePhdFields() {
      const status = phdStatusEl.value;
      const shouldShow = status !== "not-applicable" && status !== "";
      phdUniversityField.style.display = shouldShow ? "block" : "none";
      phdYearField.style.display = shouldShow ? "block" : "none";
    }
  
    // Run on change and once on load
    phdStatusEl.addEventListener("change", updatePhdFields);
    updatePhdFields();
  });
  
  


  // window.updatePhdFields = function () {
  //   const status = document.getElementById("phdStatus").value;
  //   const university = document.getElementById("phdUniversity");
  //   const year = document.getElementById("phdYear");

  //   if (status === "completed") {
  //     university.disabled = false;
  //     year.disabled = false;
  //   } else if (status === "pursuing" || status === "thesis-submitted") {
  //     university.disabled = false;
  //     year.disabled = true;
  //     year.value = "";
  //   } else if (status === "not-applicable") {
  //     university.disabled = true;
  //     university.value = "";
  //     year.disabled = true;
  //     year.value = "";
  //   } else {
  //     university.disabled = false;
  //     year.disabled = false;
  //   }
  // };

  // Resume upload feedback
  window.handleFileUpload = function (input) {
    const file = input.files[0];
    const errorText = document.getElementById("fileError");
    const uploadText = document.getElementById("uploadText");

    if (file) {
      if (file.size > 5 * 1024 * 1024) {
        errorText.style.display = "block";
        errorText.textContent = "File size exceeds 5MB.";
        input.value = "";
        uploadText.textContent = "BROWSE RESUME";
      } else if (!file.name.endsWith(".pdf")) {
        errorText.style.display = "block";
        errorText.textContent = "Only PDF files are allowed.";
        input.value = "";
        uploadText.textContent = "BROWSE RESUME";
      } else {
        errorText.style.display = "none";
        uploadText.textContent = file.name;
      }
    }
  };

  // Age auto-calculation
  document.getElementById("dob").addEventListener("change", function () {
    const dob = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
      age--;
    }
    document.getElementById("age").value = age > 0 ? age : "";
  });


// Replace the existing code with this robust version
function initializeExams() {
    const exams = ['net', 'set']; // Only include exams that exist in your HTML
    exams.forEach((exam) => {
        const element = document.getElementById(`${exam}Status`);
        if (!element) {
            console.warn(`${exam}Status element not found`);
            return;
        }
        try {
            toggleExam(exam, element.value);
        } catch (e) {
            console.error(`Error initializing ${exam}:`, e);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize exams first
    initializeExams();
    
    // Then setup other event listeners
    const addExperienceBtn = document.querySelector('.add-qualification-btn'); // Note: using your actual HTML class
    if (addExperienceBtn) {
        addExperienceBtn.addEventListener('click', addExperience);
    } else {
        console.warn('Add experience button not found');
    }
    
    // Rest of your initialization code...
});

const indianStates = [
  "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh",
  "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand",
  "Karnataka", "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur",
  "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab",
  "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura",
  "Uttar Pradesh", "Uttarakhand", "West Bengal",
  "Andaman and Nicobar Islands", "Chandigarh", "Dadra and Nagar Haveli and Daman and Diu",
  "Delhi", "Jammu and Kashmir", "Ladakh", "Lakshadweep", "Puducherry"
];

const stateSelect = document.getElementById("state");
indianStates.forEach((state) => {
  const option = document.createElement("option");
  option.value = state;
  option.textContent = state;
  stateSelect.appendChild(option);
});

 function addQualification() {
    const container = document.getElementById("qualificationsContainer");
    const items = container.querySelectorAll(".qualification-item");
    const newIndex = items.length;

    // Clone the first item
    const newItem = items[0].cloneNode(true);

    // Clear all input fields in the clone
    newItem.querySelectorAll("input, select").forEach((el) => {
      if (el.tagName.toLowerCase() === "select") {
        el.selectedIndex = 0;
      } else {
        el.value = "";
      }
    });

    // Update data-index and button onclick
    newItem.setAttribute("data-index", newIndex);
    newItem.querySelector(".remove-qualification-btn").setAttribute(
      "onclick",
      `removeQualification(${newIndex})`
    );

    container.appendChild(newItem);

    updateRemoveButtons();
  }

  function removeQualification(index) {
    const container = document.getElementById("qualificationsContainer");
    const items = container.querySelectorAll(".qualification-item");

    if (items.length > 1) {
      items[index].remove();

      // Re-index all remaining items
      const updatedItems = container.querySelectorAll(".qualification-item");
      updatedItems.forEach((item, idx) => {
        item.setAttribute("data-index", idx);
        item.querySelector(".remove-qualification-btn").setAttribute(
          "onclick",
          `removeQualification(${idx})`
        );
      });

      updateRemoveButtons();
    }
  }

  function updateRemoveButtons() {
    const items = document.querySelectorAll(".qualification-item");
    items.forEach((item, index) => {
      const removeBtn = item.querySelector(".remove-qualification-btn");
      if (items.length === 1) {
        removeBtn.style.display = "none";
      } else {
        removeBtn.style.display = "block";
      }
    });
  }

  // Initial call on page load (optional)
  window.onload = function () {
    updateRemoveButtons();
  };


  function addResearchPaper() {
    const container = document.getElementById("researchPapersContainer");
    const items = container.querySelectorAll(".research-paper-item");
    const newIndex = items.length;

    // Clone the first item
    const newItem = items[0].cloneNode(true);

    // Clear all input and select fields in the clone
    newItem.querySelectorAll("input, select").forEach((el) => {
      if (el.tagName.toLowerCase() === "select") {
        el.selectedIndex = 0;
      } else {
        el.value = "";
      }
    });

    // Update data-index and remove button's onclick
    newItem.setAttribute("data-index", newIndex);
    newItem.querySelector(".remove-research-paper-btn").setAttribute(
      "onclick",
      `removeResearchPaper(${newIndex})`
    );

    container.appendChild(newItem);

    updateResearchPaperRemoveButtons();
  }

  function removeResearchPaper(index) {
    const container = document.getElementById("researchPapersContainer");
    const items = container.querySelectorAll(".research-paper-item");

    if (items.length > 1) {
      items[index].remove();

      // Re-index remaining items and update their remove buttons
      const updatedItems = container.querySelectorAll(".research-paper-item");
      updatedItems.forEach((item, idx) => {
        item.setAttribute("data-index", idx);
        item.querySelector(".remove-research-paper-btn").setAttribute(
          "onclick",
          `removeResearchPaper(${idx})`
        );
      });

      updateResearchPaperRemoveButtons();
    }
  }

  function updateResearchPaperRemoveButtons() {
    const items = document.querySelectorAll(".research-paper-item");
    items.forEach((item) => {
      const removeBtn = item.querySelector(".remove-research-paper-btn");
      removeBtn.style.display = items.length > 1 ? "block" : "none";
    });
  }

  // On page load
  window.onload = function () {
    updateResearchPaperRemoveButtons();
  };

function addExperience() {
  const container = document.getElementById("experienceContainer");
  const items = container.querySelectorAll(".experience-item");
  const newIndex = items.length;

  const newItem = items[0].cloneNode(true);
  newItem.setAttribute("data-index", newIndex);

  // Clear inputs
  newItem.querySelectorAll("input").forEach(input => {
    if (input.type === "checkbox") input.checked = false;
    else input.value = "";
  });

  // Show remove button
  const removeBtn = newItem.querySelector(".remove-experience-btn");
  removeBtn.style.display = "inline-block";
  removeBtn.onclick = () => newItem.remove();

  container.appendChild(newItem);
}

function removeExperience(btn) {
  const container = document.getElementById("experienceContainer");
  const allItems = container.querySelectorAll(".experience-item");
  if (allItems.length > 1) {
    btn.closest(".experience-item").remove();
  } else {
    alert("At least one work experience entry is required.");
  }
}

document.getElementById('applicationForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Calculate age from DOB
  const dob = new Date(document.getElementById('dob').value);
  const ageDiff = Date.now() - dob.getTime();
  const ageDate = new Date(ageDiff);
  const calculatedAge = Math.abs(ageDate.getUTCFullYear() - 1970);
  document.getElementById('age').value = calculatedAge;


  // Collect all form data
  const formData = new FormData(this);

  // Add qualifications data
  const qualificationItems = document.querySelectorAll('.qualification-item');
  qualificationItems.forEach((item, index) => {
      formData.append(`degree[${index}]`, item.querySelector('.degree-select').value);
      formData.append(`degree_name[${index}]`, item.querySelector('.degree-name').value);
      formData.append(`education_mode[${index}]`, item.querySelector('.education-mode').value);
      formData.append(`university_name[${index}]`, item.querySelector('.university-name').value);
      formData.append(`specialization[${index}]`, item.querySelector('.specialization').value);
      formData.append(`year_of_passing[${index}]`, item.querySelector('.year-passing').value);
      formData.append(`percentage[${index}]`, item.querySelector('.percentage').value);
      formData.append(`cgpa[${index}]`, item.querySelector('.cgpa').value);
  });

  // Add work experience data
  const experienceItems = document.querySelectorAll('.experience-item');
  experienceItems.forEach((item, index) => {
      formData.append(`organization[${index}]`, item.querySelector('.experience-organization').value);
      formData.append(`designation[${index}]`, item.querySelector('.experience-designation').value);
      formData.append(`from_date[${index}]`, item.querySelector('.experience-from').value);
      formData.append(`to_date[${index}]`, item.querySelector('.experience-to').value);
      formData.append(`currently_working[${index}]`, item.querySelector('.experience-current-role').checked ? '1' : '0');
      formData.append(`salary[${index}]`, item.querySelector('.experience-salary').value);
  });

  // Add research papers data
  const researchItems = document.querySelectorAll('.research-paper-item');
  researchItems.forEach((item, index) => {
      formData.append(`paper_title[${index}]`, item.querySelector('.paper-title').value);
      formData.append(`journal_name[${index}]`, item.querySelector('.journal-name').value);
      formData.append(`year_of_publication[${index}]`, item.querySelector('.publication-year').value);
  });

  // Submit form via AJAX
  fetch('process_degree.php', {
      method: 'POST',
      body: formData
  })
  .then(response => {
      if (response.ok) {
          window.location.href = 'application_success.html';
      } else {
          alert('Error submitting form');
      }
  })
  .catch(error => {
      console.error('Error:', error);
      alert('Error submitting form');
  });
});

// Function to add qualification fields
function addQualification() {
  const container = document.getElementById('qualificationsContainer');
  const index = container.children.length;
  const newQual = document.createElement('div');
  newQual.className = 'qualification-item';
  newQual.dataset.index = index;
  newQual.innerHTML = `
      <div class="form-row">
          <div class="form-field">
              <label>Degree<span class="required">*</span></label>
              <select class="degree-select" required>
                  <option value="">Select degree</option>
                  <option value="phd">Ph.D</option>
                  <option value="masters">Master's</option>
                  <option value="bachelors">Bachelor's</option>
              </select>
          </div>
          <div class="form-field">
              <label>Degree Name</label>
              <input type="text" class="degree-name" placeholder="Enter degree name">
          </div>
      </div>
      <div class="form-row">
          <div class="form-field">
              <label>Education Mode<span class="required">*</span></label>
              <select class="education-mode" required>
                  <option value="">Select mode</option>
                  <option value="regular">Regular</option>
                  <option value="distance">Distance</option>
              </select>
          </div>
          <div class="form-field">
              <label>University Name<span class="required">*</span></label>
              <input type="text" class="university-name" placeholder="Enter university name" required>
          </div>
      </div>
      <div class="form-row">
          <div class="form-field">
              <label>Specialization</label>
              <input type="text" class="specialization" placeholder="Enter specialization">
          </div>
          <div class="form-field">
              <label>Year of Passing<span class="required">*</span></label>
              <input type="date" class="year-passing" required>
          </div>
      </div>
      <div class="form-row">
          <div class="form-field">
              <label>Percentage</label>
              <input type="number" step="0.01" class="percentage" placeholder="Enter Percentage">
          </div>
          <div class="form-field">
              <label>CGPA</label>
              <input type="number" step="0.1" class="cgpa" placeholder="Enter CGPA">
          </div>
          <div class="form-field remove-btn-container">
              <button type="button" class="remove-qualification-btn" onclick="removeQualification(${index})">
                  ✕ Remove
              </button>
          </div>
      </div>
  `;
  container.appendChild(newQual);
}

// Function to remove qualification
function removeQualification(index) {
  const item = document.querySelector(`.qualification-item[data-index="${index}"]`);
  if (item) item.remove();
  // Reindex remaining items
  const items = document.querySelectorAll('.qualification-item');
  items.forEach((item, i) => {
      item.dataset.index = i;
      const btn = item.querySelector('.remove-qualification-btn');
      if (btn) btn.onclick = () => removeQualification(i);
  });
}

// Similar functions for adding/removing work experience and research papers
function addExperience() {
  const container = document.getElementById('experienceContainer');
  const index = container.children.length;
  const newExp = document.createElement('div');
  newExp.className = 'experience-item';
  newExp.dataset.index = index;
  newExp.innerHTML = `
      <div class="form-row">
          <div class="form-field">
              <label>Organization<span class="required">*</span></label>
              <input type="text" class="experience-organization" required>
          </div>
          <div class="form-field">
              <label>Designation<span class="required">*</span></label>
              <input type="text" class="experience-designation" required>
          </div>
      </div>
      <div class="form-row">
          <div class="form-field">
              <label>From Date<span class="required">*</span></label>
              <input type="date" class="experience-from" required>
          </div>
          <div class="form-field">
              <label>To Date</label>
              <input type="date" class="experience-to">
          </div>
      </div>
      <div class="form-row">
          <div class="form-field">
              <label>Salary</label>
              <input type="number" class="experience-salary">
          </div>
          <div class="form-field checkbox-field">
              <input type="checkbox" class="experience-current-role">
              <label>Currently working</label>
          </div>
          <div class="form-field remove-btn-container">
              <button type="button" class="remove-experience-btn" onclick="removeExperience(${index})">
                  ✕ Remove
              </button>
          </div>
      </div>
  `;
  container.appendChild(newExp);
}

function removeExperience(index) {
  const item = document.querySelector(`.experience-item[data-index="${index}"]`);
  if (item) item.remove();
  // Reindex remaining items
  const items = document.querySelectorAll('.experience-item');
  items.forEach((item, i) => {
      item.dataset.index = i;
      const btn = item.querySelector('.remove-experience-btn');
      if (btn) btn.onclick = () => removeExperience(i);
  });
}

// Initialize form with one qualification and one experience by default
document.addEventListener('DOMContentLoaded', function() {
  addQualification();
  addExperience();
});

