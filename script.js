document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("memberForm");

  if (form) {
    form.addEventListener("submit", (event) => {
      event.preventDefault();

      const requiredFields = form.querySelectorAll("input[required], select[required], textarea[required]");
      let valid = true;

      requiredFields.forEach((field) => {
        if (!field.checkValidity()) {
          field.reportValidity();
          valid = false;
        }
      });

      const terms = document.getElementById("terms");
      if (terms && !terms.checked) {
        terms.reportValidity();
        valid = false;
      }

      if (!valid) return;

      const applicant = {
        fullName: document.getElementById("fullName")?.value || "",
        phone: document.getElementById("phone")?.value || "",
        email: document.getElementById("email")?.value || "",
        country: document.getElementById("country")?.value || "",
        category: document.getElementById("category")?.value || "",
        submittedAt: new Date().toISOString()
      };

      localStorage.setItem("cksOnlineJobsRegistration", JSON.stringify(applicant));
      window.location.href = "success.html";
    });
  }
});
