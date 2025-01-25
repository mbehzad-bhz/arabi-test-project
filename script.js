// Function to load names into the dropdown
const loadNames = async () => {
  try {
    const response = await fetch("get_customers.php");
    const data = await response.json();
    const nameSelect = document.getElementById("name");

    // Clear existing options
    nameSelect.innerHTML = "<option selected value=''>---</option>";

    // Populate with new data (use name as value)
    data.forEach(({ id, name }) => {
      const option = document.createElement("option");
      option.value = name; // Set name as value, not id
      option.textContent = name; // Display the name
      option.dataset.id = id; // Store the id in a data attribute (optional)
      nameSelect.appendChild(option);
    });
  } catch (error) {
    Swal.fire("خطأ", "تعذر تحميل قائمة العملاء.", "error");
  }
};

// Function to validate the file type
const validateFileType = (fileInput) => {
  const file = fileInput.files[0];
  if (file) {
    const allowedTypes = ["image/jpeg", "image/png"];
    if (!allowedTypes.includes(file.type)) {
      Swal.fire("خطأ", "يرجى تحميل صورة بصيغة JPG أو JPEG أو PNG.", "error");
      return false; // File type not allowed
    }
  }
  return true; // File type is allowed
};

// Function to validate the form before submission
const validateForm = () => {
  const nameSelect = document.getElementById("name");
  const subjectSelect = document.getElementById("subject");
  const prioritySelect = document.getElementById("priority");
  const fileInput = document.getElementById("file_input");

  // Check if any of the dropdowns is empty
  if (
    nameSelect.value === "" ||
    subjectSelect.value === "" ||
    prioritySelect.value === ""
  ) {
    Swal.fire("خطأ", "يرجى ملء جميع الحقول.", "error");
    return false; // Prevent form submission
  }

  // Validate file type
  if (!validateFileType(fileInput)) {
    return false; // Stop form submission if file type is invalid
  }

  return true; // Allow form submission
};

// Function to set the current date (without time)
const setCurrentDate = () => {
  const dateInput = document.getElementById("date");
  const currentDate = new Date();

  // Format the current date to the required format: YYYY-MM-DD
  dateInput.value = currentDate.toISOString().split("T")[0]; // Format: YYYY-MM-DD
};

// Function to initialize both geolocation, date, and dropdown
const initializePage = () => {
  loadNames();
  setCurrentDate(); // Set the current date for registration date
};

// Call initializePage when the page loads
window.onload = initializePage;

// Form submit event listener
document.getElementById("contactForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  // Validate the form before proceeding
  if (!validateForm()) {
    return; // Stop further execution if validation fails
  }

  const formData = new FormData(e.target);

  try {
    const response = await fetch("submit.php", {
      method: "POST",
      body: formData,
    });

    if (response.ok) {
      Swal.fire("نجاح", "تم إرسال البيانات بنجاح!", "success");
      document.getElementById("contactForm").reset();
      setCurrentDate(); // Reinitialize the date field
    } else {
      Swal.fire("خطأ", "حدث خطأ أثناء إرسال البيانات.", "error");
    }
  } catch {
    Swal.fire("خطأ", "تعذر إرسال البيانات. حاول مرة أخرى.", "error");
  }
});
