
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
  window.updatePhdFields = function () {
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
    }
  };

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

  // Initial toggle setup (prevent broken state on load)
  updatePhdFields();
  ["net", "set", "slet", "gate"].forEach((exam) => {
    const status = document.getElementById(`${exam}Status`).value;
    toggleExam(exam, status);
  });
  toggleWorkExperience("experience");
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

let experienceIndex = 0;

// Work experience toggle function
function workExperience(value) {
    const experienceSection = document.getElementById("experienceContainer");
    const addExpSection = document.querySelector(".add-experience-section");
    const buttons = document.querySelectorAll(".work-exp-toggle .toggle-btn");

    if (value === "fresher") {
        if (experienceSection) experienceSection.style.display = "none";
        if (addExpSection) addExpSection.style.display = "none";
        buttons.forEach(button => {
            if (button.dataset.value === "fresher") {
                button.classList.add("active");
            } else {
                button.classList.remove("active");
            }
        });
    } else if (value === "experience") {
        if (experienceSection) experienceSection.style.display = "block";
        if (addExpSection) addExpSection.style.display = "block";
        buttons.forEach(button => {
            if (button.dataset.value === "experience") {
                button.classList.add("active");
            } else {
                button.classList.remove("active");
            }
        });
    }
}
  