document.addEventListener("DOMContentLoaded", () => {
  // Multi-step form navigation
  const progressSteps = document.querySelectorAll(".progress-step")
  const formSteps = document.querySelectorAll(".form-step")
  const nextButtons = document.querySelectorAll(".next-step")
  const prevButtons = document.querySelectorAll(".prev-step")

  // Set up next buttons
  nextButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const currentStep = Number.parseInt(this.closest(".form-step").getAttribute("data-step"))
      const nextStep = currentStep + 1

      // Validate current step (simplified for demo)
      const isValid = validateStep(currentStep)

      if (isValid) {
        // Hide current step
        formSteps.forEach((step) => {
          step.classList.remove("active")
        })

        // Show next step
        const nextStepElement = document.querySelector(`.form-step[data-step="${nextStep}"]`)
        if (nextStepElement) {
          nextStepElement.classList.add("active")

          // Update progress bar
          progressSteps.forEach((step) => {
            const stepNumber = Number.parseInt(step.getAttribute("data-step"))
            if (stepNumber <= nextStep) {
              step.classList.add("active")
            } else {
              step.classList.remove("active")
            }
          })

          // Scroll to top of form
          window.scrollTo({
            top: document.querySelector(".multi-step-form-container").offsetTop - 100,
            behavior: "smooth",
          })
        }
      }
    })
  })

  // Set up previous buttons
  prevButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const currentStep = Number.parseInt(this.closest(".form-step").getAttribute("data-step"))
      const prevStep = currentStep - 1

      // Hide current step
      formSteps.forEach((step) => {
        step.classList.remove("active")
      })

      // Show previous step
      const prevStepElement = document.querySelector(`.form-step[data-step="${prevStep}"]`)
      if (prevStepElement) {
        prevStepElement.classList.add("active")

        // Update progress bar
        progressSteps.forEach((step) => {
          const stepNumber = Number.parseInt(step.getAttribute("data-step"))
          if (stepNumber < currentStep) {
            step.classList.add("active")
          } else {
            step.classList.remove("active")
          }
        })

        // Scroll to top of form
        window.scrollTo({
          top: document.querySelector(".multi-step-form-container").offsetTop - 100,
          behavior: "smooth",
        })
      }
    })
  })

  // Add service button
  const addServiceBtn = document.getElementById("add-service")
  const servicesContainer = document.getElementById("services-container")

  if (addServiceBtn && servicesContainer) {
    addServiceBtn.addEventListener("click", () => {
      const serviceItem = document.createElement("div")
      serviceItem.className = "service-item"
      serviceItem.innerHTML = `
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Service Name</label>
                        <input type="text" name="service_name[]" class="form-control" placeholder="e.g. Oil Change">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price Range</label>
                        <div class="price-range">
                            <span>$</span>
                            <input type="number" name="service_price_min[]" class="form-control" placeholder="Min">
                            <span>-</span>
                            <input type="number" name="service_price_max[]" class="form-control" placeholder="Max">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="service_description[]" class="form-control" rows="2" placeholder="Brief description of the service"></textarea>
                </div>
                <button type="button" class="remove-service btn btn-outline btn-sm">
                    <i class="fas fa-times"></i> Remove
                </button>
            `

      servicesContainer.appendChild(serviceItem)

      // Add event listener to remove button
      const removeBtn = serviceItem.querySelector(".remove-service")
      removeBtn.addEventListener("click", () => {
        servicesContainer.removeChild(serviceItem)
      })
    })
  }

  // File upload handling
  const fileInputs = document.querySelectorAll(".file-input")

  fileInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const fileInfo = this.parentElement.querySelector(".file-info")

      if (this.files.length > 0) {
        fileInfo.textContent = this.files[0].name
      } else {
        fileInfo.textContent = "No file chosen"
      }
    })
  })

  // Form submission
  const form = document.querySelector(".multi-step-form-container form")

  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault()

      // Simulate form submission
      alert(
        "Your application has been submitted successfully! We will review your information and get back to you soon.",
      )
      window.location.href = "provider-signup-success.html"
    })
  }

  // Helper function to validate step (simplified for demo)
  function validateStep(stepNumber) {
    const step = document.querySelector(`.form-step[data-step="${stepNumber}"]`)
    const requiredFields = step.querySelectorAll("[required]")
    let isValid = true

    requiredFields.forEach((field) => {
      if (!field.value) {
        isValid = false
        field.classList.add("invalid")
      } else {
        field.classList.remove("invalid")
      }
    })

    if (!isValid) {
      alert("Please fill in all required fields before proceeding.")
    }

    return isValid
  }
})

