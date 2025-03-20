document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  const mainNav = document.querySelector(".main-nav")

  if (mobileMenuToggle && mainNav) {
    mobileMenuToggle.addEventListener("click", () => {
      mainNav.classList.toggle("show")
    })
  }

  // Dropdown menus
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle")

  dropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation()
      const dropdown = this.nextElementSibling
      dropdown.classList.toggle("show")

      // Close other dropdowns
      dropdownToggles.forEach((otherToggle) => {
        if (otherToggle !== toggle) {
          otherToggle.nextElementSibling.classList.remove("show")
        }
      })
    })
  })

  // Close dropdowns when clicking outside
  document.addEventListener("click", () => {
    document.querySelectorAll(".dropdown-menu").forEach((menu) => {
      menu.classList.remove("show")
    })
  })

  // Prevent dropdown menu clicks from closing the dropdown
  document.querySelectorAll(".dropdown-menu").forEach((menu) => {
    menu.addEventListener("click", (e) => {
      e.stopPropagation()
    })
  })

  // Form validation
  const forms = document.querySelectorAll("form[data-validate]")

  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      let isValid = true

      // Check required fields
      const requiredFields = form.querySelectorAll("[required]")
      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false
          showError(field, "This field is required")
        } else {
          clearError(field)
        }
      })

      // Check email fields
      const emailFields = form.querySelectorAll('input[type="email"]')
      emailFields.forEach((field) => {
        if (field.value.trim() && !isValidEmail(field.value)) {
          isValid = false
          showError(field, "Please enter a valid email address")
        }
      })

      // Check password fields
      const passwordField = form.querySelector('input[name="password"]')
      const confirmPasswordField = form.querySelector('input[name="confirm_password"]')

      if (passwordField && confirmPasswordField) {
        if (passwordField.value !== confirmPasswordField.value) {
          isValid = false
          showError(confirmPasswordField, "Passwords do not match")
        }
      }

      if (!isValid) {
        e.preventDefault()
      }
    })
  })

  // Helper functions for form validation
  function showError(field, message) {
    // Clear any existing error
    clearError(field)

    // Add error class to the field
    field.classList.add("is-invalid")

    // Create and append error message
    const errorDiv = document.createElement("div")
    errorDiv.className = "form-error"
    errorDiv.textContent = message

    field.parentNode.appendChild(errorDiv)
  }

  function clearError(field) {
    field.classList.remove("is-invalid")
    const errorDiv = field.parentNode.querySelector(".form-error")
    if (errorDiv) {
      errorDiv.remove()
    }
  }

  function isValidEmail(email) {
    const re =
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(String(email).toLowerCase())
  }

  // Flash messages auto-dismiss
  const flashMessages = document.querySelectorAll(".alert")
  flashMessages.forEach((message) => {
    setTimeout(() => {
      message.style.opacity = "0"
      setTimeout(() => {
        message.remove()
      }, 500)
    }, 5000)
  })
})

