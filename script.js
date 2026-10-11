document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('memberForm');

  if (form) {
    form.addEventListener('submit', (event) => {
      const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
      let valid = true;

      requiredFields.forEach((field) => {
        if (!field.checkValidity()) {
          field.reportValidity();
          valid = false;
        }
      });

      const terms = document.getElementById('terms');
      if (terms && !terms.checked) {
        alert('Please accept the terms and conditions.');
        valid = false;
      }

      if (!valid) {
        event.preventDefault();
      }
    });
  }
});
